<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Singular extends Conditions_Base {
	protected $sub_conditions = [
		'front_page',
	];

	/**
	 * Get conditions priority.
	 *
	 * @since 3.7.0
	 * @return int
	 */
	public static function get_priority() {
		return 60;
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
		return 'singular';
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Singular', 'jupiterx-core' );
	}

	/**
	 * Get condition all label (for condition with group conditions).
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_all_label() {
		return esc_html__( 'All Singular', 'jupiterx-core' );
	}

	/**
	 * Get condition sub conditions.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_sub_conditions() {
		$post_types = $this->cpt;

		$post_types['attachment'] = get_post_type_object( 'attachment' )->label;

		foreach ( $post_types as $post_type => $label ) {
			$condition = \JupiterX_Popups_Conditions_Manager::register_condition( 'Post', [ 'post_type' => $post_type ] );

			$this->register_condition( $condition );
		}

		if ( ! in_array( 'child_of', $this->sub_conditions, true ) ) {
			$this->sub_conditions[] = 'child_of';
		}

		if ( ! in_array( 'any_child_of', $this->sub_conditions, true ) ) {
			$this->sub_conditions[] = 'any_child_of';
		}

		if ( ! in_array( 'by_author', $this->sub_conditions, true ) ) {
			$this->sub_conditions[] = 'by_author';
		}

		if ( ! in_array( 'not_found404', $this->sub_conditions, true ) ) {
			$this->sub_conditions[] = 'not_found404';
		}

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
		return ( is_singular() && ! is_embed() ) || is_404();
	}
}
