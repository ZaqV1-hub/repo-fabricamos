<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization\Database\Row;

use WP_Rocket\Dependencies\BerlinDB\Database\Row;

/**
 * ImageOptimization Row.
 */
class ImageOptimization extends Row {
	/**
	 * Id.
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Format.
	 *
	 * @var string
	 */
	public $format;

	/**
	 * Status
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Secret.
	 *
	 * @var string
	 */
	public $secret;

	/**
	 * Retries.
	 *
	 * @var int
	 */
	public $retries;

	/**
	 * Job id.
	 *
	 * @var string
	 */
	public $job_id;

	/**
	 * Priority.
	 *
	 * @var int
	 */
	public $priority;

	/**
	 * Error code.
	 *
	 * @var string
	 */
	public $error_code;

	/**
	 * Error message.
	 *
	 * @var string
	 */
	public $error_message;

	/**
	 * Createda at.
	 *
	 * @var false|int
	 */
	public $created_at;

	/**
	 * Modified at.
	 *
	 * @var false|int
	 */
	public $modified_at;

	/**
	 * Postponed until
	 *
	 * @var false|int
	 */
	public $postponed_until;

	/**
	 * Queue constructor.
	 *
	 * @param mixed $item Object Row.
	 */
	public function __construct( $item ) {
		parent::__construct( $item );

		// Set the type of each column, and prepare.
		$this->id              = (int) $this->id;
		$this->url             = (string) $this->url;
		$this->format          = (string) $this->format;
		$this->status          = (string) $this->status;
		$this->secret          = (string) $this->secret;
		$this->retries         = (int) $this->retries;
		$this->job_id          = (string) $this->job_id;
		$this->priority        = (int) $this->priority;
		$this->error_code      = (string) $this->error_code;
		$this->error_message   = (string) $this->error_message;
		$this->created_at      = false === $this->created_at ? 0 : strtotime( $this->created_at );
		$this->modified_at     = false === $this->modified_at ? 0 : strtotime( $this->modified_at );
		$this->postponed_until = false === $this->postponed_until ? 0 : strtotime( $this->postponed_until );
	}
}
