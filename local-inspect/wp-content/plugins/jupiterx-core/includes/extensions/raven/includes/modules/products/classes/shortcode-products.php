<?php
namespace JupiterX_Core\Raven\Modules\Products\Classes;

defined( 'ABSPATH' ) || die();

#[\AllowDynamicProperties]
class Shortcode_Products extends \WC_Shortcode_Products {
	/**
	 * Filter callback.
	 *
	 * @since 4.2.0
	 */
	public $fallback_filter;

	public function get_content() {
		$results = $this->get_query_results();

		return [
			'data' => parent::get_content(),
			'query_results' => $results,
		];
	}

	protected function parse_attributes( $attributes ) {
		$att = parent::parse_attributes( $attributes );

		// Add support for product_brand attribute
		$att['brand']          = isset( $attributes['brand'] ) ? $attributes['brand'] : '';
		$att['brand_operator'] = 'IN';

		return $att;
	}

	protected function parse_query_args() {
		$query_args = parent::parse_query_args();
		$this->set_brands_query_args( $query_args );

		return $query_args;
	}

	/**
	 * Set the query arguments for the brand filter.
	 *
	 * @param array $query_args The query arguments.
	 */
	protected function set_brands_query_args( &$query_args ) {

		if ( ! empty( $this->attributes['brand'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_brand',
				'terms'    => array_map( 'sanitize_title', explode( ',', $this->attributes['brand'] ) ),
				'field'    => 'slug',
				'operator' => $this->attributes['brand_operator'],
			);
		}
	}

}
