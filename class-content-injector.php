<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Content_Injector
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
        add_filter('the_content', [$this, 'inject_internal_links']);
    }

    public function inject_internal_links(string $content): string
    {
        if (is_admin()) {
            return $content;
        }

        global $post;

        if (!$post) {
            return $content;
        }

        $engine = Fastiko_SEO_AI_Internal_Linking_Engine::instance();

        $links = $engine->generate_links();

        return $engine->inject_links($content, $links);
    }
}