<?php
/**
 * Class DB_SEO_OG_Tags
 *
 * Handles the addition of Open Graph meta tags for the DB SEO plugin.
 */
class DB_SEO_OG_Tags {

    /**
     * Add Open Graph tags to the head section of the page.
     */
    public static function add_og_tags() {
        if (is_singular() || is_front_page()) {
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

            // Get OG type, either from custom field or default to 'website' for homepage.
            $og_type = get_post_meta($post_id, '_db_seo_og_type', true);
            if (empty($og_type)) {
                $og_type = is_front_page() ? 'website' : 'article';
            }

            $url = get_permalink();
            $site_name = get_bloginfo('name');

            echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
            echo '<meta property="og:type" content="' . esc_attr($og_type) . '" />' . "\n";
            echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
            echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '" />' . "\n";
            echo '<meta property="og:description" content="' . esc_attr($description) . '" />' . "\n";

            if ($image) {
                echo '<meta property="og:image" content="' . esc_url($image) . '" />' . "\n";
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
?>
