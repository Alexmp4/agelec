<?php
/**
 * Template for Add & Edit Location
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
$category_obj = $modelFactory->create_object( 'group_map' );
$categories = $category_obj->fetch();
if ( is_array( $categories ) and ! empty( $categories ) ) {
	$all_categories = array();
	foreach ( $categories as $category ) {
		$all_categories [ $category->group_map_id ] = $category;
	}
}
$location_obj = $modelFactory->create_object( 'location' );
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['location_id'] ) ) {
	$location_obj = $location_obj->fetch( array( array( 'location_id', '=', intval( wp_unslash( $_GET['location_id'] ) ) ) ) );
	$data = (array) $location_obj[0];
} elseif ( ! isset( $_GET['doaction'] ) and isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $data );
}
$form  = new WPGMP_Template();
$form->set_header( __( 'Location Information', WPGMP_TEXT_DOMAIN ), $response, __( 'Manage Locations', WPGMP_TEXT_DOMAIN ), 'wpgmp_manage_location' );

if( get_option( 'wpgmp_api_key' ) == '' ) {

$form->add_element( 'message', 'wpgmp_key_required', array(
	'value' => __( 'Google Maps API Key is missing. Follow instructions to <a target="_blank" href="http://bit.ly/29Rlmfc">create google maps api key </a> and then insert your key <a target="_blank" href="'.admin_url( 'admin.php?page=wpgmp_manage_settings' ).'">here</a>.',WPGMP_TEXT_DOMAIN ),
	'class' => 'fc-msg fc-danger',
	'before' => '<div class="fc-12 wpgmp_key_required">',
	'after' => '</div>',
));

}


$form->add_element( 'text', 'location_title', array(
	'lable' => __( 'Location Title', WPGMP_TEXT_DOMAIN ),
	'value' => (isset( $data['location_title'] ) and ! empty( $data['location_title'] )) ? $data['location_title'] : '',
	'required' => true,
	'placeholder' => __( 'Enter Location Title', WPGMP_TEXT_DOMAIN ),
));

$form->add_element( 'text', 'location_address', array(
	'lable' => __( 'Location Address', WPGMP_TEXT_DOMAIN ),
	'value' => (isset( $data['location_address'] ) and ! empty( $data['location_address'] )) ? $data['location_address'] : '',
	'desc' => __( 'Enter here the address. Google auto suggest helps you to choose one.', WPGMP_TEXT_DOMAIN ),
	'required' => true,
	'class' => 'form-control wpgmp_auto_suggest',
	'placeholder' => __( 'Type Location Address', WPGMP_TEXT_DOMAIN ),
));
$form->set_col( 2 );
$form->add_element( 'text', 'location_latitude', array(
	'lable' => __( 'Latitude and Longitude', WPGMP_TEXT_DOMAIN ),
	'value' => (isset( $data['location_latitude'] ) and ! empty( $data['location_latitude'] )) ? $data['location_latitude'] : '',
	'id' => 'googlemap_latitude',
	'required' => true,
	'class' => 'google_latitude form-control',
	'placeholder' => __( 'Latitude', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->add_element( 'text', 'location_longitude', array(
	'value' => (isset( $data['location_longitude'] ) and ! empty( $data['location_longitude'] )) ? $data['location_longitude'] : '',
	'id' => 'googlemap_longitude',
	'required' => true,
	'class' => 'google_longitude form-control',
	'placeholder' => __( 'Longitude', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->add_element( 'text', 'location_city', array(
	'lable' => __( 'City and State', WPGMP_TEXT_DOMAIN ),
	'value' => (isset( $data['location_city'] ) and ! empty( $data['location_city'] )) ? $data['location_city'] : '',
	'id' => 'googlemap_city',
	'class' => 'google_city form-control',
	'placeholder' => __( 'City', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->add_element( 'text', 'location_state', array(
	'value' => (isset( $data['location_state'] ) and ! empty( $data['location_state'] )) ? $data['location_state'] : '',
	'id' => 'googlemap_state',
	'class' => 'google_state form-control',
	'placeholder' => __( 'State', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->add_element( 'text', 'location_country', array(
	'lable' => __( 'Country and Postal Code', WPGMP_TEXT_DOMAIN ),
	'value' => (isset( $data['location_country'] ) and ! empty( $data['location_country'] )) ? $data['location_country'] : '',
	'id' => 'googlemap_country',
	'class' => 'google_country form-control',
	'placeholder' => __( 'Country', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->add_element( 'text', 'location_postal_code', array(
	'value' => (isset( $data['location_postal_code'] ) and ! empty( $data['location_postal_code'] )) ? $data['location_postal_code'] : '',
	'id' => 'googlemap_postal_code',
	'class' => 'google_postal_code form-control',
	'placeholder' => __( 'Postal Code', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->set_col( 1 );
$form->add_element( 'div', 'wpgmp_map', array(
	'lable' => __( 'Current Location', WPGMP_TEXT_DOMAIN ),
	'id' => 'wpgmp_map',
	'style' => array( 'width' => '100%' ,'height' => '300px' ),
));


$form->add_element( 'radio', 'location_settings[onclick]', array(
	'lable' => __( 'On Click', WPGMP_TEXT_DOMAIN ),
	'radio-val-label' => array( 'marker' => __( 'Display Infowindow',WPGMP_TEXT_DOMAIN ),'custom_link' => __( 'Redirect',WPGMP_TEXT_DOMAIN ) ),
	'current' => $data['location_settings']['onclick'],
	'class' => 'chkbox_class switch_onoff',
	'default_value' => 'marker',
	'data' => array( 'target' => '.wpgmp_location_onclick' ),
));


$form->add_element( 'textarea', 'location_messages', array(
	'lable' => __( 'Infowindow Message', WPGMP_TEXT_DOMAIN ),
	'value' => (isset( $data['location_messages'] ) and ! empty( $data['location_messages'] )) ?  $data['location_messages']  : '',
	'desc' => __( 'Enter here the infoWindow message.', WPGMP_TEXT_DOMAIN ),
	'textarea_rows' => 10,
	'textarea_name' => 'location_messages',
	'class' => 'form-control wpgmp_location_onclick wpgmp_location_onclick_marker',
	'id' => 'googlemap_infomessage',
	'show' => 'false',
));

$form->add_element( 'text', 'location_settings[redirect_link]', array(
	'lable' => __( 'Redirect Url',WPGMP_TEXT_DOMAIN ),
	'value' => $data['location_settings']['redirect_link'],
	'desc' => __( 'Enter here the redirect url. e.g http://www.flippercode.com', WPGMP_TEXT_DOMAIN ),
	'class' => 'wpgmp_location_onclick_custom_link wpgmp_location_onclick form-control',
	'before' => '<div class="fc-8">',
	'after' => '</div>',
	'show' => 'false',
));

$form->add_element( 'select', 'location_settings[redirect_link_window]', array(
	'options' => array( 'yes' => __( 'YES',WPGMP_TEXT_DOMAIN ), 'no' => __( 'NO',WPGMP_TEXT_DOMAIN ) ),
	'lable' => __( 'Open new tab',WPGMP_TEXT_DOMAIN ),
	'current' => $data['location_settings']['redirect_link_window'],
	'desc' => __( 'Open a new window tab.', WPGMP_TEXT_DOMAIN ),
	'class' => 'wpgmp_location_onclick_redirect wpgmp_location_onclick form-control',
	'before' => '<div class="fc-2">',
	'after' => '</div>',
	'show' => 'false',
));


$form->add_element( 'checkbox', 'location_infowindow_default_open', array(
	'lable' => __( 'Infowindow Default Open', WPGMP_TEXT_DOMAIN ),
	'value' => 'true',
	'id' => 'location_infowindow_default_open',
	'current' => $data['location_infowindow_default_open'],
	'desc' => __( 'Check to enable infowindow default open.', WPGMP_TEXT_DOMAIN ),
	'class' => 'chkbox_class',
));
$form->add_element( 'checkbox', 'location_draggable', array(
	'lable' => __( 'Marker Draggable', WPGMP_TEXT_DOMAIN ),
	'value' => 'true',
	'id' => 'location_draggable',
	'current' => $data['location_draggable'],
	'desc' => __( 'Check if you want to allow visitors to drag the marker.', WPGMP_TEXT_DOMAIN ),
	'class' => 'chkbox_class',
));
$form->add_element( 'select', 'location_animation', array(
	'lable' => __( 'Marker Animation', WPGMP_TEXT_DOMAIN ),
	'current' => (isset( $data['location_animation'] ) and ! empty( $data['location_animation'] )) ? $data['location_animation'] : '',
	'options' => array( 'BOUNCE' => 'Bounce', 'DROP' => 'DROP' ),
	'before' => '<div class="fc-3">',
	'after' => '</div>',
));

$form->add_element( 'group', 'marker_category_listing', array(
	'value' => __( 'Marker Categories', WPGMP_TEXT_DOMAIN ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
));

if ( ! empty( $all_categories ) ) {
	$category_data = array();
	$parent_category_data = array();
	if ( ! $data['location_group_map'] ) {
		$data['location_group_map'] = array(); }
	foreach ( $categories as $category ) {
		if ( is_null( $category->group_parent ) or 0 == $category->group_parent ) {
			$parent_category_data = ' ---- ';
		} else {
			$parent_category_data = $all_categories[ $category->group_parent ]->group_map_title;
		}
		if ( '' != $category->group_marker ) {
			$icon_src = "<img src='".$category->group_marker."' />";
		} else {
			$icon_src = "<img src='".WPGMP_IMAGES."default_marker.png' />";

		}
		$select_input = $form->field_checkbox('location_group_map[]',array(
			'value' => $category->group_map_id,
			'current' => (in_array( $category->group_map_id, $data['location_group_map'] ) ? $category->group_map_id : ''),
			'class' => 'chkbox_class',
			'before' => '<div class="fc-1">',
			'after' => '</div>',
			));
		$category_data[] = array( $select_input,$category->group_map_title,$parent_category_data,$icon_src );
	}
	$category_data = $form->add_element( 'table', 'location_group_map', array(
		'heading' => array( 'Select','Category','Parent','Icon' ),
		'data' => $category_data,
		'class' => 'fc-table fc-table-layout3',
		'before' => '<div class="fc-12">',
		'after' => '</div>',
		));
} else {
	$form->add_element( 'message', 'message', array(
		'value' => __( 'You don\'t have categorie(s).', WPGMP_TEXT_DOMAIN ),
		'class' => 'fc-msg',
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	));
}

$form->add_element('extensions','wpgmp_location_form',array(
	'value' => $data['location_settings']['extensions_fields'],
	'before' => '<div class="fc-11">',
	'after' => '</div>',
	));

$form->add_element( 'submit', 'save_entity_data', array(
	'value' => __( 'Save Location',WPGMP_TEXT_DOMAIN ),
));
$form->add_element( 'hidden', 'operation', array(
	'value' => 'save',
));
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] ) {

	$form->add_element( 'hidden', 'entityID', array(
		'value' => intval( wp_unslash( $_GET['location_id'] ) ),
	));
}
$form->render();
$infowindow_message  = (isset( $data['location_messages'] ) and ! empty( $data['location_messages'] )) ? $data['location_messages'] : '';
$infowindow_disable = (isset( $data['location_settings'] ) and ! empty( $data['location_settings'] )) ? $data['location_settings'] : '';
$category_obj = $category_obj->get( array( array( 'group_map_id', '=', intval( wp_unslash( $_GET['group_map_id'] ) ) ) ) );
$category = (array) $category_obj[0];

if ( ! empty( $category->group_marker ) ) {
	$category_group_marker = $category->group_marker;
} else {
	$category_group_marker = WPGMP_IMAGES.'default_marker.png';
}
$map_data['map_options'] = array(
'center_lat'  => (isset( $data['location_latitude'] ) and ! empty( $data['location_latitude'] )) ? $data['location_latitude'] : '',
'center_lng'  => (isset( $data['location_longitude'] ) and ! empty( $data['location_longitude'] )) ? $data['location_longitude'] : '',
);
$map_data['places'][] = array(
'id'          => (isset( $data['location_id'] ) and ! empty( $data['location_id'] )) ? $data['location_id'] : '',
'title'       => (isset( $data['location_title'] ) and ! empty( $data['location_title'] )) ? $data['location_title'] : '',
'content'     => $infowindow_message,
'location'    => array(
'icon'      => ($category_group_marker),
'lat'       => (isset( $data['location_latitude'] ) and ! empty( $data['location_latitude'] )) ? $data['location_latitude'] : '',
'lng'       => (isset( $data['location_longitude'] ) and ! empty( $data['location_longitude'] )) ? $data['location_longitude'] : '',
'draggable' => true,
'infowindow_default_open' => (isset( $data['location_infowindow_default_open'] ) and ! empty( $data['location_infowindow_default_open'] )) ? $data['location_infowindow_default_open'] : '',
'animation' => (isset( $data['location_animation'] ) and ! empty( $data['location_animation'] )) ? $data['location_animation'] : '',
'infowindow_disable' => ( 'false' === @$infowindow_disable['hide_infowindow']),
'zoom'      => (isset( $data['location_zoom'] ) and ! empty( $data['location_zoom'] )) ? $data['location_zoom'] : '',
),
'categories'  => array( array(
'id'      => $category->group_map_id,
'name'    => $category->group_map_title,
'type'    => 'category',
'icon'    => $category_group_marker,
),
),
);
$map_data['page'] = 'edit_location';
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
var map = $("#wpgmp_map").maps(<?php echo wp_json_encode( $map_data ); ?>).data('wpgmp_maps');
});
</script>
