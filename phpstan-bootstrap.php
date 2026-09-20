<?php
/**
 * PHPStan bootstrap: constants the plugin defines at runtime.
 *
 * This file is only loaded by PHPStan, never by WordPress. The values are
 * placeholders; all that matters is that the constants count as defined
 * during analysis.
 *
 * @package Jgor_Scrollytelling
 */

// Plugin jgor-scrollytelling (defined in plugin/jgor-scrollytelling.php).
if ( ! defined( 'JGOR_ST_VERSION' ) ) {
	define( 'JGOR_ST_VERSION', '1.8.0' );
}
if ( ! defined( 'JGOR_ST_MIN_PHP' ) ) {
	define( 'JGOR_ST_MIN_PHP', '8.1' );
}
if ( ! defined( 'JGOR_ST_FILE' ) ) {
	define( 'JGOR_ST_FILE', __DIR__ . '/plugin/jgor-scrollytelling.php' );
}
if ( ! defined( 'JGOR_ST_PATH' ) ) {
	define( 'JGOR_ST_PATH', __DIR__ . '/plugin/' );
}
if ( ! defined( 'JGOR_ST_URL' ) ) {
	define( 'JGOR_ST_URL', 'https://example.local/wp-content/plugins/jgor-scrollytelling/' );
}
