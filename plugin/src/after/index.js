/**
 * Registration of the afterword block.
 *
 * An afterword only ever appears inside a story block. The front end markup
 * is built by render.php; the story block moves it behind its steps.
 */

import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit';
import save from './save';
import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	edit: Edit,
	save,
} );
