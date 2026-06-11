<?php

namespace Jet_Engine\Bricks_Views;

use Bricks\Templates;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Controls JetEngine listing assets during Bricks popup rendering.
 *
 * Static popups:
 * Bricks popups render twice: a fake early render and a real render in wp_footer.
 * Listing assets are temporarily disabled during the fake render and restored
 * before the real render to ensure styles and scripts are correctly printed.
 *
 * Dynamic popups:
 * Compatibility note: Dynamic Bricks popups are intentionally skipped.
 *
 * Bricks handles dynamic popup rendering by increasing the specificity of CSS selectors
 * with the `.brx-popup` prefix:
 *
 * Example:
 * - Page render:
 *   .brxe-XXXX .brxe-YYYY { ... }
 *
 * - AJAX popup render:
 *   .brx-popup.brxe-XXXX .brxe-YYYY { ... }
 *
 * This ensures correct styling inside a popup, but dynamic popups have their own
 * query context and inline styles are scoped differently per item.
 *
 * A workaround using \Bricks\Assets::$inline_css_dynamic_data was considered, but
 * it risks duplicating or breaking styles for other components (calendar, map).
 *
 */
class Bricks_Popup_Render {

	function __construct() {
		add_filter( 'pre_do_shortcode_tag', array( $this, 'prevent_listing_assets_before_fake_popup_render' ), 10, 3 );
		add_action( 'bricks/frontend/before_render_data', array( $this, 'allow_listing_assets_before_real_popup_render' ), 10, 2 );
	}

	/**
	 * Disable listing assets during Bricks popup fake render.
	 */
	public function prevent_listing_assets_before_fake_popup_render( $flag, $tag, $attr ) {
		$template_id = ! empty( $attr['id'] ) ? intval( $attr['id'] ) : false;

		if ( ! $template_id ) {
			return $flag;
		}

		$template_type = Templates::get_template_type( $template_id );

		if ( $tag === 'bricks_template' && $template_type === 'popup' ) {
			add_filter( 'jet-engine/bricks-views/listing/render-assets', '__return_false' );
		}

		return $flag;
	}

	/**
	 * Restore listing assets before the real popup render.
	 */
	public function allow_listing_assets_before_real_popup_render( $elements, $area ) {
		if ( $area === 'popup' ) {
			remove_filter( 'jet-engine/bricks-views/listing/render-assets', '__return_false' );
		}
	}
}