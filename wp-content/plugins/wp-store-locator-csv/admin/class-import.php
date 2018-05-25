<?php
/**
 * Handle WPSL CSV Import
 * 
 * @since  1.0.0
 * @author Tijmen Smit
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_CSV_Import' ) ) {
    
	class WPSL_CSV_Import {
        
        /**
         * @since 1.0.0
         * @var WPSL_ParseCSV $parse_csv
         */
        public $parse_csv;
        
        /**
         * @since 1.0.0
         * @var WPSL_Geocode $geocode
         */
        public $geocode;
        
        /**
         * @since 1.0.0
         * @var string Holds the url of the CSV admin page.
         */
        public $page;
        
        /**
         * Class constructor
         */
        function __construct() {
            
            $this->parse_csv = new WPSL_ParseCSV();
            $this->geocode   = new WPSL_Geocode(); // From the WPSL plugin

            $this->page      = 'edit.php?post_type=wpsl_stores&page=wpsl_csv';
            
            add_action( 'wpsl_csv_upload', array( $this, 'upload' ) );
            add_action( 'wpsl_csv_import', array( $this, 'import' ) );
        }
        
        /**
         * Handle the uploaded CSV file.
         * 
         * Either redirect to the next page where the user can match the
         * CSV headers with the available WPSL post meta fields, or show
         * an error if there was a problem with the uploaded file.
         * 
         * @since 1.0.0
         * @return void
         */
        public function upload() {

            if ( isset( $_FILES['wpsl_csv_file'] ) ) {

                check_admin_referer( 'wpsl_csv_upload', 'wpsl_csv_upload_nonce' );

                if ( !current_user_can( 'wpsl_csv_manager' ) ) {
                    return;
                }

                if ( $_FILES['wpsl_csv_file']['error'] == 0 ) {
                    
                    // Make sure the uploaded CSV file contains data rows.
                    $this->csv_data_exists();

                    if ( isset( $_POST['wpsl_csv_dup_check'] ) ) {
                        if ( in_array( $_POST['wpsl_csv_dup_handling'], array( 'skip', 'update' ) ) ) {
                            $dup_args = array( 'duplicate_handling' => $_POST['wpsl_csv_dup_handling'] );
                        }
                    }

                    if ( move_uploaded_file( $_FILES['wpsl_csv_file']['tmp_name'], WPSL_CSV_IMPORT_FILE ) ) {
                        $query_args = array( 'section' => 'match_fields' );
                        
                        $this->parse_csv->auto( WPSL_CSV_IMPORT_FILE );

                        foreach ( $this->parse_csv->data as $csv_row => $csv_data ) {
                            if ( !$this->check_required_fields( $csv_data ) ) {
                                $query_args['error_type'] = 'file';
                                $query_args['code']       = 'required_fields';

                                break;
                            }
                        }

                    } else {
                        $query_args = array( 'error_type' => 'upload', 'code' => '0' );
                    }

                    // Check if we need to check for duplicate locations
                    if ( isset( $dup_args ) ) {
                        $query_args = array_merge( $query_args, $dup_args );
                    }

                    wp_redirect( add_query_arg( $query_args, admin_url( $this->page ) ) );
                    exit();
                } else {
                    wp_redirect( add_query_arg( array( 'error_type' => 'upload', 'code' => $_FILES['wpsl_csv_file']['error'] ), admin_url( $this->page ) ) );
                    exit();
                }
            }
        }
        
        /**
         * Make sure the uploaded CSV file contains data rows.
         * 
         * If this is not the case, then the user is redirected back to 
         * the Import page, and an error notice is shown about the empty file.
         *
         * @since 1.0.0
         * @return void
         */
        public function csv_data_exists() {
            
            $this->parse_csv->auto( $_FILES['wpsl_csv_file']['tmp_name'] );

            if ( empty( $this->parse_csv->data ) ) {
                wp_redirect( add_query_arg( array( 'error_type' => 'file', 'code' => 'empty' ), admin_url( $this->page ) ) );
                exit();
            }
        }
        
        /**
         * Allow users to match the CSV headers with the WPSL meta fields.
         *
         * @since 1.0.0
         * @return string|void $csv_select_list One row of CSV data | redirect if an error occured.
         */
        public function match_fields() {

            $output = '';

            $this->parse_csv->auto( WPSL_CSV_IMPORT_FILE );

            if ( !empty( $this->parse_csv->data ) ) {
                $output .= '<table>';
                $output .= '<tr>';
                $output .= '<th class="wpsl-mapping-field-sml">' . __( 'WPSL Fields', 'wpsl-csv' ) . '</th>';
                $output .= '<th class="wpsl-mapping-field-sml">' . __( 'CSV Headers', 'wpsl-csv' ) . '</th>';
                $output .= '<th>' . __( 'CSV Values', 'wpsl-csv' ) . '</th>';
                $output .= '</tr>';

                /*
                 * Loop over the titles from the imported CSV file, and create a dropdown where
                 * users can match the CSV headers with the available WPSL data fields.
                 */
                foreach ( $this->parse_csv->titles as $index => $title ) {
                    if ( isset( $title ) && $title ) {
                        $output .= '<tr>';
                        $output .= '<td>' . $this->create_csv_select_fields( $index, $title ) . '</td>';
                        $output .= '<td><label for="' . esc_attr( str_replace( ' ', '_', strtolower( $title ) ) ) . '">' . esc_html( $title ) . '</label></td>';
                        $output .= '<td>' . esc_html( wpsl_csv_limit_preview_data( $this->parse_csv->data[0][ $title ] ) ) . '</td>';
                        $output .= '</tr>';
                    }
                }

                $output .= '</table>';
            }

            return $output;
        }
        
        /**
         * Built the dropdown list for the CSV preview with the available field names.
         *
         * @since 1.0.0
         * @param  integer $index  The value for the 'name' attribute
         * @param  string  $title  The title of the CSV column
         * @return string  $select The HTML for the dropdown
         */
		public function create_csv_select_fields( $index, $title ) {

            $field_names = wpsl_get_field_names();

            $select = "<select id=" . esc_attr( str_replace( ' ', '_', strtolower( $title ) ) ) . " name='csv_fields[" . absint( $index ) . "]'>";
            $select .= "<option value=''>" . __( 'Unmapped', 'wpsl-csv' ) . "</option>";

            foreach ( $field_names as $k => $field_name ) {
                $select .= "<option value='" . esc_attr( $field_name ) . "' " . selected( strtolower( $field_name ), trim( str_replace( ' ', '_', strtolower( $title ) ) ), false ) . " >" . esc_html( $field_name ) . "</option>";
            }

            $select .= "</select>";

            return $select;
        }

        /**
         * Process the imported CSV file.
         *
         * @since 1.0.0
         * @return void
         */
        public function import() {

            global $wpsl_settings; // From the WPSL plugin.
            
            check_admin_referer( 'wpsl_csv_import', 'wpsl_csv_import_nonce' );
            
            if ( !current_user_can( 'wpsl_csv_manager' ) ) {
                return;
            }

            wp_defer_term_counting( true );
            wp_defer_comment_counting( true );

            // Try to disable the time limit to prevent timeouts.
            @set_time_limit( 0 );

            $csv_fields       = $_POST['csv_fields'];
            $csv_field_types  = array();
            $add_locations    = array();
            $remove_locations = array();
            $import_results   = array_fill_keys( array( 'imported', 'updated', 'removed', 'skipped' ), 0 );
            $duplicate_check  = '';

            if ( isset( $_POST['wpsl_duplicate_handling'] ) && in_array( $_POST['wpsl_duplicate_handling'], array( 'skip', 'update' ) ) ) {
                $duplicate_check = $_POST['wpsl_duplicate_handling'];
            }

            // Load the CSV file.
            $this->parse_csv->auto( WPSL_CSV_IMPORT_FILE );

            // Get the WPSL field names.
            $field_names = wpsl_get_field_names();

            // Loop over the list of WPSL field names, and map it to the selected dropdown values.
            foreach ( $field_names as $index => $field_name ) {
                $csv_field_types[$field_name] = array_search( $field_name, $csv_fields );
            }

            // Loop over the store date from the CSV file.
            foreach ( $this->parse_csv->data as $csv_row => $csv_data ) {
                $csv_data = array_values( $csv_data );

                // Map the selected field types to the correct store data fields.
                foreach ( $csv_field_types as $index => $field_type ) {
                    if ( is_numeric( $field_type ) ) {
                        $sorted_csv_data[$index] = $csv_data[$field_type];
                    } else {
                        $sorted_csv_data[$index] = '';
                    }
                }

                // If the 'wpsl_id' is set, and it's the only set field, then add the store id to the bulk delete list
                if ( is_numeric( $sorted_csv_data['wpsl_id'] ) && count( array_filter( $sorted_csv_data ) ) == 1 ) {
                    array_push( $remove_locations, $sorted_csv_data['wpsl_id'] );
                } else {
                    array_push( $add_locations, $sorted_csv_data );
                }
            }

            // Bulk remove store locations
            if ( $remove_locations ) {
                $removed_count = $this->remove_store_locations( $remove_locations );

                // Used in the notice after the import
                $import_results['removed'] = $removed_count;
            }

            // Get the post meta fields, but remove the 'hours' field. This field requires special attention.
            $meta_keys = array_values( array_diff( wpsl_get_field_names( false ), array( 'hours' ) ) );
            
            // Get the fields that are used as the args for wp_insert_post / wp_update_post.
            $post_fields = array_flip( wpsl_wp_post_field_map() );

            // To make sure we can point to the row # where a problem happened.
            $i = 1;

            // Loop over the collected store locations and save them.
            foreach ( $add_locations as $store_location ) {
                $i++;

                // If the name, address and city fields are empty, then skip this row.
                if ( !$this->check_required_fields( $store_location ) ) {
                    continue;
                }

                // If the duplicate check is enabled, then either skip or set the ID to trigger an update.
                if ( $duplicate_check ) {
                    $duplicate_id = $this->is_duplicate( $store_location );

                    if ( $duplicate_id ) {
                        if ( $duplicate_check == 'skip' ) {
                            $import_results['skipped']++;

                            continue;
                        } else {

                            // Set the ID from the duplicate location in the DB to the row being
                            // processed to make sure the data is used to update the existing location data.
                            $store_location['wpsl_id'] = $duplicate_id;
                        }
                    }
                }
                
                $add_data  = true;
                $post_args = $this->get_post_args( $post_fields, $store_location );
                $wpsl_id   = isset( $post_args['ID'] ) ? $post_args['ID'] : '';

                // If no post_status is provided, then we default to 'publish'.
                if ( !isset( $post_args['post_status'] ) || empty( $post_args['post_status'] ) ) {
                    $post_args['post_status'] = 'publish';
                }

                /*
                 * Check if we need to create a new store location,
                 * or update existing location data. 
                 * 
                 * Updating existing location data requires a wpsl_id 
                 * that's assigned to a 'wpsl_stores' post type.
                 */
                if ( !strlen( trim( $wpsl_id ) ) ) {
                    $post_args['post_type'] = 'wpsl_stores';

                    $post_id = wp_insert_post( $post_args, true );
                } else if ( is_numeric( $wpsl_id ) ) {

                    /* 
                     * Check if the imported 'wpsl_id' belongs 
                     * to a 'wpsl_stores' post types. 
                     * 
                     * If this is not the case, then we create a WP_Error.
                     */
                    if ( get_post_type( $wpsl_id ) == 'wpsl_stores' ) {
                        $add_data = false;
                        $post_id  = wp_update_post( $post_args, true );
                    } else {
                        $post_id = new WP_Error( 'invalid_id', sprintf( __( 'Update failed! The provided wpsl_id %s doesn\'t belong to a store location.', 'wpsl-csv' ), $store_location['wpsl_id'] ) );
                    }
                } else {
                    $post_id  = '';
                }
                
                if ( !is_wp_error( $post_id ) && $post_id ) {
                    
                    /*
                     * Save / update ( based on the $add_data true|false ) the post meta fields that 
                     * don't require any special attention. Like the phone, fax, email, url and any 
                     * custom fields that where added by filters.
                     */
                    foreach ( $meta_keys as $meta_key ) {
                        if ( isset( $store_location[$meta_key] ) && !empty( $store_location[$meta_key] ) ) {
                            $this->process_location_meta( $post_id, $meta_key, $store_location[$meta_key], $add_data );
                        } else {
                            delete_post_meta( $post_id, 'wpsl_' . $meta_key );
                        }
                    }

                    /*
                     * Check if we need to assign the store location to a categorie / tag.
                     *
                     * If the field is empty and we are updating existing location data,
                     * then we remove the terms from the object.
                     */
                    if ( isset( $store_location['category'] ) && $store_location['category'] ) {
                        $categories = explode( '|', $store_location['category'] );
                        wp_set_object_terms( $post_id, $categories, 'wpsl_store_category' );
                    } else if ( !$add_data ) {
                        wp_set_object_terms( $post_id, NULL, 'wpsl_store_category' );
                    }

                    if ( isset( $store_location['tags'] ) && $store_location['tags'] ) {
                        $tags = explode( '|', $store_location['tags'] );
                        wp_set_object_terms( $post_id, $tags, 'wpsl_store_tags' );
                    } else if ( !$add_data ) {
                        wp_set_object_terms( $post_id, NULL, 'wpsl_store_tags' );
                    }

                    /*
                     * If we have an image, set it as the featured image.
                     * 
                     * Otherwise check if we are updating an existing store location.
                     * 
                     * If this is the case, and the 'image' field in the imported data is left empty, 
                     * but the current post id has an existing post thumbnail, 
                     * then we delete the old post thumbnail.                     
                     */
                    if ( isset( $store_location['image'] ) && $store_location['image'] ) {

                        /*
                         * If the image field contains an ID, then use it to set the post thumbnail. 
                         * Otherwise try to download the image before setting the post thumbnail.   
                         */
                        if ( is_numeric( $store_location['image'] ) ) {
                            set_post_thumbnail( $post_id, $store_location['image'] );
                        } else {
                            $this->set_featured_image( $post_id, $store_location['image'] );
                        }
                    } else if ( !$add_data && has_post_thumbnail( $post_id ) ) {
                        delete_post_thumbnail( $post_id );
                    }

                    // Handle the imported opening hours
                    if ( isset( $store_location['hours'] ) && $store_location['hours'] ) {

                        /*
                         * Imported opening hours for the dropdown input need to be formated 
                         * in a specific way before we can show them in dropdowns in the wp-admin area.
                         */
                        if ( $wpsl_settings['editor_hour_input'] == 'dropdown' ) {
                            $store_location['hours'] = $this->format_opening_hours( $store_location['hours'] );
                        }

                        $this->process_location_meta( $post_id, 'hours', $store_location['hours'], $add_data );
                    } else if ( !$wpsl_settings['hide_hours'] ) {

                        /*
                         * If no openings hour are provided, and they are not set to hidden, 
                         * then we use the defaults from the settings page.
                         * 
                         * Only users who upgraded from WPSL 1.x can have the textarea option.  
                         */
                        if ( $wpsl_settings['editor_hour_input'] == 'dropdown' ) {
                            $default_hours = $wpsl_settings['editor_hours']['dropdown'];
                        } else {
                            $default_hours = $wpsl_settings['editor_hours']['textarea'];                            
                        }

                        $this->process_location_meta( $post_id, 'hours', $default_hours, $add_data );
                    }

                    // Check if we need to geocode the provided location details before saving the geolocation data.
                    if ( empty( $store_location['lat'] ) || empty( $store_location['lng'] ) ) {
                        $response = $this->geocode_imported_data( $post_id, $store_location );

                        if ( $response ) {
                            switch ( $response['type'] ) {
                                case 'zero_results':
                                    $import_results['geocode_errors']['zero_results'][] = $response['msg'];
                                    break;
                                case 'failed':
                                    $import_results['geocode_errors']['failed'][] = $response['msg'];
                                    break;
                            }
                        }
                    } else {
                        $this->add_geolocation_meta_data( $post_id, $store_location );
                    }

                    if ( $add_data ) {
                        $import_results['imported']++;
                    } else {
                        $import_results['updated']++;
                    }
                } else {
                    if ( is_wp_error( $post_id ) ) {
                        $error_msg  = $post_id->get_error_message();
                        $error_code = $post_id->get_error_code();
                    } else {
                        $error_msg  = sprintf( __( 'Update failed! Make sure %s is a valid wpsl_id', 'wpsl-csv' ), esc_html( $wpsl_id ) );
                        $error_code = '';
                    }

                    $import_results['failed'][] = array(
                        'row'      => $i,
                        'location' => $store_location['address'],
                        'msg'      => $error_msg,
                        'code'     => $error_code
                    );
                }
            } // end foreach

            // Show the number of imported locations, and possible errors.
            $this->show_import_results( $import_results );

            // Clean up
            $this->import_ended();

            // If we don't force a redirect, the notices don't show up...
            wp_redirect( admin_url( $this->page ) );
            exit();
        }

        /**
         * Clean up the uploaded file and cache.
         *
         * @since 1.2.0
         * @return void
         */
        public function import_ended() {

            global $wpsl_admin;

            wp_cache_flush();

            wp_defer_term_counting( false );
            wp_defer_comment_counting( false );

            // Remove the upload file.
            $this->clean_up();

            // Delete the store locator auto load transients.
            $wpsl_admin->delete_autoload_transient();
        }
        
        /**
         * Check if the imported CSV data contains fields that 
         * we need to include in the args for wp_insert_post or wp_update_post.
         * 
         * @since 1.0.0
         * @param array  $post_fields    List of post arguments
         * @param array  $store_location Store location data
         * @return array $post_args
         */
        public function get_post_args( $post_fields, $store_location ) {
            
            $post_args = array();

            foreach ( $post_fields as $arg => $csv_header ) {
                if ( isset( $store_location[$csv_header] ) && $store_location[$csv_header] ) {
                    $post_args[$arg] = $store_location[$csv_header];
                } else {
                    $post_args[$arg] = '';
                }
            }

            return $post_args;
        }
        
        /**
         * Add or update the post meta data for the store location
         * 
         * @since 1.0.0
         * @param integer $post_id    The id of current location
         * @param string  $meta_key   The location meta key
         * @param string  $meta_value The location meta value 
         * @param bool    $add_data   True is we need to add the data, false is we need update it.
         * @return void
         */  
        public function process_location_meta( $post_id, $meta_key, $meta_value, $add_data ) {

            if ( $add_data  ) {
                add_post_meta( $post_id, 'wpsl_' . $meta_key, $meta_value );   
            } else {
                update_post_meta( $post_id, 'wpsl_' . $meta_key, $meta_value );                
            }
        }
        
        /**
         * Check if the CSV row contains the required data.
         *
         * @since 1.0.0
         * @param array $store_location The store location data
         * @return boolean|void
         */
        public function check_required_fields( $store_location ) {

            $skip_check = apply_filters( 'wpsl_csv_skip_required_check', false );

            if ( $skip_check || !empty( $store_location['name'] ) && ( !empty( $store_location['address'] ) ) && ( !empty( $store_location['city'] ) ) || !empty( $store_location['wpsl_id'] ) ) {
                return true;
            }
        }

        /**
         * Geocode the imported CSV data.
         *
         * @since 1.0.0
         * @param integer     $post_id        The id of current location.
         * @param array       $store_location The location meta data.
         * @return array|void $geocode_error  The geocode error, or nothing if successfull.
         */
        public function geocode_imported_data( $post_id, $store_location ) {
            
            global $wpsl_admin; // From the WPSL plugin.
           
            $attempts       = 3;
            $throttle_speed = 100000;
            $loop_count     = 1;
            $delay          = 0;
            $geocode_error  = array();

            // Make a request to the Geocode API to get the latlng values,
            $geocode_response = $this->geocode->get_latlng( $store_location );

            if ( isset( $geocode_response['status'] ) ) {

                // Check the geocode response.
                switch ( $geocode_response['status'] ) {
                    case 'OK':
                        $this->process_geolocation_response( $post_id, $geocode_response, $store_location );                  
                        break;
                    case 'ZERO_RESULTS':
                        $geocode_error = array(
                            'type' => 'zero_results',
                            'msg'  => sprintf( __( '%s, %s. %sEdit details%s', 'wpsl-csv' ), esc_html( $store_location['name'] ), esc_html( $store_location['address'] ), '<a href="' . admin_url( 'post.php?post=' . esc_attr( $post_id ) . '&action=edit' ) . '">', '</a>' )
                        );

                        break;
                    case 'OVER_QUERY_LIMIT':

                        /* 
                         * If we hit the OVER_QUERY_LIMIT error, then we retry the 
                         * same address three times in a row. 
                         * 
                         * If it keeps failing we stop the import script.
                         * 
                         * See https://developers.google.com/maps/documentation/business/articles/usage_limits#limitexceeded
                         */
                        while ( $loop_count <= $attempts ) {
                            if ( $loop_count == 3 ) {
                                wp_redirect( add_query_arg( array( 'error_type' => 'geocode', 'code' => 'over_limit' ), admin_url( $this->page ) ) );
                                exit();
                            }

                            $delay += $throttle_speed;
                            $geocode_response = $this->geocode->get_latlng( $store_location );

                            // Check for a valid response, if so stop the loop.
                            if ( $geocode_response['status'] == 'OK' ) {
                                $this->process_geolocation_response( $post_id, $geocode_response, $store_location );
                                break;
                            }

                            // Pause the script for 2 seconds before making another geocode attempt.
                            sleep(2);
                            $loop_count++;
                        }

                        break;
                    default:
                        $geocode_error = array(
                            'type' => 'failed',
                            'msg'  => sprintf( __( 'Status code "%s" for location: %s, %s. %sEdit details%s %s %s', 'wpsl-csv' ), esc_html( $geocode_response['status'] ) , esc_html( $store_location['name'] ), esc_html( $store_location['address'] ), '<a href="' . admin_url( 'post.php?post=' . esc_attr( $post_id ) . '&action=edit' ) . '">', '</a>', '<br>', $this->geocode->check_geocode_error_msg( $geocode_response, false ) )
                        );

                        break;
                }

                return $geocode_error;
            } else {
                /* 
                 * This contains the error msg when wp_remote_get
                 * fails to get a response from the Geocode API, or the 
                 * Geocode API response code is set to something else
                 * other then '200'.
                 */
                $wpsl_admin->notices->save( 'error', $geocode_response );
                
                // Remove the upload file.
                $this->clean_up();

                // If we don't force a redirect, the notices don't show up...
                wp_redirect( admin_url( $this->page ) );
                exit();
            }
        }

        /**
         * Assign the returned geolocation data to the correct array key.
         * 
         * @since 1.0.0
         * @param integer $post_id          The id of current location
         * @param array   $geocode_response The returned data from the Geocode API
         * @param array   $store_location   The store location
         * @return void
         */
        public function process_geolocation_response( $post_id, $geocode_response, $store_location ) {

            // Make sure the latlng has a max of 6 decimals.
            $latlng = $this->geocode->format_latlng( $geocode_response['results'][0]['geometry']['location'] );

            $store_location['lat'] = $latlng['lat'];
            $store_location['lng'] = $latlng['lng'];
            $country               = $this->geocode->filter_country_name( $geocode_response );

            if ( version_compare( WPSL_VERSION_NUM, '2.2.10', '>=' ) ) {
                $store_location['country_iso'] = $country['short_name'];
            } else {
                $store_location['country_iso'] = $country;
            }

            // Add the geolocation meta data to the store location.
            $this->add_geolocation_meta_data( $post_id, $store_location );
        }

        /**
         * Add the latlng and country ISO to the store post meta.
         * 
         * @since 1.0.0
         * @param integer $post_id        The id of current location
         * @param array   $store_location The location meta data
         * @return void
         */
        public function add_geolocation_meta_data( $post_id, $store_location ) {

            add_post_meta( $post_id, 'wpsl_lat', $store_location['lat'], true );
            add_post_meta( $post_id, 'wpsl_lng', $store_location['lng'], true );
            add_post_meta( $post_id, 'wpsl_country_iso', $store_location['country_iso'], true );    
        }

        /**
         * Format the imported openings hours to make it compatible 
         * with the dropdown structure. 
         * 
         * This only works if the recommended hour structure 
         * is followed in the CSV file.
         *
         * @since 1.0.0
         * @param string        $opening_hours            The id of current location
         * @return string|array $formatted_openings_hours Empty or the formatted openings hours.
         */
        public function format_opening_hours( $opening_hours ) {
                        
            $formatted_openings_hours = array();
            $opening_sections         = explode( '.', $opening_hours );
            
            if ( stripos( $opening_hours, 'am' ) !== false || stripos( $opening_hours, 'pm' ) !== false ) {
                $format = '12';
            } else {
                $format = '24';
            }
    
            foreach ( $opening_sections as $opening_section ) {
                $hour_parts      = array();
                $opening_section = explode( ':', $opening_section, 2 );

                if ( count( $opening_section ) == 2 ) {
                    if ( trim( $opening_section[1] ) != 'closed' ) {
                        $preg_params = array(
                            'pattern' => array(
                               '!\s+!',
                               '/\s*-\s*/'
                            ),
                            'replacement' => array(
                                ' ',
                                ','
                            )
                        );

                        /*
                         * Replace multiple spaces with a single space, make sure there are no 
                         * spaces before and after the -, replace it with a , and remove any start / end spaces.
                         */
                        $hour_sections = preg_replace( $preg_params['pattern'], $preg_params['replacement'], trim( $opening_section[1] ) );            

                        /*
                         * Check if the hours are formated in 12 / 24hr format, and try to split them accordingly.
                         * 
                         * The 24hrs format is split at the space between the hours ( 10:00–17:00 19:00-21:00 ).
                         * The 12hrs format needs to split at the space after the hour ( 9:00 AM-5:00 PM ).
                         */
                        if ( $format == '12' ) {
                            $chunks     = array_chunk( explode( ' ', $hour_sections ), 3 );
                            $hour_parts = array_map( array( $this, 'join_hour_chunks' ), $chunks );
                        } else {
                            $hour_parts = explode( ' ', $hour_sections );    
                        }
                    }

                    $formatted_openings_hours[ trim( strtolower( $opening_section[0] ) ) ] = $hour_parts;    
                }
            }

            return $formatted_openings_hours; 
        }
        
        /**
         * Join the hour parts.
         * 
         * @since 1.0.1
         * @param array $chunks One set of hours split by three spaces.
         * @return string The hour part joined together like 9:00 AM,5:00 PM.
         */
        public function join_hour_chunks( $chunks ) {
            return implode( ' ', $chunks ); 
        }
        
        /**
         * Set a featured image for a store location,
         *
         * @since 1.0.0
         * @param integer $post_id   The id of current location
         * @param string  $image_url The Url of the featured image
         * @return void
         */
        public function set_featured_image( $post_id, $image_url ) {
            
            // Need to require these files
            if ( !function_exists( 'media_handle_upload' ) || !function_exists( 'download_url' ) ) {
                require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
                require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
            }

            $tmp = download_url( $image_url );
            
            if ( !is_wp_error( $tmp ) ) {
                $file_array = array();
                $desc       = get_the_title( $post_id );

                // Set variables for storage and fix file filename for query strings.
                preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $image_url, $matches );
                $file_array['name']     = basename( $matches[0] );
                $file_array['tmp_name'] = $tmp;

                // If error storing temporarily, unlink.
                if ( is_wp_error( $file_array['tmp_name'] ) ) {
                    @unlink( $file_array['tmp_name'] );
                    $file_array['tmp_name'] = '';
                }

                // do the validation and storage stuff.
                $attachment_id = media_handle_sideload( $file_array, $post_id, $desc );

                // If error storing permanently, unlink.
                if ( is_wp_error( $attachment_id ) ) {
                    @unlink( $file_array['tmp_name'] );
                } else {
                    set_post_thumbnail( $post_id, $attachment_id );
                }
            }
        }

        /**
         * Show the results from the import.
         * 
         * How many locations where imported, failed to import,
         * or had issues with the Geocoding API.
         * 
         * @since 1.0.0
         * @param array $import_results Holds the count of the imported locations, and possible errors.
         * @return void
         */
        public function show_import_results( $import_results ) {

            global $wpsl_admin; // From the WPSL plugin.

            $msg = '<p><strong>' . __( 'Import finished!', 'wpsl-csv' ) . '</strong></p>';

            // If we have successfully imported, updated, removed or skipped locations, then show the number of processed locations.
            if ( $import_results['imported'] || $import_results['updated'] || $import_results['removed'] || $import_results['skipped'] ) {
                $msg .= '<ul>';

                foreach ( $import_results as $action => $amount ) {
                    if ( $action !== 'failed' && $action !== 'geocode_errors' ) {
                        if ( $amount ) {
                            $dup_txt = ( $action == 'skipped' ) ? __( 'duplicate', 'wpsl-csv' ) : '';
                            $msg .= '<li>' . ucfirst( $action ) . ' ' . $amount . ' ' . sprintf( _n( ' %s location', '%s locations', $amount, 'wpsl-csv' ), $dup_txt ) . '</li>';
                        }
                    }
                }

                $msg .= '</ul>';
            }

            $wpsl_admin->notices->save( 'update', $msg, true );

            // Show how many locations failed to import.
            if ( isset( $import_results['failed'] ) ) {
                $failed_count = count( $import_results['failed'] );

                $failed_msg = '<p><strong>' . sprintf( _n( 'Failed to process %d row!', 'Failed to process %d rows!', $failed_count, 'wpsl-csv' ), $failed_count ) . '</strong></p>';
                $failed_msg .= '<ul>';

                // Loop over the loations that failed to import, and show the returned error messages
                foreach ( $import_results['failed'] as $failed_location ) {
                    if ( !empty( $failed_location['location'] ) ) {
                        $location = ' <br>'. __( 'Used address: ', 'wpsl-csv' ) . esc_html( $failed_location['location'] );
                    } else {
                        $location = '';
                    }

                    if ( in_array( $failed_location['code'], array( 'invalid_date', 'empty_content' ) ) ) {
                        $error_msg = '<a href="https://wpstorelocator.co/document/csv-manager/#error-msgs">' . esc_html( $failed_location['msg'] ) . '</a>';
                    } else {
                        $error_msg = esc_html( $failed_location['msg'] );
                    }

                    $failed_msg .= '<li>'. __( 'Returned error for row', 'wpsl-csv' ). ' ' . absint( $failed_location['row'] ) . ': ' . $error_msg . $location . '</li>';
                }

                $wpsl_admin->notices->save( 'error', $failed_msg, true );
            }

            // If we have geocode errors, then we show them.
            if ( isset( $import_results['geocode_errors'] ) ) {
                $error_fields = array(
                    'zero_results' => __( 'The geocoder returned no results for the following addresses:', 'wpsl-csv '),
                    'failed'       => __( 'Geocoding errors', 'wpsl-csv ')
                );

                foreach ( $error_fields as $field_key => $error_field ) {
                    if ( isset( $import_results['geocode_errors'][$field_key] ) ) {
                        $geocode_msg = '<p><strong>' . esc_html( $error_fields[$field_key] ). '</strong></p>';
                        $geocode_msg .= '<ul class="wpsl-geocode-errors">';

                        foreach ( $import_results['geocode_errors'][$field_key] as $geocode_error ) {
                            $geocode_msg .= '<li>' . $geocode_error . '</li>';
                        }

                        $geocode_msg .= '</ul>';   

                        if ( $field_key == 'failed' ) {
                            $geocode_msg .= '<p>' . sprintf( __( 'Read more about the different %sstatus codes%s.', 'wpsl-csv' ), '<a href="https://developers.google.com/maps/documentation/geocoding/intro#StatusCodes">', '</a>' ) . '</p>';
                        }
                    }
                }

                $wpsl_admin->notices->save( 'error', $geocode_msg, true );
            }
        }

        /**
         * Remove uploaded CSV file.
         * 
         * This is called after the CSV import is finished, 
         * and everytime the 'import' section is loaded. 
         * 
         * The last one is done to make sure no files are left 
         * behind if a user decided to upload a CSV file, 
         * but never starts the import process.
         * 
         * @since 1.0.0
         * @return void
         */
        public function clean_up() {

            if ( file_exists( WPSL_CSV_IMPORT_FILE ) ) {
                unlink( WPSL_CSV_IMPORT_FILE );
            }
        }

        /**
         * Handle the different upload / geocode errors.
         * 
         * @since 1.0.0
         * @return void
         */
        public function handle_errors() {
            
            $error_type = $_GET['error_type'];
            $error_code = $_GET['code'];
            
            if ( $error_type == 'upload' ) {
                switch( $error_code ) {
                    case 0:
                        $error = '<p>' . sprintf( __( 'Could not move the uploaded CSV file to the %s folder. Please make sure the folder is writable!', 'wpsl-csv' ), '<code>' . WPSL_CSV_IMPORT_DIR . '</code>' ) . '</p>';
                        break;
                    case 1:
                        $error = '<p>' . __( 'The uploaded file exceeds the upload_max_filesize directive in php.ini', 'wpsl-csv' ) . '</p>';
                        break;
                    case 2:
                        $error = '<p>' . __( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'wpsl-csv' ) . '</p>';
                        break;
                    case 3:
                        $error = '<p>' . __( 'The uploaded file was only partially uploaded', 'wpsl-csv' ) . '</p>';
                        break;
                    case 4:
                        $error = '<p>' . __( 'No file was uploaded', 'wpsl-csv' ) . '</p>';
                        break; 
                    case 6:
                        $error = '<p>' . __( 'Missing a temporary folder', 'wpsl-csv' ) . '</p>';
                        break;
                    case 7:
                        $error = '<p>' . __( 'Failed to write file to disk', 'wpsl-csv' ) . '</p>';
                        break;
                    case 8:
                        $error = '<p>' . __( 'File upload stopped by extension', 'wpsl-csv' ) . '</p>';
                        break;    
                }
            } else if ( $error_type == 'geocode' && $error_code == 'over_limit' ) {
                $error = '<p><strong>' . __( 'Too many OVER_QUERY_LIMIT errors.', 'wpsl-csv' ) . '</strong></p>';
                $error .= '<p>' . sprintf( __( 'It looks like you exceeded the Google Maps API %susage limits%s for today. The usage limits are reset at midnight, Pacific Time.', 'wpsl-csv' ), '<a href="https://developers.google.com/maps/documentation/geocoding/usage-limits">', '</a>' ) . '</p>';
                $error .= '<p>' . sprintf( __( 'If you repeatedly see this error, then you can raise the usage limits by setting an %sAPI key%s and %senable billing%s.', 'wpsl-csv' ), '<a href="https://developers.google.com/maps/documentation/geocoding/get-api-key#key">', '</a>', '<a href="https://developers.google.com/maps/documentation/geocoding/usage-limits#increase-your-quota-by-enabling-pay-as-you-go-billing">', '</a>' ) . '</p>';
                $error .= '<p>' . sprintf( __( 'You can also try to use a third-party %sservice%s to geocode the addresses before importing the CSV file.', 'wpsl-csv' ), '<a href="http://findlatitudeandlongitude.com/batch-geocode/">', '</a>' ) . '</p>';
            } else if ( $error_type == 'file' ) {     
                
                if ( $error_code == 'required_fields' ) {
                    $error = '<p>' . sprintf( __( 'The data for one of the %srequired fields%s ( name, address or city ) is missing in the uploaded CSV file! %s Rows that don\'t contain the required data are skipped.', 'wpsl-csv' ), '<a href="https://wpstorelocator.co/document/csv-manager/#import">', '</a>', '<br><br>' ) . '</p>';
                } else {
                    $error = '<p>' . __( 'Failed to process the uploaded CSV file. Are you sure the CSV file isn\'t empty?', 'wpsl-csv' ) . '</p>';
                }
            }
            
            if ( isset( $error ) ) {
                echo '<div class="error">' . $error . '</div>';
            }
        }

        /**
         * Bulk delete store locations
         *
         * @since 1.2.0
         * @param array $remove_locations Stores locations to remove
         * @return bool $delete_count     The number of deleted locations
         */
        public function remove_store_locations( $remove_locations ) {

            global $wpdb;

            // We do this to be able to show x locations deleted after the import.
            $delete_count = 0;

            foreach ( $remove_locations as $post_id ) {
                $response = $wpdb->delete( $wpdb->posts, array( 'ID' => $post_id ) );

                // Make sure we have actually deleted something before trying to remove the related data.
                if ( $response ) {
                    $wpdb->delete( $wpdb->postmeta, array( 'post_id'     => $post_id ) );
                    $wpdb->delete( $wpdb->posts,    array( 'post_parent' => $post_id ) );

                    $delete_count++;
                }
            }

            return $delete_count;
        }

        /**
         * Check if the provided location data ( name, address, city )
         * already exists in the db.
         *
         * @since 1.2.0
         * @param array $store_location Store location details
         * @return int  $id             The ID of the matching store location
         */
        public function is_duplicate( $store_location ) {

            global $wpdb;

            $query = "SELECT posts.ID
                        FROM $wpdb->posts AS posts
                        JOIN $wpdb->postmeta as post_address ON posts.ID = post_address.post_id AND post_address.meta_key = 'wpsl_address'
                        JOIN $wpdb->postmeta as post_city ON posts.ID = post_city.post_id AND post_city.meta_key = 'wpsl_city'
                       WHERE posts.post_status NOT IN ( 'trash' )
                         AND posts.post_title = '%s'
                         AND post_address.meta_value = '%s'
                         AND post_city.meta_value = '%s'";

            $response = $wpdb->get_results( $wpdb->prepare( $query, array(
                $store_location['name'],
                $store_location['address'],
                $store_location['city']
            ) ) );

            if ( $response ) {
                $id = $response[0]->ID;
            } else {
                $id = '';
            }

            return $id;
        }
    }
}