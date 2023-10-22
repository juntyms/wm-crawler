<?php
/**
 * Plugin main class
 *
 * @package     TO FILL
 * @since       TO FILL
 * @author      Mathieu Lamiot
 * @license     GPL-2.0-or-later
 */

namespace ROCKET_WP_CRAWLER;

/**
 * Main plugin class. It manages initialization, install, and activations.
 */
class Rocket_Wpc_Plugin_Class
{
    /**
     * Manages plugin initialization
     *
     * @return void
     */
    public function __construct()
    {
        add_action('hourly_crawl', array( $this, 'hourly_crawl' ));

        // Register plugin lifecycle hooks.
        register_deactivation_hook(ROCKET_CRWL_PLUGIN_FILENAME, array( $this, 'wpc_deactivate' ));

        $this->register_services();
    }

    public function hourly_crawl()
    {
        $crawl = new Pages\PageCrawler();

        $crawl->insert_data();
    }

    /**
     * Handles plugin activation:
     *
     * @return void
     */
    public static function wpc_activate()
    {
        // Security checks.
        if (! current_user_can('activate_plugins')) {
            return;
        }
        $plugin = isset($_REQUEST['plugin']) ? sanitize_text_field(wp_unslash($_REQUEST['plugin'])) : '';
        check_admin_referer("activate-plugin_{$plugin}");

        if(! wp_next_scheduled('hourly_crawl')) {
            wp_schedule_event(time(), 'hourly', 'hourly_crawl');
        }

    }

    /**
     * Handles plugin deactivation
     *
     * @return void
     */
    public function wpc_deactivate()
    {
        // Security checks.
        if (! current_user_can('activate_plugins')) {
            return;
        }
        $plugin = isset($_REQUEST['plugin']) ? sanitize_text_field(wp_unslash($_REQUEST['plugin'])) : '';
        check_admin_referer("deactivate-plugin_{$plugin}");

        wp_clear_scheduled_hook('hourly_crawl');
    }

    /**
     * Handles plugin uninstall
     *
     * @return void
     */
    public static function wpc_uninstall()
    {

        // Security checks.
        if (! current_user_can('activate_plugins')) {
            return;
        }
    }

    public static function get_services()
    {
        return [
            Pages\Admin::class
        ];
    }

    public function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    private static function instantiate($class)
    {
        //var_dump($class);
        $service = new $class();

        return $service;
    }

}
