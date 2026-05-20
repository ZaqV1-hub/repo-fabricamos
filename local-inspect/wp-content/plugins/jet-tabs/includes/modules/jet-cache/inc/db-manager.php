<?php

namespace Jet_Cache;

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

class DB_Manager
{

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Constructor for the class
	 */
	function __construct() {
		$this->init_db_required();
	}

	/**
	 * [wpdb description]
	 * @return [type] [description]
	 */
	public function wpdb() {
		global $wpdb;
		return $wpdb;
	}

	/**
	 * Return table name by key
	 *
	 * @param  string $table table key.
	 * @return string
	 */
	public static function tables( $table = null, $return = 'all' ) {

		global $wpdb;

		$prefix = 'jet_';

		$tables = [
			'cache' => [
				'name'        => $wpdb->prefix . $prefix . 'cache',
				'export_name' => $prefix . 'cache',
				'query'       => "
					id bigint(20) NOT NULL AUTO_INCREMENT,
					source varchar(191) DEFAULT 'default' NOT NULL,
					source_id bigint(20) DEFAULT '0' NOT NULL,
					cache_key varchar(191),
					cache_data longtext,
					cache_expired text,
					PRIMARY KEY (id)
				",
			],
		];

		if ( ! $table && 'all' === $return ) {
			return $tables;
		}

		switch ( $return ) {
			case 'all':
				return isset( $tables[ $table ] ) ? $tables[ $table ] : false;

			case 'name':
				return isset( $tables[ $table ] ) ? $tables[ $table ]['name'] : false;

			case 'query':
				return isset( $tables[ $table ] ) ? $tables[ $table ]['query'] : false;
		}

		return false;

	}

	/**
	 * @return void
	 */
	public function init_db_required() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'jet_cache';

		if ( empty( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) ) {
			self::create_tables();
		}
	}

	/**
	 * Create all tables on activation
	 *
	 * @return [type] [description]
	 */
	public static function create_tables() {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		foreach ( self::tables() as $table ) {
			$table_name  = $table['name'];
			$table_query = $table['query'];

			if ( $table_name !== $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) {

				$sql = "CREATE TABLE $table_name (
					$table_query
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

				dbDelta( $sql );
			}
		}
	}

	/**
	 * @param $key
	 * @param $data
	 * @param $expiration
	 * @return bool|int|\mysqli_result|null
	 */
	public function set_cache( $key = false, $data = [], $expiration = 0, $source_id = 0, $source = 'default' ) {
		$table_name = $this->tables( 'cache', 'name' );
		$cache_row = $this->wpdb()->get_row( $this->wpdb()->prepare( "SELECT * FROM $table_name WHERE cache_key = %s", $key ), OBJECT );
		$cache_data = maybe_serialize( $data );
		$status = false;
		$expiration_time = time() + (int) $expiration;

		if ( empty( $cache_row ) ) {
			$status = $this->wpdb()->insert(
				$table_name,
				[
					'source'        => $source,
					'source_id'     => $source_id,
					'cache_key'     => $key,
					'cache_data'    => $cache_data,
					'cache_expired' => $expiration_time,
				]
			);
		}

		return $status;
	}

	/**
	 * [insert_review description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function get_cache( $key = false, $default = false ) {
		$table_name = $this->tables( 'cache', 'name' );
		$cache_row = $this->wpdb()->get_row( $this->wpdb()->prepare( "SELECT * FROM $table_name WHERE cache_key = %s", $key ), OBJECT );

		if ( empty( $cache_row ) ) {
			return $default;
		}

		$cache_expired = $cache_row->cache_expired;

		if ( ! empty( $cache_expired ) && $cache_expired < time() ) {
			$this->delete_cache( $key );

			return false;
		}

		return maybe_unserialize( $cache_row->cache_data );
	}

	/**
	 * @param $key
	 * @return bool|int|\mysqli_result|null
	 */
	public function delete_cache( $key = false ) {

		if ( ! $key ) {
			return false;
		}

		$table_name = $this->tables( 'cache', 'name' );

		return $this->wpdb()->delete( $table_name, [ 'cache_key' => $key ] );
	}

	/**
	 * @param $key
	 * @return bool|int|\mysqli_result|null
	 */
	public function delete_cache_by_instance_id( $source_id = false, $source = false ) {

		if ( ! $source_id ) {
			return false;
		}

		$table_name = $this->tables( 'cache', 'name' );
		$params = [ 'source_id' => $source_id ];

		if ( ! empty( $source ) ) {
			$params['source'] = $source;
		}

		return $this->wpdb()->delete( $table_name, $params );
	}

	/**
	 * @param $key
	 * @return bool|int|\mysqli_result|null
	 */
	public function delete_cache_by_source( $source = false ) {

		if ( ! $source ) {
			return false;
		}

		$table_name = $this->tables( 'cache', 'name' );
		$params = [ 'source' => $source ];

		return $this->wpdb()->delete( $table_name, $params );
	}

	/**
	 * Returns the instance.
	 *
	 * @return object
	 * @since  1.0.0
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}
}
