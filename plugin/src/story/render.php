<?php
/**
 * Front end markup of the story block.
 *
 * Collects the media of every step, in the story itself and in its rows, and
 * renders them as one sticky stage that fills the viewport. The text steps
 * are placed on top of it and scroll across. Without JavaScript the first
 * medium stays visible, which keeps the block readable as a plain image with
 * text.
 *
 * An afterword block follows the steps. Its markup was taken out of $content
 * by jgor_st_collect_after(); the script pins it below a limited stage.
 *
 * With "pullContent" the script also pulls the content after the block up
 * below a limited stage. That content stays where it is in the document.
 *
 * @package Scrollstage
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Rendered markup of the inner blocks.
 * @var WP_Block             $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

// Layout attributes, each one validated against the values the editor offers.
$jgor_st_text_position = isset( $attributes['textPosition'] ) && in_array( $attributes['textPosition'], array( 'left', 'center', 'right' ), true ) ? $attributes['textPosition'] : 'center';
$jgor_st_step_align    = isset( $attributes['stepAlign'] ) && in_array( $attributes['stepAlign'], array( 'start', 'center', 'end' ), true ) ? $attributes['stepAlign'] : 'center';
$jgor_st_transition    = isset( $attributes['transition'] ) && 'none' === $attributes['transition'] ? 'none' : 'fade';
$jgor_st_offset        = isset( $attributes['stickyOffset'] ) ? min( 200, absint( $attributes['stickyOffset'] ) ) : 0;
$jgor_st_step_height   = isset( $attributes['minStepHeight'] ) ? min( 200, max( 40, absint( $attributes['minStepHeight'] ) ) ) : 100;
$jgor_st_text_width    = isset( $attributes['textWidth'] ) ? min( 100, max( 20, absint( $attributes['textWidth'] ) ) ) : 45;
$jgor_st_overlay       = isset( $attributes['overlayOpacity'] ) ? min( 90, absint( $attributes['overlayOpacity'] ) ) : 35;
$jgor_st_media_fit     = isset( $attributes['mediaFit'] ) && 'contain' === $attributes['mediaFit'] ? 'contain' : 'cover';
$jgor_st_limit_stage   = ! empty( $attributes['limitStage'] ) && 'contain' === $jgor_st_media_fit;
$jgor_st_pull_content  = ! empty( $attributes['pullContent'] ) && $jgor_st_limit_stage;

// Media of the child steps, in document order; empty steps keep their slot.
$jgor_st_stage = '';
$jgor_st_index = 0;
$jgor_st_ratio = 0.0;

// Shape of a limited stage on portrait screens, and whether any step asks for one.
$jgor_st_portrait_ratio = 0.0;
$jgor_st_has_portrait   = false;

if ( isset( $block->parsed_block['innerBlocks'] ) && is_array( $block->parsed_block['innerBlocks'] ) ) {
	// Steps of the story itself and steps inside its rows, as one flat list.
	$jgor_st_steps = jgor_st_collect_steps( $block->parsed_block['innerBlocks'] );

	/*
	 * The medium that shows before the script runs: the first one on the
	 * stage. Steps before it have none or let theirs scroll along.
	 */
	$jgor_st_initial = 0;

	foreach ( $jgor_st_steps as $jgor_st_position => $jgor_st_attrs ) {
		if ( jgor_st_has_stage_medium( $jgor_st_attrs ) ) {
			$jgor_st_initial = $jgor_st_position;
			break;
		}
	}

	foreach ( $jgor_st_steps as $jgor_st_attrs ) {
		$jgor_st_stage .= jgor_st_render_stage_item( $jgor_st_attrs, $jgor_st_index, $jgor_st_media_fit, $jgor_st_index === $jgor_st_initial );
		++$jgor_st_index;

		/*
		 * The narrowest medium sets the width of the stage: only then does
		 * every step keep its text on top of its medium.
		 */
		if ( $jgor_st_limit_stage ) {
			$jgor_st_step_ratio = jgor_st_media_ratio( $jgor_st_attrs );

			if ( $jgor_st_step_ratio > 0 && ( 0.0 === $jgor_st_ratio || $jgor_st_step_ratio < $jgor_st_ratio ) ) {
				$jgor_st_ratio = $jgor_st_step_ratio;
			}

			/*
			 * On portrait screens a step shows its image for those, if it has
			 * one, and its usual medium otherwise.
			 */
			$jgor_st_step_portrait = jgor_st_portrait_ratio( $jgor_st_attrs );

			if ( $jgor_st_step_portrait > 0 ) {
				$jgor_st_has_portrait = true;
				$jgor_st_step_ratio   = $jgor_st_step_portrait;
			}

			if ( $jgor_st_step_ratio > 0 && ( 0.0 === $jgor_st_portrait_ratio || $jgor_st_step_ratio < $jgor_st_portrait_ratio ) ) {
				$jgor_st_portrait_ratio = $jgor_st_step_ratio;
			}
		}
	}
}

// Afterwords, in document order, wherever the editor placed them.
$jgor_st_after = '';

if ( $block->inner_blocks instanceof WP_Block_List ) {
	foreach ( $block->inner_blocks as $jgor_st_inner ) {
		if ( $jgor_st_inner instanceof WP_Block && 'scrollstage/after' === $jgor_st_inner->name ) {
			$jgor_st_after .= jgor_st_after_store( $jgor_st_inner );
		}
	}
}

// Without an image for portrait screens the stage keeps one shape everywhere.
$jgor_st_has_portrait = $jgor_st_has_portrait && $jgor_st_ratio > 0 && $jgor_st_portrait_ratio > 0;

$jgor_st_classes = jgor_st_story_classes(
	array(
		'text_position'  => $jgor_st_text_position,
		'step_align'     => $jgor_st_step_align,
		'media_fit'      => $jgor_st_media_fit,
		'effects'        => 'fade' === $jgor_st_transition ? array( 'fade' ) : array(),
		'stage_limited'  => $jgor_st_limit_stage && $jgor_st_ratio > 0,
		'portrait_ratio' => $jgor_st_has_portrait,
		'pull_content'   => $jgor_st_pull_content,
		'has_after'      => '' !== trim( $jgor_st_after ),
	)
);

$jgor_st_wrapper = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $jgor_st_classes ),
		'style' => sprintf(
			'--jgor-st-offset:%1$dpx;--jgor-st-step-min:%2$dsvh;--jgor-st-text-width:%3$d%%;--jgor-st-overlay:%4$s;%5$s%6$s',
			$jgor_st_offset,
			$jgor_st_step_height,
			$jgor_st_text_width,
			round( $jgor_st_overlay / 100, 2 ),
			$jgor_st_ratio > 0 ? sprintf( '--jgor-st-stage-ratio:%s;', $jgor_st_ratio ) : '',
			$jgor_st_has_portrait ? sprintf( '--jgor-st-stage-ratio-portrait:%s;', $jgor_st_portrait_ratio ) : ''
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
	<?php if ( '' !== trim( $jgor_st_after ) ) : ?>
		<div class="jgor-st-story__after">
			<?php echo $jgor_st_after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already rendered block content. ?>
		</div>
	<?php endif; ?>
</div>
