<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization;

use WP_Rocket_WP_Background_Process;

/**
 * Background process class for scanner the image optimization queue.
 *
 * @since 3.12.6.1_1.1-1
 *
 * @see WP_Background_Process
 */
class FileScannerProcess extends WP_Rocket_WP_Background_Process {

	/**
	 * The maximum number of images to write to the database in one cycle.
	 */
	private const MAX_IMAGES_PER_LOOP = 100;

	/**
	 * Process prefix.
	 *
	 * @var string
	 */
	protected $prefix = 'rocket';

	/**
	 * Specific action identifier for image minification.
	 *
	 * @var string Action identifier
	 */
	protected $action = 'image_optimization_file_scanner';

	/**
	 * File scanner.
	 *
	 * @var FileScanner file scanner.
	 */
	private $file_scanner;

	/**
	 * Backup folder.
	 *
	 * @var string path.
	 */
	private $backup_path;

	/**
	 * Source folder.
	 *
	 * @var string path.
	 */
	private $source_path;

	/**
	 * Folder to scan.
	 *
	 * @var string folder.
	 */
	private $source_folder;

	/**
	 * Source url to files.
	 *
	 * @var string url.
	 */
	private $source_url;

	/**
	 * The number of files found by the scanner.
	 *
	 * @var int
	 */
	private $total_files = 0;

	/**
	 * Instantiate the class
	 *
	 * @param FileScanner $file_scanner Instance of file scanner.
	 * @param string      $backup_path path to folder.
	 * @param string      $source_path path to folder.
	 * @param string      $source_folder folder to scan.
	 * @param string      $source_url url.
	 */
	public function __construct( FileScanner $file_scanner, $backup_path, $source_path, $source_folder, $source_url ) {
		parent::__construct();

		$this->file_scanner  = $file_scanner;
		$this->backup_path   = $backup_path;
		$this->source_path   = $source_path;
		$this->source_folder = $source_folder;
		$this->source_url    = $source_url;
	}

	/**
	 * Scan files according to metadata stored in $item.
	 *
	 * @param mixed $item Payload array.
	 *
	 * @return bool False if task was performed successfully, true otherwise to re-queue the item.
	 * @since 3.12.6.1_1.1-1
	 */
	protected function task( $item ) {
		$last_mtime_file    = $item['last_mtime_file'];
		$last_relative_path = $item['last_relative_path'];

		$files = $this->file_scanner->diff( $this->backup_path, $this->source_path, $this->source_folder, $last_mtime_file, true );

		$this->total_files += count( $files );

		// There may be more than MAX_IMAGES_PER_LOOP files with the same mtime.
		// Group by mtime and cut by the last file.
		$groups = [];
		foreach ( $files as $relative_path => $timestamp ) {
			$groups[ $timestamp ][] = $relative_path;
		}

		$i   = 1;
		$max = self::MAX_IMAGES_PER_LOOP;
		foreach ( $groups as $timestamp => $files ) {
			sort( $files );

			if ( ! empty( $last_relative_path ) ) {
				if ( in_array( $last_relative_path, $files, true ) ) {
					$files = array_slice( $files, array_search( $last_relative_path, $files, true ) );
				}
			}

			foreach ( $files as $relative_path ) {
				if ( $i > $max ) {
					$item['last_mtime_file']    = $timestamp - 1;
					$item['last_relative_path'] = $relative_path;

					return $item;
				}

				$url = $this->source_url . $relative_path;

				do_action( 'rocket_image_optimization_add_file_to_queue_table', $url, 0 );

				++$i;

				$last_mtime_file    = $timestamp;
				$last_relative_path = $relative_path;
			}
		}

		set_transient( 'rocket_image_optimization_scanner_mtime_file', $last_mtime_file );
		set_transient( 'rocket_image_optimization_scanner_last_relative_path', $last_relative_path );

		return false;
	}

	/**
	 * {$inheritDoc}
	 */
	protected function complete() {
		parent::complete();
		if ( $this->total_files > 0 ) {
			do_action( 'rocket_image_optimization_run_queue_worker_process' );
		}
	}

	/**
	 * Is process running
	 *
	 * Check whether the current process is already running
	 * in a background process.
	 */
	public function is_running() {
		return parent::is_process_running();
	}
}
