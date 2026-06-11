<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Engine\CriticalPath;

use WP_Rocket\Admin\Options;
use WP_Rocket\Admin\Options_Data;

/**
 * Manager for WP Rocket CCSS options
 *
 * @note CL
 * @since 3.12.3.3
 */
class CCSSAWPOptionsManager {
	/**
	 * WP Options API instance
	 *
	 * @var Options
	 */
	private $options_api;

	/**
	 * WP Rocket Options instance
	 *
	 * @var Options_Data
	 */
	private $options;

	/**
	 * Constructor
	 *
	 * @param Options      $options_api WP Options API instance.
	 * @param Options_Data $options     WP Rocket Options instance.
	 */
	public function __construct( Options $options_api, Options_Data $options ) {
		$this->options_api = $options_api;
		$this->options     = $options;
	}

	/**
	 * Enable CCSS option, save CLN user id
	 *
	 * @since 3.12.3.3
	 *
	 * @param string $unique_id CLN id.
	 * @return void
	 */
	public function enable( $unique_id ) {
		$this->options->set( 'optimize_css_delivery', '1' );
		$this->options->set( 'async_css', 1 );
		$this->options->set( 'async_css_mobile', '1' );
		// Disabling unsupported CPCSS method.
		$this->options->set( 'remove_unused_css', 0 );
		$this->options->set( 'ccss_awp_unique_id', $unique_id );
		$this->options_api->set( 'settings', $this->options->get_options() );
	}

	/**
	 * Disable CCSS option
	 *
	 * @since 3.12.3.3
	 *
	 * @return void
	 */
	public function disable() {
		$this->options->set( 'async_css', 0 );
		$this->options->set( 'async_css_mobile', '1' );
		// Disabling unsupported CPCSS method.
		$this->options->set( 'remove_unused_css', 0 );
		// Remove option.
		$options = $this->options->get_options();
		unset( $options['optimize_css_delivery'] );
		$this->options_api->set( 'settings', $options );

		rocket_clean_domain();
	}

	/**
	 * Get current user CLN id.
	 *
	 * @since 3.12.3.3
	 *
	 * @return string
	 */
	public function get_awp_unique_id() {
		return $this->options->get( 'ccss_awp_unique_id', false );
	}
}
