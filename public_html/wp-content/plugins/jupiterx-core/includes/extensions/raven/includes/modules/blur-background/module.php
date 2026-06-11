<?php

namespace JupiterX_Core\Raven\Modules\Blur_Background;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Element_Base;
use JupiterX_Core\Raven\Base\Module_Base;

defined( 'ABSPATH' ) || die();

class Module extends Module_Base {
	public function __construct() {
		parent::__construct();

		add_action( 'elementor/element/section/section_background/before_section_end', [ $this, 'extend_element_background_controls' ], 20 );
		add_action( 'elementor/element/container/section_background/before_section_end', [ $this, 'extend_element_background_controls' ], 20 );

		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render' ] );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'before_render' ] );

		// Inject the blur class into the Marionette wrapper in the editor,
		// since `before_render` only runs on the PHP frontend render path
		// and sections/containers are rendered client-side in the editor.
		add_action( 'elementor/section/print_template', [ $this, 'print_template' ], 10, 2 );
		add_action( 'elementor/container/print_template', [ $this, 'print_template' ], 10, 2 );
	}

	public function get_name() {
		return 'blur-background';
	}

	public function extend_element_background_controls( Controls_Stack $element ) {
		self::extend_existing_background_group(
			$element,
			'background',
			'{{WRAPPER}}'
		);
	}

	public static function extend_existing_background_group( Controls_Stack $controls_stack, $prefix, $selector ) {
		$type_control_name = "{$prefix}_background";
		$controls          = $controls_stack->get_controls();

		if ( empty( $controls[ $type_control_name ] ) ) {
			return;
		}

		$options = empty( $controls[ $type_control_name ]['options'] ) ? [] : $controls[ $type_control_name ]['options'];

		if ( empty( $options['blur'] ) ) {
			$options['blur'] = [
				'title' => esc_html__( 'Blur', 'jupiterx-core' ),
				'icon'  => 'fa fa-adjust',
			];

			$controls_stack->update_control(
				$type_control_name,
				[
					'options' => $options,
				]
			);
		}

		$controls_stack->start_injection(
			[
				'at' => 'after',
				'of' => $type_control_name,
			]
		);

		self::add_blur_controls( $controls_stack, $prefix, $selector );

		$controls_stack->end_injection();
	}

	public static function add_blur_controls( Controls_Stack $controls_stack, $prefix, $selector ) {
		$type_control_name = "{$prefix}_background";
		$condition         = [
			$type_control_name => 'blur',
		];

		// Emit the full blur visual effect (base rules + ::before/::after pseudo-elements)
		// through Elementor's Dynamic CSS so the effect works identically in the editor
		// and on the frontend, regardless of whether the `.jupiterx-blur-background`
		// class has been injected onto the wrapper element.
		$controls_stack->add_control(
			"{$prefix}_blur_enabled",
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => $condition,
				'selectors' => [
					$selector => 'background: transparent !important; background-color: transparent !important; background-image: none !important; position: relative; isolation: isolate; --jupiterx-blur-amount: 14; --jupiterx-blur-tint-color: #FFFFFF; --jupiterx-blur-tint-opacity: 18; --jupiterx-blur-fallback-color: rgba(255,255,255,0.90);',
					$selector . '::before' => 'content: ""; position: absolute; inset: 0; pointer-events: none; border-radius: inherit; z-index: 0; background-color: transparent; -webkit-backdrop-filter: blur( calc( var( --jupiterx-blur-amount, 14 ) * 1px ) ); backdrop-filter: blur( calc( var( --jupiterx-blur-amount, 14 ) * 1px ) );',
					$selector . '::after' => 'content: ""; position: absolute; inset: 0; pointer-events: none; border-radius: inherit; z-index: 1; background-color: var( --jupiterx-blur-tint-color, #FFFFFF ); opacity: calc( var( --jupiterx-blur-tint-opacity, 18 ) / 100 );',
					$selector . ' > *' => 'position: relative; z-index: 2;',
				],
			]
		);

		$controls_stack->add_control(
			"{$prefix}_blur_amount",
			[
				'label' => esc_html__( 'Blur Amount', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 14,
				],
				'frontend_available' => true,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'condition' => $condition,
				'selectors' => [
					$selector => '--jupiterx-blur-amount: {{SIZE}};',
				],
			]
		);

		$controls_stack->add_control(
			"{$prefix}_blur_tint_color",
			[
				'label' => esc_html__( 'Tint Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'frontend_available' => true,
				'condition' => $condition,
				'selectors' => [
					$selector => '--jupiterx-blur-tint-color: {{VALUE}};',
				],
			]
		);

		$controls_stack->add_control(
			"{$prefix}_blur_tint_opacity",
			[
				'label' => esc_html__( 'Tint Opacity', 'jupiterx-core' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 18,
				],
				'frontend_available' => true,
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'condition' => $condition,
				'selectors' => [
					$selector => '--jupiterx-blur-tint-opacity: {{SIZE}};',
				],
			]
		);

		$controls_stack->add_control(
			"{$prefix}_blur_fallback_color",
			[
				'label' => esc_html__( 'Fallback Color', 'jupiterx-core' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(255,255,255,0.90)',
				'frontend_available' => true,
				'condition' => $condition,
				'selectors' => [
					$selector => '--jupiterx-blur-fallback-color: {{VALUE}};',
				],
			]
		);

		$controls_stack->add_control(
			"{$prefix}_blur_disable_on_mobile",
			[
				'label' => esc_html__( 'Disable On Mobile', 'jupiterx-core' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'jupiterx-core' ),
				'label_off' => esc_html__( 'No', 'jupiterx-core' ),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'condition' => $condition,
				'selectors' => [
					'(mobile)' . $selector . '::before' => 'display: none;',
					'(mobile)' . $selector . '::after' => 'display: none;',
				],
			]
		);
	}

	public static function is_blur_enabled( array $settings, $prefix = 'background' ) {
		return isset( $settings[ "{$prefix}_background" ] ) && 'blur' === $settings[ "{$prefix}_background" ];
	}

	public static function add_render_attributes( Element_Base $element, $attribute, array $settings, $prefix = 'background' ) {
		if ( ! self::is_blur_enabled( $settings, $prefix ) ) {
			return;
		}

		$classes = [ 'jupiterx-blur-background' ];

		if ( ! empty( $settings[ "{$prefix}_blur_disable_on_mobile" ] ) ) {
			$classes[] = 'jupiterx-blur-background--mobile-disabled';
		}

		$element->add_render_attribute( $attribute, 'class', $classes );
	}

	public function before_render( Element_Base $element ) {
		self::add_render_attributes( $element, '_wrapper', $element->get_settings_for_display() );
	}

	/**
	 * Inject the blur class into the element wrapper from the editor's Marionette template.
	 *
	 * Sections and containers are rendered client-side inside the Elementor editor, so the
	 * PHP `before_render` hook above never runs there. This mirrors what `Animated_Gradient`
	 * does: we prepend a Marionette block that calls `view.addRenderAttribute` on `_wrapper`
	 * whenever `settings.background_background` is `blur`. The pseudo-element styling is
	 * already emitted via the control's `selectors` above, so this exists purely to keep the
	 * DOM class-list in the editor consistent with what the frontend renders.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function print_template( $template, $widget ) {
		if ( ! $template ) {
			return $template;
		}

		ob_start();
		?>
		<# if ( 'blur' === settings.background_background ) {
			view.addRenderAttribute( '_wrapper', 'class', 'jupiterx-blur-background' );

			if ( 'yes' === settings.background_blur_disable_on_mobile ) {
				view.addRenderAttribute( '_wrapper', 'class', 'jupiterx-blur-background--mobile-disabled' );
			}
		} #>
		<?php
		$blur_template = ob_get_clean();

		return $blur_template . $template;
	}
}
