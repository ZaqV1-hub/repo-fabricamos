<?php
/**
 * Documentation block template.
 *
 * @since 3.0
 */

defined( 'ABSPATH' ) || exit;

?>

<i class="wpr-icon-book"></i>
<h4><?php esc_html_e( 'Documentation', 'rocket' ); ?></h4>
<br />
<p><?php esc_html_e( 'It is a great starting point to fix some of the most common issues.', 'rocket' ); ?></p>
<br />
<?php
$this->render_action_button(
	'link',
	'documentation',
	[
		'label'      => __( 'Read the documentation', 'rocket' ),
		'attributes' => [
			'target' => '_blank',
			'class'  => 'wpr-button wpr-button--blueDark',
		],
	]
);
?>
