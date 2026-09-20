<?php
/**
 * Front end markup of the story block.
 *
 * Collects the media of every child step and renders them as one sticky stage
 * in front of the scrolling text column. Without JavaScript the first item
 * stays visible, which keeps the block readable as a plain image and text
 * section.
 *
 * @package Jgor_Scrollytelling
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Rendered markup of the inner blocks.
 * @var WP_Block             $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

// Layout attributes, each one validated against the values the editor offers.
$jgor_st_media_position = isset( $attributes['mediaPosition'] ) && 'right' === $attributes['mediaPosition'] ? 'right' : 'left';
$jgor_st_step_align     = isset( $attributes['stepAlign'] ) && in_array( $attributes['stepAlign'], array( 'start', 'center', 'end' ), true ) ? $attributes['stepAlign'] : 'center';
$jgor_st_transition     = isset( $attributes['transition'] ) && 'none' === $attributes['transition'] ? 'none' : 'fade';
$jgor_st_offset         = isset( $attributes['stickyOffset'] ) ? min( 200, absint( $attributes['stickyOffset'] ) ) : 0;
$jgor_st_step_height    = isset( $attributes['minStepHeight'] ) ? min( 200, max( 40, absint( $attributes['minStepHeight'] ) ) ) : 100;

// Media of the child steps, in document order; empty steps keep their slot.
$jgor_st_stage = '';
$jgor_st_index = 0;

if ( isset( $block->parsed_block['innerBlocks'] ) && is_array( $block->parsed_block['innerBlocks'] ) ) {
	foreach ( $block->parsed_block['innerBlocks'] as $jgor_st_child ) {
		if ( ! isset( $jgor_st_child['blockName'] ) || 'jgor-scrollytelling/step' !== $jgor_st_child['blockName'] ) {
			continue;
		}

		$jgor_st_attrs  = isset( $jgor_st_child['attrs'] ) && is_array( $jgor_st_child['attrs'] ) ? $jgor_st_child['attrs'] : array();
		$jgor_st_stage .= jgor_st_render_stage_item( $jgor_st_attrs, $jgor_st_index );
		++$jgor_st_index;
	}
}

$jgor_st_classes = array(
	'jgor-st-story',
	'is-media-' . $jgor_st_media_position,
	'is-align-' . $jgor_st_step_align,
	'fade' === $jgor_st_transition ? 'has-fade' : 'no-fade',
);

$jgor_st_wrapper = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $jgor_st_classes ),
		'style' => sprintf(
			'--jgor-st-offset:%1$dpx;--jgor-st-step-min:%2$dsvh;',
			$jgor_st_offset,
			$jgor_st_step_height
		),
	)
);
?>
<div <?php echo $jgor_st_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes(). ?>>
	<?php if ( '' !== $jgor_st_stage ) : ?>
		<div class="jgor-st-story__stage">
			<?php echo $jgor_st_stage; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in jgor_st_render_stage_item(). ?>
		</div>
	<?php endif; ?>
	<div class="jgor-st-story__steps">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already rendered block content. ?>
	</div>
</div>
