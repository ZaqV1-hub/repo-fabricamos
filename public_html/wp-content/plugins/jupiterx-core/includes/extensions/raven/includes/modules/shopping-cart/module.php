<?php
namespace JupiterX_Core\Raven\Modules\Shopping_Cart;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Module_base;
use Elementor\Plugin as Elementor;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Module extends Module_Base {

	/**
	 * Rendered mini cart.
	 *
	 * @var string
	 * @since 4.4.0
	 */
	private static $renderd_mini_cart;

	public static function is_active() {
		return function_exists( 'WC' ) && defined( 'JUPITERX_VERSION' ) && defined( 'JUPITERX_API' );
	}

	public function get_widgets() {
		return [ 'shopping-cart' ];
	}

	public function __construct() {
		parent::__construct();

		add_filter( 'woocommerce_locate_template', [ $this, 'woocommerce_locate_template' ], 99, 2 );

		// update cart count using ajax while adding items from cart using add to cart button or removing them from quick cart.
		add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'menu_cart_fragments' ] );
		add_action( 'wp_ajax_raven_shopping_cart_single_insert_to_cart', [ $this, 'ajax_add_to_cart' ] );
		add_action( 'wp_ajax_nopriv_raven_shopping_cart_single_insert_to_cart', [ $this, 'ajax_add_to_cart' ] );

		// Update shopping cart in cart and checkout page.
		add_action( 'wp_ajax_cart_checkout_page_shopping_cart_fragments', [ $this, 'cart_checkout_page_shopping_cart_fragments' ] );
		add_action( 'wp_ajax_nopriv_cart_checkout_page_shopping_cart_fragments', [ $this, 'cart_checkout_page_shopping_cart_fragments' ] );
	}

	/**
	 * Override the mini cart template.
	 *
	 * @param string $template template.
	 * @param string $template_name template name.
	 * @return string
	 * @since 4.4.0
	 */
	public function woocommerce_locate_template( $template, $template_name ) {
		if ( 'cart/mini-cart.php' !== $template_name ) {
			return $template;
		}

		$has_raven_shopping_cart = filter_input( INPUT_POST, 'has_raven_shopping_cart', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( empty( $has_raven_shopping_cart ) ) {
			return $template;
		}

		$plugin_path = jupiterx_core()->plugin_dir() . 'includes/extensions/raven/includes/modules/shopping-cart/template/mini-cart.php';

		if ( file_exists( $plugin_path ) ) {
			$template = $plugin_path;
		}

		return $template;
	}

	public function ajax_add_to_cart() {
		check_ajax_referer( 'jupiterx-core-raven', 'nonce' );
		wc_nocache_headers();

		$product_id        = filter_var( wp_unslash( $_REQUEST['raven-add-to-cart'] ), FILTER_SANITIZE_NUMBER_INT ); // phpcs:ignore
		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
		$was_added_to_cart = false;
		$url               = false;
		$adding_to_cart    = wc_get_product( $product_id );
		$quantity          = filter_input( INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT );

		if ( ! $adding_to_cart ) {
			return;
		}

		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->get_type(), $adding_to_cart );
		$was_added_to_cart   = $this->handle_was_added_to_cart( $add_to_cart_handler, $product_id, $url );

		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && 0 === wc_notice_count( 'error' ) ) {
			wc_clear_notices();
			$data = [];

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( [ $product_id => $quantity ], true );
			}

			ob_start();

			woocommerce_mini_cart();

			$mini_cart = ob_get_clean();

			$data = [
				'fragments' => apply_filters(
					'woocommerce_add_to_cart_fragments',
					[
						'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
					]
				),
				'cart_hash' => WC()->cart->get_cart_hash(),
			];

			wp_send_json_success( $data );
		}

		wp_send_json_success( [
			'error' => true,
			'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
		] );
	}

	/**
	 * Handle was added to cart.
	 *
	 * @since 4.2.0
	 *
	 * @param string $add_to_cart_handler handler.
	 * @param int $product_id id.
	 * @param int $url url.
	 * @return void
	 */
	private function handle_was_added_to_cart( $add_to_cart_handler, $product_id, $url ) {
		if ( 'variable' === $add_to_cart_handler || 'variation' === $add_to_cart_handler ) {
			return self::add_to_cart_handler_variable( $product_id );
		}

		if ( 'grouped' === $add_to_cart_handler ) {
			return self::add_to_cart_handler_grouped( $product_id );
		}

		if ( has_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler ) ) {
			do_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler, $url );
			return;
		}

		return self::add_to_cart_handler_simple( $product_id );
	}

	/**
	 * Add simple products to the cart by WooCommerce native way.
	 *
	 * @since 4.2.0
	 */
	private static function add_to_cart_handler_simple( $product_id ) {
		// Nonce validation is not required, nonce already validated at the top level.
		$quantity          = filter_var( wp_unslash( $_REQUEST['quantity'] ), FILTER_SANITIZE_NUMBER_INT );// phpcs:ignore
		$quantity          = empty( $quantity ) ? 1 : wc_stock_amount( $quantity );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );

			return true;
		}

		return false;
	}

	/**
	 * Add grouped products to the cart by WooCommerce native way.
	 *
	 * @since 4.2.0
	 */
	private static function add_to_cart_handler_grouped( $product_id ) {
		$was_added_to_cart = false;
		$added_to_cart     = array();
		$items             = isset( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) ? wp_unslash( $_REQUEST['quantity'] ) : array(); // phpcs:ignore

		if ( ! empty( $items ) ) {
			$quantity_set = false;

			foreach ( $items as $item => $quantity ) {
				$quantity = wc_stock_amount( $quantity );
				if ( $quantity <= 0 ) {
					continue;
				}
				$quantity_set = true;

				// Add to cart validation.
				$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $item, $quantity );

				// Suppress total recalculation until finished.
				remove_action( 'woocommerce_add_to_cart', array( WC()->cart, 'calculate_totals' ), 20, 0 );

				if ( $passed_validation && false !== WC()->cart->add_to_cart( $item, $quantity ) ) {
					$was_added_to_cart      = true;
					$added_to_cart[ $item ] = $quantity;
				}

				add_action( 'woocommerce_add_to_cart', array( WC()->cart, 'calculate_totals' ), 20, 0 );
			}

			if ( ! $was_added_to_cart && ! $quantity_set ) {
				wc_add_notice( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'jupiterx-core' ), 'error' );
				return false;
			}

			wc_add_to_cart_message( $added_to_cart );
			WC()->cart->calculate_totals();
			return true;
		} elseif ( $product_id ) {
			/* Link on product archives */
			wc_add_notice( __( 'Please choose a product to add to your cart&hellip;', 'jupiterx-core' ), 'error' );
		}

		return false;
	}

	/**
	 * Add variable products to the cart by WooCommerce native way.
	 *
	 * @since 4.2.0
	 */
	private static function add_to_cart_handler_variable( $product_id ) {
		// Nonce is not required, nonce already validate at top level.
		$variation_id = filter_var( wp_unslash( $_REQUEST['variation_id'] ), FILTER_SANITIZE_NUMBER_INT );// phpcs:ignore
		$variation_id = empty( $variation_id ) ? '' : absint( $variation_id );
		$quantity     = filter_var( wp_unslash( $_REQUEST['quantity'] ), FILTER_SANITIZE_NUMBER_INT );// phpcs:ignore
		$quantity     = empty( $quantity ) ? 1 : wc_stock_amount( $quantity );
		$variations   = array();
		$product      = wc_get_product( $product_id );

		foreach ( $_REQUEST as $key => $value ) { // phpcs:ignore
			if ( 'attribute_' !== substr( $key, 0, 10 ) ) {
				continue;
			}

			$variations[ sanitize_title( wp_unslash( $key ) ) ] = wp_unslash( $value );
		}

		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

		if ( ! $passed_validation ) {
			return false;
		}

		// Prevent parent variable product from being added to cart.
		if ( empty( $variation_id ) && $product && $product->is_type( 'variable' ) ) {
			/* translators: 1: product link, 2: product name */
			wc_add_notice( sprintf( __( 'Please choose product options by visiting <a href="%1$s" title="%2$s">%2$s</a>.', 'jupiterx-core' ), esc_url( get_permalink( $product_id ) ), esc_html( $product->get_name() ) ), 'error' );

			return false;
		}

		if ( false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
			return true;
		}

		return false;
	}

	public static function render_mini_cart() {
		if ( ! empty( self::$renderd_mini_cart ) ) {
			return self::$renderd_mini_cart;
		}

		ob_start();

		jupiterx_core()->load_files(
			[
				'extensions/raven/includes/modules/shopping-cart/template/mini-cart',
			]
		);

		$template_path = jupiterx_core()->plugin_dir() . 'extensions/raven/includes/modules/shopping-cart/template';

		get_template_part( $template_path, 'mini-cart', [] );

		self::$renderd_mini_cart = ob_get_clean();

		return self::$renderd_mini_cart;
	}

	public function menu_cart_fragments( $fragments ) {
		$has_raven_shopping_cart = filter_input( INPUT_POST, 'raven_shopping_cart', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( empty( $has_raven_shopping_cart ) ) {
			return $fragments;
		}

		$has_cart = is_a( WC()->cart, 'WC_Cart' );

		$fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . self::render_mini_cart() . '</div>';

		if ( ! $has_cart ) {
			return $fragments;
		}

		$product_count = WC()->cart->get_cart_contents_count();

		ob_start();
		?>
		<span class="raven-shopping-cart-count"><?php echo wp_kses_post( $product_count ); ?></span>
		<?php
		$cart_count_html = ob_get_clean();

		if ( ! empty( $cart_count_html ) ) {
			$fragments['body:not(.elementor-editor-active) div.elementor-element.elementor-widget.elementor-widget-raven-shopping-cart .raven-shopping-cart-count'] = $cart_count_html;
		}

		return $fragments;
	}

	/**
	 * Shopping cart fragments for cart & checkout page.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	public function cart_checkout_page_shopping_cart_fragments() {
		$all_fragments = [];

		$templates = filter_input( INPUT_POST, 'templates', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! is_array( $templates ) ) {
			wp_send_json( [] );
		}

		foreach ( $templates as $id ) {
			$this->get_all_fragments( $id, $all_fragments );
		}

		wp_send_json( [ 'fragments' => $all_fragments ] );
	}

	/**
	 * Get All Fragments.
	 *
	 * @since 4.2.0
	 *
	 * @param $all_fragments
	 * @return void
	 */
	public function get_all_fragments( $id, &$all_fragments ) {
		$fragments_in_document = $this->get_fragments_in_document( $id );

		if ( $fragments_in_document ) {
			$all_fragments += $fragments_in_document;
		}
	}

	/**
	 * Get Fragments In Document.
	 *
	 * A general function that will return any needed fragments for a Post.
	 *
	 * @since 4.2.0
	 * @access public
	 *
	 * @return mixed $fragments
	 */
	public function get_fragments_in_document( $id ) {
		$document = Elementor::$instance->documents->get( $id );

		if ( ! is_object( $document ) ) {
			return false;
		}

		$fragments = [];

		$data = $document->get_elements_data();

		Elementor::$instance->db->iterate_data(
			$data,
			$this->get_fragments_handler( $fragments )
		);

		return ! empty( $fragments ) ? $fragments : false;
	}

	/**
	 * Get Fragments Handler.
	 *
	 * @since 4.2.0
	 *
	 * @param array $fragments
	 * @return void
	 */
	public function get_fragments_handler( array &$fragments ) {
		return function ( $element ) use ( &$fragments ) {
			if ( ! isset( $element['widgetType'] ) ) {
				return;
			}

			$fragment_data = $this->get_fragment_data( $element );

			if ( empty( $fragment_data ) ) {
				return;
			}

			$fragments = $fragment_data;
		};
	}

	/**
	 * Get Fragment Data.
	 *
	 * A function that will return the selector and HTML for WC fragments.
	 *
	 * @since 4.2.0
	 * @access private
	 *
	 * @param array $element
	 *
	 * @return array $fragment_data
	 */
	private function get_fragment_data( $element ) {
		$fragment_data = [];

		if ( 'raven-shopping-cart' === $element['widgetType'] ) {
			$has_cart = is_a( WC()->cart, 'WC_Cart' );

			$fragment_data['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . self::render_mini_cart() . '</div>';

			if ( ! $has_cart ) {
				return $fragment_data;
			}

			$product_count = WC()->cart->get_cart_contents_count();

			ob_start();
			?>
			<span class="raven-shopping-cart-count"><?php echo wp_kses_post( $product_count ); ?></span>
			<?php
			$cart_count_html = ob_get_clean();

			if ( ! empty( $cart_count_html ) ) {
				$fragment_data['body:not(.elementor-editor-active) div.elementor-element.elementor-widget.elementor-widget-raven-shopping-cart .raven-shopping-cart-count'] = $cart_count_html;
			}
		}

		return $fragment_data;
	}
}
