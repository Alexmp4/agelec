<?php

/**
 * Add WPSL Roles.
 *
 * @since 2.0.0
 * @return void
 */
function wpsl_add_roles() {
    
    global $wp_roles;

    if ( class_exists( 'WP_Roles' ) ) {
        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
    }

	if ( is_object( $wp_roles ) ) {
		add_role( 'wpsl_store_locator_manager', __( 'Store Locator Manager', 'wpsl' ), array(
			'read'                   => true,
			'edit_posts'             => true,
			'delete_posts'           => true,
			'unfiltered_html'        => true,
			'upload_files'           => true,
			'delete_others_pages'    => true,
			'delete_others_posts'    => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'edit_others_pages'      => true,
			'edit_others_posts'      => true,
			'edit_pages'             => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_published_pages'   => true,
			'edit_published_posts'   => true,
			'moderate_comments'      => true,
			'publish_pages'          => true,
			'publish_posts'          => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true
		) );
    }
}

/**
 * Add WPSL user capabilities.
 *
 * @since 2.0.0
 * @return void
 */
function wpsl_add_caps() {
    
    global $wp_roles;

    if ( class_exists( 'WP_Roles' ) ) {
        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
    }

    if ( is_object( $wp_roles ) ) {
        $wp_roles->add_cap( 'administrator', 'manage_wpsl_settings' );
        
        $capabilities = wpsl_get_post_caps();
        
        foreach ( $capabilities as $cap ) {
            $wp_roles->add_cap( 'wpsl_store_locator_manager', $cap );
            $wp_roles->add_cap( 'administrator',              $cap );
        }
    } 
}

/** 
 * Get the WPSL post type capabilities.
 * 
 * @since 2.0.0
 * @return array $capabilities The post type capabilities
 */
function wpsl_get_post_caps() {

    $capabilities = array(
        'edit_store',
        'read_store',
        'delete_store',
        'edit_stores',
        'edit_others_stores',
        'publish_stores',
        'read_private_stores',
        'delete_stores',
        'delete_private_stores',
        'delete_published_stores',
        'delete_others_stores',
        'edit_private_stores',
        'edit_published_stores'
    );
    
    return $capabilities;
}

/**
 * Remove the WPSL caps and roles.
 * 
 * Only called from uninstall.php
 *
 * @since 2.0.0
 * @return void
 */
function wpsl_remove_caps_and_roles() {
      
    global $wp_roles;

    if ( class_exists( 'WP_Roles' ) ) {
        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
    }
    
    if ( is_object( $wp_roles ) ) {
        $wp_roles->remove_cap( 'administrator', 'manage_wpsl_settings' );
        
        $capabilities = wpsl_get_post_caps();
        
        foreach ( $capabilities as $cap ) {
            $wp_roles->remove_cap( 'wpsl_store_locator_manager', $cap );
            $wp_roles->remove_cap( 'administrator',              $cap );
        }
    } 
    
    remove_role( 'wpsl_store_locator_manager' ); 
}