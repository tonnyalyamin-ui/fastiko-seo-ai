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

        $title   = get_the_title($post);
        $content = $post->post_content;
        $text    = wp_strip_all_tags($content);

        $word_count = str_word_count($text);

        // 1. Title checks
        if (empty($title)) {
            $score -= 20;
        } elseif (strlen($title) < 20) {
            $score -= 10;
        } elseif (strlen($title) > 60) {
            $score -= 5;
        }

        // 2. Content length
        if ($word_count < 300) {
            $score -= 25;
        } elseif ($word_count < 600) {
            $score -= 10;
        }

        // 3. Headings structure
        $h2_count = preg_match_all('/<h2/i', $content);
        $h3_count = preg_match_all('/<h3/i', $content);

        if ($h2_count === 0) {
            $score -= 15;
        }

        if ($h2_count > 0 && $h3_count === 0 && $word_count > 800) {
            $score -= 5;
        }

        // 4. Images
        $img_count = preg_match_all('/<img/i', $content);

        if ($img_count === 0 && $word_count > 500) {
            $score -= 10;
        }

        // 5. Internal links
        $internal_links = preg_match_all('/href=["\'](?!http)/i', $content);

        if ($internal_links === 0 && $word_count > 500) {
            $score -= 10;
        }

        // 6. Thin content penalty
        if ($word_count < 200) {
            $score -= 20;
        }

        // 7. Over-optimization (too many H2 for small content)
        if ($word_count < 500 && $h2_count > 5) {
            $score -= 10;
        }

        return max(0, min(100, $score));
    }

    public function detect_issues(WP_Post $post): array
    {
        $issues = [];

        $title   = get_the_title($post);
        $content = $post->post_content;
        $text    = wp_strip_all_tags($content);

        $word_count = str_word_count($text);

        if (empty($title)) {
            $issues[] = [
                'severity' => 'critical',
                'issue' => 'Missing title',
                'recommendation' => 'Add SEO optimized title'
            ];
        }

        if ($word_count < 300) {
            $issues[] = [
                'severity' => 'high',
                'issue' => 'Thin content',
                'recommendation' => 'Increase content to at least 600-800 words'
            ];
        }

        if (!preg_match('/<h2/i', $content)) {
            $issues[] = [
                'severity' => 'medium',
                'issue' => 'Missing H2 headings',
                'recommendation' => 'Add structured H2 sections'
            ];
        }

        if (!preg_match('/<img/i', $content)) {
            $issues[] = [
                'severity' => 'low',
                'issue' => 'No images found',
                'recommendation' => 'Add relevant images with ALT text'
            ];
        }

        if (!preg_match('/href=["\'](?!http)/i', $content)) {
            $issues[] = [
                'severity' => 'medium',
                'issue' => 'No internal links',
                'recommendation' => 'Add internal linking to related pages'
            ];
        }

        return $issues;
    }
}