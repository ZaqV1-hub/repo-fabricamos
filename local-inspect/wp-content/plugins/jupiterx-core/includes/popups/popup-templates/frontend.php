<?php
namespace JupiterX_Core\Popup\Templates;

use JupiterX_Popups_Triggers_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * JupiterX popup frontend.
 *
 * @todo We have to get data from settings
 * so we also need update the html codes as well.
 *
 * @since 3.7.0
 */
class Frontend extends Jupiterx_Popup_Template_Base {
	public $popup_default_settings = [
		'custom_selector'                   => '',
		'prevent_scrolling'                 => false,
		'show_once'                         => false,
		'show_again_delay'                  => 'none',
		'use_ajax'                          => false,
		'prevent_close_on_background_click' => false,
		'prevent_close_on_esc_key'          => false,
		'avoid_multiple_popups'             => false,
		'force_loading'                     => false,
		'ajax_url'                          => '',
		'close_button'                      => true,
		'close_button_delay'                => '',
		'close_automatically'               => '',
		'classes'                           => '',
		'entrance_animation'                => '',
		'exit_animation'                    => '',
		'animation_duration'                => '',
		'browser_language'                  => '',
		'convert_to_header_toolbar'         => false,
		'vertical_position'                 => '',
	];

	public $popup_show_again_default = [
		'minute' => MINUTE_IN_SECONDS,
		'hour' => HOUR_IN_SECONDS,
		'day' => DAY_IN_SECONDS,
		'week' => WEEK_IN_SECONDS,
		'month' => MONTH_IN_SECONDS,
	];

	public $ajax_popup_id_list = [];


	/**
	 * Get popup classes.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_classes() {
		$classes = [
			'jupiterx-popup',
			'jupiterx-popup--front-mode',
			'jupiterx-popup--hide-state',
		];

		$classes = apply_filters( 'jupiterx-core/frontend-popup/wrappers', $classes );

		if ( ! empty( $classes ) ) {
			$classes = implode( ' ', $classes );
		}

		return $classes;
	}

	/**
	 * Get popup content.
	 *
	 * @since 3.7.0
	 * @return String
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function get_content() {
		$data = $this->data;

		$translated_id = apply_filters( 'wpml_object_id', $data['id'], 'post', true );

		$popup_language_code = apply_filters(
			'wpml_element_language_code',
			null,
			[
				'element_id' => $translated_id,
				'element_type' => 'jupiterx-popups',
			]
		);

		$current_lang = apply_filters( 'wpml_current_language', null );
		if ( $popup_language_code !== $current_lang ) {
			return;
		}

		$classes          = $this->get_classes();
		$data['uniqe_id'] = 'jupiterx-popups-' . $translated_id;

		$meta_settings       = get_post_meta( $data['id'], '_elementor_page_settings', true );
		$popup_settings_main = wp_parse_args( $meta_settings, $this->popup_default_settings );
		$show_again_delay    = $this->get_milliseconds_by_tag( $popup_settings_main['show_again_delay'] );
		$browser_language    = $this->get_browser_language();
		$trigger_settings    = get_post_meta( $data['id'], '_jupiterx_popup_triggers', true );
		$triggers            = [];

		if ( ! empty( $trigger_settings ) && is_array( $trigger_settings ) ) {
			foreach ( $trigger_settings as $trigger ) {
				if ( empty( $trigger['control'] ) && 'on_page_exit_intent' !== $trigger['name'] && 'on_page_load' !== $trigger['name'] ) {
					continue;
				}

				$triggers[ $trigger['name'] ] = [
					'control' => empty( $trigger['control'] ) ? '' : $trigger['control'],
					'operator' => empty( $trigger['operator'] ) ? '' : $trigger['operator'],
				];
			}
		}

		$popup_json = [
			'id' => $data['id'],
			'jupiterx_popup_id'                 => 'jupiterx-popup-' . $data['id'],
			'custom_selector'                   => $popup_settings_main['custom_selector'],
			'prevent_scrolling'                 => filter_var( $popup_settings_main['prevent_scrolling'], FILTER_VALIDATE_BOOLEAN ),
			'show_once'                         => filter_var( $popup_settings_main['show_once'], FILTER_VALIDATE_BOOLEAN ),
			'show_again_delay'                  => $show_again_delay,
			'use_ajax'                          => filter_var( $popup_settings_main['use_ajax'], FILTER_VALIDATE_BOOLEAN ),
			'force_loading'                     => filter_var( $popup_settings_main['force_loading'], FILTER_VALIDATE_BOOLEAN ),
			'ajax_url'                          => esc_url( admin_url( 'admin-ajax.php' ) ),
			'prevent_close_on_background_click' => filter_var( $popup_settings_main['prevent_close_on_background_click'], FILTER_VALIDATE_BOOLEAN ),
			'prevent_close_on_esc_key'          => filter_var( $popup_settings_main['prevent_close_on_esc_key'], FILTER_VALIDATE_BOOLEAN ),
			'avoid_multiple_popups'             => filter_var( $popup_settings_main['avoid_multiple_popups'], FILTER_VALIDATE_BOOLEAN ),
			'close_button'                      => $popup_settings_main['close_button'],
			'close_button_delay'                => $popup_settings_main['close_button_delay'],
			'close_automatically'               => $popup_settings_main['close_automatically'],
			'entrance_animation'                => $popup_settings_main['entrance_animation'],
			'exit_animation'                    => $popup_settings_main['exit_animation'],
			'animation_duration'                => $popup_settings_main['animation_duration'],
			'browser_language'                  => $browser_language,
			'convert_to_header_toolbar'         => filter_var( $popup_settings_main['convert_to_header_toolbar'], FILTER_VALIDATE_BOOLEAN ),
			'vertical_position'                 => $popup_settings_main['vertical_position'],
		];

		if ( filter_var( $popup_settings_main['use_ajax'], FILTER_VALIDATE_BOOLEAN ) ) {
			$this->ajax_popup_id_list[] = $data['id'];
		}

		// Add custom class.
		if ( ! empty( $popup_settings_main['classes'] ) ) {
			$classes .= ' ' . $popup_settings_main['classes'];
		}

		// Add animation classes.
		$animation_classes = '';
		if ( ! empty( $popup_settings_main['entrance_animation'] ) && ! $popup_settings_main['convert_to_header_toolbar'] ) {
			$animation_classes = $popup_settings_main['entrance_animation'] . ' animated';
		}

		// User role.
		if ( ! empty( $triggers['user_role'] ) ) {
			$user_role                       = JupiterX_Popups_Triggers_Manager::register_trigger( 'User_Role' );
			$triggers['user_role']['result'] = $user_role->is_valid( $triggers );
		}

		// User type.
		if ( ! empty( $triggers['user_type'] ) ) {
			$user_type                       = JupiterX_Popups_Triggers_Manager::register_trigger( 'User_Type' );
			$triggers['user_type']['result'] = $user_type->is_valid( $triggers );
		}

		$popup_json_data = htmlspecialchars( wp_json_encode( $popup_json ) );
		$triggers        = htmlspecialchars( wp_json_encode( $triggers ) );

		do_action( 'jupiterx-core/frontend-popup/before-render-popup', $data['id'] );

		ob_start();
		?>
		<div
			id="<?php echo esc_attr( $data['uniqe_id'] ); ?>"
			class="<?php echo esc_attr( $classes ); ?>"
			data-settings="<?php echo $popup_json_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
			data-trigger="<?php echo $triggers; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
		>
			<div class="jupiterx-popup__inner">
				<div class="jupiterx-popup__overlay"></div>
				<div class="jupiterx-popup__container <?php echo esc_attr( $animation_classes ); ?>">
					<div class="jupiterx-popup__close-button">&times;</div>
					<div class="jupiterx-popup__container-inner">
						<div class="jupiterx-popup__container-overlay"></div>
						<div class="jupiterx-popup__container-content">
							<?php
								do_action( 'jupiterx-core/frontend-popup/before-content', $data['id'] );

								if ( ! filter_var( $popup_settings_main['use_ajax'], FILTER_VALIDATE_BOOLEAN ) ) {
									$plugin  = \Elementor\Plugin::instance();
									$content = $plugin->frontend->get_builder_content_for_display( $translated_id, false );

									echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}

								do_action( 'jupiterx-core/frontend-popup/after-content', $data['id'] );
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php

		do_action( 'jupiterx-core/frontend-popup/after-render-popup', $data['id'] );

		return ob_get_clean();
	}

	/**
	 * Calculate actual delay.
	 *
	 * @since 3.7.0
	 * @return int
	 */
	public function get_milliseconds_by_tag( $tag = 'none' ) {
		if ( 'none' === $tag ) {
			return 0;
		}

		$tag_array = explode( '_', $tag );

		return (int) $tag_array[0] * $this->popup_show_again_default[ $tag_array[1] ] * 1000;
	}

	/**
	 * Update browser language.
	 *
	 * @since 3.7.0
	 */
	public function get_browser_language() {
		$data = [];

		if ( ! empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			$data = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ); // phpcs:ignore
		}

		$language = ! empty( $data[0] ) ? $data[0] : '';

		if ( empty( $language ) ) {
			return '';
		}

		return $language;
	}
}
