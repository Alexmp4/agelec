<?php
if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN ') ) {
    exit;
}

// Check if we need to run the uninstall for a single or mu installation.
if ( !is_multisite() ) {
    wpsl_csv_uninstall();
} else {

    global $wpdb;

    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
        wpsl_csv_uninstall();
    }

    switch_to_blog( $original_blog_id );
}

/**
 * Remove the CSV Manager roles on uninstall.
 *
 * @since 1.1.0
 * @return void
 */
function wpsl_csv_uninstall() {

    // Remove the CSV Manager caps.
    include_once( 'admin/roles.php' );
    wpsl_csv_remove_caps_and_roles();

    // Remove the CSV Manager options.
    delete_option( 'wpsl_csv_version' );
    delete_option( 'wpsl_csv_iso_fixed' );
}