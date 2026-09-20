/**
 * Saves the inner blocks only.
 *
 * The wrapper element is created server side in render.php, therefore nothing
 * but the child blocks is written to the post content.
 */

import { InnerBlocks } from '@wordpress/block-editor';

export default function save() {
	return <InnerBlocks.Content />;
}
