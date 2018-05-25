<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmAddon {
	public $store_url = 'https://formidableforms.com';
	public $download_id;
	public $plugin_file;
	public $plugin_folder;
	public $plugin_name;
	public $plugin_slug;
	public $option_name;
	public $version;
	public $author = 'Strategy11';
	private $license;
	protected $get_beta = false;

	public function __construct() {

		if ( empty( $this->plugin_slug ) ) {
			$this->plugin_slug = preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->plugin_name ) ) );
		}
		if ( empty( $this->option_name ) ) {
			$this->option_name = 'edd_' . $this->plugin_slug . '_license_';
		}

		$this->plugin_folder = plugin_basename( $this->plugin_file );
		$this->license = $this->get_license();

		add_filter( 'frm_installed_addons', array( &$this, 'insert_installed_addon' ) );
		$this->edd_plugin_updater();
	}

	public static function load_hooks() {
		add_filter( 'frm_include_addon_page', '__return_true' );
		//new static();
	}

	public function insert_installed_addon( $plugins ) {
		$plugins[ $this->plugin_slug ] = $this;
		return $plugins;
	}

	public static function get_addon( $plugin_slug ) {
		$plugins = apply_filters( 'frm_installed_addons', array() );
		$plugin = false;
		if ( isset( $plugins[ $plugin_slug ] ) ) {
			$plugin = $plugins[ $plugin_slug ];
		}
		return $plugin;
	}

	public function edd_plugin_updater() {

		$this->is_license_revoked();
		$license = $this->license;

		if ( empty( $license ) ) {
			add_action( 'after_plugin_row_' . plugin_basename( $this->plugin_file ), array( $this, 'show_license_message' ), 10, 2 );
		} else {

			// setup the updater
			$api_data = array(
				'version' => $this->version,
				'license' => $license,
				'author'  => $this->author,
				'beta'    => $this->get_beta,
			);
			if ( is_numeric( $this->download_id ) ) {
				$api_data['item_id'] = $this->download_id;
			}

			$edd = new FrmEDD_SL_Plugin_Updater( $this->store_url, $this->plugin_file, $api_data );
			if ( 'formidable/formidable.php' === $this->plugin_folder ) {
				remove_filter( 'plugins_api', array( $edd, 'plugins_api_filter' ), 10, 3 );
			}

			add_filter( 'site_transient_update_plugins', array( &$this, 'clear_expired_download' ) );
		}
	}

	public function get_license() {
		$license = trim( get_option( $this->option_name . 'key' ) );
		if ( empty( $license ) ) {
			$license = $this->activate_defined_license();
		}
		return $license;
	}

	/**
	 * Activate the license in wp-config.php
	 * @since 2.04
	 */
	public function activate_defined_license() {
		$license = $this->get_defined_license();
		if ( ! empty( $license ) && ! $this->is_active() && $this->is_time_to_auto_activate() ) {
			$response = $this->activate_license( $license );
			$this->set_auto_activate_time();
			if ( ! $response['success'] ) {
				$license = '';
			}
		}
		return $license;
	}

	/**
	 * Check the wp-config.php for the license key
	 * @since 2.04
	 */
	public function get_defined_license() {
		$consant_name = 'FRM_' . strtoupper( $this->plugin_slug ) . '_LICENSE';
		return defined( $consant_name ) ? constant( $consant_name ) : false;
	}

	public function set_license( $license ) {
		update_option( $this->option_name . 'key', $license );
	}

	/**
	 * If the license is in the config, limit the frequency of checks.
	 * The license may be entered incorrectly, so we don't want to check on every page load.
	 * @since 2.04
	 */
	private function is_time_to_auto_activate() {
		$last_try = get_option( $this->option_name . 'last_activate' );
		return ( ! $last_try || $last_try < strtotime( '-1 day' ) );
	}

	private function set_auto_activate_time() {
		update_option( $this->option_name . 'last_activate', time() );
	}

	public function is_active() {
		return get_option( $this->option_name . 'active' );
	}

	public function clear_license() {
		delete_option( $this->option_name . 'active' );
		delete_option( $this->option_name . 'key' );
		delete_site_option( $this->transient_key() );
		delete_option( $this->transient_key() );
		delete_transient( 'frm_api_licence' );
	}

	public function set_active( $is_active ) {
		update_option( $this->option_name . 'active', $is_active );
		delete_transient( 'frm_api_licence' );
		FrmAppHelper::save_combined_js();
	}

	public function show_license_message( $file, $plugin ) {
		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
		echo '<tr class="plugin-update-tr active"><td colspan="' . esc_attr( $wp_list_table->get_column_count() ) . '" class="plugin-update colspanchange"><div class="update-message">';

		/* translators: %1$s: Plugin name, %2$s: Start link HTML, %3$s: end link HTML */
		printf( esc_html__( 'Your %1$s license key is missing. Please add it on the %2$slicenses page%3$s.', 'formidable' ), esc_html( $this->plugin_name ), '<a href="' . esc_url( admin_url( 'admin.php?page=formidable-settings&t=licenses_settings' ) ) . '">', '</a>' );
		$id = sanitize_title( $plugin['Name'] );
		echo '<script type="text/javascript">var d = document.getElementById("' . esc_attr( $id ) . '");if ( d !== null ){ d.className = d.className + " update"; }</script>';
		echo '</div></td></tr>';
	}

	public function clear_expired_download( $transient ) {
		if ( ! is_object( $transient ) ) {
			return $transient;
		}

		if ( $this->is_current_version( $transient ) ) {
			//make sure it doesn't show there is an update if plugin is up-to-date
			if ( isset( $transient->response[ $this->plugin_folder ] ) ) {
				unset( $transient->response[ $this->plugin_folder ] );
			}
		} elseif ( isset( $transient->response ) && isset( $transient->response[ $this->plugin_folder ] ) ) {
			$cache_key = $this->version_cache_key();
			$version_info = get_option( $cache_key );

			$this->clear_old_plugin_version( $version_info );

			if ( is_array( $version_info ) && isset( $version_info['value'] ) ) {
				$version_info = json_decode( $version_info['value'] );
				$version_info->new_version = trim( $version_info->new_version, 'p' );
			}

			if ( false !== $version_info && version_compare( $version_info->new_version, $this->version, '>' ) ) {
				$transient->response[ $this->plugin_folder ] = $version_info;
			} else {
				delete_option( $cache_key );
				if ( ! $this->has_been_cleared() ) {
					// if the transient has expired, clear the update and trigger it again
					$this->cleared_plugins();
					$this->manually_queue_update();
				}

				unset( $transient->response[ $this->plugin_folder ] );
			}
		}

		return $transient;
	}


	/**
	 * @since 2.05.05
	 */
	private function version_cache_key() {
		$slug = basename( $this->plugin_file, '.php' );
		return md5( serialize( $slug . $this->version . $this->license . $this->get_beta ) );
	}

	/**
	 * make sure transients don't stick around on sites that
	 * don't save the transient expiration
	 *
	 * @since 2.05.05
	 */
	private function clear_old_plugin_version( &$version_info ) {
		if ( false !== $version_info ) {
			$timeout = ( isset( $version_info['timeout'] ) && ! empty( $version_info['timeout'] ) ) ? $version_info['timeout'] : 0;
			if ( empty( $timeout ) || current_time( 'timestamp' ) > $timeout ) {
				$version_info = false; // Cache is expired
			} elseif ( ( ! is_array( $version_info ) || ! isset( $version_info['value'] ) ) ) {
				$version_info = false; // the value isn't formated as expected
			}
		}
	}

	private function is_current_version( $transient ) {
		if ( empty( $transient->checked ) || ! isset( $transient->checked[ $this->plugin_folder ] ) ) {
			return false;
		}

		$response = ! isset( $transient->response ) || empty( $transient->response );
		if ( $response ) {
			return true;
		}

		return isset( $transient->response ) && isset( $transient->response[ $this->plugin_folder ] ) && $transient->checked[ $this->plugin_folder ] === $transient->response[ $this->plugin_folder ]->new_version;
	}

	private function has_been_cleared() {
		$last_cleared = get_option( 'frm_last_cleared' );
		return ( $last_cleared && $last_cleared > date( 'Y-m-d H:i:s', strtotime( '-5 minutes' ) ) );
	}

	private function cleared_plugins() {
		update_option( 'frm_last_cleared', date( 'Y-m-d H:i:s' ) );
	}

	private function is_license_revoked() {
		if ( empty( $this->license ) || empty( $this->plugin_slug ) || isset( $_POST['license'] ) ) {
			return;
		}

		if ( is_multisite() ) {
			$last_checked = get_site_option( $this->transient_key() );
		} else {
			$last_checked = get_option( $this->transient_key() );
		}

		$seven_days_ago = date( 'Y-m-d H:i:s', strtotime( '-7 days' ) );

		if ( ! $last_checked || $last_checked < $seven_days_ago ) {
			// check weekly
			if ( is_multisite() ) {
				update_site_option( $this->transient_key(), date( 'Y-m-d H:i:s' ) );
			} else {
				update_option( $this->transient_key(), date( 'Y-m-d H:i:s' ) );
			}

			$response = $this->get_license_status();
			if ( 'revoked' === $response['status'] ) {
				$this->clear_license();
			}
		}
	}

	private function transient_key() {
		return 'frm_' . md5( sanitize_key( $this->license . '_' . $this->plugin_slug ) );
	}

	public static function activate() {
		FrmAppHelper::permission_check( 'frm_change_settings' );
		check_ajax_referer( 'frm_ajax', 'nonce' );

		if ( ! isset( $_POST['license'] ) || empty( $_POST['license'] ) ) {
			wp_die( json_encode( array(
				'message' => __( 'Oops! You forgot to enter your license number.', 'formidable' ),
				'success' => false,
			) ) );
		}

		$license = stripslashes( sanitize_text_field( $_POST['license'] ) );
		$plugin_slug = sanitize_text_field( $_POST['plugin'] );
		$this_plugin = self::get_addon( $plugin_slug );
		$response = $this_plugin->activate_license( $license );

		echo json_encode( $response );
		wp_die();
	}

	private function activate_license( $license ) {
		$this->set_license( $license );
		$this->license = $license;

		$response = $this->get_license_status();
		$response['message'] = '';
		$response['success'] = false;

		if ( $response['error'] ) {
			$response['message'] = $response['status'];
		} else {
			$messages = $this->get_messages();
			if ( is_string( $response['status'] ) && isset( $messages[ $response['status'] ] ) ) {
				$response['message'] = $messages[ $response['status'] ];
			} else {
				$response['message'] = FrmAppHelper::kses( $response['status'], array( 'a' ) );
			}

			$is_valid = false;
			if ( 'valid' === $response['status'] ) {
				$is_valid = 'valid';
				$response['success'] = true;
			}
			$this->set_active( $is_valid );
		}

		return $response;
	}

	private function get_license_status() {
		$response = array(
			'status' => 'missing',
			'error'  => true,
		);
		if ( empty( $this->license ) ) {
			$response['error'] = false;
			return $response;
		}

		try {
			$response['error'] = false;
			$license_data = $this->send_mothership_request( 'activate_license' );

			// $license_data->license will be either "valid" or "invalid"
			if ( is_array( $license_data ) ) {
				if ( in_array( $license_data['license'], array( 'valid', 'invalid' ), true ) ) {
					$response['status'] = $license_data['license'];
				}
			} else {
				$response['status'] = $license_data;
			}
		} catch ( Exception $e ) {
			$response['status'] = $e->getMessage();
		}

		return $response;
	}

	private function get_messages() {
		return array(
			'valid'   => __( 'Your license has been activated. Enjoy!', 'formidable' ),
			'invalid' => __( 'That license key is invalid', 'formidable' ),
			'expired' => __( 'That license is expired', 'formidable' ),
			'revoked' => __( 'That license has been refunded', 'formidable' ),
			'no_activations_left' => __( 'That license has been used on too many sites', 'formidable' ),
			'invalid_item_id' => __( 'Oops! That is the wrong license key for this plugin.', 'formidable' ),
			'missing' => __( 'That license key is invalid', 'formidable' ),
		);
	}

	public static function deactivate() {
		FrmAppHelper::permission_check( 'frm_change_settings' );
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$plugin_slug = sanitize_text_field( $_POST['plugin'] );
		$this_plugin = self::get_addon( $plugin_slug );
		$license = $this_plugin->get_license();
		$this_plugin->license = $license;

		$response = array(
			'success' => false,
			'message' => '',
		);
		try {
			// $license_data->license will be either "deactivated" or "failed"
			$license_data = $this_plugin->send_mothership_request( 'deactivate_license' );
			if ( is_array( $license_data ) && 'deactivated' === $license_data['license'] ) {
				$response['success'] = true;
				$response['message'] = __( 'That license was removed successfully', 'formidable' );
			} else {
				$response['message'] = __( 'There was an error deactivating your license.', 'formidable' );
			}
		} catch ( Exception $e ) {
			$response['message'] = $e->getMessage();
		}

		$this_plugin->clear_license();

		echo json_encode( $response );
		wp_die();
	}

	public function send_mothership_request( $action ) {
		$api_params = array(
			'edd_action' => $action,
			'license'    => $this->license,
			'url'        => home_url(),
		);
		if ( is_numeric( $this->download_id ) ) {
			$api_params['item_id'] = absint( $this->download_id );
		} else {
			$api_params['item_name'] = rawurlencode( $this->plugin_name );
		}

		$arg_array = array(
			'body'      => $api_params,
			'timeout'   => 25,
			'sslverify' => false,
			'user-agent' => $this->plugin_slug . '/' . $this->version . '; ' . get_bloginfo( 'url' ),
		);

		$resp = wp_remote_post( $this->store_url, $arg_array );
		$body = wp_remote_retrieve_body( $resp );

		$message = __( 'Your License Key was invalid', 'formidable' );
		if ( is_wp_error( $resp ) ) {
			/* translators: %1$s: Start link HTML, %2$s: End link HTML */
			$message = sprintf( __( 'You had an error communicating with the Formidable API. %1$sClick here%2$s for more information.', 'formidable' ), '<a href="https://formidableforms.com/knowledgebase/why-cant-i-activate-formidable-pro/" target="_blank">', '</a>' );
			$message .= ' ' . $resp->get_error_message();
		} elseif ( 'error' === $body || is_wp_error( $body ) ) {
			$message = __( 'You had an HTTP error connecting to the Formidable API', 'formidable' );
		} else {
			$json_res = json_decode( $body, true );
			if ( null !== $json_res ) {
				if ( is_array( $json_res ) && isset( $json_res['error'] ) ) {
					$message = $json_res['error'];
				} else {
					$message = $json_res;
				}
			} elseif ( isset( $resp['response'] ) && isset( $resp['response']['code'] ) ) {
				/* translators: %1$s: Error code, %2$s: Error message */
				$message = sprintf( __( 'There was a %1$s error: %2$s', 'formidable' ), $resp['response']['code'], $resp['response']['message'] . ' ' . $resp['body'] );
			}
		}

		return $message;
	}

	public function manually_queue_update() {
		set_site_transient( 'update_plugins', null );
	}
}
