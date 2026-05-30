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
        $suggestions_table = $wpdb->prefix . 'fastiko_seo_suggestions';
        $history_table = $wpdb->prefix . 'fastiko_seo_history';
        $queue_table = $wpdb->prefix . 'fastiko_seo_queue';

        $sql_pages = "
        CREATE TABLE {$pages_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            object_id BIGINT UNSIGNED NOT NULL,
            object_type VARCHAR(50) NOT NULL,
            url TEXT NOT NULL,
            title TEXT NULL,
            seo_score INT DEFAULT 0,
            scanned_at DATETIME NULL,
            PRIMARY KEY (id),
            UNIQUE KEY object_unique (object_id, object_type),
            KEY seo_score (seo_score)
        ) {$charset};
        ";

        $sql_issues = "
        CREATE TABLE {$issues_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            page_id BIGINT UNSIGNED NOT NULL,
            severity VARCHAR(20) NOT NULL,
            issue_name VARCHAR(255) NOT NULL,
            recommendation TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY page_id (page_id),
            KEY severity (severity)
        ) {$charset};
        ";

        $sql_suggestions = "
        CREATE TABLE {$suggestions_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            page_id BIGINT UNSIGNED NOT NULL,
            provider VARCHAR(20) NOT NULL,
            title TEXT NULL,
            meta_description TEXT NULL,
            h1 TEXT NULL,
            faq_json LONGTEXT NULL,
            schema_json LONGTEXT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY page_id (page_id),
            KEY provider (provider),
            KEY status (status)
        ) {$charset};
        ";

        $sql_history = "
        CREATE TABLE {$history_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            page_id BIGINT UNSIGNED NOT NULL,
            field_name VARCHAR(100) NOT NULL,
            old_value LONGTEXT NULL,
            new_value LONGTEXT NULL,
            applied_by BIGINT UNSIGNED NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY page_id (page_id)
        ) {$charset};
        ";

        $sql_queue = "
        CREATE TABLE {$queue_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            page_id BIGINT UNSIGNED NOT NULL,
            task_type VARCHAR(50) NOT NULL,
            provider VARCHAR(20) NOT NULL,
            status VARCHAR(20) DEFAULT 'waiting',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            processed_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY page_id (page_id),
            KEY status (status),
            KEY provider (provider)
        ) {$charset};
        ";

        dbDelta($sql_pages);
        dbDelta($sql_issues);
        dbDelta($sql_suggestions);
        dbDelta($sql_history);
        dbDelta($sql_queue);

        update_option(
            'fastiko_seo_ai_db_version',
            FASTIKO_SEO_AI_DB_VERSION
        );
    }

    public static function maybe_upgrade(): void
    {
        $installed_version = get_option(
            'fastiko_seo_ai_db_version',
            '0'
        );

        if (
            version_compare(
                $installed_version,
                FASTIKO_SEO_AI_DB_VERSION,
                '<'
            )
        ) {
            self::create_tables();
        }
    }
}
