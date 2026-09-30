/**
 * Selectors shared by the front end modules of the story block.
 */

export const STORY_SELECTOR = '.jgor-st-story';
export const ITEM_SELECTOR = '.jgor-st-stage__item';

// Column of steps, seen from the story.
const COLUMN = ':scope > .jgor-st-story__steps';

// Steps of a story in document order: its own and those of its rows, but not
// the steps of another story that sits inside one of them.
export const STEP_SELECTOR = [
	`${ COLUMN } > .jgor-st-step`,
	`${ COLUMN } > .jgor-st-row > .jgor-st-row__viewport > .jgor-st-row__track > .jgor-st-step`,
].join( ', ' );

// Element the steps of a row sit in.
export const TRACK_CLASS = 'jgor-st-row__track';
