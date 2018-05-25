<?php
/*
Plugin URI: https://wpstorelocator.co/add-ons/csv-manager/
Plugin Name: WP Store Locator - CSV Manager
Description: Import, export and update your locations using a CSV file.
Author: Tijmen Smit
Author URI: https://wpstorelocator.co/
Version: 1.2.0
Text Domain: wpsl-csv
Domain Path: /languages/
License: GPL v3

Copyright (C) 2016 Tijmen Smit - tijmen@wpstorelocator.co

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( !defined( 'WPSL_CSV_BASENAME' ) )
    define( 'WPSL_CSV_BASENAME', plugin_basename( __FILE__ ) );

if ( !defined( 'WPSL_CSV_PLUGIN_DIR' ) )
    define( 'WPSL_CSV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( !defined( 'WPSL_CSV_VERSION_NUM' ) )
    define( 'WPSL_CSV_VERSION_NUM', '1.2.0' );

class WPSL_CSV {

    public $min_version = '2.1.0';

    /**
     * Class constructor.
     */          
    function __construct() {

        $this->define_import_constant();
        $this->maybe_update_wpsl();

        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
    }

    /**
     * Set import constants.
     *
     * @since 1.0.0
     * @return void
     */
    public function define_import_constant() {

        $upload_dir = wp_upload_dir();  

        if ( !defined( 'WPSL_CSV_IMPORT_DIR' ) )
            define( 'WPSL_CSV_IMPORT_DIR', $upload_dir['basedir'] . '/wpsl-csv-import/'  );

        if ( !defined( 'WPSL_CSV_IMPORT_FILE' ) )
            define( 'WPSL_CSV_IMPORT_FILE', WPSL_CSV_IMPORT_DIR . 'wpsl-import.csv'  );
    }
    
    /**
     * Make sure WPSL meets the min required version,
     * before including the required files.
     *
     * @since 1.0.0
     * @return void
     */
    public function maybe_update_wpsl() {
        
        if ( version_compare( WPSL_VERSION_NUM, $this->min_version, '<' ) ) {
            add_action( 'all_admin_notices', array( $this, 'update_wpsl_notice' ) );
        } else {
            $this->setup_license();
            $this->includes();
        }    
    }
    
    /**
     * Show a notice telling the user to update WPSL
     * before they can use this add-on.
     *
     * @since 1.0.0
     * @return void
     */
    public function update_wpsl_notice() {
        echo '<div class="error"><p>' . sprintf( __( 'Please upgrade WP Store Locator to the %slatest version%s before using the CSV Manager add-on.', 'wpsl-csv' ), '<a href="https://wordpress.org/plugins/wp-store-locator/">', '</a>' ) . '</p></div>';
    }

    /**
     * Include the required files.
     *
     * @since 1.0.0
     * @return void
     */
    public function includes() {

        if ( is_admin() ) {
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/roles.php' );
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/libraries/parsecsv.lib.php' );
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/wpsl-csv-functions.php' );
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/tools-functions.php' );
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/class-import.php' );
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/class-export.php' );
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/class-admin.php' );
        }
    }
    
    /**
     * Handle the addon license.
     *
     * @since 1.0.0
     * @return void
     */
    public function setup_license() {
        if ( class_exists( 'WPSL_License_Manager' ) ) {
            $license = new WPSL_License_Manager( 'CSV Manager', WPSL_CSV_VERSION_NUM, 'Tijmen Smit', __FILE__ );
        }
    }

    /**
     * Load the translations from the language folder.
     *
     * @since 1.0.0
     * @return void
     */
    public function load_plugin_textdomain() {

        $domain = 'wpsl-csv';
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        // Load the language file from the /wp-content/languages/wp-store-locator-csv folder, custom + update proof translations
        load_textdomain( $domain, WP_LANG_DIR . '/wp-store-locator-csv/' . $domain . '-' . $locale . '.mo' );

        // Load the language file from the /wp-content/plugins/wp-store-locator-csv/languages/ folder
        load_plugin_textdomain( $domain, false, dirname( WPSL_CSV_BASENAME ) . '/languages/' );
    }
}

/**
 * Get started.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_csv_init() {
    
    // Make sure WP Store Locator itself is active.
    if ( !class_exists( 'WP_Store_locator' ) ) {
        return;
    }
    
    new WPSL_CSV();
}

add_action( 'plugins_loaded', 'wpsl_csv_init' );

/**
 * Run when the CSV Manager plugin is activated.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_csv_activate( $network_wide ) {
    require_once( WPSL_CSV_PLUGIN_DIR . 'admin/wpsl-csv-functions.php' );
    
    wpsl_csv_install( $network_wide );
}

register_activation_hook( __FILE__, 'wpsl_csv_activate' );