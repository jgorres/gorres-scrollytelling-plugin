/**
 * Text effects of the story block for browsers without scroll timelines.
 *
 * Where the browser supports scroll timelines, the stylesheet of the step
 * block ties the effect of a text box to the scroll position and this module
 * does nothing. Everywhere else it watches the text boxes with two
 * IntersectionObservers and marks their steps, so the stylesheet can run the
 * effect once as a plain transition. Nothing is moved from a scroll handler.
 */

const EFFECT_SELECTOR =
	':scope > .jgor-st-story__steps > .jgor-st-step.has-text-effect';

// Share of the viewport height a box has to be inside before it counts.
const EDGE = 0.15;

// Reach of the area above the screen in pixels; more than any page is tall.
const FAR = 1000000;

/**
 * Tells whether the browser can run the effects in CSS alone.
 *
 * @return {boolean} True when scroll timelines are supported.
 */
function supportsScrollTimeline() {
	return (
		!! window.CSS &&
		'function' === typeof window.CSS.supports &&
		window.CSS.supports( 'animation-timeline: view()' )
	);
}

/**
 * Prepares the text effects of a single story block.
 *
 * @param {HTMLElement} story The story element.
 * @return {void}
 */
export function setupText( story ) {
	if ( supportsScrollTimeline() ) {
		return;
	}

	const steps = Array.from( story.querySelectorAll( EFFECT_SELECTOR ) );
	const boxes = steps
		.map( ( step ) =>
			step.querySelector( ':scope > .jgor-st-step__content' )
		)
		.filter( Boolean );

	if ( 0 === boxes.length ) {
		return;
	}

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

	boxes.forEach( ( box ) => {
		above.observe( box );
		band.observe( box );
	} );
}
