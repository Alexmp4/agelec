<?php
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Add WPSL CSV Manager Roles.
 *
 * @since 1.1.0
 * @return void
 */
function wpsl_csv_create_roles() {
    
    global $wp_roles;

    if ( class_exists( 'WP_Roles' ) ) {
        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
    }

	if ( is_object( $wp_roles ) ) {
        $capabilities = wpsl_csv_get_post_caps();

        foreach ( $capabilities as $cap ) {
            $wp_roles->add_cap( 'wpsl_store_locator_manager', $cap );
            $wp_roles->add_cap( 'administrator',              $cap );
        }
    }
}

/**
 * Get the WPSL CSV Manager capabilities.
 *
 * @since 1.1.0
 * @return array $capabilities The CSV Manager capabilities
 */
function wpsl_csv_get_post_caps() {

    $capabilities = array(
        'wpsl_csv_manager',
        'wpsl_csv_manager_export',
        'wpsl_csv_manager_tools'
    );

    return $capabilities;
}

/**
 * Remove the WPSL caps and roles.
 * 
 * Only called from uninstall.php
 *
 * @since 1.1.0
 * @return void
 */
function wpsl_csv_remove_caps_and_roles() {
      
    global $wp_roles;

    if ( class_exists( 'WP_Roles' ) ) {
        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
    }
    
    if ( is_object( $wp_roles ) ) {
        $capabilities = wpsl_csv_get_post_caps();
        
        foreach ( $capabilities as $cap ) {
            $wp_roles->remove_cap( 'wpsl_store_locator_manager', $cap );
            $wp_roles->remove_cap( 'administrator',              $cap );
        }
    }
}