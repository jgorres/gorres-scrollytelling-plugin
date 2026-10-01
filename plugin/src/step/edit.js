/**
 * Editor implementation of the step block.
 *
 * Every step keeps one medium and its own text. The medium is only previewed
 * here; on the front end the parent story block moves it into its sticky
 * stage. Background, border, shadow and padding of the block go to the text
 * box, see text-box.js. The height of a step only shows on the front end,
 * and so does the image a step can carry for portrait screens.
 */

import { __ } from '@wordpress/i18n';
import {
	BlockControls,
	InspectorControls,
	MediaPlaceholder,
	MediaReplaceFlow,
	MediaUpload,
	MediaUploadCheck,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	Button,
	Flex,
	FocalPointPicker,
	Notice,
	PanelBody,
	RangeControl,
	SelectControl,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components';

import { useTextBoxProps } from './text-box';

const ALLOWED_MEDIA = [ 'image', 'video' ];

const TEMPLATE = [
	[ 'core/heading', { level: 3 } ],
	[ 'core/paragraph', {} ],
];

const PORTRAIT_MEDIA = [ 'image' ];

const EMPTY_PORTRAIT = {
	portraitId: 0,
	portraitUrl: '',
	portraitFocalPoint: undefined,
};

// A step without a medium has no image for portrait screens either.
const EMPTY_MEDIA = {
	mediaId: 0,
	mediaUrl: '',
	mediaAlt: '',
	mediaType: 'image',
	focalPoint: undefined,
	...EMPTY_PORTRAIT,
};

// Height a step starts with once it stops following the story, in percent.
const OWN_HEIGHT = 50;

export default function Edit( { attributes, setAttributes } ) {
	const {
		mediaId,
		mediaUrl,
		mediaAlt,
		mediaType,
		focalPoint,
		portraitId,
		portraitUrl,
		portraitFocalPoint,
		textEffect,
		minHeight,
	} = attributes;
	const hasMedia = '' !== mediaUrl;

	// Only an image can be swapped for another one on portrait screens.
	const canHavePortrait = hasMedia && 'image' === mediaType;
	const hasPortrait = canHavePortrait && '' !== portraitUrl;

	// Zero means that the step is as tall as the story says.
	const hasOwnHeight = minHeight > 0;

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

	// Background, border, shadow and padding belong to the text box.
	const innerBlocksProps = useInnerBlocksProps(
		useTextBoxProps( attributes ),
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

		const isVideo = 'video' === media.type;

		setAttributes( {
			mediaId: media.id ?? 0,
			mediaUrl: media.url,
			mediaAlt: media.alt ?? '',
			mediaType: isVideo ? 'video' : 'image',
			// A video cannot be swapped, so an image left over would never show.
			...( isVideo ? EMPTY_PORTRAIT : {} ),
		} );
	};

	/**
	 * Stores the image picked for portrait screens.
	 *
	 * @param {Object} media Media object handed over by the picker.
	 */
	const onSelectPortrait = ( media ) => {
		if ( ! media || ! media.url ) {
			setAttributes( EMPTY_PORTRAIT );
			return;
		}

		setAttributes( {
			portraitId: media.id ?? 0,
			portraitUrl: media.url,
			portraitFocalPoint: undefined,
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
						name={ __( 'Replace medium', 'scrollstage' ) }
					/>
				</BlockControls>
			) }

			<InspectorControls>
				<PanelBody title={ __( 'Medium', 'scrollstage' ) }>
					{ ! hasMedia && (
						<p>
							{ __(
								'No medium is set for this step. While scrolling, the medium of the previous step stays in place.',
								'scrollstage'
							) }
						</p>
					) }

					{ missingAlt && (
						<Notice status="warning" isDismissible={ false }>
							{ __(
								'This image has no alternative text. People using a screen reader will not learn what it shows.',
								'scrollstage'
							) }
						</Notice>
					) }

					{ hasMedia && 'image' === mediaType && (
						<TextareaControl
							__nextHasNoMarginBottom
							label={ __( 'Alternative text', 'scrollstage' ) }
							help={ __(
								'Leave empty to use the alternative text from the media library.',
								'scrollstage'
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
							label={ __( 'Focal point', 'scrollstage' ) }
							help={ __(
								'Sets which point stays visible when the medium is cropped.',
								'scrollstage'
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
							{ __( 'Remove medium', 'scrollstage' ) }
						</Button>
					) }
				</PanelBody>
				{ canHavePortrait && (
					<PanelBody
						title={ __( 'Portrait screens', 'scrollstage' ) }
						initialOpen={ false }
					>
						<p>
							{ __(
								'Optional second image for screens that are taller than wide, such as a phone held upright. It replaces the image of this step there and shares its alternative text. Front end only.',
								'scrollstage'
							) }
						</p>

						{ hasPortrait && (
							<FocalPointPicker
								__nextHasNoMarginBottom
								label={ __(
									'Focal point on portrait screens',
									'scrollstage'
								) }
								url={ portraitUrl }
								value={
									portraitFocalPoint ?? { x: 0.5, y: 0.5 }
								}
								onChange={ ( value ) =>
									setAttributes( {
										portraitFocalPoint: value,
									} )
								}
							/>
						) }

						<Flex justify="flex-start" wrap>
							<MediaUploadCheck>
								<MediaUpload
									allowedTypes={ PORTRAIT_MEDIA }
									value={ portraitId }
									onSelect={ onSelectPortrait }
									render={ ( { open } ) => (
										<Button
											__next40pxDefaultSize
											variant="secondary"
											onClick={ open }
										>
											{ hasPortrait
												? __(
														'Replace image',
														'scrollstage'
													)
												: __(
														'Choose image',
														'scrollstage'
													) }
										</Button>
									) }
								/>
							</MediaUploadCheck>

							{ hasPortrait && (
								<Button
									__next40pxDefaultSize
									isDestructive
									variant="secondary"
									onClick={ () =>
										setAttributes( EMPTY_PORTRAIT )
									}
								>
									{ __( 'Remove image', 'scrollstage' ) }
								</Button>
							) }
						</Flex>
					</PanelBody>
				) }
				<PanelBody
					title={ __( 'Height', 'scrollstage' ) }
					initialOpen={ false }
				>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __(
							'Use the step height of the story',
							'scrollstage'
						) }
						help={ __(
							'Switch off to give this step a height of its own. Lower steps without a medium let several text boxes scroll across the same medium one after the other. Front end only; has no effect inside a row.',
							'scrollstage'
						) }
						checked={ ! hasOwnHeight }
						onChange={ ( value ) =>
							setAttributes( {
								minHeight: value ? 0 : OWN_HEIGHT,
							} )
						}
					/>
					{ hasOwnHeight && (
						<RangeControl
							__nextHasNoMarginBottom
							label={ __(
								'Step height in percent of the screen height',
								'scrollstage'
							) }
							help={ __(
								'A text box taller than this makes the step as tall as it needs.',
								'scrollstage'
							) }
							value={ minHeight }
							min={ 20 }
							max={ 200 }
							step={ 10 }
							onChange={ ( value ) =>
								setAttributes( {
									minHeight: value ?? OWN_HEIGHT,
								} )
							}
						/>
					) }
				</PanelBody>
				<PanelBody
					title={ __( 'Motion', 'scrollstage' ) }
					initialOpen={ false }
				>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Text effect', 'scrollstage' ) }
						help={ __(
							'How the text box of this step appears and disappears while scrolling. Front end only.',
							'scrollstage'
						) }
						value={ textEffect }
						options={ [
							{
								label: __( 'Same as story', 'scrollstage' ),
								value: '',
							},
							{
								label: __( 'None', 'scrollstage' ),
								value: 'none',
							},
							{
								label: __( 'Fade in', 'scrollstage' ),
								value: 'fade',
							},
							{
								label: __( 'Slide up', 'scrollstage' ),
								value: 'slide',
							},
							{
								label: __( 'Zoom in', 'scrollstage' ),
								value: 'zoom',
							},
							{
								label: __( 'Rotate in', 'scrollstage' ),
								value: 'rotate',
							},
							{
								label: __( 'Dissolve', 'scrollstage' ),
								value: 'dissolve',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { textEffect: value } )
						}
					/>
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
							title: __( 'Medium of this step', 'scrollstage' ),
							instructions: __(
								'Choose an image or video for this text to scroll across.',
								'scrollstage'
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
