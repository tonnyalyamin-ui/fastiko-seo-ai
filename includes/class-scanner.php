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
        $this->scan_posts();
    }

    private function scan_posts(): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'fastiko_seo_pages';

        $posts = get_posts([
            'post_type' => 'any',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ]);

        foreach ($posts as $post) {

            $metrics = $this->analyze_post($post);

            $score = Fastiko_SEO_AI_Auditor::instance()
                ->calculate_score($post);

            $wpdb->replace(
                $table,
                [
                    'object_id' => $post->ID,
                    'object_type' => $post->post_type,
                    'url' => get_permalink($post),
                    'title' => get_the_title($post),
                    'seo_score' => $score,
                    'word_count' => $metrics['word_count'],
                    'h1_count' => $metrics['h1_count'],
                    'h2_count' => $metrics['h2_count'],
                    'images_count' => $metrics['images_count'],
                    'internal_links_count' => $metrics['internal_links_count'],
                    'scanned_at' => current_time('mysql')
                ]
            );
        }
    }

    private function analyze_post(WP_Post $post): array
    {
        $content = $post->post_content;

        return [
            'word_count' => str_word_count(
                wp_strip_all_tags($content)
            ),

            'h1_count' => preg_match_all(
                '/<h1/i',
                $content
            ),

            'h2_count' => preg_match_all(
                '/<h2/i',
                $content
            ),

            'images_count' => preg_match_all(
                '/<img/i',
                $content
            ),

            'internal_links_count' => preg_match_all(
                '/href=/i',
                $content
            )
        ];
    }
}