<?php
/**
 * Additions to the admin screens.
 *
 * @package Gorres_Scrollytelling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds a donation link to the row of the plugin in the plugin list.
 *
 * The link follows version, author and details. It leaves the site and opens
 * in a new tab, which is why it carries rel="noopener noreferrer".
 *
 * The list of links passes through the filters of other plugins first, so it
 * is only touched when it still is an array.
 *
 * @param mixed $links Meta links of the row, an array of HTML strings.
 * @param mixed $file  Path of the plugin file, relative to the plugins directory.
 * @return mixed Links, for this plugin with the donation link at the end.
 */
function jgor_st_plugin_row_meta( $links, $file ) {
	if ( ! is_array( $links ) || plugin_basename( JGOR_ST_FILE ) !== $file ) {
		return $links;
	}

	$links[] = sprintf(
		'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
		esc_url( 'https://ko-fi.com/joerngorres/' ),
		esc_html__( 'Buy the plugin author a Mercedes-Benz 😉', 'gorres-scrollytelling' )
	);

	return $links;
}
add_filter( 'plugin_row_meta', 'jgor_st_plugin_row_meta', 10, 2 );
