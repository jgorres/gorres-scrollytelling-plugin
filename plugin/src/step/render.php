<?php
/**
 * Front end markup of the step block.
 *
 * The step renders its text only. Its medium is part of the sticky stage that
 * the parent story block builds, which is why nothing media related shows up
 * here.
 *
 * Typography, text colour and margin sit on the step. Background, border,
 * shadow and padding go to the text box inside it, see
 * jgor_st_step_box_attributes().
 *
 * @package Scrollstage
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Rendered markup of the inner blocks.
 * @var WP_Block             $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

/*
 * A step without text is rendered as well: it still occupies its share of the
 * scrolling distance, and the script relies on every step having a counterpart
 * on the stage.
 */
$jgor_st_wrapper = get_block_wrapper_attributes( array( 'class' => 'jgor-st-step' ) );
$jgor_st_box     = jgor_st_step_box_attributes( $attributes );

// Attributes of the text box, escaped here and echoed as they are.
$jgor_st_content = sprintf( 'class="%s"', esc_attr( trim( 'jgor-st-step__content ' . $jgor_st_box['class'] ) ) );

if ( '' !== $jgor_st_box['style'] ) {
	$jgor_st_content .= sprintf( ' style="%s"', esc_attr( $jgor_st_box['style'] ) );
}
?>
<div <?php echo $jgor_st_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes(). ?>>
	<div <?php echo $jgor_st_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped with esc_attr() above. ?>>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already rendered block content. ?>
	</div>
</div>
