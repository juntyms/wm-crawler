<?php
/**
 * @package WpmcrawlerPlugin
 */

namespace ROCKET_WP_CRAWLER\Services;

class Services
{
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