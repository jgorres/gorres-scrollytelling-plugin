/**
 * Registration of the step block.
 *
 * A step only ever appears inside a story block. It stores the medium that
 * belongs to it; the medium itself is rendered by the parent in its sticky
 * stage, the step contributes the text column.
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
