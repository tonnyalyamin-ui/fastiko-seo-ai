<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Content_Expander
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function build_city_payload(string $city): array
    {
        return [
            'city' => $city,
            'intent' => 'programmatic_seo_city_page',
            'keywords' => [
                "adults in {$city}",
                "{$city} listings",
                "{$city} profiles",
                "best in {$city}"
            ]
        ];
    }

    public function expand(WP_Post $post): array
    {
        $city = $post->post_title;

        $provider = new Fastiko_SEO_AI_OpenAI_Provider();

        $payload = [
            'task' => 'GENERATE SEO CITY PAGE',
            'city' => $city,
            'url' => get_permalink($post),
            'instructions' => 'Return ONLY JSON with seo optimized content',
            'structure' => [
                'title',
                'meta_description',
                'h1',
                'intro',
                'sections',
                'faq',
                'internal_links_suggestions'
            ]
        ];

        return $provider->generate($payload);
    }
}