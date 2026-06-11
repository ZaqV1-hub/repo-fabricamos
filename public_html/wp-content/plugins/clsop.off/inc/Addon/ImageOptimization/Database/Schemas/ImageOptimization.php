<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization\Database\Schemas;

use WP_Rocket\Dependencies\BerlinDB\Database\Schema;
use WP_Rocket\Addon\ImageOptimization\Database\Queries\ImageOptimization as Query;

/**
 * ImageOptimization Queue Schema.
 */
class ImageOptimization extends Schema {

	/**
	 * Array of database column objects
	 *
	 * @var array
	 */
	public $columns = [

		// ID column.
		[
			'name'     => 'id',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'extra'    => 'auto_increment',
			'primary'  => true,
			'sortable' => true,
		],

		// URL column.
		[
			'name'       => 'url',
			'type'       => 'varchar',
			'length'     => '2000',
			'default'    => '',
			'cache_key'  => true,
			'searchable' => true,
			'sortable'   => true,
		],

		// Format column.
		[
			'name'       => 'format',
			'type'       => 'varchar',
			'length'     => '32',
			'default'    => '',
			'cache_key'  => true,
			'searchable' => true,
			'sortable'   => true,
		],

		// Status column.
		[
			'name'       => 'status',
			'type'       => 'varchar',
			'length'     => '32',
			'default'    => Query::STATUS_NEW,
			'cache_key'  => true,
			'searchable' => true,
			'sortable'   => true,
		],

		// Secret column.
		[
			'name'       => 'secret',
			'type'       => 'varchar',
			'length'     => '32',
			'default'    => null,
			'allow_null' => true,
		],

		// Retries column.
		[
			'name'    => 'retries',
			'type'    => 'tinyint',
			'length'  => '1',
			'default' => 0,
		],

		// Job_id column.
		[
			'name'       => 'job_id',
			'type'       => 'varchar',
			'length'     => '255',
			'default'    => null,
			'allow_null' => true,
			'searchable' => true,
		],

		// priority column.
		[
			'name'       => 'priority',
			'type'       => 'tinyint',
			'default'    => 0,
			'allow_null' => false,
			'sortable'   => true,
		],

		// error_code column.
		[
			'name'       => 'error_code',
			'type'       => 'varchar',
			'length'     => '32',
			'default'    => null,
			'allow_null' => true,
		],

		// error_message column.
		[
			'name'       => 'error_message',
			'type'       => 'longtext',
			'default'    => null,
			'allow_null' => true,
		],

		// Created_at column.
		[
			'name'       => 'created_at',
			'type'       => 'timestamp',
			'default'    => '0000-00-00 00:00:00',
			'created'    => true,
			'date_query' => true,
			'sortable'   => true,
		],

		// Modified_at column.
		[
			'name'       => 'modified_at',
			'type'       => 'timestamp',
			'default'    => '0000-00-00 00:00:00',
			'modified'   => true,
			'created'    => true,
			'date_query' => true,
			'sortable'   => true,
		],

		// Postponed_until column.
		[
			'name'       => 'postponed_until',
			'type'       => 'timestamp',
			'default'    => '0000-00-00 00:00:00',
			'created'    => true,
			'date_query' => true,
			'sortable'   => true,
		],
	];
}
