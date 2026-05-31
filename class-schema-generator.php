<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Schema_Generator
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function generate_for_post(WP_Post $post): array
    {
        return [
            $this->generate_article_schema($post),
            $this->generate_breadcrumbs($post),
        ];
    }

    private function generate_article_schema(WP_Post $post): array
    {
        return [
            "@context" => "https://schema.org",
            "@type" => "Article",
            "headline" => get_the_title($post),
            "datePublished" => get_the_date('c', $post),
            "dateModified" => get_the_modified_date('c', $post),
            "author" => [
                "@type" => "Person",
                "name" => get_the_author_meta('display_name', $post->post_author)
            ],
            "mainEntityOfPage" => get_permalink($post)
        ];
    }

    private function generate_breadcrumbs(WP_Post $post): array
    {
        $items = [
            [
                "@type" => "ListItem",
                "position" => 1,
                "name" => "Home",
                "item" => home_url("/")
            ],
            [
                "@type" => "ListItem",
                "position" => 2,
                "name" => get_post_type($post),
                "item" => get_post_type_archive_link($post->post_type)
            ],
            [
                "@type" => "ListItem",
                "position" => 3,
                "name" => get_the_title($post),
                "item" => get_permalink($post)
            ]
        ];

        return [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => $items
        ];
    }

    /**
     * CollectionPage (важно для /adults/melbourne/)
     */
    public function generate_collection_page(string $title, string $url, array $items = []): array
    {
        return [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => $title,
            "url" => $url,
            "mainEntity" => [
                "@type" => "ItemList",
                "itemListElement" => $items
            ]
        ];
    }


	public function generate_location_collection(string $city, string $url, array $items = []): array
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