<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Auto_Page_Builder
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
     * Create or update city page
     */
    public function create_city_page(string $city, string $parent_slug = 'adults'): int
    {
        $slug = $parent_slug . '/' . sanitize_title($city);

        $existing = get_page_by_path($slug);

        $content = $this->generate_content($city);

        $data = [
            'post_title'   => "Adults in {$city}",
            'post_name'    => sanitize_title($city),
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => $content
        ];

        if ($existing) {
            $data['ID'] = $existing->ID;
            return wp_update_post($data);
        }

        return wp_insert_post($data);
    }

    /**
     * Generate SEO + content via AI later
     */
    private function generate_content(string $city): string
{
    $expander = Fastiko_SEO_AI_Content_Expander::instance();

    $fake_post = (object) [
        'post_title' => $city,
        'post_content' => ''
    ];

    $ai = $expander->expand($fake_post);

    if (!empty($ai['content'])) {
        return $ai['content'];
    }

    // fallback
    return "
        <h1>Adults in {$city}</h1>
        <p>Find listings in {$city}</p>
    ";
}

    /**
     * Bulk generate cities
     */
    public function generate_bulk(array $cities): array
    {
        $created = [];

        foreach ($cities as $city) {
            $created[] = $this->create_city_page($city);
        }

        return $created;
    }
}