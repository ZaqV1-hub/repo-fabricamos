<?php
namespace JupiterX_Core\Raven\Modules\Advanced_Posts\Functionality;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Utils;
use JupiterX_Core\Raven\Modules\Post_Meta\Module as MetaModule;

/**
 * Grid layout.
 *
 * @since 2.5.3
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Frontend {
	/**
	 * Widgets settings.
	 *
	 * @since 2.5.3
	 */
	private $settings;

	/**
	 * Widget query.
	 *
	 * @since 2.5.3
	 */
	public $wp_query;

	/**
	 * Widget layout type.
	 *
	 * @since 2.5.3
	 */
	public $layout_type;

	/**
	 * Item increment.
	 *
	 * @since 2.5.3
	 */
	public $increment = 0;

	/**
	 * The count of excluded post when orderby set to Random
	 *
	 * @since 4.12.0
	 */
	private $random_exclude_page_count = 0;

	/**
	 * Archive page query.
	 *
	 * @since 2.5.3
	 */
	protected $archive_query;

	public function __construct( $widget ) {
		$this->settings    = $widget->get_settings();
		$this->layout_type = $this->settings['general_layout'];
		$this->wp_query    = $this->get_query_posts();
	}

	/**
	 * Render posts.
	 *
	 * @since 2.5.3
	 */
	public function render_content() {
		if ( $this->wp_query->have_posts() ) {
			add_filter( 'excerpt_length', [ $this, 'excerpt_length' ], PHP_INT_MAX );

			add_filter( 'excerpt_more', [ $this, 'excerpt_more' ], PHP_INT_MAX );

			$increment = 0;

			while ( $this->wp_query->have_posts() ) {
				$this->wp_query->the_post();
				++$increment;
				$post_id = get_the_ID();

				$this->render_item_before( $this->layout_type, $increment );
				$this->render_item( $post_id );
				$this->render_item_after();
			}

			remove_filter( 'excerpt_length', [ $this, 'excerpt_length' ], PHP_INT_MAX );

			remove_filter( 'excerpt_more', [ $this, 'excerpt_more' ], PHP_INT_MAX );
		} else {
			// Empty state for no results. Prefer user-defined message if present.
			$empty_message      = ! empty( $this->settings['empty_state_text'] ) ? $this->settings['empty_state_text'] : esc_html__( 'No results found.', 'jupiterx-core' );
			$layout_item_class  = 'raven-' . $this->layout_type . '-item';
			$additional_classes = ' raven-posts-empty-item';
			// Styles for the empty item are handled in the stylesheet.

			if ( in_array( $this->layout_type, [ 'matrix', 'metro' ], true ) ) {
				$additional_classes .= ' raven-posts-full-width';
			}

			printf(
				'<div class="raven-posts-item %1$s%2$s"><div class="raven-post-wrapper"><div class="raven-post"><div class="raven-posts-empty">%3$s</div></div></div></div>',
				esc_attr( $layout_item_class ),
				esc_attr( $additional_classes ),
				esc_html( $empty_message )
			);
		}

		wp_reset_postdata();
	}

	/**
	 * Render posts in ajax request.
	 *
	 * @since 2.5.3
	 */
	public function get_queried_posts( $archive_query ) {
		$this->archive_query = $archive_query;
		$this->wp_query      = $this->get_query_posts();

		$queried_posts = [];

		if ( $this->wp_query->have_posts() ) {
			add_filter( 'excerpt_length', [ $this, 'excerpt_length' ], PHP_INT_MAX );

			add_filter( 'excerpt_more', [ $this, 'excerpt_more' ], PHP_INT_MAX );

			$queried_posts['max_num_pages']  = $this->wp_query->max_num_pages;
			$queried_posts['total_posts']    = $this->wp_query->found_posts;
			$queried_posts['rendered_posts'] = isset( $this->archive_query['rendered_posts'] ) ? $this->archive_query['rendered_posts'] : [];
			$ids                             = [];

			$increment = 0;

			while ( $this->wp_query->have_posts() ) {
				$this->wp_query->the_post();
				++$increment;
				$post_id = get_the_ID();
				$ids[]   = (string) get_the_ID();

				ob_start();

				$this->render_item_before( $this->layout_type, $increment );
				$this->render_item( $post_id );
				$this->render_item_after();

				$queried_posts['posts'][] = ob_get_clean();
			}

			if ( ! empty( $queried_posts['rendered_posts'] ) ) {
				$page                            = $this->archive_query['ajax_page'];
				$queried_posts['rendered_posts'] = $this->update_rendered_posts( $queried_posts['rendered_posts'], $page, $ids );
				$queried_posts['max_num_pages'] += $this->random_exclude_page_count;
			}

			remove_filter( 'excerpt_length', [ $this, 'excerpt_length' ], PHP_INT_MAX );

			remove_filter( 'excerpt_more', [ $this, 'excerpt_more' ], PHP_INT_MAX );
		} else {
			// Provide empty markup for AJAX consumers with translatable message.
			$empty_message      = ! empty( $this->settings['empty_state_text'] ) ? $this->settings['empty_state_text'] : esc_html__( 'No results found.', 'jupiterx-core' );
			$layout_item_class  = 'raven-' . $this->layout_type . '-item';
			$additional_classes = ' raven-posts-empty-item';
			// Styles for empty state are defined in SCSS; no inline styles here.

			if ( in_array( $this->layout_type, [ 'matrix', 'metro' ], true ) ) {
				$additional_classes .= ' raven-posts-full-width';
			}

			// Width and fit for grid/masonry empty item are handled via CSS.

			$queried_posts['max_num_pages'] = 1;
			$queried_posts['total_posts']   = 0;
			$queried_posts['posts'][]       = sprintf(
				'<div class="raven-posts-item %1$s%2$s"><div class="raven-post-wrapper"><div class="raven-post"><div class="raven-posts-empty">%3$s</div></div></div></div>',
				esc_attr( $layout_item_class ),
				esc_attr( $additional_classes ),
				esc_html( $empty_message )
			);
		}

		wp_reset_postdata();

		return $queried_posts;
	}

	/**
	 * Get post query.
	 *
	 * @since 2.5.3
	 */
	public function get_query_posts() {
		$args                = Utils::get_query_args( $this->settings );
		$is_archive_template = ! empty( $this->settings['is_archive_template'] );
		$show_pagination     = ! empty( $this->settings['show_pagination'] );

		// prevent rendering repetetive posts on different page
		if ( 'rand' === $this->settings['query_orderby'] && 'yes' === $this->settings['ignore_repetitive'] && ! empty( $this->archive_query['rendered_posts'] ) ) {
			$excluded_posts = $this->exclude_rendered_posts( $args['posts_per_page'] );
			$args['paged']  = 1;

			if ( empty( $args['post__in'] ) ) {
				$args['post__not_in'] = ! empty( $args['post__not_in'] ) ? array_merge( $args['post__not_in'], $excluded_posts ) : $excluded_posts;
			} else {
				$args['post__in'] = array_diff( $args['post__in'], $excluded_posts );
			}
		}

		if ( $is_archive_template ) {
			global $wp_query;

			$args                        = $wp_query->query_vars;
			$args['ignore_sticky_posts'] = true;

			if ( $this->archive_query ) {
				$this->archive_query['ignore_sticky_posts'] = true;
				$args                                       = $this->archive_query;
			}
		}

		if ( true !== $is_archive_template && 'true' === $this->settings['related_to_current_post'] ) {
			$args = $this->add_tax_query_argument( $args );
		}

		// Disable found rows when pagination is disabled.
		if ( ! $show_pagination ) {
			$args['no_found_rows'] = true;
		}

		add_action( 'pre_get_posts', [ $this, 'sticky_posts' ], 20 );

		$args = apply_filters( 'jupiterx-raven-posts-query-arguments', $args );

		$new_query = new \WP_Query( $args );

		remove_action( 'pre_get_posts', [ $this, 'sticky_posts' ], 20 );

		return $new_query;
	}

	/**
	 * Sticky Post.
	 *
	 * @since 2.5.3
	 */
	public function sticky_posts( $query ) {
		// Hack to make sticky posts work on preview.
		if ( ! $query->get( 'ignore_sticky_posts' ) ) {
			$query->is_home = true;
		}
	}

	/**
	 * Render post item.
	 *
	 * @since 2.5.3
	 */
	public function render_item( $post_id ) {
		$html = '';

		if ( ! empty( $this->settings['equal_height'] ) && 'grid' === $this->settings['general_layout'] ) {
			$html .= $this->render_image();
			$html .= '<div class="raven-post-content-container">';
			$html .= '<div class="raven-post-content" data-id="' . $post_id . '">';
			$html .= $this->render_ordered_content();
			$html .= $this->render_button();
			$html .= '</div>';
			$html .= $this->render_author_spotlight();
			$html .= '</div>';

			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			return;
		}

		$html .= $this->render_image();
		$html .= '<div class="raven-post-content" data-id="' . $post_id . '">';
		$html .= $this->render_ordered_content();
		$html .= $this->render_button();
		$html .= $this->render_author_spotlight();
		$html .= '</div>';

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Order Post Content.
	 *
	 * @since 2.5.3
	 */
	public function render_ordered_content() {
		$meta_position = $this->settings['meta_position'];

		$html = '';

		if ( 'before_title' === $meta_position ) {
			$html .= $this->render_meta();
			$html .= $this->render_title();
			$html .= $this->render_excerpt();

			return $html;
		}

		$html .= $this->render_title();
		$html .= $this->render_meta();
		$html .= $this->render_excerpt();

		return $html;
	}

	/**
	 * Render Title.
	 *
	 * @since 2.5.3
	 */
	public function render_title() {
		if ( empty( $this->settings['show_title'] ) ) {
			return;
		}

		return sprintf(
			'<%1$s class="raven-post-title"><a class="raven-post-title-link" href="%2$s">%3$s</a></%1$s>',
			$this->settings['post_title_tag'],
			get_permalink(),
			get_the_title()
		);
	}

	/**
	 * Render Image.
	 *
	 * @since 2.5.3
	 */
	public function render_image() {
		if ( empty( $this->settings['show_image'] ) ) {
			return;
		}

		$settings = [
			'image_size'             => $this->settings['image_size'],
			'image'                  => [
				'id' => get_post_thumbnail_id(),
			],
			'image_custom_dimension' => $this->settings['image_custom_dimension'],
		];

		$image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings );
		$image_src  = \Elementor\Group_Control_Image_Size::get_attachment_image_src( get_post_thumbnail_id(), 'image', $settings );

		if ( empty( $image_html ) ) {
			return;
		}

		$classes = [ 'raven-post-image' ];

		if (
			( in_array( $this->settings['general_layout'], [ 'matrix', 'metro' ], true ) && 'overlay' !== $this->settings['metro_matrix_content_layout'] ) ||
			( 'grid' === $this->settings['general_layout'] && 'overlay' !== $this->settings['content_layout'] ) ||
			( 'masonry' === $this->settings['general_layout'] && 'overlay' !== $this->settings['content_layout'] && 'full' !== $this->settings['image_size'] )
		) {
			$classes[] = 'raven-image-fit';
		}

		$tags = $this->get_render_tags() ?? '';
		$zoom = '';

		if ( 'zoom-move' === $this->settings['featured_image_hover'] ) {
			$zoom = "<div class='raven-posts-zoom-move-wrapper' style='background-image: url(" . esc_url( $image_src ) . ")'></div>";
		}

		printf(
			'<div class="raven-post-image-wrap">%1$s<a class="%2$s" href="%3$s">%4$s %5$s<span class="raven-post-image-overlay">%6$s</span></a></div>',
			wp_kses_post( $tags ),
			esc_attr( implode( ' ', $classes ) ),
			esc_url( get_permalink() ),
			$zoom, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$image_html, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			''
		);
	}

	/**
	 * Render meta.
	 *
	 * @since 2.5.3
	 */
	public function render_meta() {
		// We can modify this to re-order the meta stack.
		$meta_list = [
			'date',
			'author',
			'categories',
			'comments',
			'reading_time',
		];

		$meta_stack    = $this->get_render_stack( $meta_list );
		$custom_fields = $this->get_render_custom_fields();

		if ( ! empty( $custom_fields ) ) {
			$meta_stack = array_merge( $meta_stack, $custom_fields );
		}

		if ( empty( $meta_stack ) ) {
			return;
		}

		$meta_html = implode( $this->get_render_divider(), $meta_stack );

		return '<div class="raven-post-meta">' . wp_kses_post( $meta_html ) . '</div>';
	}

	/**
	 * Get render by stack.
	 * Use to get render in a stack list format.
	 *
	 * @since 2.5.3
	 * @param array $stack_list List of function names.
	 */
	private function get_render_stack( $stack_list = [] ) {
		$stack_render = [];

		foreach ( $stack_list as $stack_item ) {
			$func_name = 'get_render_' . $stack_item;

			$to_render = $this->$func_name();

			if ( ! empty( $to_render ) ) {
				$stack_render[] = $to_render;
			}
		}

		return $stack_render;
	}

	/**
	 * Render the post meta divider.
	 *
	 * @since 2.5.3
	 */
	private function get_render_divider() {
		if ( ! $this->settings['posts_meta_divider'] ) {
			return PHP_EOL;
		}

		return PHP_EOL . '<span class="raven-post-meta-divider">' . esc_html( $this->settings['posts_meta_divider'] ) . '</span>' . PHP_EOL;
	}

	/**
	 * Render the post meta date.
	 *
	 * @since 2.5.3
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function get_render_date() {
		if ( ! $this->settings['show_date'] ) {
			return;
		}

		$format_options = [
			'1' => 'F j, Y',
			'2' => 'F jS, Y',
			'3' => 'M j, Y',
			'4' => 'Y/m/d',
			'5' => 'd/m/Y',
			'6' => 'd.m.Y',
			'7' => 'm.d.Y',
		];

		$format = ! empty( $format_options[ $this->settings['date_format'] ] ) ? $format_options[ $this->settings['date_format'] ] : $this->settings['custom_format'];

		$date = get_the_date( $format );

		if ( 'last_modified' === $this->settings['date_type'] ) {
			$date = get_the_modified_date( $format );
		}

		$date_link = ( 'post' === get_post_type() ) ? get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) : get_permalink();

		return '<a class="raven-post-meta-item raven-post-date" href="' . esc_url( $date_link ) . '" rel="bookmark">' . esc_html( $date ) . '</a>';
	}

	/**
	 * Render the post meta author.
	 *
	 * @since 2.5.3
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function get_render_author() {
		if ( ! $this->settings['show_author'] ) {
			return;
		}

		$href = get_author_posts_url( get_the_author_meta( 'ID' ) );

		return '<a class="raven-post-meta-item raven-post-author" href="' . esc_url( $href ) . '">' . esc_html( get_the_author() ) . '</a>';
	}

	/**
	 * Render the post meta categories.
	 *
	 * @since 2.5.3
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function get_render_categories() {
		$post_type = get_post_type();

		$valid_post_types = apply_filters(
			'jupitex_raven_valid_post_types_taxonomies',
			[
				'post'      => 'category',
				'portfolio' => 'portfolio_category',
			],
			'advanced_posts'
		);

		if ( ! in_array( $post_type, array_keys( $valid_post_types ), true ) || ! $this->settings['show_categories'] ) {
			return;
		}

		$taxonomy = 'category';

		if ( 'portfolio' === $post_type ) {
			$taxonomy = 'portfolio_category';
		}

		if ( ! empty( $valid_post_types[ $post_type ] ) ) {
			$taxonomy = $valid_post_types[ $post_type ];
		}

		$categories_list = get_the_term_list( get_the_ID(), $taxonomy, '', ', ', '' ) ?? '';

		if ( empty( $categories_list ) ) {
			return;
		}

		return sprintf( '<span class="raven-post-meta-item raven-post-categories">%1$s</span>', wp_kses_post( $categories_list ) );
	}

	/**
	 * Render the post meta reading time.
	 *
	 * @since 4.1.0
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function get_render_reading_time() {
		if ( empty( $this->settings['show_reading_time'] ) ) {
			return;
		}

		$reading_time = get_post_meta( get_the_ID(), 'jupiterx_reading_time', true );

		if ( empty( $reading_time ) ) {
			$content      = get_the_content();
			$reading_time = MetaModule::get_instance()->get_read_time( $content );

			update_post_meta( get_the_ID(), 'jupiterx_reading_time', $reading_time );
		}

		return '<span class="raven-post-meta-item raven-post-reading-time">' . esc_html( $reading_time ) . '</span>';
	}

	/**
	 * Render the post meta tags.
	 *
	 * @since 2.5.3
	 */
	private function get_render_tags() {
		$post_type = get_post_type();

		if ( ! in_array( $post_type, [ 'post', 'portfolio' ], true ) || ! $this->settings['show_tags'] ) {
			return '';
		}

		$taxonomy = 'post_tag';

		if ( 'portfolio' === $post_type ) {
			$taxonomy = 'portfolio_tag';
		}

		$tags_list = get_the_term_list( get_the_ID(), $taxonomy, '<ul class="raven-post-meta-item raven-post-tags"><li>', '</li><li>', '</li></ul>' );

		if ( empty( $tags_list ) ) {
			return;
		}

		return $tags_list;
	}

	/**
	 * Render the post comments.
	 *
	 * @since 2.5.3
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function get_render_comments() {
		if ( ! $this->settings['show_comments'] ) {
			return;
		}

		return '<a class="raven-post-meta-item raven-post-comments" href="' . esc_url( get_permalink() ) . '#comments" rel="bookmark">' . esc_html( get_comments_number_text() ) . '</a>';
	}

	/**
	 * Render the post excerpt.
	 *
	 * @since 2.5.3
	 */
	private function render_excerpt() {
		if ( ! $this->settings['show_excerpt'] ) {
			return;
		}

		global $post;

		$custom_excerpt = isset( $this->settings['custom_excerpt'] ) ? $this->settings['custom_excerpt'] : 'yes';

		if ( 'yes' === $custom_excerpt ) {
			return '<div class="raven-post-excerpt">' . get_the_excerpt() . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( has_excerpt() ) {
			return '<div class="raven-post-excerpt">' . esc_html( wp_trim_words( wp_strip_all_tags( $post->post_excerpt ), $this->excerpt_length(), '' ) ) . '</div>';
		}

		$custom_excerpt = apply_filters( 'jupiterx_advanced_posts_excerpt', $post->post_excerpt, $post );

		if ( ! empty( $custom_excerpt ) ) {
			return '<div class="raven-post-excerpt">' . wp_kses_post( $custom_excerpt ) . '</div>';
		}

		return '<div class="raven-post-excerpt">' . esc_html( wp_strip_all_tags( get_the_excerpt() ) ) . '</div>';
	}

	/**
	 * Render author spotlight.
	 *
	 * @since 2.5.3
	 */
	private function render_author_spotlight() {
		if ( ! $this->settings['author_spotlight'] ) {
			return;
		}

		return sprintf(
			'<div class="raven-post-author-spotlight"><a href="%3$s"><img src="%1$s" title="%2$s" alt="%2$s">%4$s %2$s</a></div>',
			esc_attr( get_avatar_url( get_the_author_meta( 'ID' ), [ 'size' => 256 ] ) ),
			esc_html( get_the_author() ),
			esc_attr( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html__( 'By', 'jupiterx-core' )
		);
	}

	/**
	 * Render Custom Fields
	 *
	 * @since 4.11.0
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	protected function get_render_custom_fields() {
		$show_custom_field = $this->settings['show_custom_field'];

		if ( 'yes' !== $show_custom_field ) {
			return;
		}

		$selected_custom_fields = $this->settings['custom_fields_list'];

		if ( empty( $selected_custom_fields ) ) {
			return [];
		}

		$output = [];

		foreach ( $selected_custom_fields as $field ) {
			$key        = trim( $field['field_title'] );
			$meta_value = '';

			if ( ! $key && 'taxonomy' !== $field['item_type'] ) {
				continue;
			}

			if ( 'taxonomy' === $field['item_type'] ) {

				$tax = 'custom' !== $field['taxonomy_type'] ? $field['taxonomy_type'] : $field['taxonomy_slug'];

				if ( ! $tax ) {
					continue;
				}

				$terms = get_the_terms( get_the_ID(), $tax );

				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

					$meta_value = Utils::generate_term_item( $terms, $field, 'advanced-post' );
				}
			} else {
				$meta_value = get_post_meta( get_the_ID(), $key, true );
			}

			if ( ! $meta_value ) {
				continue;
			}

			if ( 'taxonomy' === $field['item_type'] && 'yes' === $field['show_as_link'] ) {

				foreach ( $meta_value as $value ) {
					$output[] = $value;
				}
				continue;
			}

			if ( ( 'array' === $field['field_type'] && 'array' === gettype( $meta_value ) ) || 'array' === gettype( $meta_value ) ) {
				$meta_value = implode( ', ', $meta_value );
			}

			if ( 'date' === $field['field_type'] ) {
				$date = \DateTime::createFromFormat( 'Ymd', $meta_value );

				if ( $date instanceof \DateTime ) {
					$meta_value = $date->format( $field['date_type'] );
				}
			}

			if ( ! empty( $field['field_url']['url'] ) && 'meta' === $field['item_type'] ) {
				$links = [];
				$items = [];
				if ( 'date' !== $field['field_type'] ) {
					$items = explode( ',', $meta_value );
				} else {
					$items[] = $meta_value;
				}

				foreach ( $items as $value ) {
					if ( 'date' === $field['field_type'] ) {
						$value = str_replace( '-', ',', $value );
					}
					$link    = Utils::generate_link( $field['field_url'], trim( $value ), 'post' );
					$links[] = PHP_EOL . $link . PHP_EOL;
				}

				$output[] = implode( ', ', $links );
				continue;
			}

			$output[] = PHP_EOL . '<span class="raven-post-meta-item raven-post-custom-fields">' . esc_html( $meta_value ) . '</span>' . PHP_EOL;
		}
		return $output;
	}

	/**
	 * Render the post button.
	 *
	 * @since 2.5.3
	 */
	private function render_button() {
		if ( ! $this->settings['show_button'] ) {
			return;
		}

		return sprintf(
			'<div class="raven-post-read-more"><a class="raven-post-button" href="%1$s"><span class="raven-post-button-text">%2$s</span></a></div>',
			esc_attr( get_the_permalink() ),
			esc_html( $this->settings['posts_cta_button_text'] )
		);
	}

	/**
	 * Render content item before.
	 *
	 * @since 2.5.3
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function render_item_before( $type, $increment ) {
		$featured_image_position = '';
		$block_hover_animation   = '';
		$loaded_animation        = '';
		$overlay_animation       = '';
		$metro_matrix_item_count = '';

		if (
			'yes' === $this->settings['show_image'] &&
			'side' === $this->settings['content_layout'] &&
			! empty( $this->settings['featured_image_position'] ) &&
			in_array( $this->settings['general_layout'], [ 'grid', 'masonry' ], true )
		) {
			$featured_image_position = 'raven-post-inline raven-post-inline-' . $this->settings['featured_image_position'];
		}

		if ( ! empty( $this->settings['block_hover'] ) ) {
			$block_hover_animation = 'elementor-animation-' . $this->settings['block_hover'];
		}

		if ( ! empty( $this->settings['load_effect'] ) ) {
			$loaded_animation = 'raven-posts-load-effect raven-post-effect-' . $this->settings['load_effect'];
		}

		if (
			( 'overlay' === $this->settings['content_layout'] && in_array( $this->settings['general_layout'], [ 'grid', 'masonry' ], true ) ) ||
			( 'overlay' === $this->settings['metro_matrix_content_layout'] && in_array( $this->settings['general_layout'], [ 'matrix', 'metro' ], true ) )
		) {
			$overlay_animation = 'raven-post-inside';
		}

		if (
			'metro' === $type &&
			( $this->increment + 3 === $increment || 1 === $increment )
		) {
			$this->increment         = $increment;
			$metro_matrix_item_count = 'raven-posts-full-width';
		}

		if (
			'matrix' === $type &&
			( $this->increment + 4 === $increment || 1 === $increment )
		) {
			$this->increment         = $increment;
			$metro_matrix_item_count = 'raven-posts-full-width';
		}

		$classes = [
			'item'    => [
				'raven-posts-item',
				"raven-{$type}-item",
				$block_hover_animation,
				$metro_matrix_item_count,
			],
			'wrapper' => [
				'raven-post-wrapper',
				$loaded_animation,
			],
			'content' => [
				'raven-post',
				$featured_image_position,
				$overlay_animation,
			],
		];

		printf(
			'<div class="%1$s"><div class="%2$s"><div class="%3$s">',
			esc_attr( implode( ' ', $classes['item'] ) ),
			esc_attr( implode( ' ', $classes['wrapper'] ) ),
			esc_attr( implode( ' ', $classes['content'] ) )
		);
	}

	/**
	 * Render pagination.
	 *
	 * @since 2.5.3
	 */
	public function render_pagination() {
		if ( 'yes' !== $this->settings['show_pagination'] ) {
			return;
		}

		$method_name = 'render_' . $this->settings['pagination_type'];

		return $this->$method_name();
	}

	/**
	 * Render infinite load indicator.
	 *
	 * @since 4.7.8
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function render_infinite_load() {
		if ( 'infinite_load' !== $this->settings['pagination_type'] ) {
			return;
		}

		return '<span class="raven-infinite-load"></span>';
	}

	/**
	 * Render load more button.
	 *
	 * @since 2.5.3
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function render_load_more() {
		// Hide load more button in front-end when we have no more posts.
		if ( $this->wp_query->max_num_pages <= 1 && ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return;
		}

		$settings = [
			'maxNumPages' => $this->wp_query->max_num_pages,
		];

		$load_more = sprintf(
			'<span class="raven-posts-preloader"></span><div class="raven-load-more" data-settings="%1$s"><a class="raven-load-more-button" href="#"><span class="raven-post-button-text">%2$s</span></a></div>',
			esc_attr( wp_json_encode( $settings ) ),
			wp_kses_post( $this->settings['load_more_text'] ?? '' )
		);

		return $load_more;
	}

	/**
	 * Render page based pagination.
	 *
	 * @since 2.5.3
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function render_page_based() {
		if ( $this->wp_query->max_num_pages <= 1 ) {
			return;
		}

		$settings = [
			'posts_per_page' => $this->settings['query_posts_per_page'],
			'total_pages'    => $this->wp_query->max_num_pages,
			'pages_visible'  => $this->settings['page_based_pages_visible'],
		];

		$is_archive_template = $this->settings['is_archive_template'];

		if ( $is_archive_template ) {
			$settings['posts_per_page'] = $this->wp_query->query_vars['posts_per_page'];
		}

		$page_length = ( $settings['total_pages'] < $settings['pages_visible'] ) ? $settings['total_pages'] : $settings['pages_visible'];

		$render_pages = '';

		for ( $i = 1; $i <= $page_length; $i++ ) {
			$render_pages .= sprintf(
				'<a class="%1$s" href="#" data-page-num="%2$s">%2$s</a>',
				'raven-pagination-num raven-pagination-item' . ( ( 1 === $i ) ? ' raven-pagination-active' : '' ),
				$i
			);
		}

		$prev_button = sprintf(
			'<a class="raven-pagination-prev raven-pagination-item raven-pagination-disabled" href="#">%s</a>',
			wp_kses_post( $this->settings['page_based_prev_text'] ?? '' )
		);

		$next_button = sprintf(
			'<a class="raven-pagination-next raven-pagination-item" href="#">%s</a>',
			wp_kses_post( $this->settings['page_based_next_text'] ?? '' )
		);

		$pages = sprintf(
			'<span class="raven-posts-preloader"></span><div class="raven-pagination" data-settings="%1$s"><div class="raven-pagination-items">%2$s</div></div>',
			esc_attr( wp_json_encode( $settings ) ),
			wp_kses_post( $prev_button . $render_pages . $next_button )
		);

		return $pages;
	}

	/**
	 * Render sort.
	 *
	 * @since 2.5.3
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public function render_sortable() {
		if (
			! $this->settings['show_sortable'] ||
			! empty( $this->settings['query_select_ids'] ) ||
			$this->settings['is_archive_template']
		) {
			return;
		}

		$post_type = $this->settings['query_post_type'];

		$sortable_items = [];

		if ( $this->settings['show_all_title'] ) {
			$sortable_items[-1] = sprintf(
				'<a class="raven-sortable-item raven-sortable-active" data-category="-1" href="#">%s</a>',
				$this->settings['sortable_all_text']
			);
		}

		$taxonomies = get_object_taxonomies( $post_type, 'names' );

		$category_control_id = '';
		$category_name       = '';

		foreach ( $taxonomies as $taxonomy_name ) {
			$validate = false !== strpos( $taxonomy_name, 'cat' );

			$validate_taxonomy = apply_filters( 'jupitex_raven_valid_sortable_taxonomy', $validate, $taxonomy_name, 'advanced_posts' );

			if ( $validate_taxonomy ) {
				$category_control_id = 'query_' . $taxonomy_name . '_ids';
				$category_name       = $taxonomy_name;
				break;
			}
		}

		if ( empty( $category_name ) ) {
			return;
		}

		$query_args = [
			'taxonomy'     => $category_name,
			'hide_empty'   => true,
			'count'        => false,
			'pad_counts'   => false,
			'hierarchical' => false,
		];

		$posts = $this->settings['query_post_includes'];

		if ( ! empty( $posts ) ) {
			$query_args['object_ids'] = $posts;
		}

		if ( ! empty( $this->settings[ $category_control_id ] ) && empty( $posts ) ) {
			$categories = $this->settings[ $category_control_id ];

			if ( ! empty( $categories ) ) {
				$query_args['hierarchical'] = true;
				$query_args['include']      = $categories;
			}
		}

		$terms_query = get_terms( $query_args );

		if ( is_wp_error( $terms_query ) ) {
			return;
		}

		$sort_number = 0;

		foreach ( $terms_query as $term ) {
			$order_number = $sort_number;
			if ( 'portfolio_category' === $category_name || 'category' === $category_name ) {

				if (
					class_exists( 'acf' ) &&
					! empty( get_field( 'jupiterx_taxonomy_order_number', 'category_' . $term->term_id ) )
				) {
					$order_number = get_field( 'jupiterx_taxonomy_order_number', 'category_' . $term->term_id );
				}

				if ( jupiterx_core()->check_default_settings() && ! empty( $this->term ) && ! empty( get_term_meta( $this->term->term_id, 'jupiterx_taxonomy_order_number', true ) ) ) {
					$order_number = get_term_meta( $this->term->term_id, 'jupiterx_taxonomy_order_number', true );
				}

				if ( array_key_exists( $order_number, $sortable_items ) ) {
					$order_number = $order_number + $sort_number;
				}
			}

			$sortable_items[ $order_number ] = sprintf(
				'<a class="raven-sortable-item" data-category="%1$s" href="#">%2$s</a>',
				esc_attr( $term->term_id ),
				esc_html( $term->name )
			);

			++$sort_number;
		}

		ksort( $sortable_items );

		return '<div class="raven-sortable"><div class="raven-sortable-items">' . wp_kses_post( implode( '', $sortable_items ) ) . '</div><span class="raven-posts-sortable-preloader"></span></div>';
	}

	/**
	 * Render content item after.
	 *
	 * @since 2.5.3
	 */
	public function render_item_after() {
		echo '</div></div></div>';
	}

	/**
	 * Excerpt length.
	 *
	 * @since 2.5.3
	 */
	public function excerpt_length() {
		$excerpt_length = $this->settings['excerpt_length'];

		return intval( $excerpt_length['size'] );
	}

	/**
	 * Excerpt more content.
	 *
	 * @since 2.5.3
	 */
	public function excerpt_more() {
		return '';
	}

	/**
	 * Exclude post when orderby set to Random
	 *
	 * @since 4.12.0
	 */
	protected function exclude_rendered_posts( $posts_per_page ) {
		$posts         = $this->archive_query['rendered_posts'];
		$ajax_page     = $this->archive_query['ajax_page'];
		$exclude_posts = [];

		foreach ( $posts as $post ) {
			if ( $post['page'] === $ajax_page ) {
				continue;
			}
			$exclude_posts = [ ...$exclude_posts, ...$post['ids'] ]; //phpcs:ignore
		}

		$page_count = count( array_unique( $exclude_posts ) );
		if ( $page_count > 0 ) {
			$this->random_exclude_page_count = $page_count / intval( $posts_per_page );
		}

		return $exclude_posts;
	}

	/**
	 * Update or added rendered_posts
	 *
	 * @since 4.12.0
	 */
	protected function update_rendered_posts( $rendered_posts, $page, $ids ) {
		$is_already_rendered = false;

		foreach ( $rendered_posts as $key => $post ) {

			if ( $page === $post['page'] ) {
				$is_already_rendered    = true;
				$rendered_posts[ $key ] = [
					'page' => $page,
					'ids' => $ids,
				];
				break;
			}
		}

		if ( ! $is_already_rendered ) {
			$rendered_posts[] = [
				'page' => $page,
				'ids' => $ids,
			];
		}

		return $rendered_posts;
	}

	/**
	 * Add aditional argument to display related posts to current post.
	 *
	 * @since 4.13.0
	 */
	protected function add_tax_query_argument( $args ) {
		$post_id = get_the_ID();

		if ( ! $post_id ) {
			$post_id = filter_input( INPUT_POST, 'main_post_id' );
		}

		$args['post_type']      = get_post_type( $post_id );
		$args['name']           = '';
		$args['p']              = '';
		$args['post__not_in'][] = $post_id;
		$args['posts_per_page'] = $this->settings['query_posts_per_page'];
		$args['tax_query']      = array(
			'relation' => 'OR',
		);

		$taxonomy_list = $this->get_post_visible_taxonomies( $args['post_type'] );

		if ( $taxonomy_list ) {
			foreach ( $taxonomy_list as $taxonomy ) {
				$args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) ),
					'operator' => 'IN',
				);
			}
		}

		return $args;
	}

	/**
	 * Get visible taxonomies list based on post type.
	 *
	 * @since 4.13.0
	 */
	protected function get_post_visible_taxonomies( $post_type ) {
		$taxonomies         = get_object_taxonomies( $post_type, 'objects' );
		$visible_taxonomies = [];

		foreach ( $taxonomies as $taxonomy ) {
			if (
				$taxonomy->public &&
				$taxonomy->show_ui &&
				$taxonomy->show_in_nav_menus
			) {
				$visible_taxonomies[] = $taxonomy->name;
			}
		}

		return $visible_taxonomies;
	}
}
