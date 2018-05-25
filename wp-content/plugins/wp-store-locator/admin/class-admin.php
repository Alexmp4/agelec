<?php
/**
 * Admin class
 *
 * @author Tijmen Smit
 * @since  1.0.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Admin' ) ) {
    
    /**
     * Handle the backend of the store locator
     *
     * @since 1.0.0
     */
	class WPSL_Admin {

        /**
         * @since 2.0.0
         * @var WPSL_Metaboxes
         */
        public $metaboxes;
        
        /**
         * @since 2.0.0
         * @var WPSL_Geocode
         */
        public $geocode;
    
        /**
         * @since 2.0.0
         * @var WPSL_Notices
         */
        public $notices;

        /**
         * @since 2.0.0
         * @var WPSL_Settings
         */
        public $settings_page;

        /**
         * Class constructor
         */
		function __construct() {

            $this->includes();

            add_action( 'init',                                 array( $this, 'init' ) );
            add_action( 'admin_menu',                           array( $this, 'create_admin_menu' ) );
			add_action( 'admin_init',                           array( $this, 'setting_warnings' ) );
            add_action( 'delete_post',                          array( $this, 'maybe_delete_autoload_transient' ) );
            add_action( 'wp_trash_post',                        array( $this, 'maybe_delete_autoload_transient' ) );
            add_action( 'untrash_post',                         array( $this, 'maybe_delete_autoload_transient' ) );
            add_action( 'admin_enqueue_scripts',                array( $this, 'admin_scripts' ) );	
            add_filter( 'plugin_row_meta',                      array( $this, 'add_plugin_meta_row' ), 10, 2 );
            add_filter( 'plugin_action_links_' . WPSL_BASENAME, array( $this, 'add_action_links' ), 10, 2 );
            add_filter( 'admin_footer_text',                    array( $this, 'admin_footer_text' ), 1 );
            add_action( 'wp_loaded',                            array( $this, 'disable_setting_notices' ) );
		}

        /**
         * Include the required files.
         *
         * @since 2.0.0
         * @return void
         */
        public function includes() {
            require_once( WPSL_PLUGIN_DIR . 'admin/class-shortcode-generator.php' );
            require_once( WPSL_PLUGIN_DIR . 'admin/class-notices.php' );
            require_once( WPSL_PLUGIN_DIR . 'admin/class-license-manager.php' );
            require_once( WPSL_PLUGIN_DIR . 'admin/class-metaboxes.php' ); 
            require_once( WPSL_PLUGIN_DIR . 'admin/class-geocode.php' );
            require_once( WPSL_PLUGIN_DIR . 'admin/class-settings.php' );
            require_once( WPSL_PLUGIN_DIR . 'admin/upgrade.php' );
		}
        
        /**
         * Init the classes.
         *
         * @since 2.0.0
         * @return void
         */
		public function init() {
            $this->notices       = new WPSL_Notices();
            $this->metaboxes     = new WPSL_Metaboxes();
			$this->geocode       = new WPSL_Geocode();
            $this->settings_page = new WPSL_Settings();
		}
                
        /**
         * Check if we need to show warnings after 
         * the user installed the plugin.
         *
         * @since 1.0.0
         * @todo move to class-notices?
         * @return void
         */
		public function setting_warnings() {
            
            global $current_user, $wpsl_settings;
            
            $this->setting_warning = array();
                         
            // The fields settings field to check for data.
            $warnings = array(
                'start_latlng'    => 'location',
                'api_browser_key' => 'key'
            );
            
            if ( ( current_user_can( 'install_plugins' ) ) && is_admin() ) {
                foreach ( $warnings as $setting_name => $warning ) {
                    if ( empty( $wpsl_settings[$setting_name] ) && !get_user_meta( $current_user->ID, 'wpsl_disable_' . $warning . '_warning' ) ) {
                        if ( $warning == 'key' ) {
                            $this->setting_warning[$warning] = sprintf( __( "You need to create %sAPI keys%s for Google Maps before you can use the store locator! %sDismiss%s", "wpsl" ), '<a href="https://wpstorelocator.co/document/create-google-api-keys/">', "</a>", "<a href='" . esc_url( wp_nonce_url( add_query_arg( 'wpsl-notice', 'key' ), 'wpsl_notices_nonce', '_wpsl_notice_nonce' ) ) . "'>", "</a>" );
                        } else {
                            $this->setting_warning[$warning] = sprintf( __( "Before adding the [wpsl] shortcode to a page, please don't forget to define a start point on the %ssettings%s page. %sDismiss%s", "wpsl" ), "<a href='" . admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_settings' ) . "'>", "</a>", "<a href='" . esc_url( wp_nonce_url( add_query_arg( 'wpsl-notice', 'location' ), 'wpsl_notices_nonce', '_wpsl_notice_nonce' ) ) . "'>", "</a>" );
                        }
                    }
                }
                
                if ( $this->setting_warning ) {
                    add_action( 'admin_notices', array( $this, 'show_warning' ) );
                }
            }
		}

       /**
        * Show the admin warnings
        * 
        * @since 1.2.0
        * @return void
        */
        public function show_warning() {
            foreach ( $this->setting_warning as $k => $warning ) {
                echo "<div id='message' class='error'><p>" . $warning .  "</p></div>";
            }
        }
        
        /**
         * Disable notices about the plugin settings.
         * 
         * @todo move to class-notices?
         * @since 2.2.3
         * @return void
         */
        public function disable_setting_notices() {
            
            global $current_user;
            
            if ( isset( $_GET['wpsl-notice'] ) && isset( $_GET['_wpsl_notice_nonce'] ) ) {

                if ( !wp_verify_nonce( $_GET['_wpsl_notice_nonce'], 'wpsl_notices_nonce' ) ) {
                    wp_die( __( 'Security check failed. Please reload the page and try again.', 'wpsl' ) );
                }
                
                $notice = sanitize_text_field( $_GET['wpsl-notice'] );
                
                add_user_meta( $current_user->ID, 'wpsl_disable_' . $notice . '_warning', 'true', true );
            }
        }
        
        /**
         * Add the admin menu pages.
         *
         * @since 1.0.0
         * @return void
         */
		public function create_admin_menu() {
            
            $sub_menus = apply_filters( 'wpsl_sub_menu_items', array(
                    array(
                        'page_title'  => __( 'Settings', 'wpsl' ),
                        'menu_title'  => __( 'Settings', 'wpsl' ),
                        'caps'        => 'manage_wpsl_settings',
                        'menu_slug'   => 'wpsl_settings',
                        'function'    => array( $this, 'load_template' )
                    ),
                    array(
                        'page_title'  => __( 'Add-Ons', 'wpsl' ),
                        'menu_title'  => __( 'Add-Ons', 'wpsl' ),
                        'caps'        => 'manage_wpsl_settings',
                        'menu_slug'   => 'wpsl_add_ons',
                        'function'    => array( $this, 'load_template' )
                    )
                )
            );
      
            if ( count( $sub_menus ) ) {
                foreach ( $sub_menus as $sub_menu ) {
                    add_submenu_page( 'edit.php?post_type=wpsl_stores', $sub_menu['page_title'], $sub_menu['menu_title'], $sub_menu['caps'], $sub_menu['menu_slug'], $sub_menu['function'] );
                }
            }            
        }

        /**
         * Load the correct page template.
         *
         * @since 2.1.0
         * @return void
         */
        public function load_template() {
            
            switch ( $_GET['page'] ) {
                case 'wpsl_settings':
                    require 'templates/map-settings.php';
                break;
                case 'wpsl_add_ons':
                    require 'templates/add-ons.php';
                break;
            }
        }

        /**
         * Check if we need to delete the autoload transient.
         * 
         * This is called when a post it saved, deleted, trashed or untrashed.
         * 
         * @since 2.0.0
         * @return void
         */
        public function maybe_delete_autoload_transient( $post_id ) {
            
            global $wpsl_settings;
            
            if ( isset( $wpsl_settings['autoload'] ) && $wpsl_settings['autoload'] && get_post_type( $post_id ) == 'wpsl_stores' ) {
				$this->delete_autoload_transient(); 
            }
        }
        
        /**
         * Delete the transients that are used on the front-end 
         * if the autoload option is enabled.
         * 
         * The transient names used by the store locator are partly dynamic. 
         * They always start with wpsl_autoload_, followed by the number of 
         * stores to load and ends with the language code.
         * 
         * So you get wpsl_autoload_20_de if the language is set to German
         * and 20 stores are set to show on page load. 
         * 
         * The language code has to be included in case a multilingual plugin is used.
         * Otherwise it can happen the user switches to Spanish, 
         * but ends up seeing the store data in the wrong language.
         * 
         * @since 2.0.0
         * @return void
         */
        public function delete_autoload_transient() {
            
            global $wpdb;
            
            $option_names = $wpdb->get_results( "SELECT option_name AS transient_name FROM " . $wpdb->options . " WHERE option_name LIKE ('\_transient\_wpsl\_autoload\_%')" );

            if ( $option_names ) {
                foreach ( $option_names as $option_name ) {
                    $transient_name = str_replace( "_transient_", "", $option_name->transient_name );

                    delete_transient( $transient_name );
                }
            }
        }
        
        /**
         * Check if we can use a font for the plugin icon.
         * 
         * This is supported by WP 3.8 or higher
         *
         * @since 1.0.0
         * @return void
         */
        private function check_icon_font_usage() {
                        
            global $wp_version;

            if ( ( version_compare( $wp_version, '3.8', '>=' ) == TRUE ) ) {
                $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
                
                wp_enqueue_style( 'wpsl-admin-38', plugins_url( '/css/style-3.8'. $min .'.css', __FILE__ ), false );
            } 
        }
                
        /**
         * The text messages used in wpsl-admin.js.
         *
         * @since 1.2.20
         * @return array $admin_js_l10n The texts used in the wpsl-admin.js
         */
        public function admin_js_l10n() {
            
            $admin_js_l10n = array(
                'noAddress'       => __( 'Cannot determine the address at this location.', 'wpsl' ),
                'geocodeFail'     => __( 'Geocode was not successful for the following reason', 'wpsl' ),
                'securityFail'    => __( 'Security check failed, reload the page and try again.', 'wpsl' ),
                'requiredFields'  => __( 'Please fill in all the required store details.', 'wpsl' ),
                'missingGeoData'  => __( 'The map preview requires all the location details.', 'wpsl' ),
                'closedDate'      => __( 'Closed', 'wpsl' ),
                'styleError'      => __( 'The code for the map style is invalid.', 'wpsl' ),
                'browserKeyError' => sprintf( __( 'There\'s a problem with the provided %sbrowser key%s. %s You can read more about how to determine the exact issue, and how to solve it %shere%s.','wpsl' ), '<a href="https://wpstorelocator.co/document/create-google-api-keys/#browser-key">','</a>', '<br><br>', '<a href="https://wpstorelocator.co/document/create-google-api-keys/#troubleshooting">','</a>' ),
                'dismissNotice'   => __( 'Dismiss this notice.', 'wpsl' )
            );

            return $admin_js_l10n;
        }
        
        /**
         * Plugin settings that are used in the wpsl-admin.js.
         *
         * @since 2.0.0
         * @return array $settings_js The settings used in the wpsl-admin.js
         */
        public function js_settings() {
            
            global $wpsl_settings;

            $js_settings = array(
                'hourFormat'     => $wpsl_settings['editor_hour_format'],
                'defaultLatLng'  => $this->get_default_lat_lng(),
                'defaultZoom'    => 6,
                'mapType'        => $wpsl_settings['editor_map_type'],
                'requiredFields' => array( 'address', 'city', 'country' ),
                'ajaxurl'        => wpsl_get_ajax_url()
            );

            return apply_filters( 'wpsl_admin_js_settings', $js_settings );
        }

        /**
         * Get the coordinates that are used to
         * show the map on the settings page.
         *
         * @since 2.2.5
         * @return string $startLatLng The start coordinates
         */
        public function get_default_lat_lng() {

            global $wpsl_settings;

            $startLatLng = $wpsl_settings['start_latlng'];

            // If no start coordinates exists, then set the default to Holland.
            if ( !$startLatLng ) {
                $startLatLng = '52.378153,4.899363';
            }

            return $startLatLng;
        }

        /**
         * Add the required admin script.
         *
         * @since 1.0.0
         * @return void
         */
		public function admin_scripts() {

            $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min'; 
            
            // Always load the main js admin file to make sure the "dismiss" link in the location notice works.
            wp_enqueue_script( 'wpsl-admin-js', plugins_url( '/js/wpsl-admin'. $min .'.js', __FILE__ ), array( 'jquery' ), WPSL_VERSION_NUM, true );				

            $this->maybe_show_pointer();
            $this->check_icon_font_usage();
            
            // Only enqueue the rest of the css/js files if we are on a page that belongs to the store locator.
            if ( ( get_post_type() == 'wpsl_stores' ) || ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] == 'wpsl_stores' ) ) ) {
                
                // Make sure no other Google Map scripts can interfere with the one from the store locator.
                wpsl_deregister_other_gmaps();
                
                wp_enqueue_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css' );
                wp_enqueue_style( 'wpsl-admin-css', plugins_url( '/css/style'. $min .'.css', __FILE__ ), false );
                
                wp_enqueue_media();
                wp_enqueue_script( 'jquery-ui-dialog' );
                wp_enqueue_script( 'wpsl-gmap', ( '//maps.google.com/maps/api/js' . wpsl_get_gmap_api_params( 'browser_key' ) ), false, WPSL_VERSION_NUM, true );

                wp_enqueue_script( 'wpsl-queue', plugins_url( '/js/ajax-queue'. $min .'.js', __FILE__ ), array( 'jquery' ), WPSL_VERSION_NUM, true ); 
                wp_enqueue_script( 'wpsl-retina', plugins_url( '/js/retina'. $min .'.js', __FILE__ ), array( 'jquery' ), WPSL_VERSION_NUM, true ); 
                                
                wp_localize_script( 'wpsl-admin-js', 'wpslL10n',     $this->admin_js_l10n() );
                wp_localize_script( 'wpsl-admin-js', 'wpslSettings', $this->js_settings() );
            }
        }
        
        /**
         * Check if we need to show the wpsl pointer.
         *
         * @since 2.0.0
         * @return void
         */
        public function maybe_show_pointer() {

            $disable_pointer = apply_filters( 'wpsl_disable_welcome_pointer', false );

            if ( $disable_pointer ) {
                return;
            }
            
            $dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
            
            // If the user hasn't dismissed the wpsl pointer, enqueue the script and style, and call the action hook.
            if ( !in_array( 'wpsl_signup_pointer', $dismissed_pointers ) ) {
                wp_enqueue_style( 'wp-pointer' );
                wp_enqueue_script( 'wp-pointer' );
                
                add_action( 'admin_print_footer_scripts', array( $this, 'welcome_pointer_script' ) );
            } 
        }
        
        /**
         * Add the script for the welcome pointer.
         *
         * @since 2.0.0
         * @return void
         */
        public function welcome_pointer_script() {
            
            $pointer_content = '<h3>' . __( 'Welcome to WP Store Locator', 'wpsl' ) . '</h3>';
            $pointer_content .= '<p>' . __( 'Sign up for the latest plugin updates and announcements.', 'wpsl' ) . '</p>';
            $pointer_content .= '<div id="mc_embed_signup" class="wpsl-mc-wrap" style="padding:0 15px; margin-bottom:13px;"><form action="//wpstorelocator.us10.list-manage.com/subscribe/post?u=34e4c75c3dc990d14002e19f6&amp;id=4be03427d7" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate><div id="mc_embed_signup_scroll"><input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required style="margin-right:5px;width:230px;"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"><div style="position: absolute; left: -5000px;"><input type="text" name="b_34e4c75c3dc990d14002e19f6_4be03427d7" tabindex="-1" value=""></div></div></form></div>';
            ?>

            <script type="text/javascript">
			//<![CDATA[
			jQuery( document ).ready( function( $ ) {
                $( '#menu-posts-wpsl_stores' ).pointer({
                    content: '<?php echo $pointer_content; ?>',
                    position: {
                        edge: 'left',
                        align: 'center'
                    },
                    pointerWidth: 350,
                    close: function () {
                        $.post( ajaxurl, {
                            pointer: 'wpsl_signup_pointer',
                            action: 'dismiss-wp-pointer'
                        });
                    }
                }).pointer( 'open' );
                
                // If a user clicked the "subscribe" button trigger the close button for the pointer.
                $( ".wpsl-mc-wrap #mc-embedded-subscribe" ).on( "click", function() {
                    $( ".wp-pointer .close" ).trigger( "click" );
                });
            });
            //]]>
            </script>
            
            <?php
        } 

        /**
         * Add link to the plugin action row.
         *
         * @since 2.0.0
         * @param  array  $links The existing action links
         * @param  string $file  The file path of the current plugin
         * @return array  $links The modified links
         */
        public function add_action_links( $links, $file ) {
            
            if ( strpos( $file, 'wp-store-locator.php' ) !== false ) {
                $settings_link = '<a href="' . admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_settings' ) . '" title="View WP Store Locator Settings">' . __( 'Settings', 'wpsl' ) . '</a>';
                array_unshift( $links, $settings_link );
            }

            return $links;
        }
        
        /**
         * Add links to the plugin meta row.
         *
         * @since 2.1.1
         * @param  array  $links The existing meta links
         * @param  string $file  The file path of the current plugin
         * @return array  $links The modified meta links
         */
        public function add_plugin_meta_row( $links, $file ) {
            
            if ( strpos( $file, 'wp-store-locator.php' ) !== false ) {
                $new_links = array(
                    '<a href="https://wpstorelocator.co/documentation/" title="View Documentation">'. __( 'Documentation', 'wpsl' ).'</a>',
                    '<a href="https://wpstorelocator.co/add-ons/" title="View Add-Ons">'. __( 'Add-Ons', 'wpsl' ).'</a>'
                );

                $links = array_merge( $links, $new_links );
            }

            return $links;
        }
        
        /**
         * Change the footer text on the settings page.
         *
         * @since 2.0.0
         * @param  string $text The current footer text
         * @return string $text Either the original or modified footer text
         */
        public function admin_footer_text( $text ) {
            
            $current_screen = get_current_screen();
            
            // Only modify the footer text if we are on the settings page of the wp store locator.
            if ( isset( $current_screen->id ) && $current_screen->id == 'wpsl_stores_page_wpsl_settings' ) {
                $text = sprintf( __( 'If you like this plugin please leave us a %s5 star%s rating.', 'wpsl' ), '<a href="https://wordpress.org/support/view/plugin-reviews/wp-store-locator?filter=5#postform" target="_blank"><strong>', '</strong></a>' );
            }
            
            return $text;
        }
    }
	
	$GLOBALS['wpsl_admin'] = new WPSL_Admin();
}