<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization;

use DirectoryIterator;
use RuntimeException;
use Throwable;
use UnexpectedValueException;

/**
 * Class FileScanner
 *
 * @package WP_Rocket\Addon\ImageOptimization
 */
class FileScanner {
	/**
	 * Find dirs.
	 *
	 * @param string $path folder.
	 *
	 * @return array|false
	 */
	protected function find_dir( $path ) {
		return @glob( $path . '*', GLOB_ONLYDIR ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	/**
	 * File mtime.
	 *
	 * @param string $path folder.
	 *
	 * @return int
	 */
	protected function filemtime( $path ) {
		$mtime = @filemtime( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( false === $mtime ) {
			$mtime = 0;
		}

		return $mtime;
	}

	/**
	 * Is dir.
	 *
	 * @param string $path folder.
	 *
	 * @return bool
	 */
	protected function is_dir( $path ) {
		return @is_dir( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	/**
	 * Make dir.
	 *
	 * @param string $path folder.
	 *
	 * @return bool
	 */
	protected function mkdir_p( $path ) {
		return rocket_mkdir_p( $path );
	}

	/**
	 * File mtime.
	 *
	 * @param string $path folder.
	 *
	 * @return DirectoryIterator
	 * @throws RuntimeException If the path is an empty string.
	 *
	 * @throws UnexpectedValueException If the path cannot be opened.
	 */
	protected function directory_iterator( $path ) {
		return new DirectoryIterator( $path );
	}

	/**
	 * Gets details for directories in a directory.
	 *
	 * @param string $path Path to directory.
	 * @param string $folder Folder to scan.
	 *
	 * @return array
	 */
	protected function dirs_list( $path, $folder ) {
		$full_path = trailingslashit( $path . $folder );

		$folders = [
			$folder,
		];

		$stack = [ $full_path ];
		while ( ! empty( $stack ) ) {
			$current = array_pop( $stack );
			$dirs    = $this->find_dir( $current );

			if ( false === $dirs ) {
				continue;
			}

			$dirs = array_diff( $dirs, [ '..', '.' ] );

			foreach ( $dirs as $dir ) {
				$relative_path = str_replace( $path, '', $dir );

				$folders[] = $relative_path;

				$stack[] = trailingslashit( $dir );
			}
		}

		return $folders;
	}

	/**
	 * Gets details for files in a directory.
	 *
	 * @param string  $path Path to directory.
	 * @param string  $folder Path to folder.
	 * @param integer $mfile_from Timestamp.
	 *
	 * @return array
	 */
	protected function files_list( $path, $folder, $mfile_from ) {
		$full_path = $path . $folder;
		$files     = [];

		try {
			$scan = $this->directory_iterator( $full_path );
		} catch ( Throwable $th ) {
			return $files;
		}

		$pattern_ext = '/\.(jpg|jpeg|jpe|gif|png)$/i';
		foreach ( $scan as $item ) {
			if ( $item->isDot() || $item->isDir() ) {
				continue;
			}

			if ( ! preg_match( $pattern_ext, strtolower( $item->getFilename() ) ) ) {
				continue;
			}

			$mtime = $item->getMTime();
			if ( $mtime <= $mfile_from ) {
				continue;
			}

			$relative_path = str_replace( $path, '', $item->getPathname() );

			$files[ $relative_path ] = $mtime;
		}

		unset( $scan );

		return $files;
	}

	/**
	 * Diff files.
	 *
	 * @param string  $backup_path Backup folder path.
	 * @param string  $source_path Source folder path.
	 * @param string  $source_folder Source folder to scan.
	 * @param integer $mfile_from Timestamp.
	 * @param bool    $create_backup_folders Create backup folders if they don't exist.
	 *
	 * @return array
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	public function diff( $backup_path, $source_path, $source_folder, $mfile_from = 0, $create_backup_folders = false ) {
		$backup_path    = trailingslashit( $backup_path );
		$source_path    = trailingslashit( $source_path );
		$backup_folders = $this->dirs_list( $backup_path, $source_folder );
		$source_folders = $this->dirs_list( $source_path, $source_folder );

		$files = [];
		foreach ( $source_folders as $folder_relative_path ) {
			$source_files = $this->files_list( $source_path, $folder_relative_path, $mfile_from );
			if ( ! in_array( $folder_relative_path, $backup_folders, true ) ) {
				if ( true === $create_backup_folders && false === $this->is_dir( $backup_path . $folder_relative_path ) ) {
					$this->mkdir_p( $backup_path . $folder_relative_path );
				}
				$files = array_replace( $files, $source_files );
			} else {
				$backup_files = $this->files_list( $backup_path, $folder_relative_path, $mfile_from );
				foreach ( $source_files as $file_relative_path => $source_file_mtime ) {
					if (
						! array_key_exists( $file_relative_path, $backup_files ) ||
						$source_file_mtime > $backup_files[ $file_relative_path ]
					) {
						$files[ $file_relative_path ] = $source_file_mtime;
					}
				}
			}
		}

		uasort(
			$files,
			function ( $a, $b ) {
				return $a - $b;
			}
		);

		return $files;
	}
}
