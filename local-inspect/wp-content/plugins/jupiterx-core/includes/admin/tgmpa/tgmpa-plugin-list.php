<?php
/**
 * Add Jupiter X pro plugins.
 *
 * @package JupiterX_Core\Admin
 *
 * @since 1.9.0
 */

add_filter( 'jupiterx_tgmpa_plugins', 'jupiterx_pro_plugins' );

/**
 * Add Jupiter X Pro plugins.
 *
 * @since 1.9.0
 *
 * @param array $plugins Array of free Jupiter x plugins.
 * @return array Array af free and pro plugins.
 */
function jupiterx_pro_plugins( $plugins ) {
	$pro_plugins = [
		[
			'name' => __( 'Sellkit Pro', 'jupiterx-core' ),
			'slug' => 'sellkit-pro',
			'required' => false,
			'force_activation' => false,
			'force_deactivation' => false,
			'pro' => false,
			'label_type' => __( 'Optional', 'jupiterx-core' ),
		],
		[
			'name' => __( 'Sellkit', 'jupiterx-core' ),
			'slug' => 'sellkit',
			'required' => false,
			'force_activation' => false,
			'force_deactivation' => false,
			'pro' => false,
			'label_type' => __( 'Optional', 'jupiterx-core' ),
		],
	];

	return array_merge( $pro_plugins, $plugins );
}
