<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Loader
{
    public static function init(): void
    {
        self::load_core();
        self::load_ai();
        self::load_admin();
        self::load_engine();
    }

    private static function load_core(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-database.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-installer.php';
    }

    private static function load_ai(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-ai-provider-interface.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-openai-provider.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-gemini-provider.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-ai-generator.php';
    }

    private static function load_admin(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-admin.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-settings.php';
    }

    private static function load_engine(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-scanner.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-auditor.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-recommendations-service.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-schema-generator.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-schema-injector.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-auto-page-builder.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-location-seo-builder.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-internal-linking-engine.php';
        require_once FASTIKO_SEO_AI_PATH . 'includes/class-content-injector.php';
    }
}