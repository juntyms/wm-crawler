<?php
/**
 * @package WpmcrawlerPlugin
 */

namespace ROCKET_WP_CRAWLER\Pages;

class PageCrawler
{
    public $all_url_post_links;
    public function __construct()
    {
        $this->all_url_post_links = get_posts(['post_type' => 'wpmcrawler_links','numberposts' => -1]);

    }

    public function register()
    {

        add_action('delete_wpmcrawler_links', [ $this, 'delete_wpmcrawler_links']);

        add_action('crawl_hook', [$this, 'start_crawl']);

        do_action('crawl_hook');

        add_action('create_sitemap_file', [$this, 'create_sitemap']);

        do_action('create_sitemap_file');

        add_action('display_links', [$this, 'display_all_links']);


    }

    public function start_crawl()
    {

        do_action('delete_wpmcrawler_links');

        // Get the Homepage URL
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
                wp_insert_post([
                    'post_title' => $url,
                    'post_content' => $url,
                    'post_status' => 'publish',
                    'post_type' => 'wpmcrawler_links'
                ]);
            }
        }

    }

    public function delete_wpmcrawler_links()
    {

        // Delete all url_links post_type in the database
        //$all_url_post_links = get_posts(['post_type' => 'wpmcrawler_links','numberposts' => -1]);
        foreach($this->all_url_post_links as $post_link) {
            wp_delete_post($post_link->ID, true);
        }

    }


    public function create_sitemap()
    {
        //$all_url_post_links = get_posts(['post_type' => 'wpmcrawler_links','numberposts' => -1]);

        $myfile = fopen(dirname(ROCKET_CRWL_PLUGIN_FILENAME) . "/sitemap.html", "w") or die("Unable to open file!");

        $header = "<html>\n
	<head>\n
		<title>Sitemap</title>\n
	</head>\n
	<body>\n";
        fwrite($myfile, $header);

        foreach($this->all_url_post_links as $post_link) {

            fwrite($myfile, "<p>" . $post_link->post_content . "</p>\n");
        }

        $footer = "</body>\n
					</html>";

        fwrite($myfile, $footer);

        fclose($myfile);
    }

    public function display_all_links()
    {
        echo '<div class="wrap">';
        echo '<div class="card">';
        foreach($this->all_url_post_links as $post_link) {
            echo '<p>' . $post_link->post_content . '</p>';
        }
        echo '</div>';
        echo '</div>';
    }



}


//! Do homepage crawling -done

//! Delete the result from the lst crwal - done

//! Delete the sitemap.html if exist

//! Extract all of the internal hyperlinks present in the homepage - done

//! store results in database - done

//! display the results on the admin page

// save the homepage as .html in the server

// set crawl to run automatically every hour