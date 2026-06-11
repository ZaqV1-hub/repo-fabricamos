<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization;

use WP_Error;
use WP_Filesystem_Direct;
use WP_Rocket\Engine\AccelerateWp\Sentry;

/**
 * Class FileManager
 *
 * @package WP_Rocket\Addon\ImageOptimization
 */
class FileManager {

	/**
	 * Base path for storing minified images before replacing the originals.
	 *
	 * @var string
	 */
	private $download_path;

	/**
	 * Base path for backing up the original images.
	 *
	 * @var string
	 */
	private $backup_path;

	/**
	 * Instance of the filesystem handler.
	 *
	 * @var WP_Filesystem_Direct
	 */
	private $filesystem;

	/**
	 * FileManager constructor, adjust the paths for current blog.
	 *
	 * @param string               $download_path Path for storing minified images before replacing the originals.
	 * @param string               $backup_path Path for backing up the original images.
	 * @param WP_Filesystem_Direct $filesystem Instance of the filesystem handler.
	 */
	public function __construct( $download_path, $backup_path, $filesystem ) {
		$this->download_path = $download_path;
		$this->backup_path   = $backup_path;
		$this->filesystem    = $filesystem;
	}

	/**
	 * Download path.
	 *
	 * @return string
	 */
	public function download_path() {
		return $this->download_path;
	}

	/**
	 * Backup path.
	 *
	 * @return string
	 */
	public function backup_path() {
		return $this->backup_path;
	}

	/**
	 * Create dirs.
	 *
	 * @return void
	 */
	public function create_dirs() {
		// Check if the backup and the download folders exist and create them.
		foreach ( [ $this->backup_path, $this->download_path ] as $path ) {
			if ( ! $this->filesystem->is_dir( $path ) ) {
				rocket_mkdir_p( $path );
			}
		}
	}

	/**
	 * Backup dir available.
	 *
	 * @return bool
	 */
	public function backup_dir_available() {
		return $this->filesystem->is_writable( $this->backup_path );
	}

	/**
	 * Download dir available.
	 *
	 * @return bool
	 */
	public function download_dir_available() {
		return $this->filesystem->is_writable( $this->download_path );
	}

	/**
	 * Dirs available.
	 *
	 * @return bool
	 */
	public function dirs_available() {
		return (
			$this->backup_dir_available() &&
			$this->download_dir_available()
		);
	}

	/**
	 * Save minified image and move original image to a backup folder.
	 *
	 * @param string   $url URL for item to be used in error messages.
	 * @param string   $download_url Download URL of the minified image.
	 * @param string   $format Image format. Default: "original".
	 * @param callable $get_file_contents download file.
	 *
	 * @return bool|WP_Error
	 * @since 3.12.6.1_1.1-1
	 */
	public function save_image( $url, $download_url, $format, $get_file_contents ) {

		// Extract the relative path from $url.
		$relative_url = $this->extract_relative_url( $url );

		$download_result = $this->download_image_to_temporary_folder( $url, $download_url, $relative_url, $format, $get_file_contents );
		if ( is_wp_error( $download_result ) ) {
			return $download_result;
		}

		$original_file_mtime = 0;
		$original_file_path  = $this->get_original_file_path( $relative_url );
		if ( 'original' !== $format ) {
			$original_file_path = $this->maybe_append_format_extension( $original_file_path, $format );
		}

		if ( $this->filesystem->exists( $original_file_path ) ) {
			$original_file_mtime = @filemtime( $original_file_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$minified_file_path  = $this->download_path . $relative_url;

			if ( 'original' !== $format ) {
				$minified_file_path = $this->maybe_append_format_extension( $minified_file_path, $format );
			}

			// Check if the original image is not smaller than the minified one.
			$minified_file_size = (int) filesize( $minified_file_path );
			$original_file_size = (int) filesize( $original_file_path );
			if ( $minified_file_size > $original_file_size ) {
				Manager::debug(
					'Original image is smaller than minified one. Skipping.',
					[
						'original_image' => $original_file_path,
						'original_size'  => $original_file_size,
						'minified_image' => $minified_file_path,
						'minified_size'  => $minified_file_size,
					]
				);

				// Delete the downloaded image.
				$this->filesystem->delete( $minified_file_path, false, 'f' );

				return true;
			}

			$backup_result = $this->move_original_image_to_backup_folder( $url, $relative_url, $format, $original_file_mtime );
			if ( is_wp_error( $backup_result ) ) {
				return $backup_result;
			}
		}

		return $this->replace_original_image( $url, $relative_url, $format, $original_file_mtime );
	}

	/**
	 * Downloads an image from given URL to a temporary folder.
	 *
	 * It takes care of creating the folder if needed.
	 *
	 * @param string   $url The URL of the original image.
	 * @param string   $download_url The URL where the minified image should be downloaded from.
	 * @param string   $relative_url URL of the image relative to the uploads folder.
	 * @param string   $format Image format. "original" or "webp".
	 * @param callable $get_file_contents download file.
	 *
	 * @return true|\WP_Error True if the image was successfully downloaded and saved. WP_Error object in case something failed.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	private function download_image_to_temporary_folder( $url, $download_url, $relative_url, $format, $get_file_contents ) {
		$file_path_directory = dirname( $this->download_path . $relative_url );

		if ( ! $this->filesystem->is_dir( $file_path_directory ) ) {
			if ( ! rocket_mkdir_p( $file_path_directory ) ) {
				return Sentry::enrich_wp_error(
					new WP_Error(
						'image_minification_temp_folder_creation_failure',
						esc_html__( 'The destination folder could not be created.', 'rocket' ),
						[
							'dir' => $file_path_directory,
						]
					)
				);
			}
		}

		$image_data = $get_file_contents( $download_url );
		if ( is_wp_error( $image_data ) ) {
			return $image_data;
		}

		if ( 'original' !== $format ) {
			$relative_url = $this->maybe_append_format_extension( $relative_url, $format );
		}

		$image_saved = file_put_contents( $this->download_path . $relative_url, $image_data ); // @codingStandardsIgnoreLine WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		if ( false === $image_saved ) {
			// Unable to save image in a temporary folder.
			return Sentry::enrich_wp_error(
				new WP_Error(
					'image_minification_file_save_failure',
					esc_html__( 'Unable to save the minified image in temporary folder.', 'rocket' ),
					[
						'path' => $this->download_path . $relative_url,
					]
				)
			);
		}

		return true;
	}

	/**
	 * Moves the original image from the uploads folder to a backup folder.
	 *
	 * It takes care of creating the backup folder if needed.
	 *
	 * @param string  $url The URL of the original image.
	 * @param string  $relative_url URL of the image relative to the uploads folder.
	 * @param string  $format Image format. "original" or "webp".
	 * @param integer $original_file_mtime Original file mtime.
	 *
	 * @return true|\WP_Error True if the image was successfully backed up. WP_Error object in case something failed.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	private function move_original_image_to_backup_folder( $url, $relative_url, $format, $original_file_mtime ) {
		$backup_file_path    = $this->backup_path . $relative_url;
		$file_path_directory = dirname( $backup_file_path );

		if ( ! $this->filesystem->is_dir( $file_path_directory ) ) {
			if ( ! rocket_mkdir_p( $file_path_directory ) ) {
				return Sentry::enrich_wp_error(
					new WP_Error(
						'image_minification_backup_folder_creation_failure',
						esc_html__( 'The backup folder could not be created.', 'rocket' ),
						[
							'dir' => $file_path_directory,
						]
					)
				);
			}
		}

		$original_file_path = $this->get_original_file_path( $relative_url );
		if ( 'original' !== $format ) {
			$original_file_path = $this->maybe_append_format_extension( $original_file_path, $format );
			$backup_file_path   = $this->maybe_append_format_extension( $backup_file_path, $format );
		}

		if ( $this->filesystem->exists( $original_file_path ) ) {
			$result = $this->filesystem->move( $original_file_path, $backup_file_path, true );

			if ( true === $result ) {
				// We must leave mtime unchanged for the scanner to correctly search for new files.
				$this->filesystem->touch( $backup_file_path, $original_file_mtime );
			}

			return $result; // @phpstan-ignore-line
		}

		return true;
	}

	/**
	 * Replaces original image in the uploads folder using the minified image stored in a temporary folder.
	 *
	 * It takes care of creating the target folder in uploads folder if needed.
	 *
	 * @param string  $url The URL of the original image.
	 * @param string  $relative_url URL of the image relative to the uploads folder.
	 * @param string  $format Image format. "original" or "webp".
	 * @param integer $original_file_mtime Original file mtime.
	 *
	 * @return true|\WP_Error True if the image was successfully downloaded and saved. WP_Error object in case something failed.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	private function replace_original_image( $url, $relative_url, $format, $original_file_mtime ) {

		$original_file_path  = $this->get_original_file_path( $relative_url );
		$file_path_directory = dirname( $original_file_path );

		if ( ! $this->filesystem->is_dir( $file_path_directory ) ) {
			if ( ! rocket_mkdir_p( $file_path_directory ) ) {
				return Sentry::enrich_wp_error(
					new WP_Error(
						'image_minification_uploads_folder_creation_failure',
						esc_html__( 'The uploads folder could not be created.', 'rocket' ),
						[
							'dir' => $file_path_directory,
						]
					)
				);
			}
		}

		$downloaded_file_path = $this->download_path . $relative_url;
		if ( 'original' !== $format ) {
			$original_file_path   = $this->maybe_append_format_extension( $original_file_path, $format );
			$downloaded_file_path = $this->maybe_append_format_extension( $downloaded_file_path, $format );
		}

		if ( $this->filesystem->exists( $downloaded_file_path ) ) {
			$result = $this->filesystem->move( $downloaded_file_path, $original_file_path, true );

			if ( true === $result ) {
				// We must leave mtime unchanged for the scanner to correctly search for new files.
				$this->filesystem->touch( $original_file_path, $original_file_mtime );
			}

			return $result; // @phpstan-ignore-line
		}

		return true;
	}

	/**
	 * Extracts the path relative to the wp-content folder from $url.
	 *
	 * @param string $url Full image URL.
	 *
	 * @return string Path relative to the wp-content folder.
	 */
	private function extract_relative_url( string $url ) {

		$relative_url = wp_parse_url( $url, PHP_URL_PATH );
		$content_url  = wp_parse_url( WP_CONTENT_URL, PHP_URL_PATH ); // @phpstan-ignore-line

		if ( 0 === strpos( $relative_url, $content_url ) ) {
			$relative_url = str_replace( $content_url, '', $relative_url );
			$relative_url = ltrim( $relative_url, '/' );
		}

		return $relative_url;
	}

	/**
	 * Builds the path to the original image.
	 *
	 * @param string $relative_url Image UP relative to the wp-content folder.
	 *
	 * @return string Absolute path to the original image.
	 */
	private function get_original_file_path( string $relative_url ) {
		return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $relative_url;
	}

	/**
	 * Appends the image format extension to the path if needed.
	 *
	 * @param string $path Path to the image.
	 * @param string $format Image format. "original" or "webp".
	 *
	 * @return string Path to the image with the format extension appended.
	 */
	private function maybe_append_format_extension( string $path, string $format = 'original' ) {
		if ( 'original' !== $format ) {
			$path .= '.' . strtolower( $format );
		}

		return $path;
	}

	/**
	 * Checks if the original file still exists.
	 *
	 * @param string $url Full image URL.
	 *
	 * @return bool True if the original file exists. False otherwise.
	 */
	public function original_file_exists( $url ) {
		$relative_url = $this->extract_relative_url( $url );
		$file_path    = $this->get_original_file_path( $relative_url );

		return $this->filesystem->exists( $file_path );
	}
}
