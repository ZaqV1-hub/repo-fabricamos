<?php

namespace WP_Rocket\Addon\ImageOptimization;

use WP_Rocket\Admin\Options;
use WP_Rocket\Admin\Options_Data;

/**
 * Manager for Image optimization options
 */
class OptionsManager {
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
	 * @param Options_Data $options WP Rocket Options instance.
	 */
	public function __construct( Options $options_api, Options_Data $options ) {
		$this->options_api = $options_api;
		$this->options     = $options;
	}

	/**
	 * Enable Image optimization option.
	 *
	 * @param string $unique_id CDN URL.
	 *
	 * @return void
	 * @since 3.5
	 */
	public function enable( $unique_id ) {
		$this->options->set( 'awp_image_optimization', 1 );
		$this->options->set( 'awp_image_optimization_unique_id', $unique_id );
		$this->options->set( 'cache_webp', 1 );

		$this->options_api->set( 'settings', $this->options->get_options() );

		// Filter will add additional redirect rules to .htaccess file.
		add_filter( 'rocket_webp_rewritecond', [ $this, 'add_webp_rewritecond' ] );

		// Flush .htaccess rules, and regenerate WP Rocket config file.
		$this->flush_wp_rocket();

		rocket_clean_domain();
	}

	/**
	 * Disable Image optimization option.
	 *
	 * @param bool $is_deactivation Is plugin deactivation event.
	 * @return void
	 * @since 3.5
	 */
	public function disable( $is_deactivation = false ) {
		$this->options->set( 'awp_image_optimization', 0 );
		$this->options->set( 'awp_image_optimization_unique_id', '' );
		$this->options->set( 'cache_webp', 0 );

		$this->options_api->set( 'settings', $this->options->get_options() );

		if ( true !== $is_deactivation ) {
			// Remove filter that adds additional redirect rules to .htaccess file.
			remove_filter( 'rocket_webp_rewritecond', [ $this, 'add_webp_rewritecond' ] );

			// Flush .htaccess rules, and regenerate WP Rocket config file.
			$this->flush_wp_rocket();
		}

		rocket_clean_domain();
	}

	/**
	 * Updates .htaccess file and regenerates WP Rocket config file.
	 */
	private function flush_wp_rocket() {

		if ( ! function_exists( 'flush_rocket_htaccess' ) || ! function_exists( 'rocket_generate_config_file' ) ) {
			return false;
		}

		// The following trick is necessary because this is running from CLI.
		// CL. REMOVED, because SERVER_SOFTWARE passed with env
		// global $is_apache;
		// $is_apache = true;.

		// Regenerate WP Rocket config file.
		rocket_generate_config_file();

		// Update WP Rocket .htaccess rules.
		flush_rocket_htaccess();
	}

	/**
	 * Get cdn status.
	 *
	 * @return int
	 */
	public function get_cdn() {
		return $this->options->get( 'cdn', 0 );
	}

	/**
	 * Get current image optimization status.
	 *
	 * @return bool
	 */
	public function is_image_optimization_enabled() {
		return (bool) $this->options->get( 'awp_image_optimization', 0 );
	}

	/**
	 * Get unique id.
	 *
	 * @return string
	 */
	public function get_unique_id() {
		$id = $this->options->get( 'awp_image_optimization_unique_id', '' );

		if ( defined( 'AWP_UNIQUE_ID' ) ) {
			$id = AWP_UNIQUE_ID;
		}

		return (string) $id;
	}

	/**
	 * Add extra rules to redirect image requests to their webp equivalents if they exist.
	 *
	 * @param string $rules WebP related redirect rules that will be printed.
	 *
	 * @return string Rules that will be printed.
	 */
	public function add_webp_rewritecond( $rules ) {

		$extra_rules = [
			// Check if an image was requested.
			'RewriteCond %{REQUEST_FILENAME} (.*)\.(jpe?g|png|gif|bmp)$',
			// Check if WebP replacement image exists.
			'RewriteCond %{REQUEST_FILENAME}.webp -f',
			// Serve WebP image instead.
			'RewriteRule (.+)\.(jpe?g|png|gif)$ $1.$2.webp [T=image/webp,E=accept:1]',
		];

		return $rules . implode( PHP_EOL, $extra_rules ) . PHP_EOL;
	}
}
