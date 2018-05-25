<?php
/**
 * Location listings for maps.
 * @package Maps
 */

global $wpdb;
$modelFactory = new WPGMP_Model();
$category = $modelFactory->create_object( 'group_map' );
$location = $modelFactory->create_object( 'location' );
$locations = $location->fetch();
$categories = $category->fetch();
if ( ! empty( $categories ) ) {
	$categories_data = array();
	foreach ( $categories as $cat ) {
		$categories_data[ $cat->group_map_id ] = $cat->group_map_title;
	}
}
if ( ! empty( $locations ) ) {
	$all_locations = array();
	foreach ( $locations as $loc ) {
		$assigned_categories = array();
		if ( isset( $loc->location_group_map ) and is_array( $loc->location_group_map ) ) {
			foreach ( $loc->location_group_map as $c => $cat ) {
				$assigned_categories[] = $categories_data[ $cat ];
			}
		}
		$assigned_categories = implode( ',',$assigned_categories );
		$loc_checkbox = $form->field_checkbox('map_locations[]',array(
			'value' => $loc->location_id,
			'current' => ((in_array( $loc->location_id, (array) $data['map_locations'] )) ? $loc->location_id : ''),
			'class' => 'chkbox_class',
			'before' => '<div class="fc-1">',
			'after' => '</div>',
			));
		$all_locations[] = array( $loc_checkbox,$loc->location_title,$loc->location_address, $assigned_categories );
	}
}

$table_group = $form->field_html('map_location_listing',array(
	'html' => "<h4>".__( 'Choose Locations', WPGMP_TEXT_DOMAIN )."</h4>",
));

$table_group .= $form->field_select('select_all',array(
	'options' => array(
		'' => __('Choose',WPGMP_TEXT_DOMAIN),
		'select_all' => __('Select All',WPGMP_TEXT_DOMAIN),
		'deselect_all' => __('Deselect All',WPGMP_TEXT_DOMAIN)
		),
	));

$form->add_element('html','map_location_listing_div',array(
	'html' =>$table_group,
	'before' => '<div class="fc-12 wpgmp_location_selection fc-title-blue">',
	'after' => '</div>',
	));

$form->add_element( 'table', 'map_selected_locations', array(
		'heading' => array( 'Select','Title','Address', 'Category' ),
		'data' => $all_locations,
		'before' => '<div class="fc-12">',
		'after' => '</div>',
		'id' => 'wpgmp_google_map_data_table',
		'current' => $data['map_locations'],
));
