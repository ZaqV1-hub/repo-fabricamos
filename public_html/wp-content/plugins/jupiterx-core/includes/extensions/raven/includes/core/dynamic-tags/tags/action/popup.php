<?php
namespace JupiterX_Core\Raven\Core\Dynamic_Tags\Tags\Action;

use Elementor\Core\DynamicTags\Tag;
use JupiterX_Core\Raven\Controls\Query as Control_Query;
use Elementor\Core\Base\Document;
use Elementor\Controls_Manager;
use JupiterX_Popups;

defined( 'ABSPATH' ) || die();

class Popup extends Tag {

	public function get_name() {
		return 'raven-popup';
	}

	public function get_title() {
		return esc_html__( 'JupiterX Popup', 'jupiterx-core' );
	}

	public function get_group() {
		return 'action';
	}

	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::URL_CATEGORY ];
	}

	public function register_controls() {
		$this->add_control(
			'raven_action',
			[
				'label' => esc_html__( 'Action', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'open',
				'options' => [
					'open' => esc_html__( 'Open Popup', 'jupiterx-core' ),
					'close' => esc_html__( 'Close Popup', 'jupiterx-core' ),
					'toggle' => esc_html__( 'Toggle Popup', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'raven_popup',
			[
				'label' => esc_html__( 'Popup', 'jupiterx-core' ),
				'type' => 'raven_query',
				'query' => [
					'source'    => Control_Query::QUERY_SOURCE_POST,
					'post_type' => 'jupiterx-popups',
					'post_status' => 'publish',
				],
				'label_block' => true,
				'condition' => [
					'raven_action' => [ 'open', 'toggle' ],
				],
			]
		);

		$this->add_control(
			'raven_do_not_show_again',
			[
				'label' => esc_html__( 'Don\'t Show Again', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'raven_action' => 'close',
				],
			]
		);
	}

	public function render() {
		$settings = $this->get_active_settings();

		if ( 'close' === $settings['raven_action'] ) {
			$this->print_close_popup_link( $settings );

			return;
		}

		$this->print_open_popup_link( $settings );
	}

	// Keep Empty to avoid default advanced section
	protected function register_advanced_section() {}

	private function print_open_popup_link( array $settings ) {
		if ( ! $settings['raven_popup'] ) {
			return;
		}

		$action_key = 'raven_popup_' . $settings['raven_popup'] . ':open';

		$link_action_url = \Elementor\Plugin::$instance->frontend->create_action_hash( $action_key, [
			'id' => $settings['raven_popup'],
			'toggle' => 'toggle' === $settings['raven_action'],
		] );

		$this->add_popup_to_location( $settings['raven_popup'] );

		// PHPCS - `create_action_hash` is safe.
		echo $link_action_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function print_close_popup_link( array $settings ) {
		$is_editor  = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$is_preview = \Elementor\Plugin::$instance->preview->is_preview_mode();

		if ( $is_editor || $is_preview ) {
			return;
		}

		$document = \Elementor\Plugin::$instance->documents->get_current();

		if ( $document ) {
			wp_localize_script(
				'jupiterx-core-raven-frontend',
				'raven_popup_close_action_' . $document->get_main_id(),
				[ 'do_not_show_again' => $settings['raven_do_not_show_again'] ]
			);

			$raven_close_popup_data = [
				'id' => $document->get_main_id(),
				'do_not_show_again' => $settings['raven_do_not_show_again'],
			];

			// PHPCS - create_action_hash is safe.
			echo \Elementor\Plugin::$instance->frontend->create_action_hash( 'raven_popup:close', $raven_close_popup_data );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	private function add_popup_to_location( $popup_id ) {
		$popup_id = intval( $popup_id );

		if ( get_post_status( $popup_id ) !== 'publish' ) {
			return;
		}

		if ( ! wp_style_is( 'jupiterx-popups-frontend', 'enqueued' ) ) {
			wp_enqueue_style( 'jupiterx-popups-frontend' );
		}

		add_action( 'wp_footer', function() use ( $popup_id ) {
			if ( in_array( $popup_id, JupiterX_Popups::$loaded_popups, true ) ) {
				return;
			}

			JupiterX_Popups::$loaded_popups[] = $popup_id;

			( new JupiterX_Popups() )->get_popup_template( 'frontend.php', $popup_id );
		}, 10 );

		add_action( 'jupiterx-core/frontend-popup/after-render-popup', function() use ( $popup_id ) {
			if ( in_array( $popup_id, JupiterX_Popups::$loaded_popups, true ) ) {
				return;
			}

			JupiterX_Popups::$loaded_popups[] = $popup_id;

			( new JupiterX_Popups() )->get_popup_template( 'frontend.php', $popup_id );
		} );
	}
}
