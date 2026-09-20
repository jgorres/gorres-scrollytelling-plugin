<?php
/**
 * Page template "full width" for GeneratePress.
 *
 * Registers the template, loads it from the plugin directory and switches the
 * sidebars and the width restriction off for that page. The template relies on
 * the hooks and the markup of GeneratePress and is therefore only offered when
 * that theme is active.
 *
 * @package Jgor_Scrollytelling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the slug the template is stored under.
 *
 * The slug is written to the post meta _wp_page_template and must not change
 * afterwards, otherwise pages would fall back to the default template.
 *
 * @return string Template slug.
 */
function jgor_st_template_slug() {
	return 'jgor-scrollytelling-fullwidth';
}

/**
 * Tells whether GeneratePress is the active theme.
 *
 * The constant is defined by the theme itself, so a child theme of
 * GeneratePress counts as well.
 *
 * @return bool True when GeneratePress is running.
 */
function jgor_st_is_generatepress() {
	return defined( 'GENERATE_VERSION' );
}

/**
 * Adds the template to the list in the editor.
 *
 * @param array<string, string> $templates Templates offered by the theme.
 * @return array<string, string> Templates including the plugin one.
 */
function jgor_st_page_templates( $templates ) {
	if ( ! jgor_st_is_generatepress() ) {
		return $templates;
	}

	$templates[ jgor_st_template_slug() ] = __( 'ScrollyTelling: volle Breite', 'jgor-scrollytelling' );

	return $templates;
}
add_filter( 'theme_page_templates', 'jgor_st_page_templates' );

/**
 * Tells whether the current request renders a page using the template.
 *
 * Without GeneratePress the answer is always no: a page keeps its stored
 * setting and simply falls back to the template of the active theme.
 *
 * @return bool True when the template is active.
 */
function jgor_st_is_fullwidth_template() {
	if ( ! is_singular() || ! jgor_st_is_generatepress() ) {
		return false;
	}

	return jgor_st_template_slug() === get_page_template_slug( get_queried_object_id() );
}

/**
 * Loads the template file from the plugin.
 *
 * WordPress only looks for template files inside the theme, so the file has to
 * be handed over explicitly.
 *
 * @param string $template Template file chosen by WordPress.
 * @return string Template file that is used.
 */
function jgor_st_template_include( $template ) {
	if ( ! jgor_st_is_fullwidth_template() ) {
		return $template;
	}

	$file = JGOR_ST_PATH . 'templates/fullwidth.php';

	return is_readable( $file ) ? $file : $template;
}
add_filter( 'template_include', 'jgor_st_template_include' );

/**
 * Registers the layout filters, but only on pages using the template.
 *
 * @return void
 */
function jgor_st_fullwidth_setup() {
	if ( ! jgor_st_is_fullwidth_template() ) {
		return;
	}

	add_filter( 'generate_sidebar_layout', 'jgor_st_fullwidth_sidebar_layout' );
	add_filter( 'get_post_metadata', 'jgor_st_fullwidth_content_container', 10, 3 );
}
add_action( 'template_redirect', 'jgor_st_fullwidth_setup' );

/**
 * Switches the sidebar layout off.
 *
 * @return string Layout identifier understood by GeneratePress.
 */
function jgor_st_fullwidth_sidebar_layout() {
	return 'no-sidebar';
}

/**
 * Reports a full width content container for this page.
 *
 * The theme reads the setting from the post meta. Answering the request
 * instead of writing to the database keeps the page unchanged: switching back
 * to another template restores the stored setting.
 *
 * @param mixed  $value     Value that other filters provided, null by default.
 * @param int    $object_id ID of the post the meta belongs to.
 * @param string $meta_key  Requested meta key.
 * @return mixed "true" for the GeneratePress key, otherwise the value untouched.
 */
function jgor_st_fullwidth_content_container( $value, $object_id, $meta_key ) {
	if ( '_generate-full-width-content' !== $meta_key ) {
		return $value;
	}

	if ( get_queried_object_id() !== $object_id ) {
		return $value;
	}

	// get_post_meta() with $single = true unwraps the first array entry.
	return array( 'true' );
}
