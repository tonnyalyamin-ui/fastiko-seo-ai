<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Installer
{
    public static function activate(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-database.php';

        Fastiko_SEO_AI_Database::create_tables();

        if (!wp_next_scheduled('fastiko_seo_ai_daily_scan')) {
            wp_schedule_event(
                time(),
                'daily',
                'fastiko_seo_ai_daily_scan'
            );
        }
    }

    public static function deactivate(): void
    {
        wp_clear_scheduled_hook('fastiko_seo_ai_daily_scan');
    }
}