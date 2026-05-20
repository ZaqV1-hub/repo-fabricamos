<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class In_Sub_Term extends In_Taxonomy {
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
		return 'in_' . $this->taxonomy->name . '_children';
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		/* translators: %s: Taxonomy label. */
		return sprintf( esc_html__( 'In Child %s', 'jupiterx-core' ), $this->taxonomy->labels->name );
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to validate.
	 * @since 3.7.0
	 * @return boolean
	 */
	public function is_valid( $args ) {
		$id = ! empty( $args['id'] ) ? (int) $args['id']['value'] : null;

		if ( ! is_singular() || ! $id ) {
			return false;
		}

		$child_terms = get_term_children( $id, $this->taxonomy->name );

		return ! empty( $child_terms ) && has_term( $child_terms, $this->taxonomy->name );
	}
}
