<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization\Database\Queries;

use WP_Rocket\Addon\ImageOptimization\Database\Row\ImageOptimization as Row;
use WP_Rocket\Addon\ImageOptimization\Database\Schemas\ImageOptimization as Schema;
use WP_Rocket\Dependencies\BerlinDB\Database\Query;

/**
 * ImageOptimization Query.
 */
class ImageOptimization extends Query {

	public const STATUS_NEW = 'new';

	public const STATUS_PENDING = 'pending';

	public const STATUS_TO_DOWNLOAD = 'to_download';

	public const STATUS_DOWNLOADING = 'downloading';

	public const STATUS_FAILED = 'failed';

	/**
	 * Name of the database table to query.
	 *
	 * @var   string
	 */
	protected $table_name = 'wpr_image_optimization';

	/**
	 * String used to alias the database table in MySQL statement.
	 *
	 * Keep this short, but descriptive. I.E. "tr" for term relationships.
	 *
	 * This is used to avoid collisions with JOINs.
	 *
	 * @var   string
	 */
	protected $table_alias = 'wpr_imgopt';

	/**
	 * Name of class used to setup the database schema.
	 *
	 * @var   string
	 */
	protected $table_schema = Schema::class;

	/** Item ******************************************************************/

	/**
	 * Name for a single item.
	 *
	 * Use underscores between words. I.E. "term_relationship"
	 *
	 * This is used to automatically generate action hooks.
	 *
	 * @var   string
	 */
	protected $item_name = 'awp_imopt';

	/**
	 * Plural version for a group of items.
	 *
	 * Use underscores between words. I.E. "term_relationships"
	 *
	 * This is used to automatically generate action hooks.
	 *
	 * @var   string
	 */
	protected $item_name_plural = 'awp_imopt';

	/**
	 * Name of class used to turn IDs into first-class objects.
	 *
	 * This is used when looping through return values to guarantee their shape.
	 *
	 * @var   mixed
	 */
	protected $item_shape = Row::class;

	/**
	 * Table status.
	 *
	 * @var boolean
	 */
	public static $table_exists = false;

	/**
	 * Get row with the same url and format.
	 *
	 * @param string $url Page url.
	 * @param string $format original|webp.
	 *
	 * @return ?Row
	 */
	public function get_row( string $url, string $format ) {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return null;
		}

		$items = $this->query(
			[
				'url'    => $url,
				'format' => $format,
			],
			false
		);

		return $this->extract_first_item( $items );
	}

	/**
	 * Get row count with the same url and format.
	 *
	 * @param string $url Page url.
	 * @param string $format original|webp.
	 *
	 * @return int
	 */
	public function get_count_by_url_and_format( string $url, string $format ): int {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return 0;
		}

		return (int) $this->query(
			[
				'count'  => true,
				'url'    => $url,
				'format' => $format,
			],
			false
		);
	}

	/**
	 * Find job.
	 *
	 * @param string|int $id ID.
	 *
	 * @return ?Row
	 */
	public function find( $id ) {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return null;
		}

		$items = $this->query(
			[
				'id' => $id,
			],
			false
		);

		return $this->extract_first_item( $items );
	}

	/**
	 * Find by job id.
	 *
	 * @param string|int $job_id Job_ID.
	 *
	 * @return ?Row
	 */
	public function find_by_job_id( $job_id ) {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return null;
		}

		$items = $this->query(
			[
				'job_id' => $job_id,
			],
			false
		);

		return $this->extract_first_item( $items );
	}

	/**
	 * Create new DB row for specific url.
	 *
	 * @param string $url      Image URL.
	 * @param string $format   Image format.
	 * @param int    $priority Job priority. Optional, defaults to 0.
	 *
	 * @return false|int
	 */
	public function create_new( string $url, string $format, int $priority = 0 ) {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return false;
		}

		$item = [
			'url'      => $url,
			'format'   => $format,
			'priority' => $priority,
			'secret'   => wp_generate_password( 16, false ),
		];

		return $this->add_item( $item );
	}

	/**
	 * Set pending data.
	 *
	 * @param int|string $id row.
	 * @param int|string $job_id API job_id.
	 * @param callable   $wait_time_callback Callback to get the wait time.
	 *
	 * @return bool
	 */
	public function make_status_pending( $id, $job_id, callable $wait_time_callback ) {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return false;
		}

		$item = $this->find( $id );

		if ( empty( $item ) ) {
			return false;
		}

		$update_data = [
			'job_id'        => $job_id,
			'retries'       => (int) $item->retries + 1,
			'status'        => self::STATUS_PENDING,
			'error_code'    => null,
			'error_message' => null,
		];

		// Determine the postpone time based on number of retries.
		$wait_time      = $wait_time_callback( $update_data['retries'] );
		$postpone_until = time() + $wait_time;

		$update_data['postponed_until'] = gmdate( 'Y-m-d H:i:s', $postpone_until );

		return $this->update_item( $item->id, $update_data );
	}

	/**
	 * Postpone item.
	 *
	 * @param int      $id Item ID.
	 * @param callable $wait_time_callback Callback to get the wait time.
	 *
	 * @return bool
	 */
	public function postpone_item( $id, callable $wait_time_callback ) {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return false;
		}

		$item = $this->find( $id );

		if ( empty( $item ) ) {
			return false;
		}

		// Determine the postpone time based on number of retries.
		$wait_time      = $wait_time_callback( $item->retries + 1 );
		$postpone_until = time() + $wait_time;

		$update_data = [
			'retries'         => (int) $item->retries + 1,
			'postponed_until' => gmdate( 'Y-m-d H:i:s', $postpone_until ),
		];

		return $this->update_item( $item->id, $update_data );
	}

	/**
	 * Set "to download" status.
	 *
	 * @param int $id row.
	 *
	 * @return bool
	 */
	public function make_status_to_download( int $id ) {
		return $this->make_status( $id, self::STATUS_TO_DOWNLOAD );
	}

	/**
	 * Set downloading status.
	 *
	 * @param int $id row.
	 *
	 * @return bool
	 */
	public function make_status_downloading( int $id ) {
		return $this->make_status( $id, self::STATUS_DOWNLOADING );
	}

	/**
	 * Set failed status.
	 *
	 * @param int     $id row.
	 * @param string  $error_code code.
	 * @param ?string $error_message message.
	 *
	 * @return bool
	 */
	public function make_status_failed( int $id, string $error_code, ?string $error_message = '' ) {
		return $this->make_status( $id, self::STATUS_FAILED, $error_code, $error_message );
	}

	/**
	 * Set status.
	 *
	 * @param int    $id row.
	 * @param string $status status.
	 * @param string $error_code code.
	 * @param string $error_message message.
	 *
	 * @return bool
	 */
	private function make_status( int $id, string $status, $error_code = null, $error_message = null ) {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return false;
		}

		$item = $this->find( $id );

		if ( empty( $item ) ) {
			return false;
		}

		$update_data = [
			'status'        => $status,
			'error_code'    => $error_code,
			'error_message' => $error_message,
		];

		return $this->update_item( $item->id, $update_data );
	}

	/**
	 * Get job ready to download.
	 *
	 * @return ?Row Job ready to download.
	 */
	public function get_job_ready_to_download(): ?Row {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return null;
		}

		$items = $this->query(
			[
				'status'  => self::STATUS_TO_DOWNLOAD,
				'orderby' => 'modified_at',
				'order'   => 'ASC',
				'number'  => 1,
			],
			false
		);

		return $this->extract_first_item( $items );
	}

	/**
	 * Get job ready to upload.
	 *
	 * @return ?Row Job ready to upload.
	 */
	public function get_job_ready_to_upload(): ?Row {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return null;
		}

		$items = $this->query(
			[
				'status'  => self::STATUS_NEW,
				'orderby' => [
					'DESC' => 'priority',
					'ASC'  => 'created_at',
				],
				'number'  => 1,
			],
			false
		);

		return $this->extract_first_item( $items );
	}

	/**
	 * Get total number of pending jobs.
	 *
	 * @return int Total number of pending jobs.
	 */
	public function get_pending_jobs_count(): int {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return 0;
		}

		return (int) $this->query(
			[
				'count'  => true,
				'status' => self::STATUS_PENDING,
			],
			false
		);
	}

	/**
	 * Get the size of the image queue. Failed jobs are excluded.
	 *
	 * This is intended to show the remaining number of images to be optimized in a progress widget.
	 *
	 * @return int the size of the image queue
	 */
	public function get_queue_original_size(): int {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return 0;
		}

		return (int) $this->query(
			[
				'format'         => 'original',
				'count'          => true,
				'status__not_in' => [
					self::STATUS_FAILED,
				],
			],
			false
		);
	}


	/**
	 * Check if there are any more items in the queue that can be processed at this time.
	 *
	 * These could be more items ready for download or more items waiting to be uploaded. Also pending images with
	 * postponed date in the past.
	 *
	 * @since 3.12.6.1_1.1-1
	 * @since latest Added $exclude_new parameter to be able to exclude new items from the check.
	 *
	 * @param bool $exclude_new Exclude new items from the check.
	 *
	 * @return bool
	 */
	public function has_more_items_to_process( $exclude_new ): bool {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return false;
		}

		$statuses = [
			self::STATUS_TO_DOWNLOAD,
		];

		if ( ! $exclude_new ) {
			$statuses[] = self::STATUS_NEW;
		}

		$count = (int) $this->query(
			[
				'count'           => true,
				'status__in'      => $statuses,
				'postponed_until' => '0000-00-00 00:00:00',
			],
			false
		);

		if ( $count > 0 ) {
			return true;
		}

		$count = (int) $this->query(
			[
				'count'      => true,
				'status'     => [
					self::STATUS_PENDING,
				],
				'date_query' => [
					[
						'column' => 'postponed_until',
						'before' => current_time( 'mysql', true ),
					],
				],
			],
			false
		);

		return $count > 0;
	}

	/**
	 * Get earliest postponed item.
	 *
	 * @since 3.12.6.1_1.1-1
	 * @since latest Changed the return from the count of items to the earliest postponed item.
	 *
	 * @return ?Row Earliest postponed item.
	 */
	public function get_earliest_postponed_item(): ?Row {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return null;
		}

		$items = $this->query(
			[
				'status'     => self::STATUS_PENDING,
				'order'      => 'ASC',
				'orderby'    => 'postponed_until',
				'number'     => 1,
				'date_query' => [
					[
						'column'  => 'postponed_until',
						'compare' => '!=',
						'value'   => '0000-00-00 00:00:00',
					],
				],
			],
			false
		);

		return $this->extract_first_item( $items );
	}

	/**
	 * Get job pending and not postponed.
	 *
	 * @return ?Row Job pending and not postponed.
	 */
	public function get_job_pending_not_postponed(): ?Row {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return null;
		}

		$items = $this->query(
			[
				'status'     => self::STATUS_PENDING,
				'order'      => 'ASC',
				'orderby'    => 'postponed_until',
				'date_query' => [
					[
						'column' => 'postponed_until',
						'before' => current_time( 'mysql', true ),
					],
				],
				'number'     => 1,
			],
			false
		);

		return $this->extract_first_item( $items );
	}

	/**
	 * Extract the first items from raw output of query() function.
	 *
	 * @param array $items Items to extract first item from. Output from query().
	 *
	 * @return ?Row
	 *
	 * @see query()
	 */
	private function extract_first_item( $items ): ?Row {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return null;
		}

		if ( empty( $items ) || ! is_array( $items ) ) { // @phpstan-ignore-line
			return null;
		}

		$count = count( $items );
		for ( $i = 0; $i < $count; $i ++ ) {
			if ( $items[ $i ] instanceof Row ) {
				return $items[ $i ];
			}
		}

		return null;
	}

	/**
	 * Deletes all entries from the table.
	 *
	 * @return int|bool Boolean true in case of success. Boolean false on error.
	 */
	public function delete_all() {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return false;
		}

		$table_name = $this->get_db()->prefix . $this->table_name;

		return $this->get_db()->query( 'TRUNCATE ' . $table_name . ';' );
	}

	/**
	 * Make status new for all.
	 *
	 * @return int|bool Boolean true in case of success. Boolean false on error.
	 */
	public function make_status_new_for_all() {
		if ( ! self::$table_exists && ! $this->table_exists() ) {
			return false;
		}

		$table_name = $this->get_db()->prefix . $this->table_name;

		$sql = "UPDATE `$table_name` SET `status` = %s, `retries` = 0, `error_code` = null, `error_message` = null, `postponed_until` = '0000-00-00 00:00:00';";

		return $this->get_db()->query(
			$this->get_db()->prepare(
				$sql,
				[
					'status' => self::STATUS_NEW,
				]
			)
		);
	}

	/**
	 * Returns the current status of `wpr_image_optimization` table; true if it exists, false otherwise.
	 *
	 * @return boolean
	 */
	private function table_exists(): bool {
		if ( self::$table_exists ) {
			return true;
		}

		// Get the database interface.
		$db = $this->get_db();

		// Bail if no database interface is available.
		if ( empty( $db ) ) {
			return false;
		}

		// Query statement.
		$query    = 'SHOW TABLES LIKE %s';
		$like     = $db->esc_like( $db->{$this->table_name} );
		$prepared = $db->prepare( $query, $like );
		$result   = $db->get_var( $prepared );

		// Does the table exist?
		$exists = $this->is_success( $result );

		if ( $exists ) {
			self::$table_exists = $exists;
		}

		return $exists;
	}
}
