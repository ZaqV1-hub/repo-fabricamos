<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fabricamos_Native {
	const ROLE = 'fabricante';
	const MENU_LOCATION = 'fabricamos_native_menu';
	const MENU_NAME = 'Fabricamos';
	const MENU_VERSION = 3;
	const OPTION_MAIL_TO = 'fabricamos_native_mail_to';
	const OPTION_ANNOUNCE_MAIL_TO = 'fabricamos_native_announce_mail_to';
	const OPTION_PANEL_LOGIN_EMAIL = 'fabricamos_native_panel_login_email';
	const OPTION_PANEL_LOGIN_HASH = 'fabricamos_native_panel_login_hash';
	const QUERY_SUCCESS = 'fabricamos_status';
	const QUERY_GUEST_ERROR = 'fabricamos_access_error';
	const COOKIE_MANUFACTURER = 'fabricamos_manufacturer_auth';
	const COOKIE_PANEL = 'fabricamos_panel_auth';
	const COOKIE_PUBLIC_GUEST = 'fabricamos_guest_access';
	const COOKIE_SHARED_PUBLIC_GUEST = 'abiquifi_guest_access_until';

	/**
	 * @var Fabricamos_Native|null
	 */
	protected static $instance = null;
	protected $public_auth_cookie_expiration = 0;

	/**
	 * @var array<string, string>
	 */
	protected $page_map = array(
		'catalogo'              => 'catalog-buyer',
		'fabricamos'            => 'catalog-buyer',
		'login'                 => 'site-login',
		'entrar'                => 'site-login',
		'log-in'                => 'site-login',
		'cadastro'              => 'site-register',
		'fabricante'            => 'login',
		'fabricamos-fabricante' => 'catalog-seller',
		'anunciar-fabricante'   => 'announce',
		'fabricamos-login'      => 'login',
		'meu-fabricante'        => 'profile',
		'painel'                => 'panel',
	);

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function activate() {
		$self = self::instance();
		$self->register_role();
		$self->register_content_types();
		$self->register_menu_location();
		$self->ensure_pages();
		$self->ensure_navigation_menu();
		$self->ensure_front_page();
		$self->ensure_default_panel_credentials();
		flush_rewrite_rules();
	}

	public static function deactivate() {
		flush_rewrite_rules();
	}

	protected function __construct() {
		add_action( 'init', array( $this, 'bootstrap_guest_access' ), 5 );
		add_action( 'init', array( $this, 'register_role' ) );
		add_action( 'init', array( $this, 'register_content_types' ) );
		add_action( 'init', array( $this, 'register_menu_location' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_action( 'init', array( $this, 'maybe_bootstrap_pages' ), 20 );
		add_action( 'template_redirect', array( $this, 'maybe_handle_public_auth_pages' ), 1 );
		add_action( 'template_redirect', array( $this, 'stabilize_public_buyer_entrypoint' ), 0 );
		add_action( 'template_redirect', array( $this, 'handle_legacy_routes' ) );
		add_filter( 'template_include', array( $this, 'template_include' ), 100000 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'acf/init', array( $this, 'register_acf_fields' ) );
		add_filter( 'acf/fields/relationship/query/name=fab_substances', array( $this, 'filter_substance_relationship_query' ), 10, 3 );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'wp_ajax_nopriv_fabricamos_search_substances', array( $this, 'ajax_search_substances' ) );
		add_action( 'wp_ajax_fabricamos_search_substances', array( $this, 'ajax_search_substances' ) );
		add_action( 'wp_ajax_nopriv_fabricamos_search_manufacturers', array( $this, 'ajax_search_manufacturers' ) );
		add_action( 'wp_ajax_fabricamos_search_manufacturers', array( $this, 'ajax_search_manufacturers' ) );
		add_action( 'admin_post_nopriv_fabricamos_login', array( $this, 'handle_login' ) );
		add_action( 'admin_post_fabricamos_login', array( $this, 'handle_login' ) );
		add_action( 'admin_post_nopriv_fabricamos_site_login', array( $this, 'handle_site_login' ) );
		add_action( 'admin_post_fabricamos_site_login', array( $this, 'handle_site_login' ) );
		add_action( 'admin_post_nopriv_fabricamos_site_register', array( $this, 'handle_site_register' ) );
		add_action( 'admin_post_fabricamos_site_register', array( $this, 'handle_site_register' ) );
		add_action( 'admin_post_nopriv_fabricamos_guest_access', array( $this, 'handle_guest_access' ) );
		add_action( 'admin_post_fabricamos_guest_access', array( $this, 'handle_guest_access' ) );
		add_action( 'admin_post_nopriv_fabricamos_announce', array( $this, 'handle_announce' ) );
		add_action( 'admin_post_fabricamos_announce', array( $this, 'handle_announce' ) );
		add_action( 'admin_post_nopriv_fabricamos_profile', array( $this, 'handle_profile_update' ) );
		add_action( 'admin_post_fabricamos_profile', array( $this, 'handle_profile_update' ) );
		add_action( 'admin_post_nopriv_fabricamos_panel_login', array( $this, 'handle_panel_login' ) );
		add_action( 'admin_post_fabricamos_panel_login', array( $this, 'handle_panel_login' ) );
		add_action( 'admin_post_nopriv_fabricamos_logout', array( $this, 'handle_logout' ) );
		add_action( 'admin_post_fabricamos_logout', array( $this, 'handle_logout' ) );
		add_action( 'admin_post_nopriv_fabricamos_panel_save', array( $this, 'handle_panel_save' ) );
		add_action( 'admin_post_fabricamos_panel_save', array( $this, 'handle_panel_save' ) );
		add_action( 'admin_post_nopriv_fabricamos_panel_delete', array( $this, 'handle_panel_delete' ) );
		add_action( 'admin_post_fabricamos_panel_delete', array( $this, 'handle_panel_delete' ) );
		add_action( 'admin_post_nopriv_fabricamos_panel_export', array( $this, 'handle_panel_export' ) );
		add_action( 'admin_post_fabricamos_panel_export', array( $this, 'handle_panel_export' ) );
		add_filter( 'show_admin_bar', array( $this, 'maybe_hide_admin_bar' ) );
		add_filter( 'auth_cookie_expiration', array( $this, 'filter_auth_cookie_expiration' ), 10, 3 );
		add_filter( 'pre_get_document_title', array( $this, 'filter_document_title' ) );
		add_filter( 'document_title_parts', array( $this, 'filter_document_title_parts' ) );
	}

	public function register_role() {
		add_role(
			self::ROLE,
			'Fabricante',
			array(
				'read' => true,
			)
		);
	}

	public function register_content_types() {
		register_post_type(
			'fabricante',
			array(
				'labels' => array(
					'name'          => 'Fabricantes',
					'singular_name' => 'Fabricante',
					'add_new_item'  => 'Adicionar fabricante',
					'edit_item'     => 'Editar fabricante',
					'new_item'      => 'Novo fabricante',
					'view_item'     => 'Ver fabricante',
					'search_items'  => 'Pesquisar fabricantes',
				),
				'public'             => true,
				'has_archive'        => false,
				'rewrite'            => array(
					'slug'       => 'fabricante',
					'with_front' => false,
				),
				'show_in_rest'       => true,
				'supports'           => array( 'title', 'author', 'thumbnail' ),
				'menu_icon'          => 'dashicons-building',
				'publicly_queryable' => true,
				'show_ui'            => true,
			)
		);

		register_taxonomy(
			'fabricante_setor',
			array( 'fabricante' ),
			array(
				'labels'       => array(
					'name'          => 'Setores',
					'singular_name' => 'Setor',
				),
				'public'       => true,
				'hierarchical' => true,
				'show_in_rest' => true,
				'rewrite'      => false,
			)
		);
	}

	public function register_menu_location() {
		register_nav_menu( self::MENU_LOCATION, 'Menu Fabricamos' );
	}

	public function maybe_bootstrap_pages() {
		$this->ensure_navigation_menu();
		$this->ensure_pages();
		$this->ensure_front_page_template();
		$this->ensure_default_panel_credentials();

		if ( get_option( 'fabricamos_native_bootstrapped' ) ) {
			return;
		}
		update_option( 'fabricamos_native_bootstrapped', 1, false );
	}

	protected function ensure_pages() {
		$pages = array(
			'catalogo' => array(
				'title'   => 'Catálogo',
				'content' => 'Catálogo de fabricantes do ecossistema Abiquifi para consulta, descoberta e navegação profissional por setor, empresa e substância.',
			),
			'fabricamos' => array(
				'title'   => 'Fabricamos',
				'content' => 'Catálogo de fabricantes do ecossistema Abiquifi para consulta, descoberta e navegação profissional por setor, empresa e substância.',
			),
			'login' => array(
				'title'   => 'Entrar',
				'content' => 'Acesse sua conta do Fabricamos.',
			),
			'entrar' => array(
				'title'   => 'Entrar',
				'content' => 'Acesse sua conta do Fabricamos.',
			),
			'log-in' => array(
				'title'   => 'Log In',
				'content' => 'Faça login para acessar o dicionário.',
			),
			'cadastro' => array(
				'title'   => 'Cadastro',
				'content' => 'Crie sua conta para acessar o dicionário.',
			),
			'fabricante' => array(
				'title'   => 'Fabricante',
				'content' => 'Acesse a área do fabricante no Fabricamos.',
			),
			'fabricamos-fabricante' => array(
				'title'   => 'Fabricamos - Fabricante',
				'content' => 'Área do fabricante para consultar o catálogo e gerenciar o próprio cadastro.',
			),
			'anunciar-fabricante' => array(
				'title'   => 'Anunciar Fabricante',
				'content' => 'Formulário para solicitar o anúncio de um fabricante no Fabricamos.',
			),
			'fabricamos-login' => array(
				'title'   => 'Login Fabricamos',
				'content' => 'Acesse a área do fabricante no Fabricamos.',
			),
			'meu-fabricante' => array(
				'title'   => 'Meu Fabricante',
				'content' => 'Atualize as informações públicas do seu fabricante.',
			),
			'painel' => array(
				'title'   => 'Painel',
				'content' => 'Painel administrativo do Fabricamos.',
			),
			'detalhes-do-fabricante' => array(
				'title'   => 'Detalhes do Fabricante',
				'content' => 'Rota legada do detalhe do fabricante.',
			),
		);

		foreach ( $pages as $slug => $config ) {
			$page = get_page_by_path( $slug, OBJECT, 'page' );
			if ( $page ) {
				$update_args = array(
					'ID' => $page->ID,
				);

				if ( 'publish' !== $page->post_status ) {
					$update_args['post_status'] = 'publish';
				}

				if ( $this->should_replace_legacy_page_content( $page->post_content ) ) {
					$update_args['post_content'] = $config['content'];
				}

				if ( count( $update_args ) > 1 ) {
					wp_update_post( $update_args );
				}
				continue;
			}

			wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $config['title'],
					'post_content' => $config['content'],
				)
			);
		}
	}

	protected function should_replace_legacy_page_content( $content ) {
		$content = (string) $content;

		if ( '' === trim( $content ) ) {
			return true;
		}

		return false !== strpos( $content, '[fabricamos_' ) || false !== strpos( $content, 'fab-shell' );
	}

	protected function ensure_front_page() {
		$page = get_page_by_path( 'fabricamos', OBJECT, 'page' );
		if ( ! $page ) {
			return;
		}

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $page->ID );
	}

	protected function ensure_front_page_template() {
		$page = get_page_by_path( 'fabricamos', OBJECT, 'page' );
		if ( ! $page ) {
			return;
		}

		$template = (string) get_post_meta( $page->ID, '_wp_page_template', true );
		if ( 'default' !== $template ) {
			update_post_meta( $page->ID, '_wp_page_template', 'default' );
		}
	}

	protected function ensure_navigation_menu() {
		$menu = wp_get_nav_menu_object( self::MENU_NAME );
		$menu_id = $menu ? (int) $menu->term_id : 0;

		if ( ! $menu_id ) {
			$menu_id = (int) wp_create_nav_menu( self::MENU_NAME );
		}

		if ( ! $menu_id ) {
			return;
		}

		$theme_locations = (array) get_theme_mod( 'nav_menu_locations', array() );
		if ( empty( $theme_locations[ self::MENU_LOCATION ] ) ) {
			$theme_locations[ self::MENU_LOCATION ] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $theme_locations );
		}

		if ( (int) get_option( 'fabricamos_native_menu_version', 0 ) >= self::MENU_VERSION ) {
			return;
		}

		$this->seed_navigation_menu_items( $menu_id );
		update_option( 'fabricamos_native_menu_version', self::MENU_VERSION, false );
	}

	/**
	 * @return array<int, array<string, string>>
	 */
	protected function default_menu_items() {
		$items = array(
			array(
				'title' => 'Home',
				'url'   => $this->public_catalog_url(),
			),
			array(
				'title' => 'site abiquifi',
				'url'   => 'https://abiquifi.questione.ai/',
			),
		);

		return $items;
	}

	protected function seed_navigation_menu_items( $menu_id ) {
		$existing_items = wp_get_nav_menu_items(
			$menu_id,
			array(
				'post_status' => 'any',
			)
		);

		if ( ! empty( $existing_items ) ) {
			foreach ( $existing_items as $item ) {
				wp_delete_post( $item->ID, true );
			}
		}

		foreach ( $this->default_menu_items() as $item ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => $item['title'],
					'menu-item-url'    => $item['url'],
					'menu-item-status' => 'publish',
				)
			);
		}
	}

	protected function page_url_or_placeholder( $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page && 'publish' === $page->post_status ) {
			return get_permalink( $page );
		}

		return '#';
	}

	protected function get_site_register_url() {
		$page = get_page_by_path( 'cadastro', OBJECT, 'page' );
		if ( $page && 'publish' === $page->post_status ) {
			return get_permalink( $page );
		}

		return home_url( '/cadastro/' );
	}

	protected function public_catalog_url() {
		$page = get_page_by_path( 'catalogo', OBJECT, 'page' );
		if ( $page && 'publish' === $page->post_status ) {
			return get_permalink( $page );
		}

		return home_url( '/catalogo/' );
	}

	protected function get_site_login_page_url() {
		foreach ( array( 'login', 'log-in', 'entrar' ) as $slug ) {
			$page = get_page_by_path( $slug, OBJECT, 'page' );
			if ( $page && 'publish' === $page->post_status ) {
				return get_permalink( $page );
			}
		}

		return home_url( '/login/' );
	}

	protected function privacy_policy_url() {
		if ( function_exists( 'wp_get_privacy_policy_url' ) ) {
			return (string) wp_get_privacy_policy_url();
		}

		$privacy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
		if ( $privacy_page_id > 0 ) {
			$url = get_permalink( $privacy_page_id );
			if ( is_string( $url ) ) {
				return $url;
			}
		}

		return '';
	}

	protected function get_cookie_path() {
		return defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/';
	}

	protected function get_cookie_domain() {
		return defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';
	}

	protected function public_sso() {
		if ( class_exists( 'Abiquifi_Public_SSO' ) ) {
			return Abiquifi_Public_SSO::instance();
		}

		return null;
	}

	public function is_public_site_authenticated() {
		$sso = $this->public_sso();
		if ( $sso ) {
			return $sso->is_public_authenticated();
		}

		return $this->is_public_site_user( wp_get_current_user() );
	}

	protected function get_public_site_user_name() {
		$sso = $this->public_sso();
		if ( ! $sso ) {
			$user = wp_get_current_user();
			return $this->is_public_site_user( $user ) ? (string) $user->display_name : '';
		}

		$user = $sso->get_public_user();
		if ( empty( $user['display_name'] ) ) {
			$current_user = wp_get_current_user();
			return $this->is_public_site_user( $current_user ) ? (string) $current_user->display_name : '';
		}

		return (string) $user['display_name'];
	}

	protected function get_public_site_user_email() {
		$sso = $this->public_sso();
		if ( ! $sso ) {
			$user = wp_get_current_user();
			return $this->is_public_site_user( $user ) ? sanitize_email( (string) $user->user_email ) : '';
		}

		$user_email = '';
		$user       = $sso->get_public_user();
		if ( ! empty( $user['user_email'] ) ) {
			$user_email = sanitize_email( (string) $user['user_email'] );
		}

		if ( '' === $user_email ) {
			$current_user = wp_get_current_user();
			if ( $this->is_public_site_user( $current_user ) ) {
				$user_email = sanitize_email( (string) $current_user->user_email );
			}
		}

		return $user_email;
	}

	public function get_announce_form_defaults() {
		return array(
			'name'  => $this->get_public_site_user_name(),
			'email' => $this->get_public_site_user_email(),
		);
	}

	protected function read_env_value( $key, $default = '' ) {
		$key = (string) $key;

		if ( '' === $key ) {
			return $default;
		}

		$value = getenv( $key );
		if ( false !== $value && '' !== trim( (string) $value ) ) {
			return trim( (string) $value );
		}

		if ( isset( $_ENV[ $key ] ) && '' !== trim( (string) $_ENV[ $key ] ) ) {
			return trim( (string) $_ENV[ $key ] );
		}

		if ( isset( $_SERVER[ $key ] ) && '' !== trim( (string) $_SERVER[ $key ] ) ) {
			return trim( (string) $_SERVER[ $key ] );
		}

		if ( defined( $key ) ) {
			$value = constant( $key );
			if ( is_scalar( $value ) && '' !== trim( (string) $value ) ) {
				return trim( (string) $value );
			}
		}

		return $default;
	}

	protected function default_mail_recipient() {
		$recipient = sanitize_email( $this->read_env_value( 'ABIQUIFI_MAIL_TO', 'marketing@abiquifi.org.br' ) );

		if ( '' === $recipient || ! is_email( $recipient ) ) {
			$recipient = 'marketing@abiquifi.org.br';
		}

		return $recipient;
	}

	protected function get_announce_recipient_email() {
		$recipient = sanitize_email( $this->read_env_value( 'ABIQUIFI_ANNOUNCE_MAIL_TO', (string) get_option( self::OPTION_ANNOUNCE_MAIL_TO, '' ) ) );
		if ( '' === $recipient || 0 === strcasecmp( $recipient, 'Isaque@brevia.company' ) ) {
			$recipient = $this->default_mail_recipient();
		}

		if ( '' === $recipient || ! is_email( $recipient ) ) {
			$recipient = sanitize_email( (string) get_option( self::OPTION_MAIL_TO ) );
			if ( '' === $recipient || 0 === strcasecmp( $recipient, 'Isaque@brevia.company' ) ) {
				$recipient = $this->default_mail_recipient();
			}
		}

		if ( '' === $recipient || ! is_email( $recipient ) ) {
			$recipient = $this->default_mail_recipient();
		}

		return $recipient;
	}

	protected function get_announce_sender_email() {
		$sender = sanitize_email( $this->read_env_value( 'ABIQUIFI_MAIL_FROM_EMAIL', 'marketing@abiquifi.org.br' ) );

		if ( '' === $sender || ! is_email( $sender ) ) {
			$sender = sanitize_email( (string) get_option( 'admin_email' ) );
		}

		if ( '' === $sender || ! is_email( $sender ) ) {
			$sender = sanitize_email( (string) ini_get( 'sendmail_from' ) );
		}

		if ( '' === $sender || ! is_email( $sender ) ) {
			$sender = 'marketing@abiquifi.org.br';
		}

		return $sender;
	}

	protected function get_mail_from_name() {
		return $this->read_env_value( 'ABIQUIFI_MAIL_FROM_NAME', 'Fabricamos | Abiquifi' );
	}

	public function filter_auth_cookie_expiration( $length, $user_id, $remember ) {
		if ( $this->public_auth_cookie_expiration > 0 ) {
			return $this->public_auth_cookie_expiration;
		}

		return $length;
	}

	public function dictionary_url() {
		$page = get_page_by_path( 'dicionario-dsf', OBJECT, 'page' );
		if ( $page && 'publish' === $page->post_status ) {
			return get_permalink( $page );
		}

		return home_url( '/dicionario-dsf/' );
	}

	protected function is_public_site_user( $user ) {
		if ( ! $user instanceof WP_User || ! $user->exists() ) {
			return false;
		}

		if ( $this->is_panel_user( $user ) || $this->is_manufacturer_user( $user ) ) {
			return false;
		}

		return true;
	}

	protected function build_public_registration_profile_meta( $institution, $phone, $activity, $department, $job_title ) {
		return array(
			'dsf_institution_name'    => $institution,
			'dsf_phone'               => $phone,
			'dsf_activity_sector'     => $activity,
			'dsf_department'          => $department,
			'dsf_job_title'           => $job_title,
			'dsf_privacy_accepted_at' => current_time( 'mysql' ),
			'dsf_privacy_policy_url'  => $this->privacy_policy_url(),
		);
	}

	public function get_dictionary_activity_options() {
		return array(
			'Agência Governamental',
			'Consultorias',
			'Distribuidor / Importador',
			'Entidade de Classe',
			'Fabricante de IFA',
			'Fabricante de Medicamentos',
			'ICT - Instituto de Ciência e Tecnologia',
			'Laboratório Oficial',
			'Universidades',
			'Órgãos de Administração Pública',
			'Outros',
		);
	}

	public function get_dictionary_department_options() {
		return array(
			'Suprimentos',
			'Importação',
			'MKT',
			'Vendas',
			'Exportação',
			'PD&I',
			'Assuntos Regulatórios',
			'Novos Negócios',
			'Outros',
		);
	}

	public function get_dictionary_job_title_options() {
		return array(
			'Analista',
			'Pesquisador',
			'Coordenador',
			'Gerente',
			'Especialista',
			'Consultor',
			'Diretor',
			'Presidente / Vice-Presidente / CEO',
			'Outros',
		);
	}

	protected function normalize_dictionary_choice_key( $value ) {
		$value = remove_accents( (string) $value );
		$value = strtolower( trim( $value ) );
		$value = preg_replace( '/[^a-z0-9]+/i', ' ', $value );

		return is_string( $value ) ? trim( preg_replace( '/\s+/', ' ', $value ) ) : '';
	}

	protected function resolve_dictionary_choice( $value, $options, $fallback_to_others = false ) {
		$value   = trim( (string) $value );
		$options = array_values( array_filter( array_map( 'strval', (array) $options ) ) );

		if ( '' === $value ) {
			return '';
		}

		if ( in_array( $value, $options, true ) ) {
			return $value;
		}

		$normalized_value = $this->normalize_dictionary_choice_key( $value );
		foreach ( $options as $option ) {
			if ( $normalized_value === $this->normalize_dictionary_choice_key( $option ) ) {
				return $option;
			}
		}

		if ( $fallback_to_others && in_array( 'Outros', $options, true ) ) {
			$this->log_registration_debug(
				'Cadastro Fabricamos recebeu opcao fora da lista e foi ajustado para Outros.',
				array(
					'original_value' => $value,
					'normalized'     => $normalized_value,
				)
			);
			return 'Outros';
		}

		return '';
	}

	protected function normalize_public_registration_choices( $activity, $department, $job_title ) {
		return array(
			'activity'   => $this->resolve_dictionary_choice( $activity, $this->get_dictionary_activity_options(), true ),
			'department' => $this->resolve_dictionary_choice( $department, $this->get_dictionary_department_options(), true ),
			'job_title'  => $this->resolve_dictionary_choice( $job_title, $this->get_dictionary_job_title_options(), true ),
		);
	}

	public function bootstrap_guest_access() {
		$this->maybe_create_guest_access_table();

		if ( 0 === wp_rand( 0, 25 ) ) {
			$this->prune_expired_guest_access();
		}
	}

	public function get_guest_job_title_options() {
		return array(
			'Diretor',
			'Vice-diretor',
			'Presidente',
			'Vice-presidente',
			'Gerente',
			'Outros',
		);
	}

	public function guest_access_error_message( $error_code ) {
		switch ( $error_code ) {
			case 'nonce':
				return 'Não foi possível validar o formulário. Tente novamente.';
			case 'required':
				return 'Preencha todos os campos obrigatórios.';
			case 'email':
				return 'Informe um e-mail válido.';
			case 'phone':
				return 'Informe um telefone válido.';
			case 'job_title':
				return 'Selecione um cargo válido.';
			case 'save':
				return 'Não foi possível liberar o acesso. Tente novamente.';
			default:
				return '';
		}
	}

	protected function guest_access_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'fabricamos_guest_access';
	}

	protected function maybe_create_guest_access_table() {
		if ( get_option( 'fabricamos_native_guest_access_table_v1' ) ) {
			return;
		}

		global $wpdb;

		$table_name      = $this->guest_access_table_name();
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			session_id varchar(64) NOT NULL,
			full_name varchar(191) NOT NULL,
			phone varchar(60) NOT NULL,
			email varchar(191) NOT NULL,
			company varchar(191) NOT NULL,
			job_title varchar(100) NOT NULL,
			expires_at datetime NOT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY session_id (session_id),
			KEY email (email),
			KEY expires_at (expires_at)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'fabricamos_native_guest_access_table_v1', 1, false );
	}

	protected function prune_expired_guest_access() {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				'DELETE FROM ' . $this->guest_access_table_name() . ' WHERE expires_at < %s',
				gmdate( 'Y-m-d H:i:s' )
			)
		);
	}

	protected function guest_cookie_name() {
		return self::COOKIE_PUBLIC_GUEST;
	}

	protected function shared_guest_cookie_name() {
		return self::COOKIE_SHARED_PUBLIC_GUEST;
	}

	protected function shared_guest_cookie_domain() {
		return '.abiquifi.questione.ai';
	}

	protected function guest_access_ttl() {
		return DAY_IN_SECONDS;
	}

	protected function set_guest_cookie( $token, $expires_at ) {
		$params = array(
			'expires'  => (int) $expires_at,
			'path'     => '/',
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		);

		if ( PHP_VERSION_ID >= 70300 ) {
			setcookie( $this->guest_cookie_name(), $token, $params );
		} else {
			setcookie( $this->guest_cookie_name(), $token, (int) $expires_at, '/; samesite=Lax', '', is_ssl(), true );
		}

		$_COOKIE[ $this->guest_cookie_name() ] = $token;
	}

	protected function set_shared_guest_cookie( $expires_at ) {
		$params = array(
			'expires'  => (int) $expires_at,
			'path'     => '/',
			'domain'   => $this->shared_guest_cookie_domain(),
			'secure'   => is_ssl(),
			'httponly' => false,
			'samesite' => 'Lax',
		);

		if ( PHP_VERSION_ID >= 70300 ) {
			setcookie( $this->shared_guest_cookie_name(), (string) (int) $expires_at, $params );
		} else {
			setcookie( $this->shared_guest_cookie_name(), (string) (int) $expires_at, (int) $expires_at, '/; samesite=Lax', $this->shared_guest_cookie_domain(), is_ssl(), false );
		}

		$_COOKIE[ $this->shared_guest_cookie_name() ] = (string) (int) $expires_at;
	}

	protected function clear_guest_cookie() {
		if ( PHP_VERSION_ID >= 70300 ) {
			setcookie(
				$this->guest_cookie_name(),
				'',
				array(
					'expires'  => time() - HOUR_IN_SECONDS,
					'path'     => '/',
					'secure'   => is_ssl(),
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
		} else {
			setcookie( $this->guest_cookie_name(), '', time() - HOUR_IN_SECONDS, '/; samesite=Lax', '', is_ssl(), true );
		}

		unset( $_COOKIE[ $this->guest_cookie_name() ] );
	}

	protected function clear_shared_guest_cookie() {
		if ( PHP_VERSION_ID >= 70300 ) {
			setcookie(
				$this->shared_guest_cookie_name(),
				'',
				array(
					'expires'  => time() - HOUR_IN_SECONDS,
					'path'     => '/',
					'domain'   => $this->shared_guest_cookie_domain(),
					'secure'   => is_ssl(),
					'httponly' => false,
					'samesite' => 'Lax',
				)
			);
		} else {
			setcookie( $this->shared_guest_cookie_name(), '', time() - HOUR_IN_SECONDS, '/; samesite=Lax', $this->shared_guest_cookie_domain(), is_ssl(), false );
		}

		unset( $_COOKIE[ $this->shared_guest_cookie_name() ] );
	}

	public function clear_public_access_state() {
		$this->clear_guest_cookie();
		$this->clear_shared_guest_cookie();
	}

	public function has_shared_guest_access() {
		$expires_at = isset( $_COOKIE[ $this->shared_guest_cookie_name() ] )
			? (int) sanitize_text_field( wp_unslash( $_COOKIE[ $this->shared_guest_cookie_name() ] ) )
			: 0;

		if ( $expires_at <= time() ) {
			if ( $expires_at > 0 ) {
				$this->clear_shared_guest_cookie();
			}

			return false;
		}

		return true;
	}

	protected function guest_access_entry() {
		global $wpdb;

		$token = isset( $_COOKIE[ $this->guest_cookie_name() ] )
			? sanitize_text_field( wp_unslash( $_COOKIE[ $this->guest_cookie_name() ] ) )
			: '';

		if ( '' === $token ) {
			return null;
		}

		$row = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . $this->guest_access_table_name() . ' WHERE session_id = %s LIMIT 1',
				wp_hash( $token )
			),
			ARRAY_A
		);

		if ( empty( $row ) || empty( $row['expires_at'] ) ) {
			$this->clear_guest_cookie();
			return null;
		}

		if ( strtotime( (string) $row['expires_at'] ) <= time() ) {
			$this->clear_guest_cookie();
			return null;
		}

		return $row;
	}

	public function get_guest_access_entry() {
		return $this->guest_access_entry();
	}

	public function has_catalog_guest_access() {
		if ( $this->has_shared_guest_access() ) {
			return true;
		}

		return null !== $this->guest_access_entry();
	}

	protected function has_public_account_cookie() {
		if ( ! class_exists( 'Abiquifi_Public_SSO' ) ) {
			return false;
		}

		$cookie_name = Abiquifi_Public_SSO::COOKIE_NAME;
		if ( empty( $cookie_name ) ) {
			return false;
		}

		return ! empty( $_COOKIE[ $cookie_name ] );
	}

	public function has_catalog_access() {
		if ( $this->is_public_site_authenticated() ) {
			return true;
		}

		if ( $this->has_public_account_cookie() ) {
			return true;
		}

		return $this->has_catalog_guest_access();
	}

	protected function get_public_login_url( $redirect_to = '' ) {
		return $this->site_login_url( $redirect_to );
	}

	protected function get_public_register_url( $redirect_to = '' ) {
		return $this->site_register_url( $redirect_to );
	}

	protected function current_request_url() {
		$scheme = is_ssl() ? 'https' : 'http';
		$host   = isset( $_SERVER['HTTP_HOST'] ) ? wp_unslash( $_SERVER['HTTP_HOST'] ) : wp_parse_url( home_url( '/' ), PHP_URL_HOST );
		$uri    = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';

		return esc_url_raw( $scheme . '://' . $host . $uri );
	}

	public function public_current_request_url() {
		return $this->current_request_url();
	}

	protected function build_redirected_page_url( $base_url, $redirect_to = '' ) {
		$base_url = (string) $base_url;

		if ( '' === $redirect_to ) {
			return $base_url;
		}

		return add_query_arg( 'redirect_to', $redirect_to, $base_url );
	}

	protected function is_public_catalog_request() {
		if ( is_singular( 'fabricante' ) ) {
			return true;
		}

		if ( is_front_page() && $this->is_fabricamos_front_page() ) {
			return true;
		}

		return is_page( array( 'fabricamos', 'fabricamos-fabricante' ) );
	}

	protected function is_public_buyer_catalog_request() {
		if ( is_singular( 'fabricante' ) ) {
			return true;
		}

		if ( is_front_page() && $this->is_fabricamos_front_page() ) {
			return true;
		}

		return is_page( array( 'fabricamos', 'catalogo' ) );
	}

	public function stabilize_public_buyer_entrypoint() {
		if ( is_admin() ) {
			return;
		}

		if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
			return;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		if ( ! $this->is_public_buyer_catalog_request() ) {
			return;
		}

		remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );
		$this->ensure_front_page_template();
	}

	protected function enforce_public_catalog_auth() {
		if ( ! $this->is_public_catalog_request() ) {
			return;
		}

		if ( $this->has_catalog_access() ) {
			return;
		}

		if ( $this->is_public_buyer_catalog_request() ) {
			return;
		}

		wp_safe_redirect( $this->site_login_url( $this->current_request_url() ) );
		exit;
	}

	public function maybe_handle_public_auth_pages() {
		if ( is_admin() ) {
			return;
		}

		if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
			return;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : 'GET';
		if ( 'POST' !== $method ) {
			return;
		}

		if ( is_page( array( 'login', 'entrar', 'log-in' ) ) ) {
			$this->handle_site_login();
		}

		if ( is_page( 'cadastro' ) ) {
			$this->handle_site_register();
		}
	}

	public function rest_catalog_permission() {
		if ( $this->has_catalog_access() ) {
			return true;
		}

		if ( $this->is_panel_authenticated() ) {
			return true;
		}

		if ( $this->is_manufacturer_authenticated() ) {
			return true;
		}

		return current_user_can( 'manage_options' );
	}

	protected function set_auth_cookie( $name, $value, $remember = false ) {
		$expire = $remember ? time() + WEEK_IN_SECONDS * 2 : 0;
		setcookie( $name, $value, $expire, $this->get_cookie_path(), $this->get_cookie_domain(), is_ssl(), true );
		$_COOKIE[ $name ] = $value;
	}

	protected function clear_auth_cookie( $name ) {
		setcookie( $name, '', time() - HOUR_IN_SECONDS, $this->get_cookie_path(), $this->get_cookie_domain(), is_ssl(), true );
		unset( $_COOKIE[ $name ] );
	}

	protected function generate_auth_token() {
		return wp_generate_password( 40, false, false );
	}

	protected function get_authenticated_manufacturer_id() {
		$cookie = isset( $_COOKIE[ self::COOKIE_MANUFACTURER ] ) ? (string) wp_unslash( $_COOKIE[ self::COOKIE_MANUFACTURER ] ) : '';
		if ( '' === $cookie || false === strpos( $cookie, '|' ) ) {
			return 0;
		}

		list( $manufacturer_id, $token ) = array_pad( explode( '|', $cookie, 2 ), 2, '' );
		$manufacturer_id = absint( $manufacturer_id );
		$token           = (string) $token;

		if ( ! $manufacturer_id || '' === $token ) {
			return 0;
		}

		$stored = (string) get_post_meta( $manufacturer_id, 'fab_login_session_token', true );
		if ( '' === $stored || ! hash_equals( $stored, $token ) ) {
			return 0;
		}

		return $manufacturer_id;
	}

	public function get_authenticated_manufacturer() {
		$manufacturer_id = $this->get_authenticated_manufacturer_id();
		if ( ! $manufacturer_id ) {
			return null;
		}

		$post = get_post( $manufacturer_id );
		return $post instanceof WP_Post ? $post : null;
	}

	public function is_manufacturer_authenticated() {
		return $this->get_authenticated_manufacturer_id() > 0;
	}

	protected function set_manufacturer_auth( $manufacturer_id, $remember = false ) {
		$manufacturer_id = absint( $manufacturer_id );
		if ( ! $manufacturer_id ) {
			return;
		}

		$token = $this->generate_auth_token();
		update_post_meta( $manufacturer_id, 'fab_login_session_token', $token );
		$this->set_auth_cookie( self::COOKIE_MANUFACTURER, $manufacturer_id . '|' . $token, $remember );
	}

	protected function clear_manufacturer_auth() {
		$manufacturer_id = $this->get_authenticated_manufacturer_id();
		if ( $manufacturer_id ) {
			delete_post_meta( $manufacturer_id, 'fab_login_session_token' );
		}
		$this->clear_auth_cookie( self::COOKIE_MANUFACTURER );
	}

	protected function get_authenticated_panel_email() {
		$cookie = isset( $_COOKIE[ self::COOKIE_PANEL ] ) ? (string) wp_unslash( $_COOKIE[ self::COOKIE_PANEL ] ) : '';
		if ( '' === $cookie || false === strpos( $cookie, '|' ) ) {
			return '';
		}

		list( $email, $token ) = array_pad( explode( '|', $cookie, 2 ), 2, '' );
		$email = sanitize_email( $email );
		$token = (string) $token;

		if ( '' === $email || '' === $token ) {
			return '';
		}

		$stored_email = sanitize_email( (string) get_option( self::OPTION_PANEL_LOGIN_EMAIL, '' ) );
		$stored_token = (string) get_option( 'fabricamos_native_panel_session_token', '' );

		if ( '' === $stored_email || '' === $stored_token ) {
			return '';
		}

		if ( 0 !== strcasecmp( $stored_email, $email ) || ! hash_equals( $stored_token, $token ) ) {
			return '';
		}

		return $stored_email;
	}

	public function is_panel_authenticated() {
		return '' !== $this->get_authenticated_panel_email();
	}

	protected function set_panel_auth( $remember = false ) {
		$email = sanitize_email( (string) get_option( self::OPTION_PANEL_LOGIN_EMAIL, '' ) );
		if ( '' === $email ) {
			return;
		}

		$token = $this->generate_auth_token();
		update_option( 'fabricamos_native_panel_session_token', $token, false );
		$this->set_auth_cookie( self::COOKIE_PANEL, $email . '|' . $token, $remember );
	}

	protected function clear_panel_auth() {
		delete_option( 'fabricamos_native_panel_session_token' );
		$this->clear_auth_cookie( self::COOKIE_PANEL );
	}

	protected function get_manufacturer_login_email( $manufacturer_id ) {
		return sanitize_email( (string) get_post_meta( $manufacturer_id, 'fab_login_email', true ) );
	}

	protected function manufacturer_has_login_password( $manufacturer_id ) {
		return '' !== (string) get_post_meta( $manufacturer_id, 'fab_login_password_hash', true );
	}

	protected function get_manufacturer_login_plain_password( $manufacturer_id ) {
		return trim( (string) get_post_meta( $manufacturer_id, 'fab_login_password_plain', true ) );
	}

	protected function save_manufacturer_login_credentials( $manufacturer_id, $email, $password ) {
		$email = sanitize_email( (string) $email );
		$manufacturer_id = absint( $manufacturer_id );

		if ( ! $manufacturer_id ) {
			return true;
		}

		update_post_meta( $manufacturer_id, 'fab_login_email', $email );

		if ( '' !== (string) $password ) {
			update_post_meta( $manufacturer_id, 'fab_login_password_hash', wp_hash_password( (string) $password ) );
			update_post_meta( $manufacturer_id, 'fab_login_password_plain', (string) $password );
		}

		return true;
	}

	protected function get_manufacturer_by_login( $login ) {
		$login = trim( (string) $login );
		if ( '' === $login ) {
			return null;
		}

		$posts = get_posts(
			array(
				'post_type'      => 'fabricante',
				'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
				'posts_per_page' => 1,
				'meta_key'       => 'fab_login_email',
				'meta_value'     => sanitize_email( $login ),
				'orderby'        => 'ID',
				'order'          => 'ASC',
			)
		);

		return empty( $posts ) ? null : $posts[0];
	}

	public function handle_legacy_routes() {
		if ( is_page( 'detalhes-do-fabricante' ) ) {
			wp_safe_redirect( $this->public_catalog_url(), 301 );
			exit;
		}

		if ( is_page( 'fabricamos-login' ) ) {
			wp_safe_redirect( $this->login_url(), 301 );
			exit;
		}

		if ( is_page( array( 'log-in', 'entrar' ) ) ) {
			wp_safe_redirect( $this->site_login_url(), 301 );
			exit;
		}

		if ( is_page( 'fabricante' ) && $this->is_manufacturer_authenticated() ) {
			wp_safe_redirect( home_url( '/meu-fabricante/' ) );
			exit;
		}

		if ( is_page( 'fabricamos-fabricante' ) && ! $this->is_manufacturer_authenticated() ) {
			wp_safe_redirect( $this->login_url() );
			exit;
		}

		if ( is_page( 'meu-fabricante' ) && ! $this->is_manufacturer_authenticated() ) {
			wp_safe_redirect( $this->login_url() );
			exit;
		}

		if ( is_page( 'painel' ) && $this->is_panel_authenticated() && 'form' !== ( isset( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : '' ) ) {
			return;
		}

		if ( is_page( 'painel' ) && ! $this->is_panel_authenticated() && ! empty( $_GET['redirect_to'] ) ) {
			wp_safe_redirect( $this->panel_login_url() );
			exit;
		}

		$this->enforce_public_catalog_auth();
	}

	public function template_include( $template ) {
		if ( is_singular( 'fabricante' ) ) {
			return $this->template_path( 'single-fabricante.php' );
		}

		if ( is_front_page() && $this->is_fabricamos_front_page() ) {
			return $this->template_path( 'page-catalog-buyer.php' );
		}

		if ( ! is_page() ) {
			return $template;
		}

		foreach ( $this->page_map as $slug => $template_name ) {
			if ( is_page( $slug ) ) {
				return $this->template_path( 'page-' . $template_name . '.php' );
			}
		}

		return $template;
	}

	protected function is_fabricamos_front_page() {
		$page = get_page_by_path( 'fabricamos', OBJECT, 'page' );
		if ( ! $page ) {
			return false;
		}

		return (int) get_option( 'page_on_front' ) === (int) $page->ID;
	}

	protected function ensure_default_panel_credentials() {
		if ( ! get_option( self::OPTION_PANEL_LOGIN_EMAIL ) ) {
			update_option( self::OPTION_PANEL_LOGIN_EMAIL, 'abiquifi@gmail.com', false );
		}

		if ( ! get_option( self::OPTION_PANEL_LOGIN_HASH ) ) {
			update_option( self::OPTION_PANEL_LOGIN_HASH, wp_hash_password( '12345' ), false );
		}
	}

	protected function template_path( $file ) {
		return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . $file;
	}

	public function body_class( $classes ) {
		if ( $this->is_fabricamos_request() ) {
			$classes[] = 'fabricamos-native';
		}

		return $classes;
	}

	public function enqueue_assets() {
		if ( ! $this->is_fabricamos_request() ) {
			return;
		}

		$plugin_root = plugin_dir_path( dirname( __FILE__ ) );
		$css_file    = $plugin_root . 'assets/css/fabricamos-native.css';
		$js_file     = $plugin_root . 'assets/js/fabricamos-native.js';
		$css_version = file_exists( $css_file ) ? (string) filemtime( $css_file ) : '1.0.0';
		$js_version  = file_exists( $js_file ) ? (string) filemtime( $js_file ) : '1.0.0';

		wp_enqueue_style(
			'fabricamos-native-fonts',
			'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
			array(),
			null
		);

		wp_enqueue_style(
			'fabricamos-native',
			plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/fabricamos-native.css',
			array( 'fabricamos-native-fonts' ),
			$css_version
		);

		wp_enqueue_script(
			'fabricamos-native',
			plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/fabricamos-native.js',
			array(),
			$js_version,
			true
		);

		wp_localize_script(
			'fabricamos-native',
			'FabricamosNative',
			array(
				'ajaxUrl'          => esc_url_raw( add_query_arg( 'action', 'fabricamos_search_substances', admin_url( 'admin-ajax.php' ) ) ),
				'manufacturersUrl' => esc_url_raw( add_query_arg( 'action', 'fabricamos_search_manufacturers', admin_url( 'admin-ajax.php' ) ) ),
				'nonce'            => wp_create_nonce( 'wp_rest' ),
				'catalogUrl'       => $this->public_catalog_url(),
				'currentUser'      => $this->get_public_site_user_name(),
				'announceState'    => isset( $_GET[ self::QUERY_SUCCESS ] ) ? sanitize_text_field( wp_unslash( $_GET[ self::QUERY_SUCCESS ] ) ) : '',
			)
		);
	}

	protected function is_fabricamos_request() {
		if ( is_singular( 'fabricante' ) ) {
			return true;
		}

		if ( is_page() ) {
			foreach ( array_keys( $this->page_map ) as $slug ) {
				if ( is_page( $slug ) ) {
					return true;
				}
			}
		}

		return is_front_page() && $this->is_fabricamos_front_page();
	}

	public function register_settings_page() {
		add_options_page(
			'Fabricamos',
			'Fabricamos',
			'manage_options',
			'fabricamos-native',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting(
			'fabricamos_native',
			self::OPTION_MAIL_TO,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_email',
				'default'           => '',
			)
		);

		register_setting(
			'fabricamos_native',
			self::OPTION_ANNOUNCE_MAIL_TO,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_email',
				'default'           => '',
			)
		);
	}

	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1>Fabricamos</h1>
			<form action="options.php" method="post">
				<?php settings_fields( 'fabricamos_native' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="fabricamos-native-mail-to">E-mail padrao de destino</label></th>
						<td>
							<input id="fabricamos-native-mail-to" name="<?php echo esc_attr( self::OPTION_MAIL_TO ); ?>" type="email" class="regular-text" value="<?php echo esc_attr( get_option( self::OPTION_MAIL_TO, '' ) ); ?>" />
							<p class="description">Se vazio, usa <code>marketing@abiquifi.org.br</code>.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="fabricamos-native-announce-mail-to">E-mail do formulario "Quero anunciar meu fabricante"</label></th>
						<td>
							<input id="fabricamos-native-announce-mail-to" name="<?php echo esc_attr( self::OPTION_ANNOUNCE_MAIL_TO ); ?>" type="email" class="regular-text" value="<?php echo esc_attr( get_option( self::OPTION_ANNOUNCE_MAIL_TO, '' ) ); ?>" />
							<p class="description">Se vazio, usa <code>marketing@abiquifi.org.br</code>.</p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public function register_acf_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_fabricamos_native',
				'title'    => 'Fabricamos',
				'fields'   => array(
					array(
						'key'           => 'field_fab_logo',
						'label'         => 'Logo / Card',
						'name'          => 'fab_logo',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'           => 'field_fab_hero_image',
						'label'         => 'Imagem Hero',
						'name'          => 'fab_hero_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'large',
					),
					array(
						'key'   => 'field_fab_description',
						'label' => 'Descricao',
						'name'  => 'fab_description',
						'type'  => 'textarea',
					),
					array(
						'key'   => 'field_fab_contact_name',
						'label' => 'Nome / Departamento',
						'name'  => 'fab_contact_name',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_fab_phone',
						'label' => 'Telefone',
						'name'  => 'fab_phone',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_fab_email',
						'label' => 'E-mail',
						'name'  => 'fab_email',
						'type'  => 'email',
					),
					array(
						'key'   => 'field_fab_site',
						'label' => 'Site',
						'name'  => 'fab_site',
						'type'  => 'url',
					),
					array(
						'key'          => 'field_fab_substances',
						'label'        => 'Substancias',
						'name'         => 'fab_substances',
						'type'         => 'relationship',
						'post_type'    => array( 'post' ),
						'post_status'  => array( 'draft', 'publish' ),
						'filters'      => array( 'search' ),
						'return_format'=> 'id',
						'elements'     => array( 'featured_image' ),
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'fabricante',
						),
					),
				),
			)
		);
	}

	public function filter_substance_relationship_query( $args ) {
		$args['post_status'] = array( 'draft', 'publish' );
		$args['orderby']     = 'title';
		$args['order']       = 'ASC';
		return $args;
	}

	public function register_rest_routes() {
		register_rest_route(
			'fabricamos/v1',
			'/substances',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_search_substances' ),
				'permission_callback' => array( $this, 'rest_catalog_permission' ),
				'args'                => array(
					'search' => array(
						'type'     => 'string',
						'required' => false,
					),
				),
			)
		);

		register_rest_route(
			'fabricamos/v1',
			'/manufacturers',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_search_manufacturers' ),
				'permission_callback' => array( $this, 'rest_catalog_permission' ),
				'args'                => array(
					'search' => array(
						'type'     => 'string',
						'required' => false,
					),
				),
			)
		);
	}

	public function rest_search_substances( WP_REST_Request $request ) {
		$search = sanitize_text_field( (string) $request->get_param( 'search' ) );
		$posts  = $this->search_substances( $search, 8 );
		$data   = array();

		foreach ( $posts as $post ) {
			$parsed = isset( $post['meta'] ) ? $post['meta'] : $this->parse_dsf_post( $post );
			$data[] = array(
				'id'    => isset( $post['id'] ) ? (string) $post['id'] : (int) $post->ID,
				'title' => isset( $post['title'] ) ? $post['title'] : $post->post_title,
				'meta'  => $parsed,
				'payload' => array(
					'display_name' => isset( $post['title'] ) ? $post['title'] : $post->post_title,
					'insumo'       => isset( $parsed['insumo'] ) ? $parsed['insumo'] : '',
					'dcb'          => isset( $parsed['dcb'] ) ? $parsed['dcb'] : '',
					'inn'          => isset( $parsed['inn'] ) ? $parsed['inn'] : '',
					'cas'          => isset( $parsed['cas'] ) ? $parsed['cas'] : '',
					'ncm'          => isset( $parsed['ncm'] ) ? $parsed['ncm'] : '',
					'cbpf'         => isset( $parsed['cbpf'] ) ? $parsed['cbpf'] : '',
					'validade'     => isset( $parsed['validade'] ) ? $parsed['validade'] : '',
				),
			);
		}

		return rest_ensure_response( $data );
	}

	public function rest_search_manufacturers( WP_REST_Request $request ) {
		$search = sanitize_text_field( (string) $request->get_param( 'search' ) );
		$posts  = $this->search_manufacturers( $search, 8 );
		$data   = array();

		foreach ( $posts as $post ) {
			$data[] = array(
				'id'     => (int) $post->ID,
				'title'  => get_the_title( $post ),
				'url'    => get_permalink( $post ),
				'sector' => $this->get_manufacturer_sector_name( $post->ID ),
			);
		}

		return rest_ensure_response( $data );
	}

	protected function can_use_autocomplete_search() {
		if ( $this->has_catalog_access() ) {
			return true;
		}

		if ( $this->is_panel_authenticated() ) {
			return true;
		}

		if ( $this->is_manufacturer_authenticated() ) {
			return true;
		}

		return current_user_can( 'manage_options' );
	}

	public function ajax_search_substances() {
		if ( ! $this->can_use_autocomplete_search() ) {
			wp_send_json( array() );
		}

		$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
		$posts  = $this->search_substances( $search, 8 );
		$data   = array();

		foreach ( $posts as $post ) {
			$parsed = isset( $post['meta'] ) ? $post['meta'] : $this->parse_dsf_post( $post );
			$data[] = array(
				'id'    => isset( $post['id'] ) ? (string) $post['id'] : (int) $post->ID,
				'title' => isset( $post['title'] ) ? $post['title'] : $post->post_title,
				'meta'  => $parsed,
				'payload' => array(
					'display_name' => isset( $post['title'] ) ? $post['title'] : $post->post_title,
					'insumo'       => isset( $parsed['insumo'] ) ? $parsed['insumo'] : '',
					'dcb'          => isset( $parsed['dcb'] ) ? $parsed['dcb'] : '',
					'inn'          => isset( $parsed['inn'] ) ? $parsed['inn'] : '',
					'cas'          => isset( $parsed['cas'] ) ? $parsed['cas'] : '',
					'ncm'          => isset( $parsed['ncm'] ) ? $parsed['ncm'] : '',
					'cbpf'         => isset( $parsed['cbpf'] ) ? $parsed['cbpf'] : '',
					'validade'     => isset( $parsed['validade'] ) ? $parsed['validade'] : '',
				),
			);
		}

		wp_send_json( $data );
	}

	public function ajax_search_manufacturers() {
		if ( ! $this->can_use_autocomplete_search() ) {
			wp_send_json( array() );
		}

		$search = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
		$posts  = $this->search_manufacturers( $search, 8 );
		$data   = array();

		foreach ( $posts as $post ) {
			$data[] = array(
				'id'     => (int) $post->ID,
				'title'  => get_the_title( $post ),
				'url'    => get_permalink( $post ),
				'sector' => $this->get_manufacturer_sector_name( $post->ID ),
			);
		}

		wp_send_json( $data );
	}

	public function handle_login() {
		$redirect = $this->login_url();

		if ( ! isset( $_POST['fabricamos_login_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fabricamos_login_nonce'] ) ), 'fabricamos_login' ) ) {
			wp_safe_redirect( add_query_arg( 'login_error', 'nonce', $redirect ) );
			exit;
		}

		$login    = isset( $_POST['fabricamos_login_user'] ) ? sanitize_text_field( wp_unslash( $_POST['fabricamos_login_user'] ) ) : ( isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '' );
		$password = isset( $_POST['fabricamos_login_pass'] ) ? (string) wp_unslash( $_POST['fabricamos_login_pass'] ) : ( isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '' );
		$remember = ! empty( $_POST['rememberme'] );
		$post     = $this->get_manufacturer_by_login( $login );

		if ( ! $post instanceof WP_Post ) {
			wp_safe_redirect( add_query_arg( 'login_error', 'invalid', $redirect ) );
			exit;
		}

		$hash = (string) get_post_meta( $post->ID, 'fab_login_password_hash', true );
		if ( '' === $hash || ! wp_check_password( $password, $hash ) ) {
			wp_safe_redirect( add_query_arg( 'login_error', 'invalid', $redirect ) );
			exit;
		}

		$this->set_manufacturer_auth( $post->ID, $remember );
		wp_safe_redirect( home_url( '/meu-fabricante/' ) );
		exit;
	}

	public function handle_site_login() {
		$redirect = $this->site_login_url();

		if ( ! isset( $_POST['fabricamos_site_login_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fabricamos_site_login_nonce'] ) ), 'fabricamos_site_login' ) ) {
			wp_safe_redirect( add_query_arg( 'login_error', 'nonce', $redirect ) );
			exit;
		}

		$login       = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
		$password    = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '';
		$remember    = ! empty( $_POST['rememberme'] );
		$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : $this->public_catalog_url();
		if ( '' === $redirect_to ) {
			$redirect_to = $this->public_catalog_url();
		}

		$sso = $this->public_sso();
		if ( $sso && method_exists( $sso, 'remote_login' ) && method_exists( $sso, 'set_public_session_from_response' ) ) {
			$result = $sso->remote_login( $login, $password, $remember );
			if ( is_wp_error( $result ) || ! $sso->set_public_session_from_response( $result ) ) {
				wp_safe_redirect( add_query_arg( 'login_error', 'invalid', $redirect ) );
				exit;
			}

			wp_safe_redirect( $redirect_to );
			exit;
		}

		if ( is_email( $login ) ) {
			$user_by_email = get_user_by( 'email', $login );
			if ( $user_by_email instanceof WP_User ) {
				$login = (string) $user_by_email->user_login;
			}
		}

		$user = wp_authenticate( $login, $password );
		if ( is_wp_error( $user ) || ! $this->is_public_site_user( $user ) ) {
			wp_safe_redirect( add_query_arg( 'login_error', 'invalid', $redirect ) );
			exit;
		}

		$this->establish_public_site_session( $user, $remember );

		wp_safe_redirect( $redirect_to );
		exit;
	}

	public function handle_guest_access() {
		$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : $this->public_catalog_url();
		if ( '' === $redirect_to ) {
			$redirect_to = $this->public_catalog_url();
		}

		if ( ! isset( $_POST['fabricamos_guest_access_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fabricamos_guest_access_nonce'] ) ), 'fabricamos_guest_access' ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_GUEST_ERROR, 'nonce', $redirect_to ) );
			exit;
		}

		$name      = sanitize_text_field( isset( $_POST['fab_guest_name'] ) ? wp_unslash( $_POST['fab_guest_name'] ) : '' );
		$phone     = sanitize_text_field( isset( $_POST['fab_guest_phone'] ) ? wp_unslash( $_POST['fab_guest_phone'] ) : '' );
		$email     = sanitize_email( isset( $_POST['fab_guest_email'] ) ? wp_unslash( $_POST['fab_guest_email'] ) : '' );
		$company   = sanitize_text_field( isset( $_POST['fab_guest_company'] ) ? wp_unslash( $_POST['fab_guest_company'] ) : '' );
		$job_title = sanitize_text_field( isset( $_POST['fab_guest_job_title'] ) ? wp_unslash( $_POST['fab_guest_job_title'] ) : '' );

		if ( '' === $name || '' === $phone || '' === $email || '' === $company || '' === $job_title ) {
			wp_safe_redirect( add_query_arg( self::QUERY_GUEST_ERROR, 'required', $redirect_to ) );
			exit;
		}

		if ( ! is_email( $email ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_GUEST_ERROR, 'email', $redirect_to ) );
			exit;
		}

		if ( ! $this->is_valid_phone( $phone ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_GUEST_ERROR, 'phone', $redirect_to ) );
			exit;
		}

		if ( ! in_array( $job_title, $this->get_guest_job_title_options(), true ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_GUEST_ERROR, 'job_title', $redirect_to ) );
			exit;
		}

		global $wpdb;

		$token      = wp_generate_password( 48, false, false ) . wp_generate_password( 16, false, false );
		$expires_at = time() + $this->guest_access_ttl();
		$now        = current_time( 'mysql', true );
		$inserted   = $wpdb->insert(
			$this->guest_access_table_name(),
			array(
				'session_id' => wp_hash( $token ),
				'full_name'  => $name,
				'phone'      => $phone,
				'email'      => $email,
				'company'    => $company,
				'job_title'  => $job_title,
				'expires_at' => gmdate( 'Y-m-d H:i:s', $expires_at ),
				'created_at' => $now,
				'updated_at' => $now,
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		if ( false === $inserted ) {
			wp_safe_redirect( add_query_arg( self::QUERY_GUEST_ERROR, 'save', $redirect_to ) );
			exit;
		}

		$this->set_guest_cookie( $token, $expires_at );
		$this->set_shared_guest_cookie( $expires_at );

		wp_safe_redirect( remove_query_arg( self::QUERY_GUEST_ERROR, $redirect_to ) );
		exit;
	}

	public function handle_site_register() {
		$redirect = $this->site_register_url();

		if ( ! isset( $_POST['fabricamos_site_register_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fabricamos_site_register_nonce'] ) ), 'fabricamos_site_register' ) ) {
			wp_safe_redirect( add_query_arg( 'register_error', 'nonce', $redirect ) );
			exit;
		}

		$first_name     = isset( $_POST['register_name'] ) ? sanitize_text_field( wp_unslash( $_POST['register_name'] ) ) : '';
		$last_name      = isset( $_POST['register_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['register_last_name'] ) ) : '';
		$email          = isset( $_POST['register_email'] ) ? sanitize_email( wp_unslash( $_POST['register_email'] ) ) : '';
		$password       = isset( $_POST['register_password'] ) ? (string) wp_unslash( $_POST['register_password'] ) : '';
		$institution    = isset( $_POST['register_institution'] ) ? sanitize_text_field( wp_unslash( $_POST['register_institution'] ) ) : '';
		$phone          = isset( $_POST['register_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['register_phone'] ) ) : '';
		$activity       = isset( $_POST['register_activity_sector'] ) ? sanitize_text_field( wp_unslash( $_POST['register_activity_sector'] ) ) : '';
		$department     = isset( $_POST['register_department'] ) ? sanitize_text_field( wp_unslash( $_POST['register_department'] ) ) : '';
		$job_title      = isset( $_POST['register_job_title'] ) ? sanitize_text_field( wp_unslash( $_POST['register_job_title'] ) ) : '';
		$privacy_accept = ! empty( $_POST['register_privacy_accept'] );
		$normalized_choices = $this->normalize_public_registration_choices( $activity, $department, $job_title );
		$activity           = $normalized_choices['activity'];
		$department         = $normalized_choices['department'];
		$job_title          = $normalized_choices['job_title'];
		$redirect_to    = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : $this->public_catalog_url();
		if ( '' === $redirect_to ) {
			$redirect_to = $this->public_catalog_url();
		}
		$profile_meta = $this->build_public_registration_profile_meta( $institution, $phone, $activity, $department, $job_title );

		if ( '' === $first_name || '' === $last_name || '' === $email || '' === $password || '' === $institution || '' === $phone || '' === $activity || '' === $department || '' === $job_title ) {
			$this->log_registration_debug(
				'Cadastro Fabricamos rejeitado por campos obrigatorios ausentes.',
				array(
					'first_name'  => '' !== $first_name,
					'last_name'   => '' !== $last_name,
					'email'       => '' !== $email,
					'password'    => '' !== $password,
					'institution' => '' !== $institution,
					'phone'       => '' !== $phone,
					'activity'    => '' !== $activity,
					'department'  => '' !== $department,
					'job_title'   => '' !== $job_title,
				)
			);
			wp_safe_redirect( add_query_arg( 'register_error', 'required', $redirect ) );
			exit;
		}

		if ( ! is_email( $email ) ) {
			wp_safe_redirect( add_query_arg( 'register_error', 'email', $redirect ) );
			exit;
		}

		if ( strlen( $password ) < 8 ) {
			wp_safe_redirect( add_query_arg( 'register_error', 'password_length', $redirect ) );
			exit;
		}

		if ( ! $this->is_valid_phone( $phone ) ) {
			wp_safe_redirect( add_query_arg( 'register_error', 'phone', $redirect ) );
			exit;
		}

		if ( ! in_array( $activity, $this->get_dictionary_activity_options(), true ) || ! in_array( $department, $this->get_dictionary_department_options(), true ) || ! in_array( $job_title, $this->get_dictionary_job_title_options(), true ) ) {
			$this->log_registration_debug(
				'Cadastro Fabricamos rejeitado por opcoes invalidas.',
				array(
					'activity'   => $activity,
					'department' => $department,
					'job_title'  => $job_title,
				)
			);
			wp_safe_redirect( add_query_arg( 'register_error', 'choice', $redirect ) );
			exit;
		}

		if ( ! $privacy_accept ) {
			wp_safe_redirect( add_query_arg( 'register_error', 'privacy', $redirect ) );
			exit;
		}

		$sso = $this->public_sso();
		if ( $sso && method_exists( $sso, 'remote_register' ) && method_exists( $sso, 'set_public_session_from_response' ) ) {
			$full_remote_profile = array(
				'first_name'       => $first_name,
				'last_name'        => $last_name,
				'institution_name' => $institution,
				'phone'            => $phone,
				'activity_sector'  => $activity,
				'department'       => $department,
				'job_title'        => $job_title,
				'privacy_accept'   => $privacy_accept ? '1' : '0',
			);
			$minimal_remote_profile = array(
				'phone'     => $phone,
				'job_title' => $job_title,
			);

			$result = $sso->remote_register(
				trim( $first_name . ' ' . $last_name ),
				$email,
				$password,
				$password,
				$full_remote_profile
			);

			$remote_error = $this->normalize_remote_registration_error( $result );
			if ( $remote_error instanceof WP_Error && 'abiquifi_sso_invalid_choice' === $remote_error->get_error_code() ) {
				$this->log_registration_debug(
					'Cadastro Fabricamos recebeu invalid_choice do SSO remoto e sera reenviado com payload minimo.',
					array(
						'email'       => $email,
						'activity'    => $activity,
						'department'  => $department,
						'job_title'   => $job_title,
						'institution' => $institution,
					)
				);

				$result       = $sso->remote_register(
					trim( $first_name . ' ' . $last_name ),
					$email,
					$password,
					$password,
					$minimal_remote_profile
				);
				$remote_error = $this->normalize_remote_registration_error( $result );
			}

			if ( $remote_error instanceof WP_Error ) {
				$this->log_registration_debug(
					'Cadastro Fabricamos rejeitado pelo SSO remoto.',
					array(
						'code'    => $remote_error->get_error_code(),
						'message' => $remote_error->get_error_message(),
					)
				);
				$register_error = $this->map_public_register_error_code( $remote_error );

				if ( 'exists' === $register_error ) {
					$login_result = $sso->remote_login( $email, $password, true );
					if ( ! is_wp_error( $login_result ) && $sso->set_public_session_from_response( $login_result ) ) {
						$this->persist_public_registration_profile( $email, $profile_meta );
						wp_safe_redirect( $redirect_to );
						exit;
					}

					$this->redirect_existing_public_account_to_login( $email, $redirect_to );
				}

				wp_safe_redirect( add_query_arg( 'register_error', $register_error, $redirect ) );
				exit;
			}

			if ( ! $sso->set_public_session_from_response( $result ) ) {
				wp_safe_redirect( add_query_arg( 'register_error', 'error', $redirect ) );
				exit;
			}

			$this->persist_public_registration_profile( $email, $profile_meta );

			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'registered', $redirect_to ) );
			exit;
		}

		$existing_user = get_user_by( 'email', $email );
		if ( $existing_user instanceof WP_User ) {
			if ( $this->is_public_site_user( $existing_user ) ) {
				$authenticated_user = wp_authenticate( (string) $existing_user->user_login, $password );
				if ( $authenticated_user instanceof WP_User && $this->is_public_site_user( $authenticated_user ) ) {
					$this->sync_existing_public_user_profile( $authenticated_user, $first_name, $last_name, $profile_meta );
					$this->establish_public_site_session( $authenticated_user, true );
					wp_safe_redirect( $redirect_to );
					exit;
				}

				$this->redirect_existing_public_account_to_login( $email, $redirect_to );
			}

			wp_safe_redirect( add_query_arg( 'register_error', 'exists', $redirect ) );
			exit;
		}

		$username = $this->generate_username_from_email( $email );
		$user_id  = wp_insert_user(
			array(
				'user_login'   => $username,
				'user_pass'    => $password,
				'user_email'   => $email,
				'display_name' => trim( $first_name . ' ' . $last_name ),
				'first_name'   => $first_name,
				'last_name'    => $last_name,
				'role'         => 'subscriber',
			)
		);

		if ( is_wp_error( $user_id ) ) {
			if ( in_array( (string) $user_id->get_error_code(), array( 'existing_user_email', 'existing_user_login' ), true ) ) {
				$this->redirect_existing_public_account_to_login( $email, $redirect_to );
			}

			wp_safe_redirect( add_query_arg( 'register_error', 'error', $redirect ) );
			exit;
		}

		foreach ( $profile_meta as $meta_key => $meta_value ) {
			update_user_meta( $user_id, $meta_key, $meta_value );
		}

		$user = get_user_by( 'id', $user_id );
		if ( ! $user instanceof WP_User ) {
			wp_safe_redirect( add_query_arg( 'register_error', 'error', $redirect ) );
			exit;
		}

		$this->send_registration_confirmation_email( $user );
		$this->establish_public_site_session( $user, true );

		wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'registered', $redirect_to ) );
		exit;
	}

	protected function establish_public_site_session( $user, $remember = false ) {
		if ( ! $user instanceof WP_User ) {
			return;
		}

		$this->public_auth_cookie_expiration = DAY_IN_SECONDS;
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, true, is_ssl() );
		do_action( 'wp_login', $user->user_login, $user );
		$this->public_auth_cookie_expiration = 0;
	}

	protected function redirect_existing_public_account_to_login( $email, $redirect_to = '' ) {
		$args = array(
			'login_notice' => 'existing_account',
		);

		$email = sanitize_email( (string) $email );
		if ( '' !== $email ) {
			$args['login_hint'] = $email;
		}

		wp_safe_redirect( add_query_arg( $args, $this->site_login_url( $redirect_to ) ) );
		exit;
	}

	protected function sync_existing_public_user_profile( WP_User $user, $first_name, $last_name, $meta ) {
		$display_name = trim( $first_name . ' ' . $last_name );
		$payload      = array(
			'ID'         => (int) $user->ID,
			'first_name' => $first_name,
			'last_name'  => $last_name,
		);

		if ( '' !== $display_name ) {
			$payload['display_name'] = $display_name;
		}

		wp_update_user( $payload );

		foreach ( (array) $meta as $meta_key => $meta_value ) {
			if ( '' === (string) $meta_key || null === $meta_value || '' === $meta_value ) {
				continue;
			}

			update_user_meta( (int) $user->ID, (string) $meta_key, $meta_value );
		}
	}

	protected function normalize_remote_registration_error( $result ) {
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( ! is_array( $result ) ) {
			return new WP_Error( 'invalid_remote_response', 'Resposta invalida do cadastro remoto.', array( 'status' => 502 ) );
		}

		if ( ! empty( $result['_transport_error'] ) ) {
			return new WP_Error( 'remote_transport_error', (string) $result['_transport_error'], array( 'status' => 502 ) );
		}

		$status = isset( $result['_http_status'] ) ? (int) $result['_http_status'] : 200;
		if ( $status >= 400 || ! empty( $result['code'] ) ) {
			$code    = ! empty( $result['code'] ) ? sanitize_key( (string) $result['code'] ) : 'remote_registration_error';
			$message = ! empty( $result['message'] ) ? (string) $result['message'] : 'Nao foi possivel concluir o cadastro.';
			$data    = isset( $result['data'] ) && is_array( $result['data'] ) ? $result['data'] : array();
			if ( $status > 0 && empty( $data['status'] ) ) {
				$data['status'] = $status;
			}

			return new WP_Error( $code, $message, $data );
		}

		if ( empty( $result['token'] ) || empty( $result['user'] ) ) {
			return new WP_Error( 'invalid_remote_response', 'Resposta incompleta do cadastro remoto.', array( 'status' => 502 ) );
		}

		return null;
	}

	protected function build_registration_confirmation_headers() {
		return array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $this->get_mail_from_name() . ' <' . $this->get_announce_sender_email() . '>',
		);
	}

	protected function send_registration_confirmation_email( $user ) {
		if ( ! $user instanceof WP_User || ! $user->exists() ) {
			return false;
		}

		$to = sanitize_email( (string) $user->user_email );
		if ( '' === $to || ! is_email( $to ) ) {
			return false;
		}

		$name     = trim( (string) $user->display_name );
		$name     = '' === $name ? (string) $user->user_login : $name;
		$login_url = $this->site_login_url( home_url( '/catalogo/' ) );
		$subject  = 'Cadastro confirmado | Fabricamos';
		$message  = sprintf(
			'<html><body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#162b40;">' .
			'<div style="max-width:640px;margin:0 auto;padding:32px 20px;">' .
			'<div style="background:#ffffff;border-radius:16px;padding:32px;border:1px solid #d9e2ec;">' .
			'<p style="margin:0 0 16px;font-size:14px;letter-spacing:.08em;text-transform:uppercase;color:#6c8195;">Fabricamos</p>' .
			'<h1 style="margin:0 0 20px;font-size:28px;line-height:1.2;color:#0d2236;">Cadastro confirmado</h1>' .
			'<p style="margin:0 0 16px;font-size:16px;line-height:1.6;">Ola %1$s, seu cadastro no Fabricamos foi concluido com sucesso.</p>' .
			'<p style="margin:0 0 16px;font-size:16px;line-height:1.6;">Seu acesso esta liberado para o e-mail <strong>%2$s</strong>.</p>' .
			'<p style="margin:0 0 24px;font-size:16px;line-height:1.6;">Se voce nao reconhece este cadastro, ignore esta mensagem.</p>' .
			'<p style="margin:0 0 24px;"><a href="%3$s" style="display:inline-block;background:#0d2236;color:#ffffff;text-decoration:none;padding:14px 20px;border-radius:999px;font-weight:700;">Entrar no Fabricamos</a></p>' .
			'<p style="margin:0;font-size:13px;line-height:1.6;color:#6c8195;">Mensagem automatica enviada por %4$s.</p>' .
			'</div></div></body></html>',
			esc_html( $name ),
			esc_html( $to ),
			esc_url( $login_url ),
			esc_html( $this->get_mail_from_name() )
		);

		return (bool) wp_mail( $to, $subject, $message, $this->build_registration_confirmation_headers() );
	}

	protected function build_announce_email_body( $fields ) {
		$labels = array(
			'nome'     => 'Nome',
			'empresa'  => 'Empresa',
			'cargo'    => 'Cargo',
			'telefone' => 'Telefone',
			'email'    => 'E-mail',
			'assunto'  => 'Assunto',
			'mensagem' => 'Mensagem',
		);

		$rows = '';
		foreach ( $labels as $key => $label ) {
			$value = isset( $fields[ $key ] ) ? (string) $fields[ $key ] : '';
			$rows .= sprintf(
				'<tr><td style="padding:10px 12px;border:1px solid #d9e2ec;background:#f8fbff;font-weight:700;width:180px;">%1$s</td><td style="padding:10px 12px;border:1px solid #d9e2ec;">%2$s</td></tr>',
				esc_html( $label ),
				nl2br( esc_html( $value ) )
			);
		}

		return '<html><body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#162b40;">'
			. '<div style="max-width:720px;margin:0 auto;padding:32px 20px;">'
			. '<div style="background:#ffffff;border-radius:16px;padding:32px;border:1px solid #d9e2ec;">'
			. '<p style="margin:0 0 16px;font-size:14px;letter-spacing:.08em;text-transform:uppercase;color:#6c8195;">Fabricamos</p>'
			. '<h1 style="margin:0 0 20px;font-size:28px;line-height:1.2;color:#0d2236;">Quero anunciar meu fabricante</h1>'
			. '<p style="margin:0 0 24px;font-size:16px;line-height:1.6;">Uma nova solicitacao foi enviada pelo formulario publico do Fabricamos.</p>'
			. '<table style="width:100%;border-collapse:collapse;font-size:15px;line-height:1.5;">' . $rows . '</table>'
			. '</div></div></body></html>';
	}

	protected function log_registration_debug( $message, $context = array() ) {
		if ( function_exists( 'abiquifi_mailer_log' ) ) {
			abiquifi_mailer_log( (string) $message, is_array( $context ) ? $context : array() );
		}
	}

	protected function persist_public_registration_profile( $email, $meta ) {
		$user = get_user_by( 'email', sanitize_email( (string) $email ) );
		if ( ! $user instanceof WP_User ) {
			return;
		}

		foreach ( (array) $meta as $meta_key => $meta_value ) {
			if ( '' === (string) $meta_key || null === $meta_value || '' === $meta_value ) {
				continue;
			}

			update_user_meta( (int) $user->ID, (string) $meta_key, $meta_value );
		}
	}

	protected function map_public_register_error_code( WP_Error $error ) {
		$code = (string) $error->get_error_code();

		if ( in_array( $code, array( 'abiquifi_sso_required', 'required' ), true ) ) {
			return 'required';
		}

		if ( in_array( $code, array( 'abiquifi_sso_invalid_email', 'email' ), true ) ) {
			return 'email';
		}

		if ( in_array( $code, array( 'abiquifi_sso_password_short', 'password_length' ), true ) ) {
			return 'password_length';
		}

		if ( in_array( $code, array( 'abiquifi_sso_invalid_phone', 'phone' ), true ) ) {
			return 'phone';
		}

		if ( in_array( $code, array( 'abiquifi_sso_invalid_choice', 'choice' ), true ) ) {
			return 'choice';
		}

		if ( in_array( $code, array( 'abiquifi_sso_privacy_required', 'privacy' ), true ) ) {
			return 'privacy';
		}

		if ( in_array( $code, array( 'abiquifi_sso_email_exists', 'exists' ), true ) ) {
			return 'exists';
		}

		if ( in_array( $code, array( 'existing_user_login', 'existing_user_email' ), true ) ) {
			return 'exists';
		}

		return 'error';
	}

	public function handle_panel_login() {
		$redirect = $this->panel_login_url();

		if ( ! isset( $_POST['fabricamos_panel_login_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fabricamos_panel_login_nonce'] ) ), 'fabricamos_panel_login' ) ) {
			wp_safe_redirect( add_query_arg( 'login_error', 'nonce', $redirect ) );
			exit;
		}

		$login    = sanitize_email( isset( $_POST['fabricamos_panel_user'] ) ? wp_unslash( $_POST['fabricamos_panel_user'] ) : ( isset( $_POST['log'] ) ? wp_unslash( $_POST['log'] ) : '' ) );
		$password = isset( $_POST['fabricamos_panel_pass'] ) ? (string) wp_unslash( $_POST['fabricamos_panel_pass'] ) : ( isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '' );
		$remember = ! empty( $_POST['rememberme'] );
		$email    = sanitize_email( (string) get_option( self::OPTION_PANEL_LOGIN_EMAIL, '' ) );
		$hash     = (string) get_option( self::OPTION_PANEL_LOGIN_HASH, '' );

		if ( '' === $email || 0 !== strcasecmp( $email, $login ) || '' === $hash || ! wp_check_password( $password, $hash ) ) {
			wp_safe_redirect( add_query_arg( 'login_error', 'invalid', $redirect ) );
			exit;
		}

		$this->set_panel_auth( $remember );
		wp_safe_redirect( $this->panel_login_url() );
		exit;
	}

	public function handle_announce() {
		$redirect = home_url( '/anunciar-fabricante/' );

		if ( ! isset( $_POST['fabricamos_announce_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fabricamos_announce_nonce'] ) ), 'fabricamos_announce' ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', $redirect ) );
			exit;
		}

		$defaults = $this->get_announce_form_defaults();
		$fields   = array(
			'nome'     => isset( $_POST['an_nome'] ) ? sanitize_text_field( wp_unslash( $_POST['an_nome'] ) ) : $defaults['name'],
			'empresa'  => isset( $_POST['an_empresa'] ) ? sanitize_text_field( wp_unslash( $_POST['an_empresa'] ) ) : '',
			'cargo'    => isset( $_POST['an_cargo'] ) ? sanitize_text_field( wp_unslash( $_POST['an_cargo'] ) ) : '',
			'telefone' => isset( $_POST['an_telefone'] ) ? sanitize_text_field( wp_unslash( $_POST['an_telefone'] ) ) : '',
			'email'    => isset( $_POST['an_email'] ) ? sanitize_email( wp_unslash( $_POST['an_email'] ) ) : $defaults['email'],
			'assunto'  => isset( $_POST['an_assunto'] ) ? sanitize_text_field( wp_unslash( $_POST['an_assunto'] ) ) : 'Quero anunciar meu fabricante',
			'mensagem' => isset( $_POST['an_mensagem'] ) ? wp_strip_all_tags( wp_unslash( $_POST['an_mensagem'] ) ) : '',
		);

		foreach ( $fields as $value ) {
			if ( '' === trim( (string) $value ) ) {
				wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', $redirect ) );
				exit;
			}
		}

		if ( ! is_email( $fields['email'] ) || ! $this->is_valid_phone( $fields['telefone'] ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', $redirect ) );
			exit;
		}

		$recipient = $this->get_announce_recipient_email();
		if ( '' === $recipient || ! is_email( $recipient ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', $redirect ) );
			exit;
		}

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
		);
		$sender  = $this->get_announce_sender_email();
		$headers[] = 'From: ' . $this->get_mail_from_name() . ' <' . $sender . '>';
		if ( ! empty( $fields['email'] ) ) {
			$headers[] = 'Reply-To: ' . $fields['nome'] . ' <' . $fields['email'] . '>';
		}

		$sent = wp_mail( $recipient, $fields['assunto'], $this->build_announce_email_body( $fields ), $headers );
		if ( ! $sent ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', $redirect ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'sent', $redirect ) );
		exit;
	}

	public function handle_profile_update() {
		if ( ! $this->is_manufacturer_authenticated() ) {
			wp_safe_redirect( $this->login_url() );
			exit;
		}

		if ( ! isset( $_POST['fabricamos_profile_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fabricamos_profile_nonce'] ) ), 'fabricamos_profile' ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', home_url( '/meu-fabricante/' ) ) );
			exit;
		}

		$manufacturer = $this->get_current_user_manufacturer();
		if ( ! $manufacturer || ! $this->current_user_can_edit_manufacturer( $manufacturer->ID ) ) {
			wp_die( 'Sem permissao para editar este fabricante.' );
		}

		$description = isset( $_POST['fab_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['fab_description'] ) ) : '';
		$name        = isset( $_POST['fab_contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['fab_contact_name'] ) ) : '';
		$phone       = isset( $_POST['fab_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['fab_phone'] ) ) : '';
		$email       = isset( $_POST['fab_email'] ) ? sanitize_email( wp_unslash( $_POST['fab_email'] ) ) : '';
		$site        = isset( $_POST['fab_site'] ) ? esc_url_raw( wp_unslash( $_POST['fab_site'] ) ) : '';
		$substance_submission = $this->extract_substance_submission();

		if ( '' !== $phone && ! $this->is_valid_phone( $phone ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'invalid_phone', home_url( '/meu-fabricante/' ) ) );
			exit;
		}

		$this->update_manufacturer_field( $manufacturer->ID, 'field_fab_description', 'fab_description', $description );
		$this->update_manufacturer_field( $manufacturer->ID, 'field_fab_contact_name', 'fab_contact_name', $name );
		$this->update_manufacturer_field( $manufacturer->ID, 'field_fab_phone', 'fab_phone', $phone );
		$this->update_manufacturer_field( $manufacturer->ID, 'field_fab_email', 'fab_email', $email );
		$this->update_manufacturer_field( $manufacturer->ID, 'field_fab_site', 'fab_site', $site );
		$this->update_manufacturer_field( $manufacturer->ID, 'field_fab_substances', 'fab_substances', $substance_submission['matched_ids'] );
		update_post_meta( $manufacturer->ID, 'fab_compiled_substances', $substance_submission['compiled'] );
		update_post_meta( $manufacturer->ID, 'fab_catalog_items', $substance_submission['catalog_items'] );

		$hero_result = $this->handle_manufacturer_image_update(
			$manufacturer->ID,
			'field_fab_hero_image',
			'fab_hero_image',
			'fab_hero_image_file',
			'fab_hero_image_remove'
		);

		if ( is_wp_error( $hero_result ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', home_url( '/meu-fabricante/' ) ) );
			exit;
		}

		$this->sync_manufacturer_images( $manufacturer->ID );

		wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'saved', home_url( '/meu-fabricante/' ) ) );
		exit;
	}

	public function handle_panel_save() {
		if ( ! $this->is_panel_authenticated() ) {
			wp_safe_redirect( $this->panel_login_url() );
			exit;
		}

		if ( ! isset( $_POST['fabricamos_panel_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fabricamos_panel_nonce'] ) ), 'fabricamos_panel_save' ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', home_url( '/painel/' ) ) );
			exit;
		}

		$manufacturer_id = isset( $_POST['panel_manufacturer_id'] ) ? absint( $_POST['panel_manufacturer_id'] ) : 0;
		$title           = isset( $_POST['panel_title'] ) ? sanitize_text_field( wp_unslash( $_POST['panel_title'] ) ) : '';
		$associate       = isset( $_POST['fab_associate_status'] ) ? sanitize_text_field( wp_unslash( $_POST['fab_associate_status'] ) ) : null;
		$process         = isset( $_POST['fab_processo'] ) ? sanitize_text_field( wp_unslash( $_POST['fab_processo'] ) ) : null;
		$origin          = isset( $_POST['fab_origem'] ) ? sanitize_text_field( wp_unslash( $_POST['fab_origem'] ) ) : null;
		$description     = isset( $_POST['fab_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['fab_description'] ) ) : '';
		$editor_name     = isset( $_POST['fab_editor_name'] ) ? sanitize_text_field( wp_unslash( $_POST['fab_editor_name'] ) ) : '';
		$editor_phone    = isset( $_POST['fab_editor_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['fab_editor_phone'] ) ) : '';
		$editor_email    = isset( $_POST['fab_editor_email'] ) ? sanitize_email( wp_unslash( $_POST['fab_editor_email'] ) ) : '';
		$name            = isset( $_POST['fab_contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['fab_contact_name'] ) ) : '';
		$phone           = isset( $_POST['fab_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['fab_phone'] ) ) : '';
		$email           = isset( $_POST['fab_email'] ) ? sanitize_email( wp_unslash( $_POST['fab_email'] ) ) : '';
		$site            = isset( $_POST['fab_site'] ) ? esc_url_raw( wp_unslash( $_POST['fab_site'] ) ) : '';
		$substance_submission = $this->extract_substance_submission();
		$login_email     = isset( $_POST['panel_login_email'] ) ? sanitize_email( wp_unslash( $_POST['panel_login_email'] ) ) : '';
		$login_password  = isset( $_POST['panel_login_password'] ) ? (string) wp_unslash( $_POST['panel_login_password'] ) : '';

		if (
			'' === $title ||
			'' === $description ||
			'' === $editor_name ||
			'' === $editor_phone ||
			'' === $login_email ||
			( ! $manufacturer_id && '' === $login_password )
		) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'required_fields', $this->panel_form_url( $manufacturer_id ) ) );
			exit;
		}

		if ( empty( $substance_submission['catalog_items'] ) && empty( $substance_submission['matched_ids'] ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'missing_substances', $this->panel_form_url( $manufacturer_id ) ) );
			exit;
		}

		if (
			( '' !== $editor_phone && ! $this->is_valid_phone( $editor_phone ) ) ||
			( '' !== $phone && ! $this->is_valid_phone( $phone ) )
		) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'invalid_phone', $this->panel_form_url( $manufacturer_id ) ) );
			exit;
		}

		if ( ( '' !== $editor_email && ! is_email( $editor_email ) ) || ! is_email( $login_email ) || ( '' !== $email && ! is_email( $email ) ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'invalid_email', $this->panel_form_url( $manufacturer_id ) ) );
			exit;
		}

		if ( '' !== $login_email ) {
			$existing_manufacturer = $this->get_manufacturer_by_login( $login_email );
			if ( $existing_manufacturer instanceof WP_Post && (int) $existing_manufacturer->ID !== (int) $manufacturer_id ) {
				wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'panel_user_taken', $this->panel_form_url( $manufacturer_id ) ) );
				exit;
			}
		}

		$post_args = array(
			'post_type'   => 'fabricante',
			'post_status' => 'publish',
			'post_title'  => $title,
		);

		if ( $manufacturer_id ) {
			$post_args['ID'] = $manufacturer_id;
			$result          = wp_update_post( $post_args, true );
		} else {
			$result          = wp_insert_post( $post_args, true );
			$manufacturer_id = is_wp_error( $result ) ? 0 : (int) $result;
		}

		if ( is_wp_error( $result ) || ! $manufacturer_id ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', $this->panel_form_url( $manufacturer_id ) ) );
			exit;
		}

		$this->update_manufacturer_field( $manufacturer_id, 'field_fab_description', 'fab_description', $description );
		update_post_meta( $manufacturer_id, 'fab_responsavel_nome', $editor_name );
		update_post_meta( $manufacturer_id, 'fab_responsavel_telefone', $editor_phone );
		update_post_meta( $manufacturer_id, 'fab_responsavel_email', $editor_email );
		$this->update_manufacturer_field( $manufacturer_id, 'field_fab_contact_name', 'fab_contact_name', $name );
		$this->update_manufacturer_field( $manufacturer_id, 'field_fab_phone', 'fab_phone', $phone );
		$this->update_manufacturer_field( $manufacturer_id, 'field_fab_email', 'fab_email', $email );
		$this->update_manufacturer_field( $manufacturer_id, 'field_fab_site', 'fab_site', $site );
		$this->update_manufacturer_field( $manufacturer_id, 'field_fab_substances', 'fab_substances', $substance_submission['matched_ids'] );
		update_post_meta( $manufacturer_id, 'fab_compiled_substances', $substance_submission['compiled'] );
		update_post_meta( $manufacturer_id, 'fab_catalog_items', $substance_submission['catalog_items'] );
		if ( null !== $associate ) {
			update_post_meta( $manufacturer_id, 'fab_associate_status', $associate );
		}
		if ( null !== $process ) {
			update_post_meta( $manufacturer_id, 'fab_processo', $process );
		}
		if ( null !== $origin ) {
			update_post_meta( $manufacturer_id, 'fab_origem', $origin );
		}
		wp_set_object_terms( $manufacturer_id, array(), 'fabricante_setor', false );

		$hero_result = $this->handle_manufacturer_image_update(
			$manufacturer_id,
			'field_fab_hero_image',
			'fab_hero_image',
			'fab_hero_image_file',
			'fab_hero_image_remove'
		);

		if ( is_wp_error( $hero_result ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', $this->panel_form_url( $manufacturer_id ) ) );
			exit;
		}

		$this->sync_manufacturer_images( $manufacturer_id );

		$this->save_manufacturer_login_credentials( $manufacturer_id, $login_email, $login_password );

		wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'panel_saved', home_url( '/painel/' ) ) );
		exit;
	}

	public function handle_panel_delete() {
		if ( ! $this->is_panel_authenticated() ) {
			wp_safe_redirect( $this->panel_login_url() );
			exit;
		}

		if ( ! isset( $_POST['fabricamos_panel_delete_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fabricamos_panel_delete_nonce'] ) ), 'fabricamos_panel_delete' ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', home_url( '/painel/' ) ) );
			exit;
		}

		$manufacturer_id = isset( $_POST['panel_manufacturer_id'] ) ? absint( $_POST['panel_manufacturer_id'] ) : 0;
		if ( $manufacturer_id && 'fabricante' === get_post_type( $manufacturer_id ) ) {
			wp_delete_post( $manufacturer_id, true );
		}

		wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'panel_deleted', home_url( '/painel/' ) ) );
		exit;
	}

	public function handle_panel_export() {
		if ( ! $this->is_panel_authenticated() ) {
			wp_safe_redirect( $this->panel_login_url() );
			exit;
		}

		if ( ! isset( $_GET['fabricamos_panel_export_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['fabricamos_panel_export_nonce'] ) ), 'fabricamos_panel_export' ) ) {
			wp_safe_redirect( add_query_arg( self::QUERY_SUCCESS, 'error', home_url( '/painel/' ) ) );
			exit;
		}

		$search = isset( $_GET['empresa'] ) ? sanitize_text_field( wp_unslash( $_GET['empresa'] ) ) : '';
		$rows   = $this->get_panel_rows( $search );
		$file   = 'fabricamos-painel-' . gmdate( 'Y-m-d-His' ) . '.xls';
		$range  = 'R1C1:R' . max( 1, count( $rows ) + 1 ) . 'C10';

		nocache_headers();
		header( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $file . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Pragma: public' );
		header( 'Expires: 0' );

		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
	<Styles>
		<Style ss:ID="Default" ss:Name="Normal">
			<Alignment ss:Vertical="Center"/>
			<Borders>
				<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
				<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
				<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
				<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
			</Borders>
			<Font ss:FontName="Calibri" ss:Size="11" ss:Color="#203B67"/>
			<Interior/>
		</Style>
		<Style ss:ID="Header">
			<Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
			<Borders>
				<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
				<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
				<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
				<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
			</Borders>
			<Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
			<Interior ss:Color="#234785" ss:Pattern="Solid"/>
		</Style>
		<Style ss:ID="Cell">
			<Alignment ss:Vertical="Center" ss:WrapText="1"/>
			<Borders>
				<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
				<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
				<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
				<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#DDE4EF"/>
			</Borders>
			<Font ss:FontName="Calibri" ss:Size="11" ss:Color="#203B67"/>
		</Style>
	</Styles>
	<Worksheet ss:Name="Fabricamos">
		<Table ss:ExpandedColumnCount="10" ss:ExpandedRowCount="<?php echo (int) ( count( $rows ) + 1 ); ?>" x:FullColumns="1" x:FullRows="1" ss:DefaultRowHeight="18">
			<Column ss:Width="180"/>
			<Column ss:Width="80"/>
			<Column ss:Width="110"/>
			<Column ss:Width="90"/>
			<Column ss:Width="180"/>
			<Column ss:Width="120"/>
			<Column ss:Width="120"/>
			<Column ss:Width="95"/>
			<Column ss:Width="95"/>
			<Column ss:Width="140"/>
			<Row ss:AutoFitHeight="0" ss:Height="22">
				<Cell ss:StyleID="Header"><Data ss:Type="String">Empresa</Data></Cell>
				<Cell ss:StyleID="Header"><Data ss:Type="String">Associado</Data></Cell>
				<Cell ss:StyleID="Header"><Data ss:Type="String">Processo</Data></Cell>
				<Cell ss:StyleID="Header"><Data ss:Type="String">Origem</Data></Cell>
				<Cell ss:StyleID="Header"><Data ss:Type="String">Insumo</Data></Cell>
				<Cell ss:StyleID="Header"><Data ss:Type="String">DCB</Data></Cell>
				<Cell ss:StyleID="Header"><Data ss:Type="String">INN</Data></Cell>
				<Cell ss:StyleID="Header"><Data ss:Type="String">CAS</Data></Cell>
				<Cell ss:StyleID="Header"><Data ss:Type="String">NCM</Data></Cell>
				<Cell ss:StyleID="Header"><Data ss:Type="String">Certificado (CBPF)</Data></Cell>
			</Row>
			<?php foreach ( $rows as $row ) : ?>
				<Row>
					<Cell ss:StyleID="Cell"><Data ss:Type="String"><?php echo esc_html( $row['company'] ); ?></Data></Cell>
					<Cell ss:StyleID="Cell"><Data ss:Type="String"><?php echo esc_html( $row['associate'] ); ?></Data></Cell>
					<Cell ss:StyleID="Cell"><Data ss:Type="String"><?php echo esc_html( $row['process'] ); ?></Data></Cell>
					<Cell ss:StyleID="Cell"><Data ss:Type="String"><?php echo esc_html( $row['origin'] ); ?></Data></Cell>
					<Cell ss:StyleID="Cell"><Data ss:Type="String"><?php echo esc_html( $row['substance'] ); ?></Data></Cell>
					<Cell ss:StyleID="Cell"><Data ss:Type="String"><?php echo esc_html( $row['dcb'] ); ?></Data></Cell>
					<Cell ss:StyleID="Cell"><Data ss:Type="String"><?php echo esc_html( $row['inn'] ); ?></Data></Cell>
					<Cell ss:StyleID="Cell"><Data ss:Type="String"><?php echo esc_html( $row['cas'] ); ?></Data></Cell>
					<Cell ss:StyleID="Cell"><Data ss:Type="String"><?php echo esc_html( $row['ncm'] ); ?></Data></Cell>
					<Cell ss:StyleID="Cell"><Data ss:Type="String"><?php echo esc_html( $row['certificate'] ); ?></Data></Cell>
				</Row>
			<?php endforeach; ?>
		</Table>
		<AutoFilter x:Range="<?php echo esc_attr( $range ); ?>" xmlns="urn:schemas-microsoft-com:office:excel" />
		<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
			<FreezePanes/>
			<FrozenNoSplit/>
			<SplitHorizontal>1</SplitHorizontal>
			<TopRowBottomPane>1</TopRowBottomPane>
			<ActivePane>2</ActivePane>
			<Panes>
				<Pane>
					<Number>3</Number>
				</Pane>
				<Pane>
					<Number>2</Number>
					<ActiveRow>1</ActiveRow>
				</Pane>
			</Panes>
		</WorksheetOptions>
	</Worksheet>
</Workbook>
		<?php
		exit;
	}

	public function render_site_header( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'show_user' => false,
			)
		);

		$user_name = '';
		if ( $args['show_user'] && $this->is_public_site_authenticated() ) {
			$user_name = $this->get_public_site_user_name();
		}
		?>
		<header class="fab-site-header">
			<div class="fab-site-header__inner">
				<div class="fab-site-header__top">
					<div class="fab-site-header__spacer" aria-hidden="true"></div>
					<div class="fab-topbar__brand">
						<img src="<?php echo esc_url( $this->asset_url( 'img/brand-logo.png' ) ); ?>" alt="Abiquifi" class="fab-brand-strip" />
					</div>
					<div class="fab-site-header__flags">
						<img src="<?php echo esc_url( $this->asset_url( 'img/flags-strip-clean.png' ) ); ?>" alt="Idiomas" class="fab-flags-strip" />
					</div>
				</div>
				<div class="fab-site-header__navrow">
					<nav class="fab-navpills" aria-label="Menu principal">
						<?php $this->render_navigation_menu(); ?>
					</nav>
					<?php if ( $user_name ) : ?>
						<div class="fab-user-chip">
							<span class="fab-user-ico" aria-hidden="true"></span>
							<span>Olá, <strong><?php echo esc_html( $user_name ); ?></strong></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</header>
		<?php
	}

	public function register_shortcodes() {
		add_shortcode( 'dsf_user_menu', array( $this, 'render_dsf_user_menu_shortcode' ) );
		add_shortcode( 'fabricamos_account', array( $this, 'render_fabricamos_account_shortcode' ) );
	}

	public function render_dsf_user_menu_shortcode() {
		return '';
	}

	public function render_fabricamos_account_shortcode() {
		$sso = $this->public_sso();

		if ( ! $sso ) {
			return '';
		}

		$user = $sso->get_public_user();
		if ( ! $user ) {
			$sso->require_public_authentication( home_url( '/account/' ) );
			return '';
		}

		ob_start();
		?>
		<section class="fab-page fab-page--login">
			<div class="fab-login-wrap">
				<div class="fab-login-card">
					<div class="fab-account-card">
						<div class="fab-page-intro fab-page-intro--account">
							<span class="fab-page-kicker">Conta pública</span>
							<div class="fab-title-line-wrap">
								<h1 class="fab-screen-title">Minha conta</h1>
								<span class="fab-line"></span>
							</div>
							<p class="fab-page-copy">Seus dados públicos ficam acessíveis localmente no Fabricamos, com saída encerrando a sessão e devolvendo você ao dicionário deslogado.</p>
						</div>

						<div class="fab-panel fab-panel--soft fab-account-meta">
							<div class="fab-account-meta__row">
								<strong>Nome</strong>
								<span><?php echo esc_html( $user['display_name'] ); ?></span>
							</div>
							<div class="fab-account-meta__row">
								<strong>E-mail</strong>
								<span><?php echo esc_html( $user['user_email'] ); ?></span>
							</div>
						</div>

						<div class="fab-actions fab-actions--profile">
							<a class="fab-button fab-button--primary fab-button--block" href="<?php echo esc_url( $this->public_catalog_url() ); ?>">Ir para o catálogo</a>
							<a class="fab-button fab-button--ghost fab-button--block" href="<?php echo esc_url( $sso->public_logout_url( $this->public_post_logout_redirect_url() ) ); ?>">Sair</a>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php

		return (string) ob_get_clean();
	}

	public function render_navigation_menu() {
		$menu = wp_nav_menu(
			array(
				'theme_location' => self::MENU_LOCATION,
				'container'      => false,
				'echo'           => false,
				'fallback_cb'    => false,
				'items_wrap'     => '%3$s',
				'depth'          => 1,
				'link_before'    => '',
				'link_after'     => '',
			)
		);

		if ( $menu ) {
			$menu = preg_replace( '/<a /', '<a class="fab-pill" ', $menu );
			echo $menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		foreach ( $this->default_menu_items() as $item ) {
			printf(
				'<a class="fab-pill" href="%1$s">%2$s</a>',
				esc_url( $item['url'] ),
				esc_html( $item['title'] )
			);
		}
	}

	public function asset_url( $relative ) {
		return plugin_dir_url( dirname( __FILE__ ) ) . 'assets/' . ltrim( $relative, '/' );
	}

	public function login_url() {
		return home_url( '/fabricante/' );
	}

	public function panel_login_url() {
		return home_url( '/painel/' );
	}

	public function site_login_url( $redirect_to = '' ) {
		$base_url = $this->get_site_login_page_url();
		if ( '' === $redirect_to ) {
			return $base_url;
		}

		return $this->build_redirected_page_url( $base_url, $redirect_to );
	}

	public function site_register_url( $redirect_to = '' ) {
		$base_url = $this->get_site_register_url();
		if ( '' === $redirect_to ) {
			return $base_url;
		}

		return $this->build_redirected_page_url( $base_url, $redirect_to );
	}

	protected function public_post_logout_redirect_url() {
		return $this->public_catalog_url();
	}

	public function logout_url( $scope ) {
		return add_query_arg(
			array(
				'action' => 'fabricamos_logout',
				'scope'  => $scope,
			),
			admin_url( 'admin-post.php' )
		);
	}

	public function is_panel_user( $user ) {
		return $user instanceof WP_User && 0 === strcasecmp( (string) $user->user_email, 'abiquifi@gmail.com' );
	}

	public function handle_logout() {
		$scope = isset( $_GET['scope'] ) ? sanitize_text_field( wp_unslash( $_GET['scope'] ) ) : '';
		$redirect_to = $this->public_catalog_url();

		if ( 'manufacturer' === $scope ) {
			$this->clear_manufacturer_auth();
			$redirect_to = $this->login_url();
		} elseif ( 'panel' === $scope ) {
			$this->clear_panel_auth();
			$redirect_to = $this->panel_login_url();
		} else {
			$this->clear_public_access_state();

			if ( $this->public_sso() ) {
				$token = isset( $_COOKIE['abiquifi_public_sso'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['abiquifi_public_sso'] ) ) : '';
				$this->public_sso()->remote_logout( $token );
				$this->public_sso()->clear_public_cookie();
			}
			wp_logout();
			$redirect_to = $this->public_post_logout_redirect_url();
		}

		wp_safe_redirect( $redirect_to );
		exit;
	}

	public function maybe_hide_admin_bar( $show ) {
		if ( $this->is_fabricamos_request() ) {
			return false;
		}

		return $show;
	}

	public function panel_form_url( $manufacturer_id = 0 ) {
		$url = add_query_arg( 'view', 'form', home_url( '/painel/' ) );
		if ( $manufacturer_id ) {
			$url = add_query_arg( 'id', (int) $manufacturer_id, $url );
		}

		return $url;
	}

	public function get_panel_context() {
		$search      = isset( $_GET['empresa'] ) ? sanitize_text_field( wp_unslash( $_GET['empresa'] ) ) : '';
		$current     = isset( $_GET['pagina'] ) ? max( 1, absint( $_GET['pagina'] ) ) : 1;
		$per_page    = 10;
		$rows        = $this->get_panel_rows( $search );
		$total_rows  = count( $rows );
		$total_pages = max( 1, (int) ceil( $total_rows / $per_page ) );
		$current     = min( $current, $total_pages );
		$offset      = ( $current - 1 ) * $per_page;

		return array(
			'search'       => $search,
			'rows'         => array_slice( $rows, $offset, $per_page ),
			'blank_rows'   => max( 0, $per_page - count( array_slice( $rows, $offset, $per_page ) ) ),
			'current_page' => $current,
			'total_pages'  => $total_pages,
			'total_rows'   => $total_rows,
			'per_page'     => $per_page,
			'create_url'   => $this->panel_form_url(),
			'base_url'     => home_url( '/painel/' ),
			'export_url'   => wp_nonce_url(
				add_query_arg(
					array_filter(
						array(
							'action'  => 'fabricamos_panel_export',
							'empresa' => $search,
						),
						static function ( $value ) {
							return '' !== $value && null !== $value;
						}
					),
					admin_url( 'admin-post.php' )
				),
				'fabricamos_panel_export',
				'fabricamos_panel_export_nonce'
			),
		);
	}

	protected function get_panel_rows( $search = '' ) {
		$query_args  = array(
			'post_type'      => 'fabricante',
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		if ( '' !== $search ) {
			$query_args['s'] = $search;
		}

		$manufacturers = get_posts( $query_args );
		$rows          = array();

		foreach ( $manufacturers as $manufacturer ) {
			$detail       = $this->get_manufacturer_detail( $manufacturer );
			$editor       = $this->get_manufacturer_editor_detail( $manufacturer->ID );
			$associate    = $this->get_manufacturer_meta_text( $manufacturer->ID, 'fab_associate_status' );
			$process      = $this->get_manufacturer_meta_text( $manufacturer->ID, 'fab_processo' );
			$origin       = $this->get_manufacturer_meta_text( $manufacturer->ID, 'fab_origem' );
			$display_rows = $this->get_manufacturer_panel_substances( $manufacturer->ID );
			$login_email  = $this->get_manufacturer_login_email( $manufacturer->ID );
			$has_password = $this->manufacturer_has_login_password( $manufacturer->ID );
			$password_raw = $this->get_manufacturer_login_plain_password( $manufacturer->ID );

			foreach ( $display_rows as $substance ) {
				$rows[] = array(
					'id'            => (int) $manufacturer->ID,
					'company'       => $detail['title'],
					'associate'     => $associate ? $associate : '-',
					'process'       => $process ? $process : '-',
					'origin'        => $origin ? $origin : '-',
					'substance'     => $substance ? $this->panel_catalog_value( $substance['title'] ) : '-',
					'dcb'           => $substance ? $this->panel_catalog_value( isset( $substance['meta']['dcb'] ) ? $substance['meta']['dcb'] : '' ) : '-',
					'inn'           => $substance ? $this->panel_catalog_value( isset( $substance['meta']['inn'] ) ? $substance['meta']['inn'] : '' ) : '-',
					'cas'           => $substance ? $this->panel_catalog_value( isset( $substance['meta']['cas'] ) ? $substance['meta']['cas'] : '' ) : '-',
					'ncm'           => $substance ? $this->panel_catalog_value( isset( $substance['meta']['ncm'] ) ? $substance['meta']['ncm'] : '' ) : '-',
					'certificate'   => $substance ? $this->panel_catalog_value( isset( $substance['meta']['cbpf'] ) ? $substance['meta']['cbpf'] : '' ) : '-',
					'contact_name'  => $editor['name'] ? $editor['name'] : '-',
					'phone'         => $editor['phone'] ? $editor['phone'] : '-',
					'email'         => $login_email ? $login_email : '-',
					'password'      => $has_password ? '••••••' : '-',
					'password_raw'  => $password_raw,
					'edit_url'      => $this->panel_form_url( $manufacturer->ID ),
					'delete_target' => get_the_title( $manufacturer ),
				);
			}
		}

		return $rows;
	}

	protected function get_available_processes() {
		$posts = get_posts(
			array(
				'post_type'      => 'fabricante',
				'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		$items = array();
		foreach ( $posts as $post_id ) {
			$value = $this->get_manufacturer_meta_text( $post_id, 'fab_processo' );
			if ( '' === $value ) {
				continue;
			}

			$parts = preg_split( '/\s*\/\s*/', $value );
			if ( ! is_array( $parts ) ) {
				$parts = array( $value );
			}

			foreach ( $parts as $part ) {
				$part = trim( (string) $part );
				if ( '' !== $part ) {
					$items[] = $part;
				}
			}
		}

		$items = array_values( array_unique( $items ) );
		natcasesort( $items );
		return array_values( $items );
	}

	protected function get_manufacturer_meta_text( $post_id, $key ) {
		$value = get_post_meta( $post_id, $key, true );
		return is_string( $value ) ? trim( $value ) : '';
	}

	protected function is_placeholder_catalog_value( $value ) {
		$normalized = $this->normalize_lookup_value( trim( (string) $value ) );
		return in_array(
			$normalized,
			array(
				'',
				'n/a',
				'n/a - significa nao se aplica',
				'nao aplicavel',
				'nao se aplica',
				'nao possui',
			),
			true
		);
	}

	protected function clean_catalog_value( $value ) {
		$text = trim( $this->repair_mojibake_text( (string) $value ) );
		return $this->is_placeholder_catalog_value( $text ) ? '' : $text;
	}

	protected function panel_catalog_value( $value ) {
		$text = $this->clean_catalog_value( $value );
		return '' === $text ? '-' : $text;
	}

	protected function get_manufacturer_compiled_substances( $post_id ) {
		$value = get_post_meta( $post_id, 'fab_compiled_substances', true );

		if ( is_string( $value ) ) {
			$value = maybe_unserialize( $value );
		}

		if ( ! is_array( $value ) ) {
			$value = array( $value );
		}

		return array_values(
			array_filter(
				array_map(
					function ( $item ) {
						return is_string( $item ) ? $this->clean_catalog_value( $item ) : '';
					},
					$value
				)
			)
		);
	}

	protected function get_manufacturer_catalog_items( $post_id ) {
		$value = get_post_meta( $post_id, 'fab_catalog_items', true );

		if ( is_string( $value ) ) {
			$value = maybe_unserialize( $value );
		}

		if ( ! is_array( $value ) ) {
			return array();
		}

		$items = array();
		foreach ( $value as $index => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$title = '';
			foreach ( array( 'insumo', 'display_name', 'dcb', 'inn' ) as $candidate ) {
				if ( ! empty( $item[ $candidate ] ) ) {
					$title = $this->clean_catalog_value( $item[ $candidate ] );
					if ( '' !== $title ) {
						break;
					}
				}
			}

			if ( '' === $title ) {
				continue;
			}

			$meta = array(
				'insumo'   => isset( $item['insumo'] ) ? $this->clean_catalog_value( $item['insumo'] ) : '',
				'dcb'      => isset( $item['dcb'] ) ? $this->clean_catalog_value( $item['dcb'] ) : '',
				'inn'      => isset( $item['inn'] ) ? $this->clean_catalog_value( $item['inn'] ) : '',
				'cas'      => isset( $item['cas'] ) ? $this->clean_catalog_value( $item['cas'] ) : '',
				'ncm'      => isset( $item['ncm'] ) ? $this->clean_catalog_value( $item['ncm'] ) : '',
				'cbpf'     => isset( $item['cbpf'] ) ? $this->clean_catalog_value( $item['cbpf'] ) : '',
				'validade' => isset( $item['validade'] ) ? $this->clean_catalog_value( $item['validade'] ) : '',
			);

			$items[] = array(
				'id'      => 'catalog-' . $post_id . '-' . $index,
				'title'   => $title,
				'summary' => $this->format_substance_summary( $meta ),
				'meta'    => $meta,
			);
		}

		return $items;
	}

	protected function get_local_substance_library( $search = '', $limit = 6 ) {
		$search      = trim( (string) $search );
		$normalized  = $this->normalize_lookup_value( $search );
		$posts       = get_posts(
			array(
				'post_type'      => 'fabricante',
				'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		$results     = array();
		$seen_titles = array();

		foreach ( $posts as $post_id ) {
			$local_items = $this->get_manufacturer_catalog_items( $post_id );

			if ( empty( $local_items ) ) {
				foreach ( $this->get_manufacturer_compiled_substances( $post_id ) as $index => $compiled_name ) {
					$local_items[] = array(
						'id'      => 'compiled-' . $post_id . '-' . $index,
						'title'   => $compiled_name,
						'summary' => '',
						'meta'    => array(
							'insumo'   => '',
							'dcb'      => '',
							'inn'      => '',
							'cas'      => '',
							'ncm'      => '',
							'cbpf'     => '',
							'validade' => '',
						),
					);
				}
			}

			foreach ( $local_items as $item ) {
				$title_key = $this->normalize_lookup_value( $item['title'] );
				if ( '' === $title_key || isset( $seen_titles[ $title_key ] ) ) {
					continue;
				}

				$haystack = $this->normalize_lookup_value(
					implode(
						' ',
						array_filter(
							array(
								$item['title'],
								isset( $item['meta']['insumo'] ) ? $item['meta']['insumo'] : '',
								isset( $item['meta']['dcb'] ) ? $item['meta']['dcb'] : '',
								isset( $item['meta']['inn'] ) ? $item['meta']['inn'] : '',
								isset( $item['meta']['cas'] ) ? $item['meta']['cas'] : '',
								isset( $item['meta']['ncm'] ) ? $item['meta']['ncm'] : '',
							)
						)
					)
				);

				if ( '' !== $normalized && false === strpos( $haystack, $normalized ) ) {
					continue;
				}

				$results[] = $item;
				$seen_titles[ $title_key ] = true;

				if ( count( $results ) >= $limit ) {
					break 2;
				}
			}
		}

		return $results;
	}

	protected function get_manufacturer_panel_substances( $post_id ) {
		$catalog_items = $this->get_manufacturer_catalog_items( $post_id );
		if ( ! empty( $catalog_items ) ) {
			return $catalog_items;
		}

		$compiled = $this->get_manufacturer_compiled_substances( $post_id );
		if ( ! empty( $compiled ) ) {
			$items = array();
			foreach ( $compiled as $index => $compiled_name ) {
				$items[] = array(
					'id'      => 'compiled-' . $post_id . '-' . $index,
					'title'   => $compiled_name,
					'summary' => '',
					'meta'    => array(
						'insumo'   => '',
						'dcb'      => '',
						'inn'      => '',
						'cas'      => '',
						'ncm'      => '',
						'cbpf'     => '',
						'validade' => '',
					),
				);
			}

			return $items;
		}

		$detail_substances = $this->get_manufacturer_substances( $post_id );
		return empty( $detail_substances ) ? array( null ) : $detail_substances;
	}

	protected function find_substance_post_by_name( $name ) {
		$search = trim( (string) $name );
		if ( '' === $search ) {
			return null;
		}

		$posts      = $this->search_dictionary_substances( $search, 10 );
		$normalized = $this->normalize_lookup_value( $search );

		foreach ( $posts as $post ) {
			if ( $this->normalize_lookup_value( $post->post_title ) === $normalized ) {
				return $post;
			}
		}

		return empty( $posts ) ? null : $posts[0];
	}

	protected function normalize_lookup_value( $value ) {
		$value = $this->repair_mojibake_text( (string) $value );
		$value = strtolower( remove_accents( wp_strip_all_tags( (string) $value ) ) );
		$value = preg_replace( '/\s+/', ' ', $value );
		return trim( (string) $value );
	}

	protected function repair_mojibake_text( $value ) {
		$value = (string) $value;
		if ( '' === $value ) {
			return '';
		}

		if ( $this->contains_mojibake_text( $value ) ) {
			$best       = $value;
			$best_score = $this->mojibake_score( $best );
			$candidates = array();

			if ( function_exists( 'mb_convert_encoding' ) ) {
				$candidates[] = @mb_convert_encoding( $value, 'ISO-8859-1', 'UTF-8' );
				$candidates[] = @mb_convert_encoding( $value, 'Windows-1252', 'UTF-8' );
			}

			if ( function_exists( 'iconv' ) ) {
				$candidates[] = @iconv( 'UTF-8', 'ISO-8859-1//IGNORE', $value );
				$candidates[] = @iconv( 'UTF-8', 'Windows-1252//IGNORE', $value );
			}

			foreach ( $candidates as $candidate ) {
				if ( ! is_string( $candidate ) || '' === $candidate ) {
					continue;
				}

				$candidate_score = $this->mojibake_score( $candidate );
				if ( $candidate_score < $best_score ) {
					$best       = $candidate;
					$best_score = $candidate_score;
				}
			}

			$value = $best;
		}

		$replacements = array(
			'Ã¡' => 'á',
			'Ãà' => 'à',
			'Ãâ' => 'â',
			'Ãã' => 'ã',
			'Ãä' => 'ä',
			'ÃÁ' => 'Á',
			'ÃÀ' => 'À',
			'ÃÂ' => 'Â',
			'ÃÃ' => 'Ã',
			'ÃÄ' => 'Ä',
			'Ã©' => 'é',
			'Ã¨' => 'è',
			'Ãê' => 'ê',
			'Ãë' => 'ë',
			'Ã‰' => 'É',
			'Ãˆ' => 'È',
			'ÃŠ' => 'Ê',
			'Ã‹' => 'Ë',
			'Ãí' => 'í',
			'Ã¬' => 'ì',
			'Ãî' => 'î',
			'Ãï' => 'ï',
			'ÃÍ' => 'Í',
			'ÃŒ' => 'Ì',
			'ÃŽ' => 'Î',
			'Ã' => 'Ï',
			'Ã³' => 'ó',
			'Ã²' => 'ò',
			'Ã´' => 'ô',
			'Ãµ' => 'õ',
			'Ãö' => 'ö',
			'Ã“' => 'Ó',
			'Ã’' => 'Ò',
			'Ã”' => 'Ô',
			'Ã•' => 'Õ',
			'Ã–' => 'Ö',
			'Ãº' => 'ú',
			'Ã¹' => 'ù',
			'Ã»' => 'û',
			'Ã¼' => 'ü',
			'Ãš' => 'Ú',
			'Ã™' => 'Ù',
			'Ã›' => 'Û',
			'Ãœ' => 'Ü',
			'Ã§' => 'ç',
			'Ã‡' => 'Ç',
			'Ã±' => 'ñ',
			'Ã‘' => 'Ñ',
			'Âº' => 'º',
			'Âª' => 'ª',
			'Â°' => '°',
			'Â·' => '·',
			'â€“' => '–',
			'â€”' => '—',
			'â€˜' => '‘',
			'â€™' => '’',
			'â€œ' => '“',
			'â€' => '”',
			'â€¢' => '•',
			'â€¦' => '…',
			'Â'   => '',
		);

		return strtr( $value, $replacements );
	}

	protected function contains_mojibake_text( $value ) {
		return false !== strpos( (string) $value, 'Ã' ) || false !== strpos( (string) $value, 'Â' ) || false !== strpos( (string) $value, 'â€' );
	}

	protected function mojibake_score( $value ) {
		$value = (string) $value;

		return substr_count( $value, 'Ã' ) + substr_count( $value, 'Â' ) + substr_count( $value, 'â€' );
	}

	protected function get_manufacturer_display_title( $post ) {
		$post = $post instanceof WP_Post ? $post : get_post( $post );
		if ( ! $post instanceof WP_Post ) {
			return '';
		}

		$title = (string) get_post_meta( $post->ID, 'fab_company_name', true );
		if ( '' === trim( $title ) ) {
			$title = get_the_title( $post );
		}

		return $this->repair_mojibake_text( $title );
	}

	protected function get_manufacturer_preference_score( $post ) {
		$post = $post instanceof WP_Post ? $post : get_post( $post );
		if ( ! $post instanceof WP_Post ) {
			return -9999;
		}

		$score = 0;
		$title = (string) $post->post_title;
		$display_title = $this->get_manufacturer_display_title( $post );

		if ( has_post_thumbnail( $post ) ) {
			$score += 100;
		}

		$logo = $this->get_manufacturer_image_data( $post->ID, 'fab_logo' );
		if ( ! empty( $logo['url'] ) ) {
			$score += 75;
		}

		$hero = $this->get_manufacturer_image_data( $post->ID, 'fab_hero_image' );
		if ( ! empty( $hero['url'] ) ) {
			$score += 50;
		}

		if ( ! $this->contains_mojibake_text( $title ) ) {
			$score += 40;
		}

		if ( $display_title === $title ) {
			$score += 20;
		}

		if ( 'publish' === $post->post_status ) {
			$score += 10;
		}

		return $score;
	}

	protected function is_associated_manufacturer( $post_id ) {
		$status = $this->normalize_lookup_value( $this->get_manufacturer_meta_text( $post_id, 'fab_associate_status' ) );
		if ( '' === $status ) {
			return true;
		}

		return false === strpos( $status, 'nao associado' );
	}

	protected function get_manufacturer_editor_detail( $post_id ) {
		$name  = (string) get_post_meta( $post_id, 'fab_responsavel_nome', true );
		$phone = (string) get_post_meta( $post_id, 'fab_responsavel_telefone', true );
		$email = (string) get_post_meta( $post_id, 'fab_responsavel_email', true );

		if ( '' === trim( $name ) ) {
			$name = $this->get_manufacturer_field( $post_id, 'fab_contact_name' );
		}
		if ( '' === trim( $phone ) ) {
			$phone = $this->get_manufacturer_field( $post_id, 'fab_phone' );
		}
		if ( '' === trim( $email ) ) {
			$email = $this->get_manufacturer_field( $post_id, 'fab_email' );
		}

		return array(
			'name'  => $name,
			'phone' => $phone,
			'email' => $email,
		);
	}

	public function get_panel_form_context( $manufacturer_id = 0 ) {
		$manufacturer = $manufacturer_id ? get_post( $manufacturer_id ) : null;
		$detail       = $manufacturer instanceof WP_Post ? $this->get_manufacturer_detail( $manufacturer ) : null;
		$editor       = $manufacturer instanceof WP_Post ? $this->get_manufacturer_editor_detail( $manufacturer->ID ) : array(
			'name'  => '',
			'phone' => '',
			'email' => '',
		);
		$placeholder  = $this->editor_placeholder_image_url();

		return array(
			'id'             => $manufacturer instanceof WP_Post ? (int) $manufacturer->ID : 0,
			'title'          => $manufacturer instanceof WP_Post ? $this->get_manufacturer_display_title( $manufacturer ) : '',
			'associate'      => $manufacturer instanceof WP_Post ? $this->get_manufacturer_meta_text( $manufacturer->ID, 'fab_associate_status' ) : '',
			'process'        => $manufacturer instanceof WP_Post ? $this->get_manufacturer_meta_text( $manufacturer->ID, 'fab_processo' ) : '',
			'origin'         => $manufacturer instanceof WP_Post ? $this->get_manufacturer_meta_text( $manufacturer->ID, 'fab_origem' ) : '',
			'description'    => $detail ? $detail['description'] : '',
			'contact_name'   => $detail ? $detail['contact_name'] : '',
			'phone'          => $detail ? $detail['phone'] : '',
			'email'          => $detail ? $detail['email'] : '',
			'editor_name'    => $editor['name'],
			'editor_phone'   => $editor['phone'],
			'editor_email'   => $editor['email'],
			'site'           => $detail ? $detail['site'] : '',
			'image'          => $detail ? ( $detail['has_hero_image'] ? $detail['hero_image'] : $placeholder ) : $placeholder,
			'has_image'      => $detail ? $detail['has_hero_image'] : false,
			'placeholder'    => $placeholder,
			'substances'     => $detail ? $detail['substances'] : array(),
			'login_email'    => $manufacturer instanceof WP_Post ? ( $this->get_manufacturer_login_email( $manufacturer->ID ) ? $this->get_manufacturer_login_email( $manufacturer->ID ) : $editor['email'] ) : '',
			'login_password' => '',
			'is_edit'        => $manufacturer instanceof WP_Post,
			'processes'      => $this->get_available_processes(),
		);
	}

	public function filter_document_title_parts( $parts ) {
		if ( ! is_array( $parts ) || ! is_singular( 'fabricante' ) ) {
			return $parts;
		}

		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return $parts;
		}

		$parts['title'] = $this->get_manufacturer_display_title( $post );
		return $parts;
	}

	public function filter_document_title( $title ) {
		if ( ! is_singular( 'fabricante' ) ) {
			return $title;
		}

		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return $title;
		}

		return $this->get_manufacturer_display_title( $post );
	}

	public function get_manufacturer_sector_slug( $post_id ) {
		$terms = get_the_terms( $post_id, 'fabricante_setor' );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}

		return $terms[0]->slug;
	}

	public function get_catalog_context( $mode = 'comprador' ) {
		$filters = array(
			'company'   => isset( $_GET['empresa'] ) ? sanitize_text_field( wp_unslash( $_GET['empresa'] ) ) : '',
			'process'   => isset( $_GET['processo'] ) ? sanitize_text_field( wp_unslash( $_GET['processo'] ) ) : '',
			'substance' => isset( $_GET['substancia'] ) ? sanitize_text_field( wp_unslash( $_GET['substancia'] ) ) : '',
		);
		$current_page     = isset( $_GET['pagina'] ) ? max( 1, absint( $_GET['pagina'] ) ) : 1;
		$per_page         = 6;

		$manufacturer_ids = $this->search_manufacturer_ids( $filters );
		$query            = new WP_Query(
			array(
				'post_type'      => 'fabricante',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__in'       => empty( $manufacturer_ids ) && $this->has_active_filters( $filters ) ? array( 0 ) : $manufacturer_ids,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		$unique_posts = array();
		$seen_titles  = array();
		foreach ( $query->posts as $post ) {
			$key = $this->normalize_lookup_value( $this->get_manufacturer_display_title( $post ) );
			if ( isset( $seen_titles[ $key ] ) ) {
				$current = $unique_posts[ $seen_titles[ $key ] ];
				if ( $this->get_manufacturer_preference_score( $post ) > $this->get_manufacturer_preference_score( $current ) ) {
					$unique_posts[ $seen_titles[ $key ] ] = $post;
				}
				continue;
			}

			$seen_titles[ $key ] = count( $unique_posts );
			$unique_posts[]      = $post;
		}

		$total_items = count( $unique_posts );
		$total_pages = max( 1, (int) ceil( $total_items / $per_page ) );
		$current_page = min( $current_page, $total_pages );
		$offset = ( $current_page - 1 ) * $per_page;
		$current_cards = array_slice( $unique_posts, $offset, $per_page );

		return array(
			'mode'              => $mode,
			'filters'           => $filters,
			'has_filters'       => $this->has_active_filters( $filters ),
			'manufacturers'     => $unique_posts,
			'primary_cards'     => $current_cards,
			'processes'         => $this->get_available_processes(),
			'detail_text'       => $this->get_filter_detail_text( $filters ),
			'edit_url'          => home_url( '/meu-fabricante/' ),
			'current_page'      => $current_page,
			'total_pages'       => $total_pages,
			'per_page'          => $per_page,
			'total_items'       => $total_items,
		);
	}

	protected function has_active_filters( $filters ) {
		return ! empty( $filters['company'] ) || ! empty( $filters['process'] ) || ! empty( $filters['substance'] );
	}

	protected function get_filter_detail_text( $filters ) {
		if ( empty( $filters['substance'] ) ) {
			return 'Filtre por uma substância';
		}

		$substances = $this->search_substances( $filters['substance'], 1 );
		if ( empty( $substances ) ) {
			return 'Nenhuma substância encontrada para o termo informado.';
		}

		return $this->format_substance_summary( $substances[0]['meta'] );
	}

	protected function search_manufacturer_ids( $filters ) {
		$args = array(
			'post_type'      => 'fabricante',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		if ( ! empty( $filters['company'] ) ) {
			$args['s'] = $filters['company'];
		}

		if ( ! empty( $filters['process'] ) ) {
			$args['meta_query'] = array(
				array(
					'key'     => 'fab_processo',
					'value'   => $filters['process'],
					'compare' => 'LIKE',
				),
			);
		}

		$ids = array_values(
			array_filter(
				get_posts( $args ),
				function ( $post_id ) {
					return $this->is_associated_manufacturer( $post_id );
				}
			)
		);

		if ( empty( $filters['substance'] ) ) {
			return $ids;
		}

		$matched = array();
		foreach ( $ids as $post_id ) {
			if ( $this->manufacturer_matches_substance_filter( $post_id, $filters['substance'] ) ) {
				$matched[] = $post_id;
			}
		}

		return $matched;
	}

	protected function manufacturer_matches_substance_filter( $post_id, $search ) {
		$normalized_search = $this->normalize_lookup_value( $search );
		if ( '' === $normalized_search ) {
			return true;
		}

		foreach ( $this->get_manufacturer_panel_substances( $post_id ) as $item ) {
			if ( empty( $item ) ) {
				continue;
			}

			$haystack = $this->normalize_lookup_value(
				implode(
					' ',
					array_filter(
						array(
							$item['title'],
							isset( $item['meta']['insumo'] ) ? $item['meta']['insumo'] : '',
							isset( $item['meta']['dcb'] ) ? $item['meta']['dcb'] : '',
							isset( $item['meta']['inn'] ) ? $item['meta']['inn'] : '',
							isset( $item['meta']['cas'] ) ? $item['meta']['cas'] : '',
							isset( $item['meta']['ncm'] ) ? $item['meta']['ncm'] : '',
						)
					)
				)
			);

			if ( false !== strpos( $haystack, $normalized_search ) ) {
				return true;
			}
		}

		return false;
	}

	public function get_manufacturer_card_data( $post ) {
		$logo = $this->get_manufacturer_image_data( $post->ID, 'fab_logo' );
		$hero = $this->get_manufacturer_image_data( $post->ID, 'fab_hero_image' );
		$image = ! empty( $hero['url'] ) ? $hero : $logo;

		return array(
			'id'        => (int) $post->ID,
			'title'     => $this->get_manufacturer_display_title( $post ),
			'url'       => get_permalink( $post ),
			'image'     => ! empty( $image['url'] ) ? $image['url'] : $this->placeholder_image_url(),
			'has_image' => ! empty( $image['url'] ),
			'process'   => $this->get_manufacturer_meta_text( $post->ID, 'fab_processo' ),
		);
	}

	public function get_manufacturer_view_context() {
		$context = isset( $_GET['fab_context'] ) ? sanitize_key( wp_unslash( $_GET['fab_context'] ) ) : 'catalogo';
		$allowed = array( 'catalogo', 'fabricante', 'painel' );

		return in_array( $context, $allowed, true ) ? $context : 'catalogo';
	}

	public function get_manufacturer_view_url( $post, $context = 'catalogo' ) {
		$post = $post instanceof WP_Post ? $post : get_post( $post );
		if ( ! $post instanceof WP_Post ) {
			return home_url( '/catalogo/' );
		}

		return add_query_arg( 'fab_context', sanitize_key( $context ), get_permalink( $post ) );
	}

	public function get_manufacturer_back_url( $context = 'catalogo' ) {
		if ( 'painel' === $context && ( $this->is_panel_authenticated() || current_user_can( 'manage_options' ) ) ) {
			return home_url( '/painel/' );
		}

		if ( 'fabricante' === $context && $this->is_manufacturer_authenticated() ) {
			return home_url( '/fabricamos-fabricante/' );
		}

		return home_url( '/catalogo/' );
	}

	public function can_show_manufacturer_edit_action( $post_id, $context = 'catalogo' ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return false;
		}

		if ( 'painel' === $context ) {
			return $this->is_panel_authenticated() || current_user_can( 'manage_options' );
		}

		if ( 'fabricante' !== $context ) {
			return false;
		}

		$manufacturer = $this->get_authenticated_manufacturer();
		return $manufacturer instanceof WP_Post && (int) $manufacturer->ID === $post_id;
	}

	public function get_manufacturer_edit_url( $post_id, $context = 'catalogo' ) {
		$post_id = absint( $post_id );
		if ( ! $this->can_show_manufacturer_edit_action( $post_id, $context ) ) {
			return '';
		}

		if ( 'painel' === $context ) {
			return $this->panel_form_url( $post_id );
		}

		return home_url( '/meu-fabricante/' );
	}

	public function get_manufacturer_sector_name( $post_id ) {
		$terms = get_the_terms( $post_id, 'fabricante_setor' );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}

		return $terms[0]->name;
	}

	public function get_manufacturer_detail( $post ) {
		$description = $this->get_manufacturer_field( $post->ID, 'fab_description' );
		$name        = $this->get_manufacturer_field( $post->ID, 'fab_contact_name' );
		$phone       = $this->get_manufacturer_field( $post->ID, 'fab_phone' );
		$email       = $this->get_manufacturer_field( $post->ID, 'fab_email' );
		$site        = $this->get_manufacturer_field( $post->ID, 'fab_site' );
		$hero        = $this->get_manufacturer_image_data( $post->ID, 'fab_hero_image' );
		$logo        = $this->get_manufacturer_image_data( $post->ID, 'fab_logo' );
		$editor      = $this->get_manufacturer_editor_detail( $post->ID );

		return array(
			'post'             => $post,
			'title'            => $this->get_manufacturer_display_title( $post ),
			'description'      => $description,
			'contact_name'     => $name,
			'phone'            => $phone,
			'email'            => $email,
			'editor_name'      => $editor['name'],
			'editor_phone'     => $editor['phone'],
			'editor_email'     => $editor['email'],
			'site'             => $site,
			'hero_image'       => ! empty( $hero['url'] ) ? $hero['url'] : $this->placeholder_image_url(),
			'has_hero_image'   => ! empty( $hero['url'] ),
			'logo_image'       => ! empty( $logo['url'] ) ? $logo['url'] : $this->placeholder_image_url(),
			'has_logo_image'   => ! empty( $logo['url'] ),
			'image_placeholder'=> $this->placeholder_image_url(),
			'editor_placeholder'=> $this->editor_placeholder_image_url(),
			'sector'           => $this->get_manufacturer_sector_name( $post->ID ),
			'substances'       => $this->get_manufacturer_substances( $post->ID ),
			'can_edit'         => $this->current_user_can_edit_manufacturer( $post->ID ),
			'profile_edit_url' => home_url( '/meu-fabricante/' ),
		);
	}

	public function get_manufacturer_substances( $post_id ) {
		$catalog_items = $this->get_manufacturer_catalog_items( $post_id );
		if ( ! empty( $catalog_items ) ) {
			return $catalog_items;
		}

		$compiled = $this->get_manufacturer_compiled_substances( $post_id );
		if ( ! empty( $compiled ) ) {
			$items = array();
			foreach ( $compiled as $index => $compiled_name ) {
				$items[] = array(
					'id'      => 'compiled-' . $post_id . '-' . $index,
					'title'   => $compiled_name,
					'summary' => '',
					'meta'    => array(
						'insumo'   => '',
						'dcb'      => '',
						'inn'      => '',
						'cas'      => '',
						'ncm'      => '',
						'cbpf'     => '',
						'validade' => '',
					),
				);
			}

			return $items;
		}

		$ids = $this->get_manufacturer_substance_ids( $post_id );

		$items = array();
		foreach ( $ids as $id ) {
			$post = get_post( $id );
			if ( ! $post instanceof WP_Post ) {
				continue;
			}
			$parsed = $this->parse_dsf_post( $post );
			$items[] = array(
				'id'      => (int) $post->ID,
				'title'   => $post->post_title,
				'summary' => $this->format_substance_summary( $parsed ),
				'meta'    => $parsed,
			);
		}

		return $items;
	}

	public function get_current_user_manufacturer() {
		return $this->get_authenticated_manufacturer();
	}

	public function get_manufacturer_for_user( $user_id ) {
		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return null;
		}

		$posts = get_posts(
			array(
				'post_type'      => 'fabricante',
				'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
				'author'         => $user_id,
				'posts_per_page' => 1,
			)
		);

		return empty( $posts ) ? null : $posts[0];
	}

	protected function assign_manufacturer_login_user( $manufacturer_id, $email, $password, $company_name ) {
		$email = sanitize_email( (string) $email );

		if ( '' === $email ) {
			return true;
		}

		$existing_user = get_user_by( 'email', $email );
		$current_post  = get_post( $manufacturer_id );
		$current_author = $current_post instanceof WP_Post ? (int) $current_post->post_author : 0;

		if ( $existing_user instanceof WP_User ) {
			$linked_manufacturer = $this->get_manufacturer_for_user( $existing_user->ID );
			if ( $linked_manufacturer instanceof WP_Post && (int) $linked_manufacturer->ID !== (int) $manufacturer_id ) {
				return new WP_Error( 'panel_user_taken', 'Este usuário já está vinculado a outro fabricante.' );
			}

			$user_id = (int) $existing_user->ID;
		} else {
			$username = $this->generate_username_from_email( $email );
			$user_id  = wp_insert_user(
				array(
					'user_login'   => $username,
					'user_pass'    => '' !== $password ? $password : wp_generate_password( 12, true, true ),
					'user_email'   => $email,
					'display_name' => $company_name,
					'role'         => self::ROLE,
				)
			);

			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}
		}

		wp_update_user(
			array(
				'ID'           => $user_id,
				'user_email'   => $email,
				'display_name' => $company_name,
				'role'         => self::ROLE,
			)
		);

		if ( '' !== $password ) {
			wp_set_password( $password, $user_id );
		}

		if ( $current_author !== (int) $user_id ) {
			wp_update_post(
				array(
					'ID'          => $manufacturer_id,
					'post_author' => $user_id,
				)
			);
		}

		return true;
	}

	protected function generate_username_from_email( $email ) {
		$base = sanitize_user( current( explode( '@', (string) $email ) ), true );
		if ( '' === $base ) {
			$base = 'fabricante';
		}

		$username = $base;
		$suffix   = 2;

		while ( username_exists( $username ) ) {
			$username = $base . $suffix;
			$suffix++;
		}

		return $username;
	}

	public function is_manufacturer_user( $user ) {
		if ( ! $user instanceof WP_User ) {
			return false;
		}

		return (bool) $this->get_manufacturer_for_user( $user->ID );
	}

	public function placeholder_image_url() {
		$svg = <<<SVG
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 220">
    <rect width="320" height="220" rx="24" fill="#f3f6fb"/>
    <rect x="20" y="20" width="280" height="180" rx="20" fill="#eef3f9" stroke="#d5deea" stroke-width="2"/>
    <path d="M104 149l34-38 25 28 20-23 33 33" fill="none" stroke="#9bb0c9" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="130" cy="88" r="15" fill="#c8d4e4"/>
  </svg>
SVG;

		return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode( $svg );
	}

	public function editor_placeholder_image_url() {
		$svg = <<<SVG
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 220">
    <rect width="320" height="220" rx="24" fill="#f3f6fb"/>
    <rect x="20" y="20" width="280" height="180" rx="20" fill="#eef3f9" stroke="#c8d4e4" stroke-width="3" stroke-dasharray="10 10"/>
    <path d="M160 72c-18.5 0-33.5 15-33.5 33.5 0 2.7.3 5.4 1 7.9-12.3 2-21.5 12.8-21.5 25.7 0 14.4 11.6 26 26 26h55c15.7 0 28.5-12.8 28.5-28.5 0-14.3-10.7-26.3-24.8-28.2.2-1.5.3-3 .3-4.5 0-17.6-14.2-31.8-31.8-31.8Z" fill="#dbe5f1"/>
    <path d="M160 95v38" stroke="#58759d" stroke-width="10" stroke-linecap="round"/>
    <path d="m143 113 17-18 17 18" fill="none" stroke="#58759d" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
    <text x="160" y="178" text-anchor="middle" font-family="Inter, Arial, sans-serif" font-size="18" font-weight="700" fill="#5e7798">Selecione outra imagem</text>
    <text x="160" y="198" text-anchor="middle" font-family="Inter, Arial, sans-serif" font-size="12" fill="#7b90ab">Arraste aqui ou clique para enviar</text>
  </svg>
SVG;

		return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode( $svg );
	}

	protected function is_valid_phone( $phone ) {
		$digits = preg_replace( '/\D+/', '', (string) $phone );
		$length = strlen( $digits );

		return $length >= 10 && $length <= 13;
	}

	public function current_user_can_edit_manufacturer( $post_id ) {
		if ( $this->is_panel_authenticated() ) {
			return true;
		}

		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		$manufacturer = $this->get_authenticated_manufacturer();

		return $manufacturer instanceof WP_Post && (int) $manufacturer->ID === (int) $post->ID;
	}

	public function search_substances( $search, $limit = 6 ) {
		$local_results = $this->get_local_substance_library( $search, $limit );
		if ( ! empty( $local_results ) || '' === trim( (string) $search ) ) {
			return $local_results;
		}

		return array();
	}

	public function search_dictionary_substances( $search, $limit = 6 ) {
		if ( $search ) {
			return $this->search_posts_by_title_prefix( 'post', array( 'draft', 'publish' ), $search, $limit );
		}

		$args = array(
			'post_type'      => 'post',
			'post_status'    => array( 'draft', 'publish' ),
			'posts_per_page' => $limit,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'suppress_filters' => false,
		);

		if ( $search ) {
			$args['s'] = $search;
		}

		return get_posts( $args );
	}

	public function search_manufacturers( $search, $limit = 6 ) {
		if ( $search ) {
			$posts = $this->search_posts_by_title_prefix( 'fabricante', array( 'publish' ), $search, -1 );
		} else {
			$args = array(
				'post_type'           => 'fabricante',
				'post_status'         => 'publish',
				'posts_per_page'      => -1,
				'orderby'             => 'title',
				'order'               => 'ASC',
				'suppress_filters'    => false,
				'ignore_sticky_posts' => true,
			);

			$posts = get_posts( $args );
		}

		$filtered = array();
		$seen_titles = array();
		foreach ( $posts as $post ) {
			if ( ! $this->is_associated_manufacturer( $post->ID ) ) {
				continue;
			}

			$key = $this->normalize_lookup_value( $this->get_manufacturer_display_title( $post ) );
			if ( isset( $seen_titles[ $key ] ) ) {
				$current = $filtered[ $seen_titles[ $key ] ];
				if ( $this->get_manufacturer_preference_score( $post ) > $this->get_manufacturer_preference_score( $current ) ) {
					$filtered[ $seen_titles[ $key ] ] = $post;
				}
				continue;
			}

			$seen_titles[ $key ] = count( $filtered );
			$filtered[] = $post;
		}

		if ( $limit > 0 ) {
			return array_slice( $filtered, 0, $limit );
		}

		return $filtered;
	}

	protected function search_posts_by_title_prefix( $post_type, $statuses, $search, $limit ) {
		global $wpdb;

		$search   = trim( (string) $search );
		$limit    = max( 1, (int) $limit );
		$statuses = array_values( array_filter( array_map( 'sanitize_key', (array) $statuses ) ) );

		if ( '' === $search || empty( $statuses ) ) {
			return array();
		}

		$search_like     = '%' . $wpdb->esc_like( $search ) . '%';
		$prefix_like     = $wpdb->esc_like( $search ) . '%';
		$status_markers  = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
		$query_arguments = array_merge(
			array(
				$post_type,
			),
			$statuses,
			array(
				$search_like,
				$prefix_like,
				$limit,
			)
		);

		$sql = "
			SELECT ID
			FROM {$wpdb->posts}
			WHERE post_type = %s
				AND post_status IN ($status_markers)
				AND post_title LIKE %s
			ORDER BY
				CASE WHEN post_title LIKE %s THEN 0 ELSE 1 END,
				post_title ASC
			LIMIT %d
		";

		$post_ids = $wpdb->get_col( $wpdb->prepare( $sql, $query_arguments ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( empty( $post_ids ) ) {
			return array();
		}

		return array_values(
			array_filter(
				array_map( 'get_post', array_map( 'absint', $post_ids ) ),
				static function ( $post ) {
					return $post instanceof WP_Post;
				}
			)
		);
	}

	public function parse_dsf_post( $post ) {
		$content = (string) $post->post_content;
		$parsed  = array(
			'dcb'  => $post->post_title,
			'inn'  => '',
			'cas'  => '',
			'ncm'  => '',
		);

		if ( preg_match( '/<h2>(.*?)<\/h2>/i', $content, $matches ) ) {
			$parsed['dcb'] = wp_strip_all_tags( $matches[1] );
		}

		if ( preg_match( '/Nomenclatura\s+DCI\s*-\s*INN:\s*<strong>(.*?)<\/strong>/i', $content, $matches ) ) {
			$parsed['inn'] = wp_strip_all_tags( $matches[1] );
		}

		if ( preg_match( '/CAS(?:-determined CAS RN)?:\s*<strong>(.*?)<\/strong>/i', $content, $matches ) ) {
			$parsed['cas'] = wp_strip_all_tags( $matches[1] );
		}

		if ( preg_match( '/NCM:\s*<strong>(.*?)<\/strong>/i', $content, $matches ) ) {
			$parsed['ncm'] = wp_strip_all_tags( $matches[1] );
		}

		return $parsed;
	}

	public function format_substance_summary( $parsed ) {
		$parts = array();

		$dcb = isset( $parsed['dcb'] ) ? $this->clean_catalog_value( $parsed['dcb'] ) : '';
		$inn = isset( $parsed['inn'] ) ? $this->clean_catalog_value( $parsed['inn'] ) : '';
		$cas = isset( $parsed['cas'] ) ? $this->clean_catalog_value( $parsed['cas'] ) : '';
		$ncm = isset( $parsed['ncm'] ) ? $this->clean_catalog_value( $parsed['ncm'] ) : '';
		$cbpf = isset( $parsed['cbpf'] ) ? $this->clean_catalog_value( $parsed['cbpf'] ) : '';
		$validade = isset( $parsed['validade'] ) ? $this->clean_catalog_value( $parsed['validade'] ) : '';

		if ( '' !== $dcb ) {
			$parts[] = 'DCB: ' . $dcb;
		}

		if ( '' !== $inn ) {
			$parts[] = 'INN: ' . $inn;
		}

		if ( '' !== $cas ) {
			$parts[] = 'CAS: ' . $cas;
		}

		if ( '' !== $ncm ) {
			$parts[] = 'NCM: ' . $ncm;
		}

		if ( '' !== $cbpf ) {
			$parts[] = 'CBPF: ' . $cbpf;
		}

		if ( '' !== $validade ) {
			$parts[] = 'Validade: ' . $validade;
		}

		return implode( ' | ', $parts );
	}

	protected function get_manufacturer_field( $post_id, $field_name ) {
		if ( function_exists( 'get_field' ) ) {
			$value = get_field( $field_name, $post_id );
			if ( null !== $value && false !== $value ) {
				return $value;
			}
		}

		return get_post_meta( $post_id, $field_name, true );
	}

	protected function get_manufacturer_image_data( $post_id, $field_name ) {
		$value = get_post_meta( $post_id, $field_name, true );

		if ( '' === $value || null === $value ) {
			$value = $this->get_manufacturer_field( $post_id, $field_name );
		}

		if ( is_array( $value ) ) {
			if ( ! empty( $value['url'] ) ) {
				return $value;
			}

			if ( ! empty( $value['ID'] ) ) {
				$attachment_id = absint( $value['ID'] );
				$image_url     = wp_get_attachment_image_url( $attachment_id, 'full' );

				if ( $image_url ) {
					return array(
						'ID'  => $attachment_id,
						'url' => $image_url,
					);
				}
			}

			return array();
		}

		if ( is_numeric( $value ) ) {
			$attachment_id = absint( $value );
			$image_url     = wp_get_attachment_image_url( $attachment_id, 'full' );

			if ( $image_url ) {
				return array(
					'ID'  => $attachment_id,
					'url' => $image_url,
				);
			}
		}

		if ( is_string( $value ) && '' !== $value ) {
			return array(
				'url' => $value,
			);
		}

		return array();
	}

	protected function normalize_manufacturer_image_value( $value ) {
		if ( is_array( $value ) ) {
			if ( ! empty( $value['ID'] ) ) {
				return (int) $value['ID'];
			}

			if ( ! empty( $value['id'] ) ) {
				return (int) $value['id'];
			}

			if ( ! empty( $value['url'] ) ) {
				return esc_url_raw( $value['url'] );
			}
		}

		if ( is_numeric( $value ) ) {
			return (int) $value;
		}

		if ( is_string( $value ) && '' !== $value ) {
			return esc_url_raw( $value );
		}

		return 0;
	}

	protected function clear_manufacturer_image( $post_id, $field_key, $field_name ) {
		if ( function_exists( 'delete_field' ) ) {
			delete_field( $field_key, $post_id );
			delete_field( $field_name, $post_id );
		}

		delete_post_meta( $post_id, $field_name );
		delete_post_meta( $post_id, '_' . $field_name );
		clean_post_cache( $post_id );
	}

	protected function sync_manufacturer_images( $post_id ) {
		$hero_value = $this->normalize_manufacturer_image_value( get_post_meta( $post_id, 'fab_hero_image', true ) );

		if ( $hero_value ) {
			$this->update_manufacturer_field( $post_id, 'field_fab_logo', 'fab_logo', $hero_value );
			clean_post_cache( $post_id );
			return;
		}

		$this->clear_manufacturer_image( $post_id, 'field_fab_logo', 'fab_logo' );
		delete_post_thumbnail( $post_id );
		clean_post_cache( $post_id );
	}

	protected function update_manufacturer_field( $post_id, $field_key, $field_name, $value ) {
		if ( function_exists( 'update_field' ) ) {
			update_field( $field_key, $value, $post_id );
			return;
		}

		update_post_meta( $post_id, $field_name, $value );
	}

	protected function normalize_substance_submission_item( $item ) {
		if ( is_string( $item ) ) {
			$decoded = json_decode( $item, true );
			if ( is_array( $decoded ) ) {
				$item = $decoded;
			} else {
				$item = array(
					'display_name' => trim( $item ),
				);
			}
		}

		if ( ! is_array( $item ) ) {
			return null;
		}

		$title = '';
		foreach ( array( 'insumo', 'display_name', 'title', 'dcb', 'inn' ) as $candidate ) {
			if ( ! empty( $item[ $candidate ] ) ) {
				$title = $this->clean_catalog_value( $item[ $candidate ] );
				if ( '' !== $title ) {
					break;
				}
			}
		}

		if ( '' === $title ) {
			return null;
		}

		return array(
			'insumo'       => isset( $item['insumo'] ) ? $this->clean_catalog_value( $item['insumo'] ) : '',
			'dcb'          => isset( $item['dcb'] ) ? $this->clean_catalog_value( $item['dcb'] ) : '',
			'inn'          => isset( $item['inn'] ) ? $this->clean_catalog_value( $item['inn'] ) : '',
			'cas'          => isset( $item['cas'] ) ? $this->clean_catalog_value( $item['cas'] ) : '',
			'ncm'          => isset( $item['ncm'] ) ? $this->clean_catalog_value( $item['ncm'] ) : '',
			'cbpf'         => isset( $item['cbpf'] ) ? $this->clean_catalog_value( $item['cbpf'] ) : '',
			'validade'     => isset( $item['validade'] ) ? $this->clean_catalog_value( $item['validade'] ) : '',
			'display_name' => $title,
		);
	}

	protected function extract_substance_submission() {
		$payloads = isset( $_POST['fab_substance_payload'] ) ? (array) wp_unslash( $_POST['fab_substance_payload'] ) : array();
		$names    = isset( $_POST['fab_substance_names'] ) ? (array) wp_unslash( $_POST['fab_substance_names'] ) : array();
		$legacy   = isset( $_POST['fab_substances'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['fab_substances'] ) ) : array();
		$items    = array();

		foreach ( $payloads as $payload ) {
			$normalized = $this->normalize_substance_submission_item( $payload );
			if ( $normalized ) {
				$items[] = $normalized;
			}
		}

		foreach ( $names as $name ) {
			$normalized = $this->normalize_substance_submission_item( $name );
			if ( $normalized ) {
				$items[] = $normalized;
			}
		}

		$catalog_items = array();
		$compiled      = array();
		$matched_ids   = array();
		$seen          = array();

		foreach ( $items as $item ) {
			$key = $this->normalize_lookup_value( $item['display_name'] );
			if ( '' === $key || isset( $seen[ $key ] ) ) {
				continue;
			}

			$seen[ $key ]    = true;
			$catalog_items[] = $item;
			$compiled[]      = $item['display_name'];

			$matched = $this->find_substance_post_by_name( $item['display_name'] );
			if ( $matched instanceof WP_Post ) {
				$matched_ids[] = (int) $matched->ID;
			}
		}

		if ( empty( $catalog_items ) && ! empty( $legacy ) ) {
			$matched_ids = array_values( array_unique( array_filter( array_map( 'absint', $legacy ) ) ) );
			foreach ( $matched_ids as $matched_id ) {
				$post = get_post( $matched_id );
				if ( ! $post instanceof WP_Post ) {
					continue;
				}

				$meta = $this->parse_dsf_post( $post );
				$catalog_items[] = array(
					'insumo'       => '',
					'dcb'          => isset( $meta['dcb'] ) ? $meta['dcb'] : '',
					'inn'          => isset( $meta['inn'] ) ? $meta['inn'] : '',
					'cas'          => isset( $meta['cas'] ) ? $meta['cas'] : '',
					'ncm'          => isset( $meta['ncm'] ) ? $meta['ncm'] : '',
					'cbpf'         => '',
					'validade'     => '',
					'display_name' => $post->post_title,
				);
				$compiled[] = $post->post_title;
			}
		}

		return array(
			'catalog_items' => $catalog_items,
			'compiled'      => array_values( array_unique( array_filter( $compiled ) ) ),
			'matched_ids'   => array_values( array_unique( array_map( 'absint', $matched_ids ) ) ),
		);
	}

	protected function handle_manufacturer_image_update( $post_id, $field_key, $field_name, $file_key, $remove_key ) {
		$remove_image = ! empty( $_POST[ $remove_key ] );

		if ( $remove_image ) {
			$this->clear_manufacturer_image( $post_id, $field_key, $field_name );
			return true;
		}

		if ( empty( $_FILES[ $file_key ] ) || empty( $_FILES[ $file_key ]['name'] ) ) {
			return true;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = media_handle_upload( $file_key, $post_id );
		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		$this->update_manufacturer_field( $post_id, $field_key, $field_name, (int) $attachment_id );
		return true;
	}

	/**
	 * @return int[]
	 */
	protected function get_manufacturer_substance_ids( $post_id ) {
		$raw = $this->get_manufacturer_field( $post_id, 'fab_substances' );

		if ( empty( $raw ) ) {
			$raw = get_post_meta( $post_id, 'fab_substances', true );
		}

		return $this->normalize_id_list( $raw );
	}

	/**
	 * @param mixed $raw
	 * @return int[]
	 */
	protected function normalize_id_list( $raw ) {
		if ( is_string( $raw ) ) {
			$raw = maybe_unserialize( $raw );
		}

		if ( ! is_array( $raw ) ) {
			$raw = array( $raw );
		}

		return array_values( array_filter( array_map( 'absint', $raw ) ) );
	}
}
