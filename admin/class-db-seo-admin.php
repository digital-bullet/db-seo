<?php
/**
 * Class DB_SEO_Admin
 *
 * Handles the creation and management of the admin settings page for the DB SEO plugin.
 *
 * @package DB_SEO
 * @since 1.0.0
 */
class DB_SEO_Admin {

    /**
     * Initialize the admin settings page.
     *
     * @since 1.0.0
     */
    public static function init() {
        // Register admin menu and settings
        add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
        
        // Add meta boxes for post edit screens
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
        add_action( 'save_post', array( __CLASS__, 'save_meta_boxes' ) );
        
        // Enqueue scripts for admin
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_media_library' ) );
    }

    /**
     * Add the SEO settings page to the WordPress admin menu.
     *
     * @since 1.0.0
     */
    public static function add_admin_menu() {
        add_menu_page(
            __( 'DB SEO Settings', 'db-seo' ),      // Page title
            __( 'DB SEO', 'db-seo' ),               // Menu title
            'manage_options',                        // Capability
            'db_seo',                                // Menu slug
            array( __CLASS__, 'settings_page' ),     // Callback function
            'dashicons-chart-area'                   // Icon (changed to a more SEO-related icon)
        );
    }

    /**
     * Register the settings for the DB SEO plugin.
     *
     * @since 1.0.0
     */
    public static function register_settings() {
        // Register settings
        register_setting( 'db_seo_settings_group', 'db_seo_og_enabled', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '1',
        ) );
        
        register_setting( 'db_seo_settings_group', 'db_seo_twitter_enabled', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '1',
        ) );
        
        register_setting( 'db_seo_settings_group', 'db_seo_schema_enabled', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '1',
        ) );
        
        register_setting( 'db_seo_settings_group', 'db_seo_default_image', array(
            'type'              => 'string',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        
        register_setting( 'db_seo_settings_group', 'db_seo_twitter_handle', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        
        register_setting( 'db_seo_settings_group', 'db_seo_og_type', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'website',
        ) );
        
        register_setting( 'db_seo_settings_group', 'db_seo_default_meta_description', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
        ) );

        // General settings section
        add_settings_section(
            'db_seo_general_settings',
            __( 'General Settings', 'db-seo' ),
            array( __CLASS__, 'general_settings_section_callback' ),
            'db_seo'
        );
        
        // Social media section
        add_settings_section(
            'db_seo_social_settings',
            __( 'Social Media Settings', 'db-seo' ),
            array( __CLASS__, 'social_settings_section_callback' ),
            'db_seo'
        );

        // Add settings fields to general section
        add_settings_field(
            'db_seo_og_enabled',
            __( 'Enable Open Graph Tags', 'db-seo' ),
            array( __CLASS__, 'og_enabled_field' ),
            'db_seo',
            'db_seo_general_settings'
        );
        
        add_settings_field(
            'db_seo_twitter_enabled',
            __( 'Enable Twitter Cards', 'db-seo' ),
            array( __CLASS__, 'twitter_enabled_field' ),
            'db_seo',
            'db_seo_general_settings'
        );
        
        add_settings_field(
            'db_seo_schema_enabled',
            __( 'Enable Schema.org Markup', 'db-seo' ),
            array( __CLASS__, 'schema_enabled_field' ),
            'db_seo',
            'db_seo_general_settings'
        );
        
        add_settings_field(
            'db_seo_default_meta_description',
            __( 'Default Meta Description', 'db-seo' ),
            array( __CLASS__, 'default_meta_description_field' ),
            'db_seo',
            'db_seo_general_settings'
        );

        // Add settings fields to social section
        add_settings_field(
            'db_seo_default_image',
            __( 'Default Image URL', 'db-seo' ),
            array( __CLASS__, 'default_image_field' ),
            'db_seo',
            'db_seo_social_settings'
        );
        
        add_settings_field(
            'db_seo_twitter_handle',
            __( 'Twitter Site Handle', 'db-seo' ),
            array( __CLASS__, 'twitter_handle_field' ),
            'db_seo',
            'db_seo_social_settings'
        );
        
        add_settings_field(
            'db_seo_og_type',
            __( 'Default Open Graph Type', 'db-seo' ),
            array( __CLASS__, 'og_type_field' ),
            'db_seo',
            'db_seo_social_settings'
        );
    }
    
    /**
     * General settings section description.
     *
     * @since 1.1.1
     */
    public static function general_settings_section_callback() {
        echo '<p>' . __( 'Configure the general SEO settings for your site.', 'db-seo' ) . '</p>';
    }
    
    /**
     * Social media settings section description.
     *
     * @since 1.1.1
     */
    public static function social_settings_section_callback() {
        echo '<p>' . __( 'Configure how your content appears when shared on social media platforms.', 'db-seo' ) . '</p>';
    }

    /**
     * Render the Default Open Graph Type field.
     *
     * @since 1.0.0
     */
    public static function og_type_field() {
        $value = get_option( 'db_seo_og_type', 'website' );
        $options = array(
            'website' => __( 'Website', 'db-seo' ),
            'article' => __( 'Article', 'db-seo' ),
            'profile' => __( 'Profile', 'db-seo' ),
            'video'   => __( 'Video', 'db-seo' ),
            'book'    => __( 'Book', 'db-seo' )
        );
        
        echo '<select name="db_seo_og_type" id="db_seo_og_type">';
        foreach ( $options as $key => $label ) {
            echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . __( 'Select the default Open Graph type to use for the home page or generic pages.', 'db-seo' ) . '</p>';
    }

    /**
     * Enqueue the WordPress media library for image selection.
     *
     * @since 1.0.0
     */
    public static function enqueue_media_library() {
        wp_enqueue_media();
    }

    /**
     * Render the settings page HTML.
     *
     * @since 1.0.0
     */
    public static function settings_page() {
        ?>
        <div class="wrap db-seo-settings-page">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'db_seo_settings_group' );
                do_settings_sections( 'db_seo' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render the Open Graph Tags enabled checkbox.
     *
     * @since 1.0.0
     */
    public static function og_enabled_field() {
        $value = get_option( 'db_seo_og_enabled', '1' );
        echo '<label for="db_seo_og_enabled">';
        echo '<input type="checkbox" id="db_seo_og_enabled" name="db_seo_og_enabled" value="1" ' . checked( '1', $value, false ) . ' />';
        echo ' ' . __( 'Enable Open Graph Tags', 'db-seo' );
        echo '</label>';
        echo '<p class="description">' . __( 'Adds Open Graph meta tags for better sharing on Facebook and other platforms.', 'db-seo' ) . '</p>';
    }

    /**
     * Render the Twitter Cards enabled checkbox.
     *
     * @since 1.0.0
     */
    public static function twitter_enabled_field() {
        $value = get_option( 'db_seo_twitter_enabled', '1' );
        echo '<label for="db_seo_twitter_enabled">';
        echo '<input type="checkbox" id="db_seo_twitter_enabled" name="db_seo_twitter_enabled" value="1" ' . checked( '1', $value, false ) . ' />';
        echo ' ' . __( 'Enable Twitter Cards', 'db-seo' );
        echo '</label>';
        echo '<p class="description">' . __( 'Adds Twitter Card meta tags for better sharing on Twitter.', 'db-seo' ) . '</p>';
    }

    /**
     * Render the Schema.org Markup enabled checkbox.
     *
     * @since 1.0.0
     */
    public static function schema_enabled_field() {
        $value = get_option( 'db_seo_schema_enabled', '1' );
        echo '<label for="db_seo_schema_enabled">';
        echo '<input type="checkbox" id="db_seo_schema_enabled" name="db_seo_schema_enabled" value="1" ' . checked( '1', $value, false ) . ' />';
        echo ' ' . __( 'Enable Schema.org Markup', 'db-seo' );
        echo '</label>';
        echo '<p class="description">' . __( 'Adds Schema.org structured data for better search engine understanding of your content.', 'db-seo' ) . '</p>';
    }
    
    /**
     * Render the Default Meta Description field.
     *
     * @since 1.1.1
     */
    public static function default_meta_description_field() {
        $value = get_option( 'db_seo_default_meta_description', '' );
        echo '<textarea id="db_seo_default_meta_description" name="db_seo_default_meta_description" rows="3" class="large-text">' . esc_textarea( $value ) . '</textarea>';
        echo '<p class="description">' . __( 'Enter a default meta description to use if no custom description is provided.', 'db-seo' ) . '</p>';
    }

    /**
     * Render the Default Image URL field with media library button.
     *
     * @since 1.0.0
     */
    public static function default_image_field() {
        $value = get_option( 'db_seo_default_image', '' );
        echo '<div class="db-seo-image-field">';
        echo '<input type="text" id="db_seo_default_image" name="db_seo_default_image" value="' . esc_attr( $value ) . '" class="regular-text" />';
        echo '<button type="button" class="button db-seo-upload-button" data-target="#db_seo_default_image">' . __( 'Select Image', 'db-seo' ) . '</button>';
        echo '</div>';
        
        if ( ! empty( $value ) ) {
            echo '<div class="db-seo-image-preview"><img src="' . esc_url( $value ) . '" alt="' . __( 'Preview', 'db-seo' ) . '" /></div>';
        }
        
        echo '<p class="description">' . __( 'Enter the URL of the default image to be used if no other image is defined.', 'db-seo' ) . '</p>';
        
        // Script for the media uploader
        ?>
        <script>
            jQuery(document).ready(function($) {
                $(".db-seo-upload-button").click(function(e) {
                    e.preventDefault();
                    var targetInput = $(this).data("target");
                    var imageFrame = wp.media({
                        title: "<?php echo esc_js( __( 'Select or Upload an Image', 'db-seo' ) ); ?>",
                        button: {
                            text: "<?php echo esc_js( __( 'Use this image', 'db-seo' ) ); ?>"
                        },
                        multiple: false
                    });
                    imageFrame.on("select", function() {
                        var attachment = imageFrame.state().get("selection").first().toJSON();
                        $(targetInput).val(attachment.url);
                        // Add or update image preview
                        var previewContainer = $(targetInput).closest('.db-seo-image-field').siblings('.db-seo-image-preview');
                        if (previewContainer.length === 0) {
                            $('<div class="db-seo-image-preview"><img src="' + attachment.url + '" alt="<?php echo esc_js( __( 'Preview', 'db-seo' ) ); ?>" /></div>').insertAfter($(targetInput).closest('.db-seo-image-field'));
                        } else {
                            previewContainer.find('img').attr('src', attachment.url);
                        }
                    });
                    imageFrame.open();
                });
            });
        </script>
        <?php
    }

    /**
     * Render the Twitter Site Handle field.
     *
     * @since 1.0.0
     */
    public static function twitter_handle_field() {
        $value = get_option( 'db_seo_twitter_handle', '' );
        echo '<input type="text" id="db_seo_twitter_handle" name="db_seo_twitter_handle" value="' . esc_attr( $value ) . '" class="regular-text" />';
        echo '<p class="description">' . __( 'Enter the Twitter handle for the site (e.g., @yoursite).', 'db-seo' ) . '</p>';
    }

    /**
     * Add meta boxes for SEO settings to post and page edit screens.
     *
     * @since 1.0.0
     */
    public static function add_meta_boxes() {
        $post_types = apply_filters( 'db_seo_meta_box_post_types', array( 'post', 'page' ) );
        
        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'db_seo_meta_box',
                __( 'DB SEO Meta Settings', 'db-seo' ),
                array( __CLASS__, 'render_meta_box' ),
                $post_type,
                'normal',
                'high'
            );
        }
    }

    /**
     * Render the meta box HTML.
     *
     * @since 1.0.0
     * @param WP_Post $post The post object.
     */
    public static function render_meta_box( $post ) {
        // Add a nonce field for security.
        wp_nonce_field( 'db_seo_save_meta_box', 'db_seo_meta_box_nonce' );

        // Retrieve current meta values.
        $custom_meta_title = get_post_meta( $post->ID, '_db_seo_custom_meta_title', true );
        $custom_meta_description = get_post_meta( $post->ID, '_db_seo_custom_meta_description', true );
        $custom_image = get_post_meta( $post->ID, '_db_seo_custom_image', true );
        $og_type = get_post_meta( $post->ID, '_db_seo_og_type', true );
        
        if ( empty( $og_type ) ) {
            $og_type = 'article'; // Default value for singular posts
        }
        
        ?>
        <div class="db-seo-meta-box">
            <!-- Meta title field -->
            <p>
                <label for="db_seo_custom_meta_title"><strong><?php _e( 'Custom Meta Title:', 'db-seo' ); ?></strong></label>
                <input type="text" id="db_seo_custom_meta_title" name="db_seo_custom_meta_title" value="<?php echo esc_attr( $custom_meta_title ); ?>" class="widefat" />
                <span class="description"><?php _e( 'Enter a custom title for social sharing and search results.', 'db-seo' ); ?></span>
            </p>

            <!-- Meta description field -->
            <p>
                <label for="db_seo_custom_meta_description"><strong><?php _e( 'Custom Meta Description:', 'db-seo' ); ?></strong></label>
                <textarea id="db_seo_custom_meta_description" name="db_seo_custom_meta_description" class="widefat" rows="3"><?php echo esc_textarea( $custom_meta_description ); ?></textarea>
                <span class="description"><?php _e( 'Enter a custom description for social sharing and search results.', 'db-seo' ); ?></span>
            </p>

            <!-- Custom image field with media library button -->
            <p>
                <label for="db_seo_custom_image"><strong><?php _e( 'Custom Image URL:', 'db-seo' ); ?></strong></label>
                <div class="db-seo-image-field">
                    <input type="text" id="db_seo_custom_image" name="db_seo_custom_image" value="<?php echo esc_attr( $custom_image ); ?>" class="widefat" />
                    <button type="button" class="button db-seo-upload-button" data-target="#db_seo_custom_image"><?php _e( 'Select Image', 'db-seo' ); ?></button>
                </div>
                <?php if ( ! empty( $custom_image ) ) : ?>
                    <div class="db-seo-image-preview">
                        <img src="<?php echo esc_url( $custom_image ); ?>" alt="<?php _e( 'Preview', 'db-seo' ); ?>" />
                    </div>
                <?php endif; ?>
                <span class="description"><?php _e( 'Select an image to use for social sharing.', 'db-seo' ); ?></span>
            </p>

            <!-- OG Type field -->
            <p>
                <label for="db_seo_og_type"><strong><?php _e( 'Open Graph Type:', 'db-seo' ); ?></strong></label>
                <select id="db_seo_og_type" name="db_seo_og_type">
                    <?php
                    $og_options = array(
                        'website' => __( 'Website', 'db-seo' ),
                        'article' => __( 'Article', 'db-seo' ),
                        'profile' => __( 'Profile', 'db-seo' ),
                        'video'   => __( 'Video', 'db-seo' ),
                        'book'    => __( 'Book', 'db-seo' )
                    );
                    
                    foreach ( $og_options as $key => $label ) {
                        echo '<option value="' . esc_attr( $key ) . '" ' . selected( $og_type, $key, false ) . '>' . esc_html( $label ) . '</option>';
                    }
                    ?>
                </select>
                <span class="description"><?php _e( 'Select the Open Graph type for this post/page.', 'db-seo' ); ?></span>
            </p>
        </div>
        <?php
    }

    /**
     * Save the meta box values when the post is saved.
     *
     * @since 1.0.0
     * @param int $post_id The post ID.
     * @return int The post ID if meta values weren't saved.
     */
    public static function save_meta_boxes( $post_id ) {
        // Check if our nonce is set.
        if ( ! isset( $_POST['db_seo_meta_box_nonce'] ) ) {
            return $post_id;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['db_seo_meta_box_nonce'], 'db_seo_save_meta_box' ) ) {
            return $post_id;
        }

        // Check this is not an autosave.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }

        // Sanitize and save meta title.
        if ( isset( $_POST['db_seo_custom_meta_title'] ) ) {
            update_post_meta( $post_id, '_db_seo_custom_meta_title', sanitize_text_field( $_POST['db_seo_custom_meta_title'] ) );
        }

        // Sanitize and save meta description.
        if ( isset( $_POST['db_seo_custom_meta_description'] ) ) {
            update_post_meta( $post_id, '_db_seo_custom_meta_description', sanitize_textarea_field( $_POST['db_seo_custom_meta_description'] ) );
        }

        // Sanitize and save custom image.
        if ( isset( $_POST['db_seo_custom_image'] ) ) {
            update_post_meta( $post_id, '_db_seo_custom_image', esc_url_raw( $_POST['db_seo_custom_image'] ) );
        }

        // Sanitize and save OG type.
        if ( isset( $_POST['db_seo_og_type'] ) ) {
            update_post_meta( $post_id, '_db_seo_og_type', sanitize_text_field( $_POST['db_seo_og_type'] ) );
        }
        
        return $post_id;
    }
}

// Initialize the admin settings.
DB_SEO_Admin::init();
?>
