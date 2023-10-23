<?php
/**
 * @package WpmcrawlerPlugin
 */

namespace ROCKET_WP_CRAWLER\Pages;

use ROCKET_WP_CRAWLER\Views\Results;
use ROCKET_WP_CRAWLER\Pages\MenuSettings;

class Admin
{
    public $settings;

    public $menus = array();

    public $submenus = array();

    public function __construct()
    {
        $this->settings = new MenuSettings();

        $this->menus = array(
            array(
                'page_title' => 'Wpmcrawler Plugin',
                'menu_title' => 'Wpmcrawler',
                'capability' => 'manage_options',
                'menu_slug'  => 'wpmcrawler_plugin',
                'callback'   => array( $this, 'view_dashboard'),
                'icon_url'   => 'dashicons-buddicons-activity',
                'position'   => '100',
            ),
        );

        $this->submenus = array(
            array(
                'parent_slug' => 'wpmcrawler_plugin',
                'page_title'  => 'Results',
                'menu_title'  => 'Results',
                'capability'  => 'manage_options',
                'menu_slug'   => 'wpmcrawler_results',
                'callback'    => array( $this, 'view_results' ),
            ),
        );
    }
    public function register()
    {
        $this->settings->addMainMenu($this->menus)->withSubMenu('Dashboard')->addSubMenus($this->submenus)->register();
    }

    public function view_results()
    {
        return require_once dirname(ROCKET_CRWL_PLUGIN_FILENAME) . '/src/Views/Results.php';
    }

    public function view_dashboard()
    {
        return require_once dirname(ROCKET_CRWL_PLUGIN_FILENAME) . '/src/Views/Dashboard.php';
    }

}
