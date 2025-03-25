<?php
/**
 * Plugin Name: DB SEO
 * Plugin URI: https://digitalbullet.ca
 * Description: Adds SEO meta tags for social media and search engines
 * Version: 1.1.1
 * Author: Digital Bullet
 * Author URI: https://digitalbullet.ca
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: db-seo
 * Domain Path: /languages
 *
 * @package DB_HubSpot_Breakdance
 */

// © 2025 Digital Bullet (https://digitalbullet.ca)
// This plugin is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License v3 as published by
// the Free Software Foundation.

// This plugin is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define plugin constants
define( 'DB_SEO_VERSION', '1.1.1' );
define( 'DB_SEO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DB_SEO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DB_SEO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function db_seo_activate() {
    // Set default options if they don't exist
    if ( false === get_option( 'db_seo_og_enabled' ) ) {
        add_option( 'db_seo_og_enabled', '1' );
    }
    
    if ( false === get_option( 'db_seo_twitter_enabled' ) ) {
        add_option( 'db_seo_twitter_enabled', '1' );
    }
    
    if ( false === get_option( 'db_seo_schema_enabled' ) ) {
        add_option( 'db_seo_schema_enabled', '1' );
    }
}
register_activation_hook( __FILE__, 'db_seo_activate' );

/**
 * Enqueue admin styles.
 */
function db_seo_enqueue_admin_styles( $hook ) {
    // Only enqueue on our settings page or post/page edit screens
    if ( 'toplevel_page_db_seo' === $hook || 'post.php' === $hook || 'post-new.php' === $hook ) {
        wp_enqueue_style( 'db-seo-admin', DB_SEO_PLUGIN_URL . 'assets/css/db-seo-admin.css', array(), DB_SEO_VERSION );
    }
}
add_action( 'admin_enqueue_scripts', 'db_seo_enqueue_admin_styles' );

// Include required files
require_once DB_SEO_PLUGIN_DIR . 'includes/class-db-seo-og-tags.php';
require_once DB_SEO_PLUGIN_DIR . 'includes/class-db-seo-twitter-tags.php';
require_once DB_SEO_PLUGIN_DIR . 'includes/class-db-seo-schema.php';
require_once DB_SEO_PLUGIN_DIR . 'admin/class-db-seo-admin.php';

/**
 * Initialize the plugin functionalities.
 */
function db_seo_initialize() {
    // Add Open Graph tags if enabled
    if ( '1' === get_option( 'db_seo_og_enabled', '1' ) ) {
        add_action( 'wp_head', array( 'DB_SEO_OG_Tags', 'add_og_tags' ) );
    }

    // Add Twitter card tags if enabled
    if ( '1' === get_option( 'db_seo_twitter_enabled', '1' ) ) {
        add_action( 'wp_head', array( 'DB_SEO_Twitter_Tags', 'add_twitter_tags' ) );
    }

    // Add Schema.org markup if enabled
    if ( '1' === get_option( 'db_seo_schema_enabled', '1' ) ) {
        add_action( 'wp_footer', array( 'DB_SEO_Schema', 'add_schema_markup' ) );
    }
}
add_action( 'init', 'db_seo_initialize' );

/**
 * Add plugin action links.
 *
 * @param array $links The plugin action links.
 * @return array The modified plugin action links.
 */
function db_seo_plugin_action_links( $links ) {
    $settings_link = '<a href="' . admin_url( 'admin.php?page=db_seo' ) . '">' . __( 'Settings', 'db-seo' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . DB_SEO_PLUGIN_BASENAME, 'db_seo_plugin_action_links' );
?>
