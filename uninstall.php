<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

/**
 * Tables
 */
$pages_table   = $wpdb->prefix . 'fastiko_seo_pages';
$issues_table  = $wpdb->prefix . 'fastiko_seo_issues';
$suggest_table = $wpdb->prefix . 'fastiko_seo_suggestions';

/**
 * Drop plugin tables
 */
$wpdb->query("DROP TABLE IF EXISTS {$pages_table}");
$wpdb->query("DROP TABLE IF EXISTS {$issues_table}");
$wpdb->query("DROP TABLE IF EXISTS {$suggest_table}");

/**
 * Delete plugin options
 */
delete_option('fastiko_openai_key');
delete_option('fastiko_gemini_key');
delete_option('fastiko_default_provider');

/**
 * (optional) remove post meta created by plugin
 */
$wpdb->query("
    DELETE FROM {$wpdb->postmeta}
    WHERE meta_key IN (
        '_yoast_wpseo_metadesc'
    )
");