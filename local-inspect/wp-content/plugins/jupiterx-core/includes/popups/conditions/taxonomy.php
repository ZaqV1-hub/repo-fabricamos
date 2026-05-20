<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Taxonomy extends Conditions_Base {
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
		return $this->taxonomy->name;
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return $this->taxonomy->label;
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		$taxonomy = $this->get_name();
		$id       = ! empty( $args['id'] ) ? (int) $args['id']['value'] : null;

		if ( 'category' === $taxonomy ) {
			return is_category( $id );
		}

		if ( 'post_tag' === $taxonomy ) {
			return is_tag( $id );
		}

		return is_tax( $taxonomy, $id );
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
				'name' => $term->name,
			];
		}

		return $results;
	}
}
