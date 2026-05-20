<?php
/**
 * Add Popup document type.
 *
 * @package JupiterX_Core\Raven
 * @since 3.7.0
 */

namespace JupiterX_Core\Raven\Core\Document_Types\Type;

use Elementor\Core\Base\Document as Document_Base;
use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Core\DynamicTags\Dynamic_CSS;

defined( 'ABSPATH' ) || die();

class Jupiterx_Popup_Document extends Document_Base {
	public function __construct( array $data = [] ) {
		parent::__construct( $data );

		$this->add_actions();
	}

	protected function add_actions() {
		add_action( 'elementor/element/parse_css', [ $this, 'add_post_css' ], 10, 2 );
	}

	public function get_name() {
		return 'jupiterx-popups';
	}

	public static function get_type() {
		return 'jupiterx-popups';
	}

	public static function get_title() {
		return esc_html__( 'JupiterX Popup', 'jupiterx-core' );
	}

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['admin_tab_group'] = '';
		$properties['support_kit']     = true;

		return $properties;
	}

	public function add_post_css( $post_css, $element ) {
		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}

		$element_settings = $element->get_settings();

		if ( empty( $element_settings['raven_custom_css_widget'] ) ) {
			return;
		}

		$css = trim( $element_settings['raven_custom_css_widget'] );

		if ( empty( $css ) ) {
			return;
		}

		$css = str_replace(
			'selector',
			$post_css->get_element_unique_selector( $element ),
			$css
		);
		$css = sprintf(
			'/* Start custom CSS for %s, class: %s */',
			$element->get_name(),
			$element->get_unique_selector()
		) . $css . '/* End custom CSS */';

		$post_css->get_stylesheet()->add_raw_css( $css );
	}

	/**
	 *@since 3.7.0
	 *
	 * @todo We should test advanced functionality in next task.
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function register_controls() {
		parent::register_controls();

		$popup_id = '#' . $this->get_unique_name();

		$this->start_controls_section(
			'popup_layout',
			[
				'label' => esc_html__( 'Layout', 'jupiterx-core' ),
				'tab' => Controls_Manager::TAB_SETTINGS,
			]
		);

		$this->add_responsive_control(
			'popup_width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vw' ],
				'default' => [
					'size' => 640,
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__container' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'height_type',
			[
				'label' => esc_html__( 'Height', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto' => esc_html__( 'Fit To Content', 'jupiterx-core' ),
					'fit_to_screen' => esc_html__( 'Fit To Screen', 'jupiterx-core' ),
					'custom' => esc_html__( 'Custom', 'jupiterx-core' ),
				],
				'selectors_dictionary' => [
					'fit_to_screen' => '100vh',
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__container .jupiterx-popup__container-inner' => 'height: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => esc_html__( 'Custom Height', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vh' ],
				'condition' => [
					'height_type' => 'custom',
				],
				'default' => [
					'size' => 380,
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__container .jupiterx-popup__container-inner' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_position',
			[
				'label' => esc_html__( 'Content Position', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => [
					'top' => esc_html__( 'Top', 'jupiterx-core' ),
					'center' => esc_html__( 'Center', 'jupiterx-core' ),
					'bottom' => esc_html__( 'Bottom', 'jupiterx-core' ),
				],
				'condition' => [
					'height_type!' => 'auto',
				],
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'center' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__container .jupiterx-popup__container-inner' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'position_heading',
			[
				'label' => esc_html__( 'Position', 'jupiterx-core' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'horizontal_position',
			[
				'label' => esc_html__( 'Horizontal', 'jupiterx-core' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__inner' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left' => 'flex-start',
					'right' => 'flex-end',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_position',
			[
				'label' => esc_html__( 'Vertical', 'jupiterx-core' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'jupiterx-core' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'jupiterx-core' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__inner' => 'align-items: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'bottom' => 'flex-end',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'convert_to_header_toolbar',
			[
				'label' => esc_html__( 'Convert to Header Toolbar', 'jupiterx-core' ),
				'description' => esc_html__( 'Transforms the popup into a fixed header toolbar, causing the entire page content to shift downward, preventing any overlap with the header.', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'default' => 'no',
				'frontend_available' => true,
				'condition' => [
					'vertical_position' => 'top',
				],
			]
		);

		$this->add_control(
			'overlay',
			[
				'label' => esc_html__( 'Overlay', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'default' => 'yes',
				'selectors' => [
					$popup_id . ' .jupiterx-popup__container-overlay ' => 'display: block',
					$popup_id . ' .jupiterx-popup__overlay' => 'display: block; width: 100%; height: 100%;',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'close_button',
			[
				'label' => esc_html__( 'Close Button', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'jupiterx-core' ),
				'label_on' => esc_html__( 'Show', 'jupiterx-core' ),
				'default' => 'yes',
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button' => 'display: flex;',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'entrance_animation',
			[
				'label' => esc_html__( 'Entrance Animation', 'jupiterx-core' ),
				'type' => Controls_Manager::ANIMATION,
				'frontend_available' => true,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'exit_animation',
			[
				'label' => esc_html__( 'Exit Animation', 'jupiterx-core' ),
				'type' => Controls_Manager::EXIT_ANIMATION,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'animation_duration',
			[
				'label' => esc_html__( 'Animation Duration', 'jupiterx-core' ) . ' (sec)',
				'type' => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'default' => [
					'size' => 1.2,
				],
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 5,
						'step' => 0.1,
					],
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__container' => 'animation-duration: {{SIZE}}s !important',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'entrance_animation',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'exit_animation',
							'operator' => '!==',
							'value' => '',
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_page_style',
			[
				'label' => esc_html__( 'Popup', 'jupiterx-core' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'  => 'background',
				'selector' => $popup_id . ' .jupiterx-popup__container-inner',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'  => 'border',
				'selector' => $popup_id . ' .jupiterx-popup__container-inner',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__container-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'popup_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__container-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__container-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => $popup_id . ' .jupiterx-popup__container-inner',
				'fields_options' => [
					'box_shadow_type' => [
						'default' => 'yes',
					],
					'box_shadow' => [
						'default' => [
							'horizontal' => 2,
							'vertical' => 8,
							'blur' => 23,
							'spread' => 3,
							'color' => 'rgba(0,0,0,0.2)',
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_close_button',
			[
				'label' => esc_html__( 'Close Button', 'jupiterx-core' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'close_button!' => '',
				],
			]
		);

		$this->add_control(
			'popover-toggle',
			[
				'label' => esc_html__( 'Position', 'jupiterx-core' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => esc_html__( 'Default', 'jupiterx-core' ),
				'label_on' => esc_html__( 'Custom', 'jupiterx-core' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'close_button_horizontal',
			[
				'label' => esc_html__( 'Horizontal Offset', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 300,
						'min' => -300,
					],
				],
				'selectors' => [
					'body:not(.rtl) ' . $popup_id . ' .jupiterx-popup__close-button' => 'right: {{SIZE}}{{UNIT}}',
					'body.rtl ' . $popup_id . ' .jupiterx-popup__close-button' => 'left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_vertical',
			[
				'label' => esc_html__( 'Vertical Offset', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 300,
						'min' => -300,
					],
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button' => 'top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_popover();

		$this->start_controls_tabs( 'close_button_style_tabs' );

		$this->start_controls_tab(
			'tab_x_button_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'close_button_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name'      => 'close_button_background_type',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => $popup_id . ' .jupiterx-popup__close-button',
			]
		);

		$this->add_responsive_control(
			'close_button_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', 'rem' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_box_size',
			[
				'label' => esc_html__( 'Box Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'close_button_border_type',
			[
				'label' => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type' => 'select',
				'options' => [
					'none' => esc_html__( 'None', 'jupiterx-core' ),
					'solid' => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'default' => 'none',
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_border_width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'vw' ],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'close_button_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'close_button_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'close_button_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_x_button_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'close_button_hover_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'background',
			[
				'name'      => 'close_button_hover_background_type',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => $popup_id . ' .jupiterx-popup__close-button:hover',
			]
		);

		$this->add_responsive_control(
			'close_button_hover_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', 'rem' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button:hover' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_hover_box_size',
			[
				'label' => esc_html__( 'Box Size', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button:hover' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'close_button_hover_border_type',
			[
				'label' => esc_html__( 'Border Type', 'jupiterx-core' ),
				'type' => 'select',
				'options' => [
					'none' => esc_html__( 'None', 'jupiterx-core' ),
					'solid' => esc_html__( 'Solid', 'jupiterx-core' ),
					'double' => esc_html__( 'Double', 'jupiterx-core' ),
					'dotted' => esc_html__( 'Dotted', 'jupiterx-core' ),
					'dashed' => esc_html__( 'Dashed', 'jupiterx-core' ),
					'groove' => esc_html__( 'Groove', 'jupiterx-core' ),
				],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button:hover' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_hover_border_width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'vw' ],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button:hover' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'close_button_hover_border_type!' => [ 'none', '' ],
				],
			]
		);

		$this->add_control(
			'close_button_hover_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'close_button_hover_border_type!' => [ 'none', '' ],
				],
			]
		);

		$this->add_responsive_control(
			'close_button_hover_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'close_button_box_shadow_switcher',
			[
				'label' => esc_html__( 'Box Shadow', 'jupiterx-core' ),
				'type' => 'switcher',
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'selectors' => [
					$popup_id . ' .jupiterx-popup__close-button' => 'content: ""',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'close_button_box_shadow',
				'selector' => $popup_id . ' .jupiterx-popup__close-button',
				'fields_options' => [
					'box_shadow_type' => [
						'default' => 'yes',
					],
					'box_shadow' => [
						'default' => [
							'horizontal' => 2,
							'vertical' => 8,
							'blur' => 23,
							'spread' => 3,
							'color' => 'rgba(0,0,0,0.2)',
						],
					],
				],
				'condition' => [
					'close_button_box_shadow_switcher' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_overlay',
			[
				'label' => esc_html__( 'Overlay', 'jupiterx-core' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'overlay' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'overlay_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => $popup_id . ' .jupiterx-popup__overlay',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => 'rgba(0,0,0,.8)',
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_advanced',
			[
				'label' => esc_html__( 'Advanced', 'jupiterx-core' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'prevent_close_on_background_click',
			[
				'label' => esc_html__( 'Prevent Closing on Overlay', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'prevent_close_on_esc_key',
			[
				'label' => esc_html__( 'Prevent Closing on ESC Key', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'prevent_scrolling',
			[
				'label' => esc_html__( 'Disable Page Scrolling', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_once',
			[
				'label' => esc_html__( 'Show Once', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_again_delay',
			[
				'label' => esc_html__( 'Repeat Showing Popup in', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__( 'Set the timeout caching and a popup will be displayed again', 'jupiterx-core' ),
				'options' => [
					'1_minute' => esc_html__( '1 Minute', 'jupiterx-core' ),
					'10_minute' => esc_html__( '10 Minutes', 'jupiterx-core' ),
					'30_minute' => esc_html__( '30 Minutes', 'jupiterx-core' ),
					'1_hour' => esc_html__( '1 Hour', 'jupiterx-core' ),
					'3_hour' => esc_html__( '3 Hours', 'jupiterx-core' ),
					'6_hour' => esc_html__( '6 Hours', 'jupiterx-core' ),
					'12_hour' => esc_html__( '12 Hours', 'jupiterx-core' ),
					'1_day' => esc_html__( '1 Day', 'jupiterx-core' ),
					'3_day' => esc_html__( '3 Days', 'jupiterx-core' ),
					'1_week' => esc_html__( '1 Week', 'jupiterx-core' ),
					'1_month' => esc_html__( '1 Month', 'jupiterx-core' ),
					'3_month' => esc_html__( '3 Months', 'jupiterx-core' ),
					'6_month' => esc_html__( '6 Months', 'jupiterx-core' ),
				],
				'condition' => [
					'show_once' => 'yes',
				],
			]
		);

		$this->add_control(
			'avoid_multiple_popups',
			[
				'label' => esc_html__( 'Avoid Multiple Popups', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'If the user has seen another popup on the page hide this popup', 'jupiterx-core' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'use_ajax',
			[
				'label' => esc_html__( 'Load Popup Content with Ajax', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'force_loading',
			[
				'label' => esc_html__( 'Force Loading', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Force Loading every time you open the popup', 'jupiterx-core' ),
				'frontend_available' => true,
				'condition' => [
					'use_ajax' => 'yes',
				],
			]
		);

		$this->add_control(
			'close_button_delay',
			[
				'label' => esc_html__( 'Show Close Button After', 'jupiterx-core' ) . ' (sec)',
				'type' => Controls_Manager::NUMBER,
				'min' => 0.1,
				'max' => 60,
				'step' => 0.1,
				'condition' => [
					'close_button' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'close_automatically',
			[
				'label' => esc_html__( 'Automatically Close After', 'jupiterx-core' ) . ' (sec)',
				'type' => Controls_Manager::NUMBER,
				'min' => 0.1,
				'max' => 60,
				'step' => 0.1,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'custom_selector',
			[
				'label' => esc_html__( 'Open By Selector', 'jupiterx-core' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( '#id, .class', 'jupiterx-core' ),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'classes',
			[
				'label' => esc_html__( 'CSS Classes', 'jupiterx-core' ),
				'type' => Controls_Manager::TEXT,
				'title' => esc_html__( 'Add your custom class WITHOUT the dot. e.g: my-class', 'jupiterx-core' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'z_index',
			[
				'label' => esc_html__( 'Z-Index', 'jupiterx-core' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1000,
				'frontend_available' => true,
				'selectors' => [
					$popup_id => 'z-index: {{VALUE}}',
				],
				'description' => esc_html__( 'Z-Index should be equal to or greater than 1000.', 'jupiterx-core' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_custom_css',
			[
				'label' => esc_html__( 'Custom CSS', 'jupiterx-core' ),
				'tab' => Controls_Manager::TAB_SETTINGS,
			]
		);

		$this->add_control(
			'custom_css_title',
			[
				'raw' => esc_html__( 'Add your own custom CSS here', 'jupiterx-core' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);

		$this->add_control(
			'raven_custom_css_widget',
			[
				'type' => Controls_Manager::CODE,
				'label' => esc_html__( 'Custom CSS', 'jupiterx-core' ),
				'language' => 'css',
				'render_type' => 'ui',
				'show_label' => false,
				'separator' => 'none',
			]
		);

		$this->add_control(
			'custom_css_description',
			[
				'raw' => sprintf(
					/* translators: 1: Break line tag. */
					esc_html__( 'Use "selector" to target wrapper element. Examples:%1$sselector {color: red;} // For main element%1$sselector .child-element {margin: 10px;} // For child element%1$s.my-class {text-align: center;} // Or use any custom selector', 'jupiterx-core' ),
					'<br>'
				),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->end_controls_section();
	}
}
