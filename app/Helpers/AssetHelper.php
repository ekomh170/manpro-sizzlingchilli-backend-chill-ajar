<?php

namespace App\Helpers;

class AssetHelper
{
    /**
     * Get logo path
     * 
     * @param string $type (default|alt|bg)
     * @return string
     */
    public static function logo($type = 'default')
    {
        $logos = [
            'default' => 'images/logos/logo.png',
            'alt' => 'images/logos/logo-alt.png',
            'bg' => 'images/logos/logo-bg.png',
        ];

        return asset($logos[$type] ?? $logos['default']);
    }

    /**
     * Get favicon path
     * 
     * @param string $type (default|eko|icon|icon2)
     * @return string
     */
    public static function favicon($type = 'eko')
    {
        $favicons = [
            'default' => 'favicon.ico',
            'eko' => 'favicon/favicon_eko.ico',
            'icon' => 'favicon/icon.ico',
            'icon2' => 'favicon/icon2.ico',
        ];

        return asset($favicons[$type] ?? $favicons['eko']);
    }

    /**
     * Get asset image path
     * 
     * @param string $name
     * @return string
     */
    public static function asset($name)
    {
        $assets = [
            'hero' => 'images/assets/hero.png',
            'group-63' => 'images/assets/group-63.png',
            'group-68' => 'images/assets/group-68.png',
        ];

        return asset($assets[$name] ?? "images/assets/{$name}");
    }

    /**
     * Get all available logos
     * 
     * @return array
     */
    public static function allLogos()
    {
        return [
            'default' => self::logo('default'),
            'alt' => self::logo('alt'),
            'bg' => self::logo('bg'),
        ];
    }

    /**
     * Get all available favicons
     * 
     * @return array
     */
    public static function allFavicons()
    {
        return [
            'default' => self::favicon('default'),
            'eko' => self::favicon('eko'),
            'icon' => self::favicon('icon'),
            'icon2' => self::favicon('icon2'),
        ];
    }
}
