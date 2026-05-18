<?php
/**
 * Functions for updating theme version.
 *
 * @package JupiterX\Framework\API\Compatibility
 *
 * @since 1.0.0
 */

/**
 * Version updates.
 *
 * @since 1.0.2
 *
 * @return void
 */
function jupiterx_update_v102() {
	if ( is_null( get_option( 'jupiterx_setup_wizard_hide_notice', null ) ) ) {
		update_option( 'jupiterx_setup_wizard_hide_notice', true );
	}
}

/**
 * Version updates.
 *
 * @since 1.3.0
 *
 * @return void
 */
function jupiterx_update_v130() {
	set_site_transient( 'jupiterx_update_plugins_notice', 'yes' );
}

/**
 * Version updates.
 *
 * @since 1.11.0
 *
 * @return void
 */
function jupiterx_update_v1110() {
	$options = [
		'artbees_api_key',
		'jupiterx_adobe_fonts_project_id',
		'jupiterx_svg_support',
		'jupiterx_setup_wizard_current_page',
		'jupiterx_setup_wizard_hide_notice',
		'jupiterx_template_installed',
		'jupiterx_template_installed_id',
		'jupiterx_dev_mode',
		'jupiterx_unboarding_hide_popup',
		'jupiterx_post_types',
		'jupiterx_custom_sidebars',
		'jupiterx_tracking_codes_after_head',
		'jupiterx_tracking_codes_before_head',
		'jupiterx_tracking_codes_after_body',
		'jupiterx_tracking_codes_before_body',
		'jupiterx_cache_busting',
		'jupiterx_google_analytics_id',
		'jupiterx_google_analytics_anonymization',
		'jupiterx_donut_twitter_consumer_key',
		'jupiterx_donut_twitter_consumer_secret',
		'jupiterx_donut_twitter_access_token',
		'jupiterx_donut_twitter_access_token_secret',
		'jupiterx_donut_mailchimp_api_key',
		'jupiterx_donut_mailchimp_list_id',
		'jupiterx_donut_google_maps_api_key',
	];

	foreach ( $options as $option ) {
		$name = preg_replace( '/(jupiterx|artbees)_/', '', $option, 1 );

		// Only save option that has a saved value.
		$value = get_option( $option, null );
		if ( ! is_null( $value ) ) {
			jupiterx_update_option( $name, $value );
		}
	}
}

/**
 * Enable Multilingual Customizer option for active users.
 *
 * @since 1.22.1
 *
 * @return void
 */
function jupiterx_update_v1221() {
	if ( ! function_exists( 'pll_current_language' ) && ! class_exists( 'SitePress' ) ) {
		return;
	}

	jupiterx_update_option( 'multilingual_customizer', 1 );
}

/**
 * Move products skin setting to new products widget.
 *
 * @since 2.0.9
 * @return void
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
function jupiterx_update_v209() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX || ! is_admin() ) {
		return;
	}

	add_action( 'admin_init', function() {
		global $wpdb;

		// Move all raven-wc-products classic skin settings to simple settings(Just remove prefix).
		// phpcs:disable
		$post_ids = $wpdb->get_col(
			'SELECT `post_id` FROM `' . $wpdb->postmeta . '` WHERE `meta_key` = "_elementor_data" AND `meta_value` LIKE \'%"widgetType":"raven-wc-products"%\';'
		);
		// phpcs:enable

		if ( empty( $post_ids ) ) {
			return;
		}

		if ( ! class_exists( 'Elementor\Plugin' ) ) {
			return;
		}

		$elementor = Elementor\Plugin::$instance;

		foreach ( $post_ids as $post_id ) {
			$do_update = false;

			$document = $elementor->documents->get( $post_id );

			if ( $document ) {
				$data = $document->get_elements_data();
			}

			if ( empty( $data ) ) {
				continue;
			}

			$data = $elementor->db->iterate_data( $data, function( $element ) use ( &$do_update ) {
				if ( empty( $element['widgetType'] ) || 'raven-wc-products' !== $element['widgetType'] ) {
					return $element;
				}

				$fields_to_change = [
					'classic_columns',
					'classic_rows',
					'classic_load_more_text',
					'classic_show_all_products',
					'classic_show_pagination',
					'classic_pagination_type',
				];

				foreach ( $fields_to_change as $field ) {
					// TODO: Remove old value later.
					$new_field_key = str_replace( 'classic_', '', $field );

					if ( isset( $element['settings'][ $field ] ) ) {
						$element['settings'][ $new_field_key ] = $element['settings'][ $field ];
						unset( $element['settings'][ $field ] );

						$do_update = true;
					}
				}

				return $element;
			} );

			// Only update if needed.
			if ( ! $do_update ) {
				continue;
			}

			// We need the `wp_slash` in order to avoid the unslashing during the `update_post_meta`.
			$json_value = wp_slash( wp_json_encode( $data ) );

			update_metadata( 'post', $post_id, '_elementor_data', $json_value );

			// Clear WP cache for next step.
			wp_cache_flush();
		}
	} );
}

/**
 * Move video aspect ratio to new video widget.
 *
 * @since 3.0.0
 * @return void
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
function jupiterx_update_v300() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX || ! is_admin() ) {
		return;
	}

	add_action( 'admin_init', function() {
		global $wpdb;

		// phpcs:disable
		$post_ids = $wpdb->get_col(
			'SELECT `post_id` FROM `' . $wpdb->postmeta . '` WHERE `meta_key` = "_elementor_data" AND `meta_value` LIKE \'%"widgetType":"raven-video"%\';'
		);
		// phpcs:enable

		if ( empty( $post_ids ) ) {
			return;
		}

		if ( ! class_exists( 'Elementor\Plugin' ) ) {
			return;
		}

		$elementor = Elementor\Plugin::$instance;

		foreach ( $post_ids as $post_id ) {
			$do_update = false;

			$document = $elementor->documents->get( $post_id );

			if ( $document ) {
				$data = $document->get_elements_data();
			}

			if ( empty( $data ) ) {
				continue;
			}

			$data = $elementor->db->iterate_data( $data, function( $element ) use ( &$do_update ) {
				if ( empty( $element['widgetType'] ) || 'raven-video' !== $element['widgetType'] ) {
					return $element;
				}

				if ( isset( $element['settings']['video_aspect_ratio'] ) ) {
					switch ( $element['settings']['video_aspect_ratio'] ) {
						case '169':
							$element['settings']['video_aspect_ratio'] = '16 / 9';
							$do_update                                 = true;
							break;

						case '43':
							$element['settings']['video_aspect_ratio'] = '4 / 3';
							$do_update                                 = true;
							break;

						case '32':
							$element['settings']['video_aspect_ratio'] = '3 / 2';
							$do_update                                 = true;
							break;
					}
				}

				return $element;
			} );

			// Only update if needed.
			if ( ! $do_update ) {
				continue;
			}

			// We need the `wp_slash` in order to avoid the unslashing during the `update_post_meta`.
			$json_value = wp_slash( wp_json_encode( $data ) );

			update_metadata( 'post', $post_id, '_elementor_data', $json_value );

			// Clear WP cache for next step.
			wp_cache_flush();
		}
	} );
}

/**
 * Add default custom field id for the form inputs in the new form widget.
 *
 * @since 3.3.0
 * @return void
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
function jupiterx_update_v330() {
	if ( ! is_admin() ) {
		return;
	}

	add_action( 'admin_init', function() {
		global $wpdb;

		// phpcs:disable
		$post_ids = $wpdb->get_col(
			'SELECT `post_id` FROM `' . $wpdb->postmeta . '` WHERE `meta_key` = "_elementor_data" AND `meta_value` LIKE \'%"widgetType":"raven-form"%\';'
		);
		// phpcs:enable

		if ( empty( $post_ids ) ) {
			return;
		}

		if ( ! class_exists( 'Elementor\Plugin' ) ) {
			return;
		}

		$elementor = Elementor\Plugin::$instance;

		foreach ( $post_ids as $post_id ) {
			$do_update = false;

			$document = $elementor->documents->get( $post_id );

			if ( $document ) {
				$data = $document->get_elements_data();
			}

			if ( empty( $data ) ) {
				continue;
			}

			$data = $elementor->db->iterate_data( $data, function( $element ) use ( &$do_update ) {
				if ( empty( $element['widgetType'] ) || 'raven-form' !== $element['widgetType'] ) {
					return $element;
				}

				foreach ( $element['settings']['fields'] as $key => $field ) {
					if ( ! isset( $field['field_custom_id'] ) ) {
						$element['settings']['fields'][ $key ]['field_custom_id'] = $field['_id'];
						$do_update = true;
					}
				}

				return $element;
			} );

			// Only update if needed.
			if ( ! $do_update ) {
				continue;
			}

			// We need the `wp_slash` in order to avoid the unslashing during the `update_post_meta`.
			$json_value = wp_slash( wp_json_encode( $data ) );

			update_metadata( 'post', $post_id, '_elementor_data', $json_value );

			// Clear WP cache for next step.
			wp_cache_flush();
		}
	} );
}

/**
 * Update slides per view value for the testimonial carousel because we changed the elementor control from select to slider.
 *
 * @since 3.5.6
 * @return void
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
function jupiterx_update_v356() {
	if ( ! is_admin() ) {
		return;
	}

	// Update layout builder templates.
	$post_type = 'elementor_library';
	$meta_key  = 'jupiterx-condition-rules-string';
	$posts     = get_posts( [
		'post_type' => $post_type,
		'meta_query'     => [ //phpcs:ignore
			[
				'key'     => 'jx-layout-type',
				'compare' => 'EXISTS',
			],
		],
		'posts_per_page' => -1,
	] );

	foreach ( $posts as $post ) {
		$meta_value = get_post_meta( $post->ID, $meta_key, true );

		if ( empty( $meta_value ) ) {
			update_post_meta( $post->ID, $meta_key, '' );
		}
	}

	add_action( 'admin_init', function() {
		global $wpdb;

		// phpcs:disable
		$post_ids = $wpdb->get_col(
			'SELECT post_id FROM ' . $wpdb->postmeta . ' WHERE meta_key = "_elementor_data" AND meta_value LIKE \'%"widgetType":"raven-testimonial-carousel"%\';'
		);
		// phpcs:enable

		if ( empty( $post_ids ) ) {
			return;
		}

		if ( ! class_exists( 'Elementor\Plugin' ) ) {
			return;
		}

		$elementor = Elementor\Plugin::$instance;

		foreach ( $post_ids as $post_id ) {
			$do_update = false;

			$document = $elementor->documents->get( $post_id );

			if ( $document ) {
				$data = $document->get_elements_data();
			}

			if ( empty( $data ) ) {
				continue;
			}

			$data = $elementor->db->iterate_data( $data, function( $element ) use ( &$do_update ) {
				if ( empty( $element['widgetType'] ) || 'raven-testimonial-carousel' !== $element['widgetType'] ) {
					return $element;
				}

				if ( isset( $element['settings']['slides_per_view'] ) && ! is_array( $element['settings']['slides_per_view'] ) ) {
					$element['settings']['slides_per_view'] = [
						'unit' => 'px',
						'size' => $element['settings']['slides_per_view'],
						'sizes' => [],
					];

					$do_update = true;
				}

				$active_breakpoints      = Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();
				$active_breakpoints_keys = array_keys( $active_breakpoints );

				foreach ( $active_breakpoints_keys as $device ) {
					if ( isset( $element['settings'][ 'slides_per_view_' . $device ] ) && ! is_array( $element['settings'][ 'slides_per_view_' . $device ] ) ) {
						$element['settings'][ 'slides_per_view_' . $device ] = [
							'unit' => 'px',
							'size' => $element['settings'][ 'slides_per_view_' . $device ],
							'sizes' => [],
						];

						$do_update = true;
					}
				}

				return $element;
			} );

			// Only update if needed.
			if ( ! $do_update ) {
				continue;
			}

			// We need the wp_slash in order to avoid the unslashing during the update_post_meta.
			$json_value = wp_slash( wp_json_encode( $data ) );

			update_metadata( 'post', $post_id, '_elementor_data', $json_value );

			// Clear WP cache for next step.
			wp_cache_flush();
		}
	} );
}

/**
 * Migrate Logo URL in advanced menu widget.
 *
 * @since 4.0.0
 * @return void
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
function jupiterx_update_v400() {
	if ( ! is_admin() ) {
		return;
	}

	add_action( 'admin_init', function() {
		global $wpdb;

		// phpcs:disable
		$post_ids = $wpdb->get_col(
			'SELECT `post_id` FROM `' . $wpdb->postmeta . '` WHERE `meta_key` = "_elementor_data" AND `meta_value` LIKE \'%"widgetType":"raven-advanced-nav-menu"%\';'
		);
		// phpcs:enable

		if ( empty( $post_ids ) ) {
			return;
		}

		if ( ! class_exists( 'Elementor\Plugin' ) ) {
			return;
		}

		$elementor = Elementor\Plugin::$instance;

		foreach ( $post_ids as $post_id ) {
			$do_update = false;

			$document = $elementor->documents->get( $post_id );

			if ( $document ) {
				$data = $document->get_elements_data();
			}

			if ( empty( $data ) ) {
				continue;
			}

			$data = $elementor->db->iterate_data( $data, function( $element ) use ( &$do_update ) {
				if ( empty( $element['widgetType'] ) || 'raven-advanced-nav-menu' !== $element['widgetType'] ) {
					return $element;
				}

				if ( isset( $element['settings']['center_logo_skin'] ) ) {
					$element['settings']['center_image']         = $element['settings']['center_logo_skin'];
					$element['settings']['center_image']['size'] = '';

					$do_update = true;
				}

				return $element;
			} );

			// Only update if needed.
			if ( ! $do_update ) {
				continue;
			}

			// We need the `wp_slash` in order to avoid the unslashing during the `update_post_meta`.
			$json_value = wp_slash( wp_json_encode( $data ) );

			update_metadata( 'post', $post_id, '_elementor_data', $json_value );

			// Clear WP cache for next step.
			wp_cache_flush();
		}
	} );
}


/**
 * Update popup triggers to page load with zero delay if there is no trigger for popup.
 *
 * @since 4.2.0
 * @return void
 * @SuppressWarnings(PHPMD.NPathComplexity)
 *
 * @todo Update version for release.
 */
function jupiterx_update_v420() {
	if ( ! is_admin() ) {
		return;
	}

	$popups = get_posts( [
		'post_type' => 'jupiterx-popups',
		'posts_per_page' => -1,
	] );

	foreach ( $popups as $popup ) {
		$popup_id       = $popup->ID;
		$popup_triggers = get_post_meta( $popup_id, '_jupiterx_popup_triggers', true );

		if ( empty( $popup_triggers ) ) {
			$new_trigger = [
				[
					'name' => 'on_page_load',
					'control' => 0,
				],
			];

			update_post_meta( $popup_id, '_jupiterx_popup_triggers', $new_trigger );
		}
	}
}
