<?php
/**
 * Map's Center Location setting(s).
 * @package Maps
 */

$form->add_element( 'group', 'map_center_setting', array(
	'value' => __( 'Map\'s Center', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
));

$form->add_element( 'text', 'map_all_control[map_center_latitude]', array(
	'lable' => __( 'Center Latitude', WPGMP_TEXT_DOMAIN ),
	'value' => $data['map_all_control']['map_center_latitude'],
	'desc' => __( 'Enter here the center latitude.', WPGMP_TEXT_DOMAIN ),
	'placeholder' => '',
));
$form->add_element( 'text', 'map_all_control[map_center_longitude]', array(
	'lable' => __( 'Center Longitude', WPGMP_TEXT_DOMAIN ),
	'value' => $data['map_all_control']['map_center_longitude'],
	'desc' => __( 'Enter here the center longitude.', WPGMP_TEXT_DOMAIN ),
	'placeholder' => '',
));