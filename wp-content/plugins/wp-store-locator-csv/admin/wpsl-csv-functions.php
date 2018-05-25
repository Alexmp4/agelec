<?php
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Get a list of all the data fields used in WPSL.
 *
 * @since 1.0.0
 * @param boolean $include_wp_post_fields If set to true, include the field names used with wp_insert_post and the image and category field.
 *                                        Otherwise only the names of the meta box fields are returned.
 * @return array  $fields                 List of data fields used in WPSL.
 */
function wpsl_get_field_names( $include_wp_post_fields = true ) {

    global $wpsl_admin; // From the WPSL plugin

    $fields      = array();
    $meta_fields = $wpsl_admin->metaboxes->meta_box_fields();

    /*  
     * Include fields names used with wp_insert post 
     * and the image and category field?
     */
    if ( $include_wp_post_fields ) {
        $fields = array_keys( wpsl_wp_post_field_map() );
        array_push( $fields, 'image', 'category', 'tags' );
    }

    foreach ( $meta_fields as $k => $field_section ) {
        foreach ( $field_section as $field_name => $field_value ) {
            $fields[] = $field_name;
        }
    }

    return $fields;
}

/**
 * Get a list of field names used with wp_insert_post.
 * 
 * The keys are the CSV headers, and the values
 * the fields used with wp_insert_post / wp_update_post.
 *
 * @since 1.0.0
 * @see https://codex.wordpress.org/Function_Reference/wp_insert_post#Parameters
 * @return array $wp_field_map The list of fields used with wp_insert_post.
 */
function wpsl_wp_post_field_map() {

    $wp_field_map = array(
        'wpsl_id'     => 'ID',
        'name'        => 'post_title',
        'status'      => 'post_status',
        'permalink'   => 'post_name',
        'description' => 'post_content',
        'excerpt'     => 'post_excerpt',
        'author'      => 'post_author',
        'date'        => 'post_date'
    );

    return $wp_field_map;
}

/**
 * Run when the plugin is actived.
 * 
 * Check whether we need to create the CSV upload folder in 
 * a single, or multisite installation, and if the plugin 
 * is activated network wide.
 *
 * @since 1.0.0
 * @param boolean $network_wide True when the plugin is activated network wide.
 * @return void
 */
function wpsl_csv_install( $network_wide ) {

    require_once( WPSL_CSV_PLUGIN_DIR . 'admin/roles.php' );

    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
        if ( $network_wide ) {
            $args = array(
                'archived' => 0,
                'spam'     => 0,
                'deleted'  => 0
            );

            // As of WP 4.6 use get_sites instead of wp_get_sites.
            if ( function_exists( 'get_sites' ) ) {
                $mu_sites = get_sites( $args );
            } else {
                $mu_sites = wp_get_sites( $args );
            }

            if ( $mu_sites ) {
                foreach ( $mu_sites as $mu_site ) {
                    $mu_site = (array) $mu_site;

                    switch_to_blog( $mu_site['blog_id'] );

                    wpsl_csv_create_roles();
                    wpsl_csv_create_upload_folder();
                } 
            }

            restore_current_blog();     
        } else {
            wpsl_csv_create_roles();
            wpsl_csv_create_upload_folder();
        }
    } else {
        wpsl_csv_create_roles();
        wpsl_csv_create_upload_folder();
    }  
}

/**
 * Create the required 'wpsl-csv-import' folder 
 * in the correct basedir.
 * 
 * This will be under '/wp-content/uploads' on a single installation.
 * But if this is a multisite installation, then the folder path will
 * be like this 'wp-content/uploads/sites/ - the site id - /'
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_csv_create_upload_folder() {

    $upload_dir = wp_upload_dir();
    $csv_folder = $upload_dir['basedir'] . '/wpsl-csv-import/';

    wp_mkdir_p( $csv_folder );
}

/**
 * Make sure the length of the preview data
 * is restricted to 155 characters.
 *
 * @since 1.2.0
 * @param  string $csv_data Data from csv row.
 * @return string $csv_data The data restricted to 155 chars
 */
function wpsl_csv_limit_preview_data( $csv_data ) {

    if ( strlen( $csv_data ) > 155 ) {
        $csv_data = substr( $csv_data, 0, 152 ) . '...';
    }

    return $csv_data;
}