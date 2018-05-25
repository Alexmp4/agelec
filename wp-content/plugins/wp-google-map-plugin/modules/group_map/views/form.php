<?php
/**
 * Template for Add & Edit Category
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
$category = $modelFactory->create_object( 'group_map' );
$categories = (array) $category->fetch();
if ( isset( $_GET['doaction'] ) and  'edit' == $_GET['doaction'] and isset( $_GET['group_map_id'] ) ) {
	$category_obj   = $category->fetch( array( array( 'group_map_id', '=', intval( wp_unslash( $_GET['group_map_id'] ) ) ) ) );
	$_POST = (array) $category_obj[0];
} elseif ( ! isset( $_GET['doaction'] ) and isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $_POST );
}
$form  = new WPGMP_Template();
$form->set_header( __( 'Marker Category', WPGMP_TEXT_DOMAIN ), $response, __( 'Manage Marker Categories', WPGMP_TEXT_DOMAIN ), 'wpgmp_manage_group_map' );
if ( is_array( $categories ) ) {
	$markers = array( ' ' => 'Please Select' );
	foreach ( $categories as $i => $single_category ) {
			$markers[ $single_category->group_map_id ] = $single_category->group_map_title;
	}

	$form->add_element('select', 'group_parent', array(
		'lable' => __( 'Parent Category', WPGMP_TEXT_DOMAIN ),
		'current' => (isset( $_POST['group_parent'] ) and ! empty( $_POST['group_parent'] )) ? intval( wp_unslash( $_POST['group_parent'] ) ) : '',
		'desc' => __( 'Assign parent category if any.', WPGMP_TEXT_DOMAIN ),
		'options' => $markers,
	));

}

$form->add_element('text', 'group_map_title', array(
	'lable' => __( 'Marker Category Title', WPGMP_TEXT_DOMAIN ),
	'value' => (isset( $_POST['group_map_title'] ) and ! empty( $_POST['group_map_title'] )) ? sanitize_text_field( wp_unslash( $_POST['group_map_title'] ) ) : '',
	'id' => 'group_map_title',
	'desc' => __( 'Enter here marker category title.', WPGMP_TEXT_DOMAIN ),
	'class' => 'create_map form-control',
	'placeholder' => __( 'Marker Category Title', WPGMP_TEXT_DOMAIN ),
	'required' => true,
));


$form->add_element('image_picker', 'group_marker', array(
	'lable' => __( 'Choose Marker Image', WPGMP_TEXT_DOMAIN ),
	'src' => (isset( $_POST['group_marker'] ) ) ? wp_unslash( $_POST['group_marker'] ) : WPGMP_IMAGES.'/default_marker.png',
	'required' => false,
	'choose_button' => __( 'Choose', WPGMP_TEXT_DOMAIN ),
	'remove_button' => __( 'Remove',WPGMP_TEXT_DOMAIN ),
	'id' => 'marker_category_icon',
));

$form->set_col( 1 );


$form->add_element('extensions','wpgmp_category_form',array(
	'value' => $_POST['extensions_fields'],
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	));


$form->add_element('submit', 'create_group_map_location', array(
	'value' => 'Save Marker Category',
	'before' => '<div class="fc-12">',
	'after' => '</div>'

));

$form->add_element('hidden', 'operation', array(
	'value' => 'save',
));

if ( isset( $_GET['doaction'] ) and  'edit' == $_GET['doaction'] ) {
	$form->add_element('hidden', 'entityID', array(
		'value' => intval( wp_unslash( $_GET['group_map_id'] ) ),
	));
}

$form->render();
