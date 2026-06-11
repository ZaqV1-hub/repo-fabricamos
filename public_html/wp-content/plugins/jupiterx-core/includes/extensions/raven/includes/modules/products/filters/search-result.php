<?php

namespace JupiterX_Core\Raven\Modules\Products\Filters;

use JupiterX_Core\Raven\Modules\Products\Module;

defined( 'ABSPATH' ) || die();

class Search_Result extends Filter_Base {

	public static function get_title() {
		return esc_html__( 'Search Result', 'jupiterx-core' );
	}

	public static function get_name() {
		return 'search_result';
	}

	public static function get_order() {
		return 170;
	}

	public static function get_filter_args() {
		$search_query = filter_input( INPUT_GET, 's' );

		//If it is editor or preview or not search archive page, it will return default query since we don't have queried object on these pages.
		if ( Module::is_editor_or_preview() || ! $search_query ) {
			return [];
		}

		return [
			's' => $search_query,
		];
	}
}
