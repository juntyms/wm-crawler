<?php
/**
 * Plugin main class
 *
 * @package     WpmcrawlerPlugin
 * @since       2023
 * @author      Junn Eric Timoteo
 * @license     GPL-2.0-or-later
 */

namespace ROCKET_WP_CRAWLER\Pages;

use ROCKET_WP_CRAWLER\Pages\MenuSettings;

/**
 * Admin
 */
class Admin {

	/**
	 * Settings
	 *
	 * @var mixed
	 */
	public $settings;

	/**
	 * Menus
	 *
	 * @var array
	 */
	public $menus = array();

	/**
	 * Submenus
	 *
	 * @var array
	 */
	public $submenus = array();

	/**
	 * Method __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->settings = new MenuSettings();

		$this->menus = array(
			array(
				'page_title' => 'Wpmcrawler Plugin',
				'menu_title' => 'Wpmcrawler',
				'capability' => 'manage_options',
				'menu_slug'  => 'wpmcrawler_plugin',
				'callback'   => array( $this, 'view_dashboard' ),
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
	/**
	 * Method register
	 *
	 * @return void
	 */
	public function register() {
		$this->settings->add_main_menu( $this->menus )->with_sub_menu( 'Dashboard' )->add_Sub_menus( $this->submenus )->register();
	}

	/**
	 * Method view_results
	 */
	public function view_results() {

		return ( new PageCrawler() )->wpmc_display_links();
	}

	/**
	 * Method view_dashboard
	 */
	public function view_dashboard() {

		return require_once dirname( ROCKET_CRWL_PLUGIN_FILENAME ) . '/src/Views/Dashboard.php';
	}
}
