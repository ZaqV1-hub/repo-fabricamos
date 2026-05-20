<?php
/**
 * Add WPML Compatibility Module.
 *
 * @package JupiterX_Core\Raven
 * @since 1.0.4
 */

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml;

defined( 'ABSPATH' ) || die();

/**
 * Raven WPML compatibility module.
 *
 * Raven compatibility module handler class is responsible for registering and
 * managing translatable fields with WPML plugin.
 *
 * @since 1.0.4
 */
class Module {

	/**
	 * Constructor.
	 *
	 * @since 1.0.4
	 */
	public function __construct() {
		add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'register_widgets_fields' ] );
	}

	/**
	 * Register widgets fields for translation.
	 *
	 * @since 1.0.4
	 *
	 * @param array $fields Fields to translate.
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function register_widgets_fields( $fields ) {

		// Alert.
		$fields['raven-alert'] = [
			'conditions' => [ 'widgetType' => 'raven-alert' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Raven Alert: Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description',
					'type'        => esc_html__( 'Raven Alert: Content', 'jupiterx-core' ),
					'editor_type' => 'VISUAL',
				],
			],
		];

		// Button.
		$fields['raven-button'] = [
			'conditions' => [ 'widgetType' => 'raven-button' ],
			'fields'     => [
				[
					'field'       => 'text',
					'type'        => esc_html__( 'Raven Button: Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'subtext',
					'type'        => esc_html__( 'Raven Button: Subtext', 'jupiterx-core' ),
					'editor_type' => 'AREA',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Button: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Counter.
		$fields['raven-counter'] = [
			'conditions' => [ 'widgetType' => 'raven-counter' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Counter',
		];

		// Form.
		$fields['raven-form'] = [
			'conditions' => [ 'widgetType' => 'raven-form' ],
			'fields'     => [
				[
					'field'       => 'form_name',
					'type'        => esc_html__( 'Raven Form: Form Name', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'submit_button_text',
					'type'        => esc_html__( 'Raven Form: Submit Button Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'messages_success',
					'type'        => esc_html__( 'Raven Form: Success Message', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'messages_error',
					'type'        => esc_html__( 'Raven Form: Error Message', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'messages_required',
					'type'        => esc_html__( 'Raven Form: Required Message', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'messages_subscriber',
					'type'        => esc_html__( 'Raven Form: Subscriber Already Exists Message', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\Modules\Form',
		];

		// Login Form.
		$fields['raven-login'] = [
			'conditions' => [ 'widgetType' => 'raven-login' ],
			'fields'     => [
				'redirect_to' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Login Form: Redirect After Login URL', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
				'logout_redirect_to' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Login Form: Redirect After Logout URL', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'forget_password_text',
					'type'        => esc_html__( 'Raven Login Form: Forget Password Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				'forget_password_link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Login Form: Forget Password Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'submit_button_text',
					'type'        => esc_html__( 'Raven Login Form: Submit Button Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'success_message',
					'type'        => esc_html__( 'Raven Login Form: Success Message', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'error_message',
					'type'        => esc_html__( 'Raven Login Form: Error Message', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\Modules\Login_Form',
		];

		// Register Form.
		$fields['raven-register'] = [
			'conditions' => [ 'widgetType' => 'raven-register' ],
			'fields'     => [
				[
					'field'       => 'form_name',
					'type'        => esc_html__( 'Raven Register: Form Name', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'submit_button_text',
					'type'        => esc_html__( 'Raven Register: Submit Button Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'custom_message_success',
					'type'        => esc_html__( 'Raven Register: Success Message', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'custom_message_email_exist',
					'type'        => esc_html__( 'Raven Register: Email Exists', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'custom_message_error_not_same_password',
					'type'        => esc_html__( 'Raven Register: Not Same Password Message', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\Modules\Register_Form',
		];

		// Reset Password Form.
		$fields['raven-reset-password'] = [
			'conditions' => [ 'widgetType' => 'raven-reset-password' ],
			'fields'     => [
				[
					'field'       => 'submit_button_text',
					'type'        => esc_html__( 'Raven Reset Password: Submit Button Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'messages_success',
					'type'        => esc_html__( 'Raven Reset Password: Success Message', 'jupiterx-core' ),
					'editor_type' => 'AREA',
				],
			],
			'integration-class' => __NAMESPACE__ . '\Modules\Reset_Password_Form',
		];

		// Social Login Form.
		$fields['raven-social-login'] = [
			'conditions' => [ 'widgetType' => 'raven-social-login' ],
			'fields'     => [
				[
					'field'       => 'google_label',
					'type'        => esc_html__( 'Raven Social Login: Google Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'facebook_label',
					'type'        => esc_html__( 'Raven Social Login: Facebook Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'twitter_label',
					'type'        => esc_html__( 'Raven Social Login: Twitter Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				'redirect_url' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Social Login: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Heading.
		$fields['raven-heading'] = [
			'conditions' => [ 'widgetType' => 'raven-heading' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Raven Heading: Title', 'jupiterx-core' ),
					'editor_type' => 'AREA',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Heading: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Icon.
		$fields['raven-icon'] = [
			'conditions' => [ 'widgetType' => 'raven-icon' ],
			'fields'     => [
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Icon: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Image.
		$fields['raven-image'] = [
			'conditions' => [ 'widgetType' => 'raven-image' ],
			'fields'     => [
				[
					'field'       => 'caption',
					'type'        => esc_html__( 'Raven Image: Caption', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Image: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Photo Album.
		$fields['raven-photo-album'] = [
			'conditions' => [ 'widgetType' => 'raven-photo-album' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Photo_Album',
		];

		// Search Form.
		$fields['raven-search-form'] = [
			'conditions' => [ 'widgetType' => 'raven-search-form' ],
			'fields'     => [
				[
					'field'       => 'placeholder',
					'type'        => esc_html__( 'Raven Search Form: Placeholder', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Site Logo.
		$fields['raven-site-logo'] = [
			'conditions' => [ 'widgetType' => 'raven-site-logo' ],
			'fields'     => [
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Site Logo: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Tabs.
		$fields['raven-tabs'] = [
			'conditions' => [ 'widgetType' => 'raven-tabs' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Tabs',
		];

		// Video.
		$fields['raven-video'] = [
			'conditions' => [ 'widgetType' => 'raven-video' ],
			'fields'     => [
				[
					'field'       => 'youtube_link',
					'type'        => esc_html__( 'Raven Advanced Video: YouTube link', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'vimeo_link',
					'type'        => esc_html__( 'Raven Advanced Video: Vimeo link', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Video Playlist.
		$fields['raven-video-playlist'] = [
			'conditions' => [ 'widgetType' => 'raven-video-playlist' ],
			'fields'     => [
				[
					'field'       => 'playlist_title',
					'type'        => esc_html__( 'Raven Video Playlist: Playlist Name', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'inner_tab_title_1',
					'type'        => esc_html__( 'Raven Video Playlist: Tab 1 Name', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'inner_tab_title_2',
					'type'        => esc_html__( 'Raven Video Playlist: Tab 2 Name', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'inner_tab_label_show_more',
					'type'        => esc_html__( 'Raven Video Playlist: Read More Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'inner_tab_label_show_less',
					'type'        => esc_html__( 'Raven Video Playlist: Read Less Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\Modules\Video_Playlist',
		];

		// Advanced Accordion.
		$fields['raven-advanced-accordion'] = [
			'conditions' => [ 'widgetType' => 'raven-advanced-accordion' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Advanced_Accordion',
		];

		// Advanced Menu.
		$fields['raven-advanced-nav-menu'] = [
			'conditions' => [ 'widgetType' => 'raven-advanced-nav-menu' ],
			'fields'     => [
				'center_logo_link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Advanced Menu: Center Logo Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
				'side_logo_link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Advanced Menu: Side Logo Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
			'integration-class' => __NAMESPACE__ . '\Modules\Advanced_Menu',
		];

		// Animated Heading.
		$fields['raven-animated-heading'] = [
			'conditions' => [ 'widgetType' => 'raven-animated-heading' ],
			'fields'     => [
				[
					'field'       => 'before_text',
					'type'        => esc_html__( 'Raven Animated Heading: Before Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'highlighted_text',
					'type'        => esc_html__( 'Raven Animated Heading: Highlighted Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'rotating_text',
					'type'        => esc_html__( 'Raven Animated Heading: Rotating Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'after_text',
					'type'        => esc_html__( 'Raven Animated Heading: After Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Animated Heading: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Author Box.
		$fields['raven-author-box'] = [
			'conditions' => [ 'widgetType' => 'raven-author-box' ],
			'fields'     => [
				[
					'field'       => 'link_text',
					'type'        => esc_html__( 'Raven Author Box: Archive Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Business Hours.
		$fields['raven-business-hours'] = [
			'conditions' => [ 'widgetType' => 'raven-business-hours' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Business_Hours',
		];

		// Call to Action.
		$fields['raven-call-to-action'] = [
			'conditions' => [ 'widgetType' => 'raven-call-to-action' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Raven Call to Action: Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description',
					'type'        => esc_html__( 'Raven Call to Action: Description', 'jupiterx-core' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button',
					'type'        => esc_html__( 'Raven Call to Action: Button Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'ribbon_title',
					'type'        => esc_html__( 'Raven Call to Action: Ribbon Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Call to Action: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Media Carousel.
		$fields['raven-media-carousel'] = [
			'conditions' => [ 'widgetType' => 'raven-media-carousel' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Media_Carousel',
		];

		// Reviews.
		$fields['raven-reviews'] = [
			'conditions' => [ 'widgetType' => 'raven-reviews' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Reviews',
		];

		// Testimonial Carousel.
		$fields['raven-testimonial-carousel'] = [
			'conditions' => [ 'widgetType' => 'raven-testimonial-carousel' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Testimonial_Carousel',
		];

		// Cart.
		$fields['raven-cart'] = [
			'conditions' => [ 'widgetType' => 'raven-cart' ],
			'fields'     => [
				[
					'field'       => 'apply_coupon_button_text',
					'type'        => esc_html__( 'Raven Cart: Apply Coupon Button', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'update_cart_button_text',
					'type'        => esc_html__( 'Raven Cart: Update Cart Button', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'update_shipping_button_text',
					'type'        => esc_html__( 'Raven Cart: Update Shipping Button', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'totals_section_title',
					'type'        => esc_html__( 'Raven Cart: Cart Total Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'checkout_button_text',
					'type'        => esc_html__( 'Raven Cart: Checkout Button', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Circle Progress.
		$fields['raven-circle-progress'] = [
			'conditions' => [ 'widgetType' => 'raven-circle-progress' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Raven Circle Progress: Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'subtitle',
					'type'        => esc_html__( 'Raven Circle Progress: Subtitle', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'prefix',
					'type'        => esc_html__( 'Raven Circle Progress: Number Prefix', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'suffix',
					'type'        => esc_html__( 'Raven Circle Progress: Number Suffix', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Content Switch.
		$fields['raven-content-switch'] = [
			'conditions' => [ 'widgetType' => 'raven-content-switch' ],
			'fields'     => [
				[
					'field'       => 'primary_section_label',
					'type'        => esc_html__( 'Raven Content Switch: Primary Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'primary_text_content',
					'type'        => esc_html__( 'Raven Content Switch: Primary Content', 'jupiterx-core' ),
					'editor_type' => 'VISUAL',
				],
				[
					'field'       => 'secondary_section_label',
					'type'        => esc_html__( 'Raven Content Switch: Primary Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'secondary_text_content',
					'type'        => esc_html__( 'Raven Content Switch: Primary Content', 'jupiterx-core' ),
					'editor_type' => 'VISUAL',
				],
			],
		];

		// Countdown.
		$fields['raven-countdown'] = [
			'conditions' => [ 'widgetType' => 'raven-countdown' ],
			'fields'     => [
				[
					'field'       => 'days_label',
					'type'        => esc_html__( 'Raven Countdown: Days Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'hours_label',
					'type'        => esc_html__( 'Raven Countdown: Hours Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'minutes_label',
					'type'        => esc_html__( 'Raven Countdown: Minutes Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'seconds_label',
					'type'        => esc_html__( 'Raven Countdown: Seconds Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Flip Box.
		$fields['raven-flip-box'] = [
			'conditions' => [ 'widgetType' => 'raven-flip-box' ],
			'fields'     => [
				[
					'field'       => 'title_text_front',
					'type'        => esc_html__( 'Raven Flip Box: Front Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text_front',
					'type'        => esc_html__( 'Raven Flip Box: Front Title', 'jupiterx-core' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'title_text_back',
					'type'        => esc_html__( 'Raven Flip Box: Back Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text_back',
					'type'        => esc_html__( 'Raven Flip Box: Back Title', 'jupiterx-core' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Raven Flip Box: Back Button Text', 'jupiterx-core' ),
					'editor_type' => 'AREA',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Flip Box: Back Button Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Hotspot.
		$fields['raven-hotspot'] = [
			'conditions' => [ 'widgetType' => 'raven-hotspot' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Hotspot',
		];

		// Image Accordion.
		$fields['raven-image-accordion'] = [
			'conditions' => [ 'widgetType' => 'raven-image-accordion' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Image_Accordion',
		];

		// Image Comparison.
		$fields['raven-image-comparison'] = [
			'conditions' => [ 'widgetType' => 'raven-image-comparison' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Image_Comparison',
		];

		// Inline SVG.
		$fields['raven-inline-svg'] = [
			'conditions' => [ 'widgetType' => 'raven-inline-svg' ],
			'fields'     => [
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Inline SVG: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Lottie.
		$fields['raven-lottie'] = [
			'conditions' => [ 'widgetType' => 'raven-lottie' ],
			'fields'     => [
				[
					'field'       => 'caption',
					'type'        => esc_html__( 'Raven Lottie: Custom Caption Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				'custom_link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Lottie: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Content Marquee.
		$fields['raven-content-marquee'] = [
			'conditions' => [ 'widgetType' => 'raven-content-marquee' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Content_Marquee',
		];

		// Testimonial Marquee.
		$fields['raven-testimonial-marquee'] = [
			'conditions' => [ 'widgetType' => 'raven-testimonial-marquee' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Testimonial_Marquee',
		];

		// Text Marquee.
		$fields['raven-text-marquee'] = [
			'conditions' => [ 'widgetType' => 'raven-text-marquee' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Text_Marquee',
		];

		// Media Gallery.
		$fields['raven-media-gallery'] = [
			'conditions' => [ 'widgetType' => 'raven-media-gallery' ],
			'fields'     => [
				[
					'field'       => 'all_filter_label',
					'type'        => esc_html__( 'Raven Media Gallery: All Filter Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\Modules\Media_Gallery',
		];

		// My Account.
		$fields['raven-my-account'] = [
			'conditions' => [ 'widgetType' => 'raven-my-account' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\My_Account',
		];

		// Navigation Menu.
		$fields['raven-nav-menu'] = [
			'conditions' => [ 'widgetType' => 'raven-nav-menu' ],
			'fields'     => [
				'logo_link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Navigation Menu: Logo Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
				'side_logo_link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Navigation Menu: Side Logo Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
			'integration-class' => __NAMESPACE__ . '\Modules\Navigation_Menu',
		];

		// PayPal Button.
		$fields['raven-paypal-button'] = [
			'conditions' => [ 'widgetType' => 'raven-paypal-button' ],
			'fields'     => [
				[
					'field'       => 'product_name',
					'type'        => esc_html__( 'Raven PayPal Button: Item Name', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'product_sku',
					'type'        => esc_html__( 'Raven PayPal Button: SKU', 'jupiterx-core' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'text',
					'type'        => esc_html__( 'Raven PayPal Button: Button Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				'redirect_after_success' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven PayPal Button: Redirect URL', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'error_message_global',
					'type'        => esc_html__( 'Raven PayPal Button: Error Message', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'error_message_payment',
					'type'        => esc_html__( 'Raven PayPal Button: PayPal Not Connected', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Stripe Button.
		$fields['raven-stripe-button'] = [
			'conditions' => [ 'widgetType' => 'raven-stripe-button' ],
			'fields'     => [
				[
					'field'       => 'product_name',
					'type'        => esc_html__( 'Raven Stripe Button: Product Name', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'text',
					'type'        => esc_html__( 'Raven Stripe Button: Button Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				'redirect_after_success' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Stripe Button: Redirect URL', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'error_message_global',
					'type'        => esc_html__( 'Raven Stripe Button: Error Message', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'error_message_payment',
					'type'        => esc_html__( 'Raven Stripe Button: Stripe Not Connected', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Post Meta.
		$fields['raven-post-meta'] = [
			'conditions' => [ 'widgetType' => 'raven-post-meta' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Post_Meta',
		];

		// Post Navigation.
		$fields['raven-post-navigation'] = [
			'conditions' => [ 'widgetType' => 'raven-post-navigation' ],
			'fields'     => [
				[
					'field'       => 'prev_label',
					'type'        => esc_html__( 'Raven Post Navigation: Previous Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'next_label',
					'type'        => esc_html__( 'Raven Post Navigation: Next Label', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Post Terms.
		$fields['raven-post-terms'] = [
			'conditions' => [ 'widgetType' => 'raven-post-terms' ],
			'fields'     => [
				[
					'field'       => 'text_before',
					'type'        => esc_html__( 'Raven Post Terms: Before Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Post Title.
		$fields['raven-post-title'] = [
			'conditions' => [ 'widgetType' => 'raven-post-title' ],
			'fields'     => [
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Post Title: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Price List.
		$fields['raven-price-list'] = [
			'conditions' => [ 'widgetType' => 'raven-price-list' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Price_List',
		];

		// Pricing Table.
		$fields['raven-pricing-table'] = [
			'conditions' => [ 'widgetType' => 'raven-pricing-table' ],
			'fields'     => [
				[
					'field'       => 'heading',
					'type'        => esc_html__( 'Raven Pricing Table: Header Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sub_heading',
					'type'        => esc_html__( 'Raven Pricing Table: Header Description', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'price',
					'type'        => esc_html__( 'Raven Pricing Table: Price', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'original_price',
					'type'        => esc_html__( 'Raven Pricing Table: Original Price', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'period',
					'type'        => esc_html__( 'Raven Pricing Table: Period', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Raven Pricing Table: Button Text', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Pricing Table: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'footer_additional_info',
					'type'        => esc_html__( 'Raven Pricing Table: Additional Info', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'ribbon_title',
					'type'        => esc_html__( 'Raven Pricing Table: Ribbon Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\Modules\Pricing_Table',
		];

		// Product Meta.
		$fields['raven-product-meta'] = [
			'conditions' => [ 'widgetType' => 'raven-product-meta' ],
			'fields'     => [
				[
					'field'       => 'category_caption_single',
					'type'        => esc_html__( 'Raven Product Meta: Category Singular Caption', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'category_caption_plural',
					'type'        => esc_html__( 'Raven Product Meta: Category Plural Caption', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'tag_caption_single',
					'type'        => esc_html__( 'Raven Product Meta: Tag Singular Caption', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'tag_caption_plural',
					'type'        => esc_html__( 'Raven Product Meta: Tag Plural Caption', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sku_caption',
					'type'        => esc_html__( 'Raven Product Meta: SKU Caption', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sku_missing_caption',
					'type'        => esc_html__( 'Raven Product Meta: SKU Missing Caption', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Products.
		$fields['raven-wc-products'] = [
			'conditions' => [ 'widgetType' => 'raven-wc-products' ],
			'fields'     => [
				[
					'field'       => 'widget_title',
					'type'        => esc_html__( 'Raven Products: Widget Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Products.
		$fields['raven-wc-products'] = [
			'conditions' => [ 'widgetType' => 'raven-wc-products' ],
			'fields'     => [
				[
					'field'       => 'widget_title',
					'type'        => esc_html__( 'Raven Products: Widget Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Products Carousel.
		$fields['raven-products-carousel'] = [
			'conditions' => [ 'widgetType' => 'raven-products-carousel' ],
			'fields'     => [
				[
					'field'       => 'widget_title',
					'type'        => esc_html__( 'Raven Products Carousel: Widget Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Search Form.
		$fields['raven-search-form'] = [
			'conditions' => [ 'widgetType' => 'raven-search-form' ],
			'fields'     => [
				[
					'field'       => 'placeholder',
					'type'        => esc_html__( 'Raven Search Form: Placeholder', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Shopping Cart.
		$fields['raven-shopping-cart'] = [
			'conditions' => [ 'widgetType' => 'raven-shopping-cart' ],
			'fields'     => [
				[
					'field'       => 'view_cart_button_text',
					'type'        => esc_html__( 'Raven Shopping Cart: View Cart Button', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'quick_view_heading',
					'type'        => esc_html__( 'Raven Shopping Cart: Quick View Heading', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Site Logo.
		$fields['raven-site-logo'] = [
			'conditions' => [ 'widgetType' => 'raven-site-logo' ],
			'fields'     => [
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Site Logo: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Site Title.
		$fields['raven-site-title'] = [
			'conditions' => [ 'widgetType' => 'raven-site-title' ],
			'fields'     => [
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Raven Site Title: Link', 'jupiterx-core' ),
					'editor_type' => 'LINK',
				],
			],
		];

		// Slider.
		$fields['raven-slider'] = [
			'conditions' => [ 'widgetType' => 'raven-slider' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Slider',
		];

		// Social Share.
		$fields['raven-social-share'] = [
			'conditions' => [ 'widgetType' => 'raven-social-share' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Social_Share',
		];

		// Sticky Media Scroller.
		$fields['raven-sticky-media-scroller'] = [
			'conditions' => [ 'widgetType' => 'raven-sticky-media-scroller' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Sticky_Media_Scroller',
		];

		// Table of Contents.
		$fields['raven-table-of-contents'] = [
			'conditions' => [ 'widgetType' => 'raven-table-of-contents' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Raven Table of Contents: Title', 'jupiterx-core' ),
					'editor_type' => 'LINE',
				],
			],
		];

		// Team Members.
		$fields['raven-team-members'] = [
			'conditions' => [ 'widgetType' => 'raven-team-members' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\Modules\Team_Members',
		];

		return $fields;
	}
}
