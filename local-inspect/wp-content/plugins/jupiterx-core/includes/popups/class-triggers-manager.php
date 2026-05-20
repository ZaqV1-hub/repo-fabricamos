<?php
/**
 * Helper class for jupiterx popup triggers.
 *
 * @package JupiterX_Core\Post_Type
 * @since 3.7.0
 */

defined( 'ABSPATH' ) || die();

/**
 * JupiterX popups helper class for triggers.
 *
 * @since 3.7.0
 * @package JupiterX_Core\Post_Type
 */
class JupiterX_Popups_Triggers_Manager {
	/**
	 * Popup triggers.
	 *
	 * @since 3.7.0
	 */
	public static $triggers = [];

	/**
	 * Popup operators.
	 *
	 * @since 3.7.0
	 */
	public static $operators = [];

	/**
	 * Popup Triggers for control panel.
	 *
	 * @since 3.7.0
	 */
	public static $control_panel = [];

	public function __construct() {
		add_action( 'wp_loaded', [ $this, 'register_triggers' ] );
	}

	/**
	 * Register all triggers.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function register_triggers() {
		if ( ! empty( self::$triggers ) ) {
			return;
		}

		$path = jupiterx_core()->plugin_dir() . 'includes/popups/triggers/';

		$this->register_operators( $path );

		jupiterx_core()->load_files( [
			'popups/triggers/triggers-base',
		] );

		$file_paths = glob( $path . '*.php' );

		foreach ( $file_paths as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				continue;
			}

			require_once $file_path;

			$file_name     = str_replace( '.php', '', basename( $file_path ) );
			$trigger_class = str_replace( '-', ' ', $file_name );
			$trigger_class = str_replace( ' ', '_', ucwords( $trigger_class ) );

			if ( ! class_exists( $trigger_class ) ) {
				$trigger_class = "JupiterX_Core\Popup\Triggers\\{$trigger_class}";
			}

			if ( ! class_exists( $trigger_class ) || 'triggers-base' === $file_name ) {
				continue;
			}

			$trigger = new $trigger_class();

			self::$triggers[ $trigger->get_name() ]                  = $trigger;
			self::$control_panel['triggers'][ $trigger->get_name() ] = $trigger->get_data();
		}

		return self::$triggers;
	}

	/**
	 * Register specific trigger.
	 *
	 * @param string $trigger_name trigger unique name.
	 * @since 3.7.0
	 * @return array
	 */
	public static function register_trigger( $trigger_name ) {
		$condtion_file_name = str_replace( '_', '-', strtolower( $trigger_name ) );
		$trigger_class      = null;

		$trigger_file = $condtion_file_name;

		jupiterx_core()->load_files( [
			"popups/triggers/{$trigger_file}",
		] );

		if ( ! class_exists( $trigger_name ) ) {
			$trigger_class = "JupiterX_Core\Popup\Triggers\\{$trigger_name}";
		}

		$trigger = new $trigger_class();

		return $trigger;
	}

	/**
	 * Register all operators.
	 *
	 * @param string $path Path to triggers folder.
	 * @since 3.7.0
	 * @return array
	 */
	public function register_operators( $path ) {
		if ( ! empty( self::$operators ) ) {
			return;
		}

		$path = $path . 'operators/';

		jupiterx_core()->load_files( [
			'popups/triggers/operators/operator-base',
		] );

		$file_paths = glob( $path . '*.php' );

		foreach ( $file_paths as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				continue;
			}

			require_once $file_path;

			$file_name      = str_replace( '.php', '', basename( $file_path ) );
			$operator_class = str_replace( '-', ' ', $file_name );
			$operator_class = str_replace( ' ', '_', ucwords( $operator_class ) );

			if ( ! class_exists( $operator_class ) ) {
				$operator_class = "JupiterX_Core\Popup\Triggers\Operators\\{$operator_class}";
			}

			if ( ! class_exists( $operator_class ) || 'operator-base' === $file_name ) {
				continue;
			}

			$operator = new $operator_class();

			self::$operators[ $operator->get_name() ]                  = $operator;
			self::$control_panel['operators'][ $operator->get_name() ] = $operator->get_title();
		}

		return self::$operators;
	}
}

new JupiterX_Popups_Triggers_Manager();
