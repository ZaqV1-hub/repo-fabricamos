<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post extends Conditions_Base {
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
	 * Get conditions priority.
	 *
	 * @since 3.7.0
	 * @return int
	 */
	public static function get_priority() {
		return 40;
	}

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
		return $this->post_type->name;
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return $this->post_type->labels->singular_name;
	}

	/**
	 * Get condition all label (for condition with group conditions).
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_all_label() {
		/* translators: %s: Post type label. */
		return $this->post_type->label;
	}

	/**
	 * Get condition sub conditions.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_sub_conditions() {
		if ( empty( $this->post_type ) ) {
			return $this->sub_conditions;
		}

		foreach ( $this->post_taxonomies as $slug => $object ) {
			$condition = \JupiterX_Popups_Conditions_Manager::register_condition( 'In_Taxonomy', [ 'object' => $object ] );

			$this->register_condition( $condition );

			if ( $object->hierarchical ) {
				$in_sub_term = \JupiterX_Popups_Conditions_Manager::register_condition( 'In_Sub_Term', [ 'object' => $object ] );

				$this->register_condition( $in_sub_term );
			}
		}

		$by_author = \JupiterX_Popups_Conditions_Manager::register_condition( 'Post_Type_By_Author', [ 'post_type' => $this->post_type ] );

		$this->register_condition( $by_author );

		return $this->sub_conditions;
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		if ( isset( $args['id'] ) ) {

			$id = ! empty( $args['id']['value'] ) ? (int) $args['id']['value'] : '';

			if ( $id ) {
				return is_singular() && ( get_queried_object_id() === $id );
			}
		}

		return is_singular( $this->post_type->name );
	}

	/**
	 * Get options for conditions with search control.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_options( $value ) {
		$query_args = [
			'post_type' => $this->get_name(),
			's' => sanitize_text_field( $value ),
		];

		$query = new \WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			return [];
		}

		$results = [];

		foreach ( $query->posts as $post ) {
			$results[] = [
				'id' => $post->ID,
				'name' => $post->post_title,
			];
		}

		return $results;
	}
}
