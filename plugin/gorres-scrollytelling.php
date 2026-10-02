<?php
/**
 * Plugin Name:       Gorres Scrollytelling
 * Description:       Full-screen media that stay in place while text boxes scroll across them.
 * Version:           2.12.0
 * Requires at least: 6.7
 * Requires PHP:      8.1
 * Author:            Jörn Gorres
 * Author URI:        https://joern.gorres.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gorres-scrollytelling
 * Domain Path:       /languages
 *
 * @package Gorres_Scrollytelling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin constants.
 *
 * JGOR_ST_VERSION must be kept in sync with the "Version" header above on
 * every release. The block assets take their cache-busting version from the
 * "version" field of each block.json, which has to be raised as well.
 */
define( 'JGOR_ST_VERSION', '2.12.0' );
define( 'JGOR_ST_MIN_PHP', '8.1' );
define( 'JGOR_ST_FILE', __FILE__ );
define( 'JGOR_ST_PATH', plugin_dir_path( __FILE__ ) );
define( 'JGOR_ST_URL', plugin_dir_url( __FILE__ ) );

/**
 * Includes the module files from includes/.
 *
 * Add new modules here; every file covers exactly one area of responsibility.
 * The file_exists() check keeps a partial deployment from fataling the site.
 *
 * @return void
 */
function jgor_st_includes() {
	$files = array(
		'blocks.php',
		'admin.php',
	);

	foreach ( $files as $file ) {
		$path = JGOR_ST_PATH . 'includes/' . $file;
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}
jgor_st_includes();

/**
 * Runs on plugin activation.
 *
 * WordPress already honours the "Requires PHP" header, the explicit check is a
 * second layer for installations that bypass the plugin screen (WP-CLI, code).
 *
 * @return void
 */
function jgor_st_activate() {
	if ( version_compare( PHP_VERSION, JGOR_ST_MIN_PHP, '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(
			esc_html(
				sprintf(
					/* translators: %s: required PHP version */
					__( 'This plugin requires PHP %s or newer.', 'gorres-scrollytelling' ),
					JGOR_ST_MIN_PHP
				)
			),
			esc_html__( 'Plugin activation stopped', 'gorres-scrollytelling' ),
			array( 'back_link' => true )
		);
	}
}
register_activation_hook( __FILE__, 'jgor_st_activate' );
