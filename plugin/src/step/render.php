<?php
/**
 * Front end markup of the step block.
 *
 * The step renders its text. Its medium is part of the sticky stage that the
 * parent story block builds, which is why nothing media related shows up
 * here, with one exception: a medium that scrolls along with the step lies in
 * the step, at least as tall as the screen, and leaves at the top with it.
 *
 * Typography, text colour and margin sit on the step. Background, border,
 * shadow and padding go to the text box inside it, see
 * jgor_st_step_box_attributes().
 *
 * The effect the text box appears and disappears with comes from the step
 * itself or, handed down as block context, from the story. The same goes for
 * the height of the step, which the story sets as a custom property, and for
 * the position of the text box, which the story sets as classes.
 *
 * A pinned text box gets one more element around it. That element sticks to
 * the top of the screen and is as tall as the screen, so the box keeps its
 * place while the rest of the step scrolls by.
 *
 * @package Gorres_Scrollytelling
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
$jgor_st_classes = 'jgor-st-step';

// Effect of the text box: the step's own choice, otherwise the one of the story.
$jgor_st_effect = jgor_st_resolve_text_effect(
	$attributes['textEffect'] ?? '',
	$block->context['gorres-scrollytelling/textEffect'] ?? ''
);

/*
 * The effects belong to a box that enters and leaves the screen from below
 * and at the top. Inside a row the boxes pass sideways, there is nothing for
 * the effects to hold on to.
 */
$jgor_st_in_row = ! empty( $block->context['gorres-scrollytelling/inRow'] );

if ( $jgor_st_in_row ) {
	$jgor_st_effect = '';
}

/*
 * Pinned: the box does not travel across the screen, which is what the
 * effects are made for, so it goes without one. A row keeps its steps in
 * place itself.
 */
$jgor_st_pinned = ! empty( $attributes['pinText'] ) && ! $jgor_st_in_row;

if ( $jgor_st_pinned ) {
	$jgor_st_effect   = '';
	$jgor_st_classes .= ' is-pinned';
}

/*
 * Medium that scrolls along: the step carries it itself, at its upper end. A
 * row keeps the media of its steps on the stage, see jgor_st_collect_steps().
 */
$jgor_st_cover = '';

if ( ! $jgor_st_in_row && jgor_st_has_scrolling_medium( $attributes ) ) {
	$jgor_st_fit   = isset( $block->context['gorres-scrollytelling/mediaFit'] ) && 'contain' === $block->context['gorres-scrollytelling/mediaFit'] ? 'contain' : 'cover';
	$jgor_st_parts = jgor_st_medium_parts( $attributes, $jgor_st_fit, ! empty( $block->context['gorres-scrollytelling/firstStep'] ) );

	if ( '' !== $jgor_st_parts['markup'] ) {
		$jgor_st_classes .= ' has-cover';
		$jgor_st_cover    = sprintf(
			'<div class="%1$s" style="%2$s">%3$s</div>',
			esc_attr( trim( 'jgor-st-step__cover ' . $jgor_st_parts['class'] ) ),
			esc_attr( $jgor_st_parts['style'] ),
			$jgor_st_parts['markup']
		);
	}
}

// Position of the text box, where the step does not follow the story.
foreach ( jgor_st_step_position_classes( $attributes ) as $jgor_st_class ) {
	$jgor_st_classes .= ' ' . sanitize_html_class( $jgor_st_class );
}

if ( '' !== $jgor_st_effect ) {
	$jgor_st_classes .= ' has-text-effect is-text-effect-' . sanitize_html_class( $jgor_st_effect );
}

$jgor_st_wrapper_args = array( 'class' => $jgor_st_classes );

/*
 * A height of its own overrides the step height of the story for this step
 * alone; the stylesheet reads the same variable in both cases. The value is
 * an integer within fixed bounds, see jgor_st_step_min_height().
 */
$jgor_st_min_height = jgor_st_step_min_height(
	$attributes['minHeight'] ?? 0,
	$jgor_st_in_row
);

if ( $jgor_st_min_height > 0 ) {
	$jgor_st_wrapper_args['style'] = sprintf( '--jgor-st-step-min:%dsvh;', $jgor_st_min_height );

	// A medium that scrolls along then fills the step instead of setting its height.
	$jgor_st_wrapper_args['class'] .= ' has-own-height';
}

$jgor_st_wrapper = get_block_wrapper_attributes( $jgor_st_wrapper_args );
$jgor_st_box     = jgor_st_step_box_attributes( $attributes );

// Attributes of the text box, escaped here and echoed as they are.
$jgor_st_content = sprintf( 'class="%s"', esc_attr( trim( 'jgor-st-step__content ' . $jgor_st_box['class'] ) ) );

if ( '' !== $jgor_st_box['style'] ) {
	$jgor_st_content .= sprintf( ' style="%s"', esc_attr( $jgor_st_box['style'] ) );
}
?>
<div <?php echo $jgor_st_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes(). ?>>
	<?php echo $jgor_st_cover; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above and in jgor_st_medium_parts(). ?>
	<?php if ( $jgor_st_pinned ) : ?>
		<div class="jgor-st-step__pin">
	<?php endif; ?>
	<div <?php echo $jgor_st_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped with esc_attr() above. ?>>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already rendered block content. ?>
	</div>
	<?php if ( $jgor_st_pinned ) : ?>
		</div>
	<?php endif; ?>
</div>
