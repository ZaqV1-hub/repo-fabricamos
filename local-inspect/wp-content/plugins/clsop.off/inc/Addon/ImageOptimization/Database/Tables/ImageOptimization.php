<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization\Database\Tables;

use WP_Rocket\Dependencies\BerlinDB\Database\Table;

/**
 * ImageOptimization Queue Table.
 */
class ImageOptimization extends Table {

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $name = 'wpr_image_optimization';

	/**
	 * Database version key (saved in _options or _sitemeta)
	 *
	 * @var string
	 */
	protected $db_version_key = 'wpr_image_optimization_version';

	/**
	 * Database version
	 *
	 * @var int
	 */
	protected $version = 20230405;

	/**
	 * Key => value array of versions => methods.
	 *
	 * @var array
	 */
	protected $upgrades = [];

	/**
	 * Instantiate class.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'admin_init', [ $this, 'maybe_trigger_recreate_table' ], 9 );
		add_action( 'init',  [ $this, 'maybe_upgrade' ] );
	}

	/**
	 * Setup the database schema
	 *
	 * @return void
	 */
	protected function set_schema() {
		$this->schema = "
			id               bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			url              varchar(2000)       NOT NULL default '',
			format           varchar(32)         NOT NULL default '',
			status           varchar(32)         NOT NULL default '',
			secret           varchar(32)             NULL default NULL,
			retries          tinyint(1)          NOT NULL default 1,
			job_id           varchar(255)            NULL default NULL,
			priority         tinyint(1)          NOT NULL default 0,
			error_code       varchar(32)             NULL default NULL,
			error_message    longtext                NULL default NULL,
			created_at       timestamp           NOT NULL default '0000-00-00 00:00:00',
			modified_at      timestamp           NOT NULL default '0000-00-00 00:00:00',
			postponed_until  timestamp           NOT NULL default '0000-00-00 00:00:00',
			PRIMARY KEY (id),
			INDEX `status_index` (`status`),
			INDEX `format_index` (`format`)";
	}

	/**
	 * Returns name from table.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->apply_prefix( $this->table_name );
	}

	/**
	 * Trigger recreation of cache table if not exist.
	 *
	 * @return void
	 */
	public function maybe_trigger_recreate_table() {
		if ( $this->exists() ) {
			return;
		}

		delete_option( $this->db_version_key ); // @phpstan-ignore-line
	}
}
