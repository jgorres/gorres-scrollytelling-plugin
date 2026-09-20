<?php
/**
 * Front end markup of the step block.
 *
 * The step renders its text only. Its medium is part of the sticky stage that
 * the parent story block builds, which is why nothing media related shows up
 * here.
 *
 * @package Jgor_Scrollytelling
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
?>
<div <?php echo $jgor_st_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes(). ?>>
	<div class="jgor-st-step__content">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already rendered block content. ?>
	</div>
</div>
