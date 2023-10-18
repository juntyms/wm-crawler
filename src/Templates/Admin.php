<div class="wrap">

    <div class="card">
        <h2 class="title">WPMCRAWLER Plugin</h2>
        <p>
        <form action="<?php echo admin_url('admin.php?page=wpmcrawler_plugin') ?>" method="post">

            <?php wp_nonce_field('crawl_button_clicked'); ?>
            <input type="hidden" name="action" value="crawl_click">
            <input type="submit" value="Start Crawl" class="button-primary">
        </form>
        </p>
    </div>
</div>



<?php

use ROCKET_WP_CRAWLER\Pages\PageCrawler;

        if (isset($_POST['action']) && check_admin_referer('crawl_button_clicked')) {

            // Delete the result from the lst crwal

            // Delete the sitemap.html if exist


            // Extract all of the internal hyperlinks present in the homepage


            //$foundUrls = PageCrawler::();

            //foreach($foundUrls as $foundUrl) {
            //    echo $foundUrl;
            // }

            $page_crawl =  new PageCrawler();

            $page_crawl->register();

            do_action('display_links');

        }