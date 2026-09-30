/**
 * Pinned afterword of the story block.
 *
 * Below a stage that is limited to the medium, the afterword of the story is
 * pinned right below the stage. Without the script it simply follows the
 * steps.
 */

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
export function setupAfter( story ) {
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
