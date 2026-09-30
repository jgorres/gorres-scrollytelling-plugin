/**
 * Registration of the row block.
 *
 * A row only ever appears inside a story block and holds steps. The front end
 * markup is built by render.php.
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
