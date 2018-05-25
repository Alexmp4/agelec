<?php
/**
 * Create the store data templates.
 * 
 * The templates are created in JS with _.template, see http://underscorejs.org/#template
 * 
 * @since 2.0.0
 * @param string $template The type of template we need to create
 * @return void
 */
function wpsl_create_underscore_templates( $template ) {

    global $wpsl_settings, $wpsl;

    if ( $template == 'wpsl_store_locator' ) {
    ?>
<script id="wpsl-info-window-template" type="text/template">
    <?php
        $info_window_template = '<div data-store-id="<%= id %>" class="wpsl-info-window">' . "\r\n";
        $info_window_template .= "\t\t" . '<p>' . "\r\n";
        $info_window_template .= "\t\t\t" .  wpsl_store_header_template() . "\r\n";  // Check which header format we use
        $info_window_template .= "\t\t\t" . '<span><%= address %></span>' . "\r\n";
        $info_window_template .= "\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
        $info_window_template .= "\t\t\t" . '<span><%= address2 %></span>' . "\r\n";
        $info_window_template .= "\t\t\t" . '<% } %>' . "\r\n";
        $info_window_template .= "\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n"; // Use the correct address format
        $info_window_template .= "\t\t" . '</p>' . "\r\n";
        $info_window_template .= "\t\t" . '<% if ( phone ) { %>' . "\r\n";
        $info_window_template .= "\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'phone_label', __( 'Phone', 'wpsl' ) ) ) . '</strong>: <%= formatPhoneNumber( phone ) %></span>' . "\r\n";
        $info_window_template .= "\t\t" . '<% } %>' . "\r\n";
        $info_window_template .= "\t\t" . '<% if ( fax ) { %>' . "\r\n";
        $info_window_template .= "\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'fax_label', __( 'Fax', 'wpsl' ) ) ) . '</strong>: <%= fax %></span>' . "\r\n";
        $info_window_template .= "\t\t" . '<% } %>' . "\r\n";
        $info_window_template .= "\t\t" . '<% if ( email ) { %>' . "\r\n";
        $info_window_template .= "\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'email_label', __( 'Email', 'wpsl' ) ) ) . '</strong>: <%= formatEmail( email ) %></span>' . "\r\n";
        $info_window_template .= "\t\t" . '<% } %>' . "\r\n";
        $info_window_template .= "\t\t" . '<%= createInfoWindowActions( id ) %>' . "\r\n";
        $info_window_template .= "\t" . '</div>';

        echo apply_filters( 'wpsl_info_window_template', $info_window_template . "\n" );
    ?>
</script>
<script id="wpsl-listing-template" type="text/template">
    <?php
        $listing_template = '<li data-store-id="<%= id %>">' . "\r\n";
        $listing_template .= "\t\t" . '<div class="wpsl-store-location">' . "\r\n";
        $listing_template .= "\t\t\t" . '<p><%= thumb %>' . "\r\n";
        $listing_template .= "\t\t\t\t" . wpsl_store_header_template( 'listing' ) . "\r\n"; // Check which header format we use
        $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address %></span>' . "\r\n";
        $listing_template .= "\t\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
        $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address2 %></span>' . "\r\n";
        $listing_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
        $listing_template .= "\t\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n"; // Use the correct address format

        if ( !$wpsl_settings['hide_country'] ) {
            $listing_template .= "\t\t\t\t" . '<span class="wpsl-country"><%= country %></span>' . "\r\n";
        }
        
        $listing_template .= "\t\t\t" . '</p>' . "\r\n";
        
        // Show the phone, fax or email data if they exist.
        if ( $wpsl_settings['show_contact_details'] ) {
            $listing_template .= "\t\t\t" . '<p class="wpsl-contact-details">' . "\r\n";
            $listing_template .= "\t\t\t" . '<% if ( phone ) { %>' . "\r\n";
            $listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'phone_label', __( 'Phone', 'wpsl' ) ) ) . '</strong>: <%= formatPhoneNumber( phone ) %></span>' . "\r\n";
            $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
            $listing_template .= "\t\t\t" . '<% if ( fax ) { %>' . "\r\n";
            $listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'fax_label', __( 'Fax', 'wpsl' ) ) ) . '</strong>: <%= fax %></span>' . "\r\n";
            $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
            $listing_template .= "\t\t\t" . '<% if ( email ) { %>' . "\r\n";
            $listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'email_label', __( 'Email', 'wpsl' ) ) ) . '</strong>: <%= formatEmail( email ) %></span>' . "\r\n";
            $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
            $listing_template .= "\t\t\t" . '</p>' . "\r\n";
        }
        
        $listing_template .= "\t\t\t" . wpsl_more_info_template() . "\r\n"; // Check if we need to show the 'More Info' link and info
        $listing_template .= "\t\t" . '</div>' . "\r\n";
        $listing_template .= "\t\t" . '<div class="wpsl-direction-wrap">' . "\r\n";
        
        if ( !$wpsl_settings['hide_distance'] ) {
            $listing_template .= "\t\t\t" . '<%= distance %> ' . esc_html( wpsl_get_distance_unit() ) . '' . "\r\n";
        }
        
        $listing_template .= "\t\t\t" . '<%= createDirectionUrl() %>' . "\r\n"; 
        $listing_template .= "\t\t" . '</div>' . "\r\n";
        $listing_template .= "\t" . '</li>';

        echo apply_filters( 'wpsl_listing_template', $listing_template . "\n" );
    ?>
</script>            
    <?php
    } else {
    ?>
<script id="wpsl-cpt-info-window-template" type="text/template">
    <?php
        $cpt_info_window_template = '<div class="wpsl-info-window">' . "\r\n";
        $cpt_info_window_template .= "\t\t" . '<p class="wpsl-no-margin">' . "\r\n";
        $cpt_info_window_template .= "\t\t\t" .  wpsl_store_header_template( 'wpsl_map' ) . "\r\n";
        $cpt_info_window_template .= "\t\t\t" . '<span><%= address %></span>' . "\r\n";
        $cpt_info_window_template .= "\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
        $cpt_info_window_template .= "\t\t\t" . '<span><%= address2 %></span>' . "\r\n";
        $cpt_info_window_template .= "\t\t\t" . '<% } %>' . "\r\n";
        $cpt_info_window_template .= "\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n"; // Use the correct address format 
        
        if ( !$wpsl_settings['hide_country'] ) {
            $cpt_info_window_template .= "\t\t\t" . '<span class="wpsl-country"><%= country %></span>' . "\r\n"; 
        }
        
        $cpt_info_window_template .= "\t\t" . '</p>' . "\r\n";
        $cpt_info_window_template .= "\t" . '</div>';

        echo apply_filters( 'wpsl_cpt_info_window_template', $cpt_info_window_template . "\n" );
    ?>
</script>
    <?php
    }
}

/**
 * Create the more info template.
 *
 * @since 2.0.0
 * @return string $more_info_template The template that is used to show the "More info" content
 */
function wpsl_more_info_template() {
            
    global $wpsl_settings, $wpsl;

    if ( $wpsl_settings['more_info'] ) {
        $more_info_url = '#';

        if ( $wpsl_settings['template_id'] == 'default' && $wpsl_settings['more_info_location'] == 'info window' ) {
            $more_info_url = '#wpsl-search-wrap';
        }

        if ( $wpsl_settings['more_info_location'] == 'store listings' ) {
            $more_info_template = '<% if ( !_.isEmpty( phone ) || !_.isEmpty( fax ) || !_.isEmpty( email ) ) { %>' . "\r\n";
            $more_info_template .= "\t\t\t" . '<p><a class="wpsl-store-details wpsl-store-listing" href="#wpsl-id-<%= id %>">' . esc_html( $wpsl->i18n->get_translation( 'more_label', __( 'More info', 'wpsl' ) ) ) . '</a></p>' . "\r\n";
            $more_info_template .= "\t\t\t" . '<div id="wpsl-id-<%= id %>" class="wpsl-more-info-listings">' . "\r\n";
            $more_info_template .= "\t\t\t\t" . '<% if ( description ) { %>' . "\r\n";
            $more_info_template .= "\t\t\t\t" . '<%= description %>' . "\r\n";
            $more_info_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
            
            if ( !$wpsl_settings['show_contact_details'] ) {
                $more_info_template .= "\t\t\t\t" . '<p>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<% if ( phone ) { %>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'phone_label', __( 'Phone', 'wpsl' ) ) ) . '</strong>: <%= formatPhoneNumber( phone ) %></span>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<% if ( fax ) { %>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'fax_label', __( 'Fax', 'wpsl' ) ) ) . '</strong>: <%= fax %></span>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<% if ( email ) { %>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'email_label', __( 'Email', 'wpsl' ) ) ) . '</strong>: <%= formatEmail( email ) %></span>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '</p>' . "\r\n";
            }

            if ( !$wpsl_settings['hide_hours'] ) {
                $more_info_template .= "\t\t\t\t" . '<% if ( hours ) { %>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<div class="wpsl-store-hours"><strong>' . esc_html( $wpsl->i18n->get_translation( 'hours_label', __( 'Hours', 'wpsl' ) ) ) . '</strong><%= hours %></div>' . "\r\n";
                $more_info_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
            }

            $more_info_template .= "\t\t\t" . '</div>' . "\r\n"; 
            $more_info_template .= "\t\t\t" . '<% } %>';

        } else {
            $more_info_template = '<p><a class="wpsl-store-details" href="' . $more_info_url . '">' . esc_html( $wpsl->i18n->get_translation( 'more_label', __( 'More info', 'wpsl' ) ) ) . '</a></p>';
        }

        return apply_filters( 'wpsl_more_info_template', $more_info_template );
    }                 
}

/**
 * Create the store header template.
 *
 * @since 2.0.0
 * @param  string $location        The location where the header is shown ( info_window / listing / wpsl_map shortcode )
 * @return string $header_template The template for the store header
 */
function wpsl_store_header_template( $location = 'info_window' ) {

    global $wpsl_settings;

    if ( $wpsl_settings['new_window'] ) {
        $new_window = ' target="_blank"';
    } else {
        $new_window = '';
    }

    /* 
     * To keep the code readable in the HTML source we ( unfortunately ) need to adjust the 
     * amount of tabs in front of it based on the location were it is shown. 
     */
    if ( $location == 'listing') {
        $tab = "\t\t\t\t";    
    } else {
        $tab = "\t\t\t";                 
    }

    if ( $wpsl_settings['permalinks'] ) {
        
        /**
         * It's possible the permalinks are enabled, but not included in the location data on 
         * pages where the [wpsl_map] shortcode is used. 
         * 
         * So we need to check for undefined, which isn't necessary in all other cases.
         */
        if ( $location == 'wpsl_map') {
            $header_template = '<% if ( typeof permalink !== "undefined" ) { %>' . "\r\n";
            $header_template .= $tab . '<strong><a' . $new_window . ' href="<%= permalink %>"><%= store %></a></strong>' . "\r\n";
            $header_template .= $tab . '<% } else { %>' . "\r\n";
            $header_template .= $tab . '<strong><%= store %></strong>' . "\r\n";
            $header_template .= $tab . '<% } %>';   
        } else {
            $header_template = '<strong><a' . $new_window . ' href="<%= permalink %>"><%= store %></a></strong>';
        }
    } else {
        $header_template = '<% if ( wpslSettings.storeUrl == 1 && url ) { %>' . "\r\n";
        $header_template .= $tab . '<strong><a' . $new_window . ' href="<%= url %>"><%= store %></a></strong>' . "\r\n";
        $header_template .= $tab . '<% } else { %>' . "\r\n";
        $header_template .= $tab . '<strong><%= store %></strong>' . "\r\n";
        $header_template .= $tab . '<% } %>'; 
    }

    return apply_filters( 'wpsl_store_header_template', $header_template );
}
        
/**
 * Create the address placeholders based on the structure defined on the settings page.
 * 
 * @since 2.0.0
 * @return string $address_placeholders A list of address placeholders in the correct order
 */
function wpsl_address_format_placeholders() {

    global $wpsl_settings;

    $address_format = explode( '_', $wpsl_settings['address_format'] );
    $placeholders   = '';
    $part_count     = count( $address_format ) - 1;
    $i              = 0;

    foreach ( $address_format as $address_part ) {
        if ( $address_part != 'comma' ) {

            /* 
             * Don't add a space after the placeholder if the next part 
             * is going to be a comma or if it is the last part. 
             */
            if ( $i == $part_count || $address_format[$i + 1] == 'comma' ) {
                $space = '';    
            } else {
                $space = ' ';      
            }

            $placeholders .= '<%= ' . $address_part . ' %>' . $space;
        } else {
            $placeholders .= ', ';
        }

        $i++;
    }

    return $placeholders;
}