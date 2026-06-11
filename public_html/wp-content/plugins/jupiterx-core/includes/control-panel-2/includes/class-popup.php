<?php
/**
 * Handles popup functionality in control panel.
 *
 * @package JupiterX_Core\Control_Panel_2\Popup
 *
 * @since 3.7.0
 */
use Elementor\Plugin;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class JupiterX_Core_Control_Panel_Popup {
	private static $instance = null;

	/**
	 * Instance of class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_jupiterx_add_new_popup', [ $this, 'ajax_handler' ] );
		add_action( 'wp_ajax_jupiterx_get_import_form', [ $this, 'import_form' ] );
		add_action( 'admin_action_import_popup_action', [ $this, 'import_popup_templates' ] );
		add_action( 'wp_ajax_enable_unfiltered_files_upload', [ $this, 'enable_unfiltered_files_upload' ] );

		// Conditions ajax requests.
		add_action( 'wp_ajax_jupiterx_popup_get_options', [ $this, 'get_options' ] );
		add_action( 'wp_ajax_jupiterx_popup_save_conditions_triggers', [ $this, 'save_conditions_triggers' ] );
		add_action( 'wp_ajax_jupiterx_popup_get_conditions', [ $this, 'get_popup_conditions' ] );
		add_action( 'wp_ajax_jupiterx_popup_get_triggers', [ $this, 'get_popup_triggers' ] );
	}

	/**
	 * Handle unfiltered files upload.
	 *
	 * @return void
	 * @since 3.7.0
	 */
	public function enable_unfiltered_files_upload() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		update_option( 'elementor_unfiltered_files_upload', 1 );

		wp_send_json_success();
	}

	/**
	 * Handle ajax requests.
	 *
	 * @return void
	 * @since 3.7.0
	 */
	public function ajax_handler() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		$title = ! empty( $_POST[ 'title' ] ) ? htmlspecialchars( $_POST[ 'title' ] ) : ''; //phpcs:ignore

		$args = [
			'post_type'   => 'jupiterx-popups',
			'post_title'  => $title,
			'post_status' => 'draft',
			'meta_input'  => [
				'_elementor_template_type' => 'jupiterx-popups',
				'_elementor_edit_mode' => 'builder',
			],
		];

		$post_id    = wp_insert_post( $args );
		$editor_url = Plugin::$instance->documents->get( $post_id )->get_edit_url();

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( $post_id->get_error_message() );
		}

		wp_send_json_success( [
			'url' => $editor_url,
		] );
	}

	/**
	 * Get import template form.
	 *
	 * @return void
	 * @since 3.7.0
	 */
	public function import_form() {
		$action = add_query_arg(
			[
				'action' => 'import_popup_action',
			],
			esc_url( admin_url( 'admin.php' ) )
		);

		?>
		<div id="jupiterx-import-template-area">
				<div id="jupiterx-import-template-title"><?php echo esc_html__( 'Choose an Elementor template JSON file or a .zip archive of Elementor templates, and add them to the list of templates available in your library.', 'jupiterx-core' ); ?></div>
				<form id="jupiterx-import-template-form" method="post" action="<?php echo esc_url( $action ); ?>" enctype="multipart/form-data">
					<fieldset id="jupiterx-import-template-form-inputs">
						<input type="file" name="file" accept=".json,application/json,.zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed" required>
						<input id="jupiterx-import-template-action" type="submit" class="button" value="<?php echo esc_attr__( 'Import Now', 'jupiterx-core' ); ?>">
					</fieldset>
				</form>
			</div>
		<?php

		$content = ob_get_clean();

		wp_send_json_success( [
			'content' => $content,
		] );
	}

	/**
	 * Import popup template base on file type.
	 *
	 * @return void
	 * @since 3.7.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function import_popup_templates() {
		$file = filter_var_array( $_FILES, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $file ) ) {
			wp_die( esc_html__( 'Empty file.', 'jupiterx-core' ) );
		}

		$file = $file['file'];

		if ( 'application/zip' !== $file['type'] && 'application/json' !== $file['type'] ) {
			wp_die( esc_html__( 'Format not allowed', 'jupiterx-core' ) );
		}

		$path = $file['tmp_name'];

		$templates = [];

		if ( 'application/zip' === $file['type'] ) {
			$extracted_files = Plugin::instance()->uploads_manager->extract_and_validate_zip( $path, [ 'json' ] );

			if ( is_wp_error( $extracted_files ) ) {
				Plugin::instance()->uploads_manager->remove_file_or_dir( $extracted_files['extraction_directory'] );

				return $extracted_files;
			}

			foreach ( $extracted_files['files'] as $file_path ) {
				$import_result = $this->import_popup_template( $file_path );

				if ( is_wp_error( $import_result ) ) {
					Plugin::instance()->uploads_manager->remove_file_or_dir( $extracted_files['extraction_directory'] );

					return $import_result;
				}

				$templates[] = $import_result;
			}

			Plugin::instance()->uploads_manager->remove_file_or_dir( $extracted_files['extraction_directory'] );
		}

		if ( 'application/json' === $file['type'] ) {
			$templates = $this->import_popup_template( $path );
		}

		if ( ! empty( $templates ) && is_array( $templates ) ) {
			$popups = add_query_arg(
				[
					'post_type' => 'jupiterx-popups',
				],
				admin_url( 'edit.php' )
			);

			wp_safe_redirect( $popups );

			die();
		}

		if ( ! empty( $templates ) && ! is_array( $templates ) ) {
			$edit = add_query_arg(
				[
					'post' => $templates,
					'action' => 'elementor',
				],
				admin_url( 'post.php' )
			);

			wp_safe_redirect( $edit );

			die();
		}
	}

	/**
	 * Import popup template functionality.
	 *
	 * @param string $file template file path.
	 * @return void
	 * @since 3.7.0
	 */
	private function import_popup_template( $file ) {
		$content = file_get_contents( $file ); // phpcs:ignore
		$content = json_decode( $content, true );

		if ( ! $content ) {
			wp_die( esc_html__( 'No data found in file', 'jupiterx-core' ) );
		}

		$documents = Plugin::instance()->documents;
		$doc_type  = $documents->get_document_type( 'jupiterx-popups' );

		$popup_content    = $content['content'];
		$popup_conditions = ! empty( $content['popup_conditions'] ) ? $content['popup_conditions'] : [];
		$popup_triggers   = ! empty( $content['popup_triggers'] ) ? $content['popup_triggers'] : [];
		$popup_settings   = ! empty( $content['page_settings'] ) ? $content['page_settings'] : [];
		$popup_content    = $this->get_imported_template_content( $popup_content );

		$post_data = [
			'post_type'  => 'jupiterx-popups',
			'meta_input' => [
				'_elementor_edit_mode'     => 'builder',
				$doc_type::TYPE_META_KEY   => 'jupiterx-popups',
				'_elementor_data'          => wp_slash( wp_json_encode( $popup_content ) ),
				'_elementor_page_settings' => $popup_settings,
				'_jupiterx_popup_conditions' => $popup_conditions,
				'_jupiterx_popup_triggers' => $popup_triggers,
			],
		];

		$post_data['post_title'] = ! empty( $content['title'] ) ? $content['title'] : esc_html__( 'New Popup', 'jupiterx-core' );

		$popup_id = wp_insert_post( $post_data );

		if ( ! $popup_id ) {
			wp_die( esc_html__( 'Can\'t create popup.', 'jupiterx-core' ) );
		}

		return $popup_id;
	}

	/**
	 * Get import content.
	 *
	 * @param array $content template content.
	 * @return array|null
	 * @since 3.7.0
	 */
	private function get_imported_template_content( $content ) {
		$import = 'on_import';

		$data = Plugin::$instance->db->iterate_data(
			$content, function( $element_data ) use ( $import ) {
				$element = Plugin::$instance->elements_manager->create_element_instance( $element_data );

				if ( ! $element ) {
					return null;
				}

				$element_data = $element->get_data();

				if ( method_exists( $element, $import ) ) {
					$element_data = $element->{$import}( $element_data );
				}

				foreach ( $element->get_controls() as $control ) {
					$control_class = Plugin::$instance->controls_manager->get_control( $control['type'] );

					if ( ! $control_class ) {
						return $element_data;
					}

					if ( method_exists( $control_class, $import ) ) {
						$element_data['settings'][ $control['name'] ] = $control_class->{$import}( $element->get_settings( $control['name'] ), $control );
					}
				}

				return $element_data;
			}
		);

		return $data;
	}

	/**
	 * Get conditions options list.
	 *
	 * @return void
	 * @since 3.7.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function get_options() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		$type = ! empty( $_GET[ 'type' ] ) ? htmlspecialchars( $_GET[ 'type' ] ) : ''; //phpcs:ignore

		$condition_name = ! empty( $_GET[ 'condition_name' ] ) ? htmlspecialchars( $_GET[ 'condition_name' ] ) : ''; //phpcs:ignore
		$search_value   = ! empty( $_GET[ 'search_value' ] ) ? htmlspecialchars( $_GET[ 'search_value' ] ) : ''; //phpcs:ignore

		if ( empty( $condition_name ) || ( 'condition' === $type && empty( $search_value ) ) ) {
			wp_send_json_error( esc_html__( 'There is something wrong while passing data.', 'jupiterx-core' ) );
		}

		$options = [];

		if ( class_exists( 'JupiterX_Popups_Conditions_Manager' ) && 'condition' === $type ) {
			$options = call_user_func( [ JupiterX_Popups_Conditions_Manager::$conditions[ $condition_name ], 'get_options' ], $search_value );
		}

		if ( class_exists( 'JupiterX_Popups_Triggers_Manager' ) && 'trigger' === $type ) {
			$options = call_user_func( [ JupiterX_Popups_Triggers_Manager::$triggers[ $condition_name ], 'get_options' ], $search_value );
		}

		wp_send_json_success( $options );
	}

	/**
	 * Save triggers and conditions.
	 *
	 * @return void
	 * @since 3.7.0
	 */
	public function save_conditions_triggers() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		$popup_id   = ! empty( $_POST[ 'popup_id' ] ) ? (int) htmlspecialchars( $_POST[ 'popup_id' ] ) : ''; //phpcs:ignore
		$conditions = filter_input( INPUT_POST, 'conditions', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$triggers   = filter_input( INPUT_POST, 'triggers', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $popup_id ) ) {
			return;
		}

		update_post_meta( $popup_id, '_jupiterx_popup_conditions', $conditions );
		update_post_meta( $popup_id, '_jupiterx_popup_triggers', $triggers );

		wp_send_json_success();
	}

	/**
	 * Get current popup conditions
	 *
	 * @return void
	 * @since 3.7.0
	 */
	public function get_popup_conditions() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		$popup_id = ! empty( $_POST[ 'popup_id' ] ) ? (int) htmlspecialchars( $_POST[ 'popup_id' ] ) : ''; //phpcs:ignore

		$conditions = get_post_meta( $popup_id, '_jupiterx_popup_conditions', true );

		wp_send_json_success( $conditions );
	}

	/**
	 * Get current popup triggers.
	 *
	 * @return void
	 * @since 3.7.0
	 */
	public function get_popup_triggers() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		$popup_id = ! empty( $_POST[ 'popup_id' ] ) ? (int) htmlspecialchars( $_POST[ 'popup_id' ] ) : ''; //phpcs:ignore

		$conditions = get_post_meta( $popup_id, '_jupiterx_popup_triggers', true );

		wp_send_json_success( $conditions );
	}
}

JupiterX_Core_Control_Panel_Popup::get_instance();
