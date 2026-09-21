/**
 * Editor implementation of the afterword block.
 *
 * Any blocks can go into the afterword. The editor shows it where it was
 * inserted; on the front end it always follows the last step.
 */

import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { Notice, PanelBody } from '@wordpress/components';

const TEMPLATE = [
	[ 'core/heading', { level: 2 } ],
	[ 'core/paragraph', {} ],
];

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'jgor-st-after--editor',
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Afterword', 'scrollstage' ) }>
					<Notice status="info" isDismissible={ false }>
						{ __(
							'The afterword always follows the last step. When the story limits the stage to the medium, it appears right below the stage from the first step on and only scrolls away once the story ends. Front end only.',
							'scrollstage'
						) }
					</Notice>
				</PanelBody>
			</InspectorControls>

			<div { ...innerBlocksProps } />
		</>
	);
}
