<?php
/**
 * This class handles admin notices.
 *
 * @package JupiterX_Core\Admin
 *
 * @since 1.18.0
 */

/**
 * Handle admin notices.
 *
 * @package JupiterX_Core\Admin
 *
 * @since 1.18.0
 */
class JupiterX_Core_Admin_Notices {

	/**
	 * Constructor.
	 *
	 * @since 1.18.0
	 */
	public function __construct() {
		add_filter( 'jet-dashboard/js-page-config', [ $this, 'remove_croco_license_notice' ], 10, 1 );
	}

	/**
	 * Remove Croco notice.
	 *
	 * @param $notices
	 * @return void|array
	 * @since 1.20.0
	 */
	public function remove_croco_license_notice( $notices ) {
		if ( empty( $notices['noticeList'] ) ) {
			return $notices;
		}

		foreach ( $notices['noticeList'] as $key => $notice ) {
			if ( empty( $notice['id'] ) || '30days-to-license-expire' !== $notice['id'] ) {
				continue;
			}

			unset( $notices['noticeList'][ $key ] );
		}

		// Reindex array after unset
		$notices['noticeList'] = array_values( $notices['noticeList'] );

		return $notices;
	}
}

new JupiterX_Core_Admin_Notices();
