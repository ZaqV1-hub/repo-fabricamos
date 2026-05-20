<?php
namespace JupiterX_Core\Raven\Modules\Carousel\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Repeater;
use Elementor\Utils;
use JupiterX_Core\Raven\Utils as RavenUtils;
use Elementor\Plugin;
use JupiterX_Core\Raven\Controls\Query as Control_Query;

defined( 'ABSPATH' ) || die();

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Testimonial_Carousel extends Base {

	public function get_name() {
		return 'raven-testimonial-carousel';
	}

	public function get_title() {
		return esc_html__( 'Testimonial Carousel', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-testimonial-carousel';
	}

	public function get_style_depends() {
		return [ 'e-swiper', 'swiper', 'elementor-icons' ];
	}

	protected function register_controls() {
		parent::child_class_name( 'testimonial-carousel' );
		$this->register_controls_section_slides();
		$this->register_controls_section_additional_options();
		$this->register_controls_section_slides_style();
		$this->register_controls_section_navigation();

		$this->register_controls_section_rating();
		$this->register_controls_section_skin_style();
		$this->register_controls_section_content_image_styles();
		$this->register_controls_section_rating_style();
		$this->register_injections();

		$this->update_responsive_control(
			'width',
			[
				'selectors' => [
					'{{WRAPPER}}.raven-arrows-yes .raven-main-swiper' => 'width: calc( {{SIZE}}{{UNIT}} - 40px )',
					'{{WRAPPER}} .raven-main-swiper' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->remove_control( 'pagination_position' );
	}

	protected function register_controls_section_slides() {
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Slides', 'jupiterx-core' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$this->add_repeater_controls( $repeater );

		$this->add_control(
			'slides',
			[
				'label' => esc_html__( 'Slides', 'jupiterx-core' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => $this->get_repeater_defaults(),
				'separator' => 'after',
			]
		);

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_responsive_control(
			'slides_per_view',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__( 'Slides Per View', 'jupiterx-core' ),
				'description' => esc_html__( 'Supports decimal numbers as well, e.g. 2.3', 'jupiterx-core' ),
				'default' => [
					'size' => 1,
				],
				'widescreen_default' => [
					'size' => 1,
				],
				'laptop_default' => [
					'size' => 1,
				],
				'tablet_extra_default' => [
					'size' => 1,
				],
				'tablet_default' => [
					'size' => 1,
				],
				'mobile_extra_default' => [
					'size' => 1,
				],
				'mobile_default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
						'step' => 1,
					],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'centered_slides',
			[
				'label' => esc_html__( 'Centered Slides', 'jupiterx-core' ),
				'type' => 'switcher',
				'description' => esc_html__( 'Set the slide(s) at the center of the slider viewport if a decimal number is used for "Slides Per View".', 'jupiterx-core' ),
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__( 'Slides to Scroll', 'jupiterx-core' ),
				'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'jupiterx-core' ),
				'options' => [
					'' => esc_html__( 'Default', 'jupiterx-core' ),
				] + $slides_per_view,
				'inherit_placeholders' => false,
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1140,
					],
					'%' => [
						'min' => 50,
					],
				],
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-main-swiper' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_controls_section_skin_style() {
		$this->start_injection( [
			'of' => 'slides',
		] );

		$this->add_control(
			'skin',
			[
				'label' => esc_html__( 'Skin', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'jupiterx-core' ),
					'bubble' => esc_html__( 'Bubble', 'jupiterx-core' ),
				],
				'prefix_class' => 'raven-testimonial--skin-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Layout', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'image_inline',
				'options' => [
					'image_inline' => esc_html__( 'Image Inline', 'jupiterx-core' ),
					'image_stacked' => esc_html__( 'Image Stacked', 'jupiterx-core' ),
					'image_above' => esc_html__( 'Image Above', 'jupiterx-core' ),
					'image_left' => esc_html__( 'Image Left', 'jupiterx-core' ),
					'image_right' => esc_html__( 'Image Right', 'jupiterx-core' ),
				],
				'prefix_class' => 'raven-testimonial--layout-',
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'raven-testimonial-%s-align-',
			]
		);

		$this->end_injection();

		$this->start_controls_section(
			'section_skin_style',
			[
				'label' => esc_html__( 'Bubble', 'jupiterx-core' ),
				'tab' => 'style',
				'condition' => [
					'skin' => 'bubble',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'alpha' => false,
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__content, {{WRAPPER}} .raven-testimonial__content:after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '20',
					'bottom' => '20',
					'left' => '20',
					'right' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}}.raven-testimonial--layout-image_left .raven-testimonial__footer,
					{{WRAPPER}}.raven-testimonial--layout-image_right .raven-testimonial__footer' => 'padding-top: {{TOP}}{{UNIT}}',
					'{{WRAPPER}}.raven-testimonial--layout-image_above .raven-testimonial__footer,
					{{WRAPPER}}.raven-testimonial--layout-image_inline .raven-testimonial__footer,
					{{WRAPPER}}.raven-testimonial--layout-image_stacked .raven-testimonial__footer' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'switcher',
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__content, {{WRAPPER}} .raven-testimonial__content:after' => 'border-style: solid',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__content' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .raven-testimonial__content:after' => 'border-color: transparent {{VALUE}} {{VALUE}} transparent',
				],
				'condition' => [
					'border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'border_width',
			[
				'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__content, {{WRAPPER}} .raven-testimonial__content:after' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.raven-testimonial--layout-image_stacked .raven-testimonial__content:after,
					{{WRAPPER}}.raven-testimonial--layout-image_inline .raven-testimonial__content:after' => 'margin-top: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.raven-testimonial--layout-image_above .raven-testimonial__content:after' => 'margin-bottom: -{{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'border' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_controls_section_content_image_styles() {
		$this->start_injection( [
			'at' => 'before',
			'of' => 'section_navigation',
		] );

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'content_gap',
			[
				'label' => esc_html__( 'Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.raven-testimonial--layout-image_inline .raven-testimonial__footer:not(.raven-testimonial-content-template-footer),
					{{WRAPPER}}.raven-testimonial--layout-image_stacked .raven-testimonial__footer:not(.raven-testimonial-content-template-footer)' => 'margin-top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.raven-testimonial--layout-image_above .raven-testimonial__footer:not(.raven-testimonial-content-template-footer)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.raven-testimonial--layout-image_left .raven-testimonial__footer:not(.raven-testimonial-content-template-footer)' => 'padding-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.raven-testimonial--layout-image_right .raven-testimonial__footer:not(.raven-testimonial-content-template-footer)' => 'padding-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__text:not(.raven-testimonial-content-template)' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .raven-testimonial__text:not(.raven-testimonial-content-template)',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} .raven-testimonial__text:not(.raven-testimonial-content-template)',
			]
		);

		$this->add_control(
			'name_title_style',
			[
				'label' => esc_html__( 'Name', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'global' => RavenUtils::set_default_value( 'primary' ),
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .raven-testimonial__name',
			]
		);

		$this->add_control(
			'heading_title_style',
			[
				'label' => esc_html__( 'Title', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .raven-testimonial__title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => esc_html__( 'Image', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label' => esc_html__( 'Size', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__image img' => 'width: {{SIZE}}{{UNIT}};height: auto;',
					'{{WRAPPER}}.raven-testimonial--layout-image_left .raven-testimonial__content:after,
		 			{{WRAPPER}}.raven-testimonial--layout-image_right .raven-testimonial__content:after' => 'top: calc( {{text_padding.TOP}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px );',
					'body:not(.rtl) {{WRAPPER}}.raven-testimonial--layout-image_stacked:not(.raven-testimonial--align-center):not(.raven-testimonial--align-right) .raven-testimonial__content:after,
					 body:not(.rtl) {{WRAPPER}}.raven-testimonial--layout-image_inline:not(.raven-testimonial--align-center):not(.raven-testimonial--align-right) .raven-testimonial__content:after,
					 {{WRAPPER}}.raven-testimonial--layout-image_stacked.raven-testimonial--align-left .raven-testimonial__content:after,
					 {{WRAPPER}}.raven-testimonial--layout-image_inline.raven-testimonial--align-left .raven-testimonial__content:after' => 'left: calc( {{text_padding.LEFT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); right:auto;',
					'body.rtl {{WRAPPER}}.raven-testimonial--layout-image_stacked:not(.raven-testimonial--align-center):not(.raven-testimonial--align-left) .raven-testimonial__content:after,
					 body.rtl {{WRAPPER}}.raven-testimonial--layout-image_inline:not(.raven-testimonial--align-center):not(.raven-testimonial--align-left) .raven-testimonial__content:after,
					 {{WRAPPER}}.raven-testimonial--layout-image_stacked.raven-testimonial--align-right .raven-testimonial__content:after,
					 {{WRAPPER}}.raven-testimonial--layout-image_inline.raven-testimonial--align-right .raven-testimonial__content:after' => 'right: calc( {{text_padding.RIGHT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); left:auto;',
					'body:not(.rtl) {{WRAPPER}}.raven-testimonial--layout-image_above:not(.raven-testimonial--align-center):not(.raven-testimonial--align-right) .raven-testimonial__content:after,
					 {{WRAPPER}}.raven-testimonial--layout-image_above.raven-testimonial--align-left .raven-testimonial__content:after' => 'left: calc( {{text_padding.LEFT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); right:auto;',
					'body.rtl {{WRAPPER}}.raven-testimonial--layout-image_above:not(.raven-testimonial--align-center):not(.raven-testimonial--align-left) .raven-testimonial__content:after,
					 {{WRAPPER}}.raven-testimonial--layout-image_above.raven-testimonial--align-right .raven-testimonial__content:after' => 'right: calc( {{text_padding.RIGHT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); left:auto;',
				],
			]
		);

		$this->add_responsive_control(
			'image_gap',
			[
				'label' => esc_html__( 'Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'body.rtl {{WRAPPER}}.raven-testimonial--layout-image_inline.raven-testimonial--align-left .raven-testimonial__image + cite,
		 			body.rtl {{WRAPPER}}.raven-testimonial--layout-image_above.raven-testimonial--align-left .raven-testimonial__image + cite,
		 			body:not(.rtl) {{WRAPPER}}.raven-testimonial--layout-image_inline .raven-testimonial__image + cite,
		 			body:not(.rtl) {{WRAPPER}}.raven-testimonial--layout-image_above .raven-testimonial__image + cite' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',

					'body:not(.rtl) {{WRAPPER}}.raven-testimonial--layout-image_inline.raven-testimonial--align-right .raven-testimonial__image + cite,
		 			body:not(.rtl) {{WRAPPER}}.raven-testimonial--layout-image_above.raven-testimonial--align-right .raven-testimonial__image + cite,
		 			body.rtl {{WRAPPER}}.raven-testimonial--layout-image_inline .raven-testimonial__image + cite,
		 			body.rtl {{WRAPPER}}.raven-testimonial--layout-image_above .raven-testimonial__image + cite' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left:0;',

					'{{WRAPPER}}.raven-testimonial--layout-image_stacked .raven-testimonial__image + cite,
		 			{{WRAPPER}}.raven-testimonial--layout-image_left .raven-testimonial__image + cite,
		 			{{WRAPPER}}.raven-testimonial--layout-image_right .raven-testimonial__image + cite' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'image_border',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'switcher',
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__image img' => 'border-style: solid',
				],
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__image img' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_width',
			[
				'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__image img' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'slider',
				'default' => [
					'size' => 100,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-testimonial__image img' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->end_injection();

	}

	private function register_controls_section_rating() {
		$this->start_injection( [
			'at' => 'after',
			'of' => 'lazyload',
		] );

		$this->add_control(
			'rating_heading',
			[
				'label'     => esc_html__( 'Rating', 'jupiterx-core' ),
				'type'      => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'rating_star_style',
			[
				'label' => esc_html__( 'Stars Style', 'jupiterx-core' ),
				'type'  => 'choose',
				'label_block' => false,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Solid', 'jupiterx-core' ),
						'icon'  => 'eicon-star',
					],
					'outline' => [
						'title' => esc_html__( 'Outline', 'jupiterx-core' ),
						'icon'  => 'eicon-star-o',
					],
				],
				'default' => 'outline',
			]
		);

		$this->add_control(
			'rating_active_star_style',
			[
				'label' => esc_html__( 'Active Stars Style', 'jupiterx-core' ),
				'type'  => 'choose',
				'label_block' => false,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Solid', 'jupiterx-core' ),
						'icon'  => 'eicon-star',
					],
					'outline' => [
						'title' => esc_html__( 'Outline', 'jupiterx-core' ),
						'icon'  => 'eicon-star-o',
					],
				],
				'default' => 'solid',
			]
		);

		$this->end_injection();
	}

	private function register_controls_section_rating_style() {
		$this->start_controls_section(
			'section_rating_style',
			[
				'label' => esc_html__( 'Rating', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'stars_unmarked_color',
			[
				'label' => esc_html__( 'Stars', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .elementor-star-rating i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'stars_color',
			[
				'label' => esc_html__( 'Active Stars', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .elementor-star-rating i.active' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'star_size',
			[
				'label' => esc_html__( 'Size', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'star_space',
			[
				'label' => esc_html__( 'Spacing', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .elementor-star-rating i:not(:last-of-type)' => 'margin-right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .elementor-star-rating i:not(:last-of-type)' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'stars_margin',
			[
				'label' => esc_html__( 'Margin', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-star-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_repeater_controls( Repeater $repeater ) {
		$repeater->add_control(
			'content_type',
			[
				'label' => esc_html__( 'Content Type', 'jupiterx-core' ),
				'type' => 'select',
				'default' => '',
				'options' => [
					'' => esc_html__( 'Content', 'jupiterx-core' ),
					'template' => esc_html__( 'Saved Template', 'jupiterx-core' ),
				],
			]
		);

		$repeater->add_control(
			'content',
			[
				'label' => esc_html__( 'Content', 'jupiterx-core' ),
				'type' => 'textarea',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'content_type' => '',
				],
			]
		);

		$repeater->add_control(
			'template_id',
			[
				'label' => esc_html__( 'Choose a template', 'jupiterx-core' ),
				'type' => 'raven_query',
				'options' => [],
				'label_block' => true,
				'multiple' => false,
				'query' => [
					'source' => Control_Query::QUERY_SOURCE_TEMPLATE,
					'template_types' => [
						'section',
					],
				],
				'default' => false,
				'condition' => [
					'content_type' => 'template',
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Image', 'jupiterx-core' ),
				'type' => 'media',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => esc_html__( 'Name', 'jupiterx-core' ),
				'type' => 'text',
				'default' => esc_html__( 'John Doe', 'jupiterx-core' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'jupiterx-core' ),
				'type' => 'text',
				'default' => esc_html__( 'CEO', 'jupiterx-core' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'rating',
			[
				'label'   => esc_html__( 'Rating', 'jupiterx-core' ),
				'type'    => 'select',
				'default' => 0,
				'options' => [
					'0' => esc_html__( 'Hidden', 'jupiterx-core' ),
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
				],
			]
		);
	}

	protected function register_injections() {
		$this->start_injection( [
			'of' => 'pagination_size',
		] );

		$this->add_responsive_control(
			'pagination_gap',
			[
				'label' => esc_html__( 'Gap', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'pagination!' => [ 'progressbar', '' ],
				],
				'frontend_available' => true,
				'render_type' => 'template',
			]
		);

		$this->end_injection();
	}

	protected function get_repeater_defaults() {
		$placeholder_image_src = Utils::get_placeholder_image_src();

		return [
			[
				'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'jupiterx-core' ),
				'name' => esc_html__( 'John Doe', 'jupiterx-core' ),
				'title' => esc_html__( 'CEO', 'jupiterx-core' ),
				'image' => [
					'url' => $placeholder_image_src,
				],
			],
			[
				'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'jupiterx-core' ),
				'name' => esc_html__( 'John Doe', 'jupiterx-core' ),
				'title' => esc_html__( 'CEO', 'jupiterx-core' ),
				'image' => [
					'url' => $placeholder_image_src,
				],
			],
			[
				'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'jupiterx-core' ),
				'name' => esc_html__( 'John Doe', 'jupiterx-core' ),
				'title' => esc_html__( 'CEO', 'jupiterx-core' ),
				'image' => [
					'url' => $placeholder_image_src,
				],
			],
		];
	}

	protected function stars_icon( $settings, $active = false ) {
		$icon = '&#xE934;';

		if ( ( ! $active && 'outline' === $settings['rating_star_style'] ) || ( $active && 'outline' === $settings['rating_active_star_style'] ) ) {
			$icon = '&#xE933;';
		}

		return $icon;
	}

	protected function render_stars( $slide, $settings ) {
		$icon        = $this->stars_icon( $settings );
		$icon_active = $this->stars_icon( $settings, true );
		$rating      = $slide['rating'];
		$stars_html  = '';

		for ( $stars = 1; $stars <= 5; $stars++ ) {
			if ( $stars <= $rating ) {
				$stars_html .= sprintf( '<i class="elementor-star-empty active">%s</i>', $icon_active );
			} else {
				$stars_html .= sprintf( '<i class="elementor-star-empty">%s</i>', $icon );
			}
		}

		$output_stars_html = sprintf( '<div class="elementor-star-rating">%s</div>', wp_kses_post( $stars_html ) );

		return $output_stars_html;
	}

	private function print_cite( $slide, $location ) {
		if ( empty( $slide['name'] ) && empty( $slide['title'] ) ) {
			return '';
		}

		$skin              = $this->get_settings( 'skin' );
		$layout            = 'bubble' === $skin ? 'image_inline' : $this->get_settings( 'layout' );
		$locations_outside = [ 'image_above', 'image_right', 'image_left' ];
		$locations_inside  = [ 'image_inline', 'image_stacked' ];

		$print_outside = ( 'outside' === $location && in_array( $layout, $locations_outside, true ) );
		$print_inside  = ( 'inside' === $location && in_array( $layout, $locations_inside, true ) );

		$html = '';
		if ( $print_outside || $print_inside ) {
			$html = '<cite class="raven-testimonial__cite">';
			if ( ! empty( $slide['name'] ) ) {
				$html .= '<span class="raven-testimonial__name">' . esc_html( $slide['name'] ) . '</span>';
			}
			if ( ! empty( $slide['title'] ) ) {
				$html .= '<span class="raven-testimonial__title">' . esc_html( $slide['title'] ) . '</span>';
			}
			if ( ! empty( $slide['rating'] ) ) {
				$html .= $this->render_stars( $slide, $this->get_settings() );
			}
			$html .= '</cite>';
		}

		// PHPCS - the main text of a widget should not be escaped.
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Check content is shortcode dynamic tag.
	 *
	 * @since 4.6.9
	 * @return boolean|null
	 */
	protected function check_shortocde_dynamic_content( $slide ) {
		if ( ! isset( $slide['__dynamic__'] ) ) {
			return;
		}

		if ( empty( $slide['__dynamic__']['content'] ) ) {
			return;
		}

		$content = $slide['__dynamic__']['content'];

		$tag_text_data = Plugin::$instance->dynamic_tags->tag_text_to_tag_data( $content );

		if ( isset( $tag_text_data['name'] ) && 'shortcode' === $tag_text_data['name'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Render item content.
	 *
	 * @param array $slide Slide data.
	 * @since 4.6.9
	 * @return string
	 */
	protected function render_item_content( $slide ) {
		if ( empty( $slide['content_type'] ) ) {
			$is_shortcode_tag = $this->check_shortocde_dynamic_content( $slide );

			$content = esc_html( $slide['content'] );

			if ( $is_shortcode_tag ) {
				$content = $slide['content'];
			}

			return $content;
		}

		if ( empty( $slide['template_id'] ) ) {
			return '';
		}

		$id = (int) $slide['template_id'];

		return Plugin::$instance->frontend->get_builder_content_for_display( $id, true );
	}

	protected function print_slide( array $slide, array $settings, $element_key ) {
		$lazyload = 'yes' === $this->get_settings( 'lazyload' );

		// WP Rocket compatibility.
		if ( function_exists( 'get_rocket_option' ) ) {
			$lazy_loading_enabled = get_rocket_option( 'lazyload' );

			if ( $lazy_loading_enabled ) {
				$lazyload = true;
			}
		}

		$this->add_render_attribute( $element_key . '-testimonial', [
			'class' => 'raven-testimonial',
		] );

		if ( ! empty( $slide['image']['url'] ) ) {
			$img_src              = $this->get_slide_image_url( $slide, $settings );
			$img_attribute['src'] = $img_src;

			if ( $lazyload ) {
				$img_attribute['class']    = 'swiper-lazy';
				$img_attribute['data-src'] = $img_src;

				unset( $img_attribute['src'] );
			}

			$img_attribute['alt'] = $this->get_slide_image_alt_attribute( $slide );

			$this->add_render_attribute( $element_key . '-image', $img_attribute );
		}

		$content      = $this->render_item_content( $slide );
		$content_type = ! empty( $slide['content_type'] ) ? 'raven-testimonial-content-template' : 'raven-testimonial-content-text';
		?>
		<div <?php $this->print_render_attribute_string( $element_key . '-testimonial' ); ?>>
			<?php if ( ! empty( $content ) ) : ?>
				<div class="raven-testimonial__content">
					<div class="raven-testimonial__text <?php echo esc_attr( $content_type ); ?>">
						<?php Utils::print_unescaped_internal_string( $content ); ?>
					</div>
					<?php $this->print_cite( $slide, 'outside' ); ?>
				</div>
			<?php endif; ?>
			<div class="raven-testimonial__footer <?php echo esc_attr( $content_type . '-footer' ); ?>">
				<?php if ( $slide['image']['url'] ) : ?>
					<div class="raven-testimonial__image">
						<img <?php $this->print_render_attribute_string( $element_key . '-image' ); ?>>
						<?php if ( $lazyload ) : ?>
							<div class="swiper-lazy-preloader"></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php $this->print_cite( $slide, 'inside' ); ?>
			</div>
		</div>
		<?php
	}

	protected function render() {
		$this->print_slider();
	}
}
