<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Loader
{
    public static function init(): void
    {
        self::core();
        self::admin();
        self::scanner();
        self::ai();
        self::schema();
        self::seo();
        self::linking();
    }

    private static function core(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'class-database.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-installer.php';
    }

    private static function admin(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'class-admin.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-settings.php';
    }

    private static function scanner(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'class-scanner.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-auditor.php';
    }

    private static function ai(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'class-ai-provider-interface.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-openai-provider.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-gemini-provider.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-ai-generator.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-content-expander.php';
    }

    private static function schema(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'class-schema-generator.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-schema-injector.php';
    }

    private static function seo(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'class-auto-page-builder.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-location-seo-builder.php';
    }

    private static function linking(): void
    {
        require_once FASTIKO_SEO_AI_PATH . 'class-internal-linking-engine.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-content-injector.php';
        require_once FASTIKO_SEO_AI_PATH . 'class-recommendations-service.php';
    }
}