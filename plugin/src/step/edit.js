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

	const blockProps = useBlockProps( {
		className: `jgor-st-step--editor${ hasMedia ? ' has-media' : '' }`,
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
				{ hasMedia ? (
					<div className="jgor-st-step__preview">
						{ 'video' === mediaType ? (
							<video src={ mediaUrl } muted />
						) : (
							<img src={ mediaUrl } alt={ mediaAlt } />
						) }
					</div>
				) : (
					<MediaPlaceholder
						icon="format-image"
						labels={ {
							title: __(
								'Medium des Schritts',
								'jgor-scrollytelling'
							),
							instructions: __(
								'Bild oder Video auswählen, das zu diesem Textabschnitt gehört.',
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
