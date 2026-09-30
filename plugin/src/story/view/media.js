/**
 * Media change of the story block.
 *
 * Watches the text steps with an IntersectionObserver and shows the medium
 * that belongs to the step currently crossing the middle of the viewport.
 * Steps without a medium keep the previous one visible.
 *
 * The steps of a row pass sideways, two of them share the screen while one
 * replaces the other. For them the observer watches a marker, a line down
 * the middle of the step: the viewport of the row clips it unless more than
 * half of the step is inside, so only one step of a row counts at a time.
 * While a row plays from top to bottom, the line is as tall as its step and
 * stands in for it.
 */

import { ITEM_SELECTOR, STEP_SELECTOR, TRACK_CLASS } from './selectors';

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
 * Returns the element the observer watches for a step.
 *
 * A step of the story is watched itself, a step of a row gets a marker.
 *
 * @param {HTMLElement} step The step.
 * @return {HTMLElement} The step or its marker.
 */
function createTarget( step ) {
	if (
		! step.parentElement ||
		! step.parentElement.classList.contains( TRACK_CLASS )
	) {
		return step;
	}

	const marker = document.createElement( 'div' );

	marker.className = 'jgor-st-row__marker';
	marker.setAttribute( 'aria-hidden', 'true' );
	step.appendChild( marker );

	return marker;
}

/**
 * Prepares the media change of a single story block.
 *
 * @param {HTMLElement} story The story element.
 * @return {void}
 */
export function setupMedia( story ) {
	const stage = story.querySelector( '.jgor-st-story__stage' );

	if ( ! stage ) {
		return;
	}

	const items = Array.from( stage.querySelectorAll( ITEM_SELECTOR ) );
	const steps = Array.from( story.querySelectorAll( STEP_SELECTOR ) );

	if ( 0 === items.length || 0 === steps.length ) {
		return;
	}

	const targets = steps.map( createTarget );

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
					targets.indexOf( entry.target ),
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

	targets.forEach( ( target ) => observer.observe( target ) );
}
