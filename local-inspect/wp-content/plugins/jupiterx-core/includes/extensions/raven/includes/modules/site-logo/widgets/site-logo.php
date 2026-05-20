<?php
namespace JupiterX_Core\Raven\Modules\Site_Logo\Widgets;

use Elementor\Utils;
use JupiterX_Core\Raven\Base\Base_Widget;
use Elementor\Core\Responsive\Responsive;
use Elementor\Plugin as Elementor;
use Elementor\Modules\DynamicTags\Module as TagsModule;

defined( 'ABSPATH' ) || die();

/**
 * Site Logo Widget
 *
 * Displays the site's logo with support for responsive images and custom logos.
 *
 * Feature Migration: Dynamic Tag → Toggle
 * ----------------------------------------
 * This widget underwent a major UX improvement to replace the legacy dynamic tag
 * approach with a dedicated "Use Site's Logo" toggle.
 *
 * LEGACY IMPLEMENTATION (before this change):
 * - Users selected a '[site-logo]' dynamic tag from the image control dropdown
 * - The tag was stored in __dynamic__['image'] and resolved at render time
 * - Not obvious to users; buried in dynamic tags UI
 *
 * NEW IMPLEMENTATION (current):
 * - Dedicated 'use_site_logo' switcher control (yes/no toggle)
 * - Clear, prominent UI in the widget settings panel
 * - When enabled, renders the site logo from 'custom_logo' theme mod
 * - When disabled, user provides their own custom image
 *
 * BACKWARD COMPATIBILITY STRATEGY:
 * ---------------------------------
 * Widgets created before this change still have the legacy dynamic tag. To ensure
 * these widgets continue to work AND migrate seamlessly to the new approach:
 *
 * 1. PHP Render Side (this file):
 *    - has_legacy_site_logo_dynamic_tag() detects old dynamic tag in __dynamic__
 *    - should_use_site_logo() checks for legacy tag before custom images
 *    - Widgets with legacy tag automatically render site logo (no manual migration needed)
 *
 * 2. Editor Side (site-logo.js):
 *    - Migration handler runs when user opens widget in editor
 *    - Detects legacy dynamic tag → enables 'use_site_logo' toggle
 *    - Removes legacy tag from __dynamic__ (cleanup)
 *    - User sees toggle enabled, can turn it off if desired
 *    - On save, new toggle value persists (legacy tag is gone)
 *
 * 3. Timeline:
 *    - Widget created with legacy tag → still renders correctly (PHP handles it)
 *    - User opens widget in editor → JS migrates to toggle (shows as enabled)
 *    - User saves page → new toggle value saved, legacy tag removed
 *    - Future edits use toggle; legacy tag is gone
 *
 * This approach ensures:
 * - Zero breaking changes (old widgets keep working)
 * - Seamless migration (happens automatically when editor opens)
 * - Clean data (legacy tags removed after migration)
 * - Better UX (toggle is clearer than dynamic tag)
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @suppresswarnings()
 */
class Site_Logo extends Base_Widget {

	public function get_name() {
		return 'raven-site-logo';
	}

	public function get_title() {
		return __( 'Site Logo', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-site-logo';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Logo', 'jupiterx-core' ),
			]
		);

		if ( ! jupiterx_core()->check_default_settings() ) {
			$this->add_control(
				'logo_source',
				[
					'label' => __( 'Logo Source', 'jupiterx-core' ),
					'type' => 'select',
					'options' => [
						'customizer' => __( 'Customizer', 'jupiterx-core' ),
						'custom-source' => __( 'Custom Logo', 'jupiterx-core' ),
					],
					'default' => 'customizer',
					'dynamic' => [
						'active' => true,
					],
				]
			);
		} else {
			$this->add_control(
				'logo_source',
				[
					'type' => 'hidden',
					'default' => 'custom-source',
				]
			);
		}

		$this->add_control(
			'important_note',
			[
				'type' => 'raw_html',
				'raw' => sprintf(
					/* translators: %1$s: Choose logo name | %2$s: Link to Customizer page */
					__( 'Please select or upload your <strong>Logo</strong> in the <a target="_blank" href="%1$s"><em>Customizer</em></a>.', 'jupiterx-core' ),
					add_query_arg( [ 'autofocus[section]' => 'jupiterx_logo' ],
					admin_url( 'customize.php' ) )
				),
				'content_classes' => 'elementor-control-field-description',
				'condition' => [
					'logo_source' => 'customizer',
				],
			]
		);

		$this->add_responsive_control(
			'logo',
			[
				'label' => __( 'Choose Logo', 'jupiterx-core' ),
				'type' => 'select',
				'options' => [
					'primary'   => __( 'Primary', 'jupiterx-core' ),
					'secondary' => __( 'Secondary', 'jupiterx-core' ),
					'sticky'    => __( 'Sticky', 'jupiterx-core' ),
					'mobile'    => __( 'Mobile', 'jupiterx-core' ),
				],
				'default' => 'primary',
				'tablet_default' => 'primary',
				'mobile_default' => 'primary',
				'condition' => [
					'logo_source' => 'customizer',
				],
			]
		);

		// NEW: "Use Site's Logo" toggle (replaces legacy dynamic tag approach).
		// This is the primary control for enabling site logo rendering.
		$this->add_control(
			'use_site_logo',
			[
				'label' => __( 'Use Site\'s Logo', 'jupiterx-core' ),
				'type' => 'switcher',
				'return_value' => 'yes',
				'default' => '', // Empty by default; legacy widgets rely on migration logic.
				'separator' => 'before',
				'condition' => [
					'logo_source' => 'custom-source',
				],
			]
		);

		// Image control for custom logo uploads.
		// Dynamic tags remain enabled for backward compatibility with legacy widgets,
		// but we've removed the 'jupiterx_site_logo_tag' category to discourage new
		// users from using the old dynamic tag approach.
		$this->add_responsive_control(
			'image',
			[
				'label' => __( 'Choose Image', 'jupiterx-core' ),
				'type' => 'media',
				'condition' => [
					'logo_source' => 'custom-source',
					'use_site_logo!' => 'yes', // Hide when "Use Site's Logo" is enabled.
				],
				// Dynamic tags kept enabled for backward compatibility:
				// - Legacy widgets may still have __dynamic__ data stored
				// - Elementor core image category remains for other dynamic tags
				// - 'jupiterx_site_logo_tag' category removed to hide legacy site-logo tag
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::IMAGE_CATEGORY, // Elementor core image tags only.
					],
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'jupiterx-core' ),
				'type' => 'url',
				'placeholder' => __( 'Enter your web address', 'jupiterx-core' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => false,
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_logo',
			[
				'label' => __( 'Logo', 'jupiterx-core' ),
				'tab' => 'style',
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => __( 'Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-site-logo img, {{WRAPPER}} .raven-site-logo svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'max_width',
			[
				'label' => __( 'Max Width', 'jupiterx-core' ),
				'type' => 'slider',
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-site-logo img, {{WRAPPER}} .raven-site-logo svg' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'  => __( 'Alignment', 'jupiterx-core' ),
				'type' => 'choose',
				'default' => is_rtl() ? 'right' : 'left',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'jupiterx-core' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .raven-site-logo' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Temporary suppressed.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$link = $settings['link'];

		$wrapper_class = 'raven-site-logo';

		if ( 'customizer' === $settings['logo_source'] ) {
			$wrapper_class .= ' raven-site-logo-customizer';
		}

		$this->add_render_attribute(
			'logo-wrapper',
			'class',
			$wrapper_class
		);

		if ( ! isset( $link['url'] ) || empty( $link['url'] ) ) {
			$link['url'] = get_bloginfo( 'url' );
		}

		if ( ! empty( $link['url'] ) ) {
			$this->add_render_attribute( 'link', 'class', 'raven-site-logo-link' );

			$this->add_render_attribute( 'link', 'href', esc_url( $link['url'] ) );

			if ( ! empty( $link['is_external'] ) ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}

			if ( ! empty( $link['nofollow'] ) ) {
				$this->add_render_attribute( 'link', 'rel', 'nofollow' );
			}
		}
		// Custom Logo Source
		$source_control = $settings['logo_source'];
		?>
		<div class="raven-widget-wrapper">
			<div <?php echo $this->get_render_attribute_string( 'logo-wrapper' ); ?>>
				<?php if ( ! empty( $link['url'] ) ) : ?>
					<a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
				<?php endif; ?>
				<?php

					if ( 'custom-source' === $source_control ) {
						$this->custom_logo_render();
					}

					if ( 'customizer' === $source_control ) {
						$this->customizer_logo_render();
					}

				?>
				<?php if ( ! empty( $link['url'] ) ) : ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders custom logo with responsive breakpoints support.
	 *
	 * This method handles the rendering of custom logos for the widget. It checks whether
	 * the site logo should be used (either via the new toggle or legacy dynamic tag) before
	 * rendering custom images.
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	protected function custom_logo_render() {
		if ( Elementor::$instance->experiments->is_feature_active( 'additional_custom_breakpoints' ) ) {
			return $this->custom_logo_custom_breakpoints_render();
		}

		$settings     = $this->get_settings_for_display();
		$settings_raw = $this->get_settings();

		if ( $this->should_use_site_logo( $settings, $settings_raw ) ) {
			return $this->site_logo_render();
		}

		$custom_logo_default = ! empty( $settings['image']['url'] ) ? $settings['image']['url'] : '';
		$custom_logo_mobile  = ! empty( $settings['image_mobile']['url'] ) ? $settings['image_mobile']['url'] : '';
		$custom_logo_tablet  = ! empty( $settings['image_tablet']['url'] ) ? $settings['image_tablet']['url'] : '';
		$breakpoints         = Responsive::get_breakpoints();
		$picture_content     = '';

		$devices = [
			'desktop' => '',
			'tablet'  => '_tablet',
			'mobile'  => '_mobile',
		];

		foreach ( $devices as $device => $device_setting_key ) {
			$this->add_render_attribute( 'custom-logo', 'class', 'raven-site-logo-' . $device );
		}

		$this->add_render_attribute( 'custom-logo', [
			'alt'   => get_bloginfo( 'title' ),
			'data-no-lazy' => 1,
		] );

		if ( empty( $custom_logo_default ) ) {
			$custom_logo_default = Utils::get_placeholder_image_src();
		}

		$picture_content .= '<picture>';

		if ( ! empty( $custom_logo_mobile ) ) {
			$picture_content .= "<source media='(max-width:{$breakpoints['md']}px)' srcset=' $custom_logo_mobile '>";
		}

		if ( ! empty( $custom_logo_tablet ) ) {
			$picture_content .= "<source media='(max-width:{$breakpoints['lg']}px)' srcset=' $custom_logo_tablet '>";
		}

		$picture_content .= "<img {$this->get_render_attribute_string( 'custom-logo' )} src=' $custom_logo_default '>";
		$picture_content .= '</picture>';

		Utils::print_unescaped_internal_string( $picture_content );
	}

	/**
	 * Renders custom logo with Elementor's custom breakpoints experiment enabled.
	 *
	 * Similar to custom_logo_render() but uses Elementor's custom breakpoints API
	 * for more flexible responsive behavior.
	 */
	protected function custom_logo_custom_breakpoints_render() {
		$settings     = $this->get_settings_for_display();
		$settings_raw = $this->get_settings();

		if ( $this->should_use_site_logo( $settings, $settings_raw ) ) {
			return $this->site_logo_render();
		}

		// WPML compatibility.
		if ( isset( $settings['image']['id'] ) ) {
			$settings['image']['id'] = apply_filters( 'wpml_object_id', $settings['image']['id'], 'attachment', true );

			if ( ! empty( $settings['image']['id'] ) ) {
				$attachment_url = wp_get_attachment_image_src( $settings['image']['id'], 'full' );

				if ( $attachment_url ) {
					$settings['image']['url'] = $attachment_url[0];
				}
			}
		}

		$custom_logo_default = ! empty( $settings['image']['url'] ) ? $settings['image']['url'] : '';
		$picture_content     = '';

		$this->add_render_attribute( 'custom-logo', [
			'alt' => get_bloginfo( 'title' ),
			'data-no-lazy' => 1,
		] );

		if ( empty( $custom_logo_default ) ) {
			$custom_logo_default = Utils::get_placeholder_image_src();
		}

		$picture_content .= '<picture>';

		foreach ( Elementor::$instance->breakpoints->get_active_breakpoints() as $breakpoint ) {
			$breakpoint_name = $breakpoint->get_name();

			if ( empty( $settings[ "image_{$breakpoint_name}" ]['url'] ) ) {
				continue;
			}

			$image_url = $settings[ "image_{$breakpoint_name}" ]['url'];

			$picture_content .= "<source media='({$breakpoint->get_direction()}-width:{$breakpoint->get_value()}px)' srcset=' $image_url '>";
		}

		$picture_content .= "<img {$this->get_render_attribute_string( 'custom-logo' )} src=' $custom_logo_default '>";
		$picture_content .= '</picture>';

		Utils::print_unescaped_internal_string( $picture_content );
	}

	/**
	 * Determines whether to render the site logo or a custom image.
	 *
	 * Migration Strategy:
	 * -------------------
	 * Prior to this feature, widgets used a dynamic tag '[site-logo]' in the image control
	 * to display the site logo. This has been replaced with a dedicated 'use_site_logo' toggle.
	 *
	 * For backward compatibility with existing widgets:
	 * 1. If the new toggle is explicitly 'yes', use site logo
	 * 2. If the new toggle is explicitly 'no', don't use site logo
	 * 3. If toggle is unset, check for legacy dynamic tag (auto-migration on render)
	 * 4. If no legacy tag and no custom image, don't use site logo
	 *
	 * Note: The order matters! We check for legacy tags BEFORE checking custom images,
	 * because Elementor resolves dynamic tags to URLs. A widget with the legacy dynamic tag
	 * will have $settings['image']['url'] populated, but we still want to use the site logo.
	 *
	 * @param array $settings     Processed settings (with dynamic tags resolved).
	 * @param array $settings_raw Raw settings (with __dynamic__ data intact).
	 * @return bool True if site logo should be rendered, false otherwise.
	 */
	private function should_use_site_logo( array $settings, array $settings_raw ) {
		$toggle_value  = isset( $settings['use_site_logo'] ) ? $settings['use_site_logo'] : '';
		$is_toggled_on = 'yes' === $toggle_value;

		// User explicitly enabled the toggle (new behavior).
		if ( $is_toggled_on ) {
			return true;
		}

		// User explicitly disabled the toggle (they turned it off).
		if ( 'no' === $toggle_value ) {
			return false;
		}

		// Backward compatibility: Check for legacy site-logo dynamic tag usage FIRST
		// before checking for custom images, because dynamic tags will resolve to a URL.
		// This ensures widgets created before the toggle feature was introduced continue
		// to work correctly without requiring manual migration.
		if ( $this->has_legacy_site_logo_dynamic_tag( $settings_raw ) ) {
			return true;
		}

		// If user provided a custom image (and it's not from a dynamic tag), prefer it.
		if ( ! empty( $settings['image']['url'] ) ) {
			return false;
		}

		return false;
	}

	/**
	 * Checks if widget uses the legacy 'site-logo' dynamic tag.
	 *
	 * Legacy Implementation:
	 * ----------------------
	 * Before the 'use_site_logo' toggle was introduced, users could select a dynamic tag
	 * called 'site-logo' from the image control. This tag would dynamically render the
	 * site's custom logo (from theme_mod 'custom_logo').
	 *
	 * The dynamic tag data could be stored in multiple ways by Elementor:
	 * 1. In __dynamic__['image'] as a string: '[site-logo id="123"]'
	 * 2. In __dynamic__['image'] as an array: ['name' => 'site-logo', ...]
	 * 3. Directly in the image URL field (if __dynamic__ data was lost)
	 *
	 * This method checks all possible storage locations to ensure complete backward
	 * compatibility with widgets created before the migration.
	 *
	 * @param array $settings_raw Raw widget settings with __dynamic__ data intact.
	 * @return bool True if widget uses legacy site-logo dynamic tag, false otherwise.
	 */
	private function has_legacy_site_logo_dynamic_tag( array $settings_raw ) {
		if ( $this->has_legacy_tag_in_dynamic_array( $settings_raw ) ) {
			return true;
		}

		if ( $this->has_legacy_tag_in_image_fields( $settings_raw ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if the __dynamic__ array contains a site-logo tag.
	 *
	 * @param array $settings_raw Raw widget settings with __dynamic__ data intact.
	 * @return bool True if site-logo tag found in __dynamic__ array.
	 */
	private function has_legacy_tag_in_dynamic_array( array $settings_raw ) {
		if ( empty( $settings_raw['__dynamic__'] ) || ! is_array( $settings_raw['__dynamic__'] ) ) {
			return false;
		}

		foreach ( $settings_raw['__dynamic__'] as $control_id => $dynamic_value ) {
			if ( ! $this->is_image_control( $control_id ) ) {
				continue;
			}

			if ( empty( $dynamic_value ) ) {
				continue;
			}

			if ( $this->is_site_logo_tag( $dynamic_value ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the control ID is an image-related control.
	 *
	 * @param string $control_id The control ID to check.
	 * @return bool True if control is image-related.
	 */
	private function is_image_control( $control_id ) {
		return 0 === strpos( $control_id, 'image' );
	}

	/**
	 * Checks if a dynamic value contains the site-logo tag.
	 *
	 * Handles both array format (with 'name' key) and string format (tag text).
	 *
	 * @param mixed $dynamic_value The dynamic value to check (array or string).
	 * @return bool True if value contains site-logo tag.
	 */
	private function is_site_logo_tag( $dynamic_value ) {
		if ( $this->is_site_logo_tag_array( $dynamic_value ) ) {
			return true;
		}

		if ( $this->is_site_logo_tag_string( $dynamic_value ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if dynamic value is an array containing site-logo tag name.
	 *
	 * @param mixed $dynamic_value The dynamic value to check.
	 * @return bool True if value is array with site-logo name.
	 */
	private function is_site_logo_tag_array( $dynamic_value ) {
		if ( ! is_array( $dynamic_value ) || ! isset( $dynamic_value['name'] ) ) {
			return false;
		}

		return 'site-logo' === $dynamic_value['name'];
	}

	/**
	 * Checks if dynamic value is a string containing site-logo tag.
	 *
	 * @param mixed $dynamic_value The dynamic value to check.
	 * @return bool True if value is string with site-logo tag.
	 */
	private function is_site_logo_tag_string( $dynamic_value ) {
		if ( ! is_string( $dynamic_value ) ) {
			return false;
		}

		if ( false === strpos( $dynamic_value, 'site-logo' ) ) {
			return false;
		}

		$tag_text_data = Elementor::$instance->dynamic_tags->tag_text_to_tag_data( $dynamic_value );

		return isset( $tag_text_data['name'] ) && 'site-logo' === $tag_text_data['name'];
	}

	/**
	 * Checks if image fields contain the site-logo tag.
	 *
	 * Fallback check for edge cases where __dynamic__ data was lost but the tag text remains.
	 *
	 * @param array $settings_raw Raw widget settings with __dynamic__ data intact.
	 * @return bool True if site-logo tag found in image fields.
	 */
	private function has_legacy_tag_in_image_fields( array $settings_raw ) {
		$image_controls = [ 'image', 'image_mobile', 'image_tablet' ];

		foreach ( $image_controls as $image_control ) {
			if ( $this->has_legacy_tag_in_image_control( $settings_raw, $image_control ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if a specific image control contains the site-logo tag.
	 *
	 * @param array  $settings_raw   Raw widget settings with __dynamic__ data intact.
	 * @param string $image_control The image control name to check.
	 * @return bool True if site-logo tag found in the control.
	 */
	private function has_legacy_tag_in_image_control( array $settings_raw, $image_control ) {
		if ( empty( $settings_raw[ $image_control ] ) || ! is_array( $settings_raw[ $image_control ] ) ) {
			return false;
		}

		$image_data = $settings_raw[ $image_control ];

		if ( empty( $image_data['url'] ) || ! is_string( $image_data['url'] ) ) {
			return false;
		}

		return $this->is_site_logo_tag_in_url( $image_data['url'] );
	}

	/**
	 * Checks if a URL string contains the site-logo tag.
	 *
	 * @param string $url The URL string to check.
	 * @return bool True if URL contains site-logo tag.
	 */
	private function is_site_logo_tag_in_url( $url ) {
		if ( false !== strpos( $url, '[site-logo' ) ) {
			return true;
		}

		$tag_text_data = Elementor::$instance->dynamic_tags->tag_text_to_tag_data( $url );

		return isset( $tag_text_data['name'] ) && 'site-logo' === $tag_text_data['name'];
	}


	/**
	 * Renders the site logo from WordPress custom_logo theme mod.
	 *
	 * This method is used both for:
	 * 1. New widgets with 'use_site_logo' toggle enabled
	 * 2. Legacy widgets with the old 'site-logo' dynamic tag (backward compatibility)
	 *
	 * It retrieves the site logo from the 'custom_logo' theme modification and renders
	 * it as an img tag. If no logo is set, it falls back to a placeholder image.
	 */
	private function site_logo_render() {
		$custom_logo_id = (int) get_theme_mod( 'custom_logo' );

		$alt = get_bloginfo( 'title' );

		if ( $custom_logo_id ) {
			$logo_html = wp_get_attachment_image( $custom_logo_id, 'full', false, [
				'alt' => $alt,
				'data-no-lazy' => 1,
			] );

			if ( $logo_html ) {
				echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return;
			}
		}

		// Fallback to placeholder if no logo is configured.
		echo '<img alt="' . esc_attr( $alt ) . '" data-no-lazy="1" src="' . esc_url( Utils::get_placeholder_image_src() ) . '" />'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	protected function customizer_logo_render() {
		if ( Elementor::$instance->experiments->is_feature_active( 'additional_custom_breakpoints' ) ) {
			return $this->customizer_logo_custom_breakpoints_render();
		}

		$settings = $this->get_settings_for_display();

		$devices = [
			'desktop' => '',
			'tablet'  => '_tablet',
			'mobile'  => '_mobile',
		];

		$logos = [];

		foreach ( $devices as $device => $device_setting_key ) {
			$device_setting = $settings[ 'logo' . $device_setting_key ];

			$logo = 'primary' !== $device_setting ? "jupiterx_logo_{$device_setting}" : 'jupiterx_logo';

			if ( in_array( $logo, $logos, true ) ) {
				$this->add_render_attribute( $logos[ $logo ], 'class', 'raven-site-logo-' . $device );
				continue;
			}

			$logos[ $logo ] = $logo;

			$image_src = get_theme_mod( $logo, '' );

			if ( empty( $image_src ) ) {
				$image_src = Utils::get_placeholder_image_src();
			}

			$retina_logo = 'primary' !== $device_setting ? "jupiterx_logo_{$device_setting}_retina" : 'jupiterx_logo_retina';

			$retina_image_src = get_theme_mod( $retina_logo, '' );

			if ( ! empty( $retina_image_src ) ) {
				$this->add_render_attribute( $logo, 'srcset', "{$image_src} 1x, {$retina_image_src} 2x" );
			}

			$this->add_render_attribute( $logo, [
				'src'   => esc_url( $image_src ),
				'alt'   => get_bloginfo( 'title' ),
				'class' => 'raven-site-logo-' . $device,
				'data-no-lazy' => 1,
			] );
		}

		foreach ( $logos as $device_logo ) :
			echo '<img ' . $this->get_render_attribute_string( $device_logo ) . ' />';
		endforeach;

	}

	protected function customizer_logo_custom_breakpoints_render() {
		$settings          = $this->get_settings_for_display();
		$default_logo_type = ! empty( $settings['logo'] ) ? str_replace( '_primary', '', '_' . $settings['logo'] ) : '';
		$placeholder_url   = Utils::get_placeholder_image_src();
		$default_logo_url  = get_theme_mod( "jupiterx_logo{$default_logo_type}", $placeholder_url );
		$picture_content   = '<picture>';

		foreach ( Elementor::$instance->breakpoints->get_active_breakpoints() as $breakpoint ) {
			$breakpoint_name = $breakpoint->get_name();

			if ( empty( $settings[ "logo_{$breakpoint_name}" ] ) ) {
				continue;
			}

			$logo_type       = str_replace( '_primary', '', '_' . $settings[ "logo_{$breakpoint_name}" ] );
			$logo_url        = get_theme_mod( "jupiterx_logo{$logo_type}" );
			$logo_retina_url = get_theme_mod( "jupiterx_logo{$logo_type}_retina" );

			if ( 'primary' === $settings[ "logo_{$breakpoint_name}" ] && empty( $logo_type ) ) {
				$logo_type       = '_primary';
				$logo_url        = get_theme_mod( 'jupiterx_logo' );
				$logo_retina_url = get_theme_mod( 'jupiterx_logo_retina' );
			}

			if ( ! empty( $logo_retina_url ) ) {
				$logo_url = "{$logo_url} 1x, {$logo_retina_url} 2x";
			}

			$picture_content .= "<source media='({$breakpoint->get_direction()}-width:{$breakpoint->get_value()}px)' srcset='{$logo_url}'>";
		}

		if (
			( ! empty( $settings['logo'] ) && 'primary' === $settings['logo'] ) &&
			empty( $default_logo_type )
		) {
			$default_logo_url = get_theme_mod( 'jupiterx_logo', $placeholder_url );
			$logo_retina_url  = get_theme_mod( 'jupiterx_logo_retina' );

			if ( ! empty( $logo_retina_url ) ) {
				$this->add_render_attribute( 'customizer-default-logo', 'srcset', "{$default_logo_url} 1x, {$logo_retina_url} 2x" );
			}
		}

		$this->add_render_attribute( 'customizer-default-logo', [
			'src' => esc_url( $default_logo_url ),
			'alt' => get_bloginfo( 'title' ),
			'data-no-lazy' => 1,
		] );

		$picture_content .= '<img ' . $this->get_render_attribute_string( 'customizer-default-logo' ) . ' />';
		$picture_content .= '</picture>';

		Utils::print_unescaped_internal_string( $picture_content );
	}
}

