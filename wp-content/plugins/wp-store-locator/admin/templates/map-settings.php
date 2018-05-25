<?php
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb, $wpsl, $wpsl_admin, $wp_version, $wpsl_settings;
?>

<div id="wpsl-wrap" class="wrap wpsl-settings <?php if ( floatval( $wp_version ) < 3.8 ) { echo 'wpsl-pre-38'; } // Fix CSS issue with < 3.8 versions ?>">
	<h2>WP Store Locator <?php _e( 'Settings', 'wpsl' ); ?></h2>

    <?php
    settings_errors();

    $tabs          = apply_filters( 'wpsl_settings_tab', array( 'general' => __( 'General', 'wpsl' ) ) );
    $wpsl_licenses = apply_filters( 'wpsl_license_settings', array() );
    $current_tab   = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

    if ( $wpsl_licenses ) {
        $tabs['licenses'] = __( 'Licenses', 'wpsl' );
    }

    // Default to the general tab if an unknow tab value is set
    if ( !array_key_exists( $current_tab, $tabs ) ) {
        $current_tab = 'general';
    }

    if ( count( $tabs ) > 1 ) {
        echo '<h2 id="wpsl-tabs" class="nav-tab-wrapper">';

        foreach ( $tabs as $tab_key => $tab_name ) {
            if ( !$current_tab && $tab_key == 'general' || $current_tab == $tab_key ) {
                $active_tab = 'nav-tab-active';
            } else {
                $active_tab = '';
            }

            echo '<a class="nav-tab ' . $active_tab . '" title="' . esc_attr( $tab_name ) . '" href="' . admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_settings&tab=' . $tab_key ) . '">' . esc_attr( $tab_name ) . '</a>';
        }

        echo '</h2>';
    }
        
    if ( $wpsl_licenses && $current_tab == 'licenses' ) {
        ?>

        <form action="" method="post">
            <table class="wp-list-table widefat">
                <thead>
                    <tr>
                        <th scope="col"><?php _e( 'Add-On', 'wpsl' ); ?></th>
                        <th scope="col"><?php _e( 'License Key', 'wpsl' ); ?></th>
                        <th scope="col"><?php _e( 'License Expiry Date', 'wpsl' ); ?></th>
                    </tr>
                </thead>
                <tbody id="the-list">
                    <?php
                    foreach ( $wpsl_licenses as $wpsl_license ) {
                        $key = ( $wpsl_license['status'] == 'valid' ) ? esc_attr( $wpsl_license['key'] ) : '';
                        
                        echo '<tr>';
                        echo '<td>' . esc_html( $wpsl_license['name'] ) . '</td>';
                        echo '<td>';
                        echo '<input type="text" value="' . $key . '" name="wpsl_licenses[' . esc_attr( $wpsl_license['short_name'] ) . ']" />';
                        
                        if ( $wpsl_license['status'] == 'valid' ) {
                           echo '<input type="submit" class="button-secondary" name="' . esc_attr( $wpsl_license['short_name'] ) . '_license_key_deactivate" value="' . __( 'Deactivate License',  'wpsl' ) . '"/>';
                        }
                        
                        wp_nonce_field( $wpsl_license['short_name'] . '_license-nonce', $wpsl_license['short_name'] . '_license-nonce' );
                        
                        echo '</td>';
                        echo '<td>';
                        
                        if ( $wpsl_license['expiration'] && $wpsl_license['status'] == 'valid' ) {
                            echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $wpsl_license['expiration'] ) ) );
                        }
                        
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>

            <p class="submit">
                <input type="submit" value="<?php _e( 'Save Changes', 'wpsl' ); ?>" class="button button-primary" id="submit" name="submit">
            </p>
        </form>
    <?php
    } else if ( $current_tab == 'general' || !$current_tab ) {
    ?>
    
    <div id="general">
        <form id="wpsl-settings-form" method="post" action="options.php" autocomplete="off" accept-charset="utf-8">
            <div class="postbox-container">
                <div class="metabox-holder">
                    <div id="wpsl-api-settings" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Google Maps API', 'wpsl' ); ?></span></h3>
                        <div class="inside">
                            <p>
                                <label for="wpsl-api-server-key"><?php _e( 'Server key', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'A %sserver key%s allows you to monitor the usage of the Google Maps %sGeocoding API%s. %s %sRequired%s for %sapplications%s created after June 22, 2016.', 'wpsl' ), '<a href="https://wpstorelocator.co/document/create-google-api-keys/#server-key" target="_blank">', '</a>', '<a href="https://developers.google.com/maps/documentation/geocoding/intro">', '</a>', '<br><br>', '<strong>', '</strong>', '<a href="https://googlegeodevelopers.blogspot.nl/2016/06/building-for-scale-updates-to-google.html">', '</a>' ); ?></span></span></label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl_settings['api_server_key'] ); ?>" name="wpsl_api[server_key]"  class="textinput<?php if ( !get_option( 'wpsl_valid_server_key' ) ) { echo ' wpsl-validate-me wpsl-error'; } ?>" id="wpsl-api-server-key">
                            </p>
                            <p>
                                <label for="wpsl-api-browser-key"><?php _e( 'Browser key', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'A %sbrowser key%s allows you to monitor the usage of the Google Maps %sJavaScript API%s. %s %sRequired%s for %sapplications%s created after June 22, 2016.', 'wpsl' ), '<a href="https://wpstorelocator.co/document/create-google-api-keys/#browser-key" target="_blank">', '</a>', '<a href="https://developers.google.com/maps/documentation/javascript/">', '</a>', '<br><br>', '<strong>', '</strong>', '<a href="https://googlegeodevelopers.blogspot.nl/2016/06/building-for-scale-updates-to-google.html">', '</a>' ); ?></span></span></label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl_settings['api_browser_key'] ); ?>" name="wpsl_api[browser_key]" class="textinput" id="wpsl-api-browser-key">
                            </p>
                            <p>
                                <label for="wpsl-api-language"><?php _e( 'Map language', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php _e( 'If no map language is selected the browser\'s prefered language is used.', 'wpsl' ); ?></span></span></label> 
                                <select id="wpsl-api-language" name="wpsl_api[language]">
                                    <?php echo $wpsl_admin->settings_page->get_api_option_list( 'language' ); ?>          	
                                </select>
                            </p>
                            <p>
                                <label for="wpsl-api-region"><?php _e( 'Map region', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'This will bias the %sgeocoding%s results towards the selected region. %s If no region is selected the bias is set to the United States.', 'wpsl' ), '<a href="https://developers.google.com/maps/documentation/javascript/geocoding#Geocoding">', '</a>', '<br><br>' ); ?></span></span></label> 
                                <select id="wpsl-api-region" name="wpsl_api[region]">
                                    <?php echo $wpsl_admin->settings_page->get_api_option_list( 'region' ); ?>
                                </select>
                            </p>
                            <p id="wpsl-geocode-component" <?php if ( !$wpsl_settings['api_region'] ) { echo 'style="display:none;"'; } ?>>
                                <label for="wpsl-api-component"><?php _e( 'Restrict the geocoding results to the selected map region?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'If the %sgeocoding%s API finds more relevant results outside of the set map region ( some location names exist in multiple regions ), the user will likely see a "No results found" message. %s To rule this out you can restrict the results to the set map region. %s You can modify the used restrictions with %sthis%s filter.', 'wpsl' ), '<a href="https://developers.google.com/maps/documentation/javascript/geocoding#Geocoding">', '</a>', '<br><br>', '<br><br>', '<a href="http://wpstorelocator.co/document/wpsl_geocode_components">', '</a>' ); ?></span></span></label> 
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['api_geocode_component'], true ); ?> name="wpsl_api[geocode_component]" id="wpsl-api-component">
                            </p>
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'wpsl' ); ?>" class="button-primary">
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="postbox-container wpsl-search-settings">
                <div class="metabox-holder">
                    <div id="wpsl-search-settings" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Search', 'wpsl' ); ?></span></h3>
                        <div class="inside">
                            <p>
                                <label for="wpsl-search-autocomplete"><?php _e( 'Enable autocomplete?', 'wpsl' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['autocomplete'], true ); ?> name="wpsl_search[autocomplete]" id="wpsl-search-autocomplete">
                            </p>
                            <p>
                                <label for="wpsl-results-dropdown"><?php _e( 'Show the max results dropdown?', 'wpsl' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['results_dropdown'], true ); ?> name="wpsl_search[results_dropdown]" id="wpsl-results-dropdown">
                            </p>
                            <p>
                                <label for="wpsl-radius-dropdown"><?php _e( 'Show the search radius dropdown?', 'wpsl' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['radius_dropdown'], true ); ?> name="wpsl_search[radius_dropdown]" id="wpsl-radius-dropdown">
                            </p>
                            <p>
                                <label for="wpsl-category-filters"><?php _e( 'Enable category filters?', 'wpsl' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['category_filter'], true ); ?> name="wpsl_search[category_filter]" id="wpsl-category-filters" class="wpsl-has-conditional-option">
                            </p>
                            <div class="wpsl-conditional-option" <?php if ( !$wpsl_settings['category_filter'] ) { echo 'style="display:none;"'; } ?>>
                                <p>
                                    <label for="wpsl-cat-filter-types"><?php _e( 'Filter type:', 'wpsl' ); ?></label>
                                    <?php echo $wpsl_admin->settings_page->create_dropdown( 'filter_types' ); ?>           
                                </p>
                            </div>
                            <p>
                                <label for="wpsl-distance-unit"><?php _e( 'Distance unit', 'wpsl' ); ?>:</label>                          
                                <span class="wpsl-radioboxes">
                                    <input type="radio" autocomplete="off" value="km" <?php checked( 'km', $wpsl_settings['distance_unit'] ); ?> name="wpsl_search[distance_unit]" id="wpsl-distance-km">
                                    <label for="wpsl-distance-km"><?php _e( 'km', 'wpsl' ); ?></label>
                                    <input type="radio" autocomplete="off" value="mi" <?php checked( 'mi', $wpsl_settings['distance_unit'] ); ?> name="wpsl_search[distance_unit]" id="wpsl-distance-mi">
                                    <label for="wpsl-distance-mi"><?php _e( 'mi', 'wpsl' ); ?></label>
                                </span>
                            </p>
                            <p>
                                <label for="wpsl-max-results"><?php _e( 'Max search results', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php _e( 'The default value is set between the [ ].', 'wpsl' ); ?></span></span></label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl_settings['max_results'] ); ?>" name="wpsl_search[max_results]" class="textinput" id="wpsl-max-results">
                            </p>
                            <p>
                                <label for="wpsl-search-radius"><?php _e( 'Search radius options', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php _e( 'The default value is set between the [ ].', 'wpsl' ); ?></span></span></label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl_settings['search_radius'] ); ?>" name="wpsl_search[radius]" class="textinput" id="wpsl-search-radius">
                            </p>
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'wpsl' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>

            <div class="postbox-container">
                <div class="metabox-holder">
                    <div id="wpsl-map-settings" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Map', 'wpsl' ); ?></span></h3>
                        <div class="inside">
                            <p>
                                <label for="wpsl-auto-locate"><?php _e( 'Attempt to auto-locate the user', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'Most modern browsers %srequire%s a HTTPS connection before the Geolocation feature works.', 'wpsl' ), '<a href="https://wpstorelocator.co/document/html-5-geolocation-not-working/">', '</a>' ); ?></span></span></label>
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['auto_locate'], true ); ?> name="wpsl_map[auto_locate]" id="wpsl-auto-locate">
                            </p>
                            <p>
                                <label for="wpsl-autoload"><?php _e( 'Load locations on page load', 'wpsl' ); ?>:</label> 
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['autoload'], true ); ?> name="wpsl_map[autoload]" id="wpsl-autoload" class="wpsl-has-conditional-option">
                            </p>
                            <div class="wpsl-conditional-option" <?php if ( !$wpsl_settings['autoload'] ) { echo 'style="display:none;"'; } ?>>
                                <p>
                                    <label for="wpsl-autoload-limit"><?php _e( 'Number of locations to show', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'Although the location data is cached after the first load, a lower number will result in the map being more responsive. %s If this field is left empty or set to 0, then all locations are loaded.', 'wpsl' ), '<br><br>' ); ?></span></span></label>
                                    <input type="text" value="<?php echo esc_attr( $wpsl_settings['autoload_limit'] ); ?>" name="wpsl_map[autoload_limit]" class="textinput" id="wpsl-autoload-limit">
                                </p>
                            </div>
                            <p>
                                <label for="wpsl-start-name"><?php _e( 'Start point', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( '%sRequired field.%s %s If auto-locating the user is disabled or fails, the center of the provided city or country will be used as the initial starting point for the user.', 'wpsl' ), '<strong>', '</strong>', '<br><br>' ); ?></span></span></label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl_settings['start_name'] ); ?>" name="wpsl_map[start_name]" class="textinput" id="wpsl-start-name">
                                <input type="hidden" value="<?php echo esc_attr( $wpsl_settings['start_latlng'] ); ?>" name="wpsl_map[start_latlng]" id="wpsl-latlng" />
                            </p>
                            <p>
                                <label for="wpsl-run-fitbounds"><?php _e( 'Auto adjust the zoom level to make sure all markers are visible?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php _e( 'This runs after a search is made, and makes sure all the returned locations are visible in the viewport.', 'wpsl' ); ?></span></span></label>
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['run_fitbounds'], true ); ?> name="wpsl_map[run_fitbounds]" id="wpsl-run-fitbounds">
                            </p>
                            <p>
                                <label for="wpsl-zoom-level"><?php _e( 'Initial zoom level', 'wpsl' ); ?>:</label> 
                                <?php echo $wpsl_admin->settings_page->show_zoom_levels(); ?>
                            </p>
                            <p>
                                <label for="wpsl-max-zoom-level"><?php _e( 'Max auto zoom level', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'This value sets the zoom level for the "Zoom here" link in the info window. %s It is also used to limit the zooming when the viewport of the map is changed to make all the markers fit on the screen.', 'wpsl' ), '<br><br>' ); ?></span></span></label> 
                                <?php echo $wpsl_admin->settings_page->create_dropdown( 'max_zoom_level' ); ?>
                            </p> 
                            <p>
                                <label for="wpsl-streetview"><?php _e( 'Show the street view controls?', 'wpsl' ); ?></label> 
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['streetview'], true ); ?> name="wpsl_map[streetview]" id="wpsl-streetview">
                            </p>
                            <p>
                                <label for="wpsl-type-control"><?php _e( 'Show the map type control?', 'wpsl' ); ?></label> 
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['type_control'], true ); ?> name="wpsl_map[type_control]" id="wpsl-type-control">
                            </p>
                            <p>
                                <label for="wpsl-scollwheel-zoom"><?php _e( 'Enable scroll wheel zooming?', 'wpsl' ); ?></label> 
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['scrollwheel'], true ); ?> name="wpsl_map[scrollwheel]" id="wpsl-scollwheel-zoom">
                            </p>
                            <p>
                                <label><?php _e( 'Zoom control position', 'wpsl' ); ?>:</label>
                                <span class="wpsl-radioboxes">
                                    <input type="radio" autocomplete="off" value="left" <?php checked( 'left', $wpsl_settings['control_position'], true ); ?> name="wpsl_map[control_position]" id="wpsl-control-left">
                                    <label for="wpsl-control-left"><?php _e( 'Left', 'wpsl' ); ?></label>
                                    <input type="radio" autocomplete="off" value="right" <?php checked( 'right', $wpsl_settings['control_position'], true ); ?> name="wpsl_map[control_position]" id="wpsl-control-right">
                                    <label for="wpsl-control-right"><?php _e( 'Right', 'wpsl' ); ?></label>
                                </span>
                            </p>
                            <p>
                                <label for="wpsl-map-type"><?php _e( 'Map type', 'wpsl' ); ?>:</label> 
                                <?php echo $wpsl_admin->settings_page->create_dropdown( 'map_types' ); ?>
                            </p>
                            <p>
                                <label for="wpsl-map-style"><?php _e( 'Map style', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php _e( 'Custom map styles only work if the map type is set to "Roadmap" or "Terrain".', 'wpsl' ); ?></span></span></label>
                            </p>
                            <div class="wpsl-style-input">
                                <p><?php echo sprintf( __( 'You can use existing map styles from %sSnazzy Maps%s or %sMap Stylr%s and paste it in the textarea below, or you can generate a custom map style through the %sMap Style Editor%s or %sStyled Maps Wizard%s.', 'wpsl' ), '<a target="_blank" href="http://snazzymaps.com">', '</a>', '<a target="_blank" href="http://mapstylr.com">', '</a>', '<a target="_blank" href="http://mapstylr.com/map-style-editor/">', '</a>', '<a target="_blank" href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html">', '</a>' ); ?></p> 
                                <p><?php echo sprintf( __( 'If you like to write the style code yourself, then you can find the documentation from Google %shere%s.', 'wpsl' ), '<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/styling">', '</a>' ); ?></p>
                                <textarea id="wpsl-map-style" name="wpsl_map[map_style]"><?php echo strip_tags( stripslashes( json_decode( $wpsl_settings['map_style'] ) ) ); ?></textarea>
                                <input type="submit" value="<?php _e( 'Preview Map Style', 'wpsl' ); ?>" class="button-primary" name="wpsl-style-preview" id="wpsl-style-preview">
                            </div>
                            <div id="wpsl-gmap-wrap" class="wpsl-styles-preview"></div>
                            <p>
                               <label for="wpsl-show-credits"><?php _e( 'Show credits?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php _e( 'This will place a "Search provided by WP Store Locator" backlink below the map.', 'wpsl' ); ?></span></span></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['show_credits'], true ); ?> name="wpsl_credits" id="wpsl-show-credits">
                            </p>
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'wpsl' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>

            <div class="postbox-container">
                <div class="metabox-holder">
                    <div id="wpsl-user-experience" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'User Experience', 'wpsl' ); ?></span></h3>
                        <div class="inside">
                            <p>
                                <label for="wpsl-design-height"><?php _e( 'Store Locator height', 'wpsl' ); ?>:</label> 
                                <input size="3" value="<?php echo esc_attr( $wpsl_settings['height'] ); ?>" id="wpsl-design-height" name="wpsl_ux[height]"> px
                            </p> 
                            <p>
                                <label for="wpsl-infowindow-width"><?php _e( 'Max width for the info window content', 'wpsl' ); ?>:</label> 
                                <input size="3" value="<?php echo esc_attr( $wpsl_settings['infowindow_width'] ); ?>" id="wpsl-infowindow-width" name="wpsl_ux[infowindow_width]"> px
                            </p>
                            <p>
                                <label for="wpsl-search-width"><?php _e( 'Search field width', 'wpsl' ); ?>:</label> 
                                <input size="3" value="<?php echo esc_attr( $wpsl_settings['search_width'] ); ?>" id="wpsl-search-width" name="wpsl_ux[search_width]"> px
                            </p>
                            <p>
                                <label for="wpsl-label-width"><?php _e( 'Search and radius label width', 'wpsl' ); ?>:</label> 
                                <input size="3" value="<?php echo esc_attr( $wpsl_settings['label_width'] ); ?>" id="wpsl-label-width" name="wpsl_ux[label_width]"> px
                            </p> 
                            <p>
                               <label for="wpsl-store-template"><?php _e( 'Store Locator template', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'The selected template is used with the [wpsl] shortcode. %s You can add a custom template with the %swpsl_templates%s filter.', 'wpsl' ), '<br><br>', '<a href="http://wpstorelocator.co/document/wpsl_templates/">', '</a>' ); ?></span></span></label> 
                               <?php echo $wpsl_admin->settings_page->show_template_options(); ?>
                            </p>
                            <p id="wpsl-listing-below-no-scroll" <?php if ( $wpsl_settings['template_id'] != 'below_map' ) { echo 'style="display:none;"'; } ?>>
                                <label for="wpsl-more-info-list"><?php _e( 'Hide the scrollbar?', 'wpsl' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['listing_below_no_scroll'], true ); ?> name="wpsl_ux[listing_below_no_scroll]" id="wpsl-listing-below-no-scroll">
                            </p>
                            <p>
                               <label for="wpsl-new-window"><?php _e( 'Open links in a new window?', 'wpsl' ); ?></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['new_window'], true ); ?> name="wpsl_ux[new_window]" id="wpsl-new-window">
                            </p>
                            <p>
                               <label for="wpsl-reset-map"><?php _e( 'Show a reset map button?', 'wpsl' ); ?></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['reset_map'], true ); ?> name="wpsl_ux[reset_map]" id="wpsl-reset-map">
                            </p> 
                            <p>
                               <label for="wpsl-direction-redirect"><?php _e( 'When a user clicks on "Directions", open a new window, and show the route on google.com/maps ?', 'wpsl' ); ?></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['direction_redirect'], true ); ?> name="wpsl_ux[direction_redirect]" id="wpsl-direction-redirect">
                            </p>
                            <p>
                               <label for="wpsl-more-info"><?php _e( 'Show a "More info" link in the store listings?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'This places a "More Info" link below the address and will show the phone, fax, email, opening hours and description once the link is clicked.', 'wpsl' ) ); ?></span></span></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['more_info'], true ); ?> name="wpsl_ux[more_info]" id="wpsl-more-info" class="wpsl-has-conditional-option">
                            </p>   
                            <div class="wpsl-conditional-option" <?php if ( !$wpsl_settings['more_info'] ) { echo 'style="display:none;"'; } ?>>
                                <p>
                                    <label for="wpsl-more-info-list"><?php _e( 'Where do you want to show the "More info" details?', 'wpsl' ); ?></label>
                                    <?php echo $wpsl_admin->settings_page->create_dropdown( 'more_info' ); ?>
                                </p>
                            </div>
                            <p>
                               <label for="wpsl-contact-details"><?php _e( 'Always show the contact details below the address in the search results?', 'wpsl' ); ?></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['show_contact_details'], true ); ?> name="wpsl_ux[show_contact_details]" id="wpsl-contact-details">
                            </p>
                            <p>
                                <label for="wpsl-clickable-contact-details"><?php _e( 'Make the contact details always clickable?', 'wpsl' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['clickable_contact_details'], true ); ?> name="wpsl_ux[clickable_contact_details]" id="wpsl-clickable-contact-details">
                            </p>
                            <p>
                               <label for="wpsl-store-url"><?php _e( 'Make the store name clickable if a store URL exists?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'If %spermalinks%s are enabled, the store name will always link to the store page.', 'wpsl' ), '<a href="' . admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_settings#wpsl-permalink-settings' ) . '">', '</a>' ); ?></span></span></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['store_url'], true ); ?> name="wpsl_ux[store_url]" id="wpsl-store-url">
                            </p>
                            <p>
                               <label for="wpsl-phone-url"><?php _e( 'Make the phone number clickable on mobile devices?', 'wpsl' ); ?></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['phone_url'], true ); ?> name="wpsl_ux[phone_url]" id="wpsl-phone-url">
                            </p>
                            <p>
                               <label for="wpsl-marker-streetview"><?php _e( 'If street view is available for the current location, then show a "Street view" link in the info window?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'Enabling this option can sometimes result in a small delay in the opening of the info window. %s This happens because an API request is made to Google Maps to check if street view is available for the current location.', 'wpsl' ), '<br><br>' ); ?></span></span></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['marker_streetview'], true ); ?> name="wpsl_ux[marker_streetview]" id="wpsl-marker-streetview">
                            </p>
                            <p>
                               <label for="wpsl-marker-zoom-to"><?php _e( 'Show a "Zoom here" link in the info window?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'Clicking this link will make the map zoom in to the %s max auto zoom level %s.', 'wpsl' ), '<a href="#wpsl-zoom-level">', '</a>' ); ?></span></span></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['marker_zoom_to'], true ); ?> name="wpsl_ux[marker_zoom_to]" id="wpsl-marker-zoom-to">
                            </p>
                            <p>
                               <label for="wpsl-mouse-focus"><?php _e( 'On page load move the mouse cursor to the search field?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'If the store locator is not placed at the top of the page, enabling this feature can result in the page scrolling down. %s %sThis option is disabled on mobile devices.%s', 'wpsl' ), '<br><br>', '<em>', '</em>' ); ?></span></span></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['mouse_focus'], true ); ?> name="wpsl_ux[mouse_focus]" id="wpsl-mouse-focus">
                            </p>
                            <p>
                               <label for="wpsl-infowindow-style"><?php _e( 'Use the default style for the info window?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'If the default style is disabled the %sInfoBox%s library will be used instead. %s This enables you to easily change the look and feel of the info window through the .wpsl-infobox css class.', 'wpsl' ), '<a href="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/docs/reference.html" target="_blank">', '</a>', '<br><br>' ); ?></span></span></label> 
                               <input type="checkbox" value="default" <?php checked( $wpsl_settings['infowindow_style'], 'default' ); ?> name="wpsl_ux[infowindow_style]" id="wpsl-infowindow-style">
                            </p>
                            <p>
                               <label for="wpsl-hide-country"><?php _e( 'Hide the country in the search results?', 'wpsl' ); ?></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['hide_country'], true ); ?> name="wpsl_ux[hide_country]" id="wpsl-hide-country">
                            </p>
                            <p>
                               <label for="wpsl-hide-distance"><?php _e( 'Hide the distance in the search results?', 'wpsl' ); ?></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['hide_distance'], true ); ?> name="wpsl_ux[hide_distance]" id="wpsl-hide-distance">
                            </p>
                            <p>
                               <label for="wpsl-bounce"><?php _e( 'If a user hovers over the search results the store marker', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'If marker clusters are enabled this option will not work as expected as long as the markers are clustered. %s The bouncing of the marker won\'t be visible at all unless a user zooms in far enough for the marker cluster to change back in to individual markers. %s The info window will open as expected, but it won\'t be clear to which marker it belongs to. ', 'wpsl' ), '<br><br>' , '<br><br>' ); ?></span></span></label> 
                               <?php echo $wpsl_admin->settings_page->create_dropdown( 'marker_effects' ); ?>
                            </p>  
                            <p>
                                <label for="wpsl-address-format"><?php _e( 'Address format', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'You can add custom address formats with the %swpsl_address_formats%s filter.', 'wpsl' ), '<a href="http://wpstorelocator.co/document/wpsl_address_formats/">', '</a>' ); ?></span></span></label> 
                               <?php echo $wpsl_admin->settings_page->create_dropdown( 'address_format' ); ?>
                            </p>
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'wpsl' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>

            <div class="postbox-container">
                <div class="metabox-holder">
                    <div id="wpsl-marker-settings" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Markers', 'wpsl' ); ?></span></h3>
                        <div class="inside">
                            <?php echo $wpsl_admin->settings_page->show_marker_options(); ?>
                            <p>
                               <label for="wpsl-marker-clusters"><?php _e( 'Enable marker clusters?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php _e( 'Recommended for maps with a large amount of markers.', 'wpsl' ); ?></span></span></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['marker_clusters'], true ); ?> name="wpsl_map[marker_clusters]" id="wpsl-marker-clusters" class="wpsl-has-conditional-option">
                            </p>
                            <div class="wpsl-conditional-option" <?php if ( !$wpsl_settings['marker_clusters'] ) { echo 'style="display:none;"'; } ?>>
                                <p>
                                   <label for="wpsl-marker-zoom"><?php _e( 'Max zoom level', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php _e( 'If this zoom level is reached or exceeded, then all markers are moved out of the marker cluster and shown as individual markers.', 'wpsl' ); ?></span></span></label> 
                                   <?php echo $wpsl_admin->settings_page->show_cluster_options( 'cluster_zoom' ); ?>
                                </p>
                                <p>
                                   <label for="wpsl-marker-cluster-size"><?php _e( 'Cluster size', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'The grid size of a cluster in pixels. %s A larger number will result in a lower amount of clusters and also make the algorithm run faster.', 'wpsl' ), '<br><br>' ); ?></span></span></label> 
                                   <?php echo $wpsl_admin->settings_page->show_cluster_options( 'cluster_size' ); ?>
                                </p>
                            </div>
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'wpsl' ); ?>" class="button-primary">
                            </p>
                        </div>
                    </div>   
                </div>  
            </div>

            <div class="postbox-container">
                <div class="metabox-holder">
                    <div id="wpsl-store-editor-settings" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Store Editor', 'wpsl' ); ?></span></h3>
                        <div class="inside">
                            <p>
                                <label for="wpsl-editor-country"><?php _e( 'Default country', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'editor_country', '' ) ); ?>" name="wpsl_editor[default_country]" class="textinput" id="wpsl-editor-country">
                            </p>
                            <p>
                                <label for="wpsl-editor-map-type"><?php _e( 'Map type for the location preview', 'wpsl' ); ?>:</label> 
                                <?php echo $wpsl_admin->settings_page->create_dropdown( 'editor_map_types' ); ?>
                            </p>
                            <p>
                                <label for="wpsl-editor-hide-hours"><?php _e( 'Hide the opening hours?', 'wpsl' ); ?></label> 
                                <input type="checkbox" value="" <?php checked( $wpsl_settings['hide_hours'], true ); ?> name="wpsl_editor[hide_hours]" id="wpsl-editor-hide-hours" class="wpsl-has-conditional-option">
                            </p>
                            <div class="wpsl-conditional-option" <?php if ( $wpsl_settings['hide_hours'] ) { echo 'style="display:none"'; } ?>>
                                <?php if ( get_option( 'wpsl_legacy_support' ) ) { // Is only set for users who upgraded from 1.x ?>
                                <p>
                                    <label for="wpsl-editor-hour-input"><?php _e( 'Opening hours input type', 'wpsl' ); ?>:</label> 
                                    <?php echo $wpsl_admin->settings_page->create_dropdown( 'hour_input' ); ?>
                                </p>
                                <p class="wpsl-hour-notice <?php if ( $wpsl_settings['editor_hour_input'] !== 'dropdown' ) { echo 'style="display:none"'; } ?>">
                                    <em><?php echo sprintf( __( 'Opening hours created in version 1.x %sare not%s automatically converted to the new dropdown format.', 'wpsl' ), '<strong>', '</strong>' ); ?></em>
                                </p>
                                <div class="wpsl-textarea-hours" <?php if ( $wpsl_settings['editor_hour_input'] !== 'textarea' ) { echo 'style="display:none"'; } ?>>
                                    <p class="wpsl-default-hours"><strong><?php _e( 'The default opening hours', 'wpsl' ); ?></strong></p>
                                    <textarea rows="5" cols="5" name="wpsl_editor[textarea]" id="wpsl-textarea-hours"><?php if ( isset( $wpsl_settings['editor_hours']['textarea'] ) ) { echo esc_textarea( stripslashes( $wpsl_settings['editor_hours']['textarea'] ) ); } ?></textarea>
                                </div>
                                <?php } ?>
                                <div class="wpsl-dropdown-hours" <?php if ( $wpsl_settings['editor_hour_input'] !== 'dropdown' ) { echo 'style="display:none"'; } ?>>
                                    <p>
                                        <label for="wpsl-editor-hour-format"><?php _e( 'Opening hours format', 'wpsl' ); ?>:</label> 
                                        <?php echo $wpsl_admin->settings_page->show_opening_hours_format(); ?>
                                    </p>
                                    <p class="wpsl-default-hours"><strong><?php _e( 'The default opening hours', 'wpsl' ); ?></strong></p>
                                    <?php echo $wpsl_admin->metaboxes->opening_hours( 'settings' ); ?>
                                </div>
                            </div>
                            <p><em><?php _e( 'The default country and opening hours are only used when a new store is created. So changing the default values will have no effect on existing store locations.', 'wpsl' ); ?></em></p>

                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'wpsl' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>

            <div class="postbox-container">
                <div class="metabox-holder">
                    <div id="wpsl-permalink-settings" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Permalink', 'wpsl' ); ?></span></h3>
                        <div class="inside">
                            <p>
                               <label for="wpsl-permalinks-active"><?php _e( 'Enable permalink?', 'wpsl' ); ?></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['permalinks'], true ); ?> name="wpsl_permalinks[active]" id="wpsl-permalinks-active" class="wpsl-has-conditional-option">
                            </p>
                            <div class="wpsl-conditional-option" <?php if ( !$wpsl_settings['permalinks'] ) { echo 'style="display:none;"'; } ?>>
                                <p>
                                    <label for="wpsl-permalinks-slug"><?php _e( 'Store slug', 'wpsl' ); ?>:</label> 
                                    <input type="text" value="<?php echo esc_attr( $wpsl_settings['permalink_slug'] ); ?>" name="wpsl_permalinks[slug]" class="textinput" id="wpsl-permalinks-slug">
                                </p>
                                <p>
                                    <label for="wpsl-category-slug"><?php _e( 'Category slug', 'wpsl' ); ?>:</label> 
                                    <input type="text" value="<?php echo esc_attr( $wpsl_settings['category_slug'] ); ?>" name="wpsl_permalinks[category_slug]" class="textinput" id="wpsl-category-slug">
                                </p>
                                <em><?php echo sprintf( __( 'The permalink slugs %smust be unique%s on your site.', 'wpsl' ), '<strong>', '</strong>' ); ?></em>
                            </div>
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'wpsl' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>

            <div class="postbox-container">
                <div class="metabox-holder">
                    <div id="wpsl-label-settings" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Labels', 'wpsl' ); ?></span></h3>
                        <div class="inside">
                            <?php
                            /*
                             * Show a msg to make sure that when a WPML compatible plugin 
                             * is active users use the 'String Translations' page to change the labels, 
                             * instead of the 'Label' section.
                             */
                            if ( $wpsl->i18n->wpml_exists() ) {
                                echo '<p>' . sprintf( __( '%sWarning!%s %sWPML%s, or a plugin using the WPML API is active.', 'wpsl' ), '<strong>', '</strong>', '<a href="https://wpml.org/">', '</a>' ) . '</p>';
                                echo '<p>' . __( 'Please use the "String Translations" section in the used multilingual plugin to change the labels. Changing them here will have no effect as long as the multilingual plugin remains active.', 'wpsl' ) . '</p>';
                            }
                            ?>
                            <p>
                                <label for="wpsl-search"><?php _e( 'Your location', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'search_label', __( 'Your location', 'wpsl' ) ) ); ?>" name="wpsl_label[search]" class="textinput" id="wpsl-search">
                            </p>
                            <p>
                                <label for="wpsl-search-radius"><?php _e( 'Search radius', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'radius_label', __( 'Search radius', 'wpsl' ) ) ); ?>" name="wpsl_label[radius]" class="textinput" id="wpsl-search-radius">
                            </p>
                            <p>
                                <label for="wpsl-no-results"><?php _e( 'No results found', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'no_results_label', __( 'No results found', 'wpsl' ) ) ); ?>" name="wpsl_label[no_results]" class="textinput" id="wpsl-no-results">
                            </p>
                            <p>
                                <label for="wpsl-search-btn"><?php _e( 'Search', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'search_btn_label', __( 'Search', 'wpsl' ) ) ); ?>" name="wpsl_label[search_btn]" class="textinput" id="wpsl-search-btn">
                            </p>
                            <p>
                                <label for="wpsl-preloader"><?php _e( 'Searching (preloader text)', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'preloader_label', __( 'Searching...', 'wpsl' ) ) ); ?>" name="wpsl_label[preloader]" class="textinput" id="wpsl-preloader">
                            </p>
                            <p>
                                <label for="wpsl-results"><?php _e( 'Results', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'results_label', __( 'Results', 'wpsl' ) ) ); ?>" name="wpsl_label[results]" class="textinput" id="wpsl-results">
                            </p>
                            <p>
                                <label for="wpsl-category"><?php _e( 'Category filter', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'category_label', __( 'Category', 'wpsl' ) ) ); ?>" name="wpsl_label[category]" class="textinput" id="wpsl-category">
                            </p>
                            <p>
                                <label for="wpsl-category-default"><?php _e( 'Category first item', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'category_default_label', __( 'Any', 'wpsl' ) ) ); ?>" name="wpsl_label[category_default]" class="textinput" id="wpsl-category-default">
                            </p>
                            <p>
                                <label for="wpsl-more-info"><?php _e( 'More info', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'more_label', __( 'More info', 'wpsl' ) ) ); ?>" name="wpsl_label[more]" class="textinput" id="wpsl-more-info">
                            </p>
                            <p>
                                <label for="wpsl-phone"><?php _e( 'Phone', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'phone_label', __( 'Phone', 'wpsl' ) ) ); ?>" name="wpsl_label[phone]" class="textinput" id="wpsl-phone">
                            </p>                        
                            <p>
                                <label for="wpsl-fax"><?php _e( 'Fax', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'fax_label', __( 'Fax', 'wpsl' ) ) ); ?>" name="wpsl_label[fax]" class="textinput" id="wpsl-fax">
                            </p>
                            <p>
                                <label for="wpsl-email"><?php _e( 'Email', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'email_label', __( 'Email', 'wpsl' ) ) ); ?>" name="wpsl_label[email]" class="textinput" id="wpsl-email">
                            </p>
                            <p>
                                <label for="wpsl-url"><?php _e( 'Url', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'url_label', __( 'Url', 'wpsl' ) ) ); ?>" name="wpsl_label[url]" class="textinput" id="wpsl-url">
                            </p>
                            <p>
                                <label for="wpsl-hours"><?php _e( 'Hours', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'hours_label', __( 'Hours', 'wpsl' ) ) ); ?>" name="wpsl_label[hours]" class="textinput" id="wpsl-hours">
                            </p>
                            <p>
                                <label for="wpsl-start"><?php _e( 'Start location', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'start_label', __( 'Start location', 'wpsl' ) ) ); ?>" name="wpsl_label[start]" class="textinput" id="wpsl-start">
                            </p>
                            <p>
                                <label for="wpsl-directions"><?php _e( 'Get directions', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'directions_label', __( 'Directions', 'wpsl' ) ) ); ?>" name="wpsl_label[directions]" class="textinput" id="wpsl-directions">
                            </p>
                            <p>
                                <label for="wpsl-no-directions"><?php _e( 'No directions found', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'no_directions_label', __( 'No route could be found between the origin and destination', 'wpsl' ) ) ); ?>" name="wpsl_label[no_directions]" class="textinput" id="wpsl-no-directions">
                            </p>
                            <p>
                                <label for="wpsl-back"><?php _e( 'Back', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'back_label', __( 'Back', 'wpsl' ) ) ); ?>" name="wpsl_label[back]" class="textinput" id="wpsl-back">
                            </p>
                            <p>
                                <label for="wpsl-street-view"><?php _e( 'Street view', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'street_view_label', __( 'Street view', 'wpsl' ) ) ); ?>" name="wpsl_label[street_view]" class="textinput" id="wpsl-street-view">
                            </p> 
                            <p>
                                <label for="wpsl-zoom-here"><?php _e( 'Zoom here', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'zoom_here_label', __( 'Zoom here', 'wpsl' ) ) ); ?>" name="wpsl_label[zoom_here]" class="textinput" id="wpsl-zoom-here">
                            </p>
                            <p>
                                <label for="wpsl-error"><?php _e( 'General error', 'wpsl' ); ?>:</label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'error_label', __( 'Something went wrong, please try again!', 'wpsl' ) ) ); ?>" name="wpsl_label[error]" class="textinput" id="wpsl-error">
                            </p>
                            <p>
                                <label for="wpsl-limit"><?php _e( 'Query limit error', 'wpsl' ); ?>:<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'You can raise the %susage limit%s by obtaining an API %skey%s, and fill in the "API key" field at the top of this page.', 'wpsl' ), '<a href="https://developers.google.com/maps/documentation/javascript/usage#usage_limits" target="_blank">', '</a>' ,'<a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank">', '</a>' ); ?></span></span></label> 
                                <input type="text" value="<?php echo esc_attr( $wpsl->i18n->get_translation( 'limit_label', __( 'API usage limit reached', 'wpsl' ) ) ); ?>" name="wpsl_label[limit]" class="textinput" id="wpsl-limit">
                            </p>
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'wpsl' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>

            <div class="postbox-container">
                <div class="metabox-holder">
                    <div id="wpsl-tools" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Tools', 'wpsl' ); ?></span></h3>
                        <div class="inside">
                            <p>
                               <label for="wpsl-debug"><?php _e( 'Enable store locator debug?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'This disables the WPSL transient cache. %sThe transient cache is only used if the %sLoad locations on page load%s option is enabled.', 'wpsl' ), '<br><br>', '<em>', '</em>' ); ?></span></span></label> 
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['debug'], true ); ?> name="wpsl_tools[debug]" id="wpsl-debug">
                            </p>
                            <p>
                               <label for="wpsl-deregister-gmaps"><?php _e( 'Enable compatibility mode?', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'If the %sbrowser console%s shows the error below, then enabling this option should fix it. %s %sYou have included the Google Maps API multiple times on this page. This may cause unexpected errors.%s %s This error can in some situations break the store locator map.', 'wpsl' ), '<a href="https://codex.wordpress.org/Using_Your_Browser_to_Diagnose_JavaScript_Errors#Step_3:_Diagnosis">', '</a>', '<br><br>', '<em>', '</em>', '<br><br>' ); ?></span></span></label>
                               <input type="checkbox" value="" <?php checked( $wpsl_settings['deregister_gmaps'], true ); ?> name="wpsl_tools[deregister_gmaps]" id="wpsl-deregister-gmaps">
                            </p>
                            <p>
                               <label for="wpsl-transient"><?php _e( 'WPSL transients', 'wpsl' ); ?></label> 
                               <a class="button" href="<?php echo wp_nonce_url( admin_url( "edit.php?post_type=wpsl_stores&page=wpsl_settings&action=clear_wpsl_transients" ), 'clear_transients' ); ?>"><?php _e( 'Clear store locator transient cache', 'wpsl' ); ?></a>
                            </p>
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'wpsl' ); ?>" class="button-primary">
                            </p>
                        </div>
                    </div>
                </div>                    
            </div>

            <?php settings_fields( 'wpsl_settings' ); ?>
        </form>
    </div>
    
    <?php
    } else {
        do_action( 'wpsl_settings_section', $current_tab );
    }
    ?>
</div>    