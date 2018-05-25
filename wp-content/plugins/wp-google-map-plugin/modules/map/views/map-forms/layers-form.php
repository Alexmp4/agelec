<?php
/**
 * Contro Positioning over google maps.
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element( 'group', 'map_control_settings', array(
	'value' => __( 'Infowindow Settings', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
));
$url = admin_url( 'admin.php?page=wpgmp_how_overview' );
$link =  __( 'Enter placeholders {marker_title},{marker_address},{marker_message},{marker_latitude},{marker_longitude}.',WPGMP_TEXT_DOMAIN);


$default_value = '';

$default_value = (isset( $data['map_all_control']['infowindow_setting'] ) and '' != $data['map_all_control']['infowindow_setting'] ) ? $data['map_all_control']['infowindow_setting'] : $default_value;

$form->add_element( 'textarea', 'map_all_control[infowindow_setting]', array(
	'lable' => __( 'Infowindow Message', WPGMP_TEXT_DOMAIN ),
	'value' => $data['map_all_control']['infowindow_setting'],
	'desc' => $link,
	'textarea_rows' => 10,
	'textarea_name' => 'location_messages',
	'class' => 'form-control',
	'id' => 'googlemap_infomessage',
	'default_value' => $default_value,
));


if ( 'mouseclick' == $data['map_all_control']['infowindow_openoption'] ) {
	$data['map_all_control']['infowindow_openoption'] = 'click'; } else if ( 'mousehover' == $data['map_all_control']['infowindow_openoption'] ) {
	$data['map_all_control']['infowindow_openoption'] = 'mouseover'; }
	$event = array( 'click' => 'Mouse Click', 'mouseover' => 'Mouse Hover' );
	$form->add_element( 'select', 'map_all_control[infowindow_openoption]', array(
		'lable' => __( 'Show Infowindow on', WPGMP_TEXT_DOMAIN ),
		'current' => $data['map_all_control']['infowindow_openoption'],
		'desc' => __( 'Open infowindow on Mouse Click or Mouse Hover.', WPGMP_TEXT_DOMAIN ),
		'options' => $event,
	));

	$form->add_element('image_picker', 'map_all_control[marker_default_icon]', array(
		'lable' => __( 'Choose Marker Image', WPGMP_TEXT_DOMAIN ),
		'src' => (isset( $data['map_all_control']['marker_default_icon'] )  ? wp_unslash( $data['map_all_control']['marker_default_icon'] ) : WPGMP_IMAGES.'/default_marker.png'),
		'required' => false,
		'choose_button' => __( 'Choose', WPGMP_TEXT_DOMAIN ),
		'remove_button' => __( 'Remove',WPGMP_TEXT_DOMAIN ),
		'id' => 'marker_category_icon',
	));

	$form->add_element( 'checkbox', 'map_all_control[infowindow_open]', array(
		'lable' => __( 'InfoWindow Open', WPGMP_TEXT_DOMAIN ),
		'value' => 'true',
		'id' => 'wpgmp_infowindow_open',
		'current' => $data['map_all_control']['infowindow_open'],
		'desc' => __( 'Please check to enable infowindow default open.', WPGMP_TEXT_DOMAIN ),
		'class' => 'chkbox_class',
	));

	$form->add_element( 'checkbox', 'map_all_control[infowindow_close]', array(
		'lable' => __( 'Close InfoWindow', WPGMP_TEXT_DOMAIN ),
		'value' => 'true',
		'id' => 'wpgmp_infowindow_close',
		'current' => $data['map_all_control']['infowindow_close'],
		'desc' => __( 'Please check to close infowindow on map click.', WPGMP_TEXT_DOMAIN ),
		'class' => 'chkbox_class',
	));

	$event = array( '' => __( 'Select Animation',WPGMP_TEXT_DOMAIN ),'click' => __( 'Mouse Click',WPGMP_TEXT_DOMAIN ), 'mouseover' => __( 'Mouse Hover',WPGMP_TEXT_DOMAIN ) );
	$form->add_element( 'select', 'map_all_control[infowindow_bounce_animation]', array(
		'lable' => __( 'Bounce Animation', WPGMP_TEXT_DOMAIN ),
		'current' => $data['map_all_control']['infowindow_bounce_animation'],
		'desc' => __( 'Apply bounce animation on mousehover or mouse click. BOUNCE indicates that the marker should bounce in place.', WPGMP_TEXT_DOMAIN ),
		'options' => $event,
	));

	$form->add_element( 'checkbox', 'map_all_control[infowindow_drop_animation]', array(
		'lable' => __( 'Apply Drop Animation', WPGMP_TEXT_DOMAIN ),
		'value' => 'true',
		'id' => 'infowindow_drop_animation',
		'current' => $data['map_all_control']['infowindow_drop_animation'],
		'desc' => __( 'DROP indicates that the marker should drop from the top of the map. ', WPGMP_TEXT_DOMAIN ),
		'class' => 'chkbox_class',
	));


	$form->add_element( 'group', 'map_control_layers', array(
		'value' => __( 'Layers Settings', WPGMP_TEXT_DOMAIN ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	));

	$form->add_element( 'checkbox', 'map_layer_setting[choose_layer][traffic_layer]', array(
		'lable' => __( 'Traffic Layer', WPGMP_TEXT_DOMAIN ),
		'value' => 'TrafficLayer',
		'id' => 'wpgmp_traffic_layer',
		'current' => $data['map_layer_setting']['choose_layer']['traffic_layer'],
		'desc' => __( 'Please check to enable traffic Layer.', WPGMP_TEXT_DOMAIN ),
		'class' => 'chkbox_class',
	));

	$form->add_element( 'checkbox', 'map_layer_setting[choose_layer][transit_layer]', array(
		'lable' => __( 'Transit Layer', WPGMP_TEXT_DOMAIN ),
		'value' => 'TransitLayer',
		'id' => 'wpgmp_transit_layer',
		'current' => $data['map_layer_setting']['choose_layer']['transit_layer'],
		'desc' => __( 'Please check to enable Transit Layer.', WPGMP_TEXT_DOMAIN ),
		'class' => 'chkbox_class',
	));


	$form->add_element( 'checkbox', 'map_layer_setting[choose_layer][bicycling_layer]', array(
		'lable' => __( 'Bicycling Layer', WPGMP_TEXT_DOMAIN ),
		'value' => 'BicyclingLayer',
		'id' => 'wpgmp_bicycling_layer',
		'current' => $data['map_layer_setting']['choose_layer']['bicycling_layer'],
		'desc' => __( 'Please check to enable Bicycling Layer.', WPGMP_TEXT_DOMAIN ),
		'class' => 'chkbox_class',
	));
