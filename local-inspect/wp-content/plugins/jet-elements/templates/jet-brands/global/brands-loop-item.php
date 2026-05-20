<?php
/**
 * Features list item template
 */
if ( $this->_context === 'render' ) {
	$settings = $this->get_settings_for_display();
} else {
	$settings = [];
}

$title_tag = ! empty( $settings['company_name_html_tag'] ) ? jet_elements_tools()->validate_html_tag( $settings['company_name_html_tag'] ) : 'h5';
?>
<?php // phpcs:disable ?>
<div class="brands-list__item"><?php
	echo $this->_open_brand_link( 'item_url' );
	echo $this->_get_brand_image( 'item_image' );
	echo $this->_loop_item( array( 'item_name' ), '<' . $title_tag . ' class="brands-list__item-name">%s</' . $title_tag . '>' );
	echo $this->_loop_item( array( 'item_desc' ), '<div class="brands-list__item-desc">%s</div>' );
	echo $this->_close_brand_link( 'item_url' );
?></div>
<?php // phpcs:enable ?>