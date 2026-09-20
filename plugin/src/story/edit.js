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

const ALLOWED_BLOCKS = [ 'jgor-scrollytelling/step' ];

const TEMPLATE = [
	[ 'jgor-scrollytelling/step' ],
	[ 'jgor-scrollytelling/step' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const {
		textPosition,
		textWidth,
		overlayOpacity,
		mediaFit,
		limitStage,
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
				<PanelBody title={ __( 'Textkästen', 'jgor-scrollytelling' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __(
							'Waagerechte Position',
							'jgor-scrollytelling'
						) }
						value={ textPosition }
						options={ [
							{
								label: __( 'Links', 'jgor-scrollytelling' ),
								value: 'left',
							},
							{
								label: __( 'Mittig', 'jgor-scrollytelling' ),
								value: 'center',
							},
							{
								label: __( 'Rechts', 'jgor-scrollytelling' ),
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
							'Breite der Textkästen in Prozent',
							'jgor-scrollytelling'
						) }
						help={ __(
							'Auf schmalen Bildschirmen nutzen die Kästen immer die volle Breite.',
							'jgor-scrollytelling'
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
							'Senkrechte Position',
							'jgor-scrollytelling'
						) }
						value={ stepAlign }
						options={ [
							{
								label: __( 'Oben', 'jgor-scrollytelling' ),
								value: 'start',
							},
							{
								label: __( 'Mittig', 'jgor-scrollytelling' ),
								value: 'center',
							},
							{
								label: __( 'Unten', 'jgor-scrollytelling' ),
								value: 'end',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { stepAlign: value } )
						}
					/>
				</PanelBody>
				<PanelBody title={ __( 'Medien', 'jgor-scrollytelling' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Bildanpassung', 'jgor-scrollytelling' ) }
						help={ __(
							'„Ausschnitt füllen“ beschneidet das Medium auf die Bildschirmgröße, „Ganzes Medium zeigen“ lässt Ränder frei.',
							'jgor-scrollytelling'
						) }
						value={ mediaFit }
						options={ [
							{
								label: __(
									'Ausschnitt füllen',
									'jgor-scrollytelling'
								),
								value: 'cover',
							},
							{
								label: __(
									'Ganzes Medium zeigen',
									'jgor-scrollytelling'
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
								'Bühne auf das Medium begrenzen',
								'jgor-scrollytelling'
							) }
							help={ __(
								'Die Medien stehen sonst in voller Breite, der Text kann dann neben dem Medium landen. Die Breite richtet sich nach dem schmalsten Medium der Story. Wirkt nur im Frontend.',
								'jgor-scrollytelling'
							) }
							checked={ limitStage }
							onChange={ ( value ) =>
								setAttributes( { limitStage: value } )
							}
						/>
					) }
					<RangeControl
						__nextHasNoMarginBottom
						label={ __(
							'Abdunkelung in Prozent',
							'jgor-scrollytelling'
						) }
						help={ __(
							'Legt sich über die Medien, damit der Text darauf lesbar bleibt.',
							'jgor-scrollytelling'
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
							'Abstand von oben (px)',
							'jgor-scrollytelling'
						) }
						help={ __(
							'Platz für ein festes Menü über den Medien.',
							'jgor-scrollytelling'
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
							'Höhe eines Schritts in Prozent der Bildschirmhöhe',
							'jgor-scrollytelling'
						) }
						help={ __(
							'Bestimmt, wie lange ein Medium stehen bleibt.',
							'jgor-scrollytelling'
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
					title={ __( 'Bewegung', 'jgor-scrollytelling' ) }
					initialOpen={ false }
				>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Übergang', 'jgor-scrollytelling' ) }
						help={ __(
							'Bei „Ohne“ wechseln die Medien hart. Systeme mit reduzierter Bewegung blenden nie über.',
							'jgor-scrollytelling'
						) }
						value={ transition }
						options={ [
							{
								label: __(
									'Überblenden',
									'jgor-scrollytelling'
								),
								value: 'fade',
							},
							{
								label: __( 'Ohne', 'jgor-scrollytelling' ),
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
