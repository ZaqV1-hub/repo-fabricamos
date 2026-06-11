<?php
/**
 * Dynamic tags.
 *
 * @package JupiterX_Core\Raven
 * @since 1.5.0
 */

namespace JupiterX_Core\Raven\Core\Dynamic_Tags;

use ElementorPro\License\API as License_API;

defined( 'ABSPATH' ) || die();

class Module {

	public static $instance;

	public function __construct() {
		if ( defined( 'ELEMENTOR_PRO_PATH' ) && License_API::is_license_active() ) {
			add_action( 'elementor/dynamic_tags/register', [ $this, 'register_custom_tag' ], 999 );

			return;
		}

		add_action( 'elementor/dynamic_tags/register', [ $this, 'register_tags' ], 999 );
	}

	public function get_tags_structure() {
		$tag_classes = [
			'action' => [
				'title'   => esc_html__( 'Action', 'jupiterx-core' ),
				'classes' => [
					'Popup',
					'Contact_URL',
					'Lightbox',
				],
			],
			'archive' => [
				'title'   => esc_html__( 'Archive', 'jupiterx-core' ),
				'classes' => [
					'Archive_Description',
					'Archive_Meta',
					'Archive_Title',
					'Archive_URL',
				],
			],
			'author' => [
				'title'   => esc_html__( 'Author', 'jupiterx-core' ),
				'classes' => [
					'Author_Info',
					'Author_Meta',
					'Author_Name',
					'Author_URL',
					'Author_Profile_Picture',
				],
			],
			'comment' => [
				'title'   => esc_html__( 'Comment', 'jupiterx-core' ),
				'classes' => [
					'Comments_Number',
					'Comments_URL',
				],
			],
			'media' => [
				'title'   => esc_html__( 'Media', 'jupiterx-core' ),
				'classes' => [
					'Featured_Image_Data',
				],
			],
			'post' => [
				'title'   => esc_html__( 'Post', 'jupiterx-core' ),
				'classes' => [
					'Post_Custom_Field',
					'Post_Date',
					'Post_Excerpt',
					'Post_ID',
					'Post_Terms',
					'Post_Time',
					'Post_Title',
					'Post_Featured_Image',
					'Post_URL',
					'Post_Gallery',
				],
			],
			'site' => [
				'title'   => esc_html__( 'Site', 'jupiterx-core' ),
				'classes' => [
					'Shortcode',
					'Request_Parameter',
					'Site_Logo',
					'Site_Tagline',
					'Site_Title',
					'Site_URL',
					'Current_Date_Time',
					'Page_Title',
					'User_Info',
					'User_Profile_Picture',
					'Internal_URL',
				],
			],
		];

		if ( class_exists( 'woocommerce' ) ) {
			$tag_classes['woocommerce'] = [
				'title'   => esc_html__( 'WooCommerce', 'jupiterx-core' ),
				'classes' => [
					'Category_Image',
					'Product_Gallery',
					'Product_Image',
					'Product_Price',
					'Product_Rating',
					'Product_Sale',
					'Product_Short_Description',
					'Product_SKU',
					'Product_Stock',
					'Product_Terms',
					'Product_Title',
				],
			];
		}

		if ( class_exists( '\acf' ) ) {
			$tag_classes['acf'] = [
				'title'   => esc_html__( 'ACF', 'jupiterx-core' ),
				'classes' => [
					'ACF_Text',
					'ACF_Image',
					'ACF_URL',
					'ACF_Gallery',
					'ACF_File',
					'ACF_Number',
					'ACF_Color',
				],
			];
		}

		return $tag_classes;
	}

	/**
	 * Register custom tags.
	 *
	 * @since  3.8.4
	 * @access public
	 *
	 * @param Manager $dynamic_tags
	 */
	public function register_custom_tag( $dynamic_tags ) {
		$custom_tags = apply_filters( 'jupiterx\raven\dynamic_tags', [
			'site' => [
				'Internal_URL',
			],
			'action' => [
				'Popup',
			],
		] );

		if ( empty( $custom_tags ) ) {
			return;
		}

		$tags = $dynamic_tags->get_config();

		if ( ! isset( $tags['groups'] ) ) {
			return;
		}

		foreach ( $custom_tags as $group => $tags ) {
			if ( isset( $tags[ $group ] ) ) {
				continue;
			}

			foreach ( $tags as $tag ) {
				$class_name = 'JupiterX_Core\Raven\Core\Dynamic_Tags\Tags\\' . $group . '\\' . $tag;

				if ( class_exists( $class_name ) ) {
					$dynamic_tags->register( new $class_name() );
				}
			}
		}
	}

	/**
	 * Register tags.
	 *
	 * Add all the available dynamic tags.
	 *
	 * @since  1.5.0
	 * @access public
	 *
	 * @param Manager $dynamic_tags
	 */
	public function register_tags( $dynamic_tags ) {
		// Files already included by autoload.
		foreach ( $this->get_tags_structure() as $tag_group_id => $tag_group ) {
			$dynamic_tags->register_group( $tag_group_id, [
				'title' => $tag_group['title'],
			] );

			foreach ( $tag_group['classes'] as $class ) {
				$class_name = __NAMESPACE__ . '\Tags\\' . $tag_group_id . '\\' . $class;

				if ( class_exists( $class_name ) ) {
					$dynamic_tags->register( new $class_name() );
				}
			}
		}
	}
}
