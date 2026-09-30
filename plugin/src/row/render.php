<?php
/**
 * Front end markup of the row block.
 *
 * A row wraps its steps in a viewport and a track: the viewport is the part
 * that stays on screen, the track the part that moves sideways inside it. The
 * number of steps goes out as a custom property, the styles turn it into the
 * scrolling distance of the row.
 *
 * The media of the steps are not rendered here. Like those of all other
 * steps they are part of the sticky stage of the parent story block.
 *
 * @package Scrollstage
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Rendered markup of the inner blocks.
 * @var WP_Block             $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$jgor_st_inner = isset( $block->parsed_block['innerBlocks'] ) && is_array( $block->parsed_block['innerBlocks'] ) ? $block->parsed_block['innerBlocks'] : array();
$jgor_st_count = count( jgor_st_collect_steps( $jgor_st_inner ) );

$jgor_st_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'jgor-st-row',
		'style' => sprintf( '--jgor-st-row-count:%d;', $jgor_st_count ),
	)
);
?>
<div <?php echo $jgor_st_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes(). ?>>
	<div class="jgor-st-row__viewport">
		<div class="jgor-st-row__track">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already rendered block content. ?>
		</div>
	</div>
</div>
