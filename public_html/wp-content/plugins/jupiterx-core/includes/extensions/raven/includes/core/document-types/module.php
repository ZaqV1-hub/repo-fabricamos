<?php
/**
 * Add Document Type Module.
 *
 * @package JupiterX_Core\Raven
 * @since 3.7.0
 */

namespace JupiterX_Core\Raven\Core\Document_Types;

use JupiterX_Core\Raven\Core\Document_Types\Type;

defined( 'ABSPATH' ) || die();

class Module {
	public function __construct() {
		add_action( 'elementor/documents/register', [ $this, 'register_popup_document_type' ] );
	}

	public function register_popup_document_type( $documents_manager ) {
		jupiterx_core()->load_files( [ 'extensions/raven/includes/core/document-types/types/popup' ] );

		$documents_manager->register_document_type( 'jupiterx-popups', Type\Jupiterx_Popup_Document::get_class_full_name() );
	}
}
