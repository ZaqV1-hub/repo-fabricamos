<?php
/**
 * Add abstract class for popup templates.
 *
 * @since 3.7.0
 */

namespace JupiterX_Core\Popup\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * JupiterX popup templates base.
 *
 * @since 3.7.0
 */
abstract class Jupiterx_Popup_Template_Base {
	/**
	 * Popup data.
	 *
	 * @access public
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Jupiterx_Popup_Template_Base Constructor.
	 *
	 * @since 3.7.0
	 */
	public function __construct( $key ) {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		$this->get_data( $key );
	}

	/**
	 * Get popup data.
	 *
	 * @since 3.7.0
	 * @return array|null
	 */
	public function get_data( $key ) {
		$id   = empty( $key ) ? get_the_ID() : $key;
		$data = [];

		if ( empty( $id ) ) {
			return;
		}

		$popup_id         = 'jupiterx-popups-' . $id;
		$data['id']       = $id;
		$data['uniqe_id'] = $popup_id;
		$data['title']    = wp_get_document_title();

		$this->data = $data;

		return $data;
	}

	/**
	 * Get popup classes.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_classes() {}

	/**
	 * Get popup content.
	 *
	 * @since 3.7.0
	 * @return void
	 */
	public function get_content() {}
}
