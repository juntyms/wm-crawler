<?php
/**
 * @package WpmcrawlerPlugin
 */

namespace ROCKET_WP_CRAWLER\Pages;

class Admin
{
    public function register()
    {

        add_action('admin_menu', array($this, 'add_admin_pages'));

    }

    public function add_admin_pages()
    {
        // Create the menu
        add_menu_page(
            'Wpmcrawler Plugin',
            'Wpmcrawler',
            'manage_options',
            'wpmcrawler_plugin',
            array($this, 'admin_index'),
            'dashicons-buddicons-activity',
            '100'
        );


    }

    public function admin_index()
    {

        $plugin_path = plugin_dir_path(dirname(__FILE__, 1));

        require_once $plugin_path . 'Templates/Admin.php';

    }

    public function generate_sitemap()
    {
        // Do homepage crawling
        echo "clicked";
    }

}


if (isset($_POST['action']) && check_admin_referer('crawl_button_clicked')) {

    // button clicked
    (new Admin())->generate_sitemap();
}