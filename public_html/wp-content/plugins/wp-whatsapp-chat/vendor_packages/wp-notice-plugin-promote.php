<?php

if ( class_exists( 'QuadLayers\\WP_Notice_Plugin_Promote\\Load' ) ) {
	add_action('init', function() {
		/**
		 *  Promote constants
		 */
		define( 'QLWAPP_PROMOTE_LOGO_SRC', plugins_url( '/assets/backend/img/logo.jpg', QLWAPP_PLUGIN_FILE ) );
		/**
		 * Notice review
		 */
		define( 'QLWAPP_PROMOTE_REVIEW_URL', 'https://wordpress.org/support/plugin/wp-whatsapp-chat/reviews/?filter=5#new-post' );
		/**
		 * Notice premium sell
		 */
		define( 'QLWAPP_PROMOTE_PREMIUM_SELL_SLUG', 'wp-whatsapp-chat-pro' );
		define( 'QLWAPP_PROMOTE_PREMIUM_SELL_NAME', 'Social Chat PRO' );
		define( 'QLWAPP_PROMOTE_PREMIUM_SELL_URL', 'https://quadlayers.com/products/whatsapp-chat/?utm_source=qlwapp_plugin&utm_medium=dashboard_notice&utm_campaign=premium_upgrade&utm_content=more_info_button' );
		define( 'QLWAPP_PROMOTE_PREMIUM_INSTALL_URL', 'https://quadlayers.com/products/whatsapp-chat/?utm_source=qlwapp_plugin&utm_medium=dashboard_notice&utm_campaign=premium_upgrade&utm_content=more_info_button' );
		/**
		 * Notice premium sell - WhatsApp Bots
		 */
		define( 'QLWAPP_PROMOTE_PREMIUM_BOTS_SLUG', 'wp-whatsapp-chat-bots' );
		define( 'QLWAPP_PROMOTE_PREMIUM_BOTS_NAME', 'Social Chat Bots' );
		define(
			'QLWAPP_PROMOTE_PREMIUM_BOTS_TITLE',
			wp_kses(
				sprintf(
					'<h3 style="margin:0">%s</h3>',
					esc_html__( 'Overwhelmed by WhatsApp Support Requests?', 'wp-whatsapp-chat' )
				),
				array(
					'h3' => array(
						'style' => array()
					)
				)
			)
		);
		define( 'QLWAPP_PROMOTE_PREMIUM_BOTS_DESCRIPTION', esc_html__( 'Transform your customer service with AI-powered conversational bots. No coding required—build automated flows in minutes with our visual drag-and-drop builder. Capture leads, qualify customers, and scale support without losing the human touch.', 'wp-whatsapp-chat' ) );
		define( 'QLWAPP_PROMOTE_PREMIUM_BOTS_URL', 'https://quadlayers.com/landing/whatsapp-chat-bots/?utm_source=qlwapp_plugin&utm_medium=dashboard_notice&utm_campaign=premium_bots&utm_content=more_info_button' );
		define( 'QLWAPP_PROMOTE_PREMIUM_BOTS_INSTALL_LABEL', esc_html__( 'Claim Early Access', 'wp-whatsapp-chat' ) );
		/**
		 * Notice premium sell - Founder Users Program
		 */
		define( 'QLWAPP_PROMOTE_FOUNDER_PROGRAM_SLUG', 'wp-whatsapp-chat-founder-program' );
		define( 'QLWAPP_PROMOTE_FOUNDER_PROGRAM_NAME', 'Founder Users Program' );
		define(
			'QLWAPP_PROMOTE_FOUNDER_PROGRAM_TITLE',
			wp_kses(
				sprintf(
					'<h3 style="margin:0">%s</h3>',
					esc_html__( 'Shape the Future of Social Chat', 'wp-whatsapp-chat' )
				),
				array(
					'h3' => array(
						'style' => array()
					)
				)
			)
		);
		define( 'QLWAPP_PROMOTE_FOUNDER_PROGRAM_DESCRIPTION', esc_html__( 'Join our Founder Users Program and help build Social Chat Pro alongside our team. Get lifetime pricing, influence our roadmap, and access new features before public release. The best software is built with users, not just for them.', 'wp-whatsapp-chat' ) );
		define( 'QLWAPP_PROMOTE_FOUNDER_PROGRAM_URL', 'https://quadlayers.com/landing/whatsapp-chat-founder-users-program/?utm_source=qlwapp_plugin&utm_medium=dashboard_notice&utm_campaign=founder_program&utm_content=more_info_button' );
		define( 'QLWAPP_PROMOTE_FOUNDER_PROGRAM_INSTALL_LABEL', esc_html__( 'Lock In Lifetime Pricing', 'wp-whatsapp-chat' ) );


		new \QuadLayers\WP_Notice_Plugin_Promote\Load(
			QLWAPP_PLUGIN_FILE,
			array(
				array(
					'type'               => 'ranking',
					'notice_delay'       => 0,
					'notice_logo'        => QLWAPP_PROMOTE_LOGO_SRC,
					'notice_title'       => wp_kses(
						sprintf(
							'<h3 style="margin:0">%s</h3>',
							esc_html__( 'Enjoying Social Chat?', 'wp-whatsapp-chat' )
						),
						array(
							'h3' => array(
								'style' => array()
							)
						)
					),
					'notice_description' => esc_html__( 'A quick 5-star review helps us keep improving the plugin and supporting users like you. It only takes 2 seconds — thank you!', 'wp-whatsapp-chat' ),
					'notice_link'        => QLWAPP_PROMOTE_REVIEW_URL,
					'notice_more_link'   => 'https://quadlayers.com/account/support/?utm_source=qlwapp_plugin&utm_medium=dashboard_notice&utm_campaign=support&utm_content=report_bug_button',
					'notice_more_label'  => esc_html__(
						'Report a bug',
						'wp-whatsapp-chat'
					),
				),
				array(
					'plugin_slug'        => QLWAPP_PROMOTE_PREMIUM_SELL_SLUG,
					'plugin_install_link'   => QLWAPP_PROMOTE_PREMIUM_INSTALL_URL,
					'plugin_install_label'  => esc_html__(
						'Purchase Now',
						'wp-whatsapp-chat'
					),
					'notice_delay'       => WEEK_IN_SECONDS,
					'notice_logo'        => QLWAPP_PROMOTE_LOGO_SRC,
					'notice_title'       => wp_kses(
						sprintf(
							'<h3 style="margin:0">%s</h3>',
							esc_html__( 'Save 20% today!', 'wp-whatsapp-chat' )
						),
						array(
							'h3' => array(
								'style' => array()
							)
						)
					),
					'notice_description' => sprintf(
						esc_html__(
							'Today we have a special gift for you. Use the coupon code %1$s within the next 48 hours to receive a %2$s discount on the premium version of the %3$s plugin.',
							'wp-whatsapp-chat'
						),
						'ADMINPANEL20%',
						'20%',
						QLWAPP_PROMOTE_PREMIUM_SELL_NAME
					),
					'notice_more_link'   => QLWAPP_PROMOTE_PREMIUM_SELL_URL,
				),
				array(
					'plugin_slug'           => QLWAPP_PROMOTE_PREMIUM_BOTS_SLUG,
					'plugin_install_link'   => QLWAPP_PROMOTE_PREMIUM_BOTS_URL,
					'plugin_install_label'  => QLWAPP_PROMOTE_PREMIUM_BOTS_INSTALL_LABEL,
					'notice_delay'          => MONTH_IN_SECONDS * 3,
					'notice_logo'           => QLWAPP_PROMOTE_LOGO_SRC,
					'notice_title'          => QLWAPP_PROMOTE_PREMIUM_BOTS_TITLE,
					'notice_description'    => QLWAPP_PROMOTE_PREMIUM_BOTS_DESCRIPTION,
					'notice_more_link'      => QLWAPP_PROMOTE_PREMIUM_BOTS_URL
				),
				array(
					'plugin_slug'           => QLWAPP_PROMOTE_FOUNDER_PROGRAM_SLUG,
					'plugin_install_link'   => QLWAPP_PROMOTE_FOUNDER_PROGRAM_URL,
					'plugin_install_label'  => QLWAPP_PROMOTE_FOUNDER_PROGRAM_INSTALL_LABEL,
					'notice_delay'          => MONTH_IN_SECONDS * 6,
					'notice_logo'           => QLWAPP_PROMOTE_LOGO_SRC,
					'notice_title'          => QLWAPP_PROMOTE_FOUNDER_PROGRAM_TITLE,
					'notice_description'    => QLWAPP_PROMOTE_FOUNDER_PROGRAM_DESCRIPTION,
					'notice_more_link'      => QLWAPP_PROMOTE_FOUNDER_PROGRAM_URL
				),
			)
		);
	});
}
