<?php
/**
 * Class: Jet_Blog_Text_Ticker
 * Name: Text Ticker
 * Slug: jet-blog-text-ticker
 */

namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Blog_Text_Ticker extends Jet_Blog_Base {

	public function get_name() {
		return 'jet-blog-text-ticker';
	}

	public function get_title() {
		return esc_html__( 'Text Ticker', 'jet-blog' );
	}

	public function get_icon() {
		return 'jet-blog-icon-text-ticker';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/how-to-display-the-recent-post-titles-in-the-form-of-a-news-ticker-jetblog-text-ticker-widget-overview/';
	}

	public function get_categories() {
		return array( 'cherry' );
	}

	public function get_script_depends() {
		return array( 'jet-slick' );
	}

	public function get_query_conditions( $defaults = array() ) {
		return apply_filters( 'jet-blog/query-conditions', $defaults, $this );
	}

	protected function register_controls() {


		$css_scheme = apply_filters(
			'jet-blog/text-ticker/css-scheme',
			array(
				'box'           => '.jet-text-ticker',
				'widget_title'  => '.jet-text-ticker__title',
				'current_date'  => '.jet-text-ticker__date',
				'typing_cursor' => '.jet-use-typing .jet-text-ticker__item-typed:after',
				'posts'         => '.jet-text-ticker__posts',
				'posts_thumb'   => '.jet-text-ticker__post-thumb',
				'posts_author'  => '.jet-text-ticker__post-author',
				'posts_date'    => '.jet-text-ticker__post-date',
				'posts_link'    => '.jet-text-ticker__item-typed',
			)
		);

		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'General', 'jet-blog' ),
			)
		);

		$this->add_control(
			'block_title',
			array(
				'label'       => esc_html__( 'Widget Title', 'jet-blog' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'     => esc_html__( 'Title Tag', 'jet-blog' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'div',
				'options'   => array(
					'h1'  => esc_html__( 'H1', 'jet-blog' ),
					'h2'  => esc_html__( 'H2', 'jet-blog' ),
					'h3'  => esc_html__( 'H3', 'jet-blog' ),
					'h4'  => esc_html__( 'H4', 'jet-blog' ),
					'h5'  => esc_html__( 'H5', 'jet-blog' ),
					'h6'  => esc_html__( 'H6', 'jet-blog' ),
					'div' => esc_html__( 'DIV', 'jet-blog' ),
				),
			)
		);

		$this->add_control(
			'hide_title_tablet',
			array(
				'label'        => esc_html__( 'Hide Title On Tablets', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'hide_title_mobile',
			array(
				'label'        => esc_html__( 'Hide Title On Mobile', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_current_date',
			array(
				'label'        => esc_html__( 'Show Current Date', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'       => esc_html__( 'Date Format', 'jet-blog' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'l, j F, Y',
				'description' => sprintf( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'jet-blog' ) ),
				'condition' => array(
					'show_current_date' => 'yes',
				),
			)
		);

		$this->add_control(
			$this->_new_icon_prefix . 'date_icon',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => esc_html__( 'Date Icon', 'jet-blog' ),
				'label_block'      => false,
				'skin'             => 'inline',
				'fa4compatibility' => 'date_icon',
				'default'          => array(
					'value'   => 'far fa-clock',
					'library' => 'fa-regular',
				),
				'condition'        => array(
					'show_current_date' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_date_tablet',
			array(
				'label'        => esc_html__( 'Hide Date On Tablets', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition' => array(
					'show_current_date' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_date_mobile',
			array(
				'label'        => esc_html__( 'Hide Date On Mobile', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'show_current_date' => 'yes',
				),
			)
		);

		do_action( 'jet-blog/query-controls', $this, false );

		$this->add_control(
			'use_rss_feed',
			array(
				'label'   => esc_html__( 'Get Data from RSS', 'jet-blog' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'jet-blog' ),
				'label_off' => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator'    => 'before',
				'condition' => array(
					'use_custom_query' => '',
				),
			)
		);

		$this->add_control(
			'rss_feed_url',
			array(
				'label'   => esc_html__( 'RSS Feed URL', 'jet-blog' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '',
				'description' => esc_html__( 'You can use multiple RSS feeds. Past each new feed as the new line.', 'jet-blog' ),
				'condition' => array(
					'use_rss_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'rss_max_items',
			array(
				'label'       => esc_html__( 'Max Items to get from the Each RSS Feed', 'jet-blog' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 10,
				'min'         => 1,
				'max'         => 20,
				'step'        => 1,
				'condition'   => array(
					'use_rss_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'rss_cache_lifespan',
			array(
				'label'       => esc_html__( 'Cache Lifespan in hours', 'jet-blog' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 4,
				'min'         => 1,
				'max'         => 24,
				'step'        => 1,
				'condition'   => array(
					'use_rss_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'posts_num',
			array(
				'label'       => esc_html__( 'Posts Number to Show', 'jet-blog' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 4,
				'min'         => 1,
				'max'         => 20,
				'step'        => 1,
				'separator'   => 'before',
				'condition'   => $this->get_query_conditions( array(
					'use_rss_feed!' => 'yes',
				) ),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'   => esc_html__( 'Post Type', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT2,
				'multiple' => true,
				'default' => array( 'post' ),
				'options' => jet_blog_tools()->get_post_types(),
				'condition'   => $this->get_query_conditions( array(
					'use_rss_feed!' => 'yes',
				) ),
			)
		);

		$this->add_control(
			'custom_query_by',
			array(
				'label'   => esc_html__( 'Query Custom Posts By', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all' => esc_html__( 'All', 'jet-blog' ),
					'ids' => esc_html__( 'IDs', 'jet-blog' ),
				),
				'condition'   => $this->get_query_conditions( array(
					'post_type!' => '',
					'use_rss_feed!' => 'yes',
				) ),
			)
		);

		$this->add_control(
			'include_custom_ids',
			array(
				'label'       => esc_html__( 'Include posts by IDs (10, 22, 19 etc.)', 'jet-blog' ),
				'type'        => 'text',
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'conditions'  => array(
					'terms'    => array(
						array(
							'terms'    => array(
								array(
									'name'     => 'custom_query_by',
									'operator' => '=',
									'value'    => 'ids',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'query_by',
			array(
				'label'   => esc_html__( 'Query Posts By', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'all'      => esc_html__( 'All', 'jet-blog' ),
					'sticky'   => esc_html__( 'Sticky Posts', 'jet-blog' ),
					'category' => esc_html__( 'Categories', 'jet-blog' ),
					'post_tag' => esc_html__( 'Tags', 'jet-blog' ),
					'ids'      => esc_html__( 'IDs', 'jet-blog' ),
				),
				'condition'   => $this->get_query_conditions( array(
					'post_type' => 'post',
					'use_rss_feed!' => 'yes',
				) ),
			)
		);

		$this->add_control(
			'category_ids',
			array(
				'label'       => esc_html__( 'Get posts from categories:', 'jet-blog' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => jet_blog_tools()->get_terms( 'category' ),
				'label_block' => true,
				'multiple'    => true,
				'condition'   => $this->get_query_conditions( array(
					'post_type' => 'post',
					'query_by'  => 'category',
					'use_rss_feed!' => 'yes',
				) ),
			)
		);

		$this->add_control(
			'post_tag_ids',
			array(
				'label'       => esc_html__( 'Get posts from tags:', 'jet-blog' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => jet_blog_tools()->get_terms( 'post_tag' ),
				'label_block' => true,
				'multiple'    => true,
				'condition'   => $this->get_query_conditions( array(
					'post_type' => 'post',
					'query_by'  => 'post_tag',
				) ),
			)
		);

		$this->add_control(
			'include_ids',
			array(
				'label'       => esc_html__( 'Include posts by IDs (10, 22, 19 etc.)', 'jet-blog' ),
				'type'        => 'text',
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'conditions'  => array(
					'terms'    => array(
						array(
							'terms'    => array(
								array(
									'name'     => 'query_by',
									'operator' => '=',
									'value'    => 'ids',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'exclude_ids',
			array(
				'label'       => esc_html__( 'Exclude posts by IDs (eg. 10, 22, 19 etc.)', 'jet-blog' ),
				'description' => esc_html__( 'If this is used with query posts by sticky, it will be ignored. Note: use the %current_id% macros to exclude the current post.', 'jet-blog' ),
				'type'        => 'text',
				'label_block' => true,
				'default'     => '',
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'condition'   => $this->get_query_conditions( array(
					'use_rss_feed!' => 'yes',
				) ),
			)
		);

		$this->add_control(
			'posts_offset',
			array(
				'label'   => esc_html__( 'Posts Offset', 'jet-blog' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
				'condition'   => $this->get_query_conditions( array(
					'use_rss_feed!' => 'yes',
				) ),
			)
		);

		$this->add_control(
			'meta_query',
			array(
				'label'        => esc_html__( 'Filter by Custom Field', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'   => $this->get_query_conditions( array(
					'use_rss_feed!' => 'yes',
				) ),
			)
		);

		$this->add_control(
			'meta_key',
			array(
				'type'        => 'text',
				'label_block' => true,
				'label'       => esc_html__( 'Custom Field Key', 'jet-blog' ),
				'default'     => '',
				'condition'   => $this->get_query_conditions( array(
					'meta_query' => 'yes',
				) ),
			)
		);

		$this->add_control(
			'meta_value',
			array(
				'type'        => 'text',
				'label_block' => true,
				'label'       => esc_html__( 'Custom Field Value', 'jet-blog' ),
				'default'     => '',
				'condition'   => $this->get_query_conditions( array(
					'meta_query' => 'yes',
				) ),
			)
		);

		$this->add_control(
			'title_length',
			array(
				'label'       => esc_html__( 'Post Title Max Length (Words)', 'jet-blog' ),
				'description' => esc_html__( 'Set 0 to show full title', 'jet-blog' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 15,
				'step'        => 1,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'show_thumbnail',
			array(
				'label'        => esc_html__( 'Show Post Thumbnail', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'thumb_size',
			array(
				'label'       => esc_html__( 'Thumbnail Size', 'jet-blog' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 50,
				'min'         => 40,
				'max'         => 100,
				'step'        => 1,
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['posts_thumb'] => 'width: {{VALUE}}px;',
				),
				'condition'   => array(
					'show_thumbnail' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_thumbnail_tablet',
			array(
				'label'        => esc_html__( 'Hide Thumbnail On Tablets', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition' => array(
					'show_thumbnail' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_thumbnail_mobile',
			array(
				'label'        => esc_html__( 'Hide Thumbnail On Mobile', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition' => array(
					'show_thumbnail' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_author',
			array(
				'label'        => esc_html__( 'Show Post Author', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_author_avatar',
			array(
				'label'        => esc_html__( 'Show Author Avatar', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'   => array(
					'show_author' => 'yes',
					'use_rss_feed!' => 'yes',
				),
			)
		);

		$this->add_control(
			'get_avatar_from',
			array(
				'label'        => esc_html__( 'Get Avatar From', 'jet-blog' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'default' => esc_html__( 'Default Avatar', 'jet-blog' ),
					'custom'  => esc_html__( 'Get from Custom Field', 'jet-blog' ),
				),
				'default'      => 'default',
				'condition'    => array(
					'show_author_avatar' => 'yes',
					'show_author' => 'yes',
					'use_rss_feed!' => 'yes',
				),
			)
		);

		$this->add_control(
			'avatar_custom_field',
			array(
				'label'       => esc_html__( 'Field Name', 'jet-blog' ),
				'type'        => Controls_Manager::TEXT,
				'condition'   => array(
					'show_author_avatar' => 'yes',
					'get_avatar_from' => 'custom',
					'show_author' => 'yes',
				),
			)
		);

		$this->add_control(
			'avatar_size',
			array(
				'label'       => esc_html__( 'Avatar Size', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 50,
				),
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'condition'   => array(
					'show_author_avatar' => 'yes',
					'show_author' => 'yes',
					'use_rss_feed!' => 'yes',
				),
			)
		);

		$this->add_control(
			$this->_new_icon_prefix . 'show_author_icon',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => esc_html__( 'Author Icon', 'jet-blog' ),
				'label_block'      => false,
				'skin'             => 'inline',
				'fa4compatibility' => 'show_author_icon',
				'default'          => array(
					'value'   => 'fas fa-user',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'show_author' => 'yes',
					'show_author_avatar!' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_author_tablet',
			array(
				'label'        => esc_html__( 'Hide Author On Tablets', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition' => array(
					'show_author' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_author_mobile',
			array(
				'label'        => esc_html__( 'Hide Author On Mobile', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition' => array(
					'show_author' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'        => esc_html__( 'Show Post Date', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'post_date_format',
			array(
				'label'       => esc_html__( 'Post Date Format', 'jet-blog' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'H:i',
				'description' => sprintf( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'jet-blog' ) ),
				'condition' => array(
					'show_date' => 'yes',
				),
			)
		);

		$this->add_control(
			$this->_new_icon_prefix . 'show_date_icon',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => esc_html__( 'Date Icon', 'jet-blog' ),
				'label_block'      => false,
				'skin'             => 'inline',
				'fa4compatibility' => 'show_date_icon',
				'condition'        => array(
					'show_date' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_post_date_tablet',
			array(
				'label'        => esc_html__( 'Hide Date On Tablets', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition' => array(
					'show_date' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_post_date_mobile',
			array(
				'label'        => esc_html__( 'Hide Date On Mobile', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition' => array(
					'show_date' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider',
			array(
				'label' => esc_html__( 'Slider Settings', 'jet-blog' ),
			)
		);

		$this->add_control(
			'typing_effect',
			array(
				'label'        => esc_html__( 'Typing Effect', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'typing_cursor',
			array(
				'label'       => esc_html__( 'Typing Cursor Char', 'jet-blog' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '_',
				'render_type' => 'ui',
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['typing_cursor'] => 'content: "{{VALUE}}";',
				),
				'condition' => array(
					'typing_effect' => 'yes',
				),
			)
		);

		$this->add_control(
			'multiline_typing',
			array(
				'label'        => esc_html__( 'Multiline Typing', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'typing_effect' => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_autoplay',
			array(
				'label'        => esc_html__( 'Autoplay Posts', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'slider_autoplay_speed',
			array(
				'label'       => esc_html__( 'Autoplay Speed', 'jet-blog' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 5000,
				'min'         => 1000,
				'max'         => 15000,
				'step'        => 1000,
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => esc_html__( 'Show Controls Arrows', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'arrow_type',
			array(
				'label'       => esc_html__( 'Select Control Arrows Type', 'jet-blog' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fa fa-angle-left',
				'options'     => jet_blog_tools()->get_available_prev_arrows_list(),
				'condition'   => array(
					'show_arrows'      => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->_start_controls_section(
			'section_box_style',
			array(
				'label'      => esc_html__( 'Container', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			),
			100
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'container_bg',
				'selector' => '{{WRAPPER}} ' . $css_scheme['box'],
			),
			100
		);

		$this->_add_responsive_control(
			'container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['box'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			100
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'container_border',
				'label'          => esc_html__( 'Border', 'jet-blog' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['box'],
			),
			100
		);

		$this->_add_responsive_control(
			'container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['box'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			100
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['box'],
			),
			100
		);

		$this->_end_controls_section( 100 );

		$this->_start_controls_section(
			'section_title_style',
			array(
				'label'      => esc_html__( 'Widget Title', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['widget_title'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['widget_title'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'title_bg',
				'selector' => '{{WRAPPER}} ' . $css_scheme['widget_title'],
			),
			75
		);

		$this->_add_responsive_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['widget_title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'title_border',
				'label'          => esc_html__( 'Border', 'jet-blog' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['widget_title'],
			),
			75
		);

		$this->_add_responsive_control(
			'title_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['widget_title'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'title_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['widget_title'],
			),
			100
		);

		$this->_add_control(
			'show_title_pointer',
			array(
				'label'        => esc_html__( 'Show Title Pointer', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			),
			75
		);

		$this->_add_control(
			'title_pointer_color',
			array(
				'label'     => esc_html__( 'Pointer Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cccccc',
				'render_type' => 'ui',
				'condition'   => array(
					'show_title_pointer' => 'yes',
				),
			),
			75
		);

		$this->_add_control(
			'title_pointer_height',
			array(
				'label'      => esc_html__( 'Pointer Height', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 4,
						'max' => 200,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'render_type' => 'ui',
				'condition'   => array(
					'show_title_pointer' => 'yes',
				),
			),
			75
		);

		$this->_add_control(
			'title_pointer_width',
			array(
				'label'      => esc_html__( 'Pointer Width', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 4,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 5,
				),
				'render_type' => 'ui',
				'condition'   => array(
					'show_title_pointer' => 'yes',
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} ' . $css_scheme['widget_title'] . ':after' => 'position:absolute;content:"";width: 0; height: 0; border-style: solid; border-width: {{title_pointer_height.SIZE}}{{title_pointer_height.UNIT}} 0 {{title_pointer_height.SIZE}}{{title_pointer_height.UNIT}} {{SIZE}}{{UNIT}}; border-color: transparent transparent transparent {{title_pointer_color.VALUE}};left: 100%;top:50%;margin-top:-{{title_pointer_height.SIZE}}{{title_pointer_height.UNIT}};z-index: 999;',
					'.rtl {{WRAPPER}} ' . $css_scheme['widget_title'] . ':after' => 'position:absolute;content:"";width: 0; height: 0; border-style: solid; border-width: {{title_pointer_height.SIZE}}{{title_pointer_height.UNIT}} {{SIZE}}{{UNIT}} {{title_pointer_height.SIZE}}{{title_pointer_height.UNIT}} 0; border-color: transparent {{title_pointer_color.VALUE}} transparent transparent;right: 100%;top:50%;margin-top:-{{title_pointer_height.SIZE}}{{title_pointer_height.UNIT}};z-index: 999;',
				),
			),
			75
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_date_style',
			array(
				'label'      => esc_html__( 'Current Date', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_control(
			'current_date_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['current_date'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'current_date_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['current_date'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'current_date_bg',
				'selector' => '{{WRAPPER}} ' . $css_scheme['current_date'],
			),
			75
		);

		$this->_add_responsive_control(
			'current_date_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['current_date'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'current_date_border',
				'label'          => esc_html__( 'Border', 'jet-blog' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['current_date'],
			),
			75
		);

		$this->_add_responsive_control(
			'current_date_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['current_date'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'current_date_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['current_date'],
			),
			100
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_posts_style',
			array(
				'label'      => esc_html__( 'Posts', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'posts_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['posts'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'posts_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['posts'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_control(
			'posts_thumb',
			array(
				'label'     => esc_html__( 'Thumbnail', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			50
		);

		$this->_add_responsive_control(
			'thumb_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['posts_thumb'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'thumb_border',
				'label'          => esc_html__( 'Border', 'jet-blog' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['posts_thumb'],
			),
			75
		);

		$this->_add_responsive_control(
			'thumb_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['posts_thumb'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'thumb_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['posts_thumb'],
			),
			100
		);

		$this->_add_control(
			'posts_author',
			array(
				'label'     => esc_html__( 'Author', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'posts_author_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['posts_author'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'posts_author_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['posts_author'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_control(
			'meta_avatar_styles',
			array(
				'label'     => esc_html__( 'Author Avatar', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => array(
					'show_author_avatar' => 'yes',
					'show_author' => 'yes',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'meta_avatar_border',
				'label'          => esc_html__( 'Border', 'jet-blog' ),
				'placeholder'    => '1px',
				'condition'   => array(
					'show_author_avatar' => 'yes',
					'show_author' => 'yes',
				),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['posts_author'] . ' img',
			),
			75
		);


		$this->_add_responsive_control(
			'meta_avatar_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'condition'   => array(
					'show_author_avatar' => 'yes',
					'show_author' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['posts_author'] . ' img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_control(
			'posts_date',
			array(
				'label'     => esc_html__( 'Date', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'posts_date_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['posts_date'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'posts_date_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['posts_date'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_control(
			'posts_link',
			array(
				'label'     => esc_html__( 'Link', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'posts_link_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['posts_link'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'posts_link_color_hover',
			array(
				'label'     => esc_html__( 'Color Hover', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['posts_link'] . ':hover' => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'posts_link_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['posts_link'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_paging_arrows',
			array(
				'label'      => esc_html__( 'Paging Arrows', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_start_controls_tabs( 'tabs_arrows_style', 50 );

		$this->_start_controls_tab(
			'tab_prev',
			array(
				'label' => esc_html__( 'Normal', 'jet-blog' ),
			),
			50
		);

		$this->_add_group_control(
			\Jet_Blog_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'arrows_style',
				'label'          => esc_html__( 'Arrows Style', 'jet-blog' ),
				'selector'       => '{{WRAPPER}} .jet-blog-arrow',
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
			),
			50
		);

		$this->_end_controls_tab( 50 );

		$this->_start_controls_tab(
			'tab_next_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-blog' ),
			),
			50
		);

		$this->_add_group_control(
			\Jet_Blog_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'arrows_style_hover',
				'label'          => esc_html__( 'Arrows Style', 'jet-blog' ),
				'selector'       => '{{WRAPPER}} .jet-blog-arrow:hover',
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
			),
			50
		);

		$this->_end_controls_tab( 50 );

		$this->_end_controls_tabs( 50 );

		$this->_add_control(
			'prev_arrow_position',
			array(
				'label'     => esc_html__( 'Prev Arrow Position', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'prev_vert_position',
			array(
				'label'   => esc_html__( 'Vertical Postition by', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => esc_html__( 'Top', 'jet-blog' ),
					'bottom' => esc_html__( 'Bottom', 'jet-blog' ),
				),
			),
			25
		);

		$this->_add_responsive_control(
			'prev_top_position',
			array(
				'label'      => esc_html__( 'Top Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_vert_position' => 'top',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-prev' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'prev_bottom_position',
			array(
				'label'      => esc_html__( 'Bottom Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_vert_position' => 'bottom',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-prev' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				),
			),
			25
		);

		$this->_add_control(
			'prev_hor_position',
			array(
				'label'   => esc_html__( 'Horizontal Postition by', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => esc_html__( 'Left', 'jet-blog' ),
					'right' => esc_html__( 'Right', 'jet-blog' ),
				),
			),
			25
		);

		$this->_add_responsive_control(
			'prev_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_hor_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-prev' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'prev_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_hor_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-prev' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			),
			25
		);

		$this->_add_control(
			'next_arrow_position',
			array(
				'label'     => esc_html__( 'Next Arrow Position', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'next_vert_position',
			array(
				'label'   => esc_html__( 'Vertical Postition by', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => esc_html__( 'Top', 'jet-blog' ),
					'bottom' => esc_html__( 'Bottom', 'jet-blog' ),
				),
			),
			25
		);

		$this->_add_responsive_control(
			'next_top_position',
			array(
				'label'      => esc_html__( 'Top Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_vert_position' => 'top',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-next' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'next_bottom_position',
			array(
				'label'      => esc_html__( 'Bottom Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_vert_position' => 'bottom',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-next' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				),
			),
			25
		);

		$this->_add_control(
			'next_hor_position',
			array(
				'label'   => esc_html__( 'Horizontal Postition by', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'left'  => esc_html__( 'Left', 'jet-blog' ),
					'right' => esc_html__( 'Right', 'jet-blog' ),
				),
			),
			25
		);

		$this->_add_responsive_control(
			'next_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_hor_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-next' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'next_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_hor_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-next' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			),
			25
		);

		$this->_end_controls_section();

	}

	protected function render() {

		$this->_context = 'render';
		$this->_get_posts();

		$this->_open_wrap();
		include $this->_get_global_template( 'index' );
		$this->_close_wrap();
	}

	/**
	 * Query posts or fetch RSS feed items
	 *
	 * @return void
	 */
	public function _get_posts() {

		$settings  = $this->get_settings_for_display();

		if ( 'yes' === $settings['use_rss_feed'] && ! empty( $settings['rss_feed_url'] ) ) {
			$rss_items = $this->_fetch_rss_feed_items( $settings );
			$this->_set_query( $rss_items );
		} else {
		$num       = $settings['posts_num'];
		$post_types = ! empty( $settings['post_type'] ) ? ( array ) $settings['post_type'] : array('post');
		$include   = ! empty( $settings['include_ids'] ) ? $settings['include_ids'] : '';
		$exclude   = ! empty( $settings['exclude_ids'] ) ? $settings['exclude_ids'] : '';
		$offset    = ! empty( $settings['posts_offset'] ) ? absint( $settings['posts_offset'] ) : 0;

		$query_args = array(
			'post_type'           => $post_types,
			'posts_per_page'      => $num,
			'ignore_sticky_posts' => true,
			'post_status'         => 'publish',
			'paged'               => 1,
		);

		$include_ids = array();

		if ( ! empty( $settings['custom_query_by'] ) ) {

			$custom_query_by = $settings['custom_query_by'];

			if ( 'ids' === $custom_query_by ) {
				$include_custom_ids = ! empty( $settings['include_custom_ids'] ) ? $settings['include_custom_ids'] : '';
				if ( ! empty( $include_custom_ids ) ) {
					$include_ids = array_merge( $include_ids, explode( ',', str_replace( ' ', '', $include_custom_ids ) ) );
				}
			}
		}

		if ( in_array( 'post', $post_types, true ) ) {

			$query_by = $settings['query_by'];

			switch ( $query_by ) {
				case 'sticky':

					$query_args['post__in'] = get_option( 'sticky_posts' );

					break;

				case 'category':
				case 'post_tag':

				if ( isset( $settings[ $query_by . '_ids' ] ) ) {
					$terms_ids = $settings[ $query_by . '_ids' ];

					if ( ! is_array( $terms_ids ) ) {
						$terms_ids = array( $terms_ids );
					}

					$terms_ids = array_filter( $terms_ids );
				} else {
					$terms_ids = array();
				}


					if ( ! empty( $terms_ids ) ) {
						$query_args['tax_query'] = array(
							array(
								'taxonomy' => $query_by,
								'field'    => 'term_id',
								'terms'    => $terms_ids,
							),
						);
					}

					break;

				case 'ids':

					if ( ! empty( $include ) ) {
						$include_ids = array_merge( $include_ids, explode( ',', str_replace( ' ', '', $include ) ) );
					}

					break;

			}
		}

		if ( ! empty( $include_ids ) ) {
			$query_args['post__in'] = $include_ids;
		}

		if ( ! empty( $exclude ) && empty( $query_args['post__in'] ) ) {
			$exclude                    = $this->render_macros( $exclude );
			$exclude_ids                = explode( ',', str_replace( ' ', '', $exclude ) );
			$query_args['post__not_in'] = $exclude_ids;
		}

		if ( $offset ) {
			$query_args['offset'] = $offset;
		}

		if ( isset( $settings['meta_query'] ) && 'yes' === $settings['meta_query'] ) {

			$meta_key   = ! empty( $settings['meta_key'] ) ? esc_attr( $settings['meta_key'] ) : false;
			$meta_value = ! empty( $settings['meta_value'] ) ? esc_attr( $settings['meta_value'] ) : '';

			if ( ! empty( $meta_key ) ) {
				$query_args['meta_key']   = $meta_key;
				$query_args['meta_value'] = $meta_value;
			}

		}

		/**
		 * Filter query arguments before posts requested
		 *
		 * @var array
		 */
		$query_args = apply_filters( 'jet-blog/text-ticker/query-args', $query_args, $this );

		$query_args = $this->sanitize_query_args( $query_args );

		$posts = apply_filters( 'jet-blog/pre-query', false, $settings, $query_args, $this );

		if ( false === $posts ) {
			$query = new \WP_Query( $query_args );
			$posts = ! empty( $query->posts ) ? $query->posts : array();
		}

		$this->_set_query( $posts );

		}
	}

	/**
	 * Fetch RSS feed items
	 *
	 * @param array $settings Settings array.
	 * @return array $rss_items Array of formatted RSS feed items.
	 */
	private function _fetch_rss_feed_items( $settings ) {

		$feed_urls            = array_filter( array_map( 'trim', explode( "\n", $settings['rss_feed_url'] ) ) );
		$max_items_per_feed   = ! empty( $settings['rss_max_items'] ) ? absint( $settings['rss_max_items'] ) : 10;
		$all_rss_feeds_string = implode( ',', $feed_urls );
		$cache_key            = 'jet_blog_rss_feeds_' . md5( $all_rss_feeds_string . '_' . $max_items_per_feed );
		$cache_lifespan       = ! empty( $settings['rss_cache_lifespan'] ) ? absint( $settings['rss_cache_lifespan'] ) * HOUR_IN_SECONDS : 4 * HOUR_IN_SECONDS;
		$rss_items            = get_transient( $cache_key );

		if ( false === $rss_items ) {
			$rss_items = array();

			foreach ($feed_urls as $feed_url) {
				$rss = fetch_feed( $feed_url );

				if ( ! is_wp_error( $rss ) ) {
					$maxitems = $rss->get_item_quantity( $max_items_per_feed );
					$items    = $rss->get_items( 0, $maxitems );

					foreach ( $items as $item ) {
						$rss_items[] = array(
							'item_title'     => $item->get_title(),
							'item_url'       => $item->get_link(),
							'item_author'    => $item->get_author() ? $item->get_author()->get_name() : '',
							'item_date'      => $item->get_date(),
							'item_thumbnail' => $this->_get_item_thumbnail( $item ),
						);
					}
				}
			}

			set_transient( $cache_key, $rss_items, $cache_lifespan );
		}

		return $rss_items;
	}

	/**
	 * Get thumbnail from RSS item
	 *
	 * @param SimplePie_Item $item RSS item.
	 * @return string Thumbnail URL or empty string.
	 */
	private function _get_item_thumbnail( $item ) {

		foreach ( $item->get_enclosures() as $enclosure) {
			$type = $enclosure->get_type();

			if ( is_string( $type ) && strpos( $type, 'image' ) !== false ) {
				return $enclosure->get_link();
			}

		}

		return '';

	}

	/**
	 * Slider attributes.
	 *
	 * @return void
	 */
	public function _slider_atts() {

		$slider_attributes = array(
			'slidesToShow'   => 1,
			'slidesToScroll' => 1,
			'fade'           => true,
		);

		$settings                           = $this->get_settings();
		$slider_attributes['arrows']        = filter_var( $settings['show_arrows'], FILTER_VALIDATE_BOOLEAN );
		$slider_attributes['prevArrow']     = jet_blog_tools()->get_carousel_arrow( $settings['arrow_type'], 'prev' );
		$slider_attributes['nextArrow']     = jet_blog_tools()->get_carousel_arrow( $settings['arrow_type'], 'next' );
		$autoplay                           = isset( $settings['slider_autoplay'] ) ? $settings['slider_autoplay'] : true;
		$slider_attributes['autoplay']      = filter_var( $settings['slider_autoplay'], FILTER_VALIDATE_BOOLEAN );
		$slider_attributes['autoplaySpeed'] = ! empty( $settings['slider_autoplay_speed'] ) ? absint( $settings['slider_autoplay_speed'] ) : 5000;

		$slider_attributes = apply_filters( 'jet-blog/text-ticker/slider-settings', $slider_attributes );

		printf( "data-slider-atts='%s'", json_encode( $slider_attributes ) );

	}

	/**
	 * Show post meta or RSS feed item meta if allowed
	 *
	 * @param  array $settings Settings.
	 * @return void
	 */
	public function _post_author( $settings ) {
		global $post;

		$show = isset( $settings['show_author'] ) ? $settings['show_author'] : '';

		if ( 'yes' !== $show ) {
			return;
		}

		$avatar_html = '';
		$author_name = '';

		if ( is_array( $post ) && ! empty( $post['item_author'] ) ) {

			$author_name = $post['item_author'];

		} else {

			if ( isset($settings['use_rss_feed']) && 'yes' === $settings['use_rss_feed']) {
				return;
			}

			if ( isset( $settings['show_author_avatar'] ) && 'yes' === $settings['show_author_avatar'] ) {
				$avatar_size_value = $settings['avatar_size']['size'] ?? 50;
				$author_id = get_the_author_meta('ID');
				$avatar_source = $settings['get_avatar_from'];
				$source_meta_field = $settings['avatar_custom_field'] ?? false;
				$avatar_html = jet_blog_tools()->render_avatar( $author_id, $avatar_size_value, $avatar_source, $source_meta_field );
			}

			$author_name = get_the_author();
		}

		$icon_format = apply_filters(
			'jet-blog/text-ticker/post-author/icon-format',
			'<span class="jet-text-ticker__post-author-icon jet-blog-icon">%s</span>'
		);

		$icon_html = $avatar_html ? $avatar_html : $this->_get_icon( 'show_author_icon', $settings, $icon_format );

		$hide_classes = '';

		if ( ! empty( $settings['hide_author_tablet'] ) && 'yes' === $settings['hide_author_tablet'] ) {
			$hide_classes .= ' jet-blog-hidden-tablet';
		}

		if ( ! empty( $settings['hide_author_mobile'] ) && 'yes' === $settings['hide_author_mobile'] ) {
			$hide_classes .= ' jet-blog-hidden-mobile';
		}

        if ( ! is_rtl() ) {
            printf(
                '<div class="jet-text-ticker__post-author%3$s">%1$s %2$s</div>',
                $icon_html, // phpcs:ignore
                esc_html( $author_name ),
                $hide_classes // phpcs:ignore
            );
        } else {
            printf(
                '<div class="jet-text-ticker__post-author%3$s">%1$s %2$s</div>',
                esc_html( $author_name ),
                $icon_html, // phpcs:ignore
                $hide_classes // phpcs:ignore
            );
        }
	}

	/**
	 * Show post date or RSS feed item date if allowed
	 *
	 * @param  array $settings Settings.
	 *
	 * @return void
	 */
	public function _post_date( $settings ) {
		global $post;

		$show = isset( $settings['show_date'] ) ? $settings['show_date'] : '';

		if ( 'yes' !== $show ) {
			return;
		}

		$icon_format = apply_filters(
			'jet-blog/text-ticker/post-date/icon-format',
			'<span class="jet-text-ticker__post-date-icon jet-blog-icon">%s</span>'
		);

		$icon_html = $this->_get_icon( 'show_date_icon', $settings, $icon_format );

		$date_format = isset( $settings['post_date_format'] ) ? $settings['post_date_format'] : 'H:i';

		$hide_classes = '';

		if ( ! empty( $settings['hide_post_date_tablet'] ) && 'yes' === $settings['hide_post_date_tablet'] ) {
			$hide_classes .= ' jet-blog-hidden-tablet';
		}

		if ( ! empty( $settings['hide_post_date_mobile'] ) && 'yes' === $settings['hide_post_date_mobile'] ) {
			$hide_classes .= ' jet-blog-hidden-mobile';
		}

		if ( is_array( $post ) && ! empty( $post['item_date'] ) ) {
			$date = date( $date_format, strtotime( $post['item_date'] ) );
		} elseif ( ! isset( $settings['use_rss_feed'] ) || 'yes' !== $settings['use_rss_feed'] ) {
			$date = get_the_time( $date_format );
		}

		if ( empty( $date ) ) {
			return;
		}

        if ( ! is_rtl() ) {
            printf(
                '<div class="jet-text-ticker__post-date%3$s">%1$s %2$s</div>',
                $icon_html, // phpcs:ignore
                esc_html( $date ),
                $hide_classes // phpcs:ignore
            );
        } else {
            printf(
                '<div class="jet-text-ticker__post-date%3$s">%1$s %2$s</div>',
                esc_html( $date ),
                $icon_html, // phpcs:ignore
                $hide_classes // phpcs:ignore
            );
        }
	}

	/**
	 * Get widget title
	 *
	 * @param  array $settings Settings.
	 * @return void
	 */
	public function _get_widget_title( $settings ) {

		if ( empty( $settings['block_title'] ) ) {
			return;
		}

		$tag = ! empty( $settings['title_tag'] ) ? jet_blog_tools()->validate_html_tag( $settings['title_tag'] ) : 'div';

		$hide_classes = '';

		if ( ! empty( $settings['hide_title_tablet'] ) && 'yes' === $settings['hide_title_tablet'] ) {
			$hide_classes .= ' jet-blog-hidden-tablet';
		}

		if ( ! empty( $settings['hide_title_mobile'] ) && 'yes' === $settings['hide_title_mobile'] ) {
			$hide_classes .= ' jet-blog-hidden-mobile';
		}

		printf(
			'<%1$s class="jet-text-ticker__title%3$s">%2$s</%1$s>',
			$tag, wp_kses_post( $settings['block_title'] ), $hide_classes // phpcs:ignore
		);

	}

	/**
	 * Show current date id allowed
	 *
	 * @param  array $settings Settings.
	 * @return void
	 */
	public function _get_current_date( $settings ) {

		if ( empty( $settings['show_current_date'] ) ) {
			return;
		}

		$format = ! empty( $settings['date_format'] ) ? esc_attr( $settings['date_format'] ) : 'l, j F, Y';

		$icon_format = apply_filters(
			'jet-blog/text-ticker/current-date/icon-format',
			'<span class="jet-text-ticker__date-icon jet-blog-icon">%s</span>'
		);

		$icon_html = $this->_get_icon( 'date_icon', $settings, $icon_format );

		$result_format = apply_filters(
			'jet-blog/text-ticker/current-date/format',
			'<div class="jet-text-ticker__date%3$s">%1$s%2$s</div>'
		);

		$hide_classes = '';

		if ( ! empty( $settings['hide_date_tablet'] ) && 'yes' === $settings['hide_date_tablet'] ) {
			$hide_classes .= ' jet-blog-hidden-tablet';
		}

		if ( ! empty( $settings['hide_date_mobile'] ) && 'yes' === $settings['hide_date_mobile'] ) {
			$hide_classes .= ' jet-blog-hidden-mobile';
		}

		printf( $result_format, wp_kses_post( $icon_html ), date_i18n( $format ), $hide_classes ); // phpcs:ignore
	}

	/**
	 * Show post thumbnail or RSS feed item thumbnail if allowed
	 *
	 * @param  array $settings Settings.
	 * @return void
	 */
	public function _post_thumbnail( $settings ) {
		global $post;

		$show_thumbnail = isset( $settings['show_thumbnail'] ) ? $settings['show_thumbnail'] : '';

		if ( 'yes' !== $show_thumbnail ) {
			return;
		}

		$size = isset( $settings['thumb_size'] ) ? absint( $settings['thumb_size'] ) : 50;

		$class = 'jet-text-ticker__post-thumb';

		if ( ! empty( $settings['hide_thumbnail_tablet'] ) && 'yes' === $settings['hide_thumbnail_tablet'] ) {
			$class .= ' jet-blog-hidden-tablet';
		}

		if ( ! empty( $settings['hide_thumbnail_mobile'] ) && 'yes' === $settings['hide_thumbnail_mobile'] ) {
			$class .= ' jet-blog-hidden-mobile';
		}

		if ( is_array( $post ) && ! empty( $post['item_thumbnail'] ) ) {
			$thumbnail_url = $post['item_thumbnail'];

			echo '<img src="' . esc_url( $thumbnail_url ) . '" class="' . esc_attr( $class ) . '" alt="' . esc_attr( $post['item_title'] ) . '" title="' . esc_attr( $post['item_title'] ) . '" width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" />';
		} else {
			if ( has_post_thumbnail() ) {
				the_post_thumbnail(
					array( $size, $size ),
					array(
						'class' => $class,
						'alt'   => esc_attr( get_the_title() ),
						'title' => esc_attr( get_the_title() ),
					)
				);
			}
		}

	}

	/**
	 * Show post title or RSS feed item title
	 *
	 * @param  array $settings Settings.
	 * @return void
	 */
	public function _post_title(  $settings ) {
		global $post;

		$title_format = apply_filters(
			'jet-blog/text-ticker/post-title/format',
			'<a href="%1$s" class="jet-text-ticker__item-typed"><span class="jet-text-ticker__item-typed-inner"%3$s>%2$s</span></a>'
		);

		if ( is_array( $post ) && ! empty( $post['item_title'] ) ) {
			$title = $post['item_title'];
			$link  = $post['item_url'];
		} else {
			$title = get_the_title();
			$link  = get_the_permalink();
		}

		if ( ! empty( $settings['title_length'] ) ) {
			$length = absint( $settings['title_length'] );
			$title  = wp_trim_words( $title, $length, '...' );
		}

		$attr = '';

		if ( isset( $settings['typing_effect'] ) && filter_var( $settings['typing_effect'], FILTER_VALIDATE_BOOLEAN ) ) {
			$attr = ' data-typing-text="' . esc_attr( $title ) . '"';
		}

		printf(
			$title_format, // phpcs:ignore
			esc_url( $link ),
			esc_html( $title ),
			$attr // phpcs:ignore
		);
	}

}
