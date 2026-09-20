/**
 * Saves the inner blocks only.
 *
 * The wrapper element is created server side in render.php.
 */

import { InnerBlocks } from '@wordpress/block-editor';

export default function save() {
	return <InnerBlocks.Content />;
}
