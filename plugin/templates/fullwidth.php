<?php
/**
 * Page template: full width, no sidebars.
 *
 * Built for GeneratePress and following the structure of its own page.php, so
 * header, footer and every generate_* hook keep working. Only the sidebars and
 * the width restriction are dropped, and the content is printed without the
 * article frame: the story is meant to touch the edges of the screen.
 *
 * Page title and comments are left out on purpose: a story opens with its own
 * first step and ends with the last one.
 *
 * The file is loaded through jgor_st_template_include(); WordPress itself only
 * looks for templates inside the theme.
 *
 * @package Jgor_Scrollytelling
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
	<div <?php generate_do_attr( 'content' ); ?>>
		<main <?php generate_do_attr( 'main' ); ?>>
			<?php
			do_action( 'generate_before_main_content' );

			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;

			do_action( 'generate_after_main_content' );
			?>
		</main>
	</div>

	<?php
	do_action( 'generate_after_primary_content_area' );

	get_footer();
