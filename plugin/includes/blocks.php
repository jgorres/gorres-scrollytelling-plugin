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
	$blocks = array( 'story', 'step', 'row', 'after' );

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
 * Collects the attributes of all steps of a story in document order.
 *
 * A step sits either in the story itself or inside a row. The stage of the
 * story needs one item per step, whatever its parent, and in the order in
 * which the visitor meets the steps.
 *
 * A medium only scrolls along with a step of the story itself. For the steps
 * of a row the attribute is dropped here, so the stage keeps their media.
 *
 * @param array<int, array<string, mixed>> $inner_blocks Parsed inner blocks of
 *                                                       a story or a row.
 * @param bool                             $in_row       Whether the blocks are
 *                                                       the children of a row.
 * @return array<int, array<string, mixed>> Attributes of every step.
 */
function jgor_st_collect_steps( $inner_blocks, $in_row = false ) {
	$steps = array();

	foreach ( $inner_blocks as $child ) {
		$name = isset( $child['blockName'] ) ? $child['blockName'] : '';

		if ( 'scrollstage/step' === $name ) {
			$attrs = isset( $child['attrs'] ) && is_array( $child['attrs'] ) ? $child['attrs'] : array();

			if ( $in_row ) {
				unset( $attrs['mediaScroll'] );
			}

			$steps[] = $attrs;
		} elseif ( 'scrollstage/row' === $name && isset( $child['innerBlocks'] ) && is_array( $child['innerBlocks'] ) ) {
			$steps = array_merge( $steps, jgor_st_collect_steps( $child['innerBlocks'], true ) );
		}
	}

	return $steps;
}

/**
 * Tells the steps of a row that they sit in one.
 *
 * A step has no way to look at its parent while it renders. The context it
 * receives can be extended, though, and "scrollstage/inRow" is listed in the
 * usesContext of the step block.
 *
 * @param array<string, mixed> $context      Context of the block to render.
 * @param array<string, mixed> $parsed_block Parsed block, unused.
 * @param WP_Block|null        $parent_block Parent of the block, if any.
 * @return array<string, mixed> Context, marked for blocks inside a row.
 */
function jgor_st_row_context( $context, $parsed_block = array(), $parent_block = null ) {
	unset( $parsed_block );

	if ( $parent_block instanceof WP_Block && 'scrollstage/row' === $parent_block->name ) {
		$context['scrollstage/inRow'] = true;
	}

	return $context;
}
add_filter( 'render_block_context', 'jgor_st_row_context', 10, 3 );

/**
 * Tells the first step of a story that it opens the story.
 *
 * A medium that scrolls along with its step is rendered by the step, which
 * does not know its place. The one that opens a story is most likely on
 * screen when the page loads and must not be loaded lazily.
 *
 * Blocks are compared by value: a later step with exactly the same content as
 * the first one is marked as well, which only costs its lazy loading.
 *
 * @param array<string, mixed> $context      Context of the block to render.
 * @param array<string, mixed> $parsed_block Parsed block.
 * @param WP_Block|null        $parent_block Parent of the block, if any.
 * @return array<string, mixed> Context, marked for the first step of a story.
 */
function jgor_st_first_step_context( $context, $parsed_block = array(), $parent_block = null ) {
	if ( ! $parent_block instanceof WP_Block || 'scrollstage/story' !== $parent_block->name ) {
		return $context;
	}

	if ( ! isset( $parsed_block['blockName'] ) || 'scrollstage/step' !== $parsed_block['blockName'] ) {
		return $context;
	}

	$siblings = isset( $parent_block->parsed_block['innerBlocks'] ) && is_array( $parent_block->parsed_block['innerBlocks'] ) ? $parent_block->parsed_block['innerBlocks'] : array();
	$first    = reset( $siblings );

	if ( is_array( $first ) && $first === $parsed_block ) {
		$context['scrollstage/firstStep'] = true;
	}

	return $context;
}
add_filter( 'render_block_context', 'jgor_st_first_step_context', 10, 3 );

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
 *     @type bool     $portrait_ratio Whether the limited stage has a shape of
 *                                    its own on portrait screens.
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

		// Media for portrait screens can give the stage another shape there.
		if ( ! empty( $state['portrait_ratio'] ) ) {
			$classes[] = 'has-portrait-ratio';
		}
	}

	if ( ! empty( $state['has_after'] ) ) {
		$classes[] = 'has-after';
	}

	return $classes;
}

/**
 * Returns the effects a text box can appear and disappear with.
 *
 * The names end up in the class "is-text-effect-<name>" of a step; the
 * stylesheet of the step block holds one set of rules per name.
 *
 * @return string[] Effect names.
 */
function jgor_st_text_effects() {
	return array( 'fade', 'slide', 'zoom', 'rotate', 'dissolve' );
}

/**
 * Resolves the text effect of a step.
 *
 * A step follows the story unless it sets a value of its own. Everything that
 * is not a known effect, "none" included, means no effect.
 *
 * @param mixed $own       Value of the step; an empty string follows the story.
 * @param mixed $inherited Value of the story, handed down as block context.
 * @return string Effect name, or an empty string for no effect.
 */
function jgor_st_resolve_text_effect( $own, $inherited ) {
	$effects = jgor_st_text_effects();
	$value   = is_string( $own ) && '' !== $own ? $own : $inherited;

	return is_string( $value ) && in_array( $value, $effects, true ) ? $value : '';
}

/**
 * Returns the classes for the position a step gives its own text box.
 *
 * A step follows the story unless it names a position of its own, for each
 * direction separately. The class names are the ones the story uses on its
 * wrapper; on a step they only count for that step.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @return string[] Class names, none when the step follows the story.
 */
function jgor_st_step_position_classes( $attributes ) {
	$classes = array();

	if ( isset( $attributes['textPosition'] ) && in_array( $attributes['textPosition'], array( 'left', 'center', 'right' ), true ) ) {
		$classes[] = 'is-text-' . $attributes['textPosition'];
	}

	if ( isset( $attributes['stepAlign'] ) && in_array( $attributes['stepAlign'], array( 'start', 'center', 'end' ), true ) ) {
		$classes[] = 'is-align-' . $attributes['stepAlign'];
	}

	return $classes;
}

/**
 * Returns the height a step asks for itself.
 *
 * A step is as tall as the story says unless it sets a height of its own, in
 * percent of the screen height. Lower steps bring their text boxes closer
 * together: followed by steps without a medium, several boxes scroll across
 * the same medium one after the other.
 *
 * Inside a row the value does not count, because the distance a row is
 * scrolled through is the number of its steps times the step height of the
 * story.
 *
 * @param mixed $value  Attribute "minHeight" of the step block.
 * @param bool  $in_row Whether the step sits inside a row.
 * @return int Height in percent of the screen height, between 20 and 200, or
 *             0 when the step follows the story.
 */
function jgor_st_step_min_height( $value, $in_row = false ) {
	if ( $in_row || ! is_numeric( $value ) || (int) $value <= 0 ) {
		return 0;
	}

	return min( 200, max( 20, (int) $value ) );
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
 * Tells whether the medium of a step scrolls along with the step.
 *
 * Such a medium is not part of the stage. It lies in the step itself, as tall
 * as the screen, and leaves at the top like the text does, while the stage
 * behind it already shows what comes next. Only an image can do that: the
 * script that starts and stops videos only looks at the stage.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @return bool True when the step has an image that scrolls along.
 */
function jgor_st_has_scrolling_medium( $attributes ) {
	if ( empty( $attributes['mediaScroll'] ) ) {
		return false;
	}

	if ( isset( $attributes['mediaType'] ) && 'video' === $attributes['mediaType'] ) {
		return false;
	}

	$media_id  = isset( $attributes['mediaId'] ) ? absint( $attributes['mediaId'] ) : 0;
	$media_url = isset( $attributes['mediaUrl'] ) ? (string) $attributes['mediaUrl'] : '';

	return $media_id > 0 || '' !== $media_url;
}

/**
 * Tells whether a step puts a medium on the stage.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @return bool False for a step without a medium and for one whose medium
 *              scrolls along with it.
 */
function jgor_st_has_stage_medium( $attributes ) {
	$media_id  = isset( $attributes['mediaId'] ) ? absint( $attributes['mediaId'] ) : 0;
	$media_url = isset( $attributes['mediaUrl'] ) ? (string) $attributes['mediaUrl'] : '';

	if ( '' === $media_url && 0 === $media_id ) {
		return false;
	}

	return ! jgor_st_has_scrolling_medium( $attributes );
}

/**
 * Builds the markup of the medium of a step.
 *
 * The same medium can end up in two places: on the stage of the story or, when
 * it scrolls along, in the step. Both need the element itself, a marker for an
 * image that has a second one for portrait screens, and the focal points as
 * custom properties for the element around it.
 *
 * An image can come with a second one for portrait screens. Both share one
 * picture element, so the browser only loads the one that fits the screen.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @param string               $fit        How the medium fills its frame:
 *                                         "cover" or "contain".
 * @param bool                 $eager      Whether the medium is on screen when
 *                                         the page loads.
 * @return array{markup: string, class: string, style: string} Markup of the
 *         medium, class for the element around it and its inline style; all
 *         of them empty for a step without a medium.
 */
function jgor_st_medium_parts( $attributes, $fit = 'cover', $eager = false ) {
	$media_id   = isset( $attributes['mediaId'] ) ? absint( $attributes['mediaId'] ) : 0;
	$media_url  = isset( $attributes['mediaUrl'] ) ? (string) $attributes['mediaUrl'] : '';
	$media_alt  = isset( $attributes['mediaAlt'] ) ? (string) $attributes['mediaAlt'] : '';
	$media_type = isset( $attributes['mediaType'] ) && 'video' === $attributes['mediaType'] ? 'video' : 'image';

	$class  = '';
	$styles = '';

	// Focal point is stored as floats between 0 and 1 and becomes object-position.
	if ( isset( $attributes['focalPoint']['x'], $attributes['focalPoint']['y'] ) ) {
		$styles = sprintf(
			'--jgor-st-focal-x:%1$s%%;--jgor-st-focal-y:%2$s%%;',
			round( (float) $attributes['focalPoint']['x'] * 100, 2 ),
			round( (float) $attributes['focalPoint']['y'] * 100, 2 )
		);
	}

	$inner  = '';
	$source = jgor_st_portrait_source( $attributes, $fit );

	if ( '' !== $source ) {
		$class = 'has-portrait';

		// The image for portrait screens has a focal point of its own.
		if ( isset( $attributes['portraitFocalPoint']['x'], $attributes['portraitFocalPoint']['y'] ) ) {
			$styles .= sprintf(
				'--jgor-st-portrait-focal-x:%1$s%%;--jgor-st-portrait-focal-y:%2$s%%;',
				round( (float) $attributes['portraitFocalPoint']['x'] * 100, 2 ),
				round( (float) $attributes['portraitFocalPoint']['y'] * 100, 2 )
			);
		}
	}

	if ( 'video' === $media_type && '' !== $media_url ) {
		$inner = sprintf(
			'<video class="jgor-st-stage__media" src="%1$s" muted playsinline loop preload="metadata"></video>',
			esc_url( $media_url )
		);
	} elseif ( $media_id > 0 ) {
		$image_attr = array(
			'class'   => 'jgor-st-stage__media',
			'loading' => $eager ? 'eager' : 'lazy',
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
			$eager ? 'eager' : 'lazy'
		);
	}

	if ( '' !== $source && '' !== $inner ) {
		$inner = '<picture class="jgor-st-stage__picture">' . $source . $inner . '</picture>';
	}

	if ( '' === $inner ) {
		return array(
			'markup' => '',
			'class'  => '',
			'style'  => '',
		);
	}

	return array(
		'markup' => $inner,
		'class'  => $class,
		'style'  => $styles,
	);
}

/**
 * Builds one item of the sticky media stage.
 *
 * Reads the media attributes of a single step block and returns the markup for
 * the stage. Steps without a medium on the stage produce an empty item on
 * purpose: the script keeps the previous image visible for them, and at the
 * start of a story the one that follows.
 *
 * Every item except the visible one is hidden from assistive technology: all
 * media of the story live in the document at once, and without that a screen
 * reader would read every alternative text in a row before reaching the first
 * text. The script moves the marker along with the visible medium.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @param int                  $index      Zero based position of the step.
 * @param string               $fit        How the medium fills the stage:
 *                                         "cover" or "contain".
 * @param bool|null            $visible    Whether this item shows before the
 *                                         script runs; null means the first.
 * @return string Markup of one stage item.
 */
function jgor_st_render_stage_item( $attributes, $index, $fit = 'cover', $visible = null ) {
	$visible = null === $visible ? 0 === $index : (bool) $visible;
	$classes = 'jgor-st-stage__item';
	$parts   = array(
		'markup' => '',
		'class'  => '',
		'style'  => '',
	);

	if ( jgor_st_has_stage_medium( $attributes ) ) {
		$parts = jgor_st_medium_parts( $attributes, $fit, $visible );
	}

	if ( '' === $parts['markup'] ) {
		$classes .= ' is-empty';
	} elseif ( '' !== $parts['class'] ) {
		$classes .= ' ' . $parts['class'];
	}

	if ( $visible ) {
		$classes .= ' is-initial';
	}

	return sprintf(
		'<figure class="%1$s" style="%2$s" data-jgor-st-step="%3$d"%4$s>%5$s</figure>',
		esc_attr( $classes ),
		esc_attr( $parts['style'] ),
		$index,
		$visible ? '' : ' aria-hidden="true"',
		$parts['markup']
	);
}

/**
 * Tells whether a step carries an image for portrait screens.
 *
 * Only an image can have one: the browser picks between the two through a
 * picture element, which a video cannot be part of.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @return bool True when the step has an image and a second one for portrait
 *              screens.
 */
function jgor_st_has_portrait_medium( $attributes ) {
	$media_id     = isset( $attributes['mediaId'] ) ? absint( $attributes['mediaId'] ) : 0;
	$media_url    = isset( $attributes['mediaUrl'] ) ? (string) $attributes['mediaUrl'] : '';
	$portrait_id  = isset( $attributes['portraitId'] ) ? absint( $attributes['portraitId'] ) : 0;
	$portrait_url = isset( $attributes['portraitUrl'] ) ? (string) $attributes['portraitUrl'] : '';

	if ( isset( $attributes['mediaType'] ) && 'video' === $attributes['mediaType'] ) {
		return false;
	}

	if ( '' === $media_url && 0 === $media_id ) {
		return false;
	}

	return $portrait_id > 0 || '' !== $portrait_url;
}

/**
 * Builds the source element for the image of a step on portrait screens.
 *
 * A landscape image that fills a phone held upright loses most of its width.
 * A step can therefore name a second image, which replaces the first one as
 * long as the screen is taller than wide.
 *
 * An image from the media library comes with all its sizes; without the
 * attachment, the stored address is used as it is.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @param string               $fit        How the medium fills the stage:
 *                                         "cover" or "contain".
 * @return string Markup of the source element, or an empty string when the
 *                step has no image for portrait screens.
 */
function jgor_st_portrait_source( $attributes, $fit = 'cover' ) {
	if ( ! jgor_st_has_portrait_medium( $attributes ) ) {
		return '';
	}

	$portrait_id  = isset( $attributes['portraitId'] ) ? absint( $attributes['portraitId'] ) : 0;
	$portrait_url = isset( $attributes['portraitUrl'] ) ? (string) $attributes['portraitUrl'] : '';
	$srcset       = '';
	$sizes        = '';

	if ( $portrait_id > 0 && wp_attachment_is_image( $portrait_id ) ) {
		$candidates = wp_get_attachment_image_srcset( $portrait_id, 'full' );

		if ( is_string( $candidates ) && '' !== $candidates ) {
			$srcset = $candidates;
			$sizes  = jgor_st_stage_sizes( $portrait_id, $fit );
		} else {
			// Small images have no further sizes and therefore no candidates.
			$full   = wp_get_attachment_image_url( $portrait_id, 'full' );
			$srcset = is_string( $full ) ? esc_url( $full ) : '';
		}
	}

	if ( '' === $srcset && '' !== $portrait_url ) {
		$srcset = esc_url( $portrait_url );
	}

	if ( '' === $srcset ) {
		return '';
	}

	return sprintf(
		'<source media="(orientation: portrait)" srcset="%1$s"%2$s />',
		esc_attr( $srcset ),
		'' !== $sizes ? sprintf( ' sizes="%s"', esc_attr( $sizes ) ) : ''
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
 * Returns the aspect ratio of an attachment.
 *
 * @param int $attachment_id Attachment ID.
 * @return float Width divided by height, or 0 when unknown.
 */
function jgor_st_attachment_ratio( $attachment_id ) {
	$attachment_id = absint( $attachment_id );

	if ( $attachment_id < 1 ) {
		return 0.0;
	}

	$meta = wp_get_attachment_metadata( $attachment_id );

	if ( ! isset( $meta['width'], $meta['height'] ) || $meta['height'] < 1 ) {
		return 0.0;
	}

	return round( $meta['width'] / $meta['height'], 4 );
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
	return jgor_st_attachment_ratio( isset( $attributes['mediaId'] ) ? absint( $attributes['mediaId'] ) : 0 );
}

/**
 * Returns the aspect ratio of the image of a step for portrait screens.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @return float Width divided by height, or 0 when the step has no such image
 *               or its ratio is unknown.
 */
function jgor_st_portrait_ratio( $attributes ) {
	if ( ! jgor_st_has_portrait_medium( $attributes ) ) {
		return 0.0;
	}

	return jgor_st_attachment_ratio( isset( $attributes['portraitId'] ) ? absint( $attributes['portraitId'] ) : 0 );
}
