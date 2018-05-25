<?php
add_action( 'admin_init',                      'wpsl_csv_check_upgrade' );
add_action( 'wp_ajax_wpsl_start_csv_upgrades', 'wpsl_start_csv_upgrades' );

/**
 * If the db doesn't hold the current version, run the upgrade procedure
 *
 * @since 1.1.0
 * @return void
 */
function wpsl_csv_check_upgrade() {

    $current_version = get_option( 'wpsl_csv_version' );

    if ( version_compare( $current_version, WPSL_CSV_VERSION_NUM, '===' ) )
        return;

    if ( version_compare( $current_version, '1.1.0', '<' ) ) {
        require_once( WPSL_CSV_PLUGIN_DIR . 'admin/roles.php' );

        wpsl_csv_create_roles();
    }

    if ( version_compare( $current_version, '1.2.0', '<=' ) && !get_option( 'wpsl_csv_iso_fixed' ) ) {

        // Check if there's country iso data to fix
        if ( wpsl_csv_grab_iso_data() ) {
            add_action( 'admin_notices', 'wpsl_csv_show_iso_notice' );
        } else {
            update_option( 'wpsl_csv_iso_fixed', '1' );
        }
    }

    update_option( 'wpsl_csv_version', WPSL_CSV_VERSION_NUM );
}

/**
 * Check if we need to show the
 * country iso upgrade notice.
 *
 * @since 1.2.0
 * @return void
 */
function wpsl_csv_show_iso_notice() {
    ?>
        <div id="message" class="updated settings-error notice is-dismissible wpsl-upgrade-txt">
            <p><strong><?php _e( 'CSV Manager', 'wpsl-csv' ); ?></strong> &#8211; <span> <?php _e( 'We need to fix the structure of the country iso data in the database.', 'wpsl-csv' ); ?> </span> <br><br> <button class="button-primary wpsl-run-upgrade"><?php _e( 'Run Update', 'wpsl-csv' ); ?></button><img style="display:none; margin-left:10px;" id="wpsl-upgrade-loader" src="<?php echo WPSL_URL . 'img/ajax-loader.gif'; ?>"></p>
        </div>
        <script type="text/javascript">
        jQuery( ".wpsl-run-upgrade" ).click( "click", function() {
            var ajaxData = { action: "wpsl_start_csv_upgrades" };

            jQuery( "#wpsl-upgrade-loader" ).show();
            jQuery( ".wpsl-run-upgrade" ).attr( "disabled", "disabled" );

            jQuery.post( ajaxurl, ajaxData, function( response ) {
                jQuery( "#wpsl-upgrade-loader" ).hide();

                if ( typeof response.valid !== "undefined" ) {
                    jQuery( ".wpsl-upgrade-txt span" ).html( response.msg );
                    jQuery( ".wpsl-upgrade-txt br, .wpsl-run-upgrade" ).remove();
                }
            });
        });
        </script>
    <?php
}

/**
 * The SQL query that selects locations that
 * may require the country iso fix.
 *
 * @since 1.2.0
 * @param bool   $limit   Whether or not to limit the results.
 * @note 2017-12-11 is a day before the release of the 2.2.10 update that caused this issue.
 * @return array $results
 */
function wpsl_csv_grab_iso_data( $limit = true ) {

    global $wpdb;

    $limit_sql = ( $limit ) ? 'LIMIT 1' : '';

    $sql = "SELECT ID
              FROM {$wpdb->posts}
             WHERE post_type = 'wpsl_stores'
               AND post_status NOT IN ( 'trash' ) 
               AND post_date > '2017-12-11'
             $limit_sql";

    $results = $wpdb->get_results( $sql );

    return $results;
}

/**
 * Fix the structure of the country iso data
 *
 * After the WPSL plugin was updated to 2.2.10
 * the country iso data ended up being saved
 * as a serialized value instead of a single value ( DE, UK etc )
 * when the data was imported with a CSV file.
 *
 * The iso data is currently unused,
 * but this will change in the future.
 *
 * @since 1.2.0
 * @return void
 */
function wpsl_csv_fix_iso_data() {

    // Try to disable the time limit to prevent timeouts.
    @set_time_limit( 0 );

    $results = wpsl_csv_grab_iso_data( false );

    foreach ( $results as $result ) {
        $country_meta = get_post_meta( $result->ID, 'wpsl_country_iso', true );
        $country_meta = maybe_unserialize( $country_meta );

        if ( is_array( $country_meta ) && isset( $country_meta['short_name'] ) ) {
            update_post_meta( $result->ID, 'wpsl_country_iso', $country_meta['short_name'] );
        }
    }
}

/**
 * Run the required upgrades
 *
 * @since 1.2.0
 * @return void
 */
function wpsl_start_csv_upgrades() {

    $current_version = get_option( 'wpsl_csv_version' );
    $status          = array();

    if ( !current_user_can( 'manage_wpsl_settings' ) ) {
        $status = array(
            'valid' => 0,
            'msg'   => __( 'You do not have permission to run CSV Manager upgrades!', 'wpsl-csv' )
        );
    } else {
        if ( version_compare( $current_version, '1.2.0', '==' ) && !get_option( 'wpsl_csv_iso_fixed' ) ) {
            wpsl_csv_fix_iso_data();

            $status = array(
                'valid' => 1,
                'msg' => __( 'Upgrade finished!', 'wpsl-csv' )
            );

            update_option( 'wpsl_csv_iso_fixed', '1' );
        }
    }

    wp_send_json( $status );
}