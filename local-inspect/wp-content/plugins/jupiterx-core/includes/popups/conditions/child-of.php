<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Child_Of extends Conditions_Base {
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
		return 'child_of';
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Direct Child Of', 'jupiterx-core' );
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		if ( ! is_singular() ) {
			return false;
		}

		$id = (int) $args['id']['value'];

		$parent_id = wp_get_post_parent_id( get_the_ID() );

		return ( ( 0 === $id && 0 < $parent_id ) || ( $parent_id === $id ) );
	}

	/**
	 * Get options for conditions with search control.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_options( $value ) {
		$hierarchical_post_types = get_post_types( [
			'hierarchical' => true,
			'public' => true,
		] );

		$query_args = [
			'post_type' => $hierarchical_post_types,
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
