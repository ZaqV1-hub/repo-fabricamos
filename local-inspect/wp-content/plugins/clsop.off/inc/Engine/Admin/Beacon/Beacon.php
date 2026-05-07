<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */

namespace WP_Rocket\Engine\Admin\Beacon;

use WP_Rocket\Abstract_Render;
use WP_Rocket\Admin\Options_Data;
use WP_Rocket\Engine\Support\Data;
use WP_Rocket\Event_Management\Subscriber_Interface;

/**
 * Helpscout Beacon integration
 *
 * @since  3.2
 */
class Beacon extends Abstract_Render implements Subscriber_Interface {
	/**
	 * Options_Data instance
	 *
	 * @since  3.2
	 *
	 * @var Options_Data $options
	 */
	private $options;

	/**
	 * Current user locale
	 *
	 * @since  3.2
	 *
	 * @var string $locale
	 */
	private $locale;

	/**
	 * Support data instance
	 *
	 * @var Data
	 */
	private $support_data;

	/**
	 * Constructor
	 *
	 * @since 3.2
	 *
	 * @param Options_Data $options       Options instance.
	 * @param string       $template_path Absolute path to the views/settings.
	 * @param Data         $support_data  Support data instance.
	 */
	public function __construct( Options_Data $options, $template_path, Data $support_data ) {
		parent::__construct( $template_path );

		$this->options      = $options;
		$this->support_data = $support_data;
	}

	/**
	 * Return an array of events that this subscriber wants to listen to.
	 *
	 * @since  3.2
	 *
	 * @return array
	 */
	public static function get_subscribed_events() {
		return [
			'admin_print_footer_scripts-settings_page_clsop' => 'insert_script',
		];
	}

	/**
	 * Configures and returns beacon javascript
	 *
	 * @since  3.2
	 *
	 * @return void
	 */
	public function insert_script() {
		if (
			rocket_get_constant( 'WP_ROCKET_WHITE_LABEL_ACCOUNT' )
			||
			! current_user_can( 'rocket_manage_options' )
		) {
			return;
		}

		switch ( $this->get_user_locale() ) {
			case 'fr':
				$form_id = '9db9417a-5e2f-41dd-8857-1421d5112aea';
				break;
			default:
				$form_id = '44cc73fb-7636-4206-b115-c7b33823551b';
				break;
		}

		$data = [
			'form_id'  => $form_id,
			'identify' => wp_json_encode( $this->identify_data() ),
			'session'  => wp_json_encode( $this->support_data->get_support_data() ),
			'prefill'  => wp_json_encode( $this->prefill_data() ),
			'config'   => wp_json_encode( $this->config_data() ),
		];

		/*
		CL.
		echo $this->generate( 'beacon', $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		*/
	}

	/**
	 * Sets the locale property with the current user locale if not set yet
	 *
	 * @since  3.5
	 *
	 * @return string
	 */
	private function get_user_locale() {
		if ( empty( $this->locale ) ) {
			$this->locale = current( array_slice( explode( '_', get_user_locale() ), 0, 1 ) );
		}

		/**
		 * Filters the locale ID for Beacon
		 *
		 * @since 3.6
		 *
		 * @param string $locale The locale ID.
		 */
		return wpm_apply_filters_typed( 'string', 'rocket_beacon_locale', $this->locale );
	}

	/**
	 * Returns Identify data to pass to Beacon
	 *
	 * @since  3.0
	 *
	 * @return array
	 */
	private function identify_data() {
		$identify_data = [
			'email'   => $this->options->get( 'consumer_email' ),
			'Website' => home_url(),
		];
		$customer_data = get_transient( 'wp_rocket_customer_data' );

		if ( false !== $customer_data && isset( $customer_data->status ) ) {
			$identify_data['status'] = $customer_data->status;
		}

		return $identify_data;
	}

	/**
	 * Returns prefill data to pass to Beacon
	 *
	 * @since 3.6
	 *
	 * @return array
	 */
	private function prefill_data() {
		$prefill_data = [
			'fields' => [
				[
					'id'    => 21728,
					'value' => 108003, // default to nulled.
				],
			],
		];

		$customer_data = get_transient( 'wp_rocket_customer_data' );

		if ( false === $customer_data || ! isset( $customer_data->licence_account ) ) {
			return $prefill_data;
		}

		$licenses = [
			'Single'      => 108000,
			'Plus'        => 108001,
			'Infinite'    => 108002,
			'Unavailable' => 108003,
		];

		if ( isset( $licenses[ $customer_data->licence_account ] ) ) {
			$prefill_data['fields'][0]['value'] = $licenses[ $customer_data->licence_account ];
		}

		return $prefill_data;
	}

	/**
	 * Returns config data to pass to Beacon
	 *
	 * @since 3.8.5
	 *
	 * @return array
	 */
	private function config_data(): array {
		return [
			'display' => [
				'position' => is_rtl() ? 'left' : 'right',
			],
		];
	}

	/**
	 * Returns the IDs for the HelpScout docs for the corresponding section and language.
	 *
	 * @since  3.0
	 *
	 * @param string $doc_id Section identifier.
	 *
	 * @return string|array
	 */
	public function get_suggest( $doc_id ) {
		$suggest = [
			'faq'                        => [
				'en' => [
					[
						'id'    => '5569b671e4b027e1978e3c51',
						'url'   => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
						'title' => 'Pages Are Not Cached or CSS and JS Minification Are Not Working',
					],
					[
						'id'    => '556778c8e4b01a224b426fad',
						'url'   => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
						'title' => 'Google PageSpeed Grade does not Improve',
					],
					[
						'id'    => '556ef48ce4b01a224b428691',
						'url'   => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
						'title' => 'My Site Is Broken',
					],
					[
						'id'    => '6001a83b2e764327f87bf189',
						'url'   => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
						'title' => 'Eliminate Render Blocking Resources',
					],
					[
						'id'    => '54e6f7e5e4b034c37ea9095f',
						'url'   => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
						'title' => 'How to check if AccelerateWP is caching your pages',
					],
				],
				'fr' => [
					[
						'id'    => '5697d2dc9033603f7da31041',
						'url'   => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
						'title' => 'Les pages ne sont pas mises en cache, ou la minification CSS et JS ne fonctionne pas',
					],
					[
						'id'    => '569564dfc69791436155e0b0',
						'url'   => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
						'title' => "La note Google Page Speed ne s'améliore pas",
					],
					[
						'id'    => '5697d03bc69791436155ed69',
						'url'   => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
						'title' => 'Mon site est cassé',
					],
					[
						'id'    => '601d4b83ac2f834ec5385ca5',
						'url'   => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
						'title' => 'Éliminez les ressources qui bloquent le rendu',
					],
					[
						'id'    => '568fe9ebc69791436155cd32',
						'url'   => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
						'title' => 'Comment vérifier si AccelerateWP met bien en cache vos pages',
					],
				],
			],
			'user_cache_section'         => [
				'en' => '56b55ba49033600da1c0b687,587920b5c697915403a0e1f4,560c66b0c697917e72165a6d',
				'fr' => '56cb9ba990336008e9e9e3d9,5879230cc697915403a0e211,569410999033603f7da2fa94',
			],
			'user_cache'                 => [
				'en' => [
					'id'  => '56b55ba49033600da1c0b687',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '56cb9ba990336008e9e9e3d9',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'mobile_cache_section'       => [
				'en' => '577a5f1f903360258a10e52a,5678aa76c697914361558e92,5745b9a6c697917290ddc715',
				'fr' => '589b17a02c7d3a784630b249,5a6b32830428632faf6233dc,58a480e5dd8c8e56bfa7b85c',
			],
			'mobile_cache'               => [
				'en' => [
					'id'  => '577a5f1f903360258a10e52a',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '589b17a02c7d3a784630b249',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'cache_ssl'                  => [
				'en' => [
					'id'  => '56c24fd3903360436857f1ed',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '56cb9d24c6979102ccfc801c',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'cache_lifespan'             => [
				'en' => [
					'id'  => '555c7e9ee4b027e1978e17a5',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '568f7df49033603f7da2ec72',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'cache_lifespan_section'     => [
				'en' => '555c7e9ee4b027e1978e17a5,5922fd0e0428634b4a33552c',
				'fr' => '568f7df49033603f7da2ec72,598080e1042863033a1b890e',
			],
			'nonce'                      => [
				'en' => [
					'id'  => '5922fd0e0428634b4a33552c',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '598080e1042863033a1b890e',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'css_section'                => [
				'en' => '556ef48ce4b01a224b428691,6001a83b2e764327f87bf189,5569b671e4b027e1978e3c51,5d5214d10428631e94f94ae6',
				'fr' => '5697d2dc9033603f7da31041,5d5abcce0428634552d85c1c,5697d03bc69791436155ed69,601d4b83ac2f834ec5385ca5',
			],
			'js_section'                 => [
				'en' => '54b9509de4b07997ea3f27c7,59236dfb0428634b4a3358f9,5f359695042863444aa04e26,556ef48ce4b01a224b428691,6001a83b2e764327f87bf189',
				'fr' => '56967eebc69791436155e649,593fe9882c7d3a0747cddb77,5f523c46c9e77c0016384ba0,5697d03bc69791436155ed69,601d4b83ac2f834ec5385ca5',
			],
			'file_optimization'          => [
				'en' => [
					'id'  => '6001a83b2e764327f87bf189',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '601d4b83ac2f834ec5385ca5',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'combine'                    => [
				'en' => [
					'id'  => '596eaf7d2c7d3a73488b3661',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '59a418ad042863033a1c572e',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'remove_unused_css'          => [
				'en' => [
					'id'  => '6076083ff8c0ef2d98df1f97',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '60d499a705ff892e6bc2a89e',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'exclude_inline_js'          => [
				'en' => [
					'id'  => '5b4879100428630abc0c0713',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5b4dd9290428631d7a89023c',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'exclude_js'                 => [
				'en' => [
					'id'  => '54b9509de4b07997ea3f27c7',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '56967eebc69791436155e649',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'exclude_css'                => [
				'en' => [
					'id'  => '5bf339b12c7d3a31944e2111',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5bf3bece04286304a71c6d35',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'defer_js'                   => [
				'en' => [
					'id'  => '5d52138d2c7d3a68825e8faa',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5d5ac08b2c7d3a7920be3649',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'delay_js'                   => [
				'en' => [
					'id'  => '5f359695042863444aa04e26',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '60e5b05605ff892e6bc2e86c',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'delay_js_exclusions'        => [
				'en' => [
					'id'  => '',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'async'                      => [
				'en' => [
					'id'  => '5d52144c0428631e94f94ae2',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5d5abada0428634552d85bff',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'lazyload'                   => [
				'en' => [
					'id'  => '5c884cf80428633d2cf38314',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5c98ff532c7d3a1544614cf4',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'webp'                       => [
				'fr' => [
					'id'  => '5d7b495e04286364bc8f12ef',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'en' => [
					'id'  => '5d72919704286364bc8ed49d',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'lazyload_section'           => [
				'en' => '5c884cf80428633d2cf38314,54b85754e4b0512429883a86,5418c792e4b0e7b8127bed99,569ec4a69033603f7da32c93,5419e246e4b099def9b5561e',
				'fr' => '56967a859033603f7da30858,56967952c69791436155e60a,56cb9c9d90336008e9e9e3dc,569676ea9033603f7da3083d',
			],
			'sitemap_preload'            => [
				'en' => '541780fde4b005ed2d11784c,5a71c8ab2c7d3a4a4198a9b3,55b282ede4b0b0593824f852',
				'fr' => '5693d582c69791436155d645',
			],
			'preload_bot'                => [
				'en' => '541780fde4b005ed2d11784c,55b282ede4b0b0593824f852,559113eae4b027e1978eba11',
				'fr' => '5693d582c69791436155d645,569433d1c69791436155d99c',
			],
			'preload_exclusions'         => [
				'en' => [
					'id'  => '6349682bde258f5018eb456d',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '640b30058ca4460845b4a1c4',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'bot'                        => [
				'en' => [
					'id'  => '541780fde4b005ed2d11784c',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5693d582c69791436155d645',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'fonts_preload'              => [
				'en' => [
					'id'  => '5eab7729042863474d19f647',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5eb3add02c7d3a5ea54aa66d',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'preload_links'              => [
				'en' => [
					'id'  => '5f35939b042863444aa04df9',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5f58527cc9e77c001603746c',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'ecommerce'                  => [
				'en' => [
					'id'  => '548f492de4b034fd4862493e',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '569431189033603f7da2fc13',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'cache_query_strings'        => [
				'en' => [
					'id'  => '590a83610428634b4a32d52c',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '597a04fd042863033a1b6da4',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'exclude_cache'              => [
				'en' => [
					'id'  => '5519ab03e4b061031402119f',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '56941c0cc69791436155d8ab',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'exclude_cookie'             => [
				'en' => [
					'id'  => '5fe5462df24ccf588e3fe804',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'exclude_user_agent'         => [
				'en' => [
					'id'  => '5ff728d3551e0c2853f3a245',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'always_purge'               => [
				'en' => [
					'id'  => '5ff72b4dfd168b77735328b7',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'db_optimization'            => [
				'en' => [
					'id'  => '60259156b3ebfb109b58182d',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '6040c5b90a2dae5b58fb5d29',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'cdn_section'                => [
				'en' => '5e4c84bd04286364bc958833,54c7fa3de4b0512429885b5c,54a6d578e4b047ebb774a687,56b2b4459033603f7da37acf,566f749f9033603f7da28459,5434667fe4b0310ce5ee867a',
				'fr' => '5f351e42042863444aa04652,5696830b9033603f7da308ac,569685749033603f7da308c0,57a4961190336059d4edc9d8,5697d5f8c69791436155ed8e,569684d29033603f7da308b9',
			],
			'cdn'                        => [
				'en' => [
					'id'  => '54c7fa3de4b0512429885b5c',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5696830b9033603f7da308ac',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'rocketcdn'                  => [
				'en' => [
					'id'  => '5e4c84bd04286364bc958833',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5f351e42042863444aa04652',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'rocketcdn_error'            => [
				'en' => [
					'id'  => '60ddc72d9e87cb3d01249270',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '60df1cb200fd0d7c253fc044',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'exclude_cdn'                => [
				'en' => [
					'id'  => '5434667fe4b0310ce5ee867a',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '569684d29033603f7da308b9',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'cloudflare_credentials'     => [
				'en' => [
					'id'  => '54205619e4b0e7b8127bf849',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5696837e9033603f7da308ae',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'cloudflare_settings'        => [
				'en' => [
					'id'  => '54205619e4b0e7b8127bf849',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5696837e9033603f7da308ae',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'cloudflare_credentials_api' => [
				'en' => [
					'id'  => '54205619e4b0e7b8127bf849',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5696837e9033603f7da308ae',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'cloudflare_apo'             => [
				'en' => [
					'id'  => '602593e90a2dae5b58faee1e',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '6486cb4147772865db893c7c',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'sucuri_credentials'         => [
				'en' => [
					'id'  => '5bce07be2c7d3a04dd5bf94d',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5bcf39c72c7d3a4db66085b9',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'varnish'                    => [
				'en' => [
					'id'  => '56f48132c6979115a34095bd',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '56fd2f789033601d6683e574',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'heartbeat_settings'         => [
				'en' => [
					'id'  => '5bcdfecd042863158cc7b672',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5bcf4378042863215a46bc00',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'google_fonts'               => [
				'en' => [
					'id'  => '5e8687c22c7d3a7e9aea4c4a',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5e970f512c7d3a7e9aeaf9fb',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'dynamic_lists'              => [
				'en' => [
					'id'  => '63234712b0f178684ee3b04a',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '6323604341e1a47267b8d0e3',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'image_dimensions'           => [
				'en' => [
					'id'  => '5fc70216de1bfa158fb54737',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5fd20dcab6c6251cd1c35079',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'exclude_defer_js'           => [
				'en' => [
					'id'  => '59236dfb0428634b4a3358f9',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'exclude_lazyload'           => [
				'fr' => [
					'id'  => '56967952c69791436155e60a',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'en' => [
					'id'  => '5418c792e4b0e7b8127bed99',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'invalid_exclusions'         => [
				'en' => [
					'id'  => '619e90a3d3efbe495c3b26b8',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '61b21c1297682b790dad345a',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'async_opti'                 => [
				'en' => [
					'id'  => '622a725a2ce7ed0fb0914056',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '6231fc24c1688a6d26a75ee1',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'offline'                    => [
				'en' => [
					'id'  => '60623465c44f5d025f4491de',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '6065cb184466ce6ddc5f05fb',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'fallback_css'               => [
				'en' => [
					'id'  => '5ec5c4072c7d3a5ea54b7de7',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '5edf8a5504286306f804e1dc',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'domain_change'              => [
				'en' => [
					'id'  => '577578b1903360258a10d8ba',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '57868414c697912dee72a98a',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'rucss_firewall_ips'         => [
				'en' => [
					'id'  => '60ed8bde00fd0d7c253ff547',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '6246fe1a2ce7ed0fb091c543',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'optimize_critical_images'   => [
				'en' => [
					'id'  => '662c1a144c3ddc1d4e7a1d25',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '6634e9fe0cfcb4508af6b290',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'remove_cache_tab'           => [
				'en' => [
					'id'  => '6633b5df1009cb439ac6a432',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '6634e9b21009cb439ac6a6fb',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'rucss_database'             => [
				'en' => [
					'id'  => '668f1284f0fdf93e4cf10825',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '66a32d970d7d86166241eff1',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'host_fonts_locally'         => [
				'en' => [
					'id'  => '673358b02ddbd952f6241b38',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '675ab51d46b8d26833b2af82',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
			'preconnect_domains'         => [
				'en' => [
					'id'  => '681b61d889bd957cd04bd2d9',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
				'fr' => [
					'id'  => '681da5ae11561a04f5de356e',
					'url' => 'https://docs.cloudlinux.com/user-docs/user-docs-shared-pro-cloudlinux/#acceleratewp',
				],
			],
		];

		return isset( $suggest[ $doc_id ][ $this->get_user_locale() ] )
			? $suggest[ $doc_id ][ $this->get_user_locale() ]
			: $suggest[ $doc_id ]['en'];
	}
}
