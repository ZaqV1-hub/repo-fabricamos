<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Search extends Conditions_Base {
	/**
	 * Get condition type.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_type() {
		return 'singular';
	}

	/**
	 * Get condition name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'shop_page';
	}

	/**
	 * Get conditions priority.
	 *
	 * @since 3.7.0
	 * @return int
	 */
	public static function get_priority() {
		return 40;
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Shop Page', 'jupiterx-core' );
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		return is_shop();
	}
}
