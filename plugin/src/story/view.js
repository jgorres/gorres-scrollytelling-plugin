/**
 * Front end behaviour of the story block.
 *
 * Watches the text steps with an IntersectionObserver and shows the medium
 * that belongs to the step currently crossing the middle of the viewport.
 * Steps without a medium keep the previous one visible.
 *
 * Until this script runs, CSS shows the first medium, so the block stays
 * readable when JavaScript is unavailable.
 *
 * Below a stage that is limited to the medium, the script also pins the
 * afterword of the story and, when asked for, pulls up the content after the
 * block. Without the script both simply follow the steps.
 */

const STORY_SELECTOR = '.jgor-st-story';
const ITEM_SELECTOR = '.jgor-st-stage__item';
const STEP_SELECTOR = '.jgor-st-step';

/**
 * Tells whether the visitor asked for as little motion as possible.
 *
 * Read on every change, so a setting changed while reading takes effect.
 *
 * @return {boolean} True when motion should be avoided.
 */
function prefersReducedMotion() {
	return window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
}

/**
 * Activates the medium belonging to a step.
 *
 * @param {HTMLElement[]} items       Stage items, in the order of the steps.
 * @param {number}        index       Index of the step that is in view.
 * @param {number}        activeIndex Index of the medium shown right now.
 * @return {number} Index of the medium that is shown after the call.
 */
function activateItem( items, index, activeIndex ) {
	// Walk back over steps without a medium so their predecessor stays up.
	let target = Math.min( index, items.length - 1 );

	while ( target >= 0 && items[ target ].classList.contains( 'is-empty' ) ) {
		target -= 1;
	}

	if ( target === activeIndex ) {
		return activeIndex;
	}

	const reducedMotion = prefersReducedMotion();

	items.forEach( ( item, position ) => {
		const isActive = position === target;

		item.classList.toggle( 'is-active', isActive );

		// Only the visible medium belongs in the accessibility tree.
		if ( isActive ) {
			item.removeAttribute( 'aria-hidden' );
		} else {
			item.setAttribute( 'aria-hidden', 'true' );
		}

		// Only the visible video should run, and none at all when the visitor
		// asked for reduced motion.
		const video = item.querySelector( 'video' );

		if ( video ) {
			if ( isActive && ! reducedMotion ) {
				const playing = video.play();

				if ( playing && 'function' === typeof playing.catch ) {
					// Autoplay can be refused; the poster frame stays visible.
					playing.catch( () => {} );
				}
			} else {
				video.pause();
			}
		}
	} );

	return target;
}

/**
 * Prepares a single story block.
 *
 * @param {HTMLElement} story The story element.
 * @return {void}
 */
function setupStory( story ) {
	const stage = story.querySelector( '.jgor-st-story__stage' );

	if ( ! stage ) {
		return;
	}

	const items = Array.from( stage.querySelectorAll( ITEM_SELECTOR ) );
	const steps = Array.from( story.querySelectorAll( STEP_SELECTOR ) );

	if ( 0 === items.length || 0 === steps.length ) {
		return;
	}

	// From here on the script controls which medium is visible.
	story.classList.add( 'is-enhanced' );

	let activeIndex = activateItem( items, 0, -1 );

	const observer = new window.IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( ! entry.isIntersecting ) {
					return;
				}

				activeIndex = activateItem(
					items,
					steps.indexOf( entry.target ),
					activeIndex
				);
			} );
		},
		{
			// A narrow band across the middle of the viewport decides which
			// step counts as the current one.
			rootMargin: '-45% 0px -45% 0px',
			threshold: 0,
		}
	);

	steps.forEach( ( step ) => observer.observe( step ) );
}

/**
 * Tells whether a computed colour is fully transparent.
 *
 * @param {string} color Value of a computed background colour.
 * @return {boolean} True when nothing of the colour would be visible.
 */
function isTransparent( color ) {
	return (
		! color ||
		'transparent' === color ||
		/[,/]\s*0(?:\.0+)?\s*\)$/.test( color )
	);
}

/**
 * Finds the background colour the story is shown on.
 *
 * Walks up from the story itself to the first element with a visible
 * background colour. Gradients and images are not copied; the afterword then
 * takes the next solid colour behind them.
 *
 * @param {HTMLElement} story The story element.
 * @return {string} A CSS colour.
 */
function findBackground( story ) {
	for ( let node = story; node; node = node.parentElement ) {
		const color = window.getComputedStyle( node ).backgroundColor;

		if ( ! isTransparent( color ) ) {
			return color;
		}
	}

	// The canvas colour of the browser, used when no element sets one.
	return 'Canvas';
}

/**
 * Pins the afterword of a story right below its limited stage.
 *
 * Height of stage and afterword go into custom properties that the styles
 * turn into offsets. A ResizeObserver keeps them current when the window,
 * the fonts or the content change.
 *
 * @param {HTMLElement} story The story element.
 * @return {void}
 */
function setupAfter( story ) {
	const stage = story.querySelector( ':scope > .jgor-st-story__stage' );
	const after = story.querySelector( ':scope > .jgor-st-story__after' );

	if (
		! stage ||
		! after ||
		! story.classList.contains( 'is-stage-limited' )
	) {
		return;
	}

	// An afterword with its own background colour keeps it.
	const hasBackground =
		null !==
		after.querySelector( ':scope > .jgor-st-after.has-background' );

	const update = () => {
		story.style.setProperty(
			'--jgor-st-stage-h',
			`${ stage.getBoundingClientRect().height }px`
		);
		story.style.setProperty(
			'--jgor-st-after-h',
			`${ after.getBoundingClientRect().height }px`
		);

		if ( ! hasBackground ) {
			story.style.setProperty(
				'--jgor-st-after-bg',
				findBackground( story )
			);
		}
	};

	update();
	story.classList.add( 'is-after-pinned' );

	// The offsets move stage and afterword but never resize them, so the
	// observer cannot trigger itself.
	const observer = new window.ResizeObserver( update );

	observer.observe( stage );
	observer.observe( after );
}

/**
 * Collects the content that follows a story.
 *
 * Takes the siblings after the story up to the next story, so a second story
 * further down keeps its own place.
 *
 * @param {HTMLElement} story The story element.
 * @return {HTMLElement[]} Elements that follow the story.
 */
function followingContent( story ) {
	const elements = [];

	for (
		let node = story.nextElementSibling;
		node && ! node.matches( STORY_SELECTOR );
		node = node.nextElementSibling
	) {
		elements.push( node );
	}

	return elements;
}

/**
 * Pulls the content after a story up below its limited stage.
 *
 * The content keeps its place in the document and is only shifted while
 * rendering: by the distance between the lower edge of what sticks (stage,
 * or a pinned afterword) and the lower edge of the story. Before the story
 * that is the full scrolling distance, once the stage leaves it is zero. The
 * text boxes are clipped to the stage so that they neither cover the pulled
 * up content nor catch its clicks.
 *
 * @param {HTMLElement} story The story element.
 * @return {void}
 */
function setupPull( story ) {
	const stage = story.querySelector( ':scope > .jgor-st-story__stage' );
	const steps = story.querySelector( ':scope > .jgor-st-story__steps' );

	if (
		! stage ||
		! steps ||
		! story.classList.contains( 'is-stage-limited' ) ||
		! story.classList.contains( 'has-pull-content' )
	) {
		return;
	}

	const content = followingContent( story );

	if ( 0 === content.length ) {
		return;
	}

	const after = story.querySelector( ':scope > .jgor-st-story__after' );

	let frame = 0;

	const update = () => {
		frame = 0;

		const storyRect = story.getBoundingClientRect();
		const stageRect = stage.getBoundingClientRect();
		const stepsRect = steps.getBoundingClientRect();

		// A pinned afterword belongs to what sticks, the content goes below it.
		const pinnedBottom =
			after && story.classList.contains( 'is-after-pinned' )
				? after.getBoundingClientRect().bottom
				: stageRect.bottom;

		const pull = `${ Math.min( 0, pinnedBottom - storyRect.bottom ) }px`;

		content.forEach( ( element ) => {
			element.style.setProperty( '--jgor-st-pull', pull );
		} );

		story.style.setProperty(
			'--jgor-st-clip-top',
			`${ Math.max( 0, stageRect.top - stepsRect.top ) }px`
		);
		story.style.setProperty(
			'--jgor-st-clip-bottom',
			`${ Math.max( 0, stepsRect.bottom - stageRect.bottom ) }px`
		);
	};

	const schedule = () => {
		if ( 0 === frame ) {
			frame = window.requestAnimationFrame( update );
		}
	};

	update();

	content.forEach( ( element ) => element.classList.add( 'jgor-st-pulled' ) );
	story.classList.add( 'is-pulling' );

	window.addEventListener( 'scroll', schedule, { passive: true } );
	window.addEventListener( 'resize', schedule );

	// Size changes of the story, e.g. from late images or fonts, move the
	// edges without a scroll event.
	new window.ResizeObserver( schedule ).observe( story );
}

/**
 * Sets up every story on the page.
 *
 * @return {void}
 */
function init() {
	const stories = document.querySelectorAll( STORY_SELECTOR );

	if ( 'IntersectionObserver' in window ) {
		stories.forEach( setupStory );
	}

	if ( 'ResizeObserver' in window ) {
		stories.forEach( setupAfter );
		stories.forEach( setupPull );
	}
}

if ( 'loading' === document.readyState ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
