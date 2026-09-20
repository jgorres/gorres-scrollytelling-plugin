<?php
/**
 * Page template: full width, no sidebars.
 *
 * Header and footer of the theme stay in place, only the content runs across
 * the full width. Page title and comments are left out on purpose: a story
 * opens with its own first step and ends with the last one.
 *
 * The file is loaded through jgor_st_template_include(); WordPress itself only
 * looks for templates inside the theme.
 *
 * @package Jgor_Scrollytelling
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="jgor-st-content" class="jgor-st-template">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php
get_footer();
