<?php
add_action( 'wpsl_csv_tools', 'wpsl_csv_tools' );

/**
 * Handle the different actions from the tools section.
 *
 * @todo add support for Google Docs Spreadsheet import & cron job import from external URL ( pw protected ? ).
 * @since 1.1.0
 * @return void
 */
function wpsl_csv_tools() {

    if ( isset( $_POST['wpsl_csv_tools']['bulk_delete'] ) ) {
        wpsl_csv_bulk_delete();
    }
}

/**
 * Bulk delete all the locations.
 *
 * @since 1.1.0
 * @return void
 */
function wpsl_csv_bulk_delete() {

    global $wpdb, $wpsl_admin;

    check_admin_referer( 'wpsl_csv_tools', 'wpsl_csv_tools_nonce' );

    if ( !current_user_can( 'wpsl_csv_manager_tools' ) ) {
        wp_die( __( 'You do not have permission to delete the location data.', 'wpsl-csv' ), '', array( 'response' => 403 ) );
    }

    // Delete all wpsl_stores related data
    $result = $wpdb->query('
        DELETE a,b,c FROM ' . $wpdb->prefix . 'posts a
        LEFT JOIN ' . $wpdb->prefix . 'term_relationships b ON ( a.ID = b.object_id )
        LEFT JOIN ' . $wpdb->prefix . 'postmeta c ON ( a.ID = c.post_id )
        WHERE a.post_type = "wpsl_stores"'
    );

    if ( $result ) {
        // Update the term count
        wpsl_csv_update_term_count();

        $wpsl_admin->notices->save( 'update', __( 'Successfully deleted all locations!', 'wpsl-csv' ) );

        // If we don't force a redirect, the notices don't show up...
        wp_redirect( admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_csv&section=tools' ) );
        exit();
    }
}

/**
 * Update the term count
 *
 * @since 1.1.0
 * @return void
 */
function wpsl_csv_update_term_count() {

    $update_taxonomies = array( 'wpsl_store_category', 'wpsl_store_tags' );

    foreach ( $update_taxonomies as $update_taxonomy ) {
        $get_terms_args = array(
            'taxonomy'   => $update_taxonomy,
            'fields'     => 'ids',
            'hide_empty' => false
        );

        $update_terms = get_terms( $get_terms_args );

        if ( !is_wp_error( $update_terms ) && $update_terms ) {
            wp_update_term_count_now( $update_terms, $update_taxonomy );
        }
    }
}