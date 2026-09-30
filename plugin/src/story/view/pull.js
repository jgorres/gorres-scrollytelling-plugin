/**
 * Pulled up content of the story block.
 *
 * Below a stage that is limited to the medium, the content after the block
 * is pulled up into the afterword area of the story when asked for. Without
 * the script it stays where it is.
 */

import { STORY_SELECTOR } from './selectors';

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
 * Moves the following content once into the afterword area of the story, so
 * that the browser itself keeps it below the stage with position: sticky; a
 * script that shifts it on every scroll event always lags one frame behind
 * and makes the text jitter. The order of the document stays the same: the
 * content still follows the steps, only now inside the story.
 *
 * @param {HTMLElement} story The story element.
 * @return {void}
 */
export function setupPull( story ) {
	if (
		! story.classList.contains( 'is-stage-limited' ) ||
		! story.classList.contains( 'has-pull-content' ) ||
		! story.querySelector( ':scope > .jgor-st-story__stage' )
	) {
		return;
	}

	const content = followingContent( story );

	if ( 0 === content.length ) {
		return;
	}

	let area = story.querySelector( ':scope > .jgor-st-story__after' );

	if ( ! area ) {
		area = document.createElement( 'div' );
		area.className = 'jgor-st-story__after';
		story.appendChild( area );
	}

	// Behind an afterword, if the story has one.
	const wrapper = document.createElement( 'div' );

	wrapper.className = 'jgor-st-follow';
	content.forEach( ( element ) => wrapper.appendChild( element ) );
	area.appendChild( wrapper );

	// The gap below the story now sits above the moved content.
	story.classList.add( 'has-after' );
}
