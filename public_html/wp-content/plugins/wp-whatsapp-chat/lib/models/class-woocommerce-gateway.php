<?php
namespace QuadLayers\QLWAPP\Models;

use QuadLayers\QLWAPP\Entities\WooCommerce_Gateway as WooCommerceGateway_Entity;
use QuadLayers\WP_Orm\Builder\SingleRepositoryBuilder;

/**
 * WooCommerce Gateway Model
 * Maneja la persistencia de datos del gateway
 */
class WooCommerce_Gateway {

	protected static $instance;
	protected $repository;

	public function __construct() {
		// Registrar filtro de sanitización
		add_filter( 'sanitize_option_qlwapp_woocommerce_gateway', 'wp_unslash' );

		// Construir repositorio
		$builder = ( new SingleRepositoryBuilder() )
			->setTable( 'qlwapp_woocommerce_gateway' )
			->setEntity( WooCommerceGateway_Entity::class );

		$this->repository = $builder->getRepository();
	}

	/**
	 * Obtener nombre de tabla
	 */
	public function get_table() {
		return $this->repository->getTable();
	}

	/**
	 * Obtener configuración del gateway
	 *
	 * @return array Configuración con variables reemplazadas en frontend
	 */
	public function get() {
		$entity = $this->repository->find();
		$result = null;

		if ( $entity ) {
			$result = $entity->getProperties();
		} else {
			$admin  = new WooCommerceGateway_Entity();
			$result = $admin->getProperties();
		}

		// Solo reemplazar variables en frontend
		$is_rest_admin = defined( 'REST_REQUEST' ) && REST_REQUEST && is_user_logged_in();
		if ( ! is_admin() && ! $is_rest_admin ) {
			$result['title']        = qlwapp_replacements_vars( $result['title'] );
			$result['description']  = qlwapp_replacements_vars( $result['description'] );
			$result['instructions'] = qlwapp_replacements_vars( $result['instructions'] );
			$result['message']      = qlwapp_replacements_vars( $result['message'] );
		}

		return $result;
	}

	/**
	 * Guardar configuración
	 *
	 * @param array $data Datos a guardar
	 * @return bool True si se guardó exitosamente
	 */
	public function save( $data ) {
		$entity = $this->repository->create( $this->sanitize( $data ) );

		if ( $entity ) {
			// Sincronizar con opciones de WooCommerce
			$this->sync_woocommerce_settings( $data );
			return true;
		}
		return false;
	}

	/**
	 * Sanitizar datos antes de guardar
	 *
	 * @param array $settings Datos a sanitizar
	 * @return array Datos sanitizados
	 */
	public function sanitize( $settings ) {
		if ( isset( $settings['enabled'] ) ) {
			$settings['enabled'] = in_array( $settings['enabled'], array( 'yes', 'no' ) ) ? $settings['enabled'] : 'yes';
		}
		if ( isset( $settings['title'] ) ) {
			$settings['title'] = sanitize_text_field( $settings['title'] );
		}
		if ( isset( $settings['description'] ) ) {
			$settings['description'] = sanitize_textarea_field( $settings['description'] );
		}
		if ( isset( $settings['instructions'] ) ) {
			$settings['instructions'] = sanitize_textarea_field( $settings['instructions'] );
		}
		if ( isset( $settings['message'] ) ) {
			// Preserve line breaks while sanitizing the message
			$settings['message'] = wp_kses( $settings['message'], array() );
			$settings['message'] = wp_unslash( $settings['message'] );
		}
		if ( isset( $settings['phone'] ) ) {
			$settings['phone'] = qlwapp_format_phone( $settings['phone'] );
		}
		if ( isset( $settings['group'] ) ) {
			$settings['group'] = sanitize_url( $settings['group'] );
		}
		if ( isset( $settings['type'] ) ) {
			$settings['type'] = in_array( $settings['type'], array( 'phone', 'group' ) ) ? $settings['type'] : 'phone';
		}
		if ( isset( $settings['order_status'] ) ) {
			$settings['order_status'] = sanitize_text_field( $settings['order_status'] );
		}
		if ( isset( $settings['auto_redirect'] ) ) {
			$settings['auto_redirect'] = in_array( $settings['auto_redirect'], array( 'yes', 'no' ) ) ? $settings['auto_redirect'] : 'yes';
		}
		if ( isset( $settings['redirect_delay'] ) ) {
			$settings['redirect_delay'] = absint( $settings['redirect_delay'] );
		}
		if ( isset( $settings['visibility'] ) ) {
			$settings['visibility'] = in_array( $settings['visibility'], array( 'all', 'registered', 'guest' ) ) ? $settings['visibility'] : 'all';
		}
		if ( isset( $settings['icon'] ) ) {
			$settings['icon'] = sanitize_html_class( $settings['icon'] );
		}
		if ( isset( $settings['whatsapp_link_type'] ) ) {
			$settings['whatsapp_link_type'] = in_array( $settings['whatsapp_link_type'], array( 'api', 'web' ) ) ? $settings['whatsapp_link_type'] : 'web';
		}

		return $settings;
	}

	/**
	 * Sincronizar configuración con las opciones nativas de WooCommerce
	 *
	 * @param array $data Datos a sincronizar
	 */
	private function sync_woocommerce_settings( $data ) {
		// WooCommerce guarda settings del gateway en formato: woocommerce_{gateway_id}_settings
		$wc_option_name = 'woocommerce_qlwapp_whatsapp_settings';

		// Obtener settings actuales de WooCommerce (si existen)
		$wc_settings = get_option( $wc_option_name, array() );

		// Actualizar solo el campo 'enabled' para que WooCommerce reconozca el estado
		if ( isset( $data['enabled'] ) ) {
			$wc_settings['enabled'] = $data['enabled'];
			update_option( $wc_option_name, $wc_settings );
		}
	}

	/**
	 * Eliminar toda la configuración
	 *
	 * @return bool
	 */
	public function delete_all() {
		return $this->repository->delete();
	}

	/**
	 * Singleton instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
