<?php
/**
 * Abstract class for popup conditions.
 *
 * @since 3.7.0
 */
namespace JupiterX_Core\Popup\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Conditions_Base {
	protected $sub_conditions = [];
	public $data              = [];
	public $cpt               = [];

	/**
	 * Conditions_Base constructor.
	 *
	 * @param array $data condition required data like post_type data.
	 * @since 3.7.0
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function __construct( array $data = [] ) {
		$this->cpt = \JupiterX_Popups_Conditions_Manager::get_supported_cpt();

		$this->get_sub_conditions();
	}

	/**
	 * Get unique name for conditions.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_unique_name() {
		return 'condition_' . $this->get_name();
	}

	/**
	 * Get condition sub conditions.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_sub_conditions() {
		return $this->sub_conditions;
	}

	/**
	 * Get condition name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get condition type.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	abstract public function get_type();

	/**
	 * Get condition label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	abstract public function get_label();

	/**
	 * Get conditions priority.
	 *
	 * @since 3.7.0
	 * @return int
	 */
	public static function get_priority() {
		return 100;
	}

	/**
	 * Get condition all label (for condition with group conditions).
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_all_label() {
		return $this->get_label();
	}

	/**
	 * Set condition config.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_data() {
		$this->data['label']          = $this->get_label();
		$this->data['sub_conditions'] = $this->get_sub_conditions();
		$this->data['all_label']      = $this->get_all_label();

		if ( method_exists( $this, 'get_options' ) ) {
			$this->data['sub_id'] = 'true';
		}

		return $this->data;
	}

	/**
	 * Register sub condition.
	 *
	 * @param object $condition object of condition class.
	 * @since 3.7.0
	 * @return array
	 */
	public function register_condition( $condition ) {
		if ( in_array( $condition->get_name(), $this->sub_conditions, true ) ) {
			return $this->sub_conditions;
		}

		$this->sub_conditions = array_merge( $this->sub_conditions, [ $condition->get_name() ] );

		return $this->sub_conditions;
	}

	/**
	 * Validate condition in frontend.
	 *
	 * @param array $args condition saved arguments to to validate.
	 * @since 3.7.0
	 * @return boolean
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function is_valid( $args ) {
		return false;
	}
}
