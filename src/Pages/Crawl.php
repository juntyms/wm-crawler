<?php
/**
 * @package WpmcrawlerPlugin
 */

namespace ROCKET_WP_CRAWLER\Pages;

class Crawl
{
    public function register()
    {
        // call method
        add_action('crawl', array($this, 'startCrawl'));


    }

    public function startCrawl()
    {
        // Do homepage crawling

        // Delete the result from the lst crwal

        // Delete the sitemap.html if exist

        // Extract all of the internal hyperlinks present in the homepage

        // store results in database

        // display the results on the admin page

        // save the homepage as .html in the server

        // set crawl to run automatically every hour
    }

}
