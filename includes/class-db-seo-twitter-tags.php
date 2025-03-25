<?php
/**
 * Class DB_SEO_Twitter_Tags
 *
 * Handles the addition of Twitter Card meta tags for the DB SEO plugin.
 */
class DB_SEO_Twitter_Tags {

    /**
     * Add Twitter Card tags to the head section of the page.
     */
    public static function add_twitter_tags() {
        if (is_singular()) {
            $post_id = get_the_ID();

            // Use custom meta title if provided, otherwise fall back to the post title.
            $title = get_post_meta($post_id, '_db_seo_custom_meta_title', true);
            if (empty($title)) {
                $title = get_the_title();
            }

            // Use custom meta description if provided, otherwise fall back to the default meta description or post excerpt.
            $description = get_post_meta($post_id, '_db_seo_custom_meta_description', true);
            if (empty($description)) {
                $description = get_option('db_seo_default_meta_description', get_bloginfo('description'));
                if (empty($description)) {
                    $description = get_the_excerpt();
                }
            }

            // Use custom image if provided, otherwise fall back to the featured image or default image.
            $image = get_post_meta($post_id, '_db_seo_custom_image', true);
            if (empty($image)) {
                $image = self::get_featured_image();
                if (empty($image)) {
                    $image = get_option('db_seo_default_image', '');
                }
            }

            // Ensure HTTPS for image URL
            if ($image) {
                $image = self::force_https_url($image);
            }

            $url = get_permalink();
            $twitter_handle = get_option('db_seo_twitter_handle', '@default_handle');

            echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
            echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
            echo '<meta name="twitter:description" content="' . esc_attr($description) . '" />' . "\n";
            echo '<meta name="twitter:url" content="' . esc_url($url) . '" />' . "\n";
            echo '<meta name="twitter:site" content="' . esc_attr($twitter_handle) . '" />' . "\n";

            if ($image) {
                echo '<meta name="twitter:image" content="' . esc_url($image) . '" />' . "\n";
            }
        }
    }

    /**
     * Get the featured image URL for the current post.
     *
     * @return string|null The URL of the featured image, or null if not available.
     */
    private static function get_featured_image() {
        if (has_post_thumbnail()) {
            $thumbnail_id = get_post_thumbnail_id();
            $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'full');
            if ($thumbnail) {
                return $thumbnail[0];
            }
        }
        return null;
    }
    
    /**
     * Force HTTPS for URLs.
     *
     * @param string $url The URL to convert to HTTPS.
     * @return string The URL with HTTPS protocol.
     */
    private static function force_https_url($url) {
        return str_replace('http://', 'https://', $url);
    }
}
