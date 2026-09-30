/**
 * Front end behaviour of the story block.
 *
 * Entry point of the view script. The behaviour itself lives in the modules
 * below view/: the media change, the text effects for browsers without scroll
 * timelines, the pinned afterword and the pulled up content.
 *
 * Until this script runs, CSS shows the first medium, so the block stays
 * readable when JavaScript is unavailable. Afterword and following content
 * then simply follow the steps.
 */

import { STORY_SELECTOR } from './view/selectors';
import { setupMedia } from './view/media';
import { setupText } from './view/text';
import { setupAfter } from './view/after';
import { setupPull } from './view/pull';

/**
 * Sets up every story on the page.
 *
 * @return {void}
 */
function init() {
	const stories = document.querySelectorAll( STORY_SELECTOR );

	if ( 'IntersectionObserver' in window ) {
		stories.forEach( setupMedia );
		stories.forEach( setupText );
	}

	// Pulling up only makes sense when the afterword area can be pinned, and
	// it has to happen first, so that the area is measured with the content.
	if ( 'ResizeObserver' in window ) {
		stories.forEach( setupPull );
		stories.forEach( setupAfter );
	}
}

if ( 'loading' === document.readyState ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
