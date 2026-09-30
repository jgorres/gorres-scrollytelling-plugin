<?php
/**
 * Block category and block registration.
 *
 * @package Scrollstage
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds the plugin block category to the editor.
 *
 * The category is placed in front of the core ones so both blocks are easy to
 * find in the inserter.
 *
 * @param array<int, array<string, mixed>> $categories Registered block categories.
 * @return array<int, array<string, mixed>> Categories including the plugin one.
 */
function jgor_st_block_categories( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'scrollstage',
			'title' => __( 'Scrollstage', 'scrollstage' ),
			'icon'  => null,
		)
	);

	return $categories;
}
add_filter( 'block_categories_all', 'jgor_st_block_categories' );

/**
 * Registers all blocks from the build directory.
 *
 * The build directory is created by "npm run build"; without it the plugin
 * stays silent instead of throwing notices on every request.
 *
 * @return void
 */
function jgor_st_register_blocks() {
	$blocks = array( 'story', 'step', 'after' );

	foreach ( $blocks as $block ) {
		$path = JGOR_ST_PATH . 'build/' . $block;

		if ( is_readable( $path . '/block.json' ) ) {
			register_block_type_from_metadata( $path );
		}
	}
}
add_action( 'init', 'jgor_st_register_blocks' );

/**
 * Keeps the markup of afterword blocks until their story picks it up.
 *
 * WordPress renders every child before the render callback of its parent and
 * keeps the child instances in the parent's block list. The markup is stored
 * per instance, so a story only ever receives its own afterwords, even when
 * another story sits inside one of its steps. The weak map releases an entry
 * as soon as the block instance is gone.
 *
 * @param WP_Block    $block  Afterword block instance.
 * @param string|null $markup Markup to store, or null to take and clear it.
 * @return string Stored markup when taking, otherwise an empty string.
 */
function jgor_st_after_store( WP_Block $block, $markup = null ) {
	/**
	 * Markup per afterword instance, created on first use.
	 *
	 * @var WeakMap<WP_Block, string>|null $store
	 */
	static $store = null;

	if ( null === $store ) {
		$store = new WeakMap();
	}

	if ( null !== $markup ) {
		$store[ $block ] = $markup;
		return '';
	}

	$taken = $store[ $block ] ?? '';
	unset( $store[ $block ] );

	return $taken;
}

/**
 * Takes the rendered afterword out of the content stream.
 *
 * Runs last on the block specific filter, so everything other plugins add or
 * remove on "render_block" is already applied. The story block places the
 * markup behind its steps, see story/render.php.
 *
 * @param string               $block_content Rendered markup of the afterword.
 * @param array<string, mixed> $parsed_block  Parsed block, unused.
 * @param WP_Block|null        $instance      Block instance.
 * @return string Empty string once stored, the markup itself without instance.
 */
function jgor_st_collect_after( $block_content, $parsed_block = array(), $instance = null ) {
	unset( $parsed_block );

	if ( ! $instance instanceof WP_Block ) {
		return (string) $block_content;
	}

	return jgor_st_after_store( $instance, (string) $block_content );
}
add_filter( 'render_block_scrollstage/after', 'jgor_st_collect_after', PHP_INT_MAX, 3 );

/**
 * Keeps the stylesheet of the afterword although its block renders empty.
 *
 * Since WordPress 6.9 the assets of a block with empty output are dequeued
 * again. The afterword is empty on purpose, because jgor_st_collect_after()
 * hands its markup to the story, so it has to opt out.
 *
 * @param bool   $enqueue    Whether to enqueue assets for the empty block.
 * @param string $block_name Name of the block.
 * @return bool True for the afterword, otherwise the unchanged value.
 */
function jgor_st_keep_after_assets( $enqueue, $block_name ) {
	return 'scrollstage/after' === $block_name ? true : (bool) $enqueue;
}
add_filter( 'enqueue_empty_block_content_assets', 'jgor_st_keep_after_assets', 10, 2 );

/**
 * Builds the class names of the story wrapper.
 *
 * The single place where the state of a story turns into classes, so styles
 * and script can rely on one naming scheme: "is-text-", "is-align-" and
 * "is-fit-" for the layout, "is-effect-<name>" for every active effect.
 *
 * The caller validates the values against the block attributes; they are
 * sanitised here once more, because they end up in a class attribute.
 *
 * @param array<string, mixed> $state {
 *     State of the story. Missing keys leave their class out.
 *
 *     @type string   $text_position Horizontal position of the text boxes.
 *     @type string   $step_align    Vertical position of the text boxes.
 *     @type string   $media_fit     How the medium fills the stage.
 *     @type string[] $effects       Names of the active effects.
 *     @type bool     $stage_limited Whether the stage is limited to the medium.
 *     @type bool     $pull_content  Whether the following content is pulled up.
 *     @type bool     $has_after     Whether the story has an afterword.
 * }
 * @return string[] Class names, starting with the block class.
 */
function jgor_st_story_classes( $state ) {
	$classes  = array( 'jgor-st-story' );
	$variants = array(
		'text_position' => 'is-text-',
		'step_align'    => 'is-align-',
		'media_fit'     => 'is-fit-',
	);

	foreach ( $variants as $key => $prefix ) {
		$value = isset( $state[ $key ] ) && is_string( $state[ $key ] ) ? sanitize_html_class( $state[ $key ] ) : '';

		if ( '' !== $value ) {
			$classes[] = $prefix . $value;
		}
	}

	if ( isset( $state['effects'] ) && is_array( $state['effects'] ) ) {
		foreach ( $state['effects'] as $effect ) {
			$effect = is_string( $effect ) ? sanitize_html_class( $effect ) : '';

			if ( '' !== $effect ) {
				$classes[] = 'is-effect-' . $effect;
			}
		}
	}

	if ( ! empty( $state['stage_limited'] ) ) {
		$classes[] = 'is-stage-limited';

		// Pulling up only works below a limited stage.
		if ( ! empty( $state['pull_content'] ) ) {
			$classes[] = 'has-pull-content';
		}
	}

	if ( ! empty( $state['has_after'] ) ) {
		$classes[] = 'has-after';
	}

	return $classes;
}

/**
 * Builds class and style of the text box of a step.
 *
 * Background, border, shadow and padding of a step belong to its text box,
 * not to the step itself, which is as wide as the story and as tall as the
 * screen. block.json therefore keeps WordPress from putting them on the block
 * wrapper, and this function turns the same attributes into class and style
 * for the inner element, the way the block supports of core do it.
 *
 * The style engine sanitises every declaration; the class names are sanitised
 * here once more, because they end up in a class attribute.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @return array{class: string, style: string} Class names and inline style,
 *                                             both possibly empty.
 */
function jgor_st_step_box_attributes( $attributes ) {
	$style  = isset( $attributes['style'] ) && is_array( $attributes['style'] ) ? $attributes['style'] : array();
	$border = isset( $style['border'] ) && is_array( $style['border'] ) ? $style['border'] : array();

	// A colour from the palette is stored as a slug, a custom one in the style.
	$background = null;

	if ( isset( $attributes['backgroundColor'] ) && is_string( $attributes['backgroundColor'] ) && '' !== $attributes['backgroundColor'] ) {
		$background = 'var:preset|color|' . $attributes['backgroundColor'];
	} elseif ( isset( $style['color']['background'] ) ) {
		$background = $style['color']['background'];
	}

	$border_styles = array();

	// Radius and width were stored without a unit in early versions of the editor.
	foreach ( array( 'radius', 'width' ) as $property ) {
		if ( isset( $border[ $property ] ) ) {
			$border_styles[ $property ] = is_numeric( $border[ $property ] ) ? $border[ $property ] . 'px' : $border[ $property ];
		}
	}

	if ( isset( $border['style'] ) ) {
		$border_styles['style'] = $border['style'];
	}

	if ( isset( $attributes['borderColor'] ) && is_string( $attributes['borderColor'] ) && '' !== $attributes['borderColor'] ) {
		$border_styles['color'] = 'var:preset|color|' . $attributes['borderColor'];
	} elseif ( isset( $border['color'] ) ) {
		$border_styles['color'] = $border['color'];
	}

	// Borders that differ per side.
	foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
		if ( isset( $border[ $side ] ) && is_array( $border[ $side ] ) ) {
			$border_styles[ $side ] = array(
				'width' => $border[ $side ]['width'] ?? null,
				'color' => $border[ $side ]['color'] ?? null,
				'style' => $border[ $side ]['style'] ?? null,
			);
		}
	}

	// One call per block support, with the options core uses for each of them.
	$parts = array(
		wp_style_engine_get_styles(
			array( 'color' => array( 'background' => $background ) ),
			array( 'convert_vars_to_classnames' => true )
		),
		wp_style_engine_get_styles( array( 'border' => $border_styles ) ),
		wp_style_engine_get_styles( array( 'spacing' => array( 'padding' => $style['spacing']['padding'] ?? null ) ) ),
		wp_style_engine_get_styles( array( 'shadow' => $style['shadow'] ?? null ) ),
	);

	$classes = array();
	$css     = '';

	foreach ( $parts as $part ) {
		if ( ! empty( $part['classnames'] ) ) {
			foreach ( explode( ' ', $part['classnames'] ) as $class ) {
				$class = sanitize_html_class( $class );

				if ( '' !== $class ) {
					$classes[] = $class;
				}
			}
		}

		if ( ! empty( $part['css'] ) ) {
			$css .= $part['css'];
		}
	}

	return array(
		'class' => implode( ' ', array_unique( $classes ) ),
		'style' => $css,
	);
}

/**
 * Builds one item of the sticky media stage.
 *
 * Reads the media attributes of a single step block and returns the markup for
 * the stage. Steps without media produce an empty item on purpose: the script
 * keeps the previous image visible for them.
 *
 * Every item except the first is hidden from assistive technology: all media
 * of the story live in the document at once, and without that a screen reader
 * would read every alternative text in a row before reaching the first text.
 * The script moves the marker along with the visible medium.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @param int                  $index      Zero based position of the step.
 * @param string               $fit        How the medium fills the stage:
 *                                         "cover" or "contain".
 * @return string Markup of one stage item.
 */
function jgor_st_render_stage_item( $attributes, $index, $fit = 'cover' ) {
	$media_id   = isset( $attributes['mediaId'] ) ? absint( $attributes['mediaId'] ) : 0;
	$media_url  = isset( $attributes['mediaUrl'] ) ? (string) $attributes['mediaUrl'] : '';
	$media_alt  = isset( $attributes['mediaAlt'] ) ? (string) $attributes['mediaAlt'] : '';
	$media_type = isset( $attributes['mediaType'] ) && 'video' === $attributes['mediaType'] ? 'video' : 'image';

	$classes = 'jgor-st-stage__item';
	$styles  = '';

	// Focal point is stored as floats between 0 and 1 and becomes object-position.
	if ( isset( $attributes['focalPoint']['x'], $attributes['focalPoint']['y'] ) ) {
		$styles = sprintf(
			'--jgor-st-focal-x:%1$s%%;--jgor-st-focal-y:%2$s%%;',
			round( (float) $attributes['focalPoint']['x'] * 100, 2 ),
			round( (float) $attributes['focalPoint']['y'] * 100, 2 )
		);
	}

	if ( '' === $media_url && 0 === $media_id ) {
		$classes .= ' is-empty';
	}

	$inner = '';

	if ( 'video' === $media_type && '' !== $media_url ) {
		$inner = sprintf(
			'<video class="jgor-st-stage__media" src="%1$s" muted playsinline loop preload="metadata"></video>',
			esc_url( $media_url )
		);
	} elseif ( $media_id > 0 ) {
		$image_attr = array(
			'class'   => 'jgor-st-stage__media',
			'loading' => 0 === $index ? 'eager' : 'lazy',
			'sizes'   => jgor_st_stage_sizes( $media_id, $fit ),
		);

		// An empty alt text falls back to the one stored in the media library.
		if ( '' !== $media_alt ) {
			$image_attr['alt'] = $media_alt;
		}

		$inner = wp_get_attachment_image( $media_id, 'full', false, $image_attr );
	} elseif ( '' !== $media_url ) {
		$inner = sprintf(
			'<img class="jgor-st-stage__media" src="%1$s" alt="%2$s" loading="%3$s" decoding="async" />',
			esc_url( $media_url ),
			esc_attr( $media_alt ),
			0 === $index ? 'eager' : 'lazy'
		);
	}

	return sprintf(
		'<figure class="%1$s" style="%2$s" data-jgor-st-step="%3$d"%4$s>%5$s</figure>',
		esc_attr( $classes ),
		esc_attr( $styles ),
		$index,
		0 === $index ? '' : ' aria-hidden="true"',
		$inner
	);
}

/**
 * Builds the sizes attribute for a medium on the stage.
 *
 * The stage is as tall as the viewport. With "cover" a landscape image has to
 * be scaled to that height, so the browser needs a file that is wider than the
 * stage itself: height times the aspect ratio. The default "100vw" would make
 * it pick a file that is far too small, and the image looks blurry.
 *
 * @param int    $media_id Attachment ID.
 * @param string $fit      How the medium fills the stage: "cover" or "contain".
 * @return string Value for the sizes attribute.
 */
function jgor_st_stage_sizes( $media_id, $fit ) {
	if ( 'cover' !== $fit ) {
		return '100vw';
	}

	$meta = wp_get_attachment_metadata( $media_id );

	if ( ! isset( $meta['width'], $meta['height'] ) || $meta['height'] < 1 ) {
		return '100vw';
	}

	$ratio = (int) round( $meta['width'] / $meta['height'] * 100 );

	return sprintf( 'max(100vw, %dvh)', $ratio );
}

/**
 * Returns the aspect ratio of the medium of a step.
 *
 * Only images from the media library carry the necessary metadata. Everything
 * else reports 0, the caller then ignores that step.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @return float Width divided by height, or 0 when unknown.
 */
function jgor_st_media_ratio( $attributes ) {
	$media_id = isset( $attributes['mediaId'] ) ? absint( $attributes['mediaId'] ) : 0;

	if ( $media_id < 1 ) {
		return 0.0;
	}

	$meta = wp_get_attachment_metadata( $media_id );

	if ( ! isset( $meta['width'], $meta['height'] ) || $meta['height'] < 1 ) {
		return 0.0;
	}

	return round( $meta['width'] / $meta['height'], 4 );
}
