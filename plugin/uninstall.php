<?php
/**
 * Uninstall: removes every piece of data created by the plugin.
 *
 * Only executed when the plugin is deleted from the admin screen. Block content
 * lives in the posts themselves and is deliberately left untouched.
 *
 * @package Scrollstage
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/*
 * The plugin does not create any options, transients or database tables yet.
 * As soon as it does, delete every key that starts with jgor_st_ here, using
 * delete_option() and delete_site_option() for each of them.
 */
