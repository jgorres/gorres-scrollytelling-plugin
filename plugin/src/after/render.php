<?php
/**
 * Front end markup of the afterword block.
 *
 * The output does not stay where the block was rendered: jgor_st_collect_after()
 * takes it out of the stream and the parent story block places it behind its
 * steps.
 *
 * @package Gorres_Scrollytelling
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Rendered markup of the inner blocks.
 * @var WP_Block             $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$jgor_st_wrapper = get_block_wrapper_attributes( array( 'class' => 'jgor-st-after' ) );
?>
<div <?php echo $jgor_st_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes(). ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already rendered block content. ?>
</div>
