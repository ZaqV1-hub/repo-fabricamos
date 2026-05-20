<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class By_Author extends Conditions_Base {
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
		return 'by_author';
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'By Author', 'jupiterx-core' );
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		return is_singular() && get_post_field( 'post_author' ) === $args['id']['value'];
	}

	/**
	 * Get options for conditions with search control.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_options( $value ) {
		$query_args = [ 'search' => '*' . sanitize_text_field( $value ) . '*' ];
		$user_query = new \WP_User_Query( $query_args );

		$results = [];

		foreach ( $user_query->get_results() as $user ) {
			$results[] = [
				'id' => $user->ID,
				'name' => $user->data->display_name,
			];
		}

		return $results;
	}
}
