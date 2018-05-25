<?php
/**
 * Contro Positioning over google maps.
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$positions = array(
'TOP_LEFT' => 'Top Left',
'TOP_RIGHT' => 'Top Right',
'LEFT_TOP' => 'Left Top',
'RIGHT_TOP' => 'Right Top',
'TOP_CENTER' => 'Top Center',
'LEFT_CENTER' => 'Left Center',
'RIGHT_CENTER' => 'Right Center',
'BOTTOM_RIGHT' => 'Bottom Right',
'LEFT_BOTTOM' => 'Left Bottom',
'RIGHT_BOTTOM' => 'Right Bottom',
'BOTTOM_CENTER' => 'Bottom Center',
'BOTTOM_LEFT' => 'Bottom Left',
'BOTTOM_RIGHT' => 'Bottom Right',
);
$form->add_element( 'group', 'map_control_position_setting', array(
	'value' => __( 'Control Position(s) Settings', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
));

$form->add_element( 'select', 'map_all_control[zoom_control_position]', array(
	'lable' => __( 'Zoom Control', WPGMP_TEXT_DOMAIN ),
	'current' => $data['map_all_control']['zoom_control_position'],
	'desc' => __( 'Please select position of zoom control.', WPGMP_TEXT_DOMAIN ),
	'options' => $positions,
));
$zoom_control_style = array( 'LARGE' => 'Large','SMALL' => 'Small' );
$form->add_element( 'select', 'map_all_control[zoom_control_style]', array(
	'lable' => __( 'Zoom Control Style', WPGMP_TEXT_DOMAIN ),
	'current' => $data['map_all_control']['zoom_control_style'],
	'desc' => __( 'Please select style of zoom control.', WPGMP_TEXT_DOMAIN ),
	'options' => $zoom_control_style,
));

$form->add_element( 'select', 'map_all_control[map_type_control_position]', array(
	'lable' => __( 'Map Type Control', WPGMP_TEXT_DOMAIN ),
	'default_value' => 'TOP_RIGHT',
	'current' => $data['map_all_control']['map_type_control_position'],
	'desc' => __( 'Please select position of map type control.', WPGMP_TEXT_DOMAIN ),
	'options' => $positions,
));


$map_type_control_style = array( 'HORIZONTAL_BAR' => 'Horizontal Bar', 'DROPDOWN_MENU' => 'Dropdown Menu' );
$form->add_element( 'select', 'map_all_control[map_type_control_style]', array(
	'lable' => __( 'Map Type Control Style', WPGMP_TEXT_DOMAIN ),
	'current' => $data['map_all_control']['map_type_control_style'],
	'desc' => __( 'Please select style of map type control.', WPGMP_TEXT_DOMAIN ),
	'options' => $map_type_control_style,
));


$form->add_element( 'select', 'map_all_control[full_screen_control_position]', array(
	'lable' => __( 'Full Screen Control', WPGMP_TEXT_DOMAIN ),
	'default_value' => 'TOP_RIGHT',
	'current' => $data['map_all_control']['full_screen_control_position'],
	'desc' => __( 'Please select position of full screen control.', WPGMP_TEXT_DOMAIN ),
	'options' => $positions,
));

$form->add_element( 'select', 'map_all_control[street_view_control_position]', array(
	'lable' => __( 'Street View Control', WPGMP_TEXT_DOMAIN ),
	'current' => $data['map_all_control']['street_view_control_position'],
	'desc' => __( 'Please select position of street view control.', WPGMP_TEXT_DOMAIN ),
	'options' => $positions,
));
