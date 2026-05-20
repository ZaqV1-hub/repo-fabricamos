<?php
/**
 * Jet Cache Module
 *
 * Version: 1.0.0
 */

namespace Jet_Cache;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * Module directory path.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var srting.
	 */
	protected $path;

	/**
	 * Module directory URL.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var srting.
	 */
	protected $url;

	/**
	 * Module version
	 *
	 * @var string
	 */
	protected $version = '1.0.0';

	/**
	 * @var null
	 */
	public $db_manager = null;

	/**
	 * Jet_Dashboard constructor.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->load_files();
		$this->db_manager = new DB_Manager();
	}

	/**
	 * [load_files description]
	 * @return [type] [description]
	 */
	public function load_files() {
		/**
		 * Modules
		 */
		require $this->path . 'inc/db-manager.php';
		require $this->path . 'inc/functions.php';
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

