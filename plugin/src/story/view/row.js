/**
 * Rows of the story block.
 *
 * Decides for every row how it plays and marks it for the stylesheet of the
 * row block:
 *
 * - "is-sideways": the steps lie next to each other and pass through the
 *   viewport of the row. Browsers with scroll timelines tie that movement to
 *   the scroll position, in CSS alone.
 * - "is-sideways is-stepped": browsers without scroll timelines. The script
 *   reports the index of the current step and the track glides there with a
 *   transition. Nothing is moved from a scroll handler.
 * - neither: the steps follow each other like the steps of the story. That
 *   is the case for visitors who asked for reduced motion, for a row whose
 *   text does not fit the viewport, which would cut it off, and as long as
 *   this script has not run.
 *
 * A step that lies outside the viewport can still be the target of a link or
 * receive the keyboard focus. The browser cannot bring it into view, because
 * the place of the step depends on the scroll position; the script scrolls
 * there instead.
 */

import {
	ROW_SELECTOR,
	ROW_STEP_SELECTOR,
	ROW_VIEWPORT_SELECTOR,
} from './selectors';
import { supportsScrollTimeline } from './support';

// Reach of the area above the screen in pixels; more than any page is tall.
const FAR = 1000000;

/**
 * Tells whether the text of every step fits the viewport of the row.
 *
 * Measured against the stage of the story, which has the size the viewport
 * takes, so the answer does not depend on how the row plays right now.
 *
 * @param {HTMLElement[]} steps  Steps of the row.
 * @param {number}        height Height of the viewport in pixels.
 * @return {boolean} False when a text box is taller than the viewport allows.
 */
function fits( steps, height ) {
	return steps.every( ( step ) => {
		const box = step.querySelector( ':scope > .jgor-st-step__content' );

		if ( ! box ) {
			return true;
		}

		const style = window.getComputedStyle( step );
		const around = [
			style.marginTop,
			style.paddingTop,
			style.paddingBottom,
			style.marginBottom,
		].reduce( ( sum, value ) => sum + ( parseFloat( value ) || 0 ), 0 );

		return box.offsetHeight + around <= height;
	} );
}

/**
 * Reports the index of the current step of a row.
 *
 * One sentinel per step after the first marks the scroll position at which
 * that step takes over. A sentinel that has reached the upper edge of the
 * screen or lies above it counts; their number is the index. Counting them
 * stays correct after a jump, which carries several sentinels past the edge
 * at once.
 *
 * @param {HTMLElement} row   The row.
 * @param {number}      count Number of steps in the row.
 * @return {void}
 */
function watchIndex( row, count ) {
	const sentinels = [];
	const passed = [];

	for ( let index = 1; index < count; index++ ) {
		const sentinel = document.createElement( 'div' );

		sentinel.className = 'jgor-st-row__sentinel';
		sentinel.setAttribute( 'aria-hidden', 'true' );
		sentinel.style.setProperty( '--jgor-st-row-sentinel', String( index ) );
		row.appendChild( sentinel );
		sentinels.push( sentinel );
		passed.push( false );
	}

	const observer = new window.IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				passed[ sentinels.indexOf( entry.target ) ] =
					entry.isIntersecting;
			} );

			row.style.setProperty(
				'--jgor-st-row-index',
				String( passed.filter( Boolean ).length )
			);
		},
		{
			// Everything from the upper edge of the screen upwards.
			rootMargin: `${ FAR }px 0px -100% 0px`,
			threshold: 0,
		}
	);

	row.style.setProperty( '--jgor-st-row-index', '0' );
	sentinels.forEach( ( sentinel ) => observer.observe( sentinel ) );
}

/**
 * Scrolls to the position at which a step of a row is in view.
 *
 * @param {HTMLElement}   row      The row.
 * @param {HTMLElement}   viewport Viewport of the row.
 * @param {HTMLElement[]} steps    Steps of the row.
 * @param {Element}       element  Element inside one of the steps.
 * @return {void}
 */
function reveal( row, viewport, steps, element ) {
	if ( ! row.classList.contains( 'is-sideways' ) || steps.length < 2 ) {
		return;
	}

	const index = steps.findIndex( ( step ) => step.contains( element ) );

	if ( index < 0 ) {
		return;
	}

	// The viewport sticks below a fixed menu; every step after the first
	// takes an equal share of the remaining height of the row.
	const top = row.getBoundingClientRect().top + window.scrollY;
	const stick = parseFloat( window.getComputedStyle( viewport ).top ) || 0;
	const share =
		( row.offsetHeight - viewport.offsetHeight ) / ( steps.length - 1 );

	window.scrollTo( { top: top - stick + index * share } );
}

/**
 * Prepares a single row.
 *
 * @param {HTMLElement}      row    The row.
 * @param {HTMLElement|null} stage  Stage of the story.
 * @param {HTMLElement}      column Column of steps of the story.
 * @return {void}
 */
function setupRow( row, stage, column ) {
	const viewport = row.querySelector( ROW_VIEWPORT_SELECTOR );
	const steps = Array.from( row.querySelectorAll( ROW_STEP_SELECTOR ) );

	if ( ! viewport || 0 === steps.length ) {
		return;
	}

	const reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' );
	const stepped = ! supportsScrollTimeline();
	let watching = false;

	const update = () => {
		const height = stage ? stage.offsetHeight : window.innerHeight;
		const sideways = ! reduced.matches && fits( steps, height );

		if ( sideways && stepped && ! watching ) {
			watchIndex( row, steps.length );
			watching = true;
		}

		row.classList.toggle( 'is-sideways', sideways );
		row.classList.toggle( 'is-stepped', sideways && stepped );
	};

	/*
	 * Whether the text fits changes with the size of the text boxes and of
	 * the stage; the column changes its size with the window. The observer
	 * also reports once right after it starts, which sets the first state.
	 */
	const resize = new window.ResizeObserver( update );

	resize.observe( column );

	if ( stage ) {
		resize.observe( stage );
	}

	steps.forEach( ( step ) => {
		const box = step.querySelector( ':scope > .jgor-st-step__content' );

		if ( box ) {
			resize.observe( box );
		}
	} );

	if ( 'function' === typeof reduced.addEventListener ) {
		reduced.addEventListener( 'change', update );
	}

	update();

	/*
	 * Keyboard focus on something outside the viewport. A click lands on
	 * something visible and must not move the page under the pointer, so
	 * only a focus the browser would draw a focus ring for counts.
	 */
	row.addEventListener( 'focusin', ( event ) => {
		const target = event.target;

		if (
			! ( target instanceof window.Element ) ||
			! target.matches( ':focus-visible' )
		) {
			return;
		}

		const box = target.getBoundingClientRect();
		const frame = viewport.getBoundingClientRect();

		if ( box.left < frame.left - 1 || box.right > frame.right + 1 ) {
			reveal( row, viewport, steps, target );
		}
	} );

	// A link to an anchor inside the row, followed on this page or from
	// another one.
	const revealAnchor = () => {
		let target = null;

		try {
			target = document.getElementById(
				decodeURIComponent( window.location.hash.slice( 1 ) )
			);
		} catch {
			// A malformed fragment points nowhere.
			return;
		}

		if ( target && row.contains( target ) ) {
			reveal( row, viewport, steps, target );
		}
	};

	window.addEventListener( 'hashchange', revealAnchor );

	// The browser scrolls to the anchor itself until the page has loaded.
	if ( 'complete' === document.readyState ) {
		revealAnchor();
	} else {
		window.addEventListener( 'load', revealAnchor, { once: true } );
	}
}

/**
 * Prepares the rows of a single story block.
 *
 * @param {HTMLElement} story The story element.
 * @return {void}
 */
export function setupRows( story ) {
	const column = story.querySelector( ':scope > .jgor-st-story__steps' );

	if ( ! column ) {
		return;
	}

	const stage = story.querySelector( ':scope > .jgor-st-story__stage' );

	story.querySelectorAll( ROW_SELECTOR ).forEach( ( row ) => {
		setupRow( row, stage, column );
	} );
}
