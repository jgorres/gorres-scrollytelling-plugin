/**
 * Feature checks shared by the front end modules of the story block.
 */

/**
 * Tells whether the browser can tie an animation to the scroll position.
 *
 * @return {boolean} True when scroll timelines are supported.
 */
export function supportsScrollTimeline() {
	return (
		!! window.CSS &&
		'function' === typeof window.CSS.supports &&
		window.CSS.supports( 'animation-timeline: view()' )
	);
}
