<?php
/**
 * Add form Email action.
 *
 * @package JupiterX_Core\Raven
 * @since 1.0.0
 */

namespace JupiterX_Core\Raven\Modules\Forms\Actions;

use JupiterX_Core\Raven\Utils;

defined( 'ABSPATH' ) || die();

/**
 * Email Action.
 *
 * Initializing the email action by extending action base.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 *
 * @since 1.0.0
 */
class Email extends Action_Base {

	/**
	 * Get name.
	 *
	 * @since 1.19.0
	 * @access public
	 */
	public function get_name() {
		return 'email';
	}

	/**
	 * Get title.
	 *
	 * @since 1.19.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Email', 'jupiterx-core' );
	}

	/**
	 * Is private.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function is_private() {
		return false;
	}

	/**
	 * Get email action number.
	 * This number is only used to distinguish between "Email" and "Email 2" actions
	 *
	 * @since 2.5.0
	 * @access public
	 */
	protected function get_action_id() {
		return '';
	}

	/**
	 * Update controls.
	 *
	 * Add Email section.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $widget Widget instance.
	 */
	public function update_controls( $widget ) {

		$widget->start_controls_section(
			'section_email' . $this->get_action_id(),
			[
				'label' => $this->get_title(),
				'condition' => [
					'actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'email_to' . $this->get_action_id(),
			[
				'label' => __( 'To', 'jupiterx-core' ),
				'type' => 'text',
				'default' => get_bloginfo( 'admin_email' ),
				'placeholder' => get_bloginfo( 'admin_email' ),
				'title' => __( 'Separate emails with commas', 'jupiterx-core' ),
				'label_block' => true,
				'render_type' => 'ui',
			]
		);

		$widget->add_control(
			'email_subject' . $this->get_action_id(),
			[
				'label' => __( 'Email Subject', 'jupiterx-core' ),
				'type' => 'text',
				'default' => 'New message from "' . get_bloginfo( 'name' ) . '"',
				'placeholder' => 'New message from "' . get_bloginfo( 'name' ) . '"',
				'label_block' => true,
				'render_type' => 'ui',
			]
		);

		$widget->add_control(
			'email_content' . $this->get_action_id(),
			[
				'label' => esc_html__( 'Message', 'jupiterx-core' ),
				'type' => 'textarea',
				'default' => '[all-fields]',
				'placeholder' => '[all-fields]',
				'description' => sprintf(
					/* translators: %1$s: The [all-fields] shortcode, %2$s: The [all-fields without-label] shortcode, %3$s: The [field without-label] shortcode. */
					esc_html__( 'By default, all form fields are sent via %1$s shortcode. Use %2$s to send all fields without labels. To customize sent fields, copy the shortcode that appears inside each field and paste it above. You can also use %3$s for individual fields without labels.', 'jupiterx-core' ),
					'<code>[all-fields]</code>',
					'<code>[all-fields without-label]</code>',
					'<code>[field without-label id="field_id"]</code>'
				),
				'render_type' => 'none',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$widget->add_control(
			'email_from' . $this->get_action_id(),
			[
				'label' => __( 'From Email', 'jupiterx-core' ),
				'type' => 'text',
				'default' => 'email@' . Utils::get_site_domain(),
				'label_block' => true,
				'render_type' => 'ui',
			]
		);

		$widget->add_control(
			'email_name' . $this->get_action_id(),
			[
				'label' => __( 'From Name', 'jupiterx-core' ),
				'type' => 'text',
				'default' => get_bloginfo( 'name' ),
				'label_block' => true,
				'render_type' => 'ui',
			]
		);

		$widget->add_control(
			'email_reply_to_options' . $this->get_action_id(),
			[
				'label' => __( 'Reply-To', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'custom',
				'label_block' => true,
				'render_type' => 'ui',
				'options' => [],
			]
		);

		$widget->add_control(
			'email_reply_to' . $this->get_action_id(),
			[
				'type' => 'text',
				'default' => 'email@' . Utils::get_site_domain(),
				'render_type' => 'ui',
				'show_label' => false,
				'label_block' => true,
				'condition' => [
					'email_reply_to_options' => 'custom',
				],
			]
		);

		$widget->add_control(
			'email_cc' . $this->get_action_id(),
			[
				'label' => __( 'Cc', 'jupiterx-core' ),
				'type' => 'text',
				'title' => __( 'Separate emails with commas', 'jupiterx-core' ),
				'label_block' => true,
				'render_type' => 'ui',
			]
		);

		$widget->add_control(
			'email_bcc' . $this->get_action_id(),
			[
				'label' => __( 'Bcc', 'jupiterx-core' ),
				'type' => 'text',
				'title' => __( 'Separate emails with commas', 'jupiterx-core' ),
				'label_block' => true,
				'render_type' => 'ui',
			]
		);

		$widget->add_control(
			'confirmation' . $this->get_action_id(),
			[
				'label' => __( 'Confirmation', 'jupiterx-core' ),
				'type' => 'switcher',
				'description' => __( 'Send a copy of email to the one who submits the form.', 'jupiterx-core' ),
				'yes' => __( 'Yes', 'jupiterx-core' ),
				'no' => __( 'No', 'jupiterx-core' ),
			]
		);

		$widget->add_control(
			'form_metadata' . $this->get_action_id(),
			[
				'label' => __( 'Meta Data', 'jupiterx-core' ),
				'type' => 'select2',
				'multiple' => true,
				'label_block' => true,
				'separator' => 'before',
				'options' => [
					'date' => esc_html__( 'Date', 'jupiterx-core' ),
					'time' => esc_html__( 'Time', 'jupiterx-core' ),
					'page_url' => esc_html__( 'Page URL', 'jupiterx-core' ),
					'user_agent' => esc_html__( 'User Agent', 'jupiterx-core' ),
					'remote_ip' => esc_html__( 'Remote IP', 'jupiterx-core' ),
					'credit' => esc_html__( 'Credit', 'jupiterx-core' ),
				],
				'default' => [
					'date',
					'time',
					'page_url',
					'user_agent',
					'remote_ip',
					'credit',
				],
			]
		);

		$widget->add_control(
			'email_content_type' . $this->get_action_id(),
			[
				'label' => __( 'Send As', 'jupiterx-core' ),
				'type' => 'select',
				'default' => 'html',
				'options' => [
					'html' => esc_html__( 'HTML', 'jupiterx-core' ),
					'plain' => esc_html__( 'Plain', 'jupiterx-core' ),
				],
			]
		);

		$widget->end_controls_section();

	}

	/**
	 * Run action.
	 *
	 * Send email.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param object $ajax_handler Ajax handler instance.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public static function run( $ajax_handler ) {
		$form_settings = $ajax_handler->form['settings'];
		$action_id     = strpos( get_called_class(), 'Email2' ) ? '2' : '';

		$email_to               = $form_settings[ 'email_to' . $action_id ];
		$email_subject          = $form_settings[ 'email_subject' . $action_id ];
		$email_name             = $form_settings[ 'email_name' . $action_id ];
		$email_name             = str_replace( ',', ' ', $email_name );
		$email_from             = $form_settings[ 'email_from' . $action_id ];
		$email_reply_to_options = ! empty( $form_settings[ 'email_reply_to_options' . $action_id ] ) ? $form_settings[ 'email_reply_to_options' . $action_id ] : 'custom';
		$email_reply_to         = $form_settings[ 'email_reply_to' . $action_id ];
		$name_reply_to          = $email_name;
		$email_cc               = ! empty( $form_settings[ 'email_cc' . $action_id ] ) ? explode( ',', $form_settings[ 'email_cc' . $action_id ] ) : [];
		$email_bcc              = ! empty( $form_settings[ 'email_bcc' . $action_id ] ) ? explode( ',', $form_settings[ 'email_bcc' . $action_id ] ) : [];
		$confirmation           = ! empty( $form_settings[ 'confirmation' . $action_id ] ) ? $form_settings[ 'confirmation' . $action_id ] : false;
		$body                   = '';
		$content_type           = $form_settings[ 'email_content_type' . $action_id ];
		$email_content          = trim( $form_settings[ 'email_content' . $action_id ] );

		$line_break = 'html' === $content_type ? '<br>' : "\n";

		// Build email body based on content
		$body = self::build_email_body( $email_content, $form_settings['fields'], $ajax_handler->record['fields'], $content_type, $line_break );

		// Update reply-to name from form fields
		foreach ( $form_settings['fields'] as $field ) {
			if ( self::get_reply_to_name( $field, $ajax_handler->record['fields'][ $field['_id'] ] ?? '' ) && 'custom' !== $email_reply_to_options ) {
				$name_reply_to = self::get_reply_to_name( $field, $ajax_handler->record['fields'][ $field['_id'] ] );
			}
		}

		// Replace shortcodes in other email fields
		$email_fields = [ 'email_to', 'email_subject', 'email_name', 'email_from', 'email_reply_to', 'name_reply_to' ];
		foreach ( $email_fields as $field ) {
			${ $field } = trim( self::replace_setting_shortcodes( ${ $field }, $ajax_handler->record['fields'], $form_settings['fields'], $content_type, $line_break, false ) );
		}

		//Get form meta data and attatch to $body
		$form_meta = '';
		foreach ( (array) self::get_form_meta( $ajax_handler ) as $id => $meta ) {
			if ( ! empty( $meta['title'] ) && ! empty( $meta['value'] ) ) {
				$form_meta .= $meta['title'] . ': ' . $meta['value'] . $line_break;
			}
		}

		if ( ! empty( $form_meta ) ) {
			$body .= $line_break . '---' . $line_break . $line_break . $form_meta;
		}

		/**
		 * Filter for Email body.
		 *
		 * @param array $body Form Body Fields.
		 * @param array $form_settings Form Fields.
		 * @since 1.20.0
		 */
		$body = apply_filters( 'jupiterx_elements_form_email' . $action_id . '_body', $body, $form_settings, $ajax_handler->record['fields'] );

		/**
		 * Whether to strip the email body.
		 *
		 * @since 1.23.0
		 */
		$body_stripped = apply_filters( 'jupiterx_elements_form_email' . $action_id . '_body_stripped', true );

		if ( $body_stripped ) {
			$body = stripslashes( $body );
		}

		$headers[] = 'Content-Type: text/' . $content_type;
		$headers[] = 'charset=UTF-8';
		$headers[] = 'From: ' . $email_name . ' <' . $email_from . '>';

		if ( 'custom' !== $email_reply_to_options ) {
			$email_reply_to = $ajax_handler->record['fields'][ $email_reply_to_options ];
		}

		if ( ! empty( $email_reply_to ) ) {
			$headers[] = 'Reply-To: ' . $name_reply_to . '<' . $email_reply_to . '>';
		}

		if ( ! empty( $email_cc ) ) {
			foreach ( $email_cc as $email ) {
				$headers[] = 'Cc: ' . $email;
			}
		}

		if ( ! empty( $email_bcc ) ) {
			foreach ( $email_bcc as $email ) {
				$headers[] = 'Bcc: ' . $email;
			}
		}

		wp_mail( $email_to, $email_subject, $body, $headers );

		if ( 'yes' === $confirmation ) {
			self::send_confirmation_email( $ajax_handler, $email_name, $email_from, $body, $content_type );
		}

		$ajax_handler->add_response( 'success', 'Email sent.' );
	}

	/**
	 * Send confirmation email.
	 *
	 * @since 1.9.5
	 * @access private
	 * @static
	 *
	 * @param object $ajax_handler Ajax handler instance.
	 * @param string $email_name Email name.
	 * @param string $email_from Email from.
	 * @param string $body Email body.
	 */
	private static function send_confirmation_email( $ajax_handler, $email_name, $email_from, $body, $content_type ) {
		$headers[] = 'Content-Type: text/' . $content_type;
		$headers[] = 'charset=UTF-8';
		$headers[] = 'From: ' . $email_name . ' <' . $email_from . '>';

		// Email field.
		$email = array_filter( $ajax_handler->form['settings']['fields'], function( $field ) {
			return 'email' === $field['type'];
		} );

		// First email field.
		$email = reset( $email );

		// Email address.
		$email_to = $ajax_handler->record['fields'][ $email['_id'] ];

		wp_mail( $email_to, esc_html__( 'We received your email', 'jupiterx-core' ), $body, $headers );
	}

	/**
	 * Get reply-to name.
	 *
	 * @since 1.19.0
	 * @access private
	 * @static
	 *
	 * @param array $field field attributes Lists.
	 * @param string $value value of this field.
	 * @return string|boolean
	 */
	private static function get_reply_to_name( $field, $value ) {
		if ( empty( $field['label'] ) || strtolower( $field['label'] ) !== 'name' ) {
			return false;
		}

		if ( empty( $field['placeholder'] ) || strtolower( $field['placeholder'] ) !== 'name' ) {
			return false;
		}

		return $value;
	}

	/**
	 * Retrieve meta data of the form.
	 *
	 * @access private
	 *
	 * @param object $ajax_handler AJAX handler instance
	 * @return array meta data of form
	 */
	private static function get_form_meta( $ajax_handler ) {
		$action_id     = strpos( get_called_class(), 'Email2' ) ? '2' : '';
		$form_metadata = $ajax_handler->form['settings'][ 'form_metadata' . $action_id ];

		if ( empty( $form_metadata ) ) {
			return;
		}

		$result = [];

		$values = [
			'date' => date_i18n( get_option( 'date_format' ) ),
			'time' => date_i18n( get_option( 'time_format' ) ),
			'page_url' => isset( $_POST['referrer'] ) ? esc_url_raw( wp_unslash( $_POST['referrer'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.NoNonceVerification
			'page_title' => isset( $_POST['referer_title'] ) ? sanitize_text_field( wp_unslash( $_POST['referer_title'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.NoNonceVerification
			'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_textarea_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
			'remote_ip' => Utils::get_client_ip(),
			'credit' => __( 'JupiterX', 'jupiterx-core' ),
		];

		foreach ( $form_metadata as $meta_type ) {
			$result[ $meta_type ] = [
				'title' => ucfirst( $meta_type ),
				'value' => $values[ $meta_type ],
			];
		}

		return $result;
	}

	/**
	 * Build email body based on content settings.
	 *
	 * @access private
	 * @static
	 *
	 * @param string $email_content Email content setting.
	 * @param array $fields Form fields.
	 * @param array $values Form values.
	 * @param string $content_type Content type.
	 * @param string $line_break Line break.
	 * @return string
	 */
	private static function build_email_body( $email_content, $fields, $values, $content_type, $line_break ) {
		// If email content is empty, use default all fields with labels
		if ( empty( trim( $email_content ) ) ) {
			return self::generate_all_fields_content( $fields, $values, $content_type, $line_break, false );
		}

		// Process all shortcodes in the content
		$processed_content = self::process_all_fields_shortcodes( $email_content, $fields, $values, $content_type, $line_break );
		$processed_content = self::replace_setting_shortcodes( $processed_content, $values, $fields, $content_type, $line_break );

		return trim( $processed_content );
	}

	/**
	 * Process [all-fields] shortcodes in custom content.
	 *
	 * @access private
	 * @static
	 *
	 * @param string $content Content with shortcodes.
	 * @param array $fields Form fields.
	 * @param array $values Form values.
	 * @param string $content_type Content type.
	 * @param string $line_break Line break.
	 * @return string
	 */
	private static function process_all_fields_shortcodes( $content, $fields, $values, $content_type, $line_break ) {
		// Replace [all-fields without-label] variations first (more specific) - use regex for flexibility
		$content = preg_replace_callback( '/\[\s*all-fields\s+without-label?\s*\]/', function() use ( $fields, $values, $content_type, $line_break ) {
			return self::generate_all_fields_content( $fields, $values, $content_type, $line_break, true );
		}, $content );

		// Replace [all-fields] (less specific, so do this after the without-label variants) - use regex for flexibility
		$content = preg_replace_callback( '/\[\s*all-fields\s*\]/', function() use ( $fields, $values, $content_type, $line_break ) {
			return self::generate_all_fields_content( $fields, $values, $content_type, $line_break, false );
		}, $content );

		return $content;
	}

	/**
	 * Generate content for all form fields.
	 *
	 * @access private
	 * @static
	 *
	 * @param array $fields Form fields.
	 * @param array $values Form values.
	 * @param string $content_type Content type.
	 * @param string $line_break Line break.
	 * @param bool $without_label Whether to exclude labels.
	 * @return string
	 */
	private static function generate_all_fields_content( $fields, $values, $content_type, $line_break, $without_label = false ) {
		$body = '';

		foreach ( $fields as $field ) {
			// Skip non-input fields
			if ( ! isset( $field['type'] ) || in_array( $field['type'], [ 'html', 'password', 'recaptcha', 'recaptcha_v3' ], true ) ) {
				continue;
			}

			$field_content = self::get_field_content( $field, $values, $content_type );
			$field_label   = self::get_field_label( $field );

			// Skip fields that have no content and no label (completely empty)
			if ( empty( trim( $field_content ) ) && empty( trim( $field_label ) ) ) {
				continue;
			}

			// Skip empty fields when without-label is true
			if ( $without_label && empty( trim( $field_content ) ) ) {
				continue;
			}

			// Skip fields with labels but no content (prevents "Label:" with nothing after)
			if ( ! $without_label && empty( trim( $field_content ) ) ) {
				continue;
			}

			if ( $without_label ) {
				$body .= $field_content . $line_break;
			} else {
				// Only add colon if we have a label
				if ( ! empty( trim( $field_label ) ) ) {
					$body .= $field_label . ': ' . $field_content . $line_break;
				} else {
					$body .= $field_content . $line_break;
				}
			}
		}

		return $body;
	}

	/**
	 * Get field content value.
	 *
	 * @access private
	 * @static
	 *
	 * @param array $field Field configuration.
	 * @param array $values Form values.
	 * @param string $content_type Content type.
	 * @return string
	 */
	private static function get_field_content( $field, $values, $content_type ) {
		$content = $values[ $field['_id'] ] ?? '';

		// Handle file upload fields
		if ( 'upload' === $field['type'] ) {
			if ( ! empty( $content ) ) {
				// For file uploads, the content is usually a URL or file path
				if ( 'html' === $content_type ) {
					// Create a clickable link for HTML emails
					$content = '<a href="' . esc_url( $content ) . '">' . esc_html( basename( $content ) ) . '</a>';
				} else {
					// For plain text, just show the URL
					$content = $content;
				}
			} else {
				$content = __( 'No file uploaded', 'jupiterx-core' );
			}
		}

		// Handle textarea formatting for HTML content
		if ( 'textarea' === $field['type'] && 'html' === $content_type ) {
			$content = str_replace( [ "\r\n", "\n", "\r" ], '<br>', $content );
		}

		// Handle acceptance field
		if ( 'acceptance' === $field['type'] ) {
			$newsletter_key = isset( $values['register_acceptance'] ) ? 'register_acceptance' : $field['_id'];
			$newsletter     = $values[ $newsletter_key ] ?? '';
			$content        = 'on' === $newsletter ? __( 'Yes', 'jupiterx-core' ) : __( 'No', 'jupiterx-core' );
		}

		return $content;
	}

	/**
	 * Get field label.
	 *
	 * @access private
	 * @static
	 *
	 * @param array $field Field configuration.
	 * @return string
	 */
	private static function get_field_label( $field ) {
		$title = $field['label'] ?? '';

		// Special handling for newsletter field
		if ( 'newsletter' === ( $field['map_to'] ?? '' ) && 'acceptance' === $field['type'] ) {
			$title = empty( $title ) ? __( 'Newsletter', 'jupiterx-core' ) : $title;
		}

		return $title;
	}
}
