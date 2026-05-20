<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Child_Of_Term extends Taxonomy {
	private $taxonomy;

	public function __construct( $data ) {
		parent::__construct( $data );

		$this->taxonomy = $data['object'];
	}

	/**
	 * Get condition name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'child_of_' . $this->taxonomy->name;
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		/* translators: %s: Taxonomy name. */
		return sprintf( esc_html__( 'Direct Child %s Of', 'jupiterx-core' ), $this->taxonomy->labels->singular_name );
	}

	/**
	 * Validate current term.
	 *
	 * @since 3.7.0
	 * @return boolean
	 */
	public function valid_term() {
		$taxonomy       = $this->taxonomy->name;
		$queried_object = get_queried_object();

		return ( $queried_object && isset( $queried_object->taxonomy ) && $taxonomy === $queried_object->taxonomy );
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		$id      = ! empty( $args['id'] ) ? (int) $args['id']['value'] : null;
		$current = get_queried_object();

		return $this->valid_term() && $id === $current->parent;
	}
}
