<?php
  global $wpdb;
  $objects = $wpdb->get_results("select * from ".TBL_LOCATION." where location_latitude IS NULL OR location_latitude = '' or location_longitude IS NULL OR location_longitude = '' ");
  $geo_locations = array();
  if( is_array($objects) ) {
  	foreach($objects as $object) {
  		$geo_locations[$object->location_id] = $object->location_address;
  	}
  }
  $json = json_encode($geo_locations);
  $form  = new WPGMP_Template();
  echo $form->show_header();
  if( count($objects) > 0 ) {
	  $modalArgs = array( 'fc_modal_header' => __('Start Geocoding Process',WFIP_TEXT_DOMAIN),
					'fc_modal_content' => '<div class="fc-msg fc-danger">Total '.count($objects).' locations don\'t have latitude & longitude.</div><p>You can start geocoding process by clicking below link. and whole process may takes few minutes. Please don\'t close or refresh the window meanwhile.</p> <p><input type="button" name="fc-geocoding" class="fc-btn fc-btn-green fc-geocoding" value="Start Geocoding" /><div class="fcdoc-loader">
                             <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
							 <span class="sr-only">Loading...</span>
							</div><textarea class="fc-location-data-set">'.$json.'</textarea><form enctype="multipart/form-data" action="" name="wpgmp-new-loc" method="post">'.wp_nonce_field('wpgmp-nonce').'<input type="hidden" value="update_loc" name="operation" /><textarea name="fc-location-new-set" class="fc-location-new-set"></textarea><span class="wpgmp-status"></span><input type="submit" name="fc-geocoding-updates" class="fc-btn fc-btn-green fc-geocoding-updates" value="Update Locations" /></form></p>',
					'fc_modal_initiator' => '.fc-open-modal',
					'class' => 'fc-modal fc-modal-show fc-12' );

  echo WPGMP_Template::field_fc_modal('fc_import_modal', $modalArgs);  	
  }



if ( class_exists( 'WP_List_Table_Helper' ) and ! class_exists( 'Wpgmp_Location_Table' ) ) {

	class Wpgmp_Location_Table extends WP_List_Table_Helper {  public function __construct($tableinfo) {
			parent::__construct( $tableinfo ); }  }

	// Minimal Configuration :)
	global $wpdb;
	$columns   = array( 'location_title' => 'Title','location_address' => 'Address','location_latitude' => 'Latitude','location_longitude' => 'Longitude' );
	$sortable  = array( 'location_title','location_address','location_latitude','location_longitude' );
	$tableinfo = array(
	'table' => $wpdb->prefix.'map_locations',
	'textdomain' => WPGMP_TEXT_DOMAIN,
	'singular_label' => 'location',
	'plural_label' => 'locations',
	'admin_listing_page_name' => 'wpgmp_manage_location',
	'admin_add_page_name' => 'wpgmp_form_location',
	'primary_col' => 'location_id',
	'columns' => $columns,
	'sortable' => $sortable,
	'per_page' => 200,
	'actions' => array( 'edit','delete' ),
	'col_showing_links' => 'location_title',
	);
	return new Wpgmp_Location_Table( $tableinfo );

}
?>
