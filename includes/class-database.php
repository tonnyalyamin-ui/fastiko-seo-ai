<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fastiko_SEO_AI_Database
{
    public static function create_tables(): void
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset = $wpdb->get_charset_collate();

        $pages_table = $wpdb->prefix . 'fastiko_seo_pages';
        $issues_table = $wpdb->prefix . 'fastiko_seo_issues';

        $sql1 = "
        CREATE TABLE {$pages_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            object_id BIGINT UNSIGNED NOT NULL,
            object_type VARCHAR(50) NOT NULL,
            url TEXT NOT NULL,
            title TEXT NULL,
            seo_score INT DEFAULT 0,
            scanned_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY object_id (object_id)
        ) {$charset};
        ";

        $sql2 = "
        CREATE TABLE {$issues_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            page_id BIGINT UNSIGNED NOT NULL,
            severity VARCHAR(20) NOT NULL,
            issue_name VARCHAR(255) NOT NULL,
            recommendation TEXT NULL,
            PRIMARY KEY (id),
            KEY page_id (page_id)
        ) {$charset};
        ";

        dbDelta($sql1);
        dbDelta($sql2);
    }
}