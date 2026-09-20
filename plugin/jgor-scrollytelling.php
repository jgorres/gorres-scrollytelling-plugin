<?php
/**
 * Plugin Name:       ScrollyTelling
 * Plugin URI:        https://joern.gorres.com/jgor-scrollytelling
 * Description:       Scroll-Storytelling mit sticky Medienspalte und schrittweisen Texten.
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      8.1
 * Tested up to:      6.8
 * Author:            Jörn Gorres
 * Author URI:        https://joern.gorres.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       jgor-scrollytelling
 * Domain Path:       /languages
 *
 * @package Jgor_Scrollytelling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin constants.
 *
 * JGOR_ST_VERSION is also used as the asset version for cache busting and must
 * be kept in sync with the "Version" header above on every release.
 */
define( 'JGOR_ST_VERSION', '1.0.0' );
define( 'JGOR_ST_MIN_PHP', '8.1' );
define( 'JGOR_ST_FILE', __FILE__ );
define( 'JGOR_ST_PATH', plugin_dir_path( __FILE__ ) );
define( 'JGOR_ST_URL', plugin_dir_url( __FILE__ ) );

/**
 * Loads the plugin translations.
 *
 * The plugin is not hosted on wordpress.org, so the language files shipped in
 * /languages have to be registered explicitly. Runs on "init" because loading
 * a text domain earlier triggers the _load_textdomain_just_in_time notice.
 *
 * @return void
 */
function jgor_st_load_textdomain() {
	load_plugin_textdomain(
		'jgor-scrollytelling',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
add_action( 'init', 'jgor_st_load_textdomain' );

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
					/* translators: %s: benötigte PHP-Version */
					__( 'Dieses Plugin benötigt mindestens PHP %s.', 'jgor-scrollytelling' ),
					JGOR_ST_MIN_PHP
				)
			),
			esc_html__( 'Plugin-Aktivierung abgebrochen', 'jgor-scrollytelling' ),
			array( 'back_link' => true )
		);
	}
}
register_activation_hook( __FILE__, 'jgor_st_activate' );
