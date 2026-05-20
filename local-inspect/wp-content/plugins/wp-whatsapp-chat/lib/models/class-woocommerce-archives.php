<?php
namespace QuadLayers\QLWAPP\Models;

use QuadLayers\QLWAPP\Entities\WooCommerce_Archives as WooCommerceArchives_Entity;

use QuadLayers\WP_Orm\Builder\SingleRepositoryBuilder;

class WooCommerce_Archives {

	protected static $instance;
	protected $repository;

	public function __construct() {
		add_filter( 'sanitize_option_qlwapp_woocommerce_archives', 'wp_unslash' );
		$builder = ( new SingleRepositoryBuilder() )
		->setTable( 'qlwapp_woocommerce_archives' )
		->setEntity( WooCommerceArchives_Entity::class );

		$this->repository = $builder->getRepository();
	}

	public function get_table() {
		return $this->repository->getTable();
	}

	public function get() {
		$entity = $this->repository->find();
		$result = null;

		if ( $entity ) {
			$result = $entity->getProperties();
		} else {
			$admin  = new WooCommerceArchives_Entity();
			$result = $admin->getProperties();
		}

		// Only replace variables on frontend (not in admin or REST API admin requests).
		$is_rest_admin = defined( 'REST_REQUEST' ) && REST_REQUEST && is_user_logged_in();
		if ( ! is_admin() && ! $is_rest_admin ) {
			$result['text']    = qlwapp_replacements_vars( $result['text'] );
			$result['message'] = qlwapp_replacements_vars( $result['message'] );
		}

		return $result;
	}

	public function delete_all() {
		return $this->repository->delete();
	}

	public function save( $data ) {
		$entity = $this->repository->create( $data );

		if ( $entity ) {
			return true;
		}
	}

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
