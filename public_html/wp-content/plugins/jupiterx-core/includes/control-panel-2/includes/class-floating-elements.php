<?php
defined( 'ABSPATH' ) || die();
/**
 * Floating Elements (Elementor e-floating-buttons) in the control panel.
 *
 * @package JupiterX_Core\Control_Panel_2\Floating_Elements
 */

use Elementor\Plugin;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class JupiterX_Core_Control_Panel_Floating_Elements {
	const POST_TYPE = 'e-floating-buttons';

	const DOCUMENT_TYPE = 'floating-buttons';

	/**
	 * Instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Get singleton.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_jupiterx_add_new_floating_element', [ $this, 'ajax_add_new' ] );
		add_action( 'wp_ajax_jupiterx_floating_elements', [ $this, 'handle_ajax' ] );
		add_action( 'wp_ajax_jupiterx_floating_elements_get_posts', [ $this, 'get_posts' ] );
	}

	/**
	 * Whether the post is an Elementor floating-buttons document.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	private function is_floating_document( $post_id ) {
		$type = get_post_meta( (int) $post_id, '_elementor_template_type', true );

		return self::DOCUMENT_TYPE === $type;
	}

	/**
	 * Labels for floating element types (Floating Buttons / Floating Bars).
	 *
	 * @return array<string, string>
	 */
	private function get_element_type_labels() {
		if ( class_exists( '\Elementor\Modules\FloatingButtons\Module' ) ) {
			return \Elementor\Modules\FloatingButtons\Module::get_floating_elements_types();
		}

		return [
			'floating-buttons' => __( 'Floating Buttons', 'elementor' ),
			'floating-bars'    => __( 'Floating Bars', 'elementor' ),
		];
	}

	/**
	 * Get floating element type meta for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function get_element_type_slug( $post_id ) {
		$key = class_exists( '\Elementor\Modules\FloatingButtons\Module' )
			? \Elementor\Modules\FloatingButtons\Module::FLOATING_ELEMENTS_TYPE_META_KEY
			: '_elementor_floating_elements_type';

		$slug = get_post_meta( $post_id, $key, true );

		return $slug ? (string) $slug : 'floating-buttons';
	}

	/**
	 * Ajax: list posts.
	 *
	 * @return void
	 */
	public function get_posts() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		if ( ! post_type_exists( self::POST_TYPE ) ) {
			wp_send_json_error( esc_html__( 'Floating Elements are not available.', 'jupiterx-core' ) );
		}

		$paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );
		$paged = $paged ? (int) $paged : 1;

		$filter_value = isset( $_GET['filter_value'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_value'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! in_array( $filter_value, [ 'all', 'publish', 'draft', 'trash' ], true ) ) {
			$filter_value = 'all';
		}

		switch ( $filter_value ) {
			case 'publish':
				$post_statuses = [ 'publish' ];
				break;
			case 'draft':
				$post_statuses = [ 'draft', 'private' ];
				break;
			case 'trash':
				$post_statuses = 'trash';
				break;
			default:
				$post_statuses = [ 'publish', 'draft', 'private' ];
				break;
		}

		$query = new \WP_Query(
			[
				'post_type'      => self::POST_TYPE,
				'post_status'    => $post_statuses,
				'paged'          => $paged,
				'posts_per_page' => 20,
				'meta_query'     => [
					[
						'key'   => '_elementor_template_type',
						'value' => self::DOCUMENT_TYPE,
					],
				],
			]
		);

		$trash_count_query = new \WP_Query(
			[
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'trash',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => false,
				'meta_query'     => [
					[
						'key'   => '_elementor_template_type',
						'value' => self::DOCUMENT_TYPE,
					],
				],
			]
		);

		$trash_total = (int) $trash_count_query->found_posts;
		$posts       = $query->posts;

		$click_meta_key = class_exists( '\Elementor\Modules\FloatingButtons\Module' )
			? \Elementor\Modules\FloatingButtons\Module::META_CLICK_TRACKING
			: '_elementor_click_tracking';

		$type_labels = $this->get_element_type_labels();
		$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

		foreach ( $posts as $post ) {
			$document = Plugin::$instance->documents->get( $post->ID );

			if ( $document ) {
				$post->edit_url = $document->get_edit_url();
			} else {
				$post->edit_url = add_query_arg(
					[
						'post'   => $post->ID,
						'action' => 'elementor',
					],
					admin_url( 'post.php' )
				);
			}

			$preview_url = get_preview_post_link( $post );
			$preview_url = $preview_url ? $preview_url : get_permalink( $post->ID );
			$post->preview_url = add_query_arg(
				[ 'preview-id' => $post->ID ],
				$preview_url
			);

			$post->user_url = get_edit_user_link( (int) get_post_field( 'post_author', $post->ID ) );
			$post->author_name = get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post->ID ) );

			$post->custom_date = get_the_date( 'M d, Y', $post->ID );
			$post->custom_modified_date = get_the_modified_date( 'M d, Y', $post->ID );
			$post->custom_date_with_time = mysql2date( $date_format, $post->post_date );
			$post->custom_modified_date_with_time = mysql2date( $date_format, $post->post_modified );

			$type_slug = $this->get_element_type_slug( $post->ID );
			$post->element_type_slug = $type_slug;
			$post->element_type_label = isset( $type_labels[ $type_slug ] )
				? $type_labels[ $type_slug ]
				: $this->humanize_slug( $type_slug );

			$clicks = get_post_meta( $post->ID, $click_meta_key, true );
			$post->click_tracking_display = ( '' !== $clicks && null !== $clicks ) ? (string) $clicks : '';

			$conditions = get_post_meta( $post->ID, '_elementor_conditions', true );
			$post->instances_label = $this->get_instances_label( $conditions );

			if ( class_exists( '\Elementor\Modules\FloatingButtons\Documents\Floating_Buttons' ) ) {
				$fe_type = \Elementor\Modules\FloatingButtons\Documents\Floating_Buttons::get_floating_element_type( $post->ID );
				$set_id  = (int) \Elementor\Modules\FloatingButtons\Documents\Floating_Buttons::get_set_as_entire_site_post_id( $fe_type );
				$post->is_entire_site = ( $set_id === (int) $post->ID );
			} else {
				$post->is_entire_site = false;
			}

			$post->can_entire_site_actions = ( 'publish' === $post->post_status );
		}

		wp_send_json_success(
			[
				'posts'         => $posts,
				'max_num_pages' => (int) $query->max_num_pages,
				'counts'        => [
					'trash' => $trash_total,
				],
			]
		);
	}

	/**
	 * Human-readable "Instances" column (aligned with Elementor list table on free).
	 *
	 * @param mixed $conditions Elementor conditions meta.
	 * @return string
	 */
	private function get_instances_label( $conditions ) {
		if ( ! empty( $conditions ) ) {
			return __( 'Entire Site', 'elementor' );
		}

		return '—';
	}

	/**
	 * @param string $slug Meta slug.
	 * @return string
	 */
	private function humanize_slug( $slug ) {
		$slug = str_replace( [ '_', '-' ], ' ', $slug );

		return ucwords( trim( $slug ) );
	}

	/**
	 * Handle sub_action ajax.
	 *
	 * @return void
	 */
	public function handle_ajax() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		$action = filter_input( INPUT_POST, 'sub_action', FILTER_UNSAFE_RAW );

		if ( ! empty( $action ) && method_exists( $this, $action ) ) {
			call_user_func( [ $this, $action ] );
		}
	}

	/**
	 * Create draft and return Elementor edit URL.
	 *
	 * @return void
	 */
	public function ajax_add_new() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		if ( ! post_type_exists( self::POST_TYPE ) || ! class_exists( 'Elementor\Plugin' ) ) {
			wp_send_json_error( esc_html__( 'Floating Elements are not available.', 'jupiterx-core' ) );
		}

		$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$element_type = isset( $_POST['element_type'] ) ? sanitize_key( wp_unslash( $_POST['element_type'] ) ) : 'floating-buttons'; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$allowed_types = [ 'floating-buttons', 'floating-bars' ];

		if ( ! in_array( $element_type, $allowed_types, true ) ) {
			$element_type = 'floating-buttons';
		}

		$meta_key = class_exists( '\Elementor\Modules\FloatingButtons\Module' )
			? \Elementor\Modules\FloatingButtons\Module::FLOATING_ELEMENTS_TYPE_META_KEY
			: '_elementor_floating_elements_type';

		$args = [
			'post_type'   => self::POST_TYPE,
			'post_title'  => $title ? $title : esc_html__( 'Floating Element', 'jupiterx-core' ),
			'post_status' => 'draft',
			'meta_input'  => [
				'_elementor_template_type' => self::DOCUMENT_TYPE,
				'_elementor_edit_mode'     => 'builder',
				$meta_key                  => $element_type,
			],
		];

		$post_id = wp_insert_post( $args, true );

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( $post_id->get_error_message() );
		}

		$document = Plugin::$instance->documents->get( $post_id );

		if ( ! $document ) {
			wp_send_json_error( esc_html__( 'Could not load the document.', 'jupiterx-core' ) );
		}

		wp_send_json_success(
			[
				'url' => $document->get_edit_url(),
			]
		);
	}

	/**
	 * Trash post.
	 *
	 * @return void
	 */
	public function remove_post() {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		if ( empty( $post_id ) ) {
			wp_send_json_error();
		}

		$post = get_post( $post_id );

		if ( ! $post || self::POST_TYPE !== $post->post_type || ! $this->is_floating_document( $post_id ) || 'trash' === $post->post_status ) {
			wp_send_json_error();
		}

		if ( ! current_user_can( 'delete_post', $post_id ) ) {
			wp_send_json_error();
		}

		if ( ! wp_trash_post( $post_id ) ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}

	/**
	 * Delete permanently.
	 *
	 * @return void
	 */
	public function delete_post_permanently() {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		if ( empty( $post_id ) ) {
			wp_send_json_error();
		}

		$post = get_post( $post_id );

		if ( ! $post || self::POST_TYPE !== $post->post_type || ! $this->is_floating_document( $post_id ) || 'trash' !== $post->post_status ) {
			wp_send_json_error();
		}

		if ( ! current_user_can( 'delete_post', $post_id ) ) {
			wp_send_json_error();
		}

		if ( ! wp_delete_post( $post_id, true ) ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}

	/**
	 * Restore from trash.
	 *
	 * @return void
	 */
	public function restore_post() {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		if ( empty( $post_id ) ) {
			wp_send_json_error();
		}

		$post = get_post( $post_id );

		if ( ! $post || self::POST_TYPE !== $post->post_type || ! $this->is_floating_document( $post_id ) || 'trash' !== $post->post_status ) {
			wp_send_json_error();
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error();
		}

		if ( ! wp_untrash_post( $post_id ) ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}

	/**
	 * Rename title.
	 *
	 * @return void
	 */
	public function rename_post() {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$title   = filter_input( INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( empty( $post_id ) ) {
			wp_send_json_error();
		}

		$post = get_post( $post_id );

		if ( ! $post || self::POST_TYPE !== $post->post_type || ! $this->is_floating_document( $post_id ) ) {
			wp_send_json_error();
		}

		$result = wp_update_post(
			[
				'ID'         => $post_id,
				'post_title' => $title,
			],
			true
		);

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_messages() );
		}

		wp_send_json_success();
	}

	/**
	 * Set as entire site (Elementor Conversion / floating elements behavior).
	 *
	 * @return void
	 */
	public function set_as_entire_site() {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		if ( empty( $post_id ) ) {
			wp_send_json_error( esc_html__( 'Invalid post.', 'jupiterx-core' ) );
		}

		$post = get_post( $post_id );

		if ( ! $post || self::POST_TYPE !== $post->post_type || ! $this->is_floating_document( $post_id ) ) {
			wp_send_json_error( esc_html__( 'Invalid floating element.', 'jupiterx-core' ) );
		}

		if ( 'publish' !== $post->post_status ) {
			wp_send_json_error( esc_html__( 'Only published items can be set as entire site.', 'jupiterx-core' ) );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( esc_html__( 'You cannot edit this item.', 'jupiterx-core' ) );
		}

		if ( ! $this->elementor_entire_site_dependencies_available() ) {
			wp_send_json_error( esc_html__( 'Elementor Floating Elements module is not available.', 'jupiterx-core' ) );
		}

		$this->apply_set_as_entire_site( (int) $post_id );

		wp_send_json_success();
	}

	/**
	 * Remove entire-site display for this floating element.
	 *
	 * @return void
	 */
	public function remove_from_entire_site() {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		if ( empty( $post_id ) ) {
			wp_send_json_error( esc_html__( 'Invalid post.', 'jupiterx-core' ) );
		}

		$post = get_post( $post_id );

		if ( ! $post || self::POST_TYPE !== $post->post_type || ! $this->is_floating_document( $post_id ) ) {
			wp_send_json_error( esc_html__( 'Invalid floating element.', 'jupiterx-core' ) );
		}

		if ( 'publish' !== $post->post_status ) {
			wp_send_json_error( esc_html__( 'Invalid item.', 'jupiterx-core' ) );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( esc_html__( 'You cannot edit this item.', 'jupiterx-core' ) );
		}

		if ( ! $this->elementor_entire_site_dependencies_available() ) {
			wp_send_json_error( esc_html__( 'Elementor Floating Elements module is not available.', 'jupiterx-core' ) );
		}

		$fb = '\Elementor\Modules\FloatingButtons\Documents\Floating_Buttons';
		$type = $fb::get_floating_element_type( $post_id );

		if ( (int) $fb::get_set_as_entire_site_post_id( $type ) !== (int) $post_id ) {
			wp_send_json_error( esc_html__( 'This item is not set as entire site.', 'jupiterx-core' ) );
		}

		$cache = new \Elementor\Modules\FloatingButtons\Classes\Conditions\Conditions_Cache();
		delete_post_meta( $post_id, '_elementor_conditions' );
		$cache->remove_from_cache( (int) $post_id );

		wp_send_json_success();
	}

	/**
	 * @return bool
	 */
	private function elementor_entire_site_dependencies_available() {
		return class_exists( '\Elementor\Modules\FloatingButtons\Documents\Floating_Buttons' )
			&& class_exists( '\Elementor\Modules\FloatingButtons\Classes\Conditions\Conditions_Cache' )
			&& class_exists( '\Elementor\Modules\FloatingButtons\Module' );
	}

	/**
	 * Published floating elements of the same variant (buttons vs bars) that have conditions meta.
	 *
	 * @param int $post_id Reference post.
	 * @return int[]
	 */
	private function get_published_floating_element_ids_same_type( $post_id ) {
		$fb = '\Elementor\Modules\FloatingButtons\Documents\Floating_Buttons';

		$type = $fb::get_floating_element_type( $post_id );

		return get_posts(
			[
				'post_type'              => \Elementor\Modules\FloatingButtons\Module::CPT_FLOATING_BUTTONS,
				'posts_per_page'         => -1,
				'post_status'            => 'publish',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'meta_query'             => $fb::get_meta_query_for_floating_buttons( $type ),
			]
		);
	}

	/**
	 * Core logic aligned with Elementor Action_Handler::handle_set_as_entire_site().
	 *
	 * @param int $post_id Post to set as entire site.
	 * @return void
	 */
	private function apply_set_as_entire_site( $post_id ) {
		$cache = new \Elementor\Modules\FloatingButtons\Classes\Conditions\Conditions_Cache();
		$ids   = $this->get_published_floating_element_ids_same_type( $post_id );

		foreach ( $ids as $post_id_to_clear ) {
			delete_post_meta( $post_id_to_clear, '_elementor_conditions' );
			$cache->remove_from_cache( (int) $post_id_to_clear );
		}

		update_post_meta( $post_id, '_elementor_conditions', [ 'include/general' ] );
		$cache->add_to_cache( (int) $post_id );
	}
}

JupiterX_Core_Control_Panel_Floating_Elements::get_instance();
