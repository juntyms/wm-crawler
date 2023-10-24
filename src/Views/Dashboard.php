<div class="wrap">
	<div class="card">
		<h2 class="title">WPMCRAWLER Plugin</h2>
		<p>
		<form method="post">
			<?php wp_nonce_field( 'crawl_button_clicked' ); ?>
			<input type="hidden" name="action" value="crawl_click">
			<input type="submit" value="Start Crawl" class="button-primary">
		</form>
		</p>
	</div>
</div>

<?php


if ( isset( $_POST['action'] ) && check_admin_referer( 'crawl_button_clicked' ) ) {

	$wpmc_page_crawl = new ROCKET_WP_CRAWLER\Pages\PageCrawler();

	$wpmc_page_crawl->register();

	// Display the links found.
	do_action( 'wpmc_display_links' );

	// Clear the Hook to reset the time.
	wp_clear_scheduled_hook( 'wpmc_hourly_crawl' );

	// Check if hook schedule exist.
	if ( ! wp_next_scheduled( 'wpmc_hourly_crawl' ) ) {
		// Add Event Schedule.
		wp_schedule_event( time(), 'hourly', 'wpmc_hourly_crawl' );
	}
}
