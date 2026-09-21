/**
 * Editor implementation of the story block.
 *
 * The editor shows the steps stacked below each other, each one with its own
 * medium behind the text. Recreating the sticky stage inside the canvas would
 * fight the editor's own scroll container, so the effect is front end only.
 */

import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';

const ALLOWED_BLOCKS = [ 'scrollstage/step', 'scrollstage/after' ];

const TEMPLATE = [ [ 'scrollstage/step' ], [ 'scrollstage/step' ] ];

export default function Edit( { attributes, setAttributes } ) {
	const {
		textPosition,
		textWidth,
		overlayOpacity,
		mediaFit,
		limitStage,
		pullContent,
		stepAlign,
		transition,
		stickyOffset,
		minStepHeight,
	} = attributes;

	const blockProps = useBlockProps( {
		className: [
			'jgor-st-story--editor',
			`is-text-${ textPosition }`,
			`is-align-${ stepAlign }`,
			`is-fit-${ mediaFit }`,
		].join( ' ' ),
		style: {
			'--jgor-st-text-width': `${ textWidth }%`,
			'--jgor-st-overlay': overlayOpacity / 100,
		},
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
		templateLock: false,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Text boxes', 'scrollstage' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Horizontal position', 'scrollstage' ) }
						value={ textPosition }
						options={ [
							{
								label: __( 'Left', 'scrollstage' ),
								value: 'left',
							},
							{
								label: __( 'Middle', 'scrollstage' ),
								value: 'center',
							},
							{
								label: __( 'Right', 'scrollstage' ),
								value: 'right',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { textPosition: value } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __(
							'Text box width in percent',
							'scrollstage'
						) }
						help={ __(
							'On narrow screens the boxes always use the full width.',
							'scrollstage'
						) }
						value={ textWidth }
						min={ 20 }
						max={ 100 }
						step={ 5 }
						onChange={ ( value ) =>
							setAttributes( { textWidth: value ?? 45 } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Vertical position', 'scrollstage' ) }
						value={ stepAlign }
						options={ [
							{
								label: __( 'Top', 'scrollstage' ),
								value: 'start',
							},
							{
								label: __( 'Middle', 'scrollstage' ),
								value: 'center',
							},
							{
								label: __( 'Bottom', 'scrollstage' ),
								value: 'end',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { stepAlign: value } )
						}
					/>
				</PanelBody>
				<PanelBody title={ __( 'Media', 'scrollstage' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Media fit', 'scrollstage' ) }
						help={ __(
							'“Fill the frame” crops the medium to the size of the screen, “Show the whole medium” leaves margins free.',
							'scrollstage'
						) }
						value={ mediaFit }
						options={ [
							{
								label: __( 'Fill the frame', 'scrollstage' ),
								value: 'cover',
							},
							{
								label: __(
									'Show the whole medium',
									'scrollstage'
								),
								value: 'contain',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { mediaFit: value } )
						}
					/>
					{ 'contain' === mediaFit && (
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __(
								'Limit the stage to the medium',
								'scrollstage'
							) }
							help={ __(
								'Otherwise the media use the full width and the text can end up beside the medium. The width follows the narrowest medium of the story. Front end only.',
								'scrollstage'
							) }
							checked={ limitStage }
							onChange={ ( value ) =>
								setAttributes( { limitStage: value } )
							}
						/>
					) }
					{ 'contain' === mediaFit && limitStage && (
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __(
								'Pull up the following content',
								'scrollstage'
							) }
							help={ __(
								'The content after the story appears right below the stage from the first step on and only scrolls on once the story ends. Front end only.',
								'scrollstage'
							) }
							checked={ pullContent }
							onChange={ ( value ) =>
								setAttributes( { pullContent: value } )
							}
						/>
					) }
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Dimming in percent', 'scrollstage' ) }
						help={ __(
							'Sits on top of the media so that text stays readable.',
							'scrollstage'
						) }
						value={ overlayOpacity }
						min={ 0 }
						max={ 90 }
						step={ 5 }
						onChange={ ( value ) =>
							setAttributes( { overlayOpacity: value ?? 35 } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __(
							'Offset from the top (px)',
							'scrollstage'
						) }
						help={ __(
							'Room for a fixed menu above the media.',
							'scrollstage'
						) }
						value={ stickyOffset }
						min={ 0 }
						max={ 200 }
						step={ 4 }
						onChange={ ( value ) =>
							setAttributes( { stickyOffset: value ?? 0 } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __(
							'Step height in percent of the screen height',
							'scrollstage'
						) }
						help={ __(
							'Decides how long a medium stays in place.',
							'scrollstage'
						) }
						value={ minStepHeight }
						min={ 40 }
						max={ 200 }
						step={ 10 }
						onChange={ ( value ) =>
							setAttributes( { minStepHeight: value ?? 100 } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Motion', 'scrollstage' ) }
					initialOpen={ false }
				>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Transition', 'scrollstage' ) }
						help={ __(
							'With “None” the media switch instantly. Systems asking for reduced motion never cross-fade.',
							'scrollstage'
						) }
						value={ transition }
						options={ [
							{
								label: __( 'Cross-fade', 'scrollstage' ),
								value: 'fade',
							},
							{
								label: __( 'None', 'scrollstage' ),
								value: 'none',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { transition: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...innerBlocksProps } />
		</>
	);
}
