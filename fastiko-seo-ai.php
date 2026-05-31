<?php
/**
 * Plugin Name: Fastiko SEO AI
 * Plugin URI: https://fastiko.au
 * Description: AI-powered SEO auditing, recommendations, schema generation and optimization toolkit.
 * Version: 1.0.0
 * Author: Fastiko
 * Author URI: https://fastiko.au
 * Requires PHP: 8.3
 * Requires at least: 6.9
 * Text Domain: fastiko-seo-ai
 */

if (!defined('ABSPATH')) {
    exit;
}

define('FASTIKO_SEO_AI_VERSION', '1.0.0');
define('FASTIKO_SEO_AI_FILE', __FILE__);
define('FASTIKO_SEO_AI_PATH', plugin_dir_path(__FILE__));
define('FASTIKO_SEO_AI_URL', plugin_dir_url(__FILE__));
define('FASTIKO_SEO_AI_DB_VERSION', '1.1.0');


require_once FASTIKO_SEO_AI_PATH . 'class-loader.php';
Fastiko_SEO_AI_Loader::init();


register_activation_hook(
    __FILE__,
    ['Fastiko_SEO_AI_Installer', 'activate']
);

register_deactivation_hook(
    __FILE__,
    ['Fastiko_SEO_AI_Installer', 'deactivate']
);

final class Fastiko_SEO_AI {

    private static ?Fastiko_SEO_AI $instance = null;

    public static function instance(): Fastiko_SEO_AI
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks(): void
    {
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        add_action('init', [$this, 'boot']);
    }

    public function load_textdomain(): void
    {
        load_plugin_textdomain(
            'fastiko-seo-ai',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }

    public function boot(): void
    {
        Fastiko_SEO_AI_Admin::instance();
        Fastiko_SEO_AI_Scanner::instance();
        Fastiko_SEO_AI_Auditor::instance();
		Fastiko_SEO_AI_Settings::instance();
		Fastiko_SEO_AI_Database::maybe_upgrade();
		Fastiko_SEO_AI_Schema_Injector::instance();
		Fastiko_SEO_AI_Auto_Page_Builder::instance();
		Fastiko_SEO_AI_Content_Injector::instance();
    }
}

Fastiko_SEO_AI::instance();