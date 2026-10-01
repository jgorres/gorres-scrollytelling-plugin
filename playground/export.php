<?php
/**
 * Exports the online help site for the WordPress Playground bundle.
 *
 * Reads languages, pages, navigation, media and a few options of the local
 * help site and writes them to content.json and uploads.zip. import.php
 * rebuilds the site from these two files inside the Playground.
 *
 * Usage: wp eval-file export.php <target directory>
 *
 * @package Scrollstage
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

/**
 * Placeholder that replaces the site URL in exported content.
 */
const JGOR_ST_HELP_EXPORT_URL_TOKEN = '{{JGOR_ST_HELP_SITE_URL}}';

/**
 * Replaces the site URL with the placeholder, plain and JSON-escaped.
 *
 * @param string $text Text that may contain the site URL.
 * @return string Text with the placeholder.
 */
function jgor_st_help_export_tokenize( string $text ): string {
	$url = untrailingslashit( home_url() );

	return str_replace(
		array( $url, str_replace( '/', '\/', $url ) ),
		JGOR_ST_HELP_EXPORT_URL_TOKEN,
		$text
	);
}

/**
 * Collects the Polylang languages.
 *
 * @return array<int, array<string, mixed>> Language definitions, default language first.
 */
function jgor_st_help_export_languages(): array {
	$languages = array();

	foreach ( PLL()->model->get_languages_list() as $language ) {
		$languages[] = array(
			'name'       => $language->name,
			'slug'       => $language->slug,
			'locale'     => $language->locale,
			'rtl'        => (bool) $language->is_rtl,
			'term_group' => (int) $language->term_group,
			'flag'       => $language->flag_code,
			'default'    => (bool) $language->is_default,
		);
	}

	usort(
		$languages,
		static function ( array $a, array $b ): int {
			return array( ! $a['default'], $a['term_group'], $a['slug'] ) <=> array( ! $b['default'], $b['term_group'], $b['slug'] );
		}
	);

	return $languages;
}

/**
 * Collects the post meta worth transferring.
 *
 * @param int $post_id Post ID.
 * @return array<string, array<int, mixed>> Meta values by key.
 */
function jgor_st_help_export_meta( int $post_id ): array {
	$skip = array( '_edit_lock', '_edit_last', '_wp_old_slug', '_wp_old_date', '_wp_trash_meta_status', '_wp_trash_meta_time', '_wp_desired_post_slug' );
	$meta = array();
	$raw  = get_post_meta( $post_id );

	if ( ! is_array( $raw ) ) {
		return $meta;
	}

	ksort( $raw );

	foreach ( $raw as $key => $values ) {
		if ( in_array( $key, $skip, true ) ) {
			continue;
		}
		$meta[ $key ] = array_map( 'maybe_unserialize', $values );
	}

	return $meta;
}

/**
 * Collects pages, posts, navigation, synced patterns and attachments.
 *
 * @return array<int, array<string, mixed>> Posts ordered by ID.
 */
function jgor_st_help_export_posts(): array {
	$posts = get_posts(
		array(
			'post_type'        => array( 'page', 'post', 'wp_navigation', 'wp_block', 'attachment' ),
			'post_status'      => array( 'publish', 'draft', 'private', 'inherit' ),
			'posts_per_page'   => -1,
			'orderby'          => 'ID',
			'order'            => 'ASC',
			'lang'             => '',
			'suppress_filters' => false,
		)
	);

	$export = array();

	foreach ( $posts as $post ) {
		$language     = pll_get_post_language( $post->ID );
		$translations = pll_get_post_translations( $post->ID );
		ksort( $translations );

		$export[] = array(
			'id'           => $post->ID,
			'type'         => $post->post_type,
			'status'       => $post->post_status,
			'title'        => $post->post_title,
			'slug'         => $post->post_name,
			'parent'       => $post->post_parent,
			'menu_order'   => $post->menu_order,
			'mime_type'    => $post->post_mime_type,
			'content'      => jgor_st_help_export_tokenize( $post->post_content ),
			'excerpt'      => jgor_st_help_export_tokenize( $post->post_excerpt ),
			'language'     => $language ? $language : '',
			'translations' => array_map( 'intval', $translations ),
			'meta'         => jgor_st_help_export_meta( $post->ID ),
		);
	}

	return $export;
}

/**
 * Collects the options the help site depends on.
 *
 * @return array<string, mixed> Option values by name.
 */
function jgor_st_help_export_options(): array {
	$options = array();

	foreach ( array( 'blogname', 'blogdescription', 'permalink_structure', 'show_on_front', 'page_on_front', 'page_for_posts', 'wp_page_for_privacy_policy' ) as $name ) {
		$options[ $name ] = get_option( $name );
	}

	return $options;
}

/**
 * Collects the Polylang settings that shape the URLs.
 *
 * @return array<string, mixed> Polylang option values by key.
 */
function jgor_st_help_export_polylang(): array {
	$stored   = (array) get_option( 'polylang', array() );
	$settings = array();

	foreach ( array( 'force_lang', 'hide_default', 'rewrite', 'redirect_lang', 'browser', 'media_support' ) as $key ) {
		if ( array_key_exists( $key, $stored ) ) {
			$settings[ $key ] = $stored[ $key ];
		}
	}

	return $settings;
}

/**
 * Packs the uploads directory into a ZIP archive.
 *
 * The archive always holds an index.php, so it is never empty.
 *
 * @param string $zip_file Absolute path of the archive to write.
 * @return int Number of upload files in the archive.
 */
function jgor_st_help_export_uploads( string $zip_file ): int {
	$uploads = wp_upload_dir();
	$basedir = trailingslashit( $uploads['basedir'] );
	$zip     = new ZipArchive();

	if ( true !== $zip->open( $zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
		WP_CLI::error( 'Cannot write ' . $zip_file );
	}

	$zip->addFromString( 'index.php', "<?php\n// Silence is golden.\n" );

	$files = array();

	if ( is_dir( $basedir ) ) {
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $basedir, FilesystemIterator::SKIP_DOTS )
		);

		foreach ( $iterator as $file ) {
			if ( $file->isFile() && 'index.php' !== $file->getFilename() ) {
				$files[] = $file->getPathname();
			}
		}
	}

	sort( $files );

	foreach ( $files as $file ) {
		$zip->addFile( $file, substr( $file, strlen( $basedir ) ) );
	}

	if ( ! $zip->close() ) {
		WP_CLI::error( 'Cannot close ' . $zip_file );
	}

	return count( $files );
}

/**
 * Runs the export.
 *
 * @param array<int, string> $arguments Positional arguments of wp eval-file.
 * @return void
 */
function jgor_st_help_export_run( array $arguments ): void {
	if ( ! function_exists( 'PLL' ) || ! function_exists( 'pll_get_post_language' ) ) {
		WP_CLI::error( 'Polylang is not active.' );
	}

	if ( ! class_exists( 'ZipArchive' ) ) {
		WP_CLI::error( 'PHP extension zip is missing.' );
	}

	$target = isset( $arguments[0] ) ? realpath( $arguments[0] ) : false;

	if ( false === $target || ! is_dir( $target ) || ! wp_is_writable( $target ) ) {
		WP_CLI::error( 'Usage: wp eval-file export.php <writable target directory>' );
	}

	$data = array(
		'format'    => 1,
		'languages' => jgor_st_help_export_languages(),
		'polylang'  => jgor_st_help_export_polylang(),
		'options'   => jgor_st_help_export_options(),
		'posts'     => jgor_st_help_export_posts(),
	);

	if ( empty( $data['languages'] ) ) {
		WP_CLI::error( 'No Polylang language found.' );
	}

	$json = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

	if ( false === $json ) {
		WP_CLI::error( 'JSON encoding failed: ' . json_last_error_msg() );
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- CLI tool writing into the repository.
	if ( false === file_put_contents( $target . '/content.json', $json . "\n" ) ) {
		WP_CLI::error( 'Cannot write content.json.' );
	}

	$files = jgor_st_help_export_uploads( $target . '/uploads.zip' );

	WP_CLI::success(
		sprintf(
			'%d languages, %d posts, %d upload files exported to %s',
			count( $data['languages'] ),
			count( $data['posts'] ),
			$files,
			$target
		)
	);
}

jgor_st_help_export_run( isset( $args ) && is_array( $args ) ? array_values( $args ) : array() );
