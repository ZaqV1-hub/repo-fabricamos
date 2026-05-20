<?php
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Archive extends Conditions_Base {
	protected $sub_conditions = [
		'author',
		'date',
		'search',
	];

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
	 * Get condition name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'archive';
	}

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Archives', 'jupiterx-core' );
	}

	/**
	 * Get conditions priority.
	 *
	 * @since 3.7.0
	 * @return int
	 */
	public static function get_priority() {
		return 80;
	}

	/**
	 * Get condition all label (for condition with group conditions).
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_all_label() {
		return esc_html__( 'All Archives', 'jupiterx-core' );
	}

	/**
	 * Get condition sub conditions.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_sub_conditions() {
		foreach ( $this->cpt as $post_type => $label ) {
			if ( ! get_post_type_archive_link( $post_type ) ) {
				continue;
			}

			$condition = \JupiterX_Popups_Conditions_Manager::register_condition( 'Post_Type_Archive', [ 'post_type' => $post_type ] );

			$this->register_condition( $condition );
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
		$is_archive = is_archive() || is_home() || is_search();

		// WooCommerce is handled by `woocommerce` module.
		if ( $is_archive && class_exists( 'WooCommerce' ) && is_woocommerce() ) {
			$is_archive = false;
		}

		return $is_archive;
	}
}
