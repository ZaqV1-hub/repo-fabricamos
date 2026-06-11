<?php
/**
 * Add Form popup action.
 *
 * @package JupiterX_Core\Raven
 * @since 4.3.0
 */

namespace JupiterX_Core\Raven\Modules\Forms\Actions;

use Elementor\Controls_Manager;
use JupiterX_Core\Raven\Controls\Query;

defined( 'ABSPATH' ) || die();

/**
 * Popup Action.
 *
 * Initializing the popup action by extending action base.
 *
 * @since 4.3.0
 */
class Popup extends Action_Base {

	/**
	 * Get name.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function get_name() {
		return 'popup';
	}

	/**
	 * Get title.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function get_title() {
		return esc_html__( 'Popup', 'jupiterx-core' );
	}

	/**
	 * Is private.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function is_private() {
		return false;
	}

	/**
	 * Update controls.
	 *
	 * Add popup setting section.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param object $widget Widget instance.
	 */
	public function update_controls( $widget ) {

		$widget->start_controls_section(
			'section_popup',
			[
				'label' => esc_html__( 'Popup', 'jupiterx-core' ),
				'condition' => [
					'actions' => 'popup',
				],
			]
		);

		$widget->add_control(
			'popup_action',
			[
				'label' => esc_html__( 'Action', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'frontend_available' => true,
				'options' => [
					'' => esc_html__( 'Choose', 'jupiterx-core' ),
					'open' => esc_html__( 'Open Popup', 'jupiterx-core' ),
					'close' => esc_html__( 'Close Popup', 'jupiterx-core' ),
				],
			]
		);

		$widget->add_control(
			'popup_action_popup_id',
			[
				'label'       => esc_html__( 'Popup', 'jupiterx-core' ),
				'type'        => 'raven_query',
				'options'     => [],
				'label_block' => true,
				'multiple'    => false,
				'query'       => [
					'source'    => Query::QUERY_SOURCE_POST,
					'post_type' => 'jupiterx-popups',
					'post_status' => 'publish',
				],
				'default'     => false,
				'condition' => [
					'popup_action' => 'open',
				],
			]
		);

		$widget->add_control(
			'popup_action_do_not_show_again',
			[
				'label' => esc_html__( 'Don\'t Show Again', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => [
					'popup_action' => 'close',
				],
			]
		);

		$widget->end_controls_section();
	}

	/**
	 * Run action.
	 *
	 * Add popup to the response.
	 *
	 * @since 4.3.0
	 * @access public
	 * @static
	 *
	 * @param object $ajax_handler Ajax handler instance.
	 */
	public static function run( $ajax_handler ) {
		$popup_settings = $ajax_handler->form['settings']['popup_action'];
		$popup_id       = $ajax_handler->form['settings']['popup_action_popup_id'];

		if ( 'open' === $popup_settings ) {
			return self::handle_open_popup( $ajax_handler, $popup_id );
		}

		return $ajax_handler->add_response( 'popup', [
			'type' => 'close',
			'constantly' => $ajax_handler->form['settings']['popup_action_do_not_show_again'],
		] );
	}

	/**
	 * Handle open popup.
	 *
	 * @param object  $ajax_handler Ajax handler instance.
	 * @param integer $popup_id Selected popup id.
	 * @since 4.3.0
	 */
	private static function handle_open_popup( $ajax_handler, $popup_id ) {
		if ( ! empty( $popup_id ) ) {
			return $ajax_handler->add_response( 'popup', [
				'type' => 'open',
				'popupId' => $popup_id,
			] );
		}

		$admin_error = esc_html__( 'Popup is not set.', 'jupiterx-core' );

		return $ajax_handler->add_response( 'admin_errors', $admin_error );
	}
}
