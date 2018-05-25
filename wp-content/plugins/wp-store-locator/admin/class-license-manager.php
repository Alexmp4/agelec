<?php
/**
 * Handle the add-on license and updates.
 *
 * @author Tijmen Smit
 * @since  2.1.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

class WPSL_License_Manager {
    
    public $item_name;
    public $item_shortname;
    public $version;
    public $author;
    public $file;
    public $api_url = 'https://wpstorelocator.co/';
    
    /**
	 * Class constructor
	 *
	 * @param string  $item_name
	 * @param string  $version
	 * @param string  $author
	 * @param string  $file
	 */
    function __construct( $item_name, $version, $author, $file  ) {

        $this->item_name      = $item_name;
		$this->item_shortname = 'wpsl_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
		$this->version        = $version;
        $this->author         = $author;
		$this->file           = $file;
        
        $this->includes();

        add_action( 'admin_init',            array( $this, 'auto_updater' ), 0 );
        add_action( 'admin_init',            array( $this, 'license_actions' ) );
        add_filter( 'wpsl_license_settings', array( $this, 'add_license_field' ), 1 );
	}
    
   /**
	 * Include the updater class
	 *
     * @since 2.1.0
	 * @access  private
	 * @return  void
	 */
	private function includes() {
		if ( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            require_once 'EDD_SL_Plugin_Updater.php';
        }
	}
    
    /**
     * Handle the add-on updates.
     * 
     * @since 2.1.0
     * @return void
     */
    public function auto_updater() {

        if ( $this->get_license_option( 'status' ) !== 'valid' ) {
			return;
		}

		$args = array(
			'version'   => $this->version,
			'license'   => $this->get_license_option( 'key' ),
			'author'    => $this->author,
            'item_name' => $this->item_name
		);

		// Setup the updater.
		$edd_updater = new EDD_SL_Plugin_Updater(
			$this->api_url,
			$this->file,
			$args
		);    
    }
    
    /**
     * Check which license actions to take.
     * 
     * @since  2.1.0
     * @return void
     */
    public function license_actions() {

    	if ( !isset( $_POST['wpsl_licenses'] ) ) {
            return;
        }

		if ( !isset( $_POST['wpsl_licenses'][ $this->item_shortname ] ) || empty( $_POST['wpsl_licenses'][ $this->item_shortname ] ) ) {
			return;
        }
        
        if ( !check_admin_referer( $this->item_shortname . '_license-nonce', $this->item_shortname . '_license-nonce' ) ) {
            return;
        }

        if ( !current_user_can( 'manage_wpsl_settings' ) ) {
            return;
        }
        
        if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate' ] ) ) {
            $this->deactivate_license();
        } else {
            $this->activate_license();
        }
    }

    /**
     * Try to activate the license key.
     * 
     * @since  2.1.0
     * @return void
     */
    public function activate_license() {
    
        // Stop if the current license is already active. 
        if ( $this->get_license_option( 'status' ) == 'valid' ) {
			return;
		}

        $license = sanitize_text_field( $_POST['wpsl_licenses'][ $this->item_shortname ] );

		// data to send in our API request.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license' 	 => $license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url()
		);

        // Get the license data from the API.
		$license_data = $this->call_license_api( $api_params );

        if ( $license_data ) {
            update_option(
                $this->item_shortname . '_license_data',
                array(
                    'key'        => $license,
                    'expiration' => $license_data->expires,
                    'status'     => $license_data->license
                )
            );

            if ( $license_data->success ) {
                $this->set_license_notice( $this->item_name . ' license activated.', 'updated' );
            } else if ( !empty( $license_data->error ) ) {
                $this->handle_activation_errors( $license_data->error );
            }
        }
    }

    /**
     * Deactivate the license key.
     * 
     * @since  2.1.0
     * @return void
     */    
    public function deactivate_license() {

        // Data to send to the API
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license'    => $this->get_license_option( 'key' ),
            'item_name'  => urlencode( $this->item_name ),
            'url'        => home_url()
        );

        // Get the license data from the API.
		$license_data = $this->call_license_api( $api_params );
        
        if ( $license_data ) {
            if ( $license_data->license == 'deactivated' ) {
                delete_option( $this->item_shortname . '_license_data' );    
                
                $this->set_license_notice( $this->item_name . ' license deactivated.', 'updated' );
            } else {
                $message = sprintf (__( 'The %s license failed to deactivate, please try again later or contact support!', 'wpsl' ), $this->item_name );
                $this->set_license_notice( $message, 'error' );
            }
        }     
    }
    
    /**
     * Access the license API.
     * 
     * @since  2.1.0
     * @params array      $api_params   The used API parameters
     * @return void|array $license_data The returned license data on success
     */     
    public function call_license_api( $api_params ) {
        
        $response = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) ) {
            $message = $response->get_error_message() . '. ' . __( 'Please try again later!', 'wpsl' );
            $this->set_license_notice( $message, 'error' );
        } else {
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );        

            return $license_data;
        }
    }    

    /**
     * Get a single license option.
     * 
     * @since 2.1.0
     * @param string        $option Name of the license option.
     * @return void|string          The value for the license option.
     */ 
    public function get_license_option( $option ) {
        
        $license_data = get_option( $this->item_shortname . '_license_data' ); 
        
        if ( isset( $license_data[ $option ] ) ) {
            return $license_data[ $option ];   
        }
    }

    /**
     * Set a notice holding license information.
     *
     * @since 2.1.0
     * @param string $message The license message to display.
     * @param string $type    Either updated or error.
     * @return void
     */
    public function set_license_notice( $message, $type ) {
        add_settings_error( $this->item_shortname . '-license', 'license-notice', $message, $type );
    }

    /**
     * Check the different license activation errors.
     * 
     * @since 2.1.0
     * @param string $activation_errors The activation errors returned by the license API.
     * @return void
     */     
    public function handle_activation_errors( $activation_errors ) {

        switch ( $activation_errors ) {
            case 'item_name_mismatch':
                $error_msg = sprintf( __( 'The %s license key does not belong to this add-on.', 'wpsl' ), $this->item_name );
                break;
            case 'no_activations_left':
                $error_msg = sprintf( __( 'The %s license key does not have any activations left.', 'wpsl' ), $this->item_name );
                break;
            case 'expired':
                $error_msg = sprintf( __( 'The %s license key is expired. Please renew it.', 'wpsl' ), $this->item_name );
                break;
            default:
                $error_msg = sprintf( __( 'There was a problem activating the license key for the %s, please try again or contact support. Error code: %s', 'wpsl' ), $this->item_name, $activation_errors );
                break;
        }

        $this->set_license_notice( $error_msg, 'error' );
    }
        
    /**
     * Add license fields to the settings.
     * 
     * @since  2.1.0
     * @param  array $settings The existing settings.
     * @return array
     */
	public function add_license_field( $settings ) {
        
		$license_setting = array(
			array(
                'name'       => $this->item_name,
				'short_name' => $this->item_shortname,
                'status'     => $this->get_license_option( 'status' ),
                'key'        => $this->get_license_option( 'key' ),
                'expiration' => $this->get_license_option( 'expiration' )
			)
		);

		return array_merge( $settings, $license_setting );
	}
}