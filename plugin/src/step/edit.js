/**
 * Editor implementation of the step block.
 *
 * Every step keeps one medium and its own text. The medium is only previewed
 * here; on the front end the parent story block moves it into its sticky
 * stage. Background, border, shadow and padding of the block go to the text
 * box, see text-box.js. The height of a step only shows on the front end,
 * and so do the image a step can carry for portrait screens and a text box
 * that is kept in place.
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

// Everything only an image can have: a second one and scrolling along.
const EMPTY_PORTRAIT = {
	portraitId: 0,
	portraitUrl: '',
	portraitFocalPoint: undefined,
	mediaScroll: false,
};

// Removing the second image leaves the rest of the step as it is.
const REMOVED_PORTRAIT = {
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
		mediaScroll,
		textEffect,
		minHeight,
		textPosition,
		stepAlign,
		pinText,
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

	// A position of its own shows in the canvas, like the one of the story.
	const blockProps = useBlockProps( {
		className: [
			'jgor-st-step--editor',
			hasMedia ? 'has-media' : '',
			textPosition ? `is-text-${ textPosition }` : '',
			stepAlign ? `is-align-${ stepAlign }` : '',
		]
			.filter( Boolean )
			.join( ' ' ),
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
			setAttributes( REMOVED_PORTRAIT );
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
						name={ __( 'Replace medium', 'gorres-scrollytelling' ) }
					/>
				</BlockControls>
			) }

			<InspectorControls>
				<PanelBody title={ __( 'Medium', 'gorres-scrollytelling' ) }>
					{ ! hasMedia && (
						<p>
							{ __(
								'No medium is set for this step. While scrolling, the medium of the previous step stays in place.',
								'gorres-scrollytelling'
							) }
						</p>
					) }

					{ missingAlt && (
						<Notice status="warning" isDismissible={ false }>
							{ __(
								'This image has no alternative text. People using a screen reader will not learn what it shows.',
								'gorres-scrollytelling'
							) }
						</Notice>
					) }

					{ hasMedia && 'image' === mediaType && (
						<TextareaControl
							__nextHasNoMarginBottom
							label={ __(
								'Alternative text',
								'gorres-scrollytelling'
							) }
							help={ __(
								'Leave empty to use the alternative text from the media library.',
								'gorres-scrollytelling'
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
								'Focal point',
								'gorres-scrollytelling'
							) }
							help={ __(
								'Sets which point stays visible when the medium is cropped.',
								'gorres-scrollytelling'
							) }
							url={ mediaUrl }
							value={ focalPoint ?? { x: 0.5, y: 0.5 } }
							onChange={ ( value ) =>
								setAttributes( { focalPoint: value } )
							}
						/>
					) }

					{ canHavePortrait && (
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __(
								'Let the image scroll along',
								'gorres-scrollytelling'
							) }
							help={ __(
								'The image does not stay in place. It fills the screen at the start of this step and scrolls away with it, while the medium of the next step already shows behind it. An image taller than the screen keeps its height and makes the step as tall as itself, unless the step has a height of its own, which the image then fills. Front end only; has no effect inside a row.',
								'gorres-scrollytelling'
							) }
							checked={ mediaScroll }
							onChange={ ( value ) =>
								setAttributes( { mediaScroll: value } )
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
							{ __( 'Remove medium', 'gorres-scrollytelling' ) }
						</Button>
					) }
				</PanelBody>
				{ canHavePortrait && (
					<PanelBody
						title={ __(
							'Portrait screens',
							'gorres-scrollytelling'
						) }
						initialOpen={ false }
					>
						<p>
							{ __(
								'Optional second image for screens that are taller than wide, such as a phone held upright. It replaces the image of this step there and shares its alternative text. Front end only.',
								'gorres-scrollytelling'
							) }
						</p>

						{ hasPortrait && (
							<FocalPointPicker
								__nextHasNoMarginBottom
								label={ __(
									'Focal point on portrait screens',
									'gorres-scrollytelling'
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
														'gorres-scrollytelling'
													)
												: __(
														'Choose image',
														'gorres-scrollytelling'
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
										setAttributes( REMOVED_PORTRAIT )
									}
								>
									{ __(
										'Remove image',
										'gorres-scrollytelling'
									) }
								</Button>
							) }
						</Flex>
					</PanelBody>
				) }
				<PanelBody
					title={ __( 'Position', 'gorres-scrollytelling' ) }
					initialOpen={ false }
				>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __(
							'Horizontal position of the text box',
							'gorres-scrollytelling'
						) }
						value={ textPosition }
						options={ [
							{
								label: __(
									'Same as story',
									'gorres-scrollytelling'
								),
								value: '',
							},
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
					<SelectControl
						__nextHasNoMarginBottom
						label={ __(
							'Vertical position of the text box',
							'gorres-scrollytelling'
						) }
						value={ stepAlign }
						options={ [
							{
								label: __(
									'Same as story',
									'gorres-scrollytelling'
								),
								value: '',
							},
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
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __(
							'Keep the text box in place',
							'gorres-scrollytelling'
						) }
						help={ __(
							'The text box stays where it is on the screen while the visitor scrolls through this step, and leaves at the top when the step ends. It needs a step taller than the screen: set a height above 100 percent under "Height". The text box then goes without a text effect. Front end only; has no effect inside a row.',
							'gorres-scrollytelling'
						) }
						checked={ pinText }
						onChange={ ( value ) =>
							setAttributes( { pinText: value } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Height', 'gorres-scrollytelling' ) }
					initialOpen={ false }
				>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __(
							'Use the step height of the story',
							'gorres-scrollytelling'
						) }
						help={ __(
							'Switch off to give this step a height of its own. Lower steps without a medium let several text boxes scroll across the same medium one after the other. Front end only; has no effect inside a row.',
							'gorres-scrollytelling'
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
								'gorres-scrollytelling'
							) }
							help={ __(
								'A text box taller than this makes the step as tall as it needs.',
								'gorres-scrollytelling'
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
					title={ __( 'Motion', 'gorres-scrollytelling' ) }
					initialOpen={ false }
				>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Text effect', 'gorres-scrollytelling' ) }
						help={ __(
							'How the text box of this step appears and disappears while scrolling. Front end only.',
							'gorres-scrollytelling'
						) }
						value={ textEffect }
						options={ [
							{
								label: __(
									'Same as story',
									'gorres-scrollytelling'
								),
								value: '',
							},
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
								'Medium of this step',
								'gorres-scrollytelling'
							),
							instructions: __(
								'Choose an image or video for this text to scroll across.',
								'gorres-scrollytelling'
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
