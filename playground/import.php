<?php
/**
 * Builds the online help site inside the WordPress Playground.
 *
 * Reads content.json written by export.php and recreates the Polylang
 * languages, the pages with their translations, the navigation, customized
 * templates and template parts, the media entries and a few options. All posts keep their IDs, so references in
 * block attributes stay valid.
 *
 * The script removes every existing page, post, navigation, synced pattern,
 * customized template, template part and attachment first. It is meant for a fresh Playground instance only and
 * therefore refuses to run without the literal argument "confirm-wipe".
 *
 * Usage: wp eval-file import.php <path to content.json> confirm-wipe
 *
 * @package Scrollstage
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

/**
 * Placeholder that stands for the site URL in exported content.
 */
const JGOR_ST_HELP_IMPORT_URL_TOKEN = '{{JGOR_ST_HELP_SITE_URL}}';

/**
 * Post types the import owns and replaces.
 *
 * @return array<int, string> Post type names.
 */
function jgor_st_help_import_post_types(): array {
	return array( 'page', 'post', 'wp_navigation', 'wp_block', 'wp_template', 'wp_template_part', 'attachment' );
}

/**
 * Reads and validates content.json.
 *
 * @param string $file Absolute path to content.json.
 * @return array<string, mixed> Decoded export data.
 */
function jgor_st_help_import_read( string $file ): array {
	if ( ! is_file( $file ) || ! is_readable( $file ) ) {
		WP_CLI::error( 'Cannot read ' . $file );
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file, CLI tool.
	$data = json_decode( (string) file_get_contents( $file ), true );

	if ( ! is_array( $data ) || 1 !== ( $data['format'] ?? 0 ) ) {
		WP_CLI::error( 'content.json is invalid or has an unknown format.' );
	}

	foreach ( array( 'languages', 'polylang', 'options', 'posts' ) as $key ) {
		if ( ! isset( $data[ $key ] ) || ! is_array( $data[ $key ] ) ) {
			WP_CLI::error( 'content.json lacks the section ' . $key . '.' );
		}
	}

	return $data;
}

/**
 * Deletes all existing content of the owned post types.
 *
 * @return int Number of deleted posts.
 */
function jgor_st_help_import_wipe(): int {
	$ids = get_posts(
		array(
			'post_type'      => jgor_st_help_import_post_types(),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'lang'           => '',
		)
	);

	// Auto-drafts and trashed posts are not part of "any".
	$ids = array_merge(
		$ids,
		get_posts(
			array(
				'post_type'      => jgor_st_help_import_post_types(),
				'post_status'    => array( 'auto-draft', 'trash' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'lang'           => '',
			)
		)
	);

	foreach ( array_unique( array_map( 'intval', $ids ) ) as $id ) {
		wp_delete_post( $id, true );
	}

	return count( $ids );
}

/**
 * Installs the WordPress language pack of a locale, if it is missing.
 *
 * A failure is not fatal: the help pages still work, only core strings of
 * that language stay English.
 *
 * @param string $locale Locale, e.g. de_DE.
 * @return void
 */
function jgor_st_help_import_language_pack( string $locale ): void {
	if ( 'en_US' === $locale || in_array( $locale, get_available_languages(), true ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/translation-install.php';

	if ( ! wp_can_install_language_pack() || false === wp_download_language_pack( $locale ) ) {
		WP_CLI::warning( 'Language pack ' . $locale . ' could not be installed.' );
	}
}

/**
 * Creates the Polylang languages and applies the Polylang settings.
 *
 * @param array<int, array<string, mixed>> $languages Language definitions, default language first.
 * @param array<string, mixed>             $settings  Polylang option values by key.
 * @return void
 */
function jgor_st_help_import_languages( array $languages, array $settings ): void {
	$default = '';

	foreach ( $languages as $language ) {
		$slug   = sanitize_key( (string) ( $language['slug'] ?? '' ) );
		$locale = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) ( $language['locale'] ?? '' ) );

		if ( '' === $slug || '' === $locale ) {
			WP_CLI::error( 'Language without slug or locale.' );
		}

		// The first language is the fallback, an explicit flag wins.
		if ( '' === $default || ! empty( $language['default'] ) ) {
			$default = $slug;
		}

		jgor_st_help_import_language_pack( $locale );

		if ( PLL()->model->get_language( $slug ) ) {
			continue;
		}

		$result = PLL()->model->languages->add(
			array(
				'name'       => sanitize_text_field( (string) ( $language['name'] ?? $slug ) ),
				'slug'       => $slug,
				'locale'     => $locale,
				'rtl'        => ! empty( $language['rtl'] ),
				'term_group' => (int) ( $language['term_group'] ?? 0 ),
				'flag'       => sanitize_key( (string) ( $language['flag'] ?? '' ) ),
			)
		);

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( 'Language ' . $slug . ': ' . $result->get_error_message() );
		}
	}

	PLL()->model->clean_languages_cache();

	foreach ( array( 'force_lang', 'hide_default', 'rewrite', 'redirect_lang', 'browser', 'media_support' ) as $key ) {
		if ( array_key_exists( $key, $settings ) ) {
			PLL()->options[ $key ] = $settings[ $key ];
		}
	}

	PLL()->options['default_lang'] = $default;
	PLL()->options->save();
	PLL()->model->clean_languages_cache();
}

/**
 * Inserts one exported post with its original ID.
 *
 * @param array<string, mixed> $post   Exported post.
 * @param int                  $author User ID of the post author.
 * @return int Post ID.
 */
function jgor_st_help_import_post( array $post, int $author ): int {
	$id   = (int) ( $post['id'] ?? 0 );
	$type = (string) ( $post['type'] ?? '' );
	$site = untrailingslashit( home_url() );

	if ( $id < 1 || ! in_array( $type, jgor_st_help_import_post_types(), true ) ) {
		WP_CLI::error( 'Post with invalid ID or type in content.json.' );
	}

	$postarr = array(
		'import_id'      => $id,
		'post_type'      => $type,
		'post_status'    => (string) ( $post['status'] ?? 'draft' ),
		'post_title'     => (string) ( $post['title'] ?? '' ),
		'post_name'      => (string) ( $post['slug'] ?? '' ),
		'post_parent'    => (int) ( $post['parent'] ?? 0 ),
		'menu_order'     => (int) ( $post['menu_order'] ?? 0 ),
		'post_mime_type' => (string) ( $post['mime_type'] ?? '' ),
		'post_content'   => str_replace( JGOR_ST_HELP_IMPORT_URL_TOKEN, $site, (string) ( $post['content'] ?? '' ) ),
		'post_excerpt'   => str_replace( JGOR_ST_HELP_IMPORT_URL_TOKEN, $site, (string) ( $post['excerpt'] ?? '' ) ),
		'post_author'    => $author,
	);

	$meta = isset( $post['meta'] ) && is_array( $post['meta'] ) ? $post['meta'] : array();

	if ( 'attachment' === $type && isset( $meta['_wp_attached_file'][0] ) ) {
		$uploads         = wp_upload_dir();
		$postarr['guid'] = trailingslashit( $uploads['baseurl'] ) . ltrim( (string) $meta['_wp_attached_file'][0], '/' );
	}

	// wp_insert_post() expects slashed data.
	$new_id = wp_insert_post( wp_slash( $postarr ), true );

	if ( is_wp_error( $new_id ) ) {
		WP_CLI::error( 'Post ' . $id . ': ' . $new_id->get_error_message() );
	}

	if ( $new_id !== $id ) {
		WP_CLI::error( 'Post ' . $id . ' was created with ID ' . $new_id . '.' );
	}

	foreach ( $meta as $key => $values ) {
		delete_post_meta( $id, (string) $key );
		foreach ( (array) $values as $value ) {
			add_post_meta( $id, (string) $key, wp_slash( $value ) );
		}
	}

	// Templates and template parts are bound to theme and area by terms.
	$terms = isset( $post['terms'] ) && is_array( $post['terms'] ) ? $post['terms'] : array();

	foreach ( array( 'wp_theme', 'wp_template_part_area' ) as $taxonomy ) {
		if ( ! empty( $terms[ $taxonomy ] ) && is_array( $terms[ $taxonomy ] ) ) {
			wp_set_object_terms( $id, array_map( 'sanitize_title', $terms[ $taxonomy ] ), $taxonomy );
		}
	}

	return $id;
}

/**
 * Assigns languages and links the translations.
 *
 * @param array<int, array<string, mixed>> $posts Exported posts.
 * @return void
 */
function jgor_st_help_import_translations( array $posts ): void {
	$done = array();

	foreach ( $posts as $post ) {
		$language = (string) ( $post['language'] ?? '' );

		if ( '' !== $language ) {
			pll_set_post_language( (int) $post['id'], $language );
		}
	}

	foreach ( $posts as $post ) {
		$translations = isset( $post['translations'] ) && is_array( $post['translations'] ) ? array_map( 'intval', $post['translations'] ) : array();
		$group        = implode( ',', $translations );

		if ( count( $translations ) < 2 || isset( $done[ $group ] ) ) {
			continue;
		}

		pll_save_post_translations( $translations );
		$done[ $group ] = true;
	}
}

/**
 * Applies the exported options and the permalink structure.
 *
 * @param array<string, mixed> $options Option values by name.
 * @return void
 */
function jgor_st_help_import_options( array $options ): void {
	global $wp_rewrite;

	$text    = array( 'blogname', 'blogdescription' );
	$numbers = array( 'page_on_front', 'page_for_posts', 'wp_page_for_privacy_policy', 'site_logo' );

	foreach ( $text as $name ) {
		if ( isset( $options[ $name ] ) ) {
			update_option( $name, sanitize_text_field( (string) $options[ $name ] ) );
		}
	}

	foreach ( $numbers as $name ) {
		if ( isset( $options[ $name ] ) ) {
			update_option( $name, absint( $options[ $name ] ) );
		}
	}

	if ( isset( $options['show_on_front'] ) && in_array( $options['show_on_front'], array( 'page', 'posts' ), true ) ) {
		update_option( 'show_on_front', $options['show_on_front'] );
	}

	if ( isset( $options['permalink_structure'] ) ) {
		$wp_rewrite->set_permalink_structure( sanitize_option( 'permalink_structure', (string) $options['permalink_structure'] ) );
	}

	/*
	 * Polylang hooks its language prefixes into the rewrite rules while it
	 * boots. In this request it booted without languages, so rules built here
	 * would lack the prefixes. Drop them; the next request rebuilds them.
	 */
	delete_option( 'rewrite_rules' );
}

/**
 * Runs the import.
 *
 * @param array<int, string> $arguments Positional arguments of wp eval-file.
 * @return void
 */
function jgor_st_help_import_run( array $arguments ): void {
	if ( ! isset( $arguments[0], $arguments[1] ) || 'confirm-wipe' !== $arguments[1] ) {
		WP_CLI::error( 'Usage: wp eval-file import.php <path to content.json> confirm-wipe (deletes all existing pages, posts and media).' );
	}

	if ( ! function_exists( 'PLL' ) || ! function_exists( 'pll_set_post_language' ) ) {
		WP_CLI::error( 'Polylang is not active.' );
	}

	$data   = jgor_st_help_import_read( $arguments[0] );
	$admins = get_users(
		array(
			'role'    => 'administrator',
			'number'  => 1,
			'orderby' => 'ID',
			'fields'  => 'ID',
		)
	);

	if ( empty( $admins ) ) {
		WP_CLI::error( 'No administrator found.' );
	}

	$author = (int) $admins[0];
	wp_set_current_user( $author );

	// The content comes from the own repository: keep block markup untouched.
	kses_remove_filters();

	$wiped = jgor_st_help_import_wipe();

	jgor_st_help_import_languages( $data['languages'], $data['polylang'] );

	foreach ( $data['posts'] as $post ) {
		jgor_st_help_import_post( (array) $post, $author );
	}

	jgor_st_help_import_translations( $data['posts'] );
	jgor_st_help_import_options( $data['options'] );

	// No redirect to the Polylang setup wizard on the first admin visit.
	delete_transient( 'pll_activation_redirect' );

	WP_CLI::success(
		sprintf(
			'%d old posts removed, %d languages and %d posts imported.',
			$wiped,
			count( $data['languages'] ),
			count( $data['posts'] )
		)
	);
}

jgor_st_help_import_run( isset( $args ) && is_array( $args ) ? array_values( $args ) : array() );
