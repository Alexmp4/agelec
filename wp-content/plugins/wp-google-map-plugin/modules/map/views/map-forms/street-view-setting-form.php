<?php
/**
 * Contro Positioning over google maps.
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element( 'group', 'map_street_view_setting', array(
	'value' => __( 'Street View Settings', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
));

$form->add_element( 'checkbox', 'map_street_view_setting[street_control]', array(
	'lable' => __( 'Turn On Street View', WPGMP_TEXT_DOMAIN ),
	'value' => 'true',
	'id' => 'wpgmp_street_control',
	'current' => $data['map_street_view_setting']['street_control'],
	'desc' => __( 'Please check to enable Street View control.', WPGMP_TEXT_DOMAIN ),
	'class' => 'chkbox_class switch_onoff',
	'data' => array( 'target' => '.street_view_setting' ),
));

$form->add_element( 'checkbox', 'map_street_view_setting[street_view_close_button]', array(
	'lable' => __( 'Turn On Close Button.', WPGMP_TEXT_DOMAIN ),
	'value' => 'true',
	'id' => 'wpgmp_street_view_close_button',
	'current' => $data['map_street_view_setting']['street_view_close_button'],
	'desc' => __( 'Please check to enable Street View control.', WPGMP_TEXT_DOMAIN ),
	'data' => array( 'target' => '#geo_tags_table,#geo_tags_message' ),
	'class' => 'street_view_setting',
	'show' => 'false',
));

$form->add_element( 'checkbox', 'map_street_view_setting[links_control]', array(
	'lable' => __( 'Turn Off links Control.', WPGMP_TEXT_DOMAIN ),
	'value' => 'false',
	'id' => 'wpgmp_links_control',
	'current' => $data['map_street_view_setting']['links_control'],
	'desc' => __( 'Please check to disable links control.', WPGMP_TEXT_DOMAIN ),
	'data' => array( 'target' => '#geo_tags_table,#geo_tags_message' ),
	'class' => 'street_view_setting',
	'show' => 'false',
));

$form->add_element( 'checkbox', 'map_street_view_setting[street_view_pan_control]', array(
	'lable' => __( 'Turn Off links Control.', WPGMP_TEXT_DOMAIN ),
	'value' => 'false',
	'id' => 'wpgmp_street_view_pan_control',
	'current' => $data['map_street_view_setting']['street_view_pan_control'],
	'desc' => __( 'Please check to disable Street View Pan control.', WPGMP_TEXT_DOMAIN ),
	'data' => array( 'target' => '#geo_tags_table,#geo_tags_message' ),
	'class' => 'street_view_setting',
	'show' => 'false',
));

$form->add_element( 'text', 'map_street_view_setting[pov_heading]', array(
	'lable' => __( 'POV Heading', WPGMP_TEXT_DOMAIN ),
	'value' => $data['map_street_view_setting']['pov_heading'],
	'id' => 'pov_heading',
	'desc' => __( 'Please enter POV heading.', WPGMP_TEXT_DOMAIN ),
	'class' => 'form-control street_view_setting',
	'show' => 'false',
));

$form->add_element( 'text', 'map_street_view_setting[pov_heading]', array(
	'lable' => __( 'POV Heading', WPGMP_TEXT_DOMAIN ),
	'value' => $data['map_street_view_setting']['pov_heading'],
	'id' => 'pov_heading',
	'desc' => __( 'Please enter POV heading.', WPGMP_TEXT_DOMAIN ),
	'class' => 'form-control street_view_setting',
	'show' => 'false',
));


$form->add_element( 'text', 'map_street_view_setting[pov_pitch]', array(
	'lable' => __( 'POV Pitch', WPGMP_TEXT_DOMAIN ),
	'value' => $data['map_street_view_setting']['pov_pitch'],
	'id' => 'pov_heading',
	'desc' => __( 'Please enter POV Pitch.', WPGMP_TEXT_DOMAIN ),
	'class' => 'form-control street_view_setting',
	'show' => 'false',
));
