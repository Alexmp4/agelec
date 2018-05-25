<?php
/**
 * WPSL CSV Admin class
 * 
 * @since  1.0.0
 * @author Tijmen Smit
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_CSV_Admin' ) ) {

    class WPSL_CSV_Admin {
                
        /**
         * @since 1.0.0
         * @var WPSL_CSV_Import $import
         */
        public $import;
        
        /**
         * @since 1.0.0
         * @var WPSL_CSV_Export $export
         */
        public $export;

        /**
         * Class constructor
         */
		function __construct() {

            $this->includes();
            $this->init();

            add_action( 'init',                  array( $this, 'form_actions' ) );
            add_action( 'admin_menu',            array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

        /**
         * Include the required files.
         *
         * @since 1.1.0
         * @return void
         */
        public function includes() {
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/upgrade.php' );
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/roles.php' );
        }

        /**
         * Init the required classes.
         *
         * @since 1.0.0
         * @return void
         */
        public function init() {
            $this->import = new WPSL_CSV_Import();
            $this->export = new WPSL_CSV_Export();
        }

        /**
         * Handle the different form actions.
         *
         * @since 1.0.0
         * @return void
         */
        public function form_actions() {
            if ( isset( $_POST['wpsl_action'] ) ) {
                do_action( 'wpsl_' . $_POST['wpsl_action'] );
            }
        }

        /**
         * Add the 'CSV Manager' sub menu to the 
         * existing WP Store Locator menu.
         * 
         * @since  1.0.0
         * @return void
         */        
        public function admin_menu() {
            add_submenu_page( 'edit.php?post_type=wpsl_stores', __( 'CSV Manager', 'wpsl-csv' ), __( 'CSV Manager', 'wpsl-csv' ), 'wpsl_csv_manager', 'wpsl_csv', array( $this, 'csv_page' ) );
        }
        
        /**
         * Show the CSV import / export template.
         * 
         * @since  1.0.0
         * @return void
         */        
        public function csv_page() {
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/templates/html-csv.php' );  
        }

        /**
         * Add the required admin scripts.
         *
         * @since  1.0.0
         * @return void
         */
        public function admin_scripts() {

            $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

            wp_enqueue_style( 'wpsl-csv-admin', plugins_url( '/css/style'. $min .'.css', __FILE__ ), false );
            wp_enqueue_script( 'jquery-ui-dialog' );
            wp_enqueue_script( 'wpsl-csv-tools', plugins_url( '/js/wpsl-csv-tools'. $min .'.js', __FILE__ ), array( 'jquery' ), WPSL_CSV_VERSION_NUM, true );
        }
    }

    new WPSL_CSV_Admin();
}