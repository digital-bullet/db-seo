<?php
/**
 * DB SEO Admin Settings
 *
 * This file contains functions to register and render global settings for the DB SEO plugin.
 */

// Register settings fields for DB SEO
function db_seo_register_global_settings() {
    register_setting('db_seo_settings_group', 'db_seo_default_meta_description');
    register_setting('db_seo_settings_group', 'db_seo_default_meta_keywords');
    register_setting('db_seo_settings_group', 'db_seo_default_author');

    add_settings_section(
        'db_seo_global_settings',
        'Global Settings',
        'db_seo_global_settings_description',
        'db_seo'
    );

    add_settings_field(
        'db_seo_default_meta_description',
        'Default Meta Description',
        'db_seo_default_meta_description_field',
        'db_seo',
        'db_seo_global_settings'
    );

    add_settings_field(
        'db_seo_default_meta_keywords',
        'Default Meta Keywords',
        'db_seo_default_meta_keywords_field',
        'db_seo',
        'db_seo_global_settings'
    );

    add_settings_field(
        'db_seo_default_author',
        'Default Author',
        'db_seo_default_author_field',
        'db_seo',
        'db_seo_global_settings'
    );
}
add_action('admin_init', 'db_seo_register_global_settings');

/**
 * Description for the global settings section.
 */
function db_seo_global_settings_description() {
    echo '<p>Configure global SEO settings for the entire site.</p>';
}

/**
 * Render the default meta description field.
 */
function db_seo_default_meta_description_field() {
    $value = get_option('db_seo_default_meta_description', '');
    echo '<input type="text" name="db_seo_default_meta_description" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">Enter a default meta description to be used if no specific description is provided.</p>';
}

/**
 * Render the default meta keywords field.
 */
function db_seo_default_meta_keywords_field() {
    $value = get_option('db_seo_default_meta_keywords', '');
    echo '<input type="text" name="db_seo_default_meta_keywords" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">Enter default meta keywords to be used if no specific keywords are provided.</p>';
}

/**
 * Render the default author field.
 */
function db_seo_default_author_field() {
    $value = get_option('db_seo_default_author', '');
    echo '<input type="text" name="db_seo_default_author" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">Enter a default author name to be used for posts if no author is specified.</p>';
}
?>