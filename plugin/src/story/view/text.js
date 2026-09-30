/**
 * Text effects of the story block for browsers without scroll timelines.
 *
 * Where the browser supports scroll timelines, the stylesheet of the step
 * block ties the effect of a text box to the scroll position and this module
 * does nothing. Everywhere else it watches the place of every text box with
 * two IntersectionObservers and marks the steps, so the stylesheet can run
 * the effect once as a plain transition. Nothing is moved from a scroll
 * handler.
 *
 * The observers do not watch the text boxes themselves. An effect scales,
 * turns or shifts its box, and an observer sees the box as it is drawn: a
 * box that shrinks or spins while hidden would leave and enter the visible
 * band by its own movement and switch back and forth for ever. Every box
 * therefore gets a ghost, an empty element that keeps the place the box has
 * in the layout, and the ghost is what the observers watch.
 */

import { supportsScrollTimeline } from './support';

const EFFECT_SELECTOR =
	':scope > .jgor-st-story__steps > .jgor-st-step.has-text-effect';

// Share of the viewport height a box has to be inside before it counts.
const EDGE = 0.15;

// Reach of the area above the screen in pixels; more than any page is tall.
const FAR = 1000000;

/**
 * Creates the ghost of a text box.
 *
 * The ghost is positioned against the same element as the box, the column of
 * steps, so the offsets of the box can be copied as they are. Offsets come
 * from the layout and do not change when the box is transformed.
 *
 * @param {HTMLElement} box The text box.
 * @return {{element: HTMLElement, update: Function}} Ghost and its updater.
 */
function createGhost( box ) {
	const element = document.createElement( 'div' );

	element.className = 'jgor-st-step__ghost';
	element.setAttribute( 'aria-hidden', 'true' );
	box.parentElement.appendChild( element );

	const update = () => {
		element.style.top = `${ box.offsetTop }px`;
		element.style.height = `${ box.offsetHeight }px`;
	};

	update();

	return { element, update };
}

/**
 * Prepares the text effects of a single story block.
 *
 * @param {HTMLElement} story The story element.
 * @return {void}
 */
export function setupText( story ) {
	if ( supportsScrollTimeline() || ! ( 'ResizeObserver' in window ) ) {
		return;
	}

	const column = story.querySelector( ':scope > .jgor-st-story__steps' );
	const steps = Array.from( story.querySelectorAll( EFFECT_SELECTOR ) );
	const boxes = steps
		.map( ( step ) =>
			step.querySelector( ':scope > .jgor-st-step__content' )
		)
		.filter( Boolean );

	if ( ! column || 0 === boxes.length ) {
		return;
	}

	const ghosts = boxes.map( createGhost );

	/*
	 * The place of a box changes with its own size and with the height of
	 * everything above it in the column; the column changes its size in both
	 * cases, and with the window as well.
	 */
	const resize = new window.ResizeObserver( () => {
		ghosts.forEach( ( ghost ) => ghost.update() );
	} );

	resize.observe( column );
	boxes.forEach( ( box ) => resize.observe( box ) );

	/*
	 * A box that reaches above the visible band left at the top and returns
	 * from there. The band alone cannot tell: a jump, to an anchor for one,
	 * carries a box from above the band to below it without ever touching it,
	 * and an observer only reports changes. So the area from the upper edge
	 * of the band upwards gets an observer of its own.
	 */
	const above = new window.IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				entry.target.parentElement.classList.toggle(
					'is-past',
					entry.isIntersecting
				);
			} );
		},
		{
			rootMargin: `${ FAR }px 0px -${ ( 1 - EDGE ) * 100 }% 0px`,
			threshold: 0,
		}
	);

	const band = new window.IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				entry.target.parentElement.classList.toggle(
					'is-visible',
					entry.isIntersecting
				);
			} );

			/*
			 * The first call reports every box. Only after it does the
			 * stylesheet hide the boxes that are not marked visible, so a box
			 * on screen while the page loads never flickers.
			 */
			story.classList.add( 'is-text-enhanced' );
		},
		{
			rootMargin: `-${ EDGE * 100 }% 0px -${ EDGE * 100 }% 0px`,
			threshold: 0,
		}
	);

	ghosts.forEach( ( ghost ) => {
		above.observe( ghost.element );
		band.observe( ghost.element );
	} );
}
