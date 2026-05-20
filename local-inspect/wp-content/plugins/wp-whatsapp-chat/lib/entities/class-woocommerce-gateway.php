<?php
namespace QuadLayers\QLWAPP\Entities;

use QuadLayers\WP_Orm\Entity\SingleEntity;

/**
 * WooCommerce Gateway Entity
 * Define la estructura de datos para el gateway de pago WhatsApp
 */
class WooCommerce_Gateway extends SingleEntity {

	// Configuración del gateway
	public $enabled      = 'no';
	public $title        = '';
	public $description  = '';
	public $instructions = '';

	// Configuración del mensaje WhatsApp
	public $message = '';
	public $phone   = QLWAPP_PHONE_NUMBER;
	public $type    = 'phone';
	public $group   = QLWAPP_GROUP_LINK;

	// Estado de la orden
	public $order_status = 'pending';

	// Configuración de redirección
	public $auto_redirect  = 'yes';
	public $redirect_delay = 2; // segundos

	// Visibilidad
	public $visibility = 'all'; // all, registered, guest

	// Iconos y diseño
	public $icon = 'qlwapp-whatsapp-icon';

	// WhatsApp link type
	public $whatsapp_link_type = 'web';

	public function __construct() {
		$this->title        = esc_html__( 'WhatsApp Order', 'wp-whatsapp-chat' );
		$this->description  = esc_html__( 'Complete your order via WhatsApp. You will be redirected to WhatsApp to confirm payment details.', 'wp-whatsapp-chat' );
		$this->instructions = esc_html__( 'Please complete your payment via WhatsApp. We will confirm your order once payment is received.', 'wp-whatsapp-chat' );
		$this->message      = esc_html__(
			'Hello! I\'ve just placed order #{ORDER_ID} on {SITE_TITLE}.' . "\n\n" .
			'Order Details:' . "\n" .
			'- Total: {ORDER_TOTAL}' . "\n" .
			'- Date: {ORDER_DATE}' . "\n" .
			'- Customer: {CUSTOMER_NAME}' . "\n\n" .
			'Products:' . "\n" . '{ORDER_PRODUCTS}' . "\n\n" .
			'Shipping Address:' . "\n" . '{SHIPPING_ADDRESS}' . "\n\n" .
			'Please confirm and provide payment instructions.' . "\n\n" .
			'Order Link: {ORDER_URL}',
			'wp-whatsapp-chat'
		);
	}
}
