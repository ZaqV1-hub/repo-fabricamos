<?php
namespace Jet_Tabs\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Define Posts class
 */
class Elementor_Template extends Base {

	/**
	 * Query key for self-requests.
	 *
	 * @var string
	 */
	public $self_query_key = 'jet_tabs_self';

	/**
	 * Constructor to initialize the self-request handling.
	 *
	 * Adds an action to intercept self-requests through the `parse_request` hook.
	 */
	public function __construct() {
		add_action( 'parse_request', array( $this, 'handle_self_request' ) );
	}

	/**
	 * [$depended_scripts description]
	 * @var array
	 */
	public $depended_scripts = [];

	/**
	 * [$depended_styles description]
	 * @var array
	 */
	public $depended_styles = [];

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'elementor-template';
	}

	/**
	 * Returns arguments config
	 *
	 * @return [type] [description]
	 */
	public function get_args() {

		return array(
			'id' => array(
				'default'    => '',
				'required'   => false,
			),
			'dev' => array(
				'default'    => 'false',
				'required'   => false,
			),
		);
	}

	/**
	 * Prepares template data including content, scripts, and styles.
	 *
	 * It is used by both `handle_self_request` and `callback` to centralize logic.
	 *
	 * @param int  $template_id The ID of the template to retrieve.
	 * @param bool $dev Flag indicating whether the request is in development mode.
	 * @return array The prepared template data.
	 */
	public function prepare_template_data( $template_id, $dev = false ) {

		if ( ! $template_id ) {
			return false;
		}

		$query = new \WP_Query([
			'post_type' => 'elementor_library',
			'p'         => $template_id,
		]);

		if ( ! $query->have_posts() ) {
			wp_send_json_error( [ 'error' => __( 'Template not found.', 'jet-tabs' ) ] );
		}

		$plugin = \Elementor\Plugin::instance();
		$plugin->frontend->register_scripts();

		$content = $plugin->frontend->get_builder_content( $template_id, true );

		$template_scripts = [];
		$template_styles = [];

		$this->get_elementor_template_scripts( $template_id );

		foreach ( array_unique( $this->depended_scripts ) as $script ) {
			$template_scripts[ $script ] = $this->get_script_uri_by_handler( $script );
		}

		$fonts_link = $this->get_elementor_template_fonts_url( $template_id );

		if ( $fonts_link ) {
			$template_styles[ 'jet-tabs-google-fonts-css-' . $template_id ] = $fonts_link;
		}

		return [
			'template_content' => $content,
			'template_scripts' => $template_scripts,
			'template_styles'  => $template_styles,
		];
	}

	public function prepare_cached_template_data( $template_id, $dev = false ) {
		if ( ! $template_id ) {
			return false;
		}
	
		$query = new \WP_Query([
			'post_type' => 'elementor_library',
			'p'         => $template_id,
		]);
	
		if ( ! $query->have_posts() ) {
			return false; 
		}
	
		$plugin = \Elementor\Plugin::instance();
		$plugin->frontend->register_scripts();
	
		$before_styles_queue = wp_styles()->queue;
		$before_scripts_queue = wp_scripts()->queue;
	
		$content = $plugin->frontend->get_builder_content_for_display( $template_id, true );
	
		$after_styles_queue = wp_styles()->queue;
		$after_scripts_queue = wp_scripts()->queue;
	
		$template_scripts = [];
		$template_styles = [];
	
		$this->get_elementor_template_scripts( $template_id );
		foreach ( array_unique( $this->depended_scripts ) as $script ) {
			if ( isset( wp_scripts()->registered[ $script ] ) ) {
				$script_obj = wp_scripts()->registered[ $script ];
				$template_scripts[ $script ] = [
					'handle' => $script,
					'src'    => $this->get_script_uri_by_handler( $script ),
					'obj'    => $script_obj
				];
			}
		}
	
		$this->get_elementor_template_styles( $template_id );
		foreach ( array_unique( $this->depended_styles ) as $style ) {
			if ( isset( wp_styles()->registered[ $style ] ) ) {
				$style_obj = wp_styles()->registered[ $style ];
				$template_styles[ $style ] = [
					'handle' => $style,
					'src'    => $this->get_style_uri_by_handler( $style ),
					'obj'    => $style_obj
				];
			}
		}
	
		$dynamic_scripts = array_diff( $after_scripts_queue, $before_scripts_queue );
		foreach ( $dynamic_scripts as $script ) {
			if ( ! isset( $template_scripts[ $script ] ) && isset( wp_scripts()->registered[ $script ] ) ) {
				$script_obj = wp_scripts()->registered[ $script ];
				$template_scripts[ $script ] = [
					'handle' => $script,
					'src'    => $this->get_script_uri_by_handler( $script ),
					'obj'    => $script_obj
				];
			}
		}
	
		$dynamic_styles = array_diff( $after_styles_queue, $before_styles_queue );
		foreach ( $dynamic_styles as $style ) {
			if ( ! isset( $template_styles[ $style ] ) && isset( wp_styles()->registered[ $style ] ) ) {
				$style_obj = wp_styles()->registered[ $style ];
				$template_styles[ $style ] = [
					'handle' => $style,
					'src'    => $this->get_style_uri_by_handler( $style ),
					'obj'    => $style_obj
				];
			}
		}
	
		$fonts_urls = $this->get_elementor_template_fonts_urls( $template_id );
		if ( $fonts_urls ) {
			foreach ( $fonts_urls as $handle => $url ) {
				$template_styles[ $handle ] = [
					'handle' => $handle,
					'src'    => $url,
					'obj'    => null
				];
			}
		}
	
		return [
			'template_content' => $content,
			'template_scripts' => $template_scripts,
			'template_styles'  => $template_styles
		];
		
	}

	/**
	 * Parses query parameters for Self-requests.
	 *
	 * @return array Associative array of query variables.
	 */
	private function parse_query_from_self_request() {
		global $wp;

		$query_vars = [];
        $query_string = isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : ''; // phpcs:ignore
        parse_str( $query_string, $query_vars );

		return array_merge( $wp->query_vars, $query_vars );
	}

	/**
	 * Handles self-request to load template content.
	 *
	 * This method is triggered by the `parse_request` hook for self-requests.
	 *
	 * @param WP $wp Current WordPress environment instance.
	 */
	public function handle_self_request( $wp ) {
        // phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_GET[ $this->self_query_key ] ) ) {
			define( 'DOING_AJAX', true );

			$template_id = ! empty( $_GET['id'] ) ? intval( $_GET['id'] ) : false;
			$dev         = isset( $_GET['dev'] ) ? filter_var( $_GET['dev'], FILTER_VALIDATE_BOOLEAN ) : false; // phpcs:ignore
            // phpcs:enable
			if ( ! $template_id ) {
				wp_send_json_error( [ 'error' => __( 'Template ID not provided.', 'jet-tabs' ) ] );
			}

			global $wp_query, $wp;
			$original_query_vars = $wp->query_vars;
			$original_wp_query   = clone $wp_query;

			$wp->query_vars = wp_parse_args( $this->parse_query_from_self_request(), $wp->query_vars );
			$wp_query       = new \WP_Query( $wp->query_vars );

			if ( $wp_query->have_posts() ) {
				$wp_query->the_post();
			}

			$template_data = $this->prepare_template_data( $template_id, $dev );

			wp_reset_postdata();
			wp_reset_query();

			$wp->query_vars = $original_query_vars;
			$wp_query       = $original_wp_query;

			wp_send_json( $template_data );

			exit;
		}

	}

	/**
	 * [callback description]
	 * @param  [type]   $request [description]
	 * @return function          [description]
	 */
	public function callback( $request ) {

		$args = $request->get_params();

		$template_id = ! empty( $args['id'] ) ? $args['id'] : false;

		$template_id = apply_filters('wpml_object_id', $template_id, 'elementor_library', true);

		$dev = filter_var( $args['dev'], FILTER_VALIDATE_BOOLEAN ) ? true : false;

		if ( ! $template_id ) {
			return false;
		}

		$transient_key = md5( sprintf( 'jet_tabs_elementor_template_data_%s', $template_id ) );

		if ( ! $dev ) {
			$template_data = get_transient( $transient_key );

			if ( $template_data ) {
				return rest_ensure_response( $template_data );
			}

		}

		$template_data = $this->prepare_template_data( $template_id, $dev );

		if ( ! $dev ) {
			set_transient( $transient_key, $template_data, 12 * HOUR_IN_SECONDS );
		}

		return rest_ensure_response( $template_data );
	}
	
	/**
	 * Generates a self-request URL for loading a specific template.
	 *
	 * @return string The generated self-request URL.
	 */
	public static function get_self_request_url() {
		global $wp;
		$current_url = home_url( add_query_arg( [], $wp->request ) );

		return $current_url;
	}

	/**
	 * [jet_popup_get_content description]
	 * @return [type] [description]
	 */
	public function get_elementor_template_fonts_url( $template_id ) {

		$post_css = new \Elementor\Core\Files\CSS\Post( $template_id );

		$post_meta = $post_css->get_meta();

		if ( ! isset( $post_meta['fonts'] ) ) {
			return false;
		}

		$google_fonts = $post_meta['fonts'];

		$google_fonts = array_unique( $google_fonts );

		if ( empty( $google_fonts ) ) {
			return false;
		}

		function get_font_url( $font ) {
			$url = 'https://fonts.googleapis.com/css?family=' . str_replace( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';

			$response   = wp_remote_get( $url, $args = array() );
			$statusCode = wp_remote_retrieve_response_code( $response );

			if ( ! is_wp_error( $response ) ) {
				if ( $statusCode == 200 ) {
					$font_url = str_replace( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
				} else {
					\Elementor\Plugin::$instance->frontend->enqueue_font( $font );
					$font_url = false;
				}

				return $font_url;
			} else {
				return false;
			}
		}

		$fonts_url = 'https://fonts.googleapis.com/css?family=';
		$count = 0;

		foreach ( $google_fonts as $font ) {
			$font_url = get_font_url( $font );

			if ( false != $font_url ) {
				if ( $count > 0 ) {
					$fonts_url .= rawurlencode( '|' ) . $font_url;
				} else {
					$fonts_url .= $font_url;
				}
				$count++;
			}
		}

		if ( 'https://fonts.googleapis.com/css?family=' != $fonts_url ) {
			$subsets = [
				'ru_RU' => 'cyrillic',
				'bg_BG' => 'cyrillic',
				'he_IL' => 'hebrew',
				'el'    => 'greek',
				'vi'    => 'vietnamese',
				'uk'    => 'cyrillic',
				'cs_CZ' => 'latin-ext',
				'ro_RO' => 'latin-ext',
				'pl_PL' => 'latin-ext',
			];

			$locale = get_locale();

			if ( isset( $subsets[ $locale ] ) ) {
				$fonts_url .= '&subset=' . $subsets[ $locale ];
			}
		} else {
			$fonts_url = false;
		}

		return $fonts_url;
	}

	/**
 * Generates a list of URLs for Google Fonts used in the template.
 *
 * @param int $template_id The ID of the template.
 * @return array|false An array of font URLs or false if no fonts are needed.
 */
public function get_elementor_template_fonts_urls( $template_id ) {
    $post_css = new \Elementor\Core\Files\CSS\Post( $template_id );
    $post_meta = $post_css->get_meta();

    if ( ! isset( $post_meta['fonts'] ) ) {
        return false;
    }

    $fonts = $post_meta['fonts'];

    if ( empty( $fonts ) ) {
        return false;
    }

    $fonts = array_unique( $fonts );

    $google_fonts = [
        'google' => [],
        'early'  => [],
    ];

    foreach ( $fonts as $font ) {
        $font_type = \Elementor\Fonts::get_font_type( $font );

        switch ( $font_type ) {
            case \Elementor\Fonts::GOOGLE:
                $google_fonts['google'][] = $font;
                break;

            case \Elementor\Fonts::EARLYACCESS:
                $google_fonts['early'][] = $font;
                break;
        }
    }

    $urls_list = [];

    if ( ! empty( $google_fonts['google'] ) ) {
        foreach ( $google_fonts['google'] as &$font ) {
            $font = str_replace( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
        }

        $fonts_url = sprintf( 'https://fonts.googleapis.com/css?family=%s', implode( rawurlencode( '|' ), $google_fonts['google'] ) );

        $subsets = [
            'ru_RU' => 'cyrillic',
            'bg_BG' => 'cyrillic',
            'he_IL' => 'hebrew',
            'el'    => 'greek',
            'vi'    => 'vietnamese',
            'uk'    => 'cyrillic',
            'cs_CZ' => 'latin-ext',
            'ro_RO' => 'latin-ext',
            'pl_PL' => 'latin-ext',
            'hr_HR' => 'latin-ext',
            'hu_HU' => 'latin-ext',
            'sk_SK' => 'latin-ext',
            'tr_TR' => 'latin-ext',
            'lt_LT' => 'latin-ext',
        ];

        $locale = get_locale();

        if ( isset( $subsets[ $locale ] ) ) {
            $fonts_url .= '&subset=' . $subsets[ $locale ];
        }

        $urls_list[ "jet-tabs-google-fonts-{$template_id}" ] = $fonts_url;
    }

    if ( ! empty( $google_fonts['early'] ) ) {
        foreach ( $google_fonts['early'] as $current_font ) {
            $font_url = sprintf( 'https://fonts.googleapis.com/earlyaccess/%s.css', strtolower( str_replace( ' ', '', $current_font ) ) );
            $urls_list[ "jet-tabs-google-earlyaccess-{$template_id}" ] = $font_url;
        }
    }

    return ! empty( $urls_list ) ? $urls_list : false;
}

	/**
	 * [get_elementor_template_scripts_url description]
	 * @param  [type] $template_id [description]
	 * @return [type]              [description]
	 */
	public function get_elementor_template_scripts( $template_id ) {

		$document = \Elementor\Plugin::$instance->documents->get( $template_id );

		$elements_data = $document->get_elements_raw_data();

		$this->find_widgets_script_handlers( $elements_data );
	}

	/**
	 * [find_widgets_script_handlers description]
	 * @param  [type] $elements_data [description]
	 * @return [type]                [description]
	 */
	public function find_widgets_script_handlers( $elements_data ) {

		foreach ( $elements_data as $element_data ) {

			if ( 'widget' === $element_data['elType'] ) {
				$widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

				$widget_script_depends = $widget->get_script_depends();

				if ( ! empty( $widget_script_depends ) ) {
					foreach ( $widget_script_depends as $key => $script_handler ) {
						$this->depended_scripts[] = $script_handler;
					}
				}

			} else {
				$element = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

				$childrens = $element->get_children();

				foreach ( $childrens as $key => $children ) {
					$children_data[$key] = $children->get_raw_data();

					$this->find_widgets_script_handlers( $children_data );
				}
			}
		}
	}

	/**
	 * [get_script_uri_by_handler description]
	 * @param  [type] $handler [description]
	 * @return [type]          [description]
	 */
	public function get_script_uri_by_handler( $handler ) {
		global $wp_scripts;

		if ( isset( $wp_scripts->registered[ $handler ] ) ) {

			$src = $wp_scripts->registered[ $handler ]->src;

			if ( 0 === strpos( $src, site_url() ) || false === strpos( $src, site_url() ) ) {
				return $src;
			} else {
				return site_url() . $src;
			}
		}

		return false;
	}

	/**
	 * Is public endpoint.
	 *
	 * @return bool
	 */
	public function permission_callback() {
		return true;
	}

//------------------------------------------------------------------------------------
	/**
 * 
 *
 * @param int $template_id 
 */
public function get_elementor_template_styles( $template_id ) {
    
    $document = \Elementor\Plugin::$instance->documents->get( $template_id );

    if ( ! $document ) {
        return;
    }

    $elements_data = $document->get_elements_raw_data();

    $this->find_widgets_style_handlers( $elements_data );
}

/**
 * 
 *
 * @param array $elements_data 
 */
public function find_widgets_style_handlers( $elements_data ) {
    
    foreach ( $elements_data as $element_data ) {

        if ( 'widget' === $element_data['elType'] ) {
            $widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

            $widget_style_depends = $widget->get_style_depends(); // Получаем зависимости стилей

            if ( ! empty( $widget_style_depends ) ) {
                foreach ( $widget_style_depends as $style_handler ) {
                    $this->depended_styles[] = $style_handler;
                }
            }

        } else {
            $element = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

            $childrens = $element->get_children();

            foreach ( $childrens as $children ) {
                $children_data = $children->get_raw_data();

                $this->find_widgets_style_handlers( [ $children_data ] );
            }
        }
    }
	
}

/**
 * .
 *
 * @param  string $handler .
 * @return string|false .
 */
public function get_style_uri_by_handler( $handler ) {

    if ( isset( wp_styles()->registered[ $handler ] ) ) {

        $src = wp_styles()->registered[ $handler ]->src;

        if ( 0 === strpos( $src, site_url() ) || false === strpos( $src, site_url() ) ) {
            return $src;
        } else {
            return site_url() . $src;
        }
    }

    return false;
}
}
