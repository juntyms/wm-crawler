<div class="wrap">

    <div class="card">
        <h2 class="title">WPMCRAWLER Plugin</h2>
        <p>
        <form method="post">
            <?php wp_nonce_field('crawl_button_clicked'); ?>
            <input type="hidden" name="action" value="crawl_click">
            <input type="submit" value="Start Crawl" class="button-primary">
        </form>
        </p>
    </div>
</div>



<?php
            function pageCrawler()
            {

                $page_crawl =  new ROCKET_WP_CRAWLER\Pages\PageCrawler();

                $page_crawl->register();

            }

            if (isset($_POST['action']) && check_admin_referer('crawl_button_clicked')) {

                pageCrawler();

                do_action('display_links');

                // Clear the Hook to reset the time
                wp_clear_scheduled_hook('hourly_crawl');

                // Check if hook schedule exist
                if(! wp_next_scheduled('hourly_crawl')) {
                    // Add Event Schedule
                    wp_schedule_event(time(), 'hourly', 'hourly_crawl');
                }

            }



            ?>
