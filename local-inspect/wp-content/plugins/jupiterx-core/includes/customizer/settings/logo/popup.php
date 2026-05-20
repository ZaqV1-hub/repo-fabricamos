<?php
/**
 * Add Jupiter Logo options WordPress Customizer.
 *
 * @package JupiterX\Framework\Admin\Customizer
 *
 * @since   1.0.0
 */

$section = 'jupiterx_logo';

// Logos
JupiterX_Customizer::add_section( $section, [
	'priority' => 40,
	'title'  => __( 'Logos', 'jupiterx-core' ),
	'type'  => 'container',
	'boxes' => [
		'settings' => [
			'label' => __( 'Settings', 'jupiterx-core' ),
		],
	],
	'help'   => [
		'url'   => 'https://my.artbees.net/support/',
		'title' => __( 'Artbees Help Center', 'jupiterx-core' ),
	],
	'group' => 'general_settings',
	'icon'  => 'logos',
] );

// Primary Logo.
JupiterX_Customizer::add_field( [
	'type'          => 'jupiterx-image',
	'settings'      => 'jupiterx_logo',
	'section'       => $section,
	'box'           => 'settings',
	'label'         => __( 'Primary Logo', 'jupiterx-core' ),
	'template_type' => 'logo',
] );

// Secondary Logo.
JupiterX_Customizer::add_field( [
	'type'          => 'jupiterx-image',
	'settings'      => 'jupiterx_logo_secondary',
	'section'       => $section,
	'box'           => 'settings',
	'label'         => __( 'Secondary Logo', 'jupiterx-core' ),
	'template_type' => 'logo',
] );

// Sticky Logo.
JupiterX_Customizer::add_field( [
	'type'          => 'jupiterx-image',
	'settings'      => 'jupiterx_logo_sticky',
	'section'       => $section,
	'box'           => 'settings',
	'label'         => __( 'Sticky Logo', 'jupiterx-core' ),
	'template_type' => 'logo',
] );

// Retina Primary Logo.
JupiterX_Customizer::add_field( [
	'type'          => 'jupiterx-image',
	'settings'      => 'jupiterx_logo_retina',
	'section'       => $section,
	'box'           => 'settings',
	'label'         => __( 'Retina Primary Logo', 'jupiterx-core' ),
	'template_type' => 'logo',
] );

// Retina Secondary Logo.
JupiterX_Customizer::add_field( [
	'type'          => 'jupiterx-image',
	'settings'      => 'jupiterx_logo_secondary_retina',
	'section'       => $section,
	'box'           => 'settings',
	'label'         => __( 'Retina Secondary Logo', 'jupiterx-core' ),
	'template_type' => 'logo',
] );

// Retina sticky logo.
JupiterX_Customizer::add_field( [
	'type'          => 'jupiterx-image',
	'settings'      => 'jupiterx_logo_sticky_retina',
	'section'       => $section,
	'box'           => 'settings',
	'label'         => __( 'Retina Sticky Logo', 'jupiterx-core' ),
	'template_type' => 'logo',
] );

// Mobile Logo.
JupiterX_Customizer::add_field( [
	'type'          => 'jupiterx-image',
	'settings'      => 'jupiterx_logo_mobile',
	'section'       => $section,
	'box'           => 'settings',
	'label'         => __( 'Mobile Logo', 'jupiterx-core' ),
	'template_type' => 'logo',
] );

// Retina Mobile Logo.
JupiterX_Customizer::add_field( [
	'type'          => 'jupiterx-image',
	'settings'      => 'jupiterx_logo_mobile_retina',
	'section'       => $section,
	'box'           => 'settings',
	'label'         => __( 'Retina Mobile Logo', 'jupiterx-core' ),
	'template_type' => 'logo',
] );
