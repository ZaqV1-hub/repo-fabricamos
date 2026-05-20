<?php
/**
 * DB upgrder class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Blog_DB_Upgrader' ) ) {

	/**
	 * Define Jet_Blog_DB_Upgrader class
	 */
	class Jet_Blog_DB_Upgrader {

		/**
		 * Setting key
		 *
		 * @var string
		 */
		public $key = null;

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			$this->key = jet_blog_settings()->key;

			/**
			 * Plugin initialized on new Jet_Blog_DB_Upgrader call.
			 * Please ensure, that it called only on admin context
			 */
			$this->init_upgrader();
		}

		/**
		 * Initialize upgrader module
		 *
		 * @return void
		 */
		public function init_upgrader() {

			$db_updater_data = jet_blog()->module_loader->get_included_module_data( 'cx-db-updater.php' );

			new CX_DB_Updater(
				array(
					'path'      => $db_updater_data['path'],
					'url'       => $db_updater_data['url'],
					'slug'      => 'jet-blog',
					'version'   => '2.4.4',
					'callbacks' => array(
						'2.1.22' => array(
							array( $this, 'update_db_2_1_22' ),
						),
						'2.4.4' => array(
							array( $this, 'update_db_2_4_4' ),
						),
					),
					'labels'    => array(
						'start_update' => esc_html__( 'Start Update', 'jet-blog' ),
						'data_update'  => esc_html__( 'Data Update', 'jet-blog' ),
						'messages'     => array(
							'error'   => esc_html__( 'Module DB Updater init error in %s - version and slug is required arguments', 'jet-blog' ),
							'update'  => esc_html__( 'We need to update your database to the latest version.', 'jet-blog' ),
							'updated' => esc_html__( 'Update complete, thank you for updating to the latest version!', 'jet-blog' ),
						),
					),
				)
			);
		}

		/**
		 * Update db updater 2.1.22
		 *
		 * @return void
		 */
		public function update_db_2_1_22() {
			if ( class_exists( 'Elementor\Plugin' ) ) {
				Elementor\Plugin::instance()->files_manager->clear_cache();
			}
		}

		public function update_db_2_4_4() {
			if ( class_exists( 'Elementor\Plugin' ) ) {

				$elementor_post_types = get_post_types_by_support( 'elementor' );

				$query = new WP_Query( [
					'post_type'      => $elementor_post_types,
					'post_status'    => 'any',
					'meta_query'     => [
						[
							'key'     => '_elementor_data',
							'compare' => 'EXISTS',
						],
						[
							'key'     => '_elementor_data',
							'value'   => 'jet-blog-smart-listing',
							'compare' => 'LIKE',
						],
					],
					'suppress_filters' => false,
					'posts_per_page' => -1,
				] );

				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id = get_the_ID();
					$data = get_post_meta( $post_id, '_elementor_data', true );

					if ( ! $data ) {
						continue;
					}

					$elements = json_decode( $data, true );

					$update_widget_data = function( &$elements ) use ( &$update_widget_data ) {
						foreach ( $elements as &$element ) {
							if ( isset( $element['elType'] ) && 'widget' === $element['elType'] ) {
								if ( isset( $element['widgetType'] ) && 'jet-blog-smart-listing' === $element['widgetType'] ) {
									$current_post_type = isset( $element['settings']['post_type'] ) ? $element['settings']['post_type'] : false;

									if ( false != $current_post_type ) {
										$element['settings']['post_type'] = (array) $current_post_type;
									}
								}
							}

							if ( ! empty( $element['elements'] ) ) {
								$update_widget_data( $element['elements'] );
							}
						}
					};

					$update_widget_data( $elements );

					update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
				}

				wp_reset_postdata();
			}
		}
	}
}
