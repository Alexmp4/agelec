<?php
add_action( 'admin_init', 'wpsl_check_upgrade' );
add_action( 'admin_init', 'wpsl_cpt_update_state' );

/**
 * If the db doesn't hold the current version, run the upgrade procedure
 *
 * @since 1.2
 * @return void
 */
function wpsl_check_upgrade() {
    
    global $wpsl_settings;

    $current_version = get_option( 'wpsl_version' );
   
    if ( version_compare( $current_version, WPSL_VERSION_NUM, '===' ) )	
        return;

    if ( version_compare( $current_version, '1.1', '<' ) ) {
        if ( is_array( $wpsl_settings ) ) {
            if ( empty( $wpsl_settings['reset_map'] ) ) {
                $wpsl_settings['reset_map'] = 0;
            }

            if ( empty( $wpsl_settings['auto_load'] ) ) {
                $wpsl_settings['auto_load'] = 1;
            }	

            if ( empty( $wpsl_settings['new_window'] ) ) {
                $wpsl_settings['new_window'] = 0;
            }  

            update_option( 'wpsl_settings', $wpsl_settings );
        } 
    }

    if ( version_compare( $current_version, '1.2', '<' ) ) {
        if ( is_array( $wpsl_settings ) ) {
            if ( empty( $wpsl_settings['store_below'] ) ) {
                $wpsl_settings['store_below'] = 0;
            }	

            if ( empty( $wpsl_settings['direction_redirect'] ) ) {
                $wpsl_settings['direction_redirect'] = 0;
            }    

            update_option( 'wpsl_settings', $wpsl_settings );
        } 
    }

    if ( version_compare( $current_version, '1.2.11', '<' ) ) {
        if ( is_array( $wpsl_settings ) ) {
            if ( empty( $wpsl_settings['more_info'] ) ) {
                $wpsl_settings['more_info'] = 0;
            }

            if ( empty( $wpsl_settings['more_label'] ) ) {
                $wpsl_settings['more_label'] = __( 'More info', 'wpsl' );
            }

            if ( empty( $wpsl_settings['mouse_focus'] ) ) {
                $wpsl_settings['mouse_focus'] = 1;
            }	

            update_option( 'wpsl_settings', $wpsl_settings );
        } 
    }

    if ( version_compare( $current_version, '1.2.12', '<' ) ) {
        if ( is_array( $wpsl_settings ) ) {
            if ( empty( $wpsl_settings['more_info_location'] ) ) {
                $wpsl_settings['more_info_location'] = __( 'info window', 'wpsl' ); 
            }

            if ( empty( $wpsl_settings['back_label'] ) ) {
                $wpsl_settings['back_label'] = __( 'Back', 'wpsl' );
            }

            if ( empty( $wpsl_settings['reset_label'] ) ) {
                $wpsl_settings['reset_label'] = __( 'Reset', 'wpsl' );
            }                  

            if ( empty( $wpsl_settings['store_below_scroll'] ) ) {
                $wpsl_settings['store_below_scroll'] = 0;
            }  

            update_option( 'wpsl_settings', $wpsl_settings );
        } 
    }   

    if ( version_compare( $current_version, '1.2.20', '<' ) ) {

        global $wpdb;
        
        $wpsl_table = $wpdb->prefix . 'wpsl_stores';

        // Rename the street field to address.
        $wpdb->query( "ALTER TABLE $wpsl_table CHANGE street address VARCHAR(255)" );

        // Add the second address field.
        $wpdb->query( "ALTER TABLE $wpsl_table ADD address2 VARCHAR(255) NULL AFTER address" );

        if ( is_array( $wpsl_settings ) ) {
            if ( empty( $wpsl_settings['store_url'] ) ) {
                $wpsl_settings['store_url'] = 0;
            }

            if ( empty( $wpsl_settings['phone_url'] ) ) {
                $wpsl_settings['phone_url'] = 0;
            }

            if ( empty( $wpsl_settings['marker_clusters'] ) ) {
                $wpsl_settings['marker_clusters'] = 0;
            }

            if ( empty( $wpsl_settings['cluster_zoom'] ) ) {
                $wpsl_settings['cluster_zoom'] = 0;
            }

            if ( empty( $wpsl_settings['cluster_size'] ) ) {
                $wpsl_settings['cluster_size'] = 0;
            }

            if ( empty( $wpsl_settings['template_id'] ) ) {
                $wpsl_settings['template_id'] = ( $wpsl_settings['store_below'] ) ? 1 : 0;
                unset( $wpsl_settings['store_below'] );
            }

            if ( empty( $wpsl_settings['marker_streetview'] ) ) {
                $wpsl_settings['marker_streetview'] = 0;
            }

            if ( empty( $wpsl_settings['marker_zoom_to'] ) ) {
                $wpsl_settings['marker_zoom_to'] = 0;
            }

            if ( !isset( $wpsl_settings['editor_country'] ) ) {
                $wpsl_settings['editor_country'] = '';
            }

            if ( empty( $wpsl_settings['street_view_label'] ) ) {
                $wpsl_settings['street_view_label'] = __( 'Street view', 'wpsl' );
            }

            if ( empty( $wpsl_settings['zoom_here_label'] ) ) {
                $wpsl_settings['zoom_here_label'] = __( 'Zoom here', 'wpsl' );
            }

            if ( empty( $wpsl_settings['no_directions_label'] ) ) {
                $wpsl_settings['no_directions_label'] = __( 'No route could be found between the origin and destination', 'wpsl' );
            }

            update_option( 'wpsl_settings', $wpsl_settings );
        }
    }

    if ( version_compare( $current_version, '2.0', '<' ) ) {
        
        global $wpdb;
        
        $wpsl_table = $wpdb->prefix . 'wpsl_stores';
        
        if ( is_array( $wpsl_settings ) ) {
            if ( empty( $wpsl_settings['radius_dropdown'] ) ) {
                $wpsl_settings['radius_dropdown'] = 1;
            }
            
            if ( empty( $wpsl_settings['permalinks'] ) ) {
                $wpsl_settings['permalinks'] = 0;
            }

            if ( empty( $wpsl_settings['permalink_slug'] ) ) {
                $wpsl_settings['permalink_slug'] = __( 'stores', 'wpsl' );
            }
            
            if ( empty( $wpsl_settings['category_slug'] ) ) {
                $wpsl_settings['category_slug'] = __( 'store-category', 'wpsl' );
            }
           
            if ( empty( $wpsl_settings['editor_hours'] ) ) {
                $wpsl_settings['editor_hours'] = wpsl_default_opening_hours();
            }
            
            if ( empty( $wpsl_settings['editor_hour_format'] ) ) {
                $wpsl_settings['editor_hour_format'] = 12;
            }
            
            if ( empty( $wpsl_settings['editor_map_type'] ) ) {
                $wpsl_settings['editor_map_type'] = 'roadmap';
            }
            
            if ( empty( $wpsl_settings['infowindow_style'] ) ) {
                $wpsl_settings['infowindow_style'] = 'default';
            }
            
            if ( empty( $wpsl_settings['email_label'] ) ) {
                $wpsl_settings['email_label'] = __( 'Email', 'wpsl' );
            }
            
            if ( empty( $wpsl_settings['url_label'] ) ) {
                $wpsl_settings['url_label'] = __( 'Url', 'wpsl' );
            }
            
            if ( empty( $wpsl_settings['category_label'] ) ) {
                $wpsl_settings['category_label'] = __( 'Category filter', 'wpsl' );
            }
            
            if ( empty( $wpsl_settings['show_credits'] ) ) {
                $wpsl_settings['show_credits'] = 0;
            }
            
            if ( empty( $wpsl_settings['autoload_limit'] ) ) {
                $wpsl_settings['autoload_limit'] = 50;
            }
            
            if ( empty( $wpsl_settings['scrollwheel'] ) ) {
                $wpsl_settings['scrollwheel'] = 1;
            }
            
            if ( empty( $wpsl_settings['type_control'] ) ) {
                $wpsl_settings['type_control'] = 0;
            }

            if ( empty( $wpsl_settings['hide_hours'] ) ) {
                $wpsl_settings['hide_hours'] = 0;
            }
            
            // Either correct the existing map style format from the 2.0 beta or set it to empty.
            if ( isset( $wpsl_settings['map_style'] ) && is_array( $wpsl_settings['map_style'] ) && isset( $wpsl_settings['map_style']['id'] ) ) {
                switch( $wpsl_settings['map_style']['id'] ) {
                    case 'custom':
                        $map_style = $wpsl_settings['map_style']['custom_json'];
                        break;
                    case 'default':
                        $map_style = '';
                        break;
                    default:
                        $map_style = $wpsl_settings['map_style']['theme_json'];
                        break;
                }

                $wpsl_settings['map_style'] = $map_style;
            } else {
                $wpsl_settings['map_style'] = '';
            }
                        
            if ( empty( $wpsl_settings['autoload'] ) ) {
                $wpsl_settings['autoload'] = $wpsl_settings['auto_load'];
                unset( $wpsl_settings['auto_load'] );
            }
            
            if ( empty( $wpsl_settings['address_format'] ) ) {
                $wpsl_settings['address_format'] = 'city_state_zip';
            }
            
            if ( empty( $wpsl_settings['auto_zoom_level'] ) ) {
                $wpsl_settings['auto_zoom_level'] = 15;
            }
            
            if ( empty( $wpsl_settings['hide_distance'] ) ) {
                $wpsl_settings['hide_distance'] = 0;
            }
            
            if ( empty( $wpsl_settings['debug'] ) ) {
                $wpsl_settings['debug'] = 0;
            }
            
            if ( empty( $wpsl_settings['category_dropdown'] ) ) {
                $wpsl_settings['category_dropdown'] = 0;
            }
           
            /* 
             * Replace marker_bounce with marker_effect to better reflect what the option contains.
             * 
             * If a user hovers over the result list then either the corresponding marker will bounce,
             * the info window will open, or nothing will happen. 
             * 
             * The default behaviour is that the marker will bounce.
             */            
            if ( empty( $wpsl_settings['marker_effect'] ) ) {
                $wpsl_settings['marker_effect'] = ( $wpsl_settings['marker_bounce'] ) ? 'bounce' : 'ignore';
                unset( $wpsl_settings['marker_bounce'] );
            }
                        
            /* 
             * The default input for the opening hours is set to textarea for current users, 
             * for new users it will be set to dropdown ( easier to format in a table output and to use with schema.org in the future ).  
             */
            if ( empty( $wpsl_settings['editor_hour_input'] ) ) {
                $wpsl_settings['editor_hour_input'] = 'textarea';
            }
            
            // Rename store_below_scroll to listing_below_no_scroll, it better reflects what it does.
            if ( empty( $wpsl_settings['listing_below_no_scroll'] ) && isset( $wpsl_settings['store_below_scroll'] ) ) {
                $wpsl_settings['listing_below_no_scroll'] = $wpsl_settings['store_below_scroll'];
                unset( $wpsl_settings['store_below_scroll'] );
            }
            
            // Change the template ids from number based to name based.
            if ( is_numeric( $wpsl_settings['template_id'] ) ) {
                $wpsl_settings['template_id'] = ( !$wpsl_settings['template_id'] ) ? 'default' : 'below_map';
            }

            $replace_data = array(
                'max_results'   => $wpsl_settings['max_results'],
                'search_radius' => $wpsl_settings['search_radius']
            );

            /* 
             * Replace the () with [], this fixes an issue with the mod_security module that is installed on some servers. 
             * It triggerd a 'Possible SQL injection attack' warning probably because of the int,(int) format of the data.
             */
            foreach ( $replace_data as $index => $option_value ) {
                $wpsl_settings[$index] = str_replace( array( '(', ')' ), array( '[', ']' ), $option_value );
            }
            
            // The reset button now uses an icon instead of text, so no need for the label anymore.
            unset( $wpsl_settings['reset_label'] );

            update_option( 'wpsl_settings', $wpsl_settings ); 
            
            /* 
             * Users upgrading from 1.x will be given the choice between the textarea or 
             * dropdowns for the opening hours. 
             * 
             * New users don't get that choice, they will only get the dropdowns. 
             * 
             * The wpsl_legacy_support option is used to determine if we need to show both options.
             */
            update_option( 'wpsl_legacy_support', 1 ); 
                           
            // Add the WPSL roles and caps.
            wpsl_add_roles();
            wpsl_add_caps();

            // If there is a wpsl_stores table, then we need to convert all the locations to the 'wpsl_stores' custom post type.
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpsl_table'" ) && version_compare( $current_version, '1.9', '<' ) ) { 
                if ( wpsl_remaining_cpt_count() ) {
                    update_option( 'wpsl_convert_cpt', 'in_progress' );
                }
            }
        }
    }
    
    /*
     * Both map options are no longer supported in 3.22 of the Google Maps API.
     * See: https://developers.google.com/maps/articles/v322-controls-diff
     */
    if ( version_compare( $current_version, '2.0.3', '<' ) ) {
        unset( $wpsl_settings['control_style'] );
        unset( $wpsl_settings['pan_controls'] );

        update_option( 'wpsl_settings', $wpsl_settings ); 
    }

    if ( version_compare( $current_version, '2.1.0', '<' ) ) {
        if ( !isset( $wpsl_settings['api_geocode_component'] ) ) {
            $wpsl_settings['api_geocode_component'] = 0;
        }

        update_option( 'wpsl_settings', $wpsl_settings ); 
    }
    
    if ( version_compare( $current_version, '2.2', '<' ) ) {
        $wpsl_settings['autocomplete'] = 0;
        $wpsl_settings['category_default_label'] = __( 'Any', 'wpsl' );

        // Rename the 'zoom_name' and 'zoom_latlng' to 'start_name' and 'start_latlng'.
        if ( isset( $wpsl_settings['zoom_name'] ) ) {
            $wpsl_settings['start_name'] = $wpsl_settings['zoom_name'];
            unset( $wpsl_settings['zoom_name'] );
        }

        if ( isset( $wpsl_settings['zoom_latlng'] ) ) {
            $wpsl_settings['start_latlng'] = $wpsl_settings['zoom_latlng'];
            unset( $wpsl_settings['zoom_latlng'] );
        }

        if ( isset( $wpsl_settings['category_dropdown'] ) ) {
            $wpsl_settings['category_filter'] = $wpsl_settings['category_dropdown'];
            unset( $wpsl_settings['category_dropdown'] );
        }
        
        // We now have separate browser and server key fields, and assume the existing key is a server key.
        if ( isset( $wpsl_settings['api_key'] ) ) {
            $wpsl_settings['api_server_key'] = $wpsl_settings['api_key'];
            unset( $wpsl_settings['api_key'] );
        }
        
        $wpsl_settings['api_browser_key']      = '';
        $wpsl_settings['category_filter_type'] = 'dropdown';
        $wpsl_settings['hide_country']         = 0;
        $wpsl_settings['show_contact_details'] = 0;
        
        update_option( 'wpsl_settings', $wpsl_settings ); 
    }
    
    if ( version_compare( $current_version, '2.2.4', '<' ) ) {
        $wpsl_settings['deregister_gmaps'] = 0;
        
        update_option( 'wpsl_settings', $wpsl_settings ); 
    }

    if ( version_compare( $current_version, '2.2.9', '<' ) ) {
        $wpsl_settings['run_fitbounds'] = 1;

        update_option( 'wpsl_settings', $wpsl_settings );
    }

    if ( version_compare( $current_version, '2.2.13', '<' ) ) {
        $wpsl_settings['clickable_contact_details'] = 0;

        update_option( 'wpsl_settings', $wpsl_settings );
    }

    update_option( 'wpsl_version', WPSL_VERSION_NUM );
}

/**
 * Check if we need to show the notice that tells users that the store locations
 * need to be converted to custom post types before the update from 1.x to 2.x is complete.
 * 
 * @since 2.0
 * @return void
 */
function wpsl_cpt_update_state() {

    global $wpsl_admin;
    
    $conversion_state = get_option( 'wpsl_convert_cpt' );
    
    if ( $conversion_state == 'in_progress' ) {
        if ( ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) ) {
            $remaining = wpsl_remaining_cpt_count();
            $wpsl_admin->notices->save( 'error', sprintf( __( 'Because you updated WP Store Locator from version 1.x, the %s current store locations need to be %sconverted%s to custom post types.', 'wpsl' ), "<span class='wpsl-cpt-remaining'>" . $remaining . "</span>", "<a href='#' id='wpsl-cpt-dialog'>", "</a>" ) );        
        
            add_action( 'admin_footer',  'wpsl_cpt_dialog_html' );
        }

        add_action( 'admin_enqueue_scripts',     'wpsl_convert_cpt_js' );	
        add_action( 'wp_ajax_convert_cpt',       'wpsl_convert_cpt' );
        add_action( 'wp_ajax_convert_cpt_count', 'wpsl_convert_cpt_count' );
    }
}

/**
 * Include the js file that handles the ajax request to 
 * start converting the 1.x store locations to custom post types.
 * 
 * @since 2.0
 * @return void
 */
function wpsl_convert_cpt_js() {

    $cpt_js_l10n = array(
        'timeout'      => sprintf( __( 'The script converting the locations timed out. %s You can click the "Start Converting" button again to restart the script. %s If there are thousands of store locations left to convert and you keep seeing this message, then you can try to contact your host and ask if they can increase the maximum execution time. %s The plugin tried to disable the maximum execution time, but if you are reading this then that failed.', 'wpsl' ), '<br><br>', '<br><br>', '<br><br>' ),
        'securityFail' => __( 'Security check failed, reload the page and try again.', 'wpsl' )
    );

    wp_enqueue_script( 'jquery-ui-dialog' );
    wp_enqueue_script( 'wpsl-queue', plugins_url( '/js/ajax-queue.js', __FILE__ ), array( 'jquery' ), false ); 
    wp_enqueue_script( 'wpsl-cpt-js', plugins_url( '/js/wpsl-cpt-upgrade.js', __FILE__ ), array( 'jquery' ), false );
    wp_localize_script( 'wpsl-cpt-js', 'wpslCptConversion', $cpt_js_l10n );
}

/**
 * The html for the lightbox
 * 
 * @since 2.0
 * @return void
 */
function wpsl_cpt_dialog_html() {

    ?>
    <div id="wpsl-cpt-lightbox" style="display:none;">
        <span class="tb-close-icon"></span>
        <p class="wpsl-cpt-remaining"><?php _e( 'Store locations to convert:', 'wpsl' ); echo '<span></span>'; ?></p>
        <div class="wslp-cpt-fix-wrap">
            <input id="wpsl-start-cpt-conversion" class="button-primary" type="submit" value="<?php _e( 'Start Converting', 'wpsl' ); ?>" >
            <img class="wpsl-preloader" alt="preloader" src="<?php echo WPSL_URL . 'img/ajax-loader.gif'; ?>" />
        </div>
        <input type="hidden" name="wpsl-cpt-fix-nonce" value="<?php echo wp_create_nonce( 'wpsl-cpt-fix' ); ?>" />
        <input type="hidden" name="wpsl-cpt-conversion-count" value="<?php echo wp_create_nonce( 'wpsl-cpt-count' ); ?>" />
    </div>
    <div id="wpsl-cpt-overlay" style="display:none;"></div>
    <style>
        .wslp-cpt-fix-wrap {
            float:left;
            clear:both;
            width:100%;
            margin:0 0 15px 0;
        }

        #wpsl-cpt-lightbox .wpsl-cpt-remaining span {
            margin-left:5px;
        }

        #wpsl-start-cpt-conversion {
            float:left;
        }

        .wslp-cpt-fix-wrap .wpsl-preloader,
        .wslp-cpt-fix-wrap span {
            float:left;
            margin:8px 0 0 10px;    
        }

        .wslp-cpt-fix-wrap .wpsl-preloader {
            display: none;
        }
        
        #wpsl-cpt-lightbox {
            position:fixed;
            width:450px;
            left:50%;
            right:50%;
            top:3.8em;
            padding:15px;
            background:none repeat scroll 0 0 #fff;
            border-radius:3px;
            margin-left:-225px;
            z-index: 9999;
        }
        
        #wpsl-cpt-overlay {
            position:fixed;
            right:0;
            top:0;
            z-index:9998;
            background:none repeat scroll 0 0 #000;
            bottom:0;
            left:0;
            opacity:0.5;
        }
        
        .tb-close-icon {
            color: #666;
            text-align: center;
            line-height: 29px;
            width: 29px;
            height: 29px;
            position: absolute;
            top: 0;
            right: 0;
        }

        .tb-close-icon:before {
            content: '\f158';
            font: normal 20px/29px 'dashicons';
            speak: none;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .tb-close-icon:hover {
            color: #999 !important;
            cursor: pointer;
        }
    </style>
    <?php    
}

/**
 * Handle the ajax call to start converting the
 * store locations to custom post types.
 * 
 * @since 2.0
 * @return void|string json on completion
 */
function wpsl_convert_cpt() {
    
    if ( !current_user_can( 'manage_options' ) )
        die( '-1' );
    check_ajax_referer( 'wpsl-cpt-fix' );

    // Start the cpt coversion.
    wpsl_cpt_conversion();

    exit();
}

/**
 * Get the amount of locations that still need to be converted.
 * 
 * @since 2.0
 * @return string json The amount of locations that still need to be converted
 */
function wpsl_convert_cpt_count() {
   
    if ( !current_user_can( 'manage_options' ) )
        die( '-1' );
    check_ajax_referer( 'wpsl-cpt-count' );
    
    $remaining_count = wpsl_remaining_cpt_count();
        
    $response['success'] = true;
    
    if ( $remaining_count ) {
        $response['count'] = $remaining_count;
    } else {
        $response['url'] = sprintf( __( 'All the store locations are now converted to custom post types. %s You can view them on the %sAll Stores%s page.', 'wpsl' ), '<br><br>', '<a href="' . admin_url( 'edit.php?post_type=wpsl_stores' ) . '">', '</a>' );
        
        delete_option( 'wpsl_convert_cpt' );
    }
    
    wp_send_json( $response );
    
    exit();
}

/**
 * Return the difference between the number of existing wpsl custom post types, 
 * and the number of records in the old wpsl_stores database.
 * 
 * @since 2.0
 * @return int|boolean $remaining The amount of locations that still need to be converted
 */
function wpsl_remaining_cpt_count() {
    
    global $wpdb;
    
    $table = $wpdb->prefix . 'wpsl_stores';
    $count = wp_count_posts( 'wpsl_stores' );
    
    if ( isset( $count->publish ) && isset( $count->draft ) ) {
        $cpt_count = $count->publish + $count->draft;
    } else {
        $cpt_count = 0;
    }
    
    $db_count   = $wpdb->get_var( "SELECT COUNT(wpsl_id) FROM $table" );
    $difference = $db_count - $cpt_count;
    
    /* 
     * This prevents users who used the 2.0 beta, and later added 
     * more stores from seeing the upgrade notice again.
     */
    $remaining = ( $difference < 0 ) ? false : $difference;
                    
    return $remaining;
}

/**
 * Convert the existing locations to custom post types.
 * 
 * @since 2.0
 * @return void|boolean True if the conversion is completed
 */
function wpsl_cpt_conversion() {
    
    global $wpdb;
    
    // Try to disable the time limit to prevent timeouts.
    @set_time_limit( 0 );

    $meta_keys  = array( 'address', 'address2', 'city', 'state', 'zip', 'country', 'country_iso', 'lat', 'lng', 'phone', 'fax', 'url', 'email', 'hours' );
    $offset     = wpsl_remaining_cpt_count();
    $wpsl_table = $wpdb->prefix . 'wpsl_stores';
    $stores     = $wpdb->get_results( "(SELECT * FROM $wpsl_table ORDER BY wpsl_id DESC LIMIT $offset) ORDER BY wpsl_id ASC" );
    
    foreach ( $stores as $store ) {
        
        // Make sure we set the correct post status.
        if ( $store->active ) {
            $post_status = 'publish';
        } else {
            $post_status = 'draft';
        }
        
        $post = array (
            'post_type'    => 'wpsl_stores',
            'post_status'  => $post_status,
            'post_title'   => $store->store,
            'post_content' => $store->description              
        );

        $post_id = wp_insert_post( $post );

        if ( $post_id ) {
            
            // Save the data from the wpsl_stores db table as post meta data.
            foreach ( $meta_keys as $meta_key ) {
                if ( isset( $store->{$meta_key} ) && !empty( $store->{$meta_key} ) ) {
                    update_post_meta( $post_id, 'wpsl_' . $meta_key, $store->{$meta_key} );
                }
            }
            
            // If we have a thumb ID set the post thumbnail for the inserted post.
            if ( $store->thumb_id ) {
                set_post_thumbnail( $post_id, $store->thumb_id );
            }
        }
    }
}