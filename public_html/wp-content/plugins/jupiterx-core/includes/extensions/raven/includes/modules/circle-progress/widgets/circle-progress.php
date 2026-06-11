<?php

namespace JupiterX_Core\Raven\Modules\Circle_Progress\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use JupiterX_Core\Raven\Utils;
use Elementor\Utils as ElementorUtils;

defined( 'ABSPATH' ) || die();

class Circle_Progress extends Base_Widget {
	public function get_name() {
		return 'raven-circle-progress';
	}

	public function get_title() {
		return esc_html__( 'Circle Progress', 'jupiterx-core' );
	}


	public function get_icon() {
		return 'raven-element-icon raven-element-icon-circle-progress';
	}

	public function get_script_depends() {
		return [
			'jquery-numerator',
		];
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_additional_options_controls();

		$this->register_circle_progres_style();
		$this->register_content_style();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'type',
			[
				'label' => esc_html__( 'Type', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'percent',
				'options' => [
					'percent' => esc_html__( 'Percent', 'jupiterx-core' ),
					'custom' => esc_html__( 'Custom', 'jupiterx-core' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'compeleted_progress',
			[
				'label' => esc_html__( 'Compeleted Progress', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => '30',
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'type' => 'percent',
				],
				'frontend_available' => true,
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'custom_compeleted_progress',
			[
				'label'   => esc_html__( 'Compeleted Progress', 'jupiterx-core' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 30,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'type' => 'custom',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'custom_total_progress',
			[
				'label'   => esc_html__( 'Total Progress', 'jupiterx-core' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 100,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'type' => 'custom',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'separate_Ttousands',
			[
				'label' => esc_html__( 'Separate Thousands', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'type' => 'custom',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'prefix',
			[
				'label'       => esc_html__( 'Number Prefix', 'jupiterx-core' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => '+',
			]
		);

		$this->add_control(
			'suffix',
			[
				'label'       => esc_html__( 'Number Suffix', 'jupiterx-core' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '%',
				'placeholder' => '+',
			]
		);

		$this->add_control(
			'duration',
			[
				'label'   => esc_html__( 'Animation Duration', 'jupiterx-core' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1000,
				'min'     => 100,
				'step'    => 100,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function register_additional_options_controls() {
		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => esc_html__( 'Additional Options', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'jupiterx-core' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Here is a title.', 'jupiterx-core' ),
				'default' => esc_html__( 'Development', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label' => esc_html__( 'Subtitle', 'jupiterx-core' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Some more title here.', 'jupiterx-core' ),
				'default' => esc_html__( 'HTML and CSS', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'number_position',
			[
				'label' => esc_html__( 'Progress Number Position', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'default' => 'inside',
				'options' => [
					'inside' => esc_html__( 'Inside of Circle', 'jupiterx-core' ),
					'outside' => esc_html__( 'Outside of Circle', 'jupiterx-core' ),
				],
			]
		);

		$this->add_control(
			'title_subtitle_position',
			[
				'label' => esc_html__( 'Title & Subtitle Position', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'default' => 'outside',
				'options' => [
					'inside' => esc_html__( 'Inside of Circle', 'jupiterx-core' ),
					'outside' => esc_html__( 'Outside of Circle', 'jupiterx-core' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_circle_progres_style() {
		$this->start_controls_section(
			'section_circle_progress_style',
			[
				'label' => esc_html__( 'Progress Circle', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'circle_size',
			[
				'label' => esc_html__( 'Circle Size', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 600,
						'step' => 1,
					],
				],
				'default' => [
					'size' => '300',
				],
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-bar-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-circle-progress-bar' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .raven-circle-progress-content-inside' => 'height: {{SIZE}}{{UNIT}};',

				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'progress_circle_thickness',
			[
				'label' => esc_html__( 'Progress Circle Thickness', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'size' => '15',
				],
				'frontend_available' => true,
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'progress_indicator_thickness',
			[
				'label' => esc_html__( 'Progress Indicator Thickness', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'size' => '15',
				],
				'frontend_available' => true,
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'progress_endings',
			[
				'label' => esc_html__( 'Progress Endings', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'round',
				'options' => [
					'round' => esc_html__( 'Rounded', 'jupiterx-core' ),
					'butt' => esc_html__( 'Flat', 'jupiterx-core' ),
				],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-value' => 'stroke-linecap: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'circle_background_color',
			[
				'label' => esc_html__( 'Circle Background Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-meter' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'progress_circle_color',
			[
				'label' => esc_html__( 'Progress Circle Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E7EEEE',
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-meter'  => 'stroke: {{VALUE}};',
				],
				'frontend_available' => 'true',
			]
		);

		$this->add_control(
			'progress_indicator_color',
			[
				'label' => esc_html__( 'Progress Indicator Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => Utils::set_old_default_value( '#6EC1E4' ),
				'global' => Utils::set_default_value( 'primary' ),
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-value'  => 'stroke: {{VALUE}};',
				],
				'frontend_available' => 'true',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'circle_box_shadow',
				'label' => esc_html__( 'Circle Box Shadow', 'jupiterx-core' ),
				'selector' => '{{WRAPPER}} .raven-circle-progress-bar',
			]
		);

		$this->end_controls_section();
	}

	protected function register_content_style() {
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content Style', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->register_content_numbers_style();
		$this->register_content_prefix_style();
		$this->register_content_suffix_style();
		$this->register_content_title_style();
		$this->register_content_subtitle_style();

		$this->end_controls_section();
	}

	protected function register_content_numbers_style() {
		$this->add_control(
			'numbers_heading',
			[
				'label' => esc_html__( 'Numbers', 'jupiterx-core' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'numbers_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => Utils::set_old_default_value( '#6EC1E4' ),
				'global' => Utils::set_default_value( 'accent' ),
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-counter > *' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'numbers_typography',
				'selector' => '{{WRAPPER}} .raven-circle-progress-counter',
			]
		);

		$this->add_responsive_control(
			'numbers_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-counter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}

	protected function register_content_prefix_style() {
		$this->add_control(
			'prefix_heading',
			[
				'label' => esc_html__( 'Prefix', 'jupiterx-core' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'prefix_font_size',
			[
				'label' => esc_html__( 'Font Size', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [
					'px',
					'em',
					'rem',
					'custom',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-counter .raven-circle-progress-counter-prefix' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'prefix_gap',
			[
				'label' => esc_html__( 'Gap (px)', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-counter .raven-circle-progress-counter-prefix' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'prefix_alignment',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'flex-start' => [
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
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-counter .raven-circle-progress-counter-prefix' => 'align-self: {{VALUE}};',
				],
			]
		);

	}

	protected function register_content_suffix_style() {
		$this->add_control(
			'suffix_heading',
			[
				'label' => esc_html__( 'Suffix', 'jupiterx-core' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'suffix_font_size',
			[
				'label' => esc_html__( 'Font Size', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [
					'px',
					'em',
					'rem',
					'custom',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-counter .raven-circle-progress-counter-suffix' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'suffix_gap',
			[
				'label' => esc_html__( 'Gap (px)', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-counter .raven-circle-progress-counter-suffix' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'suffix_alignment',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'flex-start' => [
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
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-counter .raven-circle-progress-counter-suffix' => 'align-self: {{VALUE}};',
				],
			]
		);

	}

	protected function register_content_title_style() {
		$this->add_control(
			'title_heading',
			[
				'label' => esc_html__( 'Title', 'jupiterx-core' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => Utils::set_old_default_value( '#141414' ),
				'global' => Utils::set_default_value( 'primary' ),
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .raven-circle-progress-title',
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '30',
					'bottom' => '10',
					'left' => '0',
					'right' => '0',
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}

	protected function register_content_subtitle_style() {
		$this->add_control(
			'subtitle_heading',
			[
				'label' => esc_html__( 'Subtitle', 'jupiterx-core' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => Utils::set_old_default_value( '#8C8C8C' ),
				'global' => Utils::set_default_value( 'text' ),
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .raven-circle-progress-subtitle',
			]
		);

		$this->add_responsive_control(
			'subtitle_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .raven-circle-progress-subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'content-container',
			'class',
			'raven-circle-progress-container'
		);

		?>
		<div <?php echo $this->get_render_attribute_string( 'content-container' ); ?>>
			<div class="raven-circle-progress-bar-wrapper" >
				<?php ElementorUtils::print_unescaped_internal_string( $this->render_progress_bar( $settings ) ); ?>
				<?php if ( 'inside' === $settings['number_position'] || 'inside' === $settings['title_subtitle_position'] ) : ?>
					<div class="raven-circle-progress-content-inside" >
						<div class="raven-circle-progress-content-inside-wrapper">
							<?php if ( 'inside' === $settings['number_position'] ) : ?>
								<?php $this->render_counter( $settings ); ?>
							<?php endif; ?>
							<?php if ( 'inside' === $settings['title_subtitle_position'] ) : ?>
								<?php $this->render_title_subtitle( $settings ); ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( 'outside' === $settings['number_position'] || 'outside' === $settings['title_subtitle_position'] ) : ?>
				<div class="raven-circle-progress-content-outside" >
					<?php if ( 'outside' === $settings['number_position'] ) : ?>
						<?php $this->render_counter( $settings ); ?>
					<?php endif; ?>
					<?php if ( 'outside' === $settings['title_subtitle_position'] ) : ?>
						<?php $this->render_title_subtitle( $settings ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render progress bar.
	 *
	 * @param Array @settings Array of element settings.
	 * @since 3.7.0
	 */
	protected function render_progress_bar( $settings ) {
		$size   = ! empty( $settings['circle_size']['size'] ) ? $settings['circle_size']['size'] : 300;
		$radius = $size / 2;
		$center = $radius;

		$circle_thickness    = ! empty( $settings['progress_circle_thickness'] ) ? $settings['progress_circle_thickness']['size'] : 15;
		$indicator_thickness = ! empty( $settings['progress_indicator_thickness'] ) ? $settings['progress_indicator_thickness']['size'] : 15;

		$thickness = $circle_thickness >= $indicator_thickness ? $circle_thickness : $indicator_thickness;
		$radius    = $radius - ( $thickness / 2 );

		$value           = $this->get_progress_value( $settings );
		$compelete       = ! empty( $settings['custom_compeleted_progress'] ) ? $settings['custom_compeleted_progress'] : 30;
		$value_compelete = 'custom' === $settings['type'] ? $compelete : $value;

		$circumference = 2 * M_PI * $radius;

		if ( 0 === $value && 'custom' === $settings['type'] ) {
			$value_compelete = 0;
			$compelete       = 0;
		}

		$this->add_render_attribute( 'progress-bar', [
			'class' => [
				'raven-circle-progress-bar',
			],
			'width' => $size,
			'height' => $size,
			'viewBox' => '0 0 ' . esc_attr( $size ) . ' ' . esc_attr( $size ),
			'radius' => esc_attr( $radius ),
			'circumference' => esc_attr( $circumference ),
			'value' => esc_attr( $value ),
		] );

		$svg_attribute_string = $this->get_render_attribute_string( 'progress-bar' );

		return '<svg ' . $svg_attribute_string . '>
			<circle
				class="raven-circle-progress-meter"
				cx="' . esc_attr( $center ) . '"
				cy="' . esc_attr( $center ) . '"
				r="' . esc_attr( $radius ) . '"
				stroke="' . esc_attr( $settings['progress_circle_color'] ) . '"
				stroke-width="' . esc_attr( $circle_thickness ) . '"
				fill="none"
			/>
			<circle
				class="raven-circle-progress-value"
				cx="' . esc_attr( $center ) . '"
				cy="' . esc_attr( $center ) . '"
				r="' . esc_attr( $radius ) . '"
				stroke="' . esc_attr( $settings['progress_indicator_color'] ) . '"
				stroke-width="' . esc_attr( $indicator_thickness ) . '"
				style="stroke-dasharray: ' . esc_attr( $circumference ) . 'px;stroke-dashoffset: ' . esc_attr( $circumference ) . 'px;"
				data-value="' . esc_attr( $value_compelete ) . '"
				fill="none"
			/>
		</svg>';
	}

	/**
	 * Get current value for progress counter and bar.
	 *
	 * @param Array  @settings Array of element settings.
	 * @return Number
	 * @since 3.7.0
	 */
	protected function get_progress_value( $settings ) {
		if ( 'percent' === $settings['type'] ) {
			return 100 <= $settings['compeleted_progress']['size'] ? 100 : $settings['compeleted_progress']['size'];
		}

		$compelete = ! empty( $settings['custom_compeleted_progress'] ) ? $settings['custom_compeleted_progress'] : 30;
		$total     = ! empty( $settings['custom_total_progress'] ) ? $settings['custom_total_progress'] : 100;

		if ( 0 === $total || 0 === $compelete ) {
			return 0;
		}

		if ( $compelete > $total ) {
			return 0;
		}

		return round( ( ( absint( $compelete ) * 100 ) / absint( $total ) ), 0 );
	}

	/**
	 * Render title and subtitle.
	 *
	 * @param Array @settings Array of element settings.
	 * @since 3.7.0
	 */
	protected function render_title_subtitle( $settings ) {
		$title    = $settings['title'];
		$subtitle = $settings['subtitle'];

		if ( empty( $title ) && empty( $subtitle ) ) {
			return;
		}

		$html = '<div class="raven-circle-progress-content-wrapper">';

		if ( ! empty( $title ) ) {
			$html .= '<div class="raven-circle-progress-title">' . wp_kses_post( $title ) . '</div>';
		}

		if ( ! empty( $subtitle ) ) {
			$html .= '<div class="raven-circle-progress-subtitle">' . wp_kses_post( $subtitle ) . '</div>';
		}

		$html .= '</div>';

		ElementorUtils::print_unescaped_internal_string( $html );
	}

	/**
	 * Render progress counter.
	 *
	 * @param Array @settings Array of element settings.
	 * @since 3.7.0
	 */
	protected function render_counter( $settings ) {
		$type   = $settings['type'];
		$prefix = $this->render_prefix_suffix( 'prefix', $settings );
		$suffix = $this->render_prefix_suffix( 'suffix', $settings );

		$this->add_render_attribute( 'progress-counter', [
			'class' => [
				'raven-circle-progress-counter',
				'raven-circle-progress-counter-' . esc_attr( $type ),
			],
		] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'progress-counter' ); ?>>
			<?php echo wp_kses_post( $prefix ); ?>
			<div class="raven-circle-progress-counter-number">0</div>
			<?php echo wp_kses_post( $suffix ); ?>
		</div>
		<?php
	}

	/**
	 * Render number prefix and suffix.
	 *
	 * @param String $type     Index of the suffix or prefix.
	 * @param Array  @settings Array of element settings.
	 * @since 3.7.0
	 */
	protected function render_prefix_suffix( $type, $settings ) {
		if ( empty( $settings[ $type ] ) ) {
			return '';
		}

		return '<div class="raven-circle-progress-counter-' . esc_attr( $type ) . '">' . $settings[ $type ] . '</div>';
	}
}
