<?php

namespace JupiterX_Core\Raven\Modules\Products\Filters;

use JupiterX_Core\Raven\Modules\Products\Module;

defined( 'ABSPATH' ) || die();

class Categories_Tags extends Filter_Base {

	public static function get_title() {
		return esc_html__( 'Categories & Tags & Brands', 'jupiterx-core' );
	}

	public static function get_name() {
		return 'categories_tags';
	}

	public static function get_order() {
		return 20;
	}

	public static function get_filter_attributes() {
		$query_tags   = (array) self::$settings['query_filter_tags'];
		$tags         = [];
		$query_brands = (array) self::$settings['query_filter_brands'];
		$brands       = [];

		foreach ( $query_tags as $query_tag ) {
			$term = get_term_by( 'id', $query_tag, 'product_tag' );

			if ( empty( $term ) ) {
				continue;
			}

			$tags[] = $term->slug;
		}

		if ( ! empty( $query_brands ) ) {
			foreach ( $query_brands as $query_brand ) {
				$term = get_term_by( 'id', $query_brand, 'product_brand' );

				if ( empty( $term ) ) {
					continue;
				}

				$brands[] = $term->slug;
			}
		}

		return [
			'category' => implode( ',', (array) self::$settings['query_filter_categories'] ),
			'tag' => implode( ',', $tags ),
			'brand' => implode( ',', $brands ),
		];
	}
}
