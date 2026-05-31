<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Schema_Injector
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
        add_action('wp_head', [$this, 'inject_schema'], 20);
    }

    public function inject_schema(): void
    {
        if (!is_singular()) {
            return;
        }

        global $post;

        if (!$post) {
            return;
        }

        $generator = Fastiko_SEO_AI_Schema_Generator::instance();

        $schemas = $generator->generate_for_post($post);

        foreach ($schemas as $schema) {
            echo '<script type="application/ld+json">';
            echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo '</script>';
        }
    }
}