<?php
/**
 * Additions to the admin screens.
 *
 * @package Gorres_Scrollytelling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds the documentation and donation links to the row of the plugin in the
 * plugin list.
 *
 * The links follow version, author and details. Both leave the site and open
 * in a new tab, which is why they carry rel="noopener noreferrer". The
 * documentation is a help site that runs in WordPress Playground.
 *
 * The list of links passes through the filters of other plugins first, so it
 * is only touched when it still is an array.
 *
 * @param mixed $links Meta links of the row, an array of HTML strings.
 * @param mixed $file  Path of the plugin file, relative to the plugins directory.
 * @return mixed Links, for this plugin with the two links at the end.
 */
function jgor_st_plugin_row_meta( $links, $file ) {
	if ( ! is_array( $links ) || plugin_basename( JGOR_ST_FILE ) !== $file ) {
		return $links;
	}

	$links[] = sprintf(
		'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
		esc_url( JGOR_ST_HELP_URL ),
		esc_html__( 'Documentation', 'gorres-scrollytelling' )
	);

	$links[] = sprintf(
		'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
		esc_url( 'https://ko-fi.com/joerngorres/' ),
		esc_html__( 'Buy the plugin author a Mercedes-Benz 😉', 'gorres-scrollytelling' )
	);

	return $links;
}
add_filter( 'plugin_row_meta', 'jgor_st_plugin_row_meta', 10, 2 );
