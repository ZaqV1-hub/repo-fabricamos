<?php
/**
 * Layout Builder condition generator.
 *
 * @package JupiterX_Core\Control_Panel_2
 */

class JupiterX_Core_Condition_Generator {
	/**
	 * Class instance.
	 *
	 * @since 4.0.0
	 * @var JupiterX_Core_Condition_Generator Class instance.
	 */
	private static $instance = null;

	/**
	 * Get a class instance.
	 *
	 * @since 4.0.0
	 *
	 * @return JupiterX_Core_Condition_Generator Class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get conditon by template key.
	 *
	 * @since 4.0.0
	 * @return array
	 */
	public function get_condition( $key ) {
		if ( ! method_exists( $this, $key ) ) {
			return [];
		}

		$conditions = $this->$key();

		if ( $conditions['is_multi'] ) {
			return [
				'conditions' => $conditions['conditions'],
				'rule_string' => $conditions['rule_string'],
				'is_multi' => $conditions['is_multi'],
			];
		}

		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => $conditions['conditions']['conditionB'],
				'conditionC' => $conditions['conditions']['conditionC'],
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => $conditions['rule_string'],
			'is_multi' => $conditions['is_multi'],
		];
	}

	/**
	 * Update template condition meta on layout builder.
	 *
	 * @param integer $id         Current template id.
	 * @param boolean $is_multi   Check if template has multi conditions.
	 * @param array   $conditions Template condition list.
	 * @since 4.0.0
	 */
	public function update_condition_meta( $id, $is_multi, $conditions ) {
		if ( ! $is_multi ) {
			update_post_meta( $id, 'jupiterx-condition-rules', [
				0 => [
					'conditionA' => $conditions['conditionA'],
					'conditionB' => $conditions['conditionB'],
					'conditionC' => $conditions['conditionC'],
					'conditionD' => $conditions['conditionD'],
				],
			] );

			return;
		}

		$new_conditions = [];

		foreach ( $conditions as $key => $condition ) {
			$new_conditions[ $key ] = [
				'conditionA' => $condition['conditionA'],
				'conditionB' => $condition['conditionB'],
				'conditionC' => $condition['conditionC'],
				'conditionD' => $condition['conditionD'],
			];
		}

		update_post_meta( $id, 'jupiterx-condition-rules', $new_conditions );
	}

	/**
	 * Get template type and title.
	 *
	 * @param string $title         Tempate title.
	 * @param string $key           Tempate key.
	 * @param string $template_type Tempate type.
	 */
	public function get_layout_builder_template_data( $title, $key, $template_type ) {
		if ( 'single_product' === $key ) {
			$template_type = 'product';
		}

		if ( 'product_archive' === $key || 'product_category' === $key ) {
			$template_type = 'product-archive';

			if ( 'product_archive' === $key ) {
				$title = 'Shop Archive';
			}
		}

		if (
			'order_received' === $key ||
			'404_page' === $key ||
			'my_account' === $key ||
			'cart' === $key ||
			'checkout' === $key
		) {
			$template_type = 'single';
		}

		if ( 'search' === $key ) {
			$template_type = 'archive';
		}

		return [
			'title' => $title,
			'type' => $template_type,
		];
	}

	/**
	 * Handle blog template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function blog_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'archive',
				'conditionC' => 'single_post',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'Post Archives', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle shop template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function shop_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'woocommerce',
				'conditionC' => 'shop_archive',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'Shop page', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle single post template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function single_post_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'singular',
				'conditionC' => 'single_post',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'All Posts', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle archive post template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function archive_post_condition() {
		return [
			'conditions' => [
				0 => [
					'conditionA' => 'include',
					'conditionB' => 'archive',
					'conditionC' => 'post_in_category',
					'conditionD' => [
						0 => 'all',
						1 => 'All',
					],
				],
				1 => [
					'conditionA' => 'include',
					'conditionB' => 'archive',
					'conditionC' => 'post_in_post_tag',
					'conditionD' => [
						0 => 'all',
						1 => 'All',
					],
				],
			],
			'rule_string' => esc_html__( 'All category archive, All post tag archives', 'jupiterx-core' ),
			'is_multi' => true,
		];
	}

	/**
	 * Handle single protfolio template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function single_portfolio_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'singular',
				'conditionC' => 'single_portfolio',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'All Portfolios', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle archive protfolio template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function archive_portfolio_condition() {
		return [
			'conditions' => [
				0 => [
					'conditionA' => 'include',
					'conditionB' => 'archive',
					'conditionC' => 'portfolio',
					'conditionD' => [
						0 => 'all',
						1 => 'All',
					],
				],
				1 => [
					'conditionA' => 'include',
					'conditionB' => 'archive',
					'conditionC' => 'portfolio@portfolio_category',
					'conditionD' => [
						0 => 'all',
						1 => 'All',
					],
				],
				2 => [
					'conditionA' => 'include',
					'conditionB' => 'archive',
					'conditionC' => 'portfolio@portfolio_tag',
					'conditionD' => [
						0 => 'all',
						1 => 'All',
					],
				],
			],
			'rule_string' => esc_html__( 'Portfolios Archive, All archive of the Portfolio Categories, All archive of the Portfolio Tags', 'jupiterx-core' ),
			'is_multi' => true,
		];
	}

	/**
	 * Handle search template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function search_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'archive',
				'conditionC' => 'search',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'Search result', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle 404 page template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function not_found_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'singular',
				'conditionC' => 'error_404',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( '404 Page', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle single product template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function single_product_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'woocommerce',
				'conditionC' => 'single_product',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'All products', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle product archive template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function product_archive_condition() {
		return [
			'conditions' => [
				0 => [
					'conditionA' => 'include',
					'conditionB' => 'woocommerce',
					'conditionC' => 'all_product_archive',
					'conditionD' => [
						0 => 'all',
						1 => 'All',
					],
				],
			],
			'rule_string' => esc_html__( 'All product archive', 'jupiterx-core' ),
			'is_multi' => true,
		];
	}

	/**
	 * Handle product archive template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function product_category_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'woocommerce',
				'conditionC' => 'product_cat_archive',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'Product category archive', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle my account template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function my_account_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'woocommerce',
				'conditionC' => 'my-account-user',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'User my account page', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle cart template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function cart_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'woocommerce',
				'conditionC' => 'cart-page',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'Cart page', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle checkout template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function checkout_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'woocommerce',
				'conditionC' => 'checkout-page',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'Checkout page', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}

	/**
	 * Handle order received template condition.
	 *
	 * @since 4.0.0
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function order_received_condition() {
		return [
			'conditions' => [
				'conditionA' => 'include',
				'conditionB' => 'woocommerce',
				'conditionC' => 'thankyou-page',
				'conditionD' => [
					0 => 'all',
					1 => 'All',
				],
			],
			'rule_string' => esc_html__( 'Order received page', 'jupiterx-core' ),
			'is_multi' => false,
		];
	}
}
