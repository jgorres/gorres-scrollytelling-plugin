/**
 * Editor implementation of the row block.
 *
 * A row takes steps only. The editor shows them stacked below each other,
 * like the steps of the story itself; a frame and a note set them apart as
 * the part of the story that plays sideways.
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const ALLOWED_BLOCKS = [ 'gorres-scrollytelling/step' ];

const TEMPLATE = [
	[ 'gorres-scrollytelling/step' ],
	[ 'gorres-scrollytelling/step' ],
];

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'jgor-st-row--editor',
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'jgor-st-row__steps' },
		{
			allowedBlocks: ALLOWED_BLOCKS,
			template: TEMPLATE,
			templateLock: false,
		}
	);

	return (
		<div { ...blockProps }>
			<p className="jgor-st-row__note">
				{ __(
					'Row: these steps pass sideways on the front end.',
					'gorres-scrollytelling'
				) }
			</p>
			<div { ...innerBlocksProps } />
		</div>
	);
}
