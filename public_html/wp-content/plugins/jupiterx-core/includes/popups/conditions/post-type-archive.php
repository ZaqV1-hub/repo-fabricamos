<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post_Type_Archive extends Conditions_Base {
	private $post_type;
	private $post_taxonomies;

	public function __construct( $data ) {
		parent::__construct();

		$this->post_type = get_post_type_object( $data['post_type'] );
		$taxonomies      = get_object_taxonomies( $data['post_type'], 'objects' );

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
	 * Get conditions priority.
	 *
	 * @since 3.7.0
	 * @return int
	 */
	public static function get_priority() {
		return 70;
	}

	/**
	 * Get condition name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return $this->post_type->name . '_archive';
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		/* translators: %s: Post type label. */
		return sprintf( esc_html__( '%s Archive', 'jupiterx-core' ), $this->post_type->label );
	}

	/**
	 * Get condition all label (for condition with group conditions).
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_all_label() {
		/* translators: %s: Post type label. */
		return sprintf( esc_html__( '%s Archive', 'jupiterx-core' ), $this->post_type->label );
	}

	/**
	 * Get condition sub conditions.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_sub_conditions() {
		if ( empty( $this->post_taxonomies ) ) {
			return $this->sub_conditions;
		}

		$sub_conditions_result = [];

		foreach ( $this->post_taxonomies as $slug => $object ) {
			$condition = \JupiterX_Popups_Conditions_Manager::register_condition( 'Taxonomy', [ 'object' => $object ] );

			$sub_conditions_result = $this->register_condition( $condition );

			if ( ! $object->hierarchical ) {
				continue;
			}

			$sub_conditions = [
				'Child_Of_Term',
				'Any_Child_Of_Term',
			];

			foreach ( $sub_conditions as $class_name ) {
				$sub_condition = \JupiterX_Popups_Conditions_Manager::register_condition( $class_name, [ 'object' => $object ] );

				$sub_conditions_result = $this->register_condition( $sub_condition );
			}
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
		return is_post_type_archive( $this->post_type->name ) || ( 'post' === $this->post_type->name && is_home() );
	}
}
