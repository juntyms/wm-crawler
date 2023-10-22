<?php
/**
 * @package WpmcrawlerPlugin
 */

namespace ROCKET_WP_CRAWLER\Pages;

class MenuSettings
{
    public $admin_menus = [];

    public $admin_submenus = [];

    public function register()
    {
        if (! empty($this->admin_menus)) {
            add_action('admin_menu', [$this, 'addAdminMenu']);
        }
    }

    public function addMainMenu(array $menus)
    {
        $this->admin_menus = $menus;

        return $this;
    }

    public function withSubMenu(string $title = null)
    {
        if (empty($this->admin_menus)) {
            return $this;
        }

        $admin_menu = $this->admin_menus[0];

        $submenu = [
            [
                'parent_slug' => $admin_menu['menu_slug'],
                'page_title' => $admin_menu['page_title'],
                'menu_title' => ($title) ? $title : $admin_menu['menu_title'],
                'capability' => $admin_menu['capability'],
                'menu_slug' => $admin_menu['menu_slug'],
                'callback' => $admin_menu['callback']
            ]
        ];


        $this->admin_submenus = $submenu;

        return $this;

    }

    public function addSubMenus(array $menus)
    {
        $this->admin_submenus = array_merge($this->admin_submenus, $menus);

        return $this;
    }

    public function addAdminMenu()
    {
        foreach($this->admin_menus as $menu) {
            add_menu_page($menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'], $menu['callback'], $menu['icon_url'], $menu['position']);
        }


        foreach ($this->admin_submenus as $menu) {
            add_submenu_page($menu['parent_slug'], $menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'], $menu['callback']);
        }

    }
}