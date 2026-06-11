<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class General extends Conditions_Base {
	protected $sub_conditions = [
		'archive',
		'singular',
	];

	public function __construct() {
		if ( class_exists( 'WooCommerce' ) ) {
			$this->sub_conditions[] = 'woo_commerce';
		}
	}

	/**
	 * Get condition type.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_type() {
		return 'general';
	}

	/**
	 * Get condition name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'general';
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Entire Site', 'jupiterx-core' );
	}

	/**
	 * Get condition all label (for condition with group conditions).
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_all_label() {
		return esc_html__( 'Entire Site', 'jupiterx-core' );
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		return true;
	}
}
