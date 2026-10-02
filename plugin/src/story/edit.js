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

const ALLOWED_BLOCKS = [
	'gorres-scrollytelling/step',
	'gorres-scrollytelling/row',
	'gorres-scrollytelling/after',
];

const TEMPLATE = [
	[ 'gorres-scrollytelling/step' ],
	[ 'gorres-scrollytelling/step' ],
];

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
		textEffect,
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
				<PanelBody
					title={ __( 'Text boxes', 'gorres-scrollytelling' ) }
				>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __(
							'Horizontal position',
							'gorres-scrollytelling'
						) }
						value={ textPosition }
						options={ [
							{
								label: __( 'Left', 'gorres-scrollytelling' ),
								value: 'left',
							},
							{
								label: __( 'Middle', 'gorres-scrollytelling' ),
								value: 'center',
							},
							{
								label: __( 'Right', 'gorres-scrollytelling' ),
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
							'gorres-scrollytelling'
						) }
						help={ __(
							'On narrow screens the boxes always use the full width.',
							'gorres-scrollytelling'
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
						label={ __(
							'Vertical position',
							'gorres-scrollytelling'
						) }
						value={ stepAlign }
						options={ [
							{
								label: __( 'Top', 'gorres-scrollytelling' ),
								value: 'start',
							},
							{
								label: __( 'Middle', 'gorres-scrollytelling' ),
								value: 'center',
							},
							{
								label: __( 'Bottom', 'gorres-scrollytelling' ),
								value: 'end',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { stepAlign: value } )
						}
					/>
				</PanelBody>
				<PanelBody title={ __( 'Media', 'gorres-scrollytelling' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Media fit', 'gorres-scrollytelling' ) }
						help={ __(
							'“Fill the frame” crops the medium to the size of the screen, “Show the whole medium” leaves margins free.',
							'gorres-scrollytelling'
						) }
						value={ mediaFit }
						options={ [
							{
								label: __(
									'Fill the frame',
									'gorres-scrollytelling'
								),
								value: 'cover',
							},
							{
								label: __(
									'Show the whole medium',
									'gorres-scrollytelling'
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
								'gorres-scrollytelling'
							) }
							help={ __(
								'Otherwise the media use the full width and the text can end up beside the medium. The width follows the narrowest medium of the story. Front end only.',
								'gorres-scrollytelling'
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
								'gorres-scrollytelling'
							) }
							help={ __(
								'The content after the story appears right below the stage from the first step on and only scrolls on once the story ends. Front end only.',
								'gorres-scrollytelling'
							) }
							checked={ pullContent }
							onChange={ ( value ) =>
								setAttributes( { pullContent: value } )
							}
						/>
					) }
					<RangeControl
						__nextHasNoMarginBottom
						label={ __(
							'Dimming in percent',
							'gorres-scrollytelling'
						) }
						help={ __(
							'Sits on top of the media so that text stays readable.',
							'gorres-scrollytelling'
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
							'gorres-scrollytelling'
						) }
						help={ __(
							'Room for a fixed menu above the media.',
							'gorres-scrollytelling'
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
							'gorres-scrollytelling'
						) }
						help={ __(
							'Decides how long a medium stays in place.',
							'gorres-scrollytelling'
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
					title={ __( 'Motion', 'gorres-scrollytelling' ) }
					initialOpen={ false }
				>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Transition', 'gorres-scrollytelling' ) }
						help={ __(
							'With “None” the media switch instantly. Systems asking for reduced motion never cross-fade.',
							'gorres-scrollytelling'
						) }
						value={ transition }
						options={ [
							{
								label: __(
									'Cross-fade',
									'gorres-scrollytelling'
								),
								value: 'fade',
							},
							{
								label: __( 'None', 'gorres-scrollytelling' ),
								value: 'none',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { transition: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Text effect', 'gorres-scrollytelling' ) }
						help={ __(
							'How the text boxes appear and disappear while scrolling. A step can set its own effect. Front end only; systems asking for reduced motion show no effect.',
							'gorres-scrollytelling'
						) }
						value={ textEffect }
						options={ [
							{
								label: __( 'None', 'gorres-scrollytelling' ),
								value: 'none',
							},
							{
								label: __( 'Fade in', 'gorres-scrollytelling' ),
								value: 'fade',
							},
							{
								label: __(
									'Slide up',
									'gorres-scrollytelling'
								),
								value: 'slide',
							},
							{
								label: __( 'Zoom in', 'gorres-scrollytelling' ),
								value: 'zoom',
							},
							{
								label: __(
									'Rotate in',
									'gorres-scrollytelling'
								),
								value: 'rotate',
							},
							{
								label: __(
									'Dissolve',
									'gorres-scrollytelling'
								),
								value: 'dissolve',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { textEffect: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...innerBlocksProps } />
		</>
	);
}
