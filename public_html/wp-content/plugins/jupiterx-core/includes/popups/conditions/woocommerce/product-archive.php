<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Archive extends Conditions_Base {
	private $post_taxonomies;

	public function __construct( $data ) {
		parent::__construct();

		$taxonomies            = get_object_taxonomies( 'product', 'objects' );
		$this->post_taxonomies = wp_filter_object_list( $taxonomies, [
			'public' => true,
			'show_in_nav_menus' => true,
		] );
	}

	/**
	 * Get condition type.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_type() {
		return 'archive';
	}

	/**
	 * Get condition name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'product_archive';
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
		return esc_html__( 'Product Archive', 'jupiterx-core' );
	}

	/**
	 * Get condition all label (for condition with group conditions).
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_all_label() {
		return esc_html__( 'All Product Archives', 'jupiterx-core' );
	}

	/**
	 * Get condition sub conditions.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_sub_conditions() {
		if ( empty( $this->post_taxonomies ) ) {
			$taxonomies            = get_object_taxonomies( 'product', 'objects' );
			$this->post_taxonomies = wp_filter_object_list( $taxonomies, [
				'public' => true,
				'show_in_nav_menus' => true,
			] );
		}

		if ( empty( $this->post_taxonomies ) ) {
			return;
		}

		$sub_conditions = [
			'Shop_Page',
			'Product_Search',
		];

		$sub_conditions_result = [];

		foreach ( $sub_conditions as $class_name ) {
			$condition = \JupiterX_Popups_Conditions_Manager::register_condition( $class_name, [], 'woocommerce' );

			$sub_conditions_result = $this->register_condition( $condition );
		}

		foreach ( $this->post_taxonomies as $slug => $object ) {
			$object->labels->name = ucwords( $object->labels->name );
			$object->label        = ucwords( $object->label );

			$condition = \JupiterX_Popups_Conditions_Manager::register_condition( 'Taxonomy', [ 'object' => $object ] );

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
		return is_shop() || is_product_taxonomy() || \JupiterX_Popups_Conditions_Manager::is_product_search_page();
	}
}
