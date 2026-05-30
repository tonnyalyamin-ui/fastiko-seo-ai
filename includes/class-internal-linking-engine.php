<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Internal_Linking_Engine
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
     * Get all SEO pages
     */
    public function get_pages(): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'fastiko_seo_pages';

        return $wpdb->get_results(
            "SELECT * FROM {$table}",
            ARRAY_A
        );
    }

    /**
     * Build graph of pages
     */
    public function build_graph(): array
    {
        $pages = $this->get_pages();

        $graph = [];

        foreach ($pages as $page) {
            $graph[$page['object_id']] = [
                'score' => (int) $page['seo_score'],
                'links' => []
            ];
        }

        foreach ($graph as $from_id => &$node) {

            foreach ($graph as $to_id => $target) {

                if ($from_id === $to_id) {
                    continue;
                }

                // higher score pages get linked more often
                if ($target['score'] >= 60) {
                    $node['links'][] = $to_id;
                }
            }
        }

        return $graph;
    }

    /**
     * Find hub pages (high authority inside site)
     */
    public function get_hub_pages(int $min_score = 80): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'fastiko_seo_pages';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE seo_score >= %d ORDER BY seo_score DESC",
                $min_score
            ),
            ARRAY_A
        );
    }

    /**
     * Generate internal link suggestions
     */
    public function generate_links(): array
    {
        $pages = $this->get_pages();

        $links = [];

        foreach ($pages as $from) {
            foreach ($pages as $to) {

                if ($from['object_id'] === $to['object_id']) {
                    continue;
                }

                // rule-based relevance
                if ($this->is_relevant($from, $to)) {
                    $links[] = [
                        'from' => $from['object_id'],
                        'to' => $to['object_id'],
                        'anchor' => $to['title'],
                        'url' => $to['url']
                    ];
                }
            }
        }

        return $links;
    }

    /**
     * Simple relevance scoring
     */
    private function is_relevant(array $from, array $to): bool
    {
        // same type pages = high relevance
        if ($from['object_type'] === $to['object_type']) {
            return true;
        }

        // high SEO score pages link more
        if ($to['seo_score'] > 70) {
            return true;
        }

        return false;
    }

    /**
     * Inject internal links into content (basic AI-less version)
     */
    public function inject_links(string $content, array $links): string
    {
        foreach ($links as $link) {

            $anchor = esc_html($link['anchor']);
            $url    = esc_url($link['url']);

            $pattern = '/' . preg_quote($anchor, '/') . '/i';

            $replacement = '<a href="' . $url . '">' . $anchor . '</a>';

            $content = preg_replace($pattern, $replacement, $content, 1);
        }

        return $content;
    }
}