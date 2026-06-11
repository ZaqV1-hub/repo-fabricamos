<?php
namespace JupiterX_Core\Raven\Modules\Product_Reviews\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use JupiterX_Core\Raven\Modules\Product_Reviews\Classes\Jupiterx_Product_Review_Content;
use JupiterX_Core\Raven\Utils;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

defined( 'ABSPATH' ) || die();

class Product_Reviews extends Base_Widget {
	public function get_name() {
		return 'raven-product-reviews';
	}

	public function get_title() {
		return __( 'Product Reviews', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-product-reviews';
	}

	public function get_categories() {
		return [ 'jupiterx-core-raven-woo-elements' ];
	}

	protected function register_controls() {
		$this->heading_style_controls();
		$this->primary_text_style_controls();
		$this->secondary_text_style_controls();
		$this->date_style_controls();
		$this->star_style_controls();
		$this->border_style_controls();
		$this->button_style_controls();
		$this->pagination_style_controls();
	}

	private function heading_style_controls() {
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Heading', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-header' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'heading_typography',
				'selector' => '{{WRAPPER}} .jupiterx-product-review-header',
			]
		);

		$this->end_controls_section();
	}

	private function primary_text_style_controls() {
		$this->start_controls_section(
			'style_section_primary_text',
			[
				'label' => esc_html__( 'Primary text', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'primary_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-form-subs > h5' => 'color: {{VALUE}}',
					'{{WRAPPER}} #jupiterx-product-review-widget .jupiterx-product-review-single-author' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'primary_text_typography',
				'selector' => '{{WRAPPER}} .jupiterx-product-review-form-subs > h5, #jupiterx-product-review-widget .jupiterx-product-review-single-author',
			]
		);

		$this->end_controls_section();
	}

	private function secondary_text_style_controls() {
		$this->start_controls_section(
			'style_section_secondary_text',
			[
				'label' => esc_html__( 'Secondary text', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'secondary_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} #jupiterx-product-review-widget .jx-product-review-secondary-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'secondary_text_typography',
				'selector' => '{{WRAPPER}} #jupiterx-product-review-widget .jx-product-review-secondary-text',
			]
		);

		$this->end_controls_section();
	}

	private function date_style_controls() {
		$this->start_controls_section(
			'style_section_date_text',
			[
				'label' => esc_html__( 'Date', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'date_text_color',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} #jupiterx-product-review-widget .jupiterx-product-review-single-date' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'date_text_typography',
				'selector' => '{{WRAPPER}} #jupiterx-product-review-widget .jupiterx-product-review-single-date',
			]
		);

		$this->end_controls_section();
	}

	private function star_style_controls() {
		$this->start_controls_section(
			'style_section_star',
			[
				'label' => esc_html__( 'Star', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'star_color',
			[
				'label' => esc_html__( 'Star Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-marked' => 'color: {{VALUE}}',
					'{{WRAPPER}} svg.jupiterx-product-review-marked' => 'fill: {{VALUE}}',
					'{{WRAPPER}} svg.jupiterx-product-review-marked use' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'empty_star_color',
			[
				'label' => esc_html__( 'Empty Star Color', 'jupiterx-core' ),
				'type' => 'color',
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-unmarked' => 'color: {{VALUE}}',
					'{{WRAPPER}} svg.jupiterx-product-review-unmarked' => 'fill: {{VALUE}}',
					'{{WRAPPER}} svg.jupiterx-product-review-unmarked use' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function border_style_controls() {
		$this->start_controls_section(
			'style_section_border',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Border Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} #jupiterx-product-review-widget input' => 'border-color: {{VALUE}} !important',
					'{{WRAPPER}} #jupiterx-product-review-widget .jupiterx-product-review-singles-wrapper' => 'border-color: {{VALUE}} !important',
					'{{WRAPPER}} #jupiterx-product-review-widget textarea' => 'border-color: {{VALUE}} !important',
					'{{WRAPPER}} #jupiterx-product-review-widget input[type=checkbox]' => 'border: 1px solid {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();
	}

	private function button_style_controls() {
		$this->start_controls_section(
			'style_section_button',
			[
				'label' => esc_html__( 'Button', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .jupiterx-product-review-submit-new',
			]
		);

		$this->add_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-submit-new' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->start_controls_tabs(
			'style_tabs'
		);

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'plugin-name' ),
			]
		);

		$this->add_control(
			'button_text_color_normal',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-submit-new' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->add_control(
			'button_bg_color_normal',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-submit-new' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'plugin-name' ),
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-submit-new:hover' => 'color: {{VALUE}} !important',
				],
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-submit-new:hover' => 'background-color: {{VALUE}} !important',
				],
			]
		);

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function pagination_style_controls() {
		$this->start_controls_section(
			'pagination_style_section',
			[
				'label' => esc_html__( 'Pagination', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'pagination_alignment',
			[
				'label' => esc_html__( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-pagination' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_gap',
			[
				'label' => esc_html__( 'Space Between', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-pagination .review-pagination-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'pagination_tabs' );

		$this->start_controls_tab(
			'pagination_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'pagination_background',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'pagination_typography',
				'selector' => '{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'pagination_color_hover',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'pagination_background_hover',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers:hover',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'pagination_typography_hover',
				'selector' => '{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_active_tab',
			[
				'label' => esc_html__( 'Active', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'pagination_color_active',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers.current' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'raven-background',
			[
				'name' => 'pagination_background_active',
				'exclude' => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => esc_html__( 'Background Color Type', 'jupiterx-core' ),
					],
					'color' => [
						'label' => esc_html__( 'Background Color', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers.current',
			]
		);

		$this->add_group_control(
			'typography',
			[
				'name' => 'pagination_typography_active',
				'selector' => '{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers.current',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pagination_border',
			[
				'label' => esc_html__( 'Border', 'jupiterx-core' ),
				'type' => 'heading',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'pagination_border_color',
			[
				'label' => esc_html__( 'Color', 'jupiterx-core' ),
				'type' => 'color',
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			'border',
			[
				'name' => 'pagination_border_color_width',
				'placeholder' => '1px',
				'exclude' => [ 'color' ],
				'fields_options' => [
					'width' => [
						'label' => esc_html__( 'Border Width', 'jupiterx-core' ),
					],
				],
				'selector' => '{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers',
			]
		);

		$this->add_control(
			'pagination_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'jupiterx-core' ),
				'type' => 'dimensions',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_padding',
			[
				'label' => esc_html__( 'Padding', 'jupiterx-core' ),
				'type' => 'dimensions',
				'separator' => 'before',
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .jupiterx-product-review-pagination .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		jupiterx_core()->load_files( [
			'extensions/raven/includes/modules/product-reviews/classes/reviews',
		] );

		$settings = $this->get_settings_for_display();
		Utils::get_product();

		global $product;

		if ( empty( $product ) ) {
			echo '<div class="elementor-alert elementor-alert-danger">' . sprintf(
				esc_html__( 'Current page is not a product page.', 'jupiterx-core' )
			) . '</div>';

			return;
		}

		$review = new Jupiterx_Product_Review_Content( $product->get_id() );
		?>
			<div id="jupiterx-product-review-widget" class="jupiterx-product-review-widget-wrapper">
				<?php
					$review->comments();
					$review->form();
				?>
			</div>
		<?php
	}
}
