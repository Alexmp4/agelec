<?php
if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN ') ) {
	exit;
}

// Check if we need to run the uninstall for a single or mu installation.
if ( !is_multisite() ) {
    wpsl_uninstall();
} else {

    global $wpdb;
    
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();
    
    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
        wpsl_uninstall();  
    }
    
    switch_to_blog( $original_blog_id );
}

// Delete the table ( users who upgraded from 1.x only ), options, store locations and taxonomies from the db.
function wpsl_uninstall() {
	
	global $wpdb, $current_user;
    
    // If the 1.x table still exists we remove it.
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wpsl_stores' );
    
    // Check if we need to delete the autoload transients.
    $option_names = $wpdb->get_results( "SELECT option_name AS transient_name FROM " . $wpdb->options . " WHERE option_name LIKE ('\_transient\_wpsl\_autoload\_%')" );

    if ( $option_names ) {
        foreach ( $option_names as $option_name ) {
            $transient_name = str_replace( "_transient_", "", $option_name->transient_name );

            delete_transient( $transient_name );
        }
    }
    
    // Delete the options used by the plugin.
    $options = array( 'wpsl_version', 'wpsl_settings', 'wpsl_notices', 'wpsl_legacy_support', 'wpsl_flush_rewrite', 'wpsl_delete_transient', 'wpsl_convert_cpt', 'wpsl_valid_server_key' );
    
    foreach ( $options as $option ) {
        delete_option( $option );    
    }
    
    delete_user_meta( $current_user->ID, 'wpsl_disable_location_warning' );
    delete_user_meta( $current_user->ID, 'wpsl_stores_per_page' ); // Not used in 2.x, but was used in 1.x

    // Disable the time limit before we start removing all the store location posts.
    @set_time_limit( 0 );

    // 'any' ignores trashed or auto-draft store location posts, so we make sure they are removed as well.
    $post_statuses = array( 'any', 'trash', 'auto-draft' );

    // Delete the 'wpsl_stores' custom post types.
    foreach ( $post_statuses as $post_status ) {
        $posts = get_posts( array( 'post_type' => 'wpsl_stores', 'post_status' => $post_status, 'posts_per_page' => -1, 'fields' => 'ids' ) );

        if ( $posts ) {
            foreach ( $posts as $post ) {
                wp_delete_post( $post, true );
            }
        }
    }
    
    // Delete the terms, taxonomy and term relationships for the wpsl_store_category.
    $sql = "DELETE t,tt,tr FROM $wpdb->terms AS t
         LEFT JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
         LEFT JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
             WHERE tt.taxonomy = 'wpsl_store_category'";
    
    $wpdb->query( $sql );

    // Remove the WPSL caps and roles.
    include_once( 'admin/roles.php' );

    wpsl_remove_caps_and_roles();
}