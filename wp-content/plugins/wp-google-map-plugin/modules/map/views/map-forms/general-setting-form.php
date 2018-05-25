<?php
/**
 * Map's general setting(s).
 * @package Maps
 */

$form->add_element( 'text', 'map_title', array(
	'lable' => __( 'Map Title', WPGMP_TEXT_DOMAIN ),
	'value' => $data['map_title'],
	'desc' => __( 'Enter here the map title.', WPGMP_TEXT_DOMAIN ),
	'required' => true,
	'placeholder' => '',
));
$form->add_element( 'text', 'map_width', array(
	'lable' => __( 'Map Width', WPGMP_TEXT_DOMAIN ),
	'value' => $data['map_width'],
	'desc' => __( 'Enter here the map width in pixel. Leave it blank for 100% width.', WPGMP_TEXT_DOMAIN ),
	'placeholder' => '',
));
$form->add_element( 'text', 'map_height', array(
	'lable' => __( 'Map Height', WPGMP_TEXT_DOMAIN ),
	'value' => $data['map_height'],
	'desc' => __( 'Enter here the map height in pixel.', WPGMP_TEXT_DOMAIN ),
	'required' => true,
	'placeholder' => '',
));

$zoom_level = array();
for ( $i = 0; $i < 20; $i++ ) {
	$zoom_level[ $i ] = $i;
}
$form->add_element( 'select', 'map_zoom_level', array(
	'lable' => __( 'Map Zoom Level', WPGMP_TEXT_DOMAIN ),
	'current' => $data['map_zoom_level'],
	'desc' => __( 'Available options 0 to 19.', WPGMP_TEXT_DOMAIN ),
	'options' => $zoom_level,
	'default_value' => 5,
));
$map_type = array( 'ROADMAP' => 'ROADMAP','SATELLITE' => 'SATELLITE','HYBRID' => 'HYBRID','TERRAIN' => 'TERRAIN' );
$form->add_element( 'select', 'map_type', array(
	'lable' => __( 'Map Type', WPGMP_TEXT_DOMAIN ),
	'current' => $data['map_type'],
	'options' => $map_type,
));

$form->add_element( 'checkbox', 'map_scrolling_wheel', array(
	'lable' => __( 'Turn Off Scrolling Wheel', WPGMP_TEXT_DOMAIN ),
	'value' => 'false',
	'id' => 'wpgmp_map_scrolling_wheel',
	'current' => $data['map_scrolling_wheel'],
	'desc' => __( 'Please check to disable scroll wheel zoom.', WPGMP_TEXT_DOMAIN ),
	'class' => 'chkbox_class ',
));
$form->add_element( 'checkbox', 'map_all_control[map_draggable]', array(
	'lable' => __( 'Map Draggable', WPGMP_TEXT_DOMAIN ),
	'value' => 'false',
	'id' => 'wpgmp_map_draggable',
	'current' => $data['map_all_control']['map_draggable'],
	'desc' => __( 'Please check to disable map draggable.', WPGMP_TEXT_DOMAIN ),
	'class' => 'chkbox_class',
));

$form->add_element( 'checkbox', 'map_45imagery', array(
	'lable' => __( '45&deg; Imagery', WPGMP_TEXT_DOMAIN ),
	'value' => '45',
	'id' => 'wpgmp_map_45imagery',
	'current' => $data['map_45imagery'],
	'desc' => __( 'Apply 45&deg; Imagery ? (only available for map type SATELLITE and HYBRID).', WPGMP_TEXT_DOMAIN ),
	'class' => 'chkbox_class',
));

