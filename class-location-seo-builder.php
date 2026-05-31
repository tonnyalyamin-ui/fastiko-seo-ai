<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Location_Builder
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get all "location pages" like /adults/{city}/
     */
    public function get_location_pages(string $post_type = 'page'): array
    {
        $pages = get_posts([
            'post_type' => $post_type,
            'post_status' => 'publish',
            'numberposts' => -1,
        ]);

        $locations = [];

        foreach ($pages as $page) {

            $slug = $page->post_name;

            if (preg_match('/adults[-\/](.+)/', $slug, $matches)) {
                $locations[] = [
                    'id' => $page->ID,
                    'title' => $page->post_title,
                    'slug' => $slug,
                    'url' => get_permalink($page)
                ];
            }
        }

        return $locations;
    }

    /**
     * Build internal linking suggestions
     */
    public function build_links(): array
    {
        $locations = $this->get_location_pages();

        $links = [];

        foreach ($locations as $from) {
            foreach ($locations as $to) {

                if ($from['id'] === $to['id']) {
                    continue;
                }

                $links[] = [
                    'from' => $from['id'],
                    'to' => $to['id'],
                    'anchor' => $to['title'],
                    'url' => $to['url']
                ];
            }
        }

        return $links;
    }

    /**
     * Suggest SEO metadata for location page
     */
    public function generate_location_seo(string $city): array
    {
        return [
            'title' => "Adults in {$city} | Verified Profiles & Listings",
            'meta_description' => "Browse verified adult profiles in {$city}. Find listings, compare profiles and discover new connections.",
            'h1' => "Adults in {$city}"
        ];
    }

    /**
     * Create CollectionPage schema for location
     */
    public function generate_location_schema(string $city, string $url, array $items = []): array
    {
        return [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => "Adults in {$city}",
            "url" => $url,
            "mainEntity" => [
                "@type" => "ItemList",
                "itemListElement" => $items
            ]
        ];
    }
}