<?php
/**
 * Helper class for jupiterx popup conditions.
 *
 * @package JupiterX_Core\Post_Type
 * @since 3.7.0
 */

use JupiterX_Core\Popup\Conditions\Conditions_Base;

defined( 'ABSPATH' ) || die();

/**
 * JupiterX popups helper class for conditions.
 *
 * @since 3.7.0
 * @package JupiterX_Core\Post_Type
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class JupiterX_Popups_Conditions_Manager {
	/**
	 * Popup conditions.
	 *
	 * @var Conditions_Base[]
	 * @since 3.7.0
	 */
	public static $conditions = [];

	/**
	 * Popup conditions for control panel.
	 *
	 * @var Conditions_Base[]
	 * @since 3.7.0
	 */
	public static $control_panel = [];

	/**
	 * Conditions that will be loaded by other conditions.
	 *
	 * @since 3.7.0
	 */
	const LOADED_CONDITIONS = [
		'conditions-base',
		'post-type-archive',
		'taxonomy',
		'any-child-of-term',
		'child-of-term',
		'post',
		'in-taxonomy',
		'in-sub-term',
		'post-type-by-author',
	];

	public function __construct() {
		add_action( 'wp_loaded', [ $this, 'register_conditions' ] );
	}

	/**
	 * Register all conditions.
	 *
	 * @since 3.7.0
	 * @return array
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function register_conditions() {
		if ( ! empty( self::$conditions ) ) {
			return;
		}

		$path = jupiterx_core()->plugin_dir() . 'includes/popups/conditions/';

		jupiterx_core()->load_files( [
			'popups/conditions/conditions-base',
		] );

		$file_paths = array_filter( glob( $path . '*.php' ), function( $path ) {
			if ( false !== strpos( $path, 'any-child-of-term.php' ) ) {
				return;
			}

			if ( false !== strpos( $path, 'in-sub-term' ) ) {
				return;
			}

			if ( ! class_exists( 'WooCommerce' ) && false !== strpos( $path, 'woo-commerce' ) ) {
				return;
			}

			return $path;
		} );

		foreach ( $file_paths as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				continue;
			}

			require_once $file_path;

			$file_name       = str_replace( '.php', '', basename( $file_path ) );
			$condition_class = str_replace( '-', ' ', $file_name );
			$condition_class = str_replace( ' ', '_', ucwords( $condition_class ) );

			if ( ! class_exists( $condition_class ) ) {
				$condition_class = "JupiterX_Core\Popup\Conditions\\{$condition_class}";
			}

			if ( ! class_exists( $condition_class ) || in_array( $file_name, self::LOADED_CONDITIONS, true ) ) {
				continue;
			}

			$condition = new $condition_class();

			self::$conditions[ $condition->get_name() ]    = $condition;
			self::$control_panel[ $condition->get_name() ] = $condition->get_data();
		}

		return self::$conditions;
	}

	/**
	 * Register specific condition.
	 *
	 * @param string $condition_name condition unique name.
	 * @param array  $args required argument for condition.
	 * @param string $path custom path to load files from.
	 * @since 3.7.0
	 * @return array
	 */
	public static function register_condition( $condition_name, $args, string $path = '' ) {
		$condtion_file_name = str_replace( '_', '-', strtolower( $condition_name ) );
		$condtion_class     = null;

		$condition_file = $condtion_file_name;

		if ( ! empty( $path ) ) {
			$condition_file = $path . '/' . $condtion_file_name;
		}

		jupiterx_core()->load_files( [
			"popups/conditions/{$condition_file}",
		] );

		if ( ! class_exists( $condition_name ) ) {
			$condtion_class = "JupiterX_Core\Popup\Conditions\\{$condition_name}";
		}

		$condition = new $condtion_class( $args );

		self::$conditions[ $condition->get_name() ]    = $condition;
		self::$control_panel[ $condition->get_name() ] = $condition->get_data();

		return $condition;
	}

	/**
	 * Get popup conidtions.
	 *
	 * @param int $popup_id popup id.
	 * @since 3.7.0
	 * @return string
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function get_popup_coditions( $popup_id ) {
		$conditions = get_post_meta( $popup_id, '_jupiterx_popup_conditions', true );

		if ( empty( $conditions ) ) {
			return [ esc_html__( 'None', 'jupiterx-core' ) ];
		}

		$result = [];

		foreach ( $conditions as $condition ) {
			if ( 'exclude' === $condition['type'] ) {
				continue;
			}

			$condition_name = ! empty( $condition['sub_name'] ) ? $condition['sub_name'] : $condition['name'];
			$condition_data = $this->get_condition( $condition_name );

			if ( empty( $condition_data ) ) {
				continue;
			}

			$instance_label = '';

			if ( ! empty( $condition['sub_id'] ) && is_array( $condition['sub_id'] ) ) {
				$instance_label = $condition_data->get_label() . " #{$condition['sub_id']['value']}";
			}

			if ( isset( $condition['sub_id'] ) && 'all' === $condition['sub_id'] ) {
				$instance_label = $condition_data->get_label();
			}

			if ( empty( $instance_label ) ) {
				$instance_label = $condition_data->get_all_label();
			}

			$result[ $condition_data->get_name() ] = $instance_label;
		}

		return $result;
	}

	/**
	 * Get condition data.
	 *
	 * @param string $id condition name.
	 * @since 3.7.0
	 * @return array|boolean
	 */
	public function get_condition( $id ) {
		return isset( self::$conditions[ $id ] ) ? self::$conditions[ $id ] : false;
	}

	/**
	 * Handle conditions for frontend.
	 *
	 * @param array $templates list of popup templates.
	 * @since 3.7.0
	 * @return array
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function handle_conditions( $templates ) {
		$conditions_priority = [];
		$excludes            = [];

		foreach ( $templates as $template ) {
			$conditions   = get_post_meta( $template->ID, '_jupiterx_popup_conditions', true );
			$popup_status = get_post_status( $template->ID );

			if ( empty( $conditions ) && 'publish' === $popup_status ) {
				continue;
			}

			if ( empty( $conditions ) ) {
				continue;
			}

			foreach ( $conditions as $condition ) {
				$type     = ! empty( $condition['type'] ) ? $condition['type'] : '';
				$name     = ! empty( $condition['name'] ) ? $condition['name'] : '';
				$sub_name = ! empty( $condition['sub_name'] ) ? $condition['sub_name'] : '';
				$sub_id   = ! empty( $condition['sub_id'] ) && 'all' !== $condition['sub_id'] ? $condition['sub_id'] : '';

				$is_include = 'include' === $type;

				$condition_instance = $this->get_condition( $name );

				if ( ! $condition_instance ) {
					continue;
				}

				$condition_pass         = $condition_instance->is_valid( [] );
				$sub_condition_instance = null;

				if ( $condition_pass && $sub_name ) {
					$sub_condition_instance = $this->get_condition( $sub_name );

					if ( ! $sub_condition_instance ) {
						continue;
					}

					$args = [
						'id' => $sub_id,
					];

					$condition_pass = $sub_condition_instance->is_valid( $args );
				}

				if ( $condition_pass ) {
					$post_status = get_post_status( $template->ID );

					if ( 'publish' !== $post_status ) {
						continue;
					}

					if ( $is_include ) {
						$conditions_priority[  $template->ID ] = $this->get_condition_priority( $condition_instance, $sub_condition_instance, $sub_id );
					}

					if ( ! $is_include ) {
						$excludes[] = $template->ID;
					}
				}
			}
		}

		foreach ( $excludes as $exclude_id ) {
			unset( $conditions_priority[ $exclude_id ] );
		}

		asort( $conditions_priority );

		return $conditions_priority;
	}

	/**
	 * Handle conditions priority.
	 *
	 * @param object     $condition_instance instance of the condition.
	 * @param object     $sub_condition_instance instance of the sub condition.
	 * @param null|array $sub_id condition sub id.
	 * @since 3.7.0
	 * @return number
	 */
	private function get_condition_priority( $condition_instance, $sub_condition_instance, $sub_id ) {
		$priority = $condition_instance::get_priority();

		if ( $sub_condition_instance ) {
			if ( $sub_condition_instance::get_priority() < $priority ) {
				$priority = $sub_condition_instance::get_priority();
			}

			$priority -= 10;

			if ( $sub_id ) {
				$priority -= 10;
			}

			if ( ! $sub_id && 0 === count( $sub_condition_instance->get_sub_conditions() ) ) {
				$priority -= 5;
			}
		}

		return $priority;
	}

	/**
	 * Get valid custom post types.
	 *
	 * @since 3.7.0
	 * @return array.
	 */
	public static function get_supported_cpt() {
		$args = [
			'exclude_from_search' => false,
		];

		$post_type_args = [
			// Default is the value $public.
			'show_in_nav_menus' => true,
		];

		// Keep for backwards compatibility
		if ( ! empty( $args['post_type'] ) ) {
			$post_type_args['name'] = $args['post_type'];
			unset( $args['post_type'] );
		}

		$post_type_args = wp_parse_args( $post_type_args, $args );

		$_post_types = get_post_types( $post_type_args, 'objects' );

		$post_types = [];

		foreach ( $_post_types as $post_type => $object ) {
			$post_types[ $post_type ] = $object->label;
		}

		if ( class_exists( 'WooCommerce' ) && isset( $post_types['product'] ) ) {
			unset( $post_types['product'] );
		}

		return $post_types;
	}

	/**
	 * Check is product search page.
	 *
	 * @since 3.7.0
	 * @return boolean.
	 */
	public static function is_product_search_page() {
		return is_search() && 'product' === get_query_var( 'post_type' );
	}
}

new JupiterX_Popups_Conditions_Manager();
