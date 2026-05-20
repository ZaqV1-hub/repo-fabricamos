<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Any_Child_Of_Term extends Child_Of_Term {
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
		return 'any_child_of_' . $this->taxonomy->name;
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		/* translators: %s: Taxonomy name. */
		return sprintf( esc_html__( 'Any Child %s Of', 'jupiterx-core' ), $this->taxonomy->labels->singular_name );
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

		$current = get_queried_object();

		if ( ! $this->valid_term() || 0 === $current->parent ) {
			return false;
		}

		while ( $current->parent > 0 ) {
			if ( $id === $current->parent ) {
				return true;
			}

			$current = get_term_by( 'id', $current->parent, $current->taxonomy );
		}

		return $id === $current->parent;
	}
}
