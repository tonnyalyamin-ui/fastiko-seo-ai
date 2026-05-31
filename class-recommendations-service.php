<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Recommendations_Service
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
     * Generate + store AI recommendations
     */
    public function generate_for_post(WP_Post $post): int
    {
        global $wpdb;

        $generator = Fastiko_SEO_AI_Generator::instance();

        $result = $generator->generate($post);

        $table = $wpdb->prefix . 'fastiko_seo_suggestions';

        $wpdb->insert(
            $table,
            [
                'page_id' => $post->ID,
                'provider' => get_option('fastiko_default_provider', 'openai'),
                'title' => $result['title'] ?? '',
                'meta_description' => $result['meta_description'] ?? '',
                'h1' => $result['h1'] ?? '',
                'faq_json' => json_encode($result['faq'] ?? []),
                'schema_json' => json_encode($result['schema'] ?? []),
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ]
        );

        return (int) $wpdb->insert_id;
    }

    /**
     * Get recommendations list
     */
    public function get_list(int $limit = 100): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'fastiko_seo_suggestions';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table}
                 ORDER BY created_at DESC
                 LIMIT %d",
                $limit
            ),
            ARRAY_A
        );
    }

    /**
     * Apply recommendation to post
     */
    public function apply(int $id): bool
    {
        global $wpdb;

        $table = $wpdb->prefix . 'fastiko_seo_suggestions';

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$row) {
            return false;
        }

        wp_update_post([
            'ID' => $row['page_id'],
            'post_title' => $row['title'],
        ]);

        // meta description (if SEO plugin exists)
        if (!empty($row['meta_description'])) {
            update_post_meta(
                $row['page_id'],
                '_yoast_wpseo_metadesc',
                $row['meta_description']
            );
        }

        $wpdb->update(
            $table,
            ['status' => 'applied'],
            ['id' => $id]
        );

        return true;
    }

    /**
     * Ignore recommendation
     */
    public function ignore(int $id): void
    {
        global $wpdb;

        $wpdb->update(
            $wpdb->prefix . 'fastiko_seo_suggestions',
            ['status' => 'ignored'],
            ['id' => $id]
        );
    }

    /**
     * Regenerate recommendation
     */
    public function regenerate(int $post_id): int
    {
        return $this->generate_for_post(
            get_post($post_id)
        );
    }
}