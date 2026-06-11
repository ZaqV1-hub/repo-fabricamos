<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class In_Taxonomy extends Conditions_Base {
	private $taxonomy;

	public function __construct( $data ) {
		parent::__construct();

		$this->taxonomy = $data['object'];
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
		return 'in_' . $this->taxonomy->name;
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		/* translators: %s: Taxonomy label. */
		return sprintf( esc_html__( 'In %s', 'jupiterx-core' ), $this->taxonomy->labels->singular_name );
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		$term = ! empty( $args['id']['value'] ) ? (int) $args['id']['value'] : '';

		return is_singular() && has_term( $term, $this->taxonomy->name );
	}

	/**
	 * Get options for conditions with search control.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_options( $value ) {
		$args = [
			'taxonomy' => [ $this->taxonomy->name ],
			'hide_empty' => true,
			'fields' => 'all',
			'name__like' => sanitize_text_field( $value ),
		];

		$terms = get_terms( $args );
		$count = count( $terms );

		if ( $count < 1 ) {
			return [];
		}

		$results = [];

		foreach ( $terms as $term ) {
			$results[] = [
				'id' => $term->term_id,
				'name' => esc_html__( 'Categories: ', 'jupiterx-core' ) . $term->name,
			];
		}

		return $results;
	}
}
