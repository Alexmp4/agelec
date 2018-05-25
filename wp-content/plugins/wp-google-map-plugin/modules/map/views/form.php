<?php
/**
 * Template for Add & Edit Map
 * @author  Flipper Code <hello@flippercode.com>
 * @package Maps
 */

if ( isset( $_REQUEST['_wpnonce'] ) ) {

	$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

		die( 'Cheating...' );

	} else {
		$data = $_POST;
	}
}
global $wpdb;
$modelFactory = new WPGMP_Model();
$map_obj = $modelFactory->create_object( 'map' );
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['map_id'] ) ) {
	$map_obj = $map_obj->fetch( array( array( 'map_id', '=', intval( wp_unslash( $_GET['map_id'] ) ) ) ) );
	$map = $map_obj[0];
	if(!empty($map)) {
		$map->map_street_view_setting = unserialize( $map->map_street_view_setting );
		$map->map_route_direction_setting = unserialize( $map->map_route_direction_setting );
		$map->map_all_control = unserialize( $map->map_all_control );
		$map->map_info_window_setting = unserialize( $map->map_info_window_setting );
		$map->style_google_map = unserialize( $map->style_google_map );
		$map->map_locations = unserialize( $map->map_locations );
		$map->map_layer_setting = unserialize( $map->map_layer_setting );
		$map->map_polygon_setting = unserialize( $map->map_polygon_setting );
		$map->map_polyline_setting = unserialize( $map->map_polyline_setting );
		$map->map_cluster_setting = unserialize( $map->map_cluster_setting );
		$map->map_overlay_setting = unserialize( $map->map_overlay_setting );
		$map->map_infowindow_setting = unserialize( $map->map_infowindow_setting );
		$map->map_geotags = unserialize( $map->map_geotags );
	}
	
	$data = (array) $map;
} elseif ( ! isset( $_GET['doaction'] ) and isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $data );
}

$form  = new WPGMP_Template();
$form->set_header( __( 'Map Information', WPGMP_TEXT_DOMAIN ), $response, __( 'Manage Maps', WPGMP_TEXT_DOMAIN ), 'wpgmp_manage_map' );

if( get_option( 'wpgmp_api_key' ) == '' ) {

$form->add_element( 'message', 'wpgmp_key_required', array(
	'value' => __( 'Google Maps API Key is missing. Follow instructions to <a target="_blank" href="http://bit.ly/292gCV2">create google maps api key </a> and then insert your key <a target="_blank" href="'.admin_url( 'admin.php?page=wpgmp_manage_settings' ).'">here</a>.',WPGMP_TEXT_DOMAIN ),
	'class' => 'fc-msg fc-danger',
	'before' => '<div class="fc-12 wpgmp_key_required">',
	'after' => '</div>',
));

}

include( 'map-forms/general-setting-form.php' );
include( 'map-forms/map-center-settings.php' );
include( 'map-forms/locations-form.php' );
include( 'map-forms/control-setting-form.php' );
include( 'map-forms/control-position-style-form.php' );
include( 'map-forms/layers-form.php' );
include( 'map-forms/street-view-setting-form.php' );
include( 'map-forms/extensible-settings.php' );
$form->add_element('extensions','wpgmp_map_form',array(
	'value' => $data,
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	));
$form->add_element( 'submit', 'save_entity_data', array(
	'value' => __( 'Save Map',WPGMP_TEXT_DOMAIN ),
));
$form->add_element( 'hidden', 'operation', array(
	'value' => 'save',
));
$form->add_element( 'hidden', 'map_locations', array(
	'value' => '',
));
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['map_id'] ) ) {

	$form->add_element( 'hidden', 'entityID', array(
		'value' => intval( wp_unslash( $_GET['map_id'] ) ),
	));
}
$form->render();
