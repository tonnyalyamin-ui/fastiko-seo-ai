<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Generator
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function generate(WP_Post $post): array
    {
        $provider = $this->get_provider();

        $auditor = Fastiko_SEO_AI_Auditor::instance();

        $payload = [
            'url' => get_permalink($post),
            'title' => get_the_title($post),
            'content' => wp_strip_all_tags($post->post_content),
            'seo_score' => $auditor->calculate_score($post),
            'issues' => method_exists($auditor, 'detect_issues')
                ? $auditor->detect_issues($post)
                : []
        ];

        return $provider->generate($payload);
    }

    private function get_provider(): Fastiko_SEO_AI_Provider_Interface
    {
        $default = get_option('fastiko_default_provider', 'openai');

        return match ($default) {
            'gemini' => new Fastiko_SEO_AI_Gemini_Provider(),
            default  => new Fastiko_SEO_AI_OpenAI_Provider(),
        };
    }
}