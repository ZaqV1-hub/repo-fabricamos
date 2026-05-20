<?php

namespace JupiterX_Core\Raven\Modules\Inline_Svg\Widgets;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Utils;

/**
 * Inline SVG.
 *
 * @since 2.5.9
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Inline_Svg extends Base_Widget {
	/**
	 * Open link tag.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $link_open = '';

	/**
	 * Close link tag.
	 *
	 * @since 4.0.0
	 * @var string
	 */
	public $link_close = '';

	public function get_name() {
		return 'raven-inline-svg';
	}

	public function get_title() {
		return esc_html__( 'Inline SVG', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-inline-svg';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			[
				'label' => esc_html__( 'SVG', 'jupiterx-core' ),
			]
		);

		$this->content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'svg_style',
			[
				'label' => esc_html__( 'SVG', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->style_controls();

		$this->end_controls_section();
	}

	private function content_controls() {
		$this->add_control(
			'svg',
			[
				'label' => esc_html__( 'SVG', 'jupiterx-core' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [ 'svg' ],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'URL', 'jupiterx-core' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'Paste URL or type', 'jupiterx-core' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);
	}

	private function style_controls() {
		$this->add_control(
			'custom_width',
			[
				'label' => esc_html__( 'Use Custom Width', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Makes SVG responsive and allows to change its width.', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'aspect_ratio',
			[
				'label' => esc_html__( 'Use Aspect Ratio', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'custom_width' => 'yes',
				],
				'description' => esc_html__( 'This option allows your SVG item to be scaled up exactly as your bitmap image, at the same time saving its width compared to the height.', 'jupiterx-core' ),
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => esc_html__( 'Width', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'selectors' => [
					'{{WRAPPER}} #jupiterx-inline-svg-wrapper .svg-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'custom_width' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => esc_html__( 'Height', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'selectors' => [
					'{{WRAPPER}} #jupiterx-inline-svg-wrapper svg' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'custom_width' => 'yes',
					'aspect_ratio!' => 'yes',
				],
			]
		);

		$this->add_control(
			'remove_inline_css',
			[
				'label' => esc_html__( 'Remove Inline CSS', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__( 'Use this option to delete the inline styles in the loaded SVG.', 'jupiterx-core' ),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .jupiterx-inline-svg-wrapper' => 'justify-content: {{VALUE}};',
				],
			]
		);
	}

	/**
	 * Prepare link attributes.
	 *
	 * @param array $settings widget settings.
	 * @since 2.5.9
	 */
	private function manage_link( $settings ) {
		if ( method_exists( $this, 'add_link_attributes' ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		} else {
			$this->add_render_attribute( 'link', 'href', $settings['link']['url'] );

			if ( 'on' === $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}

			if ( 'on' === $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'link', 'rel', 'nofollow' );
			}
		}

		$this->link_open  = '<a ' . $this->get_render_attribute_string( 'link' ) . ' >';
		$this->link_close = '</a>';
	}

	protected function render() {
		$settings         = $this->get_settings_for_display();
		$svg              = $settings['svg'];
		$this->link_open  = '';
		$this->link_close = '';

		if ( empty( $svg['url'] ) ) {
			return;
		}

		// WPML compatibility.
		$svg['id'] = apply_filters( 'wpml_object_id', $svg['id'], 'attachment', true );

		if ( ! empty( $svg['id'] ) ) {
			$attachment_url = wp_get_attachment_image_src( $svg['id'], 'full' );

			if ( $attachment_url ) {
				$svg['url'] = $attachment_url[0];
			}
		}

		// Security check: Validate URL and file extension
		if ( ! filter_var( $svg['url'], FILTER_VALIDATE_URL ) || pathinfo( $svg['url'], PATHINFO_EXTENSION ) !== 'svg' ) {
			return;
		}

		// Security check: Get file content safely
		$response = wp_remote_get( $svg['url'] );
		if ( is_wp_error( $response ) ) {
			return;
		}

		$svg_content = wp_remote_retrieve_body( $response );

		if ( ! $svg_content ) {
			return;
		}

		// Sanitize SVG content
		$svg_content = jupiterx_svg_sanitizer( $svg_content );

		$classes = [ 'svg-wrapper' ];

		if ( isset( $settings['aspect_ratio'] ) && 'yes' === $settings['aspect_ratio'] ) {
			$classes[] = 'jupiterx-svg-with-auto-aspect';
		}

		if ( ! isset( $settings['aspect_ratio'] ) || 'yes' !== $settings['aspect_ratio'] ) {
			$svg_content = str_replace( '<svg', '<svg preserveAspectRatio="none" ', $svg_content );
		}

		if ( isset( $settings['remove_inline_css'] ) && 'yes' === $settings['remove_inline_css'] ) {
			$svg_content = preg_replace( '[style\s*?=\s*?"\s*?.*?\s*?"]', '', $svg_content );
		}

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->manage_link( $settings );
		}

		// Check if custom-width is not set and SVG has no width, we set a default width for it.
		if ( ! isset( $settings['custom_width'] ) || 'yes' !== $settings['custom_width'] ) {
			$classes = $this->check_svg_width( $svg_content, $classes );
		}

		$this->add_render_attribute(
			'inner_wrapper',
			[
				'class' => $classes,
			]
		);

		?>
			<div class="jupiterx-inline-svg-wrapper" id="jupiterx-inline-svg-wrapper">
				<?php echo $this->link_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div <?php echo $this->get_render_attribute_string( 'inner_wrapper' ); ?>>
					<?php Utils::print_unescaped_internal_string( $svg_content ); ?>
				</div>
				<?php echo $this->link_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php
	}

	/**
	 * Checks if svg tag has a default width attribute.
	 *
	 * @param string $svg SVG html.
	 * @param array  $classes array of classes.
	 * @since 2.5.9
	 */
	private function check_svg_width( $svg, $classes ) {
		if ( ! $svg ) {
			return $classes;
		}

		$svg_       = simplexml_load_string( $svg );
		$attributes = $svg_->attributes();

		if ( empty( (string) $attributes->width ) ) {
			$classes[] = 'jupiterx-inline-svg-default-svg-width';
		}

		return $classes;
	}

}
