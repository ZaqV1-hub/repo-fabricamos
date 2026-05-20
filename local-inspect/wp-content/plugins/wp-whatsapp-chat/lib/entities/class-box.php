<?php
namespace QuadLayers\QLWAPP\Entities;

use QuadLayers\WP_Orm\Entity\SingleEntity;

class Box extends SingleEntity {
	public $enable              = 'yes';
	public $auto_open           = 'no';
	public $auto_delay_open     = 1000;
	public $lazy_load           = 'no';
	public $allow_outside_close = 'no';
	public $header              = '
								<p style="line-height: 1;text-align: start"><span style="font-size: 12px;vertical-align: bottom;letter-spacing: -0.2px;opacity: 0.8;margin: 5px 0 0 1px">Powered by</span></p>
								<p style="line-height: 1;text-align: start"><a style="font-size: 24px;line-height: 34px;font-weight: bold;text-decoration: none;color: white" href="https://quadlayers.com/products/whatsapp-chat/?utm_source=qlwapp_plugin&utm_medium=header&utm_campaign=social-chat" target="_blank" rel="noopener">Social Chat</a></p>';
	public $footer              = '<p style="text-align: start;">Need help? Our team is just a message away</p>';
	public $response;
	public $consent_message;
	public $consent_enabled = 'no';

	public function __construct() {
		$this->response        = esc_html__( 'Write a response', 'wp-whatsapp-chat' );
		$this->consent_message = esc_html__( 'I accept cookies and privacy policy.', 'wp-whatsapp-chat' );
	}
}
