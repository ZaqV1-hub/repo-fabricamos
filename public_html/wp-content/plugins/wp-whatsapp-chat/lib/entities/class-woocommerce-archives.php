<?php
namespace QuadLayers\QLWAPP\Entities;

use QuadLayers\WP_Orm\Entity\SingleEntity;

class WooCommerce_Archives extends SingleEntity {
	public $layout             = 'button';
	public $box                = 'no';
	public $position           = 'woocommerce_before_shop_loop_item';
	public $text               = '';
	public $message            = '';
	public $icon               = 'qlwapp-whatsapp-icon';
	public $type               = 'phone';
	public $phone              = QLWAPP_PHONE_NUMBER;
	public $group              = QLWAPP_GROUP_LINK;
	public $developer          = 'no';
	public $rounded            = 'yes';
	public $timefrom           = '00:00';
	public $timeto             = '00:00';
	public $timedays           = array();
	public $timezone           = '';
	public $visibility         = 'readonly';
	public $whatsapp_link_type = 'web';
	public $animation_name     = '';
	public $animation_delay    = '';
	public $position_priority  = 10;
	public $show_in_shop       = 'yes';
	public $show_in_category   = 'yes';
	public $show_in_tag        = 'yes';
	public $show_in_brand      = 'yes';
	public $devices            = 'hide';

	public function __construct() {
		$this->text     = esc_html__( 'Ask about this', 'wp-whatsapp-chat' );
		$this->message  = esc_html__( 'Hello! I\'m browsing *{SITE_TITLE}* and I need assistance finding the right product. Can you help me? {PAGE_URL}', 'wp-whatsapp-chat' );
		$this->timezone = qlwapp_get_timezone_current();
	}
}
