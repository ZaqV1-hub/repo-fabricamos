<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Woo_Commerce extends Conditions_Base {
	/**
	 * Get condition type.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_type() {
		return 'woo_commerce';
	}

	/**
	 * Get condition name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'woo_commerce';
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'WooCommerce', 'jupiterx-core' );
	}

	/**
	 * Get condition all label (for condition with group conditions).
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_all_label() {
		return esc_html__( 'Entire Shop', 'jupiterx-core' );
	}

	/**
	 * Get condition sub conditions.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_sub_conditions() {
		$sub_conditions_result = [];

		$sub_conditions = [
			'Product_Archive',
			'Post',
		];

		foreach ( $sub_conditions as $class_name ) {
			$condition = \JupiterX_Popups_Conditions_Manager::register_condition( $class_name, [ 'post_type' => 'product' ], 'woocommerce' );

			$sub_conditions_result = $this->register_condition( $condition );
		}

		return $sub_conditions_result;
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		return is_woocommerce() || \JupiterX_Popups_Conditions_Manager::is_product_search_page();
	}
}
