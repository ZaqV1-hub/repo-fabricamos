<?php
/**
 * Forces Elementor Canvas for Saved Templates quick view (control panel iframe preview).
 *
 * @package JupiterX_Core\Control_Panel_2
 * @since 4.16.0
 */

defined( 'ABSPATH' ) || die();

/**
 * Resolve the elementor_library post ID for the current quick-view request.
 *
 * @return int
 */
function jupiterx_cp_elementor_library_quick_view_resolve_post_id() {
	$post_id = (int) get_queried_object_id();

	if ( ! $post_id && isset( $_GET['p'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = absint( wp_unslash( $_GET['p'] ) );
	}

	if ( ! $post_id && isset( $_GET['page_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = absint( wp_unslash( $_GET['page_id'] ) );
	}

	if ( ! $post_id && ! empty( $_GET['preview_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = absint( wp_unslash( $_GET['preview_id'] ) );
	}

	return $post_id;
}

/**
 * Whether the current front-end request is an authorized Saved Templates quick view.
 *
 * Must run after the main query (e.g. template_redirect) so pretty permalinks resolve
 * the elementor_library post without ?p= in the URL.
 *
 * @return bool
 */
function jupiterx_cp_elementor_library_quick_view_is_active() {
	static $active = null;

	if ( null !== $active ) {
		return $active;
	}

	$active = false;

	if ( is_admin() ) {
		return $active;
	}

	if ( empty( $_GET['jupiterx_cp_quick_view'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return $active;
	}

	if ( ! is_user_logged_in() ) {
		return $active;
	}

	$post_id = jupiterx_cp_elementor_library_quick_view_resolve_post_id();

	if ( ! $post_id || 'elementor_library' !== get_post_type( $post_id ) ) {
		return $active;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $active;
	}

	$active = true;

	return $active;
}

/**
 * Hide the front-end admin bar inside control panel quick view iframes.
 *
 * Runs on template_redirect before _wp_admin_bar_init (priority 0) so the bar is
 * never initialized. Pretty permalinks omit ?p=, so post ID must come from the main query.
 *
 * @return void
 */
function jupiterx_cp_elementor_library_quick_view_hide_admin_bar() {
	if ( ! jupiterx_cp_elementor_library_quick_view_is_active() ) {
		return;
	}

	show_admin_bar( false );
	add_filter( 'show_admin_bar', '__return_false', 99999 );

	remove_action( 'wp_body_open', 'wp_admin_bar_render', 0 );
	remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_admin_bar_bump_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_admin_bar_header_styles' );
}

add_action( 'template_redirect', 'jupiterx_cp_elementor_library_quick_view_hide_admin_bar', -1 );

/**
 * Dequeue admin bar assets for quick view (belt and suspenders).
 *
 * @return void
 */
function jupiterx_cp_elementor_library_quick_view_dequeue_admin_bar_assets() {
	if ( ! jupiterx_cp_elementor_library_quick_view_is_active() ) {
		return;
	}

	wp_dequeue_style( 'admin-bar' );
	wp_dequeue_script( 'admin-bar' );
}

add_action( 'wp_enqueue_scripts', 'jupiterx_cp_elementor_library_quick_view_dequeue_admin_bar_assets', 100 );

/**
 * Remove admin-bar body classes so themes do not reserve top offset.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function jupiterx_cp_elementor_library_quick_view_body_class( $classes ) {
	if ( ! jupiterx_cp_elementor_library_quick_view_is_active() ) {
		return $classes;
	}

	return array_values( array_diff( $classes, [ 'admin-bar' ] ) );
}

add_filter( 'body_class', 'jupiterx_cp_elementor_library_quick_view_body_class', 999 );

/**
 * Use Elementor's canvas template so quick view shows only template content (no theme header/footer).
 *
 * @param string $template Path to the current template.
 * @return string
 */
function jupiterx_cp_elementor_library_quick_view_template( $template ) {
	if ( ! jupiterx_cp_elementor_library_quick_view_is_active() ) {
		return $template;
	}

	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return $template;
	}

	$modules = \Elementor\Plugin::$instance->modules_manager;

	if ( ! $modules || ! is_object( $modules ) ) {
		return $template;
	}

	$page_templates = $modules->get_modules( 'page-templates' );

	if ( ! $page_templates || ! method_exists( $page_templates, 'get_template_path' ) ) {
		return $template;
	}

	$canvas = $page_templates->get_template_path( \Elementor\Modules\PageTemplates\Module::TEMPLATE_CANVAS );

	if ( empty( $canvas ) || ! is_readable( $canvas ) ) {
		return $template;
	}

	return $canvas;
}

add_filter( 'template_include', 'jupiterx_cp_elementor_library_quick_view_template', 999 );
