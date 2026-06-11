<?php
/**
 * Class: Jet_Tabs_Widget
 * Name: Tabs
 * Slug: jet-tabs
 */

namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Jet_Tabs\Endpoints\Elementor_Template;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Tabs_Widget extends Jet_Tabs_Base {

	public function get_name() {
		return 'jet-tabs';
	}

	public function get_title() {
		return esc_html__( 'Tabs', 'jet-tabs' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jettabs-tabs-widget-how-to-arrange-the-content-built-with-elementor-inside-the-tabs?utm_source=jettabs&utm_medium=jet-tabs&utm_campaign=need-help';
	}

	public function get_icon() {
		return 'jet-tabs-icon-tabs';
	}

	public function get_categories() {
		return array( 'jet-tabs' );
	}

	protected function register_controls() {
		$css_scheme = apply_filters(
			'jet-tabs/tabs/css-scheme',
			array(
				'instance'        => '> .elementor-widget-container > .jet-tabs',
				'control_wrapper' => '> .elementor-widget-container > .jet-tabs > .jet-tabs__control-wrapper',
				'control'         => '> .elementor-widget-container > .jet-tabs > .jet-tabs__control-wrapper > .jet-tabs__control',
				'content_wrapper' => '> .elementor-widget-container > .jet-tabs > .jet-tabs__content-wrapper',
				'content'         => '> .elementor-widget-container > .jet-tabs > .jet-tabs__content-wrapper > .jet-tabs__content',
				'label'           => '.jet-tabs__label-text',
				'icon'            => '.jet-tabs__label-icon',
				'control_swiper_wrapper' => '> .elementor-widget-container > .jet-tabs > .jet-tabs__control-wrapper > .jet-tabs-swiper-container > .swiper-wrapper',
				'control_swiper'  => '> .elementor-widget-container > .jet-tabs > .jet-tabs__control-wrapper > .jet-tabs-swiper-container > .swiper-wrapper > .jet-tabs__control',
			)
		);

		$this->start_controls_section(
			'section_items_data',
			array(
				'label' => esc_html__( 'Items', 'jet-tabs' ),
			)
		);

		do_action( 'jet-engine-query-gateway/control', $this, 'tabs' );

		$repeater = new Repeater();

		$repeater->add_control(
			'item_active',
			array(
				'label'        => esc_html__( 'Active', 'jet-tabs' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-tabs' ),
				'label_off'    => esc_html__( 'No', 'jet-tabs' ),
				'return_value' => 'yes',
				'default'      => 'false',
			)
		);

		$repeater->add_control(
			'item_use_image',
			array(
				'label'        => esc_html__( 'Use Image?', 'jet-tabs' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-tabs' ),
				'label_off'    => esc_html__( 'No', 'jet-tabs' ),
				'return_value' => 'yes',
				'default'      => 'false',
			)
		);

		$repeater->add_control(
			$this->__new_icon_prefix . 'item_icon',
			array(
				'label'            => esc_html__( 'Icon', 'jet-tabs' ),
				'type'             => Controls_Manager::ICONS,
				'label_block'      => false,
				'skin'             => 'inline',
				'fa4compatibility' => 'item_icon',
				'default'          => array(
					'value'   => 'fas fa-arrow-circle-right',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater->add_control(
			'item_image',
			array(
				'label'   => esc_html__( 'Image', 'jet-tabs' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'item_label',
			array(
				'label'   => esc_html__( 'Label', 'jet-tabs' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'New Tab', 'jet-tabs' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'content_type',
			array(
				'label'       => esc_html__( 'Content Type', 'jet-tabs' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'template',
				'options'     => array(
					'template' => esc_html__( 'Template', 'jet-tabs' ),
					'editor'   => esc_html__( 'Editor', 'jet-tabs' ),
				),
				'label_block' => 'true',
			)
		);

		$repeater->add_control(
			'item_template_id',
			array(
				'label'       => esc_html__( 'Choose Template', 'jet-tabs' ),
				'type'        => 'jet-query',
				'query_type'  => 'elementor_templates',
				'edit_button' => array(
					'active' => true,
					'label'  => esc_html__( 'Edit Template', 'jet-tabs' ),
				),
				'condition'   => array(
					'content_type' => 'template',
				)
			)
		);

		$repeater->add_control(
			'item_editor_content',
			array(
				'label'      => esc_html__( 'Content', 'jet-tabs' ),
				'type'       => Controls_Manager::WYSIWYG,
				'default'    => esc_html__( 'Tab Item Content', 'jet-tabs' ),
				'dynamic' => array(
					'active' => true,
				),
				'condition'   => array(
					'content_type' => 'editor',
				)
			)
		);

		$repeater->add_control(
			'control_id',
			array(
				'label'   => esc_html__( 'Control CSS ID', 'jet-tabs' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'tabs',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'item_label' => esc_html__( 'Tab #1', 'jet-tabs' ),
					),
					array(
						'item_label' => esc_html__( 'Tab #2', 'jet-tabs' ),
					),
					array(
						'item_label' => esc_html__( 'Tab #3', 'jet-tabs' ),
					),
				),
				'title_field' => '{{{ item_label }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings_data',
			array(
				'label' => esc_html__( 'Settings', 'jet-tabs' ),
			)
		);

		$this->add_control(
			'item_html_tag',
			array(
				'label'   => esc_html__( 'Item Label HTML Tag', 'jet-tabs' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_available_item_html_tags(),
				'default' => 'div',
			)
		);

		$this->add_responsive_control(
			'tabs_position',
			array(
				'label'   => esc_html__( 'Tabs Position', 'jet-tabs' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'left'   => esc_html__( 'Left', 'jet-tabs' ),
					'top'    => esc_html__( 'Top', 'jet-tabs' ),
					'right'  => esc_html__( 'Right', 'jet-tabs' ),
					'bottom' => esc_html__( 'Bottom', 'jet-tabs' ),
				),
				'frontend_available' => true,
				'render_type'        => 'template',
			)
		);

		$this->add_control(
			'show_effect',
			array(
				'label'   => esc_html__( 'Show Effect', 'jet-tabs' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'move-up',
				'options' => array(
					'none'             => esc_html__( 'None', 'jet-tabs' ),
					'fade'             => esc_html__( 'Fade', 'jet-tabs' ),
					'zoom-in'          => esc_html__( 'Zoom In', 'jet-tabs' ),
					'zoom-out'         => esc_html__( 'Zoom Out', 'jet-tabs' ),
					'move-up'          => esc_html__( 'Move Up', 'jet-tabs' ),
					'fall-perspective' => esc_html__( 'Fall Perspective', 'jet-tabs' ),
				),
			)
		);

		$this->add_control(
			'tabs_event',
			array(
				'label'   => esc_html__( 'Tabs Event', 'jet-tabs' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'click',
				'options' => array(
					'click' => esc_html__( 'Click', 'jet-tabs' ),
					'hover' => esc_html__( 'Hover', 'jet-tabs' ),
				),
			)
		);

		$this->add_control(
			'auto_switch',
			array(
				'label'        => esc_html__( 'Auto Switch', 'jet-tabs' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'jet-tabs' ),
				'label_off'    => esc_html__( 'Off', 'jet-tabs' ),
				'return_value' => 'yes',
				'default'      => 'false',
			)
		);

		$this->add_control(
			'auto_switch_delay',
			array(
				'label'     => esc_html__( 'Auto Switch Delay', 'jet-tabs' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3000,
				'min'       => 1000,
				'max'       => 20000,
				'step'      => 100,
				'condition' => array(
					'auto_switch' => 'yes',
				),
			)
		);

		$this->add_control(
			'no_active_tabs',
			array(
				'label'              => esc_html__( 'No Active Tabs', 'jet-tabs' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'jet-tabs' ),
				'label_off'          => esc_html__( 'Off', 'jet-tabs' ),
				'return_value'       => 'yes',
				'default'            => 'false',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'ajax_template',
			array(
				'label'        => esc_html__( 'Use Ajax Loading for Template', 'jet-tabs' ),
				'description'  => wp_kses_post(
					sprintf(
						__( 'If you need to use dynamic data inside the template, please switch <a href="%s" target="_blank" rel="noopener noreferrer">Ajax Request Type</a> to Self.', 'jet-tabs' ),
						esc_url( admin_url( 'admin.php?page=jet-dashboard-settings-page&subpage=jet-tabs-general-settings' ) )
					)
				),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'jet-tabs' ),
				'label_off'    => esc_html__( 'Off', 'jet-tabs' ),
				'return_value' => 'yes',
				'default'      => 'false',
			)
		);

		$this->add_control(
			'tab_control_switching',
			array(
				'label'        => esc_html__( 'Scrolling to the Content', 'jet-tabs' ),
				'description'  => esc_html__( 'Scrolling to the Content after Switching Tab Control', 'jet-tabs' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'jet-tabs' ),
				'label_off'    => esc_html__( 'Off', 'jet-tabs' ),
				'return_value' => 'yes',
				'default'      => 'false',
			)
		);

		$this->add_control(
			'tab_control_switching_offset',
			array(
				'label' => esc_html__( 'Scrolling offset (px)', 'jet-tabs' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => 0,
				),
				'condition' => array(
					'tab_control_switching' => 'yes'
				)
			)
		);

		$this->add_control(
			'tab_scrolling_navigation',
			array(
				'label'        => esc_html__( 'Scrolling Tabs Navigation', 'jet-tabs' ),
				'description'  => esc_html__( 'Scrolling tabs navigation if it does not fit in viewport', 'jet-tabs' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'jet-tabs' ),
				'label_off'    => esc_html__( 'Off', 'jet-tabs' ),
				'return_value' => 'yes',
				'default'      => 'false',
				'condition' => array(
					'tabs_position!' => ['left', 'right'],
				),
			)
		);

		$this->add_control(
			'tab_scroll_type',
			array(
				'label'        => esc_html__( 'Scroll Type', 'jet-tabs' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'plain',
				'options'      => array(
					'plain'  => esc_html__( 'Plain', 'jet-tabs' ),
					'slider' => esc_html__( 'Slider', 'jet-tabs' ),
				),
				'condition'    => array(
					'tab_scrolling_navigation' => 'yes',
					'tabs_position!' => ['left', 'right'],
				),
			)
		);

		$this->add_control(
			'fixed_slide_width',
			array(
				'label'        => esc_html__( 'Fixed Slide Width', 'jet-tabs' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'jet-tabs' ),
				'label_off'    => esc_html__( 'Off', 'jet-tabs' ),
				'return_value' => 'yes',
				'default'      => 'false',
				'condition'    => array(
					'tab_scrolling_navigation' => 'yes',
					'tab_scroll_type'          => 'slider',
					'tabs_position!' => ['left', 'right'],
				),
			)
		);

		$this->add_control(
			'slide_width_value',
			array(
				'label'       => esc_html__( 'Slide Width', 'jet-tabs' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'em' ),
				'range'       => array(
					'px' => array(
						'min' => 50,
						'max' => 500,
					),
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 200,
				),
				'condition'   => array(
					'tabs_position!' => ['left', 'right'],
					'tab_scrolling_navigation' => 'yes',
					'tab_scroll_type'          => 'slider',
					'fixed_slide_width'        => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_centered',
			array(
				'label'        => esc_html__( 'Centered', 'jet-tabs' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'jet-tabs' ),
				'label_off'    => esc_html__( 'Off', 'jet-tabs' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'tab_scrolling_navigation' => 'yes',
					'tab_scroll_type'          => 'slider',
					'tabs_position!' => ['left', 'right'],
				),
			)
		);

		$this->add_control(
			'slider_looped',
			array(
				'label'        => esc_html__( 'Looped', 'jet-tabs' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'jet-tabs' ),
				'label_off'    => esc_html__( 'Off', 'jet-tabs' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'tab_scrolling_navigation' => 'yes',
					'tab_scroll_type'          => 'slider',
					'tabs_position!' => ['left', 'right'],
				),
			)
		);

		$this->add_control(
			'slider_show_nav',
			array(
				'type'    => Controls_Manager::SWITCHER,
				'label'   => __( 'Enable Navigation', 'jet-tabs' ),
				'label_on'     => esc_html__( 'On', 'jet-tabs' ),
				'label_off'    => esc_html__( 'Off', 'jet-tabs' ),
				'return_value' => 'yes',
				'default'      => 'false',
				'condition' => array(
					'tabs_position!' => ['left', 'right'],
					'tab_scrolling_navigation' => 'yes',
					'tab_scroll_type'          => 'slider',
				),
			)
		);

		$this->end_controls_section();

		$this->__start_controls_section(
			'section_general_style',
			array(
				'label'      => esc_html__( 'General', 'jet-tabs' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->__add_responsive_control(
			'tabs_control_wrapper_width',
			array(
				'label'      => esc_html__( 'Tabs Control Width', 'jet-tabs' ),
				'description' => esc_html__( 'Working with left or right tabs position', 'jet-tabs' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', '%',
				),
				'range'      => array(
					'%' => array(
						'min' => 10,
						'max' => 50,
					),
					'px' => array(
						'min' => 100,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-tabs.jet-tabs-position-left > .jet-tabs__control-wrapper' => 'min-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .jet-tabs.jet-tabs-position-right > .jet-tabs__control-wrapper' => 'min-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .jet-tabs.jet-tabs-position-left > .jet-tabs__content-wrapper' => 'min-width: calc(100% - {{SIZE}}{{UNIT}})',
					'{{WRAPPER}} .jet-tabs.jet-tabs-position-right > .jet-tabs__content-wrapper' => 'min-width: calc(100% - {{SIZE}}{{UNIT}})',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'tabs_container_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['instance'],
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['instance'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			50
		);

		$this->__add_responsive_control(
			'tabs_container_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['instance'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'tabs_container_border',
				'label'       => esc_html__( 'Border', 'jet-tabs' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['instance'],
			),
			100
		);

		$this->__add_responsive_control(
			'tabs_container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['instance'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			100
		);

		$this->__add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'tabs_container_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['instance'],
			),
			100
		);

		$this->__end_controls_section();

		/**
		 * Tabs Control Style Section
		 */
		$this->__start_controls_section(
			'section_tabs_control_style',
			array(
				'label'      => esc_html__( 'Tabs Control', 'jet-tabs' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->__add_responsive_control(
			'tabs_controls_container_aligment',
			array(
				'label'   => esc_html__( 'Tabs Container Alignment', 'jet-tabs' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'flex-start',
				'options' => array(
					'flex-start' => array(
						'title' => ! is_rtl() ? esc_html__( 'Start', 'jet-tabs' ) : esc_html__( 'End', 'jet-tabs' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-tabs' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => ! is_rtl() ? esc_html__( 'End', 'jet-tabs' ) : esc_html__( 'Start', 'jet-tabs' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
					'stretch' => array(
						'title' => esc_html__( 'Stretch', 'jet-tabs' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] => 'align-self: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper']  => 'align-self: {{VALUE}};',
				),
			),
			100
		);

		$this->__add_responsive_control(
			'tabs_controls_aligment',
			array(
				'label'   => esc_html__( 'Tabs Alignment', 'jet-tabs' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'flex-start',
				'options' => array(
					'flex-start' => array(
						'title' => ! is_rtl() ? esc_html__( 'Start', 'jet-tabs' ) : esc_html__( 'End', 'jet-tabs' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-tabs' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => ! is_rtl() ? esc_html__( 'End', 'jet-tabs' ) : esc_html__( 'Start', 'jet-tabs' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
					'stretch' => array(
						'title' => esc_html__( 'Stretch', 'jet-tabs' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'selectors_dictionary' => array(
					'flex-start' => 'justify-content: flex-start;',
					'center'     => 'justify-content: center;',
					'flex-end'   => 'justify-content: flex-end;',
					'stretch'    => 'flex-grow: 1;',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] => '{{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['control'] => '{{VALUE}}',
				),
			),
			100
		);

		$this->__add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'tabs_content_wrapper_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control_wrapper'],
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_wrapper_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			50
		);

		$this->__add_responsive_control(
			'tabs_control_wrapper_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'tabs_control_wrapper_border',
				'label'       => esc_html__( 'Border', 'jet-tabs' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['control_wrapper'],
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_wrapper_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'tabs_control_wrapper_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control_wrapper'],
			),
			100
		);

		$this->__end_controls_section();

		/**
		 * Tabs Control Style Section
		 */
		$this->__start_controls_section(
			'section_tabs_control_item_style',
			array(
				'label'      => esc_html__( 'Tabs Control Item', 'jet-tabs' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->__add_responsive_control(
			'tabs_controls_item_aligment_left_right_icon',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-tabs' ),
				'description' => esc_html__( 'Working with left or right tabs position and left or right icon position', 'jet-tabs' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'flex-start'    => array(
						'title' => esc_html__( 'Start', 'jet-tabs' ),
						'icon'  => 'eicon-arrow-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-tabs' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-tabs' ),
						'icon'  => 'eicon-arrow-right',
					),
				),
				'condition' => array(
					'tabs_position!' => ['top', 'bottom'],
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-tabs.jet-tabs-position-left > .jet-tabs__control-wrapper > .jet-tabs__control.jet-tabs__control-icon-left .jet-tabs__control-inner' => 'justify-content: {{VALUE}};',

					'{{WRAPPER}} .jet-tabs.jet-tabs-position-left > .jet-tabs__control-wrapper > .jet-tabs__control.jet-tabs__control-icon-right .jet-tabs__control-inner' => 'justify-content: {{VALUE}};',

					'{{WRAPPER}} .jet-tabs.jet-tabs-position-right > .jet-tabs__control-wrapper > .jet-tabs__control.jet-tabs__control-icon-left .jet-tabs__control-inner' => 'justify-content: {{VALUE}};',

					'{{WRAPPER}} .jet-tabs.jet-tabs-position-right > .jet-tabs__control-wrapper > .jet-tabs__control.jet-tabs__control-icon-right .jet-tabs__control-inner' => 'justify-content: {{VALUE}};',
				),
				'classes' => 'jet-tabs-text-align-control',
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_controls_item_aligment_top_icon',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-tabs' ),
				'description' => esc_html__( 'Working with left or right tabs position and top icon position', 'jet-tabs' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'flex-start'    => array(
						'title' => ! is_rtl() ? esc_html__( 'Start', 'jet-tabs' ) : esc_html__( 'End', 'jet-tabs' ),
						'icon'  => ! is_rtl() ? 'eicon-arrow-left' : 'eicon-arrow-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-tabs' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => ! is_rtl() ? esc_html__( 'End', 'jet-tabs' ) : esc_html__( 'Start', 'jet-tabs' ),
						'icon'  => ! is_rtl() ? 'eicon-arrow-right' : 'eicon-arrow-left',
					),
				),
				'condition' => array(
					'tabs_position!' => ['top', 'bottom'],
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-tabs.jet-tabs-position-left > .jet-tabs__control-wrapper > .jet-tabs__control.jet-tabs__control-icon-top .jet-tabs__control-inner' => 'align-items: {{VALUE}};',

					'{{WRAPPER}} .jet-tabs.jet-tabs-position-right > .jet-tabs__control-wrapper > .jet-tabs__control.jet-tabs__control-icon-top .jet-tabs__control-inner' => 'align-items: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_control(
			'tabs_control_icon_style_devider',
			array(
				'type'      => Controls_Manager::DIVIDER,
			)
		);

		$this->__add_control(
			'tabs_control_icon_style_heading',
			array(
				'label'     => esc_html__( 'Icon Styles', 'jet-tabs' ),
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->__add_responsive_control(
			'tabs_control_icon_margin',
			array(
				'label'      => esc_html__( 'Icon Margin', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ' .jet-tabs__label-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ' .jet-tabs__label-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			50
		);

		$this->__add_responsive_control(
			'tabs_control_image_margin',
			array(
				'label'      => esc_html__( 'Image Margin', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ' .jet-tabs__label-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ' .jet-tabs__label-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			100
		);

		$this->__add_responsive_control(
			'tabs_control_image_width',
			array(
				'label'      => esc_html__( 'Image Width', 'jet-tabs' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem', '%',
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ' .jet-tabs__label-image' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ' .jet-tabs__label-image' => 'width: {{SIZE}}{{UNIT}}',
				),
			),
			100
		);

		$this->__add_control(
			'tabs_control_icon_position',
			array(
				'label'   => esc_html__( 'Icon Position', 'jet-tabs' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => esc_html__( 'Left', 'jet-tabs' ),
					'top'   => esc_html__( 'Top', 'jet-tabs' ),
					'right' => esc_html__( 'Right', 'jet-tabs' ),
				),
			),
			50
		);

		$this->__add_control(
			'tabs_control_state_style_heading',
			array(
				'label'     => esc_html__( 'State Styles', 'jet-tabs' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->__start_controls_tabs( 'tabs_control_styles' );

		$this->__start_controls_tab(
			'tabs_control_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-tabs' ),
			)
		);

		$this->__add_control(
			'tabs_control_label_color',
			array(
				'label'  => esc_html__( 'Text Color', 'jet-tabs' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ' ' . $css_scheme['label'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ' ' . $css_scheme['label'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tabs_control_label_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control'] . ' ' . $css_scheme['label'] . ', {{WRAPPER}} ' . $css_scheme['control_swiper'] . ' ' . $css_scheme['label'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->__add_control(
			'tabs_control_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-tabs' ),
				'type'      => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ' ' . $css_scheme['icon'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ' ' . $css_scheme['icon'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-tabs' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem',
				),
				'range'      => array(
					'px' => array(
						'min' => 18,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ' ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ' ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
				),
			),
			50
		);

		$this->__add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'tabs_control_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control'] . ', {{WRAPPER}} ' . $css_scheme['control_swiper'],
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ' .jet-tabs__control-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ' .jet-tabs__control-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			50
		);

		$this->__add_responsive_control(
			'tabs_control_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'tabs_control_border',
				'label'       => esc_html__( 'Border', 'jet-tabs' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['control'] . ', {{WRAPPER}} ' . $css_scheme['control_swiper'],
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'tabs_control_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control'] . ', {{WRAPPER}} ' . $css_scheme['control_swiper'],
			),
			100
		);

		$this->__end_controls_tab();

		$this->__start_controls_tab(
			'tabs_control_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-tabs' ),
			)
		);

		$this->__add_control(
			'tabs_control_label_color_hover',
			array(
				'label'  => esc_html__( 'Text Color', 'jet-tabs' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ':hover ' . $css_scheme['label'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ':hover ' . $css_scheme['label'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tabs_control_label_typography_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control'] . ':hover ' . $css_scheme['label'] . ', {{WRAPPER}} ' . $css_scheme['control_swiper'] . ':hover ' . $css_scheme['label'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->__add_control(
			'tabs_control_icon_color_hover',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-tabs' ),
				'type'      => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ':hover ' . $css_scheme['icon'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ':hover ' . $css_scheme['icon'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_icon_size_hover',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-tabs' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem',
				),
				'range'      => array(
					'px' => array(
						'min' => 18,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ':hover ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ':hover ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
				),
			),
			50
		);

		$this->__add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'tabs_control_background_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control'] . ':hover' . ', {{WRAPPER}} ' . $css_scheme['control_swiper'] . ':hover',
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_padding_hover',
			array(
				'label'      => esc_html__( 'Padding', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ':hover' . ' .jet-tabs__control-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ':hover' . ' .jet-tabs__control-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			50
		);

		$this->__add_responsive_control(
			'tabs_control_margin_hover',
			array(
				'label'      => esc_html__( 'Margin', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ':hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ':hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'tabs_control_border_hover',
				'label'       => esc_html__( 'Border', 'jet-tabs' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['control'] . ':hover' . ', {{WRAPPER}} ' . $css_scheme['control_swiper'] . ':hover',
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . ':hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . ':hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'tabs_control_box_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control'] . ':hover' . ', {{WRAPPER}} ' . $css_scheme['control_swiper'] . ':hover',
			),
			100
		);

		$this->__end_controls_tab();

		$this->__start_controls_tab(
			'tabs_control_active',
			array(
				'label' => esc_html__( 'Active', 'jet-tabs' ),
			)
		);

		$this->__add_control(
			'tabs_control_label_color_active',
			array(
				'label'  => esc_html__( 'Text Color', 'jet-tabs' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . '.active-tab ' . $css_scheme['label'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . '.active-tab ' . $css_scheme['label'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tabs_control_label_typography_active',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control'] . '.active-tab ' . $css_scheme['label'] . ', {{WRAPPER}} ' . $css_scheme['control_swiper'] . '.active-tab ' . $css_scheme['label'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->__add_control(
			'tabs_control_icon_color_active',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-tabs' ),
				'type'      => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . '.active-tab ' . $css_scheme['icon'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . '.active-tab ' . $css_scheme['icon'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_icon_size_active',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-tabs' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem',
				),
				'range'      => array(
					'px' => array(
						'min' => 18,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . '.active-tab ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . '.active-tab ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
				),
			),
			50
		);

		$this->__add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'tabs_control_background_active',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control'] . '.active-tab' . ', {{WRAPPER}} ' . $css_scheme['control_swiper'] . '.active-tab',
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_padding_active',
			array(
				'label'      => esc_html__( 'Padding', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . '.active-tab' . ' .jet-tabs__control-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . '.active-tab' . ' .jet-tabs__control-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			50
		);

		$this->__add_responsive_control(
			'tabs_control_margin_active',
			array(
				'label'      => esc_html__( 'Margin', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . '.active-tab' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . '.active-tab' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'tabs_control_border_active',
				'label'       => esc_html__( 'Border', 'jet-tabs' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['control'] . '.active-tab' . ', {{WRAPPER}} ' . $css_scheme['control_swiper'] . '.active-tab',
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_control_border_radius_active',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['control'] . '.active-tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['control_swiper'] . '.active-tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'tabs_control_box_shadow_active',
				'selector' => '{{WRAPPER}} ' . $css_scheme['control'] . '.active-tab' . ', {{WRAPPER}} ' . $css_scheme['control_swiper'] . '.active-tab',
			),
			100
		);

		$this->__end_controls_tab();

		$this->__end_controls_tabs();

		$this->__end_controls_section();

		/**
		 * Tabs Content Style Section
		 */
		$this->__start_controls_section(
			'section_tabs_content_style',
			array(
				'label'      => esc_html__( 'Tabs Content', 'jet-tabs' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->__add_control(
			'tabs_content_text_color',
			array(
				'label'     => esc_html__( 'Text color', 'jet-tabs' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['content'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tabs_content_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['content'],
			),
			50
		);

		$this->__add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'tabs_content_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['content_wrapper'],
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['content'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			50
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'tabs_content_border',
				'label'       => esc_html__( 'Border', 'jet-tabs' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['content_wrapper'],
			),
			25
		);

		$this->__add_responsive_control(
			'tabs_content_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-tabs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['content_wrapper'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'custom_units' => true,
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'tabs_content_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['content_wrapper'],
			),
			100
		);

		$this->__add_control(
			'tabs_content_loader_style_heading',
			array(
				'label'     => esc_html__( 'Loader Styles', 'jet-tabs' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'ajax_template' => 'yes',
				),
			),
			25
		);

		$this->__add_control(
			'tabs_content_loader_color',
			array(
				'label'     => esc_html__( 'Loader color', 'jet-tabs' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['content'] . ' .jet-tabs-loader' => 'border-color: {{VALUE}}; border-top-color: white;',
				),
				'condition' => array(
					'ajax_template' => 'yes',
				),
			),
			25
		);

		$this->__end_controls_section();

		/**
		 * Tabs Slider Navigation
		 */
		$this->start_controls_section(
			'section_navigation_style',
			array(
				'label' => esc_html__( 'Navigation', 'jet-tabs' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'tab_scrolling_navigation' => 'yes',
					'tab_scroll_type' => 'slider',
					'tabs_position!'  => ['left', 'right'],
					'slider_show_nav' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_arrows_style',
			array(
				'label' => esc_html__( 'Arrows', 'jet-tabs' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'tab_scrolling_navigation' => 'yes',
					'tab_scroll_type' => 'slider',
					'tabs_position!'  => ['left', 'right'],
					'slider_show_nav' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_size',
			array(
				'label' => __( 'Size', 'jet-tabs' ),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => array(
					'px' => array(
						'min' => 6,
						'max' => 80,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] . ' .swiper-button-next:after, {{WRAPPER}} ' . $css_scheme['control_wrapper'] . ' .swiper-button-prev:after' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'tab_scrolling_navigation' => 'yes',
					'tab_scroll_type' => 'slider',
					'tabs_position!'  => ['left', 'right'],
					'slider_show_nav' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_position',
			array(
				'label' => esc_html__( 'Position', 'jet-tabs' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inside',
				'options' => array(
					'inside' => esc_html__( 'Inside', 'jet-tabs' ),
					'outside' => esc_html__( 'Outside', 'jet-tabs' ),
				),
				'prefix_class' => 'jet-tabs-nav-position-',
				'condition' => array(
					'tab_scrolling_navigation' => 'yes',
					'tab_scroll_type' => 'slider',
					'tabs_position!'  => ['left', 'right'],
					'slider_show_nav' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_arrows_colors' );

		$this->start_controls_tab(
			'tab_arrows_normal',
			array(
				'label' => __( 'Normal', 'jet-tabs' ),
			)
		);

		$this->add_control(
			'arrows_color',
			array(
				'label' => __( 'Color', 'jet-tabs' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] . ' .swiper-button-next, {{WRAPPER}} ' . $css_scheme['control_wrapper'] . ' .swiper-button-prev' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrows_hover',
			array(
				'label' => __( 'Hover', 'jet-tabs' ),
			)
		);

		$this->add_control(
			'arrows_color_hover',
			array(
				'label' => __( 'Color', 'jet-tabs' ),
				'type'  => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['control_wrapper'] . ' .swiper-button-next:hover, {{WRAPPER}} ' . $css_scheme['control_wrapper'] . ' .swiper-button-prev:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function get_tab_item_content( $item = array(), $index = 0, $args = array() ) {

		$tab_count = $index + 1;
		$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );
		$id_int = $args['id_int'];
		$active_index = $args['active_index'];
		$no_active_tabs = $args['no_active_tabs'];
		$ajax_template = $args['ajax_template'];

		$this->add_render_attribute( $tab_content_setting_key, array(
			'id'               => 'jet-tabs-content-' . $id_int . $tab_count,
			'class'            => array(
				'jet-tabs__content',
				( $index === $active_index && ! $no_active_tabs ) ? 'active-content' : '',
			),
			'data-tab'         => $tab_count,
			'role'             => 'tabpanel',
			'aria-hidden'      => $index === $active_index ? 'false' : 'true',
			'data-template-id' => ! empty( $item['item_template_id'] ) ? $item['item_template_id'] : 'false',
		) );

		$content_html = '';
		
		$template_cache_settings = jet_tabs_settings()->get( 'useTemplateCache', [
			'enable'          => false,
			'cacheExpiration' => 'week',
		] );

		$elementor_template = new Elementor_Template();
		$in_elementor = jet_tabs_integration()->in_elementor();

		switch ( $item[ 'content_type' ] ) {
			case 'template':
				if ( ! empty( $item['item_template_id'] ) ) {
					$template_id = apply_filters( 'jet-tabs/widgets/template_id', $item['item_template_id'], $this );
					$template_id = absint( $template_id );

					$template_post = get_post( $template_id );
					if ( ! $template_post ) {
						$content_html = $this->no_templates_message();
						break;
					}

					if ( 'elementor_library' !== $template_post->post_type ) {
						$content_html = $this->no_template_content_message();
						break;
					}

					if ( 'publish' !== $template_post->post_status ) {
						$content_html = $this->no_template_content_message();
						break;
					}

					$template_content = jet_tabs()->elementor()->frontend->get_builder_content_for_display( $template_id );

					if ( ! empty( $template_content ) ) {
						if ( ! $ajax_template ) {
							if ( $template_cache_settings['enable'] && ! $in_elementor && is_array( $template_content ) ) {
								$content_html .= $template_content['template_content'];
								$template_styles  = $template_content['template_styles'] ?? [];
								$template_scripts = $template_content['template_scripts'] ?? [];

								$this->styles_to_enqueue = wp_parse_args( $template_styles, $this->styles_to_enqueue );
								$this->scripts_to_enqueue = wp_parse_args( $template_scripts, $this->scripts_to_enqueue );
							} else {
								$content_html .= is_array( $template_content ) ? ( $template_content['template_content'] ?? '' ) : $template_content;
							}
						} else {
							$content_html .= '<div class="jet-tabs-loader"></div>';
						}
					
						
						if ( jet_tabs_integration()->is_edit_mode() ) {
							$link = add_query_arg(
								array(
									'elementor' => '',
								),
								get_permalink( $item['item_template_id'] )
							);
		
							$content_html .= sprintf(
								'<div class="jet-tabs__edit-cover" data-template-edit-link="%s"><i class="fas fa-pencil-alt"></i><span>%s</span></div>',
								esc_url( $link ),
								esc_html__( 'Edit Template', 'jet-tabs' )
							);
						}
					} else {
						$content_html = $this->no_template_content_message();
					}
				} else {
					$content_html = $this->no_templates_message();
				}
			break;

			case 'editor':
				$content_html = $this->parse_text_editor( $item['item_editor_content'] );
			break;
		}

		return sprintf( '<div %1$s>%2$s</div>', $this->get_render_attribute_string( $tab_content_setting_key ), $content_html );
	}

	/**
	 * [render description]
	 * @return [type] [description]
	 */
	protected function render() {

		$this->__context = 'render';

		$tabs = $this->get_settings_for_display( 'tabs' );


		$id_int = substr( $this->get_id_int(), 0, 3 );

		$tabs_position        = $this->get_settings( 'tabs_position' );
		$tabs_position_tablet = $this->get_settings( 'tabs_position_tablet' );
		$tabs_position_mobile = $this->get_settings( 'tabs_position_mobile' );
		$show_effect          = $this->get_settings( 'show_effect' );
		$no_active_tabs       = filter_var( $this->get_settings( 'no_active_tabs' ), FILTER_VALIDATE_BOOLEAN );
		$ajax_template        = filter_var( $this->get_settings( 'ajax_template' ), FILTER_VALIDATE_BOOLEAN );
		$tabs_item_label_tag  = ! empty( $this->get_settings( 'item_html_tag' ) ) ? $this->get_settings( 'item_html_tag' ) : 'div';
		$tabs_scrolling_navigation = filter_var( $this->get_settings( 'tab_scrolling_navigation' ), FILTER_VALIDATE_BOOLEAN );
		$tab_scroll_type      = $this->get_settings( 'tab_scroll_type' );
		$fixed_slide_width    = filter_var( $this->get_settings( 'fixed_slide_width' ), FILTER_VALIDATE_BOOLEAN );
		$slide_width_value    = $this->get_settings( 'slide_width_value' );
		$slider_centered      = filter_var( $this->get_settings( 'slider_centered' ), FILTER_VALIDATE_BOOLEAN );
		$slider_looped        = filter_var( $this->get_settings( 'slider_looped' ), FILTER_VALIDATE_BOOLEAN );
		$slider_show_nav      = filter_var( $this->get_settings( 'slider_show_nav' ), FILTER_VALIDATE_BOOLEAN );

		$tabs = apply_filters( 'jet-tabs/widget/loop-items', $tabs, 'tabs', $this );

		if ( ! $tabs || empty( $tabs ) ) {
			return false;
		}

		$active_index = -1;

		foreach ( $tabs as $index => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			// Find first active tab
			if (
				$active_index === -1 &&
				array_key_exists( 'item_active', $item ) &&
				filter_var( $item['item_active'], FILTER_VALIDATE_BOOLEAN )
			) {
				$active_index = $index;
			}

			if (
				'template' === ( $item['content_type'] ?? '' ) &&
				! empty( $item['item_template_id'] )
			) {
				$post_status = get_post_status( $item['item_template_id'] );
				if ( 'draft' === $post_status || 'trash' === $post_status ) {
					unset( $tabs[ $index ] );
				}
			}
		}

		if ( $active_index === -1 ) {
			$active_index = 0;
		}

		$settings = array(
			'activeIndex'           => ! $no_active_tabs ? $active_index : -1,
			'event'                 => $this->get_settings( 'tabs_event' ),
			'autoSwitch'            => filter_var( $this->get_settings( 'auto_switch' ), FILTER_VALIDATE_BOOLEAN ),
			'autoSwitchDelay'       => $this->get_settings( 'auto_switch_delay' ),
			'ajaxTemplate'          => $ajax_template,
			'tabsPosition'          => $tabs_position,
			'switchScrolling'       => filter_var( $this->get_settings( 'tab_control_switching' ), FILTER_VALIDATE_BOOLEAN ),
			'switchScrollingOffset' => ! empty( $this->get_settings_for_display( 'tab_control_switching_offset' ) ) ? $this->get_settings_for_display( 'tab_control_switching_offset' ) : 0
		);

		$attributes = array(
			'class'         => array(
				'jet-tabs',
				'jet-tabs-position-' . $tabs_position,
				'jet-tabs-' . $show_effect . '-effect',
				( $ajax_template ) ? 'jet-tabs-ajax-template' : '',
			),
			'data-settings' => wp_json_encode( $settings ),
		);

		if ( $tabs_scrolling_navigation && $tab_scroll_type === 'slider' && ( $tabs_position === 'top' || $tabs_position === 'bottom' ) ) {
			$swiper_settings = array(
				'slidesPerView' => $fixed_slide_width ? 'fixed' : 'auto',
				'itemWidth'     => $fixed_slide_width ? $slide_width_value['size'] . $slide_width_value['unit'] : 'auto',
				'centeredSlides'=> $slider_centered,
				'loop'          => $slider_looped,
			);
			$attributes['data-swiper-settings'] = wp_json_encode( $swiper_settings );
		}

		$this->add_render_attribute( 'instance', $attributes );

		$tabs_content = array();

		?>

		<div <?php echo $this->get_render_attribute_string( 'instance' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>>
			<div class="jet-tabs__control-wrapper <?php echo ( $tabs_scrolling_navigation && $tab_scroll_type === 'plain' ) ? 'jet-tabs-scrolling-navigation' : ''; ?><?php echo ( $tabs_scrolling_navigation && $tab_scroll_type === 'slider' && ( $tabs_position === 'top' || $tabs_position === 'bottom') ) ? 'jet-tabs-swiper' : ''; ?>" role="tablist">
                <?php if ( $tabs_scrolling_navigation && $tab_scroll_type === 'slider' && ( $tabs_position === 'top' || $tabs_position === 'bottom') ) : ?>
                <div class="swiper jet-tabs-swiper-container">
                    <div class="swiper-wrapper">
                    <?php endif; ?>
                    <?php

						do_action( 'jet-engine-query-gateway/before-loop', 'tabs', $this );

                        foreach ( $tabs as $index => $item ) {

                            do_action( 'jet-engine-query-gateway/do-item', $item );

                            $tab_count = $index + 1;
                            $tab_title_setting_key = $this->get_repeater_setting_key( 'jet_tab_control', 'tabs', $index );
                            $tab_control_id = ! empty( $item['control_id'] ) ? esc_attr( $item['control_id'] ) : 'jet-tabs-control-' . $id_int . $tab_count;

                            $this->add_render_attribute( $tab_title_setting_key, array(
                                'id'               => $tab_control_id,
                                'class'            => array(
                                    'jet-tabs__control',
                                    'jet-tabs__control-icon-' . $this->get_settings( 'tabs_control_icon_position' ),
                                    'elementor-menu-anchor',
                                    ( $index === $active_index && ! $no_active_tabs ) ? 'active-tab' : '',
                                    ( $tabs_scrolling_navigation && $tab_scroll_type === 'slider' ) ? 'swiper-slide' : '',
                                ),
                                'data-tab'         => $tab_count,
                                'tabindex'         => 0,
                                'role'             => 'tab',
                                'aria-controls'    => 'jet-tabs-content-' . $id_int . $tab_count,
                                'aria-expanded'    => $index === $active_index ? 'true' : 'false',
                                'data-template-id' => ! empty( $item['item_template_id'] ) ? $item['item_template_id'] : 'false',
                            ) );

                            $title_icon_html = $this->__get_icon( 'item_icon', $item, '<div class="jet-tabs__label-icon jet-tabs-icon">%s</div>' );

                            $title_image_html = '';

                            if ( ! empty( $item['item_image']['url'] ) ) {
                                $title_image_html = sprintf( '<img class="jet-tabs__label-image" src="%1$s" alt="">', esc_url( $item['item_image']['url'] ) );
                            }

                            $title_label_html = '';

                            if ( ! empty( $item['item_label'] ) ) {
                                $title_label_html = sprintf( '<' . $tabs_item_label_tag . ' class="jet-tabs__label-text">%1$s</' . $tabs_item_label_tag . '>', wp_kses_post( $item['item_label'] ) );
                            }

                            if ( 'right' === $this->get_settings( 'tabs_control_icon_position' ) ) {
                                echo sprintf(
                                    '<div %1$s><div class="jet-tabs__control-inner">%2$s%3$s</div></div>',
                                    $this->get_render_attribute_string( $tab_title_setting_key ), // phpcs:ignore
                                    $title_label_html, // phpcs:ignore
                                    filter_var( $item['item_use_image'], FILTER_VALIDATE_BOOLEAN ) ? $title_image_html : $title_icon_html // phpcs:ignore
                                );
                            } else {
                                echo sprintf(
                                    '<div %1$s><div class="jet-tabs__control-inner">%2$s%3$s</div></div>',
                                    $this->get_render_attribute_string( $tab_title_setting_key ), // phpcs:ignore
                                    filter_var( $item['item_use_image'], FILTER_VALIDATE_BOOLEAN ) ? $title_image_html : $title_icon_html, // phpcs:ignore
                                    $title_label_html // phpcs:ignore
                                );
                            }

                            $tabs_content[] = $this->get_tab_item_content( $item, $index, array(
                                'id_int' => $id_int,
                                'active_index' => $active_index,
                                'no_active_tabs' => $no_active_tabs,
                                'ajax_template' => $ajax_template,
                            ) );

                        }

                        do_action( 'jet-engine-query-gateway/reset-item' );
                    ?>

                    <?php if ( $tabs_scrolling_navigation && $tab_scroll_type === 'slider' && ( $tabs_position === 'top' || $tabs_position === 'bottom') ) : ?>
                    </div> <!-- Close swiper-wrapper -->
                </div> <!-- Close swiper -->

                <?php if ( $slider_show_nav ) : ?>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <?php endif; ?>

			    <?php endif; ?>
			    </div>
			<div class="jet-tabs__content-wrapper">
				<?php
					foreach ( $tabs_content as $index => $tab ) {
						echo $tab; // phpcs:ignore
					}
				?>
			</div>
			<?php
			$this->maybe_enqueue_styles();
			$this->maybe_enqueue_scripts();
			?>
		</div>
		<?php
	}

	/**
	 * [empty_templates_message description]
	 * @return [type] [description]
	 */
	public function empty_templates_message() {
		return '<div id="elementor-widget-template-empty-templates">
				<div class="elementor-widget-template-empty-templates-icon"><i class="eicon-nerd"></i></div>
				<div class="elementor-widget-template-empty-templates-title">' . esc_html__( 'You Haven’t Saved Templates Yet.', 'jet-tabs' ) . '</div>
				<div class="elementor-widget-template-empty-templates-footer">' . esc_html__( 'What is Library?', 'jet-tabs' ) . ' <a class="elementor-widget-template-empty-templates-footer-url" href="https://go.elementor.com/docs-library/" target="_blank">' . esc_html__( 'Read our tutorial on using Library templates.', 'jet-tabs' ) . '</a></div>
				</div>';
	}

	/**
	 * [no_templates_message description]
	 * @return [type] [description]
	 */
	public function no_templates_message() {
		$message = '<span>' . esc_html__( 'Template is not defined. ', 'jet-tabs' ) . '</span>';

		$link = add_query_arg(
			array(
				'post_type'     => 'elementor_library',
				'action'        => 'elementor_new_post',
				'_wpnonce'      => wp_create_nonce( 'elementor_action_new_post' ),
				'template_type' => 'section',
			),
			esc_url( admin_url( '/edit.php' ) )
		);

		$new_link = '<span>' . esc_html__( 'Select an existing template or create a ', 'jet-tabs' ) . '</span><a class="jet-tabs-new-template-link elementor-clickable" target="_blank" href="' . $link . '">' . esc_html__( 'new one', 'jet-tabs' ) . '</a>' ;

		return sprintf(
			'<div class="jet-tabs-no-template-message">%1$s%2$s</div>',
			$message,
			jet_tabs_integration()->in_elementor() ? $new_link : ''
		);
	}

	/**
	 * [no_template_content_message description]
	 * @return [type] [description]
	 */
	public function no_template_content_message() {
		$message = '<span>' . esc_html__( 'The tabs are working. Please, note, that you have to add a template to the library in order to be able to display it inside the tabs.', 'jet-tabs' ) . '</span>';

		return sprintf( '<div class="jet-toogle-no-template-message">%1$s</div>', $message );
	}

	/**
	 * [get_template_edit_link description]
	 * @param  [type] $template_id [description]
	 * @return [type]              [description]
	 */
	public function get_template_edit_link( $template_id ) {

		$link = add_query_arg( 'elementor', '', get_permalink( $template_id ) );

		return '<a target="_blank" class="elementor-edit-template elementor-clickable" href="' . esc_url( $link ) .'"><i class="fas fa-pencil"></i> ' . esc_html__( 'Edit Template', 'jet-tabs' ) . '</a>';
	}

	/**
	 * Get seconds by tag for transient caching
	 *
	 * @param string $tag Time duration tag
	 * @return string|int Returns 'none' or seconds
	 */
	public static function get_cache_timeout($tag = 'none') {
		if ('none' === $tag) {
			return 'none';
		}

		switch ($tag) {
			case 'hour':
				$delay = HOUR_IN_SECONDS;
				break;

			case 'day':
				$delay = DAY_IN_SECONDS;
				break;

			case '3days':
				$delay = 3 * DAY_IN_SECONDS;
				break;

			case 'week':
				$delay = WEEK_IN_SECONDS;
				break;

			case 'month':
				$delay = MONTH_IN_SECONDS;
				break;

			default:
				$delay = 'none';
				break;
		}

		return $delay;
	}

	/**
	 * @return false|void
	 */
	public function maybe_enqueue_styles() {
		$style_depends = $this->get_styles_to_enqueue();
	
		if ( empty( $style_depends ) ) {
			return false;
		}
	
		foreach ( $style_depends as $key => $style_data ) {
			$style_handle = $style_data['handle'];
	
			if ( wp_style_is( $style_handle ) ) {
				continue;
			}
	
			$style_obj = $style_data['obj'];
	
			if ( $style_obj ) {
				if ( ! isset( wp_styles()->registered[ $style_handle ] ) ) {
					wp_styles()->registered[ $style_handle ] = $style_obj;
				}
				wp_enqueue_style( $style_obj->handle, $style_obj->src, $style_obj->deps, $style_obj->ver );
			} else {
				wp_enqueue_style( $style_handle, $style_data['src'] );
			}
		}
	}

	/**
	 * []
	 * @return [type] [description]
	 */
	public function maybe_enqueue_scripts() {
		$script_depends = $this->get_scripts_to_enqueue();

		if ( empty( $script_depends ) ) {
			return false;
		}

		foreach ( $script_depends as $script => $script_data ) {

			$script_handle = $script_data['handle'];
			$script_obj = $script_data['obj'];

			if ( wp_script_is( $script_handle ) ) {
				continue;
			}

			wp_scripts()->registered[ $script_handle ] = $script_obj;

		}

		foreach ( $script_depends as $script => $script_data ) {
			
			$script_handle = $script_data['handle'];

			if ( wp_script_is( $script_handle ) ) {
				continue;
			}

			$script_obj = $script_data['obj'];

			wp_enqueue_script( $script_obj->handle, $script_obj->src, $script_obj->deps, $script_obj->ver );

			wp_scripts()->print_extra_script( $script_obj->handle );
		}
	}

	public function get_styles_to_enqueue() {
		return $this->styles_to_enqueue;
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_scripts_to_enqueue() {
		return $this->scripts_to_enqueue;
	}

	/**
	 * @var array
	 */
	public $styles_to_enqueue = [];

	/**
	 * [$depended_scripts description]
	 * @var array
	 */
	public $scripts_to_enqueue = [];

}
