<?php
/**
 * @package WpmcrawlerPlugin
 */

namespace ROCKET_WP_CRAWLER\Pages;

use DOMXPath;
use DOMDocument;

class PageCrawler
{
    public function start_crawl()
    {
        //$home_url = get_home_url();

        // $client = new \GuzzleHttp\Client();

        // $res = $client->request('GET', $home_url);

        // //echo $res->getBody();

        // $crawler = new Crawler($res->getBody());

        // $crawler->filter('body');

        // print_r($crawler);


        $home_url = get_home_url();

        $urlData = file_get_contents($home_url);
        $dom = new DOMDocument();
        @$dom->loadHTML($urlData);
        $xpath = new DOMXPath($dom);
        $hrefs = $xpath->evaluate("/html/body//a");
        for($i = 0;$i < $hrefs->length;$i++) {
            $href = $hrefs->item($i);
            $url = $href->getAttribute('href');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            if(!filter_var($url, FILTER_VALIDATE_URL) === false) {
                $urlList[] = $url;
            }
        }

        return $urlList;



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