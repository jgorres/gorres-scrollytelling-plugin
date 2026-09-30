/**
 * Text effects of the story block for browsers without scroll timelines.
 *
 * Where the browser supports scroll timelines, the stylesheet of the step
 * block ties the effect of a text box to the scroll position and this module
 * does nothing. Everywhere else it watches the text boxes with an
 * IntersectionObserver and marks their steps, so the stylesheet can run the
 * effect once as a plain transition. Nothing is moved from a scroll handler.
 */

const EFFECT_SELECTOR =
	':scope > .jgor-st-story__steps > .jgor-st-step.has-text-effect';

// Share of the viewport height a box has to be inside before it counts.
const EDGE = 0.15;

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

	const observer = new window.IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				const step = entry.target.parentElement;

				// Upper edge of the band a box has to reach into.
				const edge = entry.rootBounds
					? entry.rootBounds.top
					: window.innerHeight * EDGE;

				step.classList.toggle( 'is-visible', entry.isIntersecting );

				// A box above the band left at the top and returns from there.
				step.classList.toggle(
					'is-past',
					! entry.isIntersecting &&
						entry.boundingClientRect.bottom <= edge
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

	boxes.forEach( ( box ) => observer.observe( box ) );
}
