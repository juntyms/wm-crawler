<h1>WM Crawler</h1>
<form action="<?php echo admin_url('admin.php?page=wpmcrawler_plugin') ?>" method="post">

    <?php wp_nonce_field('crawl_button_clicked'); ?>
    <input type="hidden" name="action" value="crawl_click">
    <input type="submit" value="Start Crawl" class="btn">
</form>