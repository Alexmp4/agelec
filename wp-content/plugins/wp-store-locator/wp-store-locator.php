<?php
/*
Plugin Name: WP Store Locator
Description: An easy to use location management system that enables users to search for nearby physical stores
Author: Tijmen Smit
Author URI: https://wpstorelocator.co/
Version: 2.2.14
Text Domain: wpsl
Domain Path: /languages/
License: GPL v3

WP Store Locator
Copyright (C) 2013 Tijmen Smit - tijmen@wpstorelocator.co

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
  
@package WP_Store_locator
@category Core
@author Tijmen Smit
*/

if ( !class_exists( 'WP_Store_locator' ) ) {

	class WP_Store_locator {
        
        /**
         * Class constructor
         */          
        function __construct() {
                                    
            $this->define_constants();
            $this->includes();
            $this->plugin_settings();

            // Load classes
            $this->post_types = new WPSL_Post_Types();
            $this->i18n       = new WPSL_i18n();
            $this->frontend   = new WPSL_Frontend();
            $this->templates  = new WPSL_Templates();
                        
            register_activation_hook( __FILE__, array( $this, 'install' ) );
        }
        
        /**
         * Setup plugin constants.
         *
         * @since 1.0.0
         * @return void
         */
        public function define_constants() {

            if ( !defined( 'WPSL_VERSION_NUM' ) )
                define( 'WPSL_VERSION_NUM', '2.2.14' );

            if ( !defined( 'WPSL_URL' ) )
                define( 'WPSL_URL', plugin_dir_url( __FILE__ ) );

            if ( !defined( 'WPSL_BASENAME' ) )
                define( 'WPSL_BASENAME', plugin_basename( __FILE__ ) );

            if ( !defined( 'WPSL_PLUGIN_DIR' ) )
                define( 'WPSL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }
        
        /**
         * Include the required files.
         *
         * @since 2.0.0
         * @return void
         */
        public function includes() {

            require_once( WPSL_PLUGIN_DIR . 'inc/wpsl-functions.php' );
            require_once( WPSL_PLUGIN_DIR . 'inc/class-templates.php' );
            require_once( WPSL_PLUGIN_DIR . 'inc/class-post-types.php' );
            require_once( WPSL_PLUGIN_DIR . 'inc/class-i18n.php' );
            require_once( WPSL_PLUGIN_DIR . 'frontend/class-frontend.php' );
            
            if ( is_admin() || defined( 'WP_CLI' ) && WP_CLI ) {
                require_once( WPSL_PLUGIN_DIR . 'admin/roles.php' );
                require_once( WPSL_PLUGIN_DIR . 'admin/class-admin.php' );
            }
        }
        
        /**
         * Setup the plugin settings.
         *
         * @since 2.0.0
         * @return void
         */
        public function plugin_settings() {
            
            global $wpsl_settings, $wpsl_default_settings;
            
            $wpsl_settings         = wpsl_get_settings();
            $wpsl_default_settings = wpsl_get_default_settings();
        }
        
        /**
         * Install the plugin data.
         *
         * @since 2.0.0
         * @return void
         */
        public function install( $network_wide ) {
            require_once( WPSL_PLUGIN_DIR . 'inc/install.php' );
            wpsl_install( $network_wide );
        }
	}
	
	$GLOBALS['wpsl'] = new WP_Store_locator();
}