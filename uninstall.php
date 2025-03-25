<?php
/**
 * DB SEO Plugin Uninstaller
 *
 * This file runs when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all plugin options
delete_option('db_seo_og_enabled');
delete_option('db_seo_twitter_enabled');
delete_option('db_seo_schema_enabled');
delete_option('db_seo_default_image');
delete_option('db_seo_twitter_handle');
delete_option('db_seo_og_type');
delete_option('db_seo_default_meta_description');

// Delete all post meta
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_db_seo_%'"); 