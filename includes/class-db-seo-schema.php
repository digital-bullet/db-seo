<?php
/**
 * Class DB_SEO_Schema
 *
 * Handles the addition of Schema.org JSON-LD markup for the DB SEO plugin.
 *
 * @package DB_SEO
 * @since 1.0.0
 */
class DB_SEO_Schema {

    /**
     * Add Schema.org JSON-LD markup to the footer section of the page.
     *
     * @since 1.0.0
     */
    public static function add_schema_markup() {
        // Only add schema markup if enabled in settings
        if ( '1' !== get_option( 'db_seo_schema_enabled', '1' ) ) {
            return;
        }
        
        if ( is_singular() ) {
            $schema = array(
                '@context'         => 'https://schema.org',
                '@type'            => 'Article',
                'headline'         => self::get_meta_title(),
                'mainEntityOfPage' => array(
                    '@type' => 'WebPage',
                    '@id'   => get_permalink(),
                ),
                'datePublished'    => get_the_date( 'c' ),
                'dateModified'     => get_the_modified_date( 'c' ),
                'author'           => array(
                    '@type' => 'Person',
                    'name'  => get_the_author(),
                ),
                'publisher'        => array(
                    '@type' => 'Organization',
                    'name'  => get_bloginfo( 'name' ),
                    'logo'  => array(
                        '@type' => 'ImageObject',
                        'url'   => self::get_site_logo(),
                    ),
                ),
                'description'      => self::get_meta_description(),
                'image'            => self::get_custom_or_featured_image(),
            );

            // Apply filters to allow other plugins to modify the schema markup
            $schema = apply_filters( 'db_seo_schema_markup', $schema, get_the_ID() );
            
            echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
        } elseif ( is_front_page() ) {
            // Special schema for homepage
            $schema = array(
                '@context'        => 'https://schema.org',
                '@type'           => 'WebSite',
                'name'            => get_bloginfo( 'name' ),
                'alternateName'   => wp_get_document_title(),
                'url'             => home_url(),
                'potentialAction' => array(
                    '@type'       => 'SearchAction',
                    'target'      => home_url( '/?s={search_term_string}' ),
                    'query-input' => 'required name=search_term_string',
                ),
            );
            
            // Apply filters to allow other plugins to modify the schema markup
            $schema = apply_filters( 'db_seo_schema_markup_home', $schema );
            
            echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
        }
    }

    /**
     * Get the featured image or the custom image URL for the current post.
     *
     * @since 1.0.0
     * @return string The URL of the custom image, featured image, or default image.
     */
    private static function get_custom_or_featured_image() {
        $custom_image = get_post_meta( get_the_ID(), '_db_seo_custom_image', true );
        if ( ! empty( $custom_image ) ) {
            return esc_url( $custom_image );
        }
        
        $featured_image = self::get_featured_image();
        if ( $featured_image ) {
            return $featured_image;
        }
        
        $default_image = get_option( 'db_seo_default_image', '' );
        if ( ! empty( $default_image ) ) {
            return esc_url( $default_image );
        }
        
        return '';
    }

    /**
     * Get the featured image URL for the current post.
     *
     * @since 1.0.0
     * @return string|null The URL of the featured image, or null if not available.
     */
    private static function get_featured_image() {
        if ( has_post_thumbnail() ) {
            $thumbnail_id = get_post_thumbnail_id();
            $thumbnail = wp_get_attachment_image_src( $thumbnail_id, 'full' );
            if ( $thumbnail ) {
                return $thumbnail[0];
            }
        }
        return null;
    }

    /**
     * Get the site logo URL.
     *
     * @since 1.0.0
     * @return string The URL of the site logo, or a default image if not available.
     */
    private static function get_site_logo() {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
            if ( $logo ) {
                return $logo[0];
            }
        }
        
        // If no logo is found, use the default image
        $default_image = get_option( 'db_seo_default_image', '' );
        if ( ! empty( $default_image ) ) {
            return esc_url( $default_image );
        }
        
        // If no default image is set, return a transparent 1x1 pixel
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
    }

    /**
     * Get the custom meta title or the post title.
     *
     * @since 1.0.0
     * @return string The custom meta title or the post title.
     */
    private static function get_meta_title() {
        $custom_meta_title = get_post_meta( get_the_ID(), '_db_seo_custom_meta_title', true );
        if ( ! empty( $custom_meta_title ) ) {
            return esc_attr( $custom_meta_title );
        }
        return get_the_title();
    }

    /**
     * Get the custom meta description or the default meta description.
     *
     * @since 1.0.0
     * @return string The custom meta description, excerpt, or default description.
     */
    private static function get_meta_description() {
        $custom_meta_description = get_post_meta( get_the_ID(), '_db_seo_custom_meta_description', true );
        if ( ! empty( $custom_meta_description ) ) {
            return esc_attr( $custom_meta_description );
        }
        
        $excerpt = get_the_excerpt();
        if ( ! empty( $excerpt ) ) {
            return esc_attr( $excerpt );
        }
        
        $default_description = get_option( 'db_seo_default_meta_description', '' );
        if ( ! empty( $default_description ) ) {
            return esc_attr( $default_description );
        }
        
        return esc_attr( get_bloginfo( 'description' ) );
    }
}
?>