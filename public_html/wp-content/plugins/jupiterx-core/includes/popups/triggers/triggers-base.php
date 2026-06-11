<?php
/**
 * Abstract class for popup triggers.
 *
 * @since 3.7.0
 */
namespace JupiterX_Core\Popup\Triggers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Triggers_Base {
	public $data = [];

	/**
	 * Get unique name for triggers.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_unique_name() {
		return 'trigger_' . $this->get_name();
	}

	/**
	 * Get trigger name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get trigger label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	abstract public function get_label();

	/**
	 * Get trigger control.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function add_control() {
		return [];
	}

	/**
	 * Get trigger operators.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function operators() {
		return [];
	}

	/**
	 * Set condition config.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_data() {
		$this->data['label']     = $this->get_label();
		$this->data['control']   = $this->add_control();
		$this->data['operators'] = $this->operators();

		return $this->data;
	}
}
