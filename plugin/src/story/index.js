/**
 * Registration of the story block.
 *
 * The block only ships the editor implementation; the front end markup is
 * built by render.php so that changes take effect without resaving posts.
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
