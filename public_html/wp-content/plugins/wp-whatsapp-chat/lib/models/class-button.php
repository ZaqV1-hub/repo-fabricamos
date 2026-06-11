<?php
namespace QuadLayers\QLWAPP\Models;

use QuadLayers\QLWAPP\Entities\Button as Button_Entity;

use QuadLayers\WP_Orm\Builder\SingleRepositoryBuilder;

class Button {

	protected static $instance;
	protected $repository;

	public function __construct() {
		add_filter( 'sanitize_option_qlwapp_button', 'wp_unslash' );
		$builder = ( new SingleRepositoryBuilder() )
		->setTable( 'qlwapp_button' )
		->setEntity( Button_Entity::class );

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
			$admin  = new Button_Entity();
			$result = $admin->getProperties();
		}

		if ( ! is_admin() ) {
			$result['text']    = qlwapp_replacements_vars( $result['text'] );
			$result['message'] = qlwapp_replacements_vars( $result['message'] );
		}

		return $result;
	}

	public function delete_all() {
		return $this->repository->delete();
	}

	public function save( $data ) {
		$entity = $this->repository->create( $this->sanitize( $data ) );

		if ( $entity ) {
			return true;
		}
	}

	public function sanitize( $settings ) {
		if ( isset( $settings['layout'] ) ) {
			$settings['layout'] = sanitize_html_class( $settings['layout'] );
		}
		if ( isset( $settings['position'] ) ) {
			$settings['position'] = sanitize_html_class( $settings['position'] );
		}
		if ( isset( $settings['text'] ) ) {
			$settings['text'] = sanitize_text_field( $settings['text'] );
		}
		if ( isset( $settings['message'] ) ) {
			// Preserve line breaks while sanitizing the message
			$settings['message'] = wp_kses( $settings['message'], array() );
			$settings['message'] = wp_unslash( $settings['message'] );
		}
		if ( isset( $settings['icon'] ) ) {
			// Check if it's a URL (for custom images) or a CSS class (for font icons)
			if ( filter_var( $settings['icon'], FILTER_VALIDATE_URL ) ||
				( strpos( $settings['icon'], 'http' ) === 0 ) ||
				( strpos( $settings['icon'], '.' ) !== false && preg_match( '/\.(jpg|jpeg|png|gif|svg|webp)$/i', $settings['icon'] ) ) ) {
				// It's an image URL, sanitize as URL
				$settings['icon'] = sanitize_url( $settings['icon'] );
			} else {
				// It's a CSS class, sanitize as HTML class
				$settings['icon'] = sanitize_html_class( $settings['icon'] );
			}
		}
		if ( isset( $settings['phone'] ) ) {
			$settings['phone'] = qlwapp_format_phone( $settings['phone'] );
		}
		if ( isset( $settings['group'] ) ) {
			$settings['group'] = sanitize_url( $settings['group'] );
		}
		if ( isset( $settings['whatsapp_link_type'] ) ) {
			$settings['whatsapp_link_type'] = in_array( $settings['whatsapp_link_type'], array( 'api', 'web' ) ) ? $settings['whatsapp_link_type'] : 'web';
		}

		return $settings;
	}

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
