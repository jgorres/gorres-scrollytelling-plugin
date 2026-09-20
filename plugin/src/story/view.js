/**
 * Front end behaviour of the story block.
 *
 * Watches the text steps with an IntersectionObserver and shows the medium
 * that belongs to the step currently crossing the middle of the viewport.
 * Steps without a medium keep the previous one visible.
 *
 * Until this script runs, CSS shows the first medium, so the block stays
 * readable when JavaScript is unavailable.
 */

const STORY_SELECTOR = '.jgor-st-story';
const ITEM_SELECTOR = '.jgor-st-stage__item';
const STEP_SELECTOR = '.jgor-st-step';

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

	items.forEach( ( item, position ) => {
		const isActive = position === target;

		item.classList.toggle( 'is-active', isActive );

		// Only the visible video should run.
		const video = item.querySelector( 'video' );

		if ( video ) {
			if ( isActive ) {
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
 * Sets up every story on the page.
 *
 * @return {void}
 */
function init() {
	if ( ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	document.querySelectorAll( STORY_SELECTOR ).forEach( setupStory );
}

if ( 'loading' === document.readyState ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
