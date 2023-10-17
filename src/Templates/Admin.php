<h1>WM Crawler</h1>
<form action="<?php echo admin_url('admin.php?page=wpmcrawler_plugin') ?>" method="post">

    <?php wp_nonce_field('crawl_button_clicked'); ?>
    <input type="hidden" name="action" value="crawl_click">
    <input type="submit" value="Start Crawl" class="button">
</form>


<?php


if (isset($_POST['action']) && check_admin_referer('crawl_button_clicked')) {

    // Delete the result from the lst crwal

    // Delete the sitemap.html if exist


    // Extract all of the internal hyperlinks present in the homepage

    $home_url = get_home_url();

    $urlData = file_get_contents($home_url);

    $dom = new \DOMDocument();
    @$dom->loadHTML($urlData);

    $xpath = new \DOMXPath($dom);
    $hrefs = $xpath->evaluate("/html/body//a");

    for($i = 0;$i < $hrefs->length;$i++) {
        $href = $hrefs->item($i);
        $url = $href->getAttribute('href');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        if(!filter_var($url, FILTER_VALIDATE_URL) === false) {
            $urlList[] = $url;
        }
    }

    echo "<ul>";
    foreach($urlList as $homepagelink) {
        echo "<li>$homepagelink</li>";
    }
    echo "</ul>";


    // store results in database

    // display the results on the admin page

    // save the homepage as .html in the server

    // set crawl to run automatically every hour





}