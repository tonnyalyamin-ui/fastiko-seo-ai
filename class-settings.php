<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Settings
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings(): void
    {
        register_setting(
            'fastiko_seo_ai_settings',
            'fastiko_openai_key'
        );

        register_setting(
            'fastiko_seo_ai_settings',
            'fastiko_gemini_key'
        );

        register_setting(
            'fastiko_seo_ai_settings',
            'fastiko_default_provider'
        );
    }
}