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

/**
 * MenuSettings
 */
class MenuSettings {

	/**
	 * Admin_menus
	 *
	 * @var array
	 */
	public $admin_menus = array();

	/**
	 * Admin_submenus
	 *
	 * @var array
	 */
	public $admin_submenus = array();

	/**
	 * Method register
	 */
	public function register() {
		if ( ! empty( $this->admin_menus ) ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		}
	}

	/**
	 * Method add_main_menu
	 *
	 * @param array $menus Array Containing the menu parameters.
	 */
	public function add_main_menu( array $menus ) {

		$this->admin_menus = $menus;

		return $this;
	}

	/**
	 * Method withSubMenu
	 *
	 * @param string $title Title of the submenu.
	 */
	public function with_sub_menu( $title = null ) {

		if ( empty( $this->admin_menus ) ) {
			return $this;
		}

		$admin_menu = $this->admin_menus[0];

		$submenu = array(
			array(
				'parent_slug' => $admin_menu['menu_slug'],
				'page_title'  => $admin_menu['page_title'],
				'menu_title'  => ( $title ) ? $title : $admin_menu['menu_title'],
				'capability'  => $admin_menu['capability'],
				'menu_slug'   => $admin_menu['menu_slug'],
				'callback'    => $admin_menu['callback'],
			),
		);

		$this->admin_submenus = $submenu;

		return $this;
	}

	/**
	 * Method addSubMenus
	 *
	 * @param array $menus array containing the submenu.
	 */
	public function add_sub_menus( array $menus ) {

		$this->admin_submenus = array_merge( $this->admin_submenus, $menus );

		return $this;
	}

	/**
	 * Method addAdminMenu
	 */
	public function add_admin_menu() {

		foreach ( $this->admin_menus as $menu ) {
			add_menu_page( $menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'], $menu['callback'], $menu['icon_url'], $menu['position'] );
		}

		foreach ( $this->admin_submenus as $menu ) {
			add_submenu_page( $menu['parent_slug'], $menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'], $menu['callback'] );
		}
	}
}
