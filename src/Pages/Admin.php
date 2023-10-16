<?php
/**
 * @package WpmcrawlerPlugin
 */

namespace ROCKET_WP_CRAWLER\Pages;

use ROCKET_WP_CRAWLER\Pages\Settings;
use ROCKET_WP_CRAWLER\Pages\Crawl;

class Admin
{
    public $settings;

    public $menus = [];

    public $submenus = [];

    public function __construct()
    {
        $this->settings = new Settings();

        $this->menus = [
            [
                'page_title' => 'Wpmcrawler Plugin',
                'menu_title' => 'Wpmcrawler',
                'capability' => 'manage_options',
                'menu_slug' => 'wpmcrawler_plugin',
                'callback' => function () { echo '<h1>Dashboard</h1>'; },
                'icon_url' => 'dashicons-buddicons-activity',
                'position' => '100'
            ]
        ];


        $this->submenus = [
            [
                'parent_slug' => 'wpmcrawler_plugin',
                'page_title' => 'Results',
                'menu_title' => 'Results',
                'capability' => 'manage_options',
                'menu_slug' => 'wpmcrawler_results',
                'callback' => function () { echo '<h1>Results</h1>'; }
            ]
        ];


    }
    public function register()
    {
        //add_action('admin_menu', array($this, 'add_admin_pages'));

        $this->settings->addMainMenu($this->menus)->withSubMenu('Dashboard')->addSubMenus($this->submenus)->register();
    }


}


if (isset($_POST['action']) && check_admin_referer('crawl_button_clicked')) {

    // button clicked
    $crawl = new Crawl();
    $crawl->register();
}
