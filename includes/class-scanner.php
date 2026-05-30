<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Scanner
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function scan_site(): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'fastiko_seo_pages';

        $posts = get_posts([
            'post_type'      => 'any',
            'post_status'    => 'publish',
            'posts_per_page' => -1
        ]);

        foreach ($posts as $post) {

            $score = Fastiko_SEO_AI_Auditor::instance()
                ->calculate_score($post);

            $wpdb->insert(
                $table,
                [
                    'object_id' => $post->ID,
                    'object_type' => $post->post_type,
                    'url' => get_permalink($post),
                    'title' => get_the_title($post),
                    'seo_score' => $score,
                    'scanned_at' => current_time('mysql')
                ]
            );
        }
    }
}