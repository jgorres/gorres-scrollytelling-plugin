<?php
/**
 * Block category and block registration.
 *
 * @package Jgor_Scrollytelling
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
			'slug'  => 'jgor-bloecke',
			'title' => __( 'JGOR-Blöcke', 'jgor-scrollytelling' ),
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
	$blocks = array( 'story', 'step' );

	foreach ( $blocks as $block ) {
		$path = JGOR_ST_PATH . 'build/' . $block;

		if ( is_readable( $path . '/block.json' ) ) {
			register_block_type_from_metadata( $path );
		}
	}
}
add_action( 'init', 'jgor_st_register_blocks' );

/**
 * Builds one item of the sticky media stage.
 *
 * Reads the media attributes of a single step block and returns the markup for
 * the stage. Steps without media produce an empty item on purpose: the script
 * keeps the previous image visible for them.
 *
 * @param array<string, mixed> $attributes Attributes of the step block.
 * @param int                  $index      Zero based position of the step.
 * @return string Markup of one stage item.
 */
function jgor_st_render_stage_item( $attributes, $index ) {
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
		'<figure class="%1$s" style="%2$s" data-jgor-st-step="%3$d">%4$s</figure>',
		esc_attr( $classes ),
		esc_attr( $styles ),
		$index,
		$inner
	);
}
