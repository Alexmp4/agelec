<?php

class FrmSettingsController {

    public static function menu() {
		// Make sure admins can see the menu items
		FrmAppHelper::force_capability( 'frm_change_settings' );

        add_submenu_page( 'formidable', 'Formidable | ' . __( 'Global Settings', 'formidable' ), __( 'Global Settings', 'formidable' ), 'frm_change_settings', 'formidable-settings', 'FrmSettingsController::route' );
    }

    public static function license_box() {
		$a = FrmAppHelper::simple_get( 't', 'sanitize_title', 'general_settings' );
        include( FrmAppHelper::plugin_path() . '/classes/views/frm-settings/license_box.php' );
    }

    public static function display_form( $errors = array(), $message = '' ) {
        global $frm_vars;

        $frm_settings = FrmAppHelper::get_settings();
        $frm_roles = FrmAppHelper::frm_capabilities();

        $uploads = wp_upload_dir();
        $target_path = $uploads['basedir'] . '/formidable/css';

		$sections = self::get_settings_tabs();

		$captcha_lang = FrmAppHelper::locales( 'captcha' );

		require( FrmAppHelper::plugin_path() . '/classes/views/frm-settings/form.php' );
	}

	private static function get_settings_tabs() {
		$sections = array();
		if ( apply_filters( 'frm_include_addon_page', false ) ) {
			$sections['licenses'] = array(
				'class'    => 'FrmAddonsController',
				'function' => 'license_settings',
				'name'     => __( 'Plugin Licenses', 'formidable' ),
				'ajax'     => true,
			);
		}
		$sections = apply_filters( 'frm_add_settings_section', $sections );

		return $sections;
	}

	public static function load_settings_tab() {
		FrmAppHelper::permission_check( 'frm_change_settings' );
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$section = FrmAppHelper::get_post_param( 'tab', '', 'sanitize_text_field' );
		$sections = self::get_settings_tabs();
		if ( ! isset( $sections[ $section ] ) ) {
			wp_die();
		}

		$section = $sections[ $section ];

		if ( isset( $section['class'] ) ) {
			call_user_func( array( $section['class'], $section['function'] ) );
		} else {
			call_user_func( ( isset( $section['function'] ) ? $section['function'] : $section ) );
		}
		wp_die();
	}

    public static function process_form( $stop_load = false ) {
        global $frm_vars;

        $frm_settings = FrmAppHelper::get_settings();

		$process_form = FrmAppHelper::get_post_param( 'process_form', '', 'sanitize_text_field' );
		if ( ! wp_verify_nonce( $process_form, 'process_form_nonce' ) ) {
            wp_die( $frm_settings->admin_permission );
        }

        $errors = array();
        $message = '';

        if ( ! isset( $frm_vars['settings_routed'] ) || ! $frm_vars['settings_routed'] ) {
            $errors = $frm_settings->validate( $_POST, array() );

            $frm_settings->update( stripslashes_deep( $_POST ) );

            if ( empty( $errors ) ) {
                $frm_settings->store();
                $message = __( 'Settings Saved', 'formidable' );
            }
        } else {
            $message = __( 'Settings Saved', 'formidable' );
        }

		if ( $stop_load == 'stop_load' ) {
            $frm_vars['settings_routed'] = true;
            return;
        }

        self::display_form( $errors, $message );
    }

    public static function route( $stop_load = false ) {
        $action = isset( $_REQUEST['frm_action'] ) ? 'frm_action' : 'action';
		$action = FrmAppHelper::get_param( $action, '', 'get', 'sanitize_title' );
        if ( $action == 'process-form' ) {
			self::process_form( $stop_load );
        } else if ( $stop_load != 'stop_load' ) {
			self::display_form();
        }
    }
}
