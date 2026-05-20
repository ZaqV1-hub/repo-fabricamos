<?php
defined( 'ABSPATH' ) || die();

/**
 * Home screen data provider for JupiterX Control Panel.
 *
 * @package JupiterX_Core\Control_Panel_2
 * @since 4.9.0
 */
class JupiterX_Core_Control_Panel_Home {

	/**
	 * Cache key for Artbees latest posts.
	 */
	const POSTS_CACHE_KEY = 'jupiterx_cp_artbees_latest_posts_v2';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_jupiterx_cp_get_home_posts', [ $this, 'get_home_posts' ] );
	}

	/**
	 * Return latest blog posts for the control panel home.
	 */
	public function get_home_posts() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error(
				[
					'message' => __( 'Unauthorized.', 'jupiterx-core' ),
				],
				403
			);
		}

		wp_send_json_success( $this->fetch_latest_blog_posts() );
	}

	/**
	 * Fetch and cache latest posts from Artbees.
	 *
	 * @return array
	 */
	private function fetch_latest_blog_posts() {
		$posts = get_transient( self::POSTS_CACHE_KEY );

		if ( false !== $posts ) {
			return is_array( $posts ) ? $posts : [];
		}

		$response = wp_remote_get(
			'https://artbees.net/wp-json/artbees/v1/latest-posts',
			[
				'timeout' => 15,
			]
		);

		if ( is_wp_error( $response ) ) {
			return [];
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) ) {
			return [];
		}

		$posts = array_map(
			function( $post ) {
				$categories = [];

				if ( ! empty( $post['categories'] ) && is_array( $post['categories'] ) ) {
					foreach ( $post['categories'] as $category ) {
						$categories[] = [
							'name' => isset( $category['name'] ) ? wp_strip_all_tags( $category['name'] ) : '',
							'url'  => isset( $category['url'] ) ? esc_url_raw( $category['url'] ) : '',
						];
					}
				}

				return [
					'title'              => isset( $post['title'] ) ? wp_kses_post( $post['title'] ) : '',
					'url'                => isset( $post['url'] ) ? esc_url_raw( $post['url'] ) : '',
					'author'             => isset( $post['author'] ) ? wp_kses_post( $post['author'] ) : '',
					'published_date'     => isset( $post['published_date'] ) ? wp_kses_post( $post['published_date'] ) : '',
					'featured_image_url' => isset( $post['featured_image_url'] ) ? esc_url_raw( $post['featured_image_url'] ) : '',
					'categories'         => $categories,
				];
			},
			array_slice( $data, 0, 3 )
		);

		set_transient( self::POSTS_CACHE_KEY, $posts, 3 * HOUR_IN_SECONDS );

		return $posts;
	}
}

new JupiterX_Core_Control_Panel_Home();
