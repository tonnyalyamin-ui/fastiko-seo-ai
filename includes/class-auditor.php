<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Auditor
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function calculate_score(WP_Post $post): int
    {
        $score = 100;

        $title = get_the_title($post);
        $content = wp_strip_all_tags($post->post_content);

        if (strlen($title) < 20) {
            $score -= 10;
        }

        if (strlen($title) > 60) {
            $score -= 5;
        }

        if (str_word_count($content) < 300) {
            $score -= 20;
        }

        if (
            !preg_match('/<h1/i', $post->post_content)
        ) {
            $score -= 10;
        }

        return max(0, $score);
    }
}