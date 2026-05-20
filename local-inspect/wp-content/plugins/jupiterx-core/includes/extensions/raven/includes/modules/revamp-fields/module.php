<?php
namespace JupiterX_Core\Raven\Modules\Revamp_Fields;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Module_base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

class Module extends Module_Base {

	/**
	 * Construct.
	 *
	 * @since 3.8.0
	 */
	public function __construct() {
		if ( ! jupiterx_core()->check_default_settings() ) {
			return;
		}

		add_action( 'elementor/element/after_section_end', [ $this, 'inject_revamp_fields' ], 10, 2 );
		add_action( 'elementor/editor/after_save', [ $this, 'update_saved_meta_field' ], 10, 1 );

		// Header controls.
		add_action( 'elementor/element/before_section_end', [ $this, 'add_header_controls' ], 10, 2 );

		// Add elementor settings to jupiterx header.
		add_filter( 'jupiterx_header_settings', [ $this, 'add_header_settings' ] );
	}

	public function add_header_settings( $data ) {
		$current_header_id   = apply_filters( 'layout_builder_template_id', 0 );
		$current_header_data = get_post_meta( $current_header_id, '_elementor_page_settings', true );

		if ( empty( $current_header_data ) ) {
			return $data;
		}

		foreach ( $current_header_data as $key => $value ) {
			if ( ! empty( $value['size'] ) ) {
				$value = $value['size'];
			}

			$data[ 'elementor_' . $key ] = $value;
		}

		return $data;
	}

	public function update_saved_meta_field( $post_id ) {
		$post_settings = get_post_meta( $post_id, '_elementor_page_settings', true );

		if ( empty( $post_settings ) ) {
			return;
		}

		$this->update_background_meta( $post_settings, $post_id );
		$this->update_margin_meta( $post_settings, $post_id );
		$this->update_padding_meta( $post_settings, $post_id );
	}

	/**
	 * Update post background meta field.
	 *
	 * @param array $post_settings Array of elementor settings.
	 * @param int   $post_id       Current headers id.
	 * @since 3.8.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function update_background_meta( $post_settings, $post_id ) {
		$background_type = empty( $post_settings['content_style_background_background'] ) ? '' : $post_settings['content_style_background_background'];

		if ( 'classic' !== $background_type ) {
			return;
		}

		$background_color    = ! empty( $post_settings['content_style_background_color'] ) ? $post_settings['content_style_background_color'] : '';
		$background_image    = ! empty( $post_settings['content_style_background_image'] ) ? $post_settings['content_style_background_image'] : '';
		$background_repeat   = ! empty( $post_settings['content_style_background_repeat'] ) ? $post_settings['content_style_background_repeat'] : '';
		$background_position = ! empty( $post_settings['content_style_background_position'] ) ? $post_settings['content_style_background_position'] : '';

		if ( ! empty( $background_color ) ) {
			update_post_meta( $post_id, 'jupiterx_main_background_color', $background_color );
		}

		if ( ! empty( $background_image['id'] ) ) {
			update_post_meta( $post_id, 'jupiterx_main_background_image', $background_image['id'] );
		}

		if ( ! empty( $background_position ) ) {
			update_post_meta( $post_id, 'jupiterx_main_background_position', $background_position );
		}

		if ( ! empty( $background_repeat ) ) {
			update_post_meta( $post_id, 'jupiterx_main_background_repeat', $background_repeat );
		}
	}

	/**
	 * Update post margin meta field.
	 *
	 * @param array $post_settings Array of elementor settings.
	 * @param int   $post_id       Current headers id.
	 * @since 3.8.0
	 */
	public function update_margin_meta( $post_settings, $post_id ) {
		$margin_control = empty( $post_settings['content_style_margin'] ) ? '' : $post_settings['content_style_margin'];

		if ( empty( $margin_control ) ) {
			return;
		}

		$margin_values = [
			'top',
			'right',
			'bottom',
			'left',
		];

		foreach ( $margin_values as $value ) {
			$meta_name = 'jupiterx_main_spacing_margin_' . $value;

			if ( isset( $margin_control[ $value ] ) ) {
				update_post_meta( $post_id, $meta_name, $margin_control[ $value ] );
			}
		}
	}

	/**
	 * Update post padding meta field.
	 *
	 * @param array $post_settings Array of elementor settings.
	 * @param int   $post_id       Current headers id.
	 * @since 3.8.0
	 */
	public function update_padding_meta( $post_settings, $post_id ) {
		$padding_control = empty( $post_settings['content_style_padding'] ) ? '' : $post_settings['content_style_padding'];

		if ( empty( $padding_control ) ) {
			return;
		}

		$padding_values = [
			'top',
			'right',
			'bottom',
			'left',
		];

		foreach ( $padding_values as $value ) {
			$meta_name = 'jupiterx_main_spacing_padding_' . $value;

			if ( isset( $padding_control[ $value ] ) ) {
				update_post_meta( $post_id, $meta_name, $padding_control[ $value ] );
			}
		}
	}

	/**
	 * Inject new controls to page style.
	 *
	 * @param \Elementor\Controls_Stack $element    The element type.
	 * @param string                    $section_id Section ID.
	 * @since 3.8.0
	 */
	public function inject_revamp_fields( $element, $section_id ) {
		if ( 'section_page_style' !== $section_id ) {
			return;
		}

		$element->start_controls_section(
			'content_style',
			[
				'tab' => Controls_Manager::TAB_STYLE,
				'label' => esc_html__( 'Content Style', 'jupiterx-core' ),
			]
		);

		$element->add_responsive_control(
			'content_style_margin',
			[
				'label'      => esc_html__( 'Margin', 'jupiterx-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-main' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'content_style_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_style_background',
				'label'     => esc_html__( 'Background Type', 'jupiterx-core' ),
				'types'     => [ 'classic', 'gradient' ],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .jupiterx-main',
			]
		);

		$element->end_controls_section();
	}

	/**
	 * Inject new controls to header.
	 *
	 * @param \Elementor\Controls_Stack $element    The element type.
	 * @param string                    $section_id Section ID.
	 * @since 3.8.0
	 */
	public function add_header_controls( $element, $section_id ) {
		if ( 'header' !== $element->get_name() || 'document_settings' !== $section_id ) {
			return;
		}

		$document_id   = $element->get_main_id();
		$document_type = get_post_meta( $document_id, '_elementor_template_type', true );

		if ( 'header' !== $document_type ) {
			return;
		}

		$element->add_control(
			'header_overlay_content',
			[
				'label' => esc_html__( 'Overlay Content', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'false',
				'description' => esc_html__( 'To achieve a transparent header, it is necessary to enable this option, which ensures that the content following the header remains positioned beneath it.', 'jupiterx-core' ),
			]
		);

		$element->add_control(
			'header_behavior',
			[
				'label' => esc_html__( 'Header Behavior', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal' => esc_html__( 'Normal', 'jupiterx-core' ),
					'fixed' => esc_html__( 'Fixed', 'jupiterx-core' ),
					'sticky' => esc_html__( 'Sticky', 'jupiterx-core' ),
				],
			]
		);

		$element->add_control(
			'header_fixed_position',
			[
				'label' => esc_html__( 'Position', 'jupiterx-core' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => [
					'top' => esc_html__( 'Top', 'jupiterx-core' ),
					'bottom' => esc_html__( 'Bottom', 'jupiterx-core' ),
				],
				'condition' => [
					'header_behavior' => 'fixed',
				],
			]
		);

		$element->add_responsive_control(
			'header_sticky_offset',
			[
				'label' => esc_html__( 'Offset', 'jupiterx-core' ),
				'type' => 'slider',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition' => [
					'header_behavior' => 'sticky',
				],
			]
		);
	}
}
