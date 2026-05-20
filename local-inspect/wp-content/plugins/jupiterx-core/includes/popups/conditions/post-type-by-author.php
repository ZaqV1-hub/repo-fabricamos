<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post_Type_By_Author extends Conditions_Base {
	private $post_type;

	public function __construct( $data ) {
		parent::__construct();

		$this->post_type = $data['post_type'];
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
	 * Get conditions priority.
	 *
	 * @since 3.7.0
	 * @return int
	 */
	public static function get_priority() {
		return 40;
	}

	/**
	 * Get condition name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return $this->post_type->name . '_by_author';
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		/* translators: %s: Post type label. */
		return sprintf( esc_html__( '%s By Author', 'jupiterx-core' ), $this->post_type->label );
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		return is_singular( $this->post_type->name ) && get_post_field( 'post_author' ) === $args['id']['value'];
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
