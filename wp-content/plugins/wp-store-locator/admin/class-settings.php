<?php
/**
 * Handle the plugin settings.
 *
 * @author Tijmen Smit
 * @since  2.0.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Settings' ) ) {
    
	class WPSL_Settings {
                        
        public function __construct() {
            
            $this->manually_clear_transient();

            add_action( 'wp_ajax_validate_server_key',        array( $this, 'ajax_validate_server_key' ) );
            add_action( 'wp_ajax_nopriv_validate_server_key', array( $this, 'ajax_validate_server_key' ) );
            add_action( 'admin_init',                         array( $this, 'register_settings' ) );
            add_action( 'admin_init',                         array( $this, 'maybe_flush_rewrite_and_transient' ) );
        }

        /**
         * Determine if we need to clear the autoload transient.
         * 
         * User can do this manually from the 'Tools' section on the settings page. 
         * 
         * @since 2.0.0
         * @return void
         */
        public function manually_clear_transient() {
            
            global $wpsl_admin;
            
            if ( isset( $_GET['action'] ) && $_GET['action'] == 'clear_wpsl_transients' && isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'clear_transients' ) ) {
                $wpsl_admin->delete_autoload_transient();
                
                $msg = __( 'WP Store Locator Transients Cleared', 'wpsl' );
                $wpsl_admin->notices->save( 'update', $msg );
                
                /* 
                 * Make sure the &action=clear_wpsl_transients param is removed from the url.
                 * 
                 * Otherwise if the user later clicks the 'Save Changes' button, 
                 * and the &action=clear_wpsl_transients param is still there it 
                 * will show two notices 'WP Store Locator Transients Cleared' and 'Settings Saved'.
                 */
                wp_redirect( admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_settings' ) );
                exit;
            }
        }

        /**
         * Register the settings.
         * 
         * @since 2.0.0
         * @return void
         */
        public function register_settings() {
            register_setting( 'wpsl_settings', 'wpsl_settings', array( $this, 'sanitize_settings' ) );
        }       
            
        /**
         * Sanitize the submitted plugin settings.
         * 
         * @since 1.0.0
         * @return array $output The setting values
         */
		public function sanitize_settings() {

            global $wpsl_settings, $wpsl_admin;

            $ux_absints = array(
                'height',
                'infowindow_width',
                'search_width',
                'label_width'
            );
            
            $marker_effects = array(
                'bounce',
                'info_window',
                'ignore'
            );
            
            $ux_checkboxes = array(
                'new_window',
                'reset_map',
                'listing_below_no_scroll',
                'direction_redirect',
                'more_info',
                'store_url',
                'phone_url',
                'marker_streetview',
                'marker_zoom_to',
                'mouse_focus',
                'reset_map',
                'show_contact_details',
                'clickable_contact_details',
                'hide_country',
                'hide_distance'
            );

            /*
             * If the provided server key is different then the existing value,
             * then we test if it's valid by making a call to the Geocode API.
             */
            if ( $_POST['wpsl_api']['server_key'] && $wpsl_settings['api_server_key'] != $_POST['wpsl_api']['server_key'] || !get_option( 'wpsl_valid_server_key' ) ) {
                $server_key = sanitize_text_field( $_POST['wpsl_api']['server_key'] );

                $this->validate_server_key( $server_key );
            }

			$output['api_server_key']        = sanitize_text_field( $_POST['wpsl_api']['server_key'] );
            $output['api_browser_key']       = sanitize_text_field( $_POST['wpsl_api']['browser_key'] );
			$output['api_language']          = wp_filter_nohtml_kses( $_POST['wpsl_api']['language'] );
			$output['api_region']            = wp_filter_nohtml_kses( $_POST['wpsl_api']['region'] );
            $output['api_geocode_component'] = isset( $_POST['wpsl_api']['geocode_component'] ) ? 1 : 0;
                        
            // Check the search filter.
            $output['autocomplete']         = isset( $_POST['wpsl_search']['autocomplete'] ) ? 1 : 0;
            $output['results_dropdown']     = isset( $_POST['wpsl_search']['results_dropdown'] ) ? 1 : 0;
            $output['radius_dropdown']      = isset( $_POST['wpsl_search']['radius_dropdown'] ) ? 1 : 0;
            $output['category_filter']      = isset( $_POST['wpsl_search']['category_filter'] ) ? 1 : 0;
            $output['category_filter_type'] = ( $_POST['wpsl_search']['category_filter_type'] == 'dropdown' ) ? 'dropdown' : 'checkboxes';
            
            $output['distance_unit'] = ( $_POST['wpsl_search']['distance_unit'] == 'km' ) ? 'km' : 'mi';
			
			// Check for a valid max results value, otherwise we use the default.
			if ( !empty( $_POST['wpsl_search']['max_results'] ) ) {
				$output['max_results'] = sanitize_text_field( $_POST['wpsl_search']['max_results'] );
			} else {
				$this->settings_error( 'max_results' );
				$output['max_results'] = wpsl_get_default_setting( 'max_results' );
			}
			
			// See if a search radius value exist, otherwise we use the default.
			if ( !empty( $_POST['wpsl_search']['radius'] ) ) {
				$output['search_radius'] = sanitize_text_field( $_POST['wpsl_search']['radius'] );
			} else {
				$this->settings_error( 'search_radius' );
				$output['search_radius'] = wpsl_get_default_setting( 'search_radius' );
			}

			// Check if we have a valid zoom level, it has to be between 1 or 12. If not set it to the default of 3.
			$output['zoom_level'] = wpsl_valid_zoom_level( $_POST['wpsl_map']['zoom_level'] );	
            
            // Check for a valid max auto zoom level.
            $max_zoom_levels = wpsl_get_max_zoom_levels();
            
            if ( in_array( absint( $_POST['wpsl_map']['max_auto_zoom'] ), $max_zoom_levels ) ) {
                $output['auto_zoom_level'] = $_POST['wpsl_map']['max_auto_zoom'];
            } else {
                $output['auto_zoom_level'] = wpsl_get_default_setting( 'auto_zoom_level' );
            }

            if ( isset( $_POST['wpsl_map']['start_name'] ) ) {
                $output['start_name'] = sanitize_text_field( $_POST['wpsl_map']['start_name'] );
            } else {
                $output['start_name'] = '';
            }

			// If no location name is then we also empty the latlng values from the hidden input field.
			if ( empty( $output['start_name'] ) ) {
				$this->settings_error( 'start_point' );
                $output['start_latlng'] = '';
			} else {

                /*
                 * If the start latlng is empty, but a start location name is provided, 
                 * then make a request to the Geocode API to get it.
                 * 
                 * This can only happen if there is a JS error in the admin area that breaks the
                 * Google Maps Autocomplete. So this code is only used as fallback to make sure
                 * the provided start location is always geocoded.
                 */
                if ( $wpsl_settings['start_name'] != $_POST['wpsl_map']['start_name']
                  && $wpsl_settings['start_latlng'] == $_POST['wpsl_map']['start_latlng']
                  || empty( $_POST['wpsl_map']['start_latlng'] ) ) {
                    $start_latlng = wpsl_get_address_latlng( $_POST['wpsl_map']['start_name'] );
                } else {
                    $start_latlng = sanitize_text_field( $_POST['wpsl_map']['start_latlng'] );
                }
                
				$output['start_latlng'] = $start_latlng;
			}

			// Do we need to run the fitBounds function make the markers fit in the viewport?
            $output['run_fitbounds'] = isset( $_POST['wpsl_map']['run_fitbounds'] ) ? 1 : 0;

			// Check if we have a valid map type.
			$output['map_type']    = wpsl_valid_map_type( $_POST['wpsl_map']['type'] );
            $output['auto_locate'] = isset( $_POST['wpsl_map']['auto_locate'] ) ? 1 : 0; 
            $output['autoload']    = isset( $_POST['wpsl_map']['autoload'] ) ? 1 : 0; 

            // Make sure the auto load limit is either empty or an int.
            if ( empty( $_POST['wpsl_map']['autoload_limit'] ) ) {
                $output['autoload_limit'] = '';
            } else {
                $output['autoload_limit'] = absint( $_POST['wpsl_map']['autoload_limit'] );
            }
     
			$output['streetview'] 		= isset( $_POST['wpsl_map']['streetview'] ) ? 1 : 0;
            $output['type_control']     = isset( $_POST['wpsl_map']['type_control'] ) ? 1 : 0;
            $output['scrollwheel']      = isset( $_POST['wpsl_map']['scrollwheel'] ) ? 1 : 0;	
			$output['control_position'] = ( $_POST['wpsl_map']['control_position'] == 'left' ) ? 'left' : 'right';	
            
            $output['map_style'] = json_encode( strip_tags( trim( $_POST['wpsl_map']['map_style'] ) ) );
                    
            // Make sure we have a valid template ID.
            if ( isset( $_POST['wpsl_ux']['template_id'] ) && ( $_POST['wpsl_ux']['template_id'] ) ) {
				$output['template_id'] = sanitize_text_field( $_POST['wpsl_ux']['template_id'] );
			} else {
				$output['template_id'] = wpsl_get_default_setting( 'template_id' );
			}
            
            $output['marker_clusters'] = isset( $_POST['wpsl_map']['marker_clusters'] ) ? 1 : 0;	
                        
            // Check for a valid cluster zoom value.
            if ( in_array( $_POST['wpsl_map']['cluster_zoom'], $this->get_default_cluster_option( 'cluster_zoom' ) ) ) {
                $output['cluster_zoom'] = $_POST['wpsl_map']['cluster_zoom'];
            } else {
                $output['cluster_zoom'] = wpsl_get_default_setting( 'cluster_zoom' );
            }
            
            // Check for a valid cluster size value.
            if ( in_array( $_POST['wpsl_map']['cluster_size'], $this->get_default_cluster_option( 'cluster_size' ) ) ) {
                $output['cluster_size'] = $_POST['wpsl_map']['cluster_size'];
            } else {
                $output['cluster_size'] = wpsl_get_default_setting( 'cluster_size' );
            }
                        
            /* 
             * Make sure all the ux related fields that should contain an int, actually are an int.
             * Otherwise we use the default value. 
             */
            foreach ( $ux_absints as $ux_key ) {
                if ( absint( $_POST['wpsl_ux'][$ux_key] ) ) {
                    $output[$ux_key] = $_POST['wpsl_ux'][$ux_key];
                } else {
                    $output[$ux_key] = wpsl_get_default_setting( $ux_key );
                }
            }
            
            // Check if the ux checkboxes are checked.
            foreach ( $ux_checkboxes as $ux_key ) {
                $output[$ux_key] = isset( $_POST['wpsl_ux'][$ux_key] ) ? 1 : 0; 
            }
            
            // Check if we have a valid marker effect.
            if ( in_array( $_POST['wpsl_ux']['marker_effect'], $marker_effects ) ) {
                $output['marker_effect'] = $_POST['wpsl_ux']['marker_effect'];
            } else {
				$output['marker_effect'] = wpsl_get_default_setting( 'marker_effect' );
			}
            
            // Check if we have a valid address format.  
            if ( array_key_exists( $_POST['wpsl_ux']['address_format'], wpsl_get_address_formats() ) ) {
                $output['address_format'] = $_POST['wpsl_ux']['address_format'];
            } else {
				$output['address_format'] = wpsl_get_default_setting( 'address_format' );
			}
            
            $output['more_info_location'] = ( $_POST['wpsl_ux']['more_info_location'] == 'store listings' ) ? 'store listings' : 'info window';	
            $output['infowindow_style']   = isset( $_POST['wpsl_ux']['infowindow_style'] ) ? 'default' : 'infobox';
            $output['start_marker']       = wp_filter_nohtml_kses( $_POST['wpsl_map']['start_marker'] );
            $output['store_marker']       = wp_filter_nohtml_kses( $_POST['wpsl_map']['store_marker'] );
			$output['editor_country']     = sanitize_text_field( $_POST['wpsl_editor']['default_country'] );
            $output['editor_map_type']    = wpsl_valid_map_type( $_POST['wpsl_editor']['map_type'] );
            $output['hide_hours']         = isset( $_POST['wpsl_editor']['hide_hours'] ) ? 1 : 0; 
            
            if ( isset( $_POST['wpsl_editor']['hour_input'] ) ) {
				$output['editor_hour_input'] = ( $_POST['wpsl_editor']['hour_input'] == 'textarea' ) ? 'textarea' : 'dropdown';	
			} else {
				$output['editor_hour_input'] = 'dropdown';
			}
            
            $output['editor_hour_format'] = ( $_POST['wpsl_editor']['hour_format'] == 12 ) ? 12 : 24;
            
            // The default opening hours.
            if ( isset( $_POST['wpsl_editor']['textarea'] ) ) {
                $output['editor_hours']['textarea'] = wp_kses_post( trim( stripslashes( $_POST['wpsl_editor']['textarea'] ) ) );
            }
            
            $output['editor_hours']['dropdown'] = $wpsl_admin->metaboxes->format_opening_hours();
            array_walk_recursive( $output['editor_hours']['dropdown'], 'wpsl_sanitize_multi_array' );  
            
            // Permalink and taxonomy slug.
            $output['permalinks'] = isset( $_POST['wpsl_permalinks']['active'] ) ? 1 : 0;
            
            if ( !empty( $_POST['wpsl_permalinks']['slug'] ) ) {
				$output['permalink_slug'] = sanitize_text_field( $_POST['wpsl_permalinks']['slug'] );
			} else {
				$output['permalink_slug'] = wpsl_get_default_setting( 'permalink_slug' );
			}
            
            if ( !empty( $_POST['wpsl_permalinks']['category_slug'] ) ) {
				$output['category_slug'] = sanitize_text_field( $_POST['wpsl_permalinks']['category_slug'] );
			} else {
				$output['category_slug'] = wpsl_get_default_setting( 'category_slug' );
			}
                                    
			$required_labels = wpsl_labels();
            
			// Sanitize the labels.
			foreach ( $required_labels as $label ) {
                $output[$label.'_label'] = sanitize_text_field( $_POST['wpsl_label'][$label] );
			}

            $output['show_credits']     = isset( $_POST['wpsl_credits'] ) ? 1 : 0;
            $output['debug']            = isset( $_POST['wpsl_tools']['debug'] ) ? 1 : 0;
            $output['deregister_gmaps'] = isset( $_POST['wpsl_tools']['deregister_gmaps'] ) ? 1 : 0;
            
            // Check if we need to flush the permalinks.
            $this->set_flush_rewrite_option( $output );           
  
            // Check if there is a reason to delete the autoload transient.
            if ( $wpsl_settings['autoload'] ) {
                $this->set_delete_transient_option( $output );
            }
            
			return $output;
		}

        /**
         * Handle the AJAX call to validate the provided
         * server key for the Google Maps API.
         *
         * @since 2.2.10
         * @return void
         */
        public function ajax_validate_server_key() {

            if ( ( current_user_can( 'manage_wpsl_settings' ) ) && is_admin() ) {
                $server_key = sanitize_text_field( $_GET['server_key'] );

                if ( $server_key ) {
                    $this->validate_server_key( $server_key );
                }
            }
        }

        /**
         * Check if the provided server key for
         * the Google Maps API is valid.
         *
         * @since 2.2.10
         * @param string $server_key The server key to validate
         * @return json|void If the validation failed and AJAX is used, then json
         */
        public function validate_server_key( $server_key ) {

            global $wpsl_admin;

            // Test the server key by making a request to the Geocode API.
            $address  = 'Manhattan, NY 10036, USA';
            $url      = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) .'&key=' . $server_key;
            $response = wp_remote_get( $url );

            if ( !is_wp_error( $response ) ) {
                $response = json_decode( $response['body'], true );

                // If the state is not OK, then there's a problem with the key.
                if ( $response['status'] !== 'OK' ) {
                    $geocode_errors = $wpsl_admin->geocode->check_geocode_error_msg( $response, true );
                    $error_msg      = sprintf( __( 'There\'s a problem with the provided %sserver key%s. %s' ), '<a href="https://wpstorelocator.co/document/create-google-api-keys/#server-key">', '</a>', $geocode_errors );

                    update_option( 'wpsl_valid_server_key', 0 );

                    // If the server key input field has 'wpsl-validate-me' class on it, then it's validated with AJAX in the background.
                    if ( defined('DOING_AJAX' ) && DOING_AJAX ) {
                        $key_status = array(
                            'valid' => 0,
                            'msg'   => $error_msg
                        );

                        wp_send_json( $key_status );

                        exit();
                    } else {
                        add_settings_error( 'setting-errors', esc_attr( 'server-key' ), $error_msg, 'error' );
                    }
                } else {
                    update_option( 'wpsl_valid_server_key', 1 );
                }
            }
        }

        /**
         * Check if we need set the option that will be used to determine 
         * if we need to flush the permalinks once the setting page reloads.
         * 
         * @since 2.0.0
         * @param array $new_settings The submitted plugin settings
         * @return void
         */
        public function set_flush_rewrite_option( $new_settings ) {
            
            global $wpsl_settings;
            
            if ( ( $wpsl_settings['permalinks'] != $new_settings['permalinks'] ) || ( $wpsl_settings['permalink_slug'] != $new_settings['permalink_slug'] ) || ( $wpsl_settings['category_slug'] != $new_settings['category_slug'] ) ) {
                update_option( 'wpsl_flush_rewrite', 1 );
            }
        }
        
        /**
         * Check if we need set the option that is used to determine 
         * if we need to delete the autoload transient once the settings page reloads.
         * 
         * @since 2.0.0
         * @param array $new_settings The submitted plugin settings
         * @return void
         */
        public function set_delete_transient_option( $new_settings ) {

            global $wpsl_settings;

            // The options we need to check for changes.
            $options = array(
                'start_name',
                'debug',
                'autoload', 
                'autoload_limit', 
                'more_info', 
                'more_info_location', 
                'hide_hours',
                'hide_distance',
                'hide_country',
                'show_contact_details'
            );

            foreach ( $options as $option_name ) {
                if ( $wpsl_settings[$option_name] != $new_settings[$option_name] ) {
                    update_option( 'wpsl_delete_transient', 1 );
                    break;
                }
            }
        }

        /**
         * Check if the permalinks settings changed.
         * 
         * @since 2.0.0
         * @return void
         */
        public function maybe_flush_rewrite_and_transient() {

            global $wpsl_admin;
            
            if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'wpsl_settings' ) ) {
                $flush_rewrite    = get_option( 'wpsl_flush_rewrite' );
                $delete_transient = get_option( 'wpsl_delete_transient' );
                
                if ( $flush_rewrite ) {
                    flush_rewrite_rules();
                    update_option( 'wpsl_flush_rewrite', 0 );
                }
                
                if ( $delete_transient ) {
                    update_option( 'wpsl_delete_transient', 0 );
                }
                
                if ( $flush_rewrite || $delete_transient ) {
                    $wpsl_admin->delete_autoload_transient();     
                }
            }
        }

        /**
         * Handle the different validation errors for the plugin settings.
         * 
         * @since 1.0.0
         * @param string $error_type Contains the type of validation error that occured
         * @return void
         */
		private function settings_error( $error_type ) {
            
			switch ( $error_type ) {
				case 'max_results':
					$error_msg = __( 'The max results field cannot be empty, the default value has been restored.', 'wpsl' );	
					break;
				case 'search_radius':
					$error_msg = __( 'The search radius field cannot be empty, the default value has been restored.', 'wpsl' );	
					break;	
                case 'start_point':
					$error_msg = sprintf( __( 'Please provide the name of a city or country that can be used as a starting point under "Map Settings". %s This will only be used if auto-locating the user fails, or the option itself is disabled.', 'wpsl' ), '<br><br>' );
					break;
			}
			
			add_settings_error( 'setting-errors', esc_attr( 'settings_fail' ), $error_msg, 'error' );
		}
        
        /**
         * Options for the language and region list.
         *
         * @since 1.0.0
         * @param  string      $list        The request list type
         * @return string|void $option_list The html for the selected list, or nothing if the $list contains invalud values
         */
		public function get_api_option_list( $list ) {
            
            global $wpsl_settings;
            
			switch ( $list ) {
				case 'language':	
					$api_option_list = array ( 	
						__('Select your language', 'wpsl')    => '',
						__('English', 'wpsl')                 => 'en',
						__('Arabic', 'wpsl')                  => 'ar',
						__('Basque', 'wpsl')                  => 'eu',
						__('Bulgarian', 'wpsl')               => 'bg',
						__('Bengali', 'wpsl')                 => 'bn',
						__('Catalan', 'wpsl')                 => 'ca',
						__('Czech', 'wpsl')                   => 'cs',
						__('Danish', 'wpsl')                  => 'da',
						__('German', 'wpsl')                  => 'de',
						__('Greek', 'wpsl')                   => 'el',
						__('English (Australian)', 'wpsl')    => 'en-AU',
						__('English (Great Britain)', 'wpsl') => 'en-GB',
						__('Spanish', 'wpsl')                 => 'es',
						__('Farsi', 'wpsl')                   => 'fa',
						__('Finnish', 'wpsl')                 => 'fi',
						__('Filipino', 'wpsl')                => 'fil',
						__('French', 'wpsl')                  => 'fr',
						__('Galician', 'wpsl')                => 'gl',
						__('Gujarati', 'wpsl')                => 'gu',
						__('Hindi', 'wpsl')                   => 'hi',
						__('Croatian', 'wpsl')                => 'hr',
						__('Hungarian', 'wpsl')               => 'hu',
						__('Indonesian', 'wpsl')              => 'id',
						__('Italian', 'wpsl')                 => 'it',
						__('Hebrew', 'wpsl')                  => 'iw',
						__('Japanese', 'wpsl')                => 'ja',
						__('Kannada', 'wpsl')                 => 'kn',
						__('Korean', 'wpsl')                  => 'ko',
						__('Lithuanian', 'wpsl')              => 'lt',
						__('Latvian', 'wpsl')                 => 'lv',
						__('Malayalam', 'wpsl')               => 'ml',
						__('Marathi', 'wpsl')                 => 'mr',
						__('Dutch', 'wpsl')                   => 'nl',
						__('Norwegian', 'wpsl')               => 'no',
						__('Norwegian Nynorsk', 'wpsl')       => 'nn',
						__('Polish', 'wpsl')                  => 'pl',
						__('Portuguese', 'wpsl')              => 'pt',
						__('Portuguese (Brazil)', 'wpsl')     => 'pt-BR',
						__('Portuguese (Portugal)', 'wpsl')   => 'pt-PT',
						__('Romanian', 'wpsl')                => 'ro',
						__('Russian', 'wpsl')                 => 'ru',
						__('Slovak', 'wpsl')                  => 'sk',
						__('Slovenian', 'wpsl')               => 'sl',
						__('Serbian', 'wpsl')                 => 'sr',
						__('Swedish', 'wpsl')                 => 'sv',
						__('Tagalog', 'wpsl')                 => 'tl',
						__('Tamil', 'wpsl')                   => 'ta',
						__('Telugu', 'wpsl')                  => 'te',
						__('Thai', 'wpsl')                    => 'th',
						__('Turkish', 'wpsl')                 => 'tr',
						__('Ukrainian', 'wpsl')               => 'uk',
						__('Vietnamese', 'wpsl')              => 'vi',
						__('Chinese (Simplified)', 'wpsl')    => 'zh-CN',
						__('Chinese (Traditional)' ,'wpsl')   => 'zh-TW'
				);	
					break;			
				case 'region':
                    $api_option_list = array (
                        __('Select your region', 'wpsl')               => '',
                        __('Afghanistan', 'wpsl')                      => 'af',
                        __('Albania', 'wpsl')                          => 'al',
                        __('Algeria', 'wpsl')                          => 'dz',
                        __('American Samoa', 'wpsl')                   => 'as',
                        __('Andorra', 'wpsl')                          => 'ad',
                        __('Angola', 'wpsl')                           => 'ao',
                        __('Anguilla', 'wpsl')                         => 'ai',
                        __('Antarctica', 'wpsl')                       => 'aq',
                        __('Antigua and Barbuda', 'wpsl')              => 'ag',
                        __('Argentina', 'wpsl')                        => 'ar',
                        __('Armenia', 'wpsl')                          => 'am',
                        __('Aruba', 'wpsl')                            => 'aw',
                        __('Ascension Island', 'wpsl')                 => 'ac',
                        __('Australia', 'wpsl')                        => 'au',
                        __('Austria', 'wpsl')                          => 'at',
                        __('Azerbaijan', 'wpsl')                       => 'az',
                        __('Bahamas', 'wpsl')                          => 'bs',
                        __('Bahrain', 'wpsl')                          => 'bh',
                        __('Bangladesh', 'wpsl')                       => 'bd',
                        __('Barbados', 'wpsl')                         => 'bb',
                        __('Belarus', 'wpsl')                          => 'by',
                        __('Belgium', 'wpsl')                          => 'be',
                        __('Belize', 'wpsl')                           => 'bz',
                        __('Benin', 'wpsl')                            => 'bj',
                        __('Bermuda', 'wpsl')                          => 'bm',
                        __('Bhutan', 'wpsl')                           => 'bt',
                        __('Bolivia', 'wpsl')                          => 'bo',
                        __('Bosnia and Herzegovina', 'wpsl')           => 'ba',
                        __('Botswana', 'wpsl')                         => 'bw',
                        __('Bouvet Island', 'wpsl')                    => 'bv',
                        __('Brazil', 'wpsl')                           => 'br',
                        __('British Indian Ocean Territory', 'wpsl')   => 'io',
                        __('British Virgin Islands', 'wpsl')           => 'vg',
                        __('Brunei', 'wpsl')                           => 'bn',
                        __('Bulgaria', 'wpsl')                         => 'bg',
                        __('Burkina Faso', 'wpsl')                     => 'bf',
                        __('Burundi', 'wpsl')                          => 'bi',
                        __('Cambodia', 'wpsl')                         => 'kh',
                        __('Cameroon', 'wpsl')                         => 'cm',
                        __('Canada', 'wpsl')                           => 'ca',
                        __('Canary Islands', 'wpsl')                   => 'ic',
                        __('Cape Verde', 'wpsl')                       => 'cv',
                        __('Caribbean Netherlands', 'wpsl')            => 'bq',
                        __('Cayman Islands', 'wpsl')                   => 'ky',
                        __('Central African Republic', 'wpsl')         => 'cf',
                        __('Ceuta and Melilla', 'wpsl')                => 'ea',
                        __('Chad', 'wpsl')                             => 'td',
                        __('Chile', 'wpsl')                            => 'cl',
                        __('China', 'wpsl')                            => 'cn',
                        __('Christmas Island', 'wpsl')                 => 'cx',
                        __('Clipperton Island', 'wpsl')                => 'cp',
                        __('Cocos (Keeling) Islands', 'wpsl')          => 'cc',
                        __('Colombia', 'wpsl')                         => 'co',
                        __('Comoros', 'wpsl')                          => 'km',
                        __('Congo (DRC)', 'wpsl')                       => 'cd',
                        __('Congo (Republic)', 'wpsl')                 => 'cg',
                        __('Cook Islands', 'wpsl')                     => 'ck',
                        __('Costa Rica', 'wpsl')                       => 'cr',
                        __('Croatia', 'wpsl')                          => 'hr',
                        __('Cuba', 'wpsl')                             => 'cu',
                        __('Curaçao', 'wpsl')                          => 'cw',
                        __('Cyprus', 'wpsl')                           => 'cy',
                        __('Czech Republic', 'wpsl')                   => 'cz',
                        __('Côte d\'Ivoire', 'wpsl')                   => 'ci',
                        __('Denmark', 'wpsl')                          => 'dk',
                        __('Djibouti', 'wpsl')                         => 'dj',
                        __('Democratic Republic of the Congo', 'wpsl') => 'cd',
                        __('Dominica', 'wpsl')                         => 'dm',
                        __('Dominican Republic', 'wpsl')               => 'do',
                        __('Ecuador', 'wpsl')                          => 'ec',
                        __('Egypt', 'wpsl')                            => 'eg',
                        __('El Salvador', 'wpsl')                      => 'sv',
                        __('Equatorial Guinea', 'wpsl')                => 'gq',
                        __('Eritrea', 'wpsl')                          => 'er',
                        __('Estonia', 'wpsl')                          => 'ee',
                        __('Ethiopia', 'wpsl')                         => 'et',
                        __('Falkland Islands(Islas Malvinas)', 'wpsl') => 'fk',
                        __('Faroe Islands', 'wpsl')                    => 'fo',
                        __('Fiji', 'wpsl')                             => 'fj',
                        __('Finland', 'wpsl')                          => 'fi',
                        __('France', 'wpsl')                           => 'fr',
                        __('French Guiana', 'wpsl')                    => 'gf',
                        __('French Polynesia', 'wpsl')                 => 'pf',
                        __('French Southern Territories', 'wpsl')      => 'tf',
                        __('Gabon', 'wpsl')                            => 'ga',
                        __('Gambia', 'wpsl')                           => 'gm',
                        __('Georgia', 'wpsl')                          => 'ge',
                        __('Germany', 'wpsl')                          => 'de',
                        __('Ghana', 'wpsl')                            => 'gh',
                        __('Gibraltar', 'wpsl')                        => 'gi',
                        __('Greece', 'wpsl')                           => 'gr',
                        __('Greenland', 'wpsl')                        => 'gl',
                        __('Grenada', 'wpsl')                          => 'gd',
                        __('Guam', 'wpsl')                             => 'gu',
                        __('Guadeloupe', 'wpsl')                       => 'gp',
                        __('Guam', 'wpsl')                             => 'gu',
                        __('Guatemala', 'wpsl')                        => 'gt',
                        __('Guernsey', 'wpsl')                         => 'gg',
                        __('Guinea', 'wpsl')                           => 'gn',
                        __('Guinea-Bissau', 'wpsl')                    => 'gw',
                        __('Guyana', 'wpsl')                           => 'gy',
                        __('Haiti', 'wpsl')                            => 'ht',
                        __('Heard and McDonald Islands', 'wpsl')       => 'hm',
                        __('Honduras', 'wpsl')                         => 'hn',
                        __('Hong Kong', 'wpsl')                        => 'hk',
                        __('Hungary', 'wpsl')                          => 'hu',
                        __('Iceland', 'wpsl')                          => 'is',
                        __('India', 'wpsl')                            => 'in',
                        __('Indonesia', 'wpsl')                        => 'id',
                        __('Iran', 'wpsl')                             => 'ir',
                        __('Iraq', 'wpsl')                             => 'iq',
                        __('Ireland', 'wpsl')                          => 'ie',
                        __('Isle of Man', 'wpsl')                      => 'im',
                        __('Israel', 'wpsl')                           => 'il',
                        __('Italy', 'wpsl')                            => 'it',
                        __('Jamaica', 'wpsl')                          => 'jm',
                        __('Japan', 'wpsl')                            => 'jp',
                        __('Jersey', 'wpsl')                           => 'je',
                        __('Jordan', 'wpsl')                           => 'jo',
                        __('Kazakhstan', 'wpsl')                       => 'kz',
                        __('Kenya', 'wpsl')                            => 'ke',
                        __('Kiribati', 'wpsl')                         => 'ki',
                        __('Kosovo', 'wpsl')                           => 'xk',
                        __('Kuwait', 'wpsl')                           => 'kw',
                        __('Kyrgyzstan', 'wpsl')                       => 'kg',
                        __('Laos', 'wpsl')                             => 'la',
                        __('Latvia', 'wpsl')                           => 'lv',
                        __('Lebanon', 'wpsl')                          => 'lb',
                        __('Lesotho', 'wpsl')                          => 'ls',
                        __('Liberia', 'wpsl')                          => 'lr',
                        __('Libya', 'wpsl')                            => 'ly',
                        __('Liechtenstein', 'wpsl')                    => 'li',
                        __('Lithuania', 'wpsl')                        => 'lt',
                        __('Luxembourg', 'wpsl')                       => 'lu',
                        __('Macau', 'wpsl')                            => 'mo',
                        __('Macedonia (FYROM)', 'wpsl')                => 'mk',
                        __('Madagascar', 'wpsl')                       => 'mg',
                        __('Malawi', 'wpsl')                           => 'mw',
                        __('Malaysia ', 'wpsl')                        => 'my',
                        __('Maldives ', 'wpsl')                        => 'mv',
                        __('Mali', 'wpsl')                             => 'ml',
                        __('Malta', 'wpsl')                            => 'mt',
                        __('Marshall Islands', 'wpsl')                 => 'mh',
                        __('Martinique', 'wpsl')                       => 'mq',
                        __('Mauritania', 'wpsl')                       => 'mr',
                        __('Mauritius', 'wpsl')                        => 'mu',
                        __('Mayotte', 'wpsl')                          => 'yt',
                        __('Mexico', 'wpsl')                           => 'mx',
                        __('Micronesia', 'wpsl')                       => 'fm',
                        __('Moldova', 'wpsl')                          => 'md',
                        __('Monaco' ,'wpsl')                           => 'mc',
                        __('Mongolia', 'wpsl')                         => 'mn',
                        __('Montenegro', 'wpsl')                       => 'me',
                        __('Montserrat', 'wpsl')                       => 'ms',
                        __('Morocco', 'wpsl')                          => 'ma',
                        __('Mozambique', 'wpsl')                       => 'mz',
                        __('Myanmar (Burma)', 'wpsl')                  => 'mm',
                        __('Namibia', 'wpsl')                          => 'na',
                        __('Nauru', 'wpsl')                            => 'nr',
                        __('Nepal', 'wpsl')                            => 'np',
                        __('Netherlands', 'wpsl')                      => 'nl',
                        __('Netherlands Antilles', 'wpsl')             => 'an',
                        __('New Caledonia', 'wpsl')                    => 'nc',
                        __('New Zealand', 'wpsl')                      => 'nz',
                        __('Nicaragua', 'wpsl')                        => 'ni',
                        __('Niger', 'wpsl')                            => 'ne',
                        __('Nigeria', 'wpsl')                          => 'ng',
                        __('Niue', 'wpsl')                             => 'nu',
                        __('Norfolk Island', 'wpsl')                   => 'nf',
                        __('North Korea', 'wpsl')                      => 'kp',
                        __('Northern Mariana Islands', 'wpsl')         => 'mp',
                        __('Norway', 'wpsl')                           => 'no',
                        __('Oman', 'wpsl')                             => 'om',
                        __('Pakistan', 'wpsl')                         => 'pk',
                        __('Palau', 'wpsl')                            => 'pw',
                        __('Palestine', 'wpsl')                        => 'ps',
                        __('Panama' ,'wpsl')                           => 'pa',
                        __('Papua New Guinea', 'wpsl')                 => 'pg',
                        __('Paraguay' ,'wpsl')                         => 'py',
                        __('Peru', 'wpsl')                             => 'pe',
                        __('Philippines', 'wpsl')                      => 'ph',
                        __('Pitcairn Islands', 'wpsl')                 => 'pn',
                        __('Poland', 'wpsl')                           => 'pl',
                        __('Portugal', 'wpsl')                         => 'pt',
                        __('Puerto Rico', 'wpsl')                      => 'pr',
                        __('Qatar', 'wpsl')                            => 'qa',
                        __('Reunion', 'wpsl')                          => 're',
                        __('Romania', 'wpsl')                          => 'ro',
                        __('Russia', 'wpsl')                           => 'ru',
                        __('Rwanda', 'wpsl')                           => 'rw',
                        __('Saint Helena', 'wpsl')                     => 'sh',
                        __('Saint Kitts and Nevis', 'wpsl')            => 'kn',
                        __('Saint Vincent and the Grenadines', 'wpsl') => 'vc',
                        __('Saint Lucia', 'wpsl')                      => 'lc',
                        __('Samoa', 'wpsl')                            => 'ws',
                        __('San Marino', 'wpsl')                       => 'sm',
                        __('São Tomé and Príncipe', 'wpsl')            => 'st',
                        __('Saudi Arabia', 'wpsl')                     => 'sa',
                        __('Senegal', 'wpsl')                          => 'sn',
                        __('Serbia', 'wpsl')                           => 'rs',
                        __('Seychelles', 'wpsl')                       => 'sc',
                        __('Sierra Leone', 'wpsl')                     => 'sl',
                        __('Singapore', 'wpsl')                        => 'sg',
                        __('Sint Maarten', 'wpsl')                     => 'sx',
                        __('Slovakia', 'wpsl')                         => 'sk',
                        __('Slovenia', 'wpsl')                         => 'si',
                        __('Solomon Islands', 'wpsl')                  => 'sb',
                        __('Somalia', 'wpsl')                          => 'so',
                        __('South Africa', 'wpsl')                     => 'za',
                        __('South Georgia and South Sandwich Islands', 'wpsl') => 'gs',
                        __('South Korea', 'wpsl')                      => 'kr',
                        __('South Sudan', 'wpsl')                      => 'ss',
                        __('Spain', 'wpsl')                            => 'es',
                        __('Sri Lanka', 'wpsl')                        => 'lk',
                        __('Sudan', 'wpsl')                            => 'sd',
                        __('Swaziland', 'wpsl')                        => 'sz',
                        __('Sweden', 'wpsl')                           => 'se',
                        __('Switzerland', 'wpsl')                      => 'ch',
                        __('Syria', 'wpsl')                            => 'sy',
                        __('São Tomé & Príncipe', 'wpsl')              => 'st',
                        __('Taiwan', 'wpsl')                           => 'tw',
                        __('Tajikistan', 'wpsl')                       => 'tj',
                        __('Tanzania', 'wpsl')                         => 'tz',
                        __('Thailand', 'wpsl')                         => 'th',
                        __('Timor-Leste', 'wpsl')                      => 'tl',
                        __('Tokelau' ,'wpsl')                          => 'tk',
                        __('Togo', 'wpsl')                             => 'tg',
                        __('Tokelau' ,'wpsl')                          => 'tk',
                        __('Tonga', 'wpsl')                            => 'to',
                        __('Trinidad and Tobago', 'wpsl')              => 'tt',
                        __('Tristan da Cunha', 'wpsl')                 => 'ta',
                        __('Tunisia', 'wpsl')                          => 'tn',
                        __('Turkey', 'wpsl')                           => 'tr',
                        __('Turkmenistan', 'wpsl')                     => 'tm',
                        __('Turks and Caicos Islands', 'wpsl')         => 'tc',
                        __('Tuvalu', 'wpsl')                           => 'tv',
                        __('Uganda', 'wpsl')                           => 'ug',
                        __('Ukraine', 'wpsl')                          => 'ua',
                        __('United Arab Emirates', 'wpsl')             => 'ae',
                        __('United Kingdom', 'wpsl')                   => 'gb',
                        __('United States', 'wpsl')                    => 'us',
                        __('Uruguay', 'wpsl')                          => 'uy',
                        __('Uzbekistan', 'wpsl')                       => 'uz',
                        __('Vanuatu', 'wpsl')                          => 'vu',
                        __('Vatican City', 'wpsl')                     => 'va',
                        __('Venezuela', 'wpsl')                        => 've',
                        __('Vietnam', 'wpsl')                          => 'vn',
                        __('Wallis Futuna', 'wpsl')                    => 'wf',
                        __('Western Sahara', 'wpsl')                   => 'eh',
                        __('Yemen', 'wpsl')                            => 'ye',
                        __('Zambia' ,'wpsl')                           => 'zm',
                        __('Zimbabwe', 'wpsl')                         => 'zw',
                        __('Åland Islands', 'wpsl')                    => 'ax'
                    );
			}
			
			// Make sure we have an array with a value.
			if ( !empty( $api_option_list ) && ( is_array( $api_option_list ) ) ) {
                $option_list = '';
				$i = 0;
				
				foreach ( $api_option_list as $api_option_key => $api_option_value ) {  
				
					// If no option value exist, set the first one as selected.
					if ( ( $i == 0 ) && ( empty( $wpsl_settings['api_'.$list] ) ) ) {
						$selected = 'selected="selected"';
					} else {
						$selected = ( $wpsl_settings['api_'.$list] == $api_option_value ) ? 'selected="selected"' : '';
					}
					
					$option_list .= '<option value="' . esc_attr( $api_option_value ) . '" ' . $selected . '> ' . esc_html( $api_option_key ) . '</option>';
					$i++;
				}
												
				return $option_list;				
			}
		}
        
        /**
         * Create the dropdown to select the zoom level.
         *
         * @since 1.0.0
         * @return string $dropdown The html for the zoom level list
         */
		public function show_zoom_levels() {
            
            global $wpsl_settings;
                        
			$dropdown = '<select id="wpsl-zoom-level" name="wpsl_map[zoom_level]" autocomplete="off">';
			
			for ( $i = 1; $i < 13; $i++ ) {
				$selected = ( $wpsl_settings['zoom_level'] == $i ) ? 'selected="selected"' : '';
				
				switch ( $i ) {
					case 1:
						$zoom_desc = ' - ' . __( 'World view', 'wpsl' );
						break;
					case 3:
						$zoom_desc = ' - ' . __( 'Default', 'wpsl' );
						break;
					case 12:
						$zoom_desc = ' - ' . __( 'Roadmap', 'wpsl' );
						break;	
					default:
						$zoom_desc = '';		
				}
		
				$dropdown .= "<option value='$i' $selected>". $i . esc_html( $zoom_desc ) . "</option>";	
			}
				
			$dropdown .= "</select>";
				
			return $dropdown;
		}
        
        /**
         * Create the html output for the marker list that is shown on the settings page.
         * 
         * There are two markers lists, one were the user can set the marker for the start point 
         * and one were a marker can be set for the store. We also check if the marker img is identical
         * to the name in the option field. If so we set it to checked.
         *
         * @since 1.0.0
         * @param  string $marker_img  The filename of the marker
         * @param  string $location    Either contains "start" or "store"
         * @return string $marker_list A list of all the available markers
         */
        public function create_marker_html( $marker_img, $location ) {

            global $wpsl_settings;

            $marker_path = ( defined( 'WPSL_MARKER_URI' ) ) ? WPSL_MARKER_URI : WPSL_URL . 'img/markers/';
            $marker_list = '';

            if ( $wpsl_settings[$location.'_marker'] == $marker_img ) {
                $checked   = 'checked="checked"';
                $css_class = 'class="wpsl-active-marker"';
            } else {
                $checked   = '';
                $css_class = '';
            }
            
            $marker_list .= '<li ' . $css_class . '>';
            $marker_list .= '<img src="' . $marker_path . $marker_img . '" />';
            $marker_list .= '<input ' . $checked . ' type="radio" name="wpsl_map[' . $location . '_marker]"  value="' . $marker_img . '" />';
            $marker_list .= '</li>';

            return $marker_list;
        }
        
        /**
         * Get the default values for the marker clusters dropdown options.
         *
         * @since 1.2.20
         * @param  string $type           The cluster option type
         * @return string $cluster_values The default cluster options
         */
		public function get_default_cluster_option( $type ) {
            
            $cluster_values = array(
                'cluster_zoom' => array(
                    '7',
                    '8',
                    '9',
                    '10',
                    '11',
                    '12',
                    '13'
                ),
                'cluster_size' => array(
                    '40',
                    '50',
                    '60',
                    '70',
                    '80'
                ), 
            );
            
            return $cluster_values[$type];
        }
        
        /**
         * Create a dropdown for the marker cluster options.
         *
         * @since 1.2.20
         * @param  string $type     The cluster option type
         * @return string $dropdown The html for the distance option list
         */
		public function show_cluster_options( $type ) {
            
            global $wpsl_settings;
            
			$cluster_options = array(
                'cluster_zoom' => array(
                    'id'      => 'wpsl-marker-zoom',
                    'name'    => 'cluster_zoom',
                    'options' => $this->get_default_cluster_option( $type )
                 ),
                'cluster_size' => array(
                    'id'      => 'wpsl-marker-cluster-size',
                    'name'    => 'cluster_size',
                    'options' => $this->get_default_cluster_option( $type )
                ),
			);
            
			$dropdown = '<select id="' . esc_attr( $cluster_options[$type]['id'] ) . '" name="wpsl_map[' . esc_attr( $cluster_options[$type]['name'] ) . ']" autocomplete="off">';
			
            $i = 0;
			foreach ( $cluster_options[$type]['options'] as $item => $value ) {
				$selected = ( $wpsl_settings[$type] == $value ) ? 'selected="selected"' : '';
                
                if ( $i == 0 ) {
                    $dropdown .= "<option value='0' $selected>" . __( 'Default', 'wpsl' ) . "</option>";
                } else {
                    $dropdown .= "<option value=". absint( $value ) . " $selected>" . absint( $value ) . "</option>";
                }
                    
                $i++;
			}
			
			$dropdown .= "</select>";
			
			return $dropdown;			
		}
        
        /**
         * Show the options of the start and store markers.
         *
         * @since 1.0.0
         * @return string $marker_list The complete list of available and selected markers
         */
        public function show_marker_options() {

            $marker_list      = '';
            $marker_images    = $this->get_available_markers();
            $marker_locations = array( 
                'start', 
                'store' 
            );

            foreach ( $marker_locations as $location ) {
                if ( $location == 'start' ) {
                    $marker_list .= __( 'Start location marker', 'wpsl' ) . ':';
                } else  {
                    $marker_list .= __( 'Store location marker', 'wpsl' ) . ':'; 
                }

                if ( !empty( $marker_images ) ) {
                    $marker_list .= '<ul class="wpsl-marker-list">';

                    foreach ( $marker_images as $marker_img ) {
                        $marker_list .= $this->create_marker_html( $marker_img, $location );
                    }

                    $marker_list .= '</ul>';
                }
            }

            return $marker_list;
        }

        /**
         * Load the markers that are used on the map.
         *
         * @since 1.0.0
         * @return array $marker_images A list of all the available markers.
         */
        public function get_available_markers() {
            
            $marker_images = array();
            $dir           = apply_filters( 'wpsl_admin_marker_dir', WPSL_PLUGIN_DIR . 'img/markers/' );
            
            if ( is_dir( $dir ) ) {
                if ( $dh = opendir( $dir ) ) {
                    while ( false !== ( $file = readdir( $dh ) ) ) {
                        if ( $file == '.' || $file == '..' || ( strpos( $file, '@2x' ) !== false ) ) continue;
                        $marker_images[] = $file;
                    }

                    closedir( $dh );
                }
            }
            
            return $marker_images;
        }
        
        /**
         * Show a list of available templates.
         *
         * @since 1.2.20
         * @return string $dropdown The html for the template option list
         */
        public function show_template_options() {
            
            global $wpsl_settings;
            
			$dropdown = '<select id="wpsl-store-template" name="wpsl_ux[template_id]" autocomplete="off">';

            foreach ( wpsl_get_templates() as $template ) {
                $template_id = ( isset( $template['id'] ) ) ? $template['id'] : '';
                
				$selected = ( $wpsl_settings['template_id'] == $template_id ) ? ' selected="selected"' : '';
				$dropdown .= "<option value='" . esc_attr( $template_id ) . "' $selected>" . esc_html( $template['name'] ) . "</option>";
            }
			
			$dropdown .= '</select>';
			
			return $dropdown;            
        }
        
        /**
         * Create dropdown lists.
         * 
         * @since 2.0.0
         * @param  string $type     The type of dropdown
         * @return string $dropdown The html output for the dropdown
         */
        public function create_dropdown( $type ) {
            
            global $wpsl_settings;
            
			$dropdown_lists = apply_filters( 'wpsl_setting_dropdowns', array(
                'hour_input' => array(
                    'values' => array(
                        'textarea' => __( 'Textarea', 'wpsl' ), 
                        'dropdown' => __( 'Dropdowns (recommended)', 'wpsl' )
                     ),
                    'id'       => 'wpsl-editor-hour-input',
                    'name'     => 'wpsl_editor[hour_input]',
                    'selected' => $wpsl_settings['editor_hour_input']
                ),
                'marker_effects' => array(
                    'values' => array(
                        'bounce'      => __( 'Bounces up and down', 'wpsl' ),
                        'info_window' => __( 'Will open the info window', 'wpsl' ),
                        'ignore'      => __( 'Does not respond', 'wpsl' )
                    ),
                    'id'       => 'wpsl-marker-effect',
                    'name'     => 'wpsl_ux[marker_effect]',
                    'selected' => $wpsl_settings['marker_effect']
                ),
                'more_info' => array(
                    'values' => array(
                        'store listings' => __( 'In the store listings', 'wpsl' ),
                        'info window'    => __( 'In the info window on the map', 'wpsl' )
                    ),
                    'id'       => 'wpsl-more-info-list',
                    'name'     => 'wpsl_ux[more_info_location]',
                    'selected' => $wpsl_settings['more_info_location']
                ),
                'map_types' => array(
                    'values'   => wpsl_get_map_types(),
                    'id'       => 'wpsl-map-type',
                    'name'     => 'wpsl_map[type]',
                    'selected' => $wpsl_settings['map_type']
                ),
                'editor_map_types' => array(
                    'values'   => wpsl_get_map_types(),
                    'id'       => 'wpsl-editor-map-type',
                    'name'     => 'wpsl_editor[map_type]',
                    'selected' => $wpsl_settings['editor_map_type']
                ),
                'max_zoom_level' => array(
                    'values'   => wpsl_get_max_zoom_levels(),
                    'id'       => 'wpsl-max-auto-zoom',
                    'name'     => 'wpsl_map[max_auto_zoom]',
                    'selected' => $wpsl_settings['auto_zoom_level']
                ),
                'address_format' => array(
                    'values'   => wpsl_get_address_formats(),
                    'id'       => 'wpsl-address-format',
                    'name'     => 'wpsl_ux[address_format]',
                    'selected' => $wpsl_settings['address_format']
                ),
                'filter_types' => array(
                    'values' => array(
                        'dropdown'   => __( 'Dropdown', 'wpsl' ), 
                        'checkboxes' => __( 'Checkboxes', 'wpsl' )
                     ),
                    'id'       => 'wpsl-cat-filter-types',
                    'name'     => 'wpsl_search[category_filter_type]',
                    'selected' => $wpsl_settings['category_filter_type']
                )
            ) );
                        
			$dropdown = '<select id="' . esc_attr( $dropdown_lists[$type]['id'] ) . '" name="' . esc_attr( $dropdown_lists[$type]['name'] ) . '" autocomplete="off">';
			
			foreach ( $dropdown_lists[$type]['values'] as $key => $value ) {
				$selected = ( $key == $dropdown_lists[$type]['selected'] ) ? 'selected="selected"' : '';
				$dropdown .= "<option value='" . esc_attr( $key ) . "' $selected>" . esc_html( $value ) . "</option>";
			}
			
			$dropdown .= '</select>';
			
			return $dropdown;			
		}
        
        /**
         * Create a dropdown for the 12/24 opening hours format.
         * 
         * @since 2.0.0
         * @param  string $hour_format The hour format that should be set to selected
         * @return string $dropdown    The html for the dropdown
         */
        public function show_opening_hours_format( $hour_format = '' ) {
            
            global $wpsl_settings;
            
			$items = array( 
                '12' => __( '12 Hours', 'wpsl' ),
                '24' => __( '24 Hours', 'wpsl' )
            );
            
            if ( !absint( $hour_format ) ) {
                $hour_format = $wpsl_settings['editor_hour_format'];
            } 
            
			$dropdown = '<select id="wpsl-editor-hour-format" name="wpsl_editor[hour_format]" autocomplete="off">';
			
			foreach ( $items as $key => $value ) {
				$selected = ( $hour_format == $key ) ? 'selected="selected"' : '';
				$dropdown .= "<option value='$key' $selected>" . esc_html( $value ) . "</option>";
			}
			
			$dropdown .= '</select>';
			
			return $dropdown;			
		}
    }
}