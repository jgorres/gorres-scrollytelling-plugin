/**
 * Editor implementation of the story block.
 *
 * The editor shows the steps stacked below each other, each one with its own
 * media. Recreating the sticky stage inside the canvas would fight the
 * editor's own scroll container, so the effect is front end only.
 */

import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';

const ALLOWED_BLOCKS = [ 'jgor-scrollytelling/step' ];

const TEMPLATE = [
	[ 'jgor-scrollytelling/step' ],
	[ 'jgor-scrollytelling/step' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const {
		mediaPosition,
		stepAlign,
		transition,
		stickyOffset,
		minStepHeight,
	} = attributes;

	const blockProps = useBlockProps( {
		className: [
			'jgor-st-story--editor',
			`is-media-${ mediaPosition }`,
			`is-align-${ stepAlign }`,
		].join( ' ' ),
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
		templateLock: false,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'jgor-scrollytelling' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __(
							'Position der Medien',
							'jgor-scrollytelling'
						) }
						value={ mediaPosition }
						options={ [
							{
								label: __( 'Links', 'jgor-scrollytelling' ),
								value: 'left',
							},
							{
								label: __( 'Rechts', 'jgor-scrollytelling' ),
								value: 'right',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { mediaPosition: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __(
							'Ausrichtung der Schritte',
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
					<RangeControl
						__nextHasNoMarginBottom
						label={ __(
							'Abstand von oben (px)',
							'jgor-scrollytelling'
						) }
						help={ __(
							'Platz für ein festes Menü über der Medienspalte.',
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
							'Bestimmt, wie lange ein Schritt sichtbar bleibt.',
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
