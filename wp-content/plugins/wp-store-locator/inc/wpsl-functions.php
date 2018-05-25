<?php

/**
 * Collect all the parameters ( language, key, region )
 * we need before making a request to the Google Maps API.
 *
 * @since 1.0.0
 * @param  string  $api_key_type   The type of API key we need to include ( server_key or browser_key ).
 * @param  boolean $geocode_params
 * @return string  $api_params     The API parameters.
 */
function wpsl_get_gmap_api_params( $api_key_type, $geocode_params = false ) {

    global $wpsl, $wpsl_settings;

    $api_params = '';
    $param_keys = array( 'language', 'region', 'key' );
    
    /*
     * The geocode params are included after the address so we need to 
     * use a '&' as the first char, but when the maps script is included on 
     * the front-end it does need to start with a '?'.
     */
    $first_sep = ( $geocode_params ) ? '&' : '?';

    foreach ( $param_keys as $param_key ) {
        $option_key = ( $param_key == 'key' ) ? $api_key_type : $param_key;
        
        /*
         * Get the current language code if WPML or qTranslate-X is active. 
         * Otherwise get the param value from the settings var.
         */
        if ( $option_key == 'language' && ( $wpsl->i18n->wpml_exists() || $wpsl->i18n->qtrans_exists() ) ) {
            $param_val = $wpsl->i18n->check_multilingual_code();
        } else {
            $param_val = $wpsl_settings['api_' . $option_key];
        }
        
        if ( !empty( $param_val ) ) {
            $api_params .= $param_key . '=' . $param_val . '&';
        }
    }

    if ( $api_params ) {
        $api_params = $first_sep . rtrim( $api_params, '&' );
    }
    
    // Do we need to include the autocomplete library?
    if ( ( $wpsl_settings['autocomplete'] && $api_key_type == 'browser_key' ) || is_admin() ) {
        $api_params .= '&libraries=places';
    }

    return apply_filters( 'wpsl_gmap_api_params', $api_params );
}

/**
 * Get the default plugin settings.
 *
 * @since 1.0.0
 * @return array $default_settings The default settings
 */
function wpsl_get_default_settings() {

    $default_settings = array(
        'api_browser_key'           => '',
        'api_server_key'            => '',
        'api_language'              => 'en',
        'api_region'                => '',
        'api_geocode_component'     => 0,
        'distance_unit'             => 'km',
        'max_results'               => '[25],50,75,100',
        'search_radius'             => '10,25,[50],100,200,500',
        'marker_effect'             => 'bounce',
        'address_format'            => 'city_state_zip',
        'hide_distance'             => 0,
        'hide_country'              => 0,
        'show_contact_details'      => 0,
        'clickable_contact_details' => 0,
        'auto_locate'               => 1,
        'autocomplete'              => 0,
        'autoload'                  => 1,
        'autoload_limit'            => 50,
        'run_fitbounds'             => 1,
        'zoom_level'                => 3,
        'auto_zoom_level'           => 15,
        'start_name'                => '',
        'start_latlng'              => '',
        'height'                    => 350,
        'map_type'                  => 'roadmap',
        'map_style'                 => '',
        'type_control'              => 0,
        'streetview'                => 0,
        'results_dropdown'          => 1,
        'radius_dropdown'           => 1,
        'category_filter'           => 0,
        'category_filter_type'      => 'dropdown',
        'infowindow_width'          => 225,
        'search_width'              => 179,
        'label_width'               => 95,
        'control_position'          => 'left',
        'scrollwheel'               => 1,
        'marker_clusters'           => 0,
        'cluster_zoom'              => 0,
        'cluster_size'              => 0,
        'new_window'                => 0,
        'reset_map'                 => 0,
        'template_id'               => 'default',
        'listing_below_no_scroll'   => 0,
        'direction_redirect'        => 0,
        'more_info'                 => 0,
        'store_url'                 => 0,
        'phone_url'                 => 0,
        'marker_streetview'         => 0,
        'marker_zoom_to'            => 0,
        'more_info_location'        => 'info window',
        'mouse_focus'               => 1,
        'start_marker'              => 'red.png',
        'store_marker'              => 'blue.png',
        'editor_country'            => '',
        'editor_hours'              => wpsl_default_opening_hours(),
        'editor_hour_input'         => 'dropdown',
        'editor_hour_format'        => 12,
        'editor_map_type'           => 'roadmap',
        'hide_hours'                => 0,
        'permalinks'                => 0,
        'permalink_slug'            => __( 'stores', 'wpsl' ),
        'category_slug'             => __( 'store-category', 'wpsl' ),
        'infowindow_style'          => 'default',
        'show_credits'              => 0,
        'debug'                     => 0,
        'deregister_gmaps'          => 0,
        'start_label'               => __( 'Start location', 'wpsl' ),
        'search_label'              => __( 'Your location', 'wpsl' ),
        'search_btn_label'          => __( 'Search', 'wpsl' ),
        'preloader_label'           => __( 'Searching...', 'wpsl' ),
        'radius_label'              => __( 'Search radius', 'wpsl' ),
        'no_results_label'          => __( 'No results found', 'wpsl' ),
        'results_label'             => __( 'Results', 'wpsl' ),
        'more_label'                => __( 'More info', 'wpsl' ),
        'directions_label'          => __( 'Directions', 'wpsl' ),
        'no_directions_label'       => __( 'No route could be found between the origin and destination', 'wpsl' ),
        'back_label'                => __( 'Back', 'wpsl' ),
        'street_view_label'         => __( 'Street view', 'wpsl' ),
        'zoom_here_label'           => __( 'Zoom here', 'wpsl' ),
        'error_label'               => __( 'Something went wrong, please try again!', 'wpsl' ),
        'limit_label'               => __( 'API usage limit reached', 'wpsl' ),
        'phone_label'               => __( 'Phone', 'wpsl' ),
        'fax_label'                 => __( 'Fax', 'wpsl' ),
        'email_label'               => __( 'Email', 'wpsl' ),
        'url_label'                 => __( 'Url', 'wpsl' ),
        'hours_label'               => __( 'Hours', 'wpsl' ),
        'category_label'            => __( 'Category filter', 'wpsl' ),
        'category_default_label'    => __( 'Any', 'wpsl' )
    ); 

    return $default_settings;
}

/**
 * Get the current plugin settings.
 * 
 * @since 1.0.0
 * @return array $setting The current plugin settings
 */
function wpsl_get_settings() {

    $settings = get_option( 'wpsl_settings' );            

    if ( !$settings ) {
        update_option( 'wpsl_settings', wpsl_get_default_settings() );
        $settings = wpsl_get_default_settings();
    }

    return $settings;
} 

/**
 * Get a single value from the default settings.
 * 
 * @since 1.0.0
 * @param  string $setting               The value that should be restored
 * @return string $wpsl_default_settings The default setting value
 */
function wpsl_get_default_setting( $setting ) {

    global $wpsl_default_settings;

    return $wpsl_default_settings[$setting];
}

/**
 * Set the default plugin settings.
 * 
 * @since 1.0.0
 * @return void
 */
function wpsl_set_default_settings() {

    $settings = get_option( 'wpsl_settings' );

    if ( !$settings ) {
        update_option( 'wpsl_settings', wpsl_get_default_settings() );
    }
}

/**
 * Return a list of the store templates.
 * 
 * @since 1.2.20
 * @return array $templates The list of default store templates
 */
function wpsl_get_templates() {

    $templates = array(
        array(
            'id'   => 'default',
            'name' => __( 'Default', 'wpsl' ), 
            'path' => WPSL_PLUGIN_DIR . 'frontend/templates/default.php'
        ), 
        array(
            'id'   => 'below_map',
            'name' => __( 'Show the store list below the map', 'wpsl' ), 
            'path' => WPSL_PLUGIN_DIR . 'frontend/templates/store-listings-below.php'
        )
    );

    return apply_filters( 'wpsl_templates', $templates );
}

/**
 * Return the days of the week.
 *
 * @since 2.0.0
 * @return array $weekdays The days of the week
 */
function wpsl_get_weekdays() {

   $weekdays = array( 
       'monday'    => __( 'Monday', 'wpsl' ), 
       'tuesday'   => __( 'Tuesday', 'wpsl' ),  
       'wednesday' => __( 'Wednesday', 'wpsl' ),  
       'thursday'  => __( 'Thursday', 'wpsl' ),  
       'friday'    => __( 'Friday', 'wpsl' ),  
       'saturday'  => __( 'Saturday', 'wpsl' ),
       'sunday'    => __( 'Sunday' , 'wpsl' )
   );

   return $weekdays;
}

/** 
 * Get the default opening hours.
 *
 * @since 2.0.0
 * @return array $opening_hours The default opening hours
 */
function wpsl_default_opening_hours() {

   $current_version = get_option( 'wpsl_version' );
     
   $opening_hours = array(
       'dropdown' => array(
           'monday'    => array( '9:00 AM,5:00 PM' ),
           'tuesday'   => array( '9:00 AM,5:00 PM' ),
           'wednesday' => array( '9:00 AM,5:00 PM' ),
           'thursday'  => array( '9:00 AM,5:00 PM' ),
           'friday'    => array( '9:00 AM,5:00 PM' ),
           'saturday'  => '',
           'sunday'    => ''
        )
    );
   
   /* Only add the textarea defaults for users that upgraded from 1.x */
   if ( version_compare( $current_version, '2.0', '<' ) ) {
       $opening_hours['textarea'] = sprintf( __( 'Mon %sTue %sWed %sThu %sFri %sSat Closed %sSun Closed', 'wpsl' ), '9:00 AM - 5:00 PM' . "\n", '9:00 AM - 5:00 PM' . "\n", '9:00 AM - 5:00 PM' . "\n", '9:00 AM - 5:00 PM' . "\n", '9:00 AM - 5:00 PM' . "\n", "\n" ); //cleaner way without repeating it 5 times??
   }

   return $opening_hours;
}

/**
 * Get the available map types.
 * 
 * @since 2.0.0
 * @return array $map_types The available map types 
 */
function wpsl_get_map_types() {
    
    $map_types = array(
        'roadmap'   => __( 'Roadmap', 'wpsl' ), 
        'satellite' => __( 'Satellite', 'wpsl' ),  
        'hybrid'    => __( 'Hybrid', 'wpsl' ),  
        'terrain'   => __( 'Terrain', 'wpsl' )
    );
    
    return $map_types;
}

/**
 * Get the address formats.
 * 
 * @since 2.0.0
 * @return array $address_formats The address formats
 */
function wpsl_get_address_formats() {
    
    $address_formats = array(
        'city_state_zip'       => __( '(city) (state) (zip code)', 'wpsl' ),
        'city_comma_state_zip' => __( '(city), (state) (zip code)', 'wpsl' ),
        'city_zip'             => __( '(city) (zip code)', 'wpsl' ),
        'city_comma_zip'       => __( '(city), (zip code)', 'wpsl' ),
        'zip_city_state'       => __( '(zip code) (city) (state)', 'wpsl' ),
        'zip_city'             => __( '(zip code) (city)', 'wpsl' )
    );
   
    return apply_filters( 'wpsl_address_formats', $address_formats );
}

/**
 * Make sure the provided map type is valid.
 * 
 * If the map type is invalid the default is used ( roadmap ).
 * 
 * @since 2.0.0
 * @param  string $map_type The provided map type
 * @return string $map_type A valid map type
 */
function wpsl_valid_map_type( $map_type ) {
    
    $allowed_map_types = wpsl_get_map_types();
    
    if ( !array_key_exists( $map_type, $allowed_map_types ) ) {
        $map_type = wpsl_get_default_setting( 'map_type' );
    }
    
    return $map_type;
}

/**
 * Make sure the provided zoom level is valid.
 * 
 * If the zoom level is invalid the default is used ( 3 ).
 * 
 * @since 2.0.0
 * @param  string $zoom_level The provided zoom level 
 * @return string $zoom_level A valid zoom level
 */
function wpsl_valid_zoom_level( $zoom_level ) {
    
    $zoom_level = absint( $zoom_level );
    
    if ( ( $zoom_level < 1 ) || ( $zoom_level > 21 ) ) {
        $zoom_level = wpsl_get_default_setting( 'zoom_level' );	
    }

    return $zoom_level;
}

/**
 * Get the max auto zoom levels for the map.
 * 
 * @since 2.0.0
 * @return array $max_zoom_levels The array holding the min - max zoom levels
 */
function wpsl_get_max_zoom_levels() {
    
    $max_zoom_levels = array();
    $zoom_level = array(
        'min' => 10,
        'max' => 21
    );
    
    $i = $zoom_level['min'];

    while ( $i <= $zoom_level['max'] ) {
        $max_zoom_levels[$i] = $i;
        $i++;
    } 
    
    return $max_zoom_levels;
}

/**
 * The labels and the values that can be set through the settings page.
 * 
 * @since 2.0.0
 * @return array $labels The label names from the settings page.
 */
function wpsl_labels() {

    $labels = array( 
        'search',
        'search_btn',
        'preloader',
        'radius',
        'no_results',
        'results',
        'more',
        'directions',
        'no_directions',
        'back',
        'street_view',
        'zoom_here',
        'error',
        'phone',
        'fax',
        'email',
        'url',
        'hours',
        'start',
        'limit',
        'category',
        'category_default'
    );

    return $labels;
}

/**
 * Callback for array_walk_recursive, sanitize items in a multidimensional array.
 *
 * @since 2.0.0
 * @param string  $item The value
 * @param integer $key  The key
 */
function wpsl_sanitize_multi_array( &$item, $key ) {
    $item = sanitize_text_field( $item );
}
        
/**
 * Check whether the array is multidimensional.
 *
 * @since 2.0.0
 * @param  array    $array The array to check
 * @return boolean
 */
function wpsl_is_multi_array( $array ) {

    foreach ( $array as $value ) {
        if ( is_array( $value ) ) return true;
    }

    return false;
}

/**
 * @since 2.1.1
 * @param string $address  The address to geocode.
 * @return array $response Either a WP_Error or the response from the Geocode API.
 */
function wpsl_call_geocode_api( $address ) {

    $url      = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . wpsl_get_gmap_api_params( 'server_key', true );
    $response = wp_remote_get( $url );
    
    return $response;
}

/**
 * Get the latlng for the provided address.
 * 
 * This is used to geocode the address set as the start point on
 * the settings page in case the autocomplete fails
 * ( only happens when there is a JS error on the page ),
 * or to get the latlng when the 'start_location' attr is set
 * on the wpsl shortcode.
 * 
 * @since 2.2
 * @param string      $address The address to geocode.
 * @return array|void $latlng  The returned latlng or nothing if there was an error.
 */
function wpsl_get_address_latlng( $address ) {

    $latlng   = '';
    $response = wpsl_call_geocode_api( $address );

    if ( !is_wp_error( $response ) ) {
        $response = json_decode( $response['body'], true );

        if ( $response['status'] == 'OK' ) {
            $latlng = $response['results'][0]['geometry']['location']['lat'] . ',' . $response['results'][0]['geometry']['location']['lng'];    
        }
    }

    return $latlng;
}

/**
 * Check if there's a transient that holds
 * the coordinates for the passed address.
 *
 * If not, then we geocode the address and
 * set the returned value in the transient.
 *
 * @since 2.2.11
 * @param  string $address The location to geocode
 * @return string $latlng  The coordinates of the geocoded location
 */
function wpsl_check_latlng_transient( $address ) {

    $name_section   = explode( ',', $address );
    $transient_name = 'wpsl_' . trim( strtolower( $name_section[0] ) ) . '_latlng';

    if ( false === ( $latlng = get_transient( $transient_name ) ) ) {
        $latlng = wpsl_get_address_latlng( $address );

        if ( $latlng ) {
            set_transient( $transient_name, $latlng, 0 );
        }
    }

    return $latlng;
}
        
/**
 * Make sure the shortcode attributes are booleans 
 * when they are expected to be.
 *
 * @since 2.0.4
 * @param  array $atts Shortcode attributes
 * @return array $atts Shortcode attributes
 */
function wpsl_bool_check( $atts ) {

    foreach ( $atts as $key => $val ) {
        if ( in_array( $val, array( 'true', '1', 'yes', 'on' ) ) ) {
            $atts[$key] = true;
        } else if ( in_array( $val, array( 'false', '0', 'no', 'off' ) ) ) {
            $atts[$key] = false;
        }
    }

    return $atts;
}

/**
 * Create a string with random characters.
 * 
 * @since 2.2.4
 * @param  int    $length       Used length
 * @return string $random_chars Random characters
 */
function wpsl_random_chars( $length = 5 ) {
    
    $random_chars = substr( str_shuffle( "abcdefghijklmnopqrstuvwxyz" ), 0, $length );

    return $random_chars;
}

/**
 * Deregister other Google Maps scripts.
 * 
 * If plugins / themes also include the Google Maps library, then it can cause
 * problems with the autocomplete function on the settings page and break
 * the store locator on the front-end.
 * 
 * @since 2.2.4
 * @return void
 */
function wpsl_deregister_other_gmaps() {
                
    global $wp_scripts;

    foreach ( $wp_scripts->registered as $index => $script ) {
        if ( ( strpos( $script->src, 'maps.google.com' ) !== false ) || ( strpos( $script->src, 'maps.googleapis.com' ) !== false ) && ( $script->handle !== 'wpsl-gmap' ) ) { 
            wp_deregister_script( $script->handle );
        }
    }
}

/**
 * Return the used distance unit.
 *
 * @since 2.2.8
 * @return string Either km or mi
 */
function wpsl_get_distance_unit() {

    global $wpsl_settings;

    return apply_filters( 'wpsl_distance_unit', $wpsl_settings['distance_unit'] );
}

/**
 * Find the term ids for the provided term slugs.
 *
 * @since 2.2.10
 * @param  array $cat_list List of term slugs
 * @return array $term_ids The term ids
 */
function wpsl_get_term_ids( $cat_list ) {

    $term_ids = array();
    $cats     = explode( ',', $cat_list );

    foreach ( $cats as $key => $term_slug ) {
        $term_data = get_term_by( 'slug', $term_slug, 'wpsl_store_category' );

        if ( isset( $term_data->term_id ) && $term_data->term_id ) {
            $term_ids[] = $term_data->term_id;
        }
    }

    return $term_ids;
}

/**
 * Get the url to the admin-ajax.php
 *
 * @since 2.2.3
 * @return string $ajax_url URL to the admin-ajax.php possibly with the WPML lang param included.
 */
function wpsl_get_ajax_url() {

    $i18n = new WPSL_i18n();

    $param = '';

    if ( $i18n->wpml_exists() && defined( 'ICL_LANGUAGE_CODE' ) ) {
        $param = '?lang=' . ICL_LANGUAGE_CODE;
    }

    $ajax_url = admin_url( 'admin-ajax.php' . $param );

    return $ajax_url;
}

/**
 * Get the used location fields
 *
 * @since 2.2.14
 * @return array $fields
 */
function wpsl_get_location_fields() {

    global $wpsl_admin;

    $fields = array();

    $meta_fields = $wpsl_admin->metaboxes->meta_box_fields();

    $fields['id']       = 'id';
    $fields['distance'] = 'distance';

    foreach ( $meta_fields as $k => $field_section ) {
        foreach ( $field_section as $field_name => $field_value ) {
            if ( in_array( $field_name, array( 'lat', 'lng', 'country_iso' ) ) ) {
                continue;
            }

            $fields[$field_name] = $field_name;
        }
    }

    return $fields;
}