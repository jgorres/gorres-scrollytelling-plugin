<?php
/**
 * Page template "full width" shipped with the plugin.
 *
 * Registers the template, loads it from the plugin directory and removes the
 * width restriction and the sidebars for that page. GeneratePress is steered
 * through its own filters, every other theme through a stylesheet.
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
 * Adds the template to the list in the editor.
 *
 * Block themes build their templates from HTML files in the theme and ignore
 * PHP templates, so the entry is only offered for classic themes.
 *
 * @param array<string, string> $templates Templates offered by the theme.
 * @return array<string, string> Templates including the plugin one.
 */
function jgor_st_page_templates( $templates ) {
	if ( wp_is_block_theme() ) {
		return $templates;
	}

	$templates[ jgor_st_template_slug() ] = __( 'ScrollyTelling: volle Breite', 'jgor-scrollytelling' );

	return $templates;
}
add_filter( 'theme_page_templates', 'jgor_st_page_templates' );

/**
 * Tells whether the current request renders a page using the template.
 *
 * Under a block theme the answer is always no: the template file works with
 * get_header() and get_footer(), which such themes do not provide. A page
 * keeps its stored setting and simply falls back to the theme template.
 *
 * @return bool True when the template is active.
 */
function jgor_st_is_fullwidth_template() {
	if ( ! is_singular() || wp_is_block_theme() ) {
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
 * Registers everything the template needs, but only where it is used.
 *
 * @return void
 */
function jgor_st_fullwidth_setup() {
	if ( ! jgor_st_is_fullwidth_template() ) {
		return;
	}

	add_filter( 'generate_sidebar_layout', 'jgor_st_fullwidth_sidebar_layout' );
	add_filter( 'get_post_metadata', 'jgor_st_fullwidth_generatepress_meta', 10, 3 );
	add_filter( 'sidebars_widgets', 'jgor_st_fullwidth_sidebars_widgets' );
	add_filter( 'body_class', 'jgor_st_fullwidth_body_class' );
	add_action( 'wp_enqueue_scripts', 'jgor_st_fullwidth_styles' );
}
add_action( 'template_redirect', 'jgor_st_fullwidth_setup' );

/**
 * GeneratePress: switches the sidebar layout off.
 *
 * @return string Layout identifier understood by GeneratePress.
 */
function jgor_st_fullwidth_sidebar_layout() {
	return 'no-sidebar';
}

/**
 * GeneratePress: reports a full width content container for this page.
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
function jgor_st_fullwidth_generatepress_meta( $value, $object_id, $meta_key ) {
	if ( '_generate-full-width-content' !== $meta_key ) {
		return $value;
	}

	if ( get_queried_object_id() !== $object_id ) {
		return $value;
	}

	// get_post_meta() with $single = true unwraps the first array entry.
	return array( 'true' );
}

/**
 * Empties the content sidebars for this page.
 *
 * Themes that check is_active_sidebar() then skip their sidebar markup. Only
 * areas whose name starts with "sidebar" are cleared, so footer widgets stay
 * where they are.
 *
 * @param array<string, mixed> $sidebars_widgets Widgets per sidebar.
 * @return array<string, mixed> Widgets with the content sidebars emptied.
 */
function jgor_st_fullwidth_sidebars_widgets( $sidebars_widgets ) {
	foreach ( array_keys( $sidebars_widgets ) as $sidebar ) {
		if ( is_string( $sidebar ) && 0 === strpos( $sidebar, 'sidebar' ) ) {
			$sidebars_widgets[ $sidebar ] = array();
		}
	}

	return $sidebars_widgets;
}

/**
 * Marks the page with a body class the stylesheet can hook into.
 *
 * @param array<int, string> $classes Body classes.
 * @return array<int, string> Body classes including the plugin one.
 */
function jgor_st_fullwidth_body_class( $classes ) {
	$classes[] = 'jgor-st-fullwidth';

	return $classes;
}

/**
 * Loads the stylesheet of the template.
 *
 * @return void
 */
function jgor_st_fullwidth_styles() {
	wp_enqueue_style(
		'jgor-scrollytelling-template',
		JGOR_ST_URL . 'assets/css/jgor-st-template.css',
		array(),
		JGOR_ST_VERSION
	);
}
