<?php
/**
 * WPSL Install
 *
 * @author Tijmen Smit
 * @since  2.0.0
 */

if ( !defined( 'ABSPATH' ) ) exit;
        
/**
 * Run the install.
 *
 * @since 1.2.20
 * @return void
 */
function wpsl_install( $network_wide ) {

    global $wpdb;

    if ( function_exists( 'is_multisite' ) && is_multisite() ) {

        if ( $network_wide ) {
            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                wpsl_install_data();
            }

            restore_current_blog();     
        } else {
            wpsl_install_data();
        }
    } else {
        wpsl_install_data();
    }    
}

/**
 * Install the required data.
 *
 * @since 1.2.20
 * @return void
 */
function wpsl_install_data() {

    global $wpsl;

    // Register the post type and flush the permalinks.
    $wpsl->post_types->register_post_types();
    flush_rewrite_rules();

    // Create the default settings.
    wpsl_set_default_settings();

    // Set the correct version.
    update_option( 'wpsl_version', WPSL_VERSION_NUM );
    
    // Add user roles.
    wpsl_add_roles();
    
    // Add user capabilities.
    wpsl_add_caps();
} 