<?php
/**
 * Class responsible for handling FAQ structured data schema (JSON-LD)
 */
namespace Jet_Tabs;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Jet_Tabs\JSON_LD_Schema' ) ) {

	/**
	 * Define JSON_LD_Schema class
	 */
	class JSON_LD_Schema {

		/**
		 * Holds the single instance of the class.
		 *
		 * @var JSON_LD_Schema|null
		 */
		private static $instance = null;

		/**
		 * Array to store FAQ schema items.
		 *
		 * @var array
		 */
		private $schema = [];

		/**
		 * Indicates whether the schema rendering has been hooked.
		 *
		 * @var bool
		 */
		private $hooked = false;

		/**
		 * Get instance of the class
		 *
		 * @return JSON_LD_Schema
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Add item (Question-Answer) to schema
		 *
		 * @param string $question Question text
		 * @param string $answer   Answer text
		 *
		 * @return void
		 */
		public function add_item( $question, $answer ) {

			if ( ! $this->hooked ) {
				add_action( 'wp_footer', [ $this, 'print_schema' ], 999 );
				$this->hooked = true;
			}

			$this->schema[] = array(
				'@type' => 'Question',
				'name'  => wp_strip_all_tags( $question ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $answer ),
				),
			);
		}

		/**
		 * Print schema in JSON-LD format
		 *
		 * @return void
		 */
		public function print_schema() {
			if ( ! empty( $this->schema ) ) {
				$json_ld = array(
					'@context'   => 'https://schema.org',
					'@type'      => 'FAQPage',
					'mainEntity' => $this->schema,
				);
				echo '<script type="application/ld+json">' . wp_json_encode( $json_ld ) . '</script>';
			}
		}
	}
}
