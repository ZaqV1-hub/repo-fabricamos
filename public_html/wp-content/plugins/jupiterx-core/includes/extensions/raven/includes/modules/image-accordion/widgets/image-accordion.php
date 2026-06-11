<?php
namespace JupiterX_Core\Raven\Modules\Image_Accordion\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use \Elementor\Controls_Manager;
use \Elementor\Utils;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined( 'ABSPATH' ) || die();

class Image_Accordion extends Base_Widget {

	public $link_open;

	public $link_close;

	public function get_name() {
		return 'raven-image-accordion';
	}

	public function get_title() {
		return esc_html__( 'Image Accordion', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-image-accordion';
	}

	protected function register_controls() {
		$this->register_content_repeater_controls();
		$this->register_content_settings_controls();
		$this->register_style_container_controls();
		$this->register_style_item_controls();
		$this->register_style_content_controls();
		$this->register_style_title_controls();
		$this->register_style_description_controls();
		$this->register_style_button_controls();
	}

	private function register_content_repeater_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Items', 'jupiterx-core' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'list_active',
			[
				'label' => esc_html__( 'Active', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$repeater->add_control(
			'list_image',
			[
				'label' => esc_html__( 'Image', 'jupiterx-core' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'list_title',
			[
				'label' => esc_html__( 'Title', 'jupiterx-core' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Title', 'jupiterx-core' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'list_description',
			[
				'label' => esc_html__( 'Description', 'jupiterx-core' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'jupiterx-core' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'list_button_text',
			[
				'label' => esc_html__( 'Button text', 'jupiterx-core' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'More', 'jupiterx-core' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'list_link',
			[
				'label'       => esc_html__( 'Link', 'jupiterx-core' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
				'dynamic' => [
					'active' => true,
				],
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'list',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ list_title }}}',
				'default' => [
					[
						'list_title' => esc_html( 'Title #1', 'jupiterx-core' ),
						'list_description' => esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'jupiterx-core' ),
						'list_button_text' => esc_html( 'More', 'jupiterx-core' ),
						'list_link' => '#',
					],
					[
						'list_title' => esc_html( 'Title #2', 'jupiterx-core' ),
						'list_description' => esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'jupiterx-core' ),
						'list_button_text' => esc_html( 'More', 'jupiterx-core' ),
						'list_link' => '#',
					],
					[
						'list_title' => esc_html( 'Title #3', 'jupiterx-core' ),
						'list_description' => esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'jupiterx-core' ),
						'list_button_text' => esc_html( 'More', 'jupiterx-core' ),
						'list_link' => '#',
					],
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_content_settings_controls() {
		$this->start_controls_section(
			'content_settings',
			[
				'label' => esc_html__( 'Settings', 'jupiterx-core' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'html_tag',
			[
				'label'       => esc_html__( 'HTML Tag', 'jupiterx-core' ),
				'description' => esc_html__( 'Select the HTML Tag for the Item\'s title', 'jupiterx-core' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default'     => 'h5',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'exclude' => [ 'custom' ],
				'default' => 'full',
			]
		);

		$this->add_responsive_control(
			'orientation',
			[
				'label'   => esc_html__( 'Orientation', 'jupiterx-core' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'row',
				'options' => [
					'row'   => esc_html__( 'Vertical', 'jupiterx-core' ),
					'column' => esc_html__( 'Horizontal', 'jupiterx-core' ),
				],
				'selectors' => [
					'{{WRAPPER}} .accordion-list-items' => 'flex-direction: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'active_size',
			[
				'label' => esc_html__( 'Active Size(%)', 'jupiterx-core' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 50,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'anim_duration',
			[
				'label'   => esc_html__( 'Animation Duration', 'jupiterx-core' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 100,
				'selectors' => [
					'{{WRAPPER}} .jupiterx-image-accordion-item' => 'transition-duration: {{VALUE}}ms',
				],
			]
		);

		$this->add_control(
			'anim_ease',
			[
				'label'   => esc_html__( 'Easing', 'jupiterx-core' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'circ',
				'options' => [
					'sine'  => esc_html__( 'Sine', 'jupiterx-core' ),
					'quint' => esc_html__( 'Quint', 'jupiterx-core' ),
					'cubic' => esc_html__( 'Cubic', 'jupiterx-core' ),
					'expo'  => esc_html__( 'Expo', 'jupiterx-core' ),
					'circ'  => esc_html__( 'Circ', 'jupiterx-core' ),
					'back'  => esc_html__( 'Back', 'jupiterx-core' ),
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_container_controls() {
		$this->start_controls_section(
			'container_style',
			[
				'label'      => esc_html__( 'Container', 'jupiterx-core' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_responsive_control(
			'container_height',
			[
				'label' => esc_html__( 'Height(px)', 'jupiterx-core' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 600,
				],
				'selectors'  => [
					'{{WRAPPER}} .jupiterx-image-advanced-accordion-wrap' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'container_background',
				'selector' => '{{WRAPPER}} .jupiterx-image-advanced-accordion-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'container_border',
				'label'       => esc_html__( 'Border', 'jupiterx-core' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .jupiterx-image-advanced-accordion-wrap',
			]
		);

		$this->add_control(
			'container_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .jupiterx-image-advanced-accordion-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'container_shadow',
				'selector' => '{{WRAPPER}} .jupiterx-image-advanced-accordion-wrap',
			]
		);

		$this->end_controls_section();
	}

	private function register_style_item_controls() {
		$this->start_controls_section(
			'item_style',
			[
				'label'      => esc_html__( 'Item', 'jupiterx-core' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[
				'label' => esc_html__( 'Item Gutter', 'jupiterx-core' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors'  => [
					'{{WRAPPER}} .jupiterx-image-accordion-item' => 'margin: calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .jupiterx-image-advanced-accordion-wrap' => 'margin: calc(-{{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => '{{WRAPPER}}',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => esc_html__( 'Border', 'jupiterx-core' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .jupiterx-image-accordion-item',
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .jupiterx-image-accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .jupiterx-image-accordion-item',
			]
		);

		$this->add_control(
			'item_cover_style_heading',
			[
				'label'     => esc_html__( 'Cover Styles', 'jupiterx-core' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'item_cover_tabs_styles' );

		$this->start_controls_tab(
			'item_cover_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_cover_background',
				'selector' => '{{WRAPPER}} .jupiterx-image-accordion-item:before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'item_cover_active',
			[
				'label' => esc_html__( 'Active', 'jupiterx-core' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_cover_background_active',
				'selector' => '{{WRAPPER}} .jupiterx-active-image-accordion-item:before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function register_style_content_controls() {
		$this->start_controls_section(
			'content_style',
			[
				'label'      => esc_html__( 'Content', 'jupiterx-core' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'   => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'flex-start'    => [
						'title' => esc_html__( 'Top', 'jupiterx-core' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => esc_html__( 'Bottom', 'jupiterx-core' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .jupiterx-image-accordion-item-content' => 'justify-content: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_content_background',
				'selector' => '{{WRAPPER}} .jupiterx-image-accordion-item-content',
			]
		);

		$this->add_responsive_control(
			'item_content_padding',
			[
				'label'      => __( 'Padding', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .jupiterx-image-accordion-item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_title_controls() {
		$this->start_controls_section(
			'title_style',
			[
				'label'      => esc_html__( 'Title', 'jupiterx-core' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_responsive_control(
			'title_alignment',
			[
				'label'   => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'  => esc_html__( 'Color', 'jupiterx-core' ),
				'type'   => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .accordion-image-item-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .accordion-image-item-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__( 'Margin', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_description_controls() {
		$this->start_controls_section(
			'description_style',
			[
				'label'      => esc_html__( 'Description', 'jupiterx-core' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_responsive_control(
			'description_alignment',
			[
				'label'   => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-description' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'  => esc_html__( 'Color', 'jupiterx-core' ),
				'type'   => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .accordion-image-item-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .accordion-image-item-description',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_responsive_control(
			'description_padding',
			[
				'label'      => esc_html__( 'Padding', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_margin',
			[
				'label'      => esc_html__( 'Margin', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_button_controls() {
		$this->start_controls_section(
			'button_style',
			[
				'label'      => esc_html__( 'Button', 'jupiterx-core' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_responsive_control(
			'button_alignment',
			[
				'label'   => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-button' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Padding', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-button a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label'      => esc_html__( 'Margin', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-button a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-image-item-button a' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .accordion-image-item-button a' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .accordion-image-item-button a',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'label'       => esc_html__( 'Border', 'jupiterx-core' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .accordion-image-item-button a',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-button a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .accordion-image-item-button a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-image-item-button a:hover' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->add_control(
			'primary_button_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-image-item-button a:hover' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_hover_typography',
				'selector' => '{{WRAPPER}} .accordion-image-item-button a:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_hover_border',
				'label'       => esc_html__( 'Border', 'jupiterx-core' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .accordion-image-item-button a:hover',
			]
		);

		$this->add_responsive_control(
			'button_hover_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .accordion-image-item-button a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .accordion-image-item-button a:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$list           = $settings['list'];
		$list_classes   = [ 'accordion-list-items' ];
		$list_classes[] = isset( $settings['orientation'] ) ? 'accordion-mode-is-' . $settings['orientation'] : 'accordion-mode-is-row';
		$list_classes[] = 'accordion-anime-is-' . $settings['anim_ease'];
		$active         = false;

		$this->add_render_attribute(
			'list_wrapper',
			[
				'class' => $list_classes,
			]
		);

		?>
			<div class="jupiterx-image-advanced-accordion-wrap">
				<div <?php echo $this->get_render_attribute_string( 'list_wrapper' ); ?>>
					<?php foreach ( $list as $item ) : ?>
						<?php
							$active_class = '';

							if ( false === $active && 'yes' === $item['list_active'] ) {
								$active_class = 'jupiterx-default-active-image-accordion-item';
								$active       = true;
							}

						?>
						<div class="jupiterx-image-accordion-item <?php echo esc_attr( $active_class ); ?>">
							<?php
								// WPML Compatibility.
								$item['list_image']['id'] = apply_filters( 'wpml_object_id', $item['list_image']['id'], 'attachment', true );

								$img_id  = $item['list_image']['id'];
								$img_src = wp_get_attachment_image_url( $img_id, $settings['thumbnail_size'] );

								if ( empty( $img_src ) ) {
									$img_src = Utils::get_placeholder_image_src();
								}

								$img_alt = get_post_meta( $item['list_image']['id'], '_wp_attachment_image_alt', true );
							?>
							<img
								src="<?php echo esc_url( $img_src ); ?>"
								alt="<?php echo esc_attr( $img_alt ); ?>"
							/>
							<div class="jupiterx-image-accordion-item-content">
								<<?php Utils::print_validated_html_tag( $settings['html_tag'] ); ?> class="accordion-image-item-title accordion-image-item-content">
									<?php echo esc_html( $item['list_title'] ); ?>
								</<?php Utils::print_validated_html_tag( $settings['html_tag'] ); ?>>
								<div class="accordion-image-item-description accordion-image-item-content">
									<?php echo esc_html( $item['list_description'] ); ?>
								</div>
								<?php $this->manage_link( $item ); ?>
								<div class="accordion-image-item-button accordion-image-item-content">
									<?php echo $this->link_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php echo esc_html( $item['list_button_text'] ); ?>
									<?php echo $this->link_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php
	}

	/**
	 * Prepare link attributes.
	 *
	 * @param array $item repeater item settings.
	 * @since 4.2.0
	 */
	private function manage_link( $item ) {
		$this->add_link_attributes( 'link_' . $item['_id'], $item['list_link'] );
		$this->link_open  = '<a class="elementor-button" ' . $this->get_render_attribute_string( 'link_' . $item['_id'] ) . ' >';
		$this->link_close = '</a>';
	}
}
