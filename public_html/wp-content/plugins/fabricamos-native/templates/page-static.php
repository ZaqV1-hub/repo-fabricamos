<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page = get_queried_object();

if ( ! $page instanceof WP_Post ) {
	status_header( 404 );
	nocache_headers();
	include get_query_template( '404' );
	return;
}

$page_title = get_the_title( $page );
$show_user  = false;

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--static">
	<div class="fab-container">
		<div class="fab-panel fab-panel--soft fab-static-panel">
			<div class="fab-title-line-wrap">
				<h1 class="fab-screen-title"><?php echo esc_html( get_the_title( $page ) ); ?></h1>
				<span class="fab-line"></span>
			</div>
			<div class="fab-static-content">
				<?php echo wp_kses_post( wpautop( $page->post_content ) ); ?>
			</div>
		</div>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
