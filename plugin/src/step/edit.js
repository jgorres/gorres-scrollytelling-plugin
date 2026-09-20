/**
 * Editor implementation of the step block.
 *
 * Every step keeps one medium and its own text. The medium is only previewed
 * here; on the front end the parent story block moves it into its sticky
 * stage.
 */

import { __ } from '@wordpress/i18n';
import {
	BlockControls,
	InspectorControls,
	MediaPlaceholder,
	MediaReplaceFlow,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	Button,
	FocalPointPicker,
	Notice,
	PanelBody,
	TextareaControl,
} from '@wordpress/components';

const ALLOWED_MEDIA = [ 'image', 'video' ];

const TEMPLATE = [
	[ 'core/heading', { level: 3 } ],
	[ 'core/paragraph', {} ],
];

const EMPTY_MEDIA = {
	mediaId: 0,
	mediaUrl: '',
	mediaAlt: '',
	mediaType: 'image',
	focalPoint: undefined,
};

export default function Edit( { attributes, setAttributes } ) {
	const { mediaId, mediaUrl, mediaAlt, mediaType, focalPoint } = attributes;
	const hasMedia = '' !== mediaUrl;

	/*
	 * Picking a medium copies the alternative text of the media library into
	 * the attribute. An empty attribute therefore means that neither place
	 * holds a description.
	 */
	const missingAlt =
		hasMedia && 'image' === mediaType && '' === mediaAlt.trim();

	const blockProps = useBlockProps( {
		className: `jgor-st-step--editor${ hasMedia ? ' has-media' : '' }`,
		style: focalPoint
			? {
					'--jgor-st-focal-x': `${ focalPoint.x * 100 }%`,
					'--jgor-st-focal-y': `${ focalPoint.y * 100 }%`,
				}
			: undefined,
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'jgor-st-step__text' },
		{ template: TEMPLATE }
	);

	/**
	 * Stores the medium picked in the media library.
	 *
	 * @param {Object} media Media object handed over by the picker.
	 */
	const onSelectMedia = ( media ) => {
		if ( ! media || ! media.url ) {
			setAttributes( EMPTY_MEDIA );
			return;
		}

		setAttributes( {
			mediaId: media.id ?? 0,
			mediaUrl: media.url,
			mediaAlt: media.alt ?? '',
			mediaType: 'video' === media.type ? 'video' : 'image',
		} );
	};

	return (
		<>
			{ hasMedia && (
				<BlockControls group="other">
					<MediaReplaceFlow
						mediaId={ mediaId }
						mediaURL={ mediaUrl }
						allowedTypes={ ALLOWED_MEDIA }
						accept="image/*,video/*"
						onSelect={ onSelectMedia }
						name={ __( 'Medium ersetzen', 'jgor-scrollytelling' ) }
					/>
				</BlockControls>
			) }

			<InspectorControls>
				<PanelBody title={ __( 'Medium', 'jgor-scrollytelling' ) }>
					{ ! hasMedia && (
						<p>
							{ __(
								'Für diesen Schritt ist kein Medium gesetzt. Beim Scrollen bleibt das Medium des vorherigen Schritts stehen.',
								'jgor-scrollytelling'
							) }
						</p>
					) }

					{ missingAlt && (
						<Notice status="warning" isDismissible={ false }>
							{ __(
								'Dieses Bild hat keinen Alternativtext. Menschen, die einen Screenreader nutzen, erfahren sonst nicht, was zu sehen ist.',
								'jgor-scrollytelling'
							) }
						</Notice>
					) }

					{ hasMedia && 'image' === mediaType && (
						<TextareaControl
							__nextHasNoMarginBottom
							label={ __(
								'Alternativtext',
								'jgor-scrollytelling'
							) }
							help={ __(
								'Leer lassen, um den Alternativtext aus der Mediathek zu übernehmen.',
								'jgor-scrollytelling'
							) }
							value={ mediaAlt }
							onChange={ ( value ) =>
								setAttributes( { mediaAlt: value } )
							}
						/>
					) }

					{ hasMedia && (
						<FocalPointPicker
							__nextHasNoMarginBottom
							label={ __(
								'Bildausschnitt',
								'jgor-scrollytelling'
							) }
							help={ __(
								'Legt fest, welcher Punkt beim Zuschneiden sichtbar bleibt.',
								'jgor-scrollytelling'
							) }
							url={ mediaUrl }
							value={ focalPoint ?? { x: 0.5, y: 0.5 } }
							onChange={ ( value ) =>
								setAttributes( { focalPoint: value } )
							}
						/>
					) }

					{ hasMedia && (
						<Button
							__next40pxDefaultSize
							isDestructive
							variant="secondary"
							onClick={ () => setAttributes( EMPTY_MEDIA ) }
						>
							{ __( 'Medium entfernen', 'jgor-scrollytelling' ) }
						</Button>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ hasMedia && (
					<div className="jgor-st-step__backdrop" aria-hidden="true">
						{ 'video' === mediaType ? (
							<video src={ mediaUrl } muted />
						) : (
							<img src={ mediaUrl } alt="" />
						) }
					</div>
				) }

				{ ! hasMedia && (
					<MediaPlaceholder
						icon="format-image"
						labels={ {
							title: __(
								'Medium des Schritts',
								'jgor-scrollytelling'
							),
							instructions: __(
								'Bild oder Video auswählen, über das dieser Textabschnitt scrollen soll.',
								'jgor-scrollytelling'
							),
						} }
						allowedTypes={ ALLOWED_MEDIA }
						accept="image/*,video/*"
						multiple={ false }
						onSelect={ onSelectMedia }
					/>
				) }

				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
