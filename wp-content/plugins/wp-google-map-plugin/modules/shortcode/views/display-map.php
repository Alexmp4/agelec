<?php
/**
 * Parse Shortcode and display maps.
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

global $wpgmp_dbobj;
$wpgmp_dbobj  = new FlipperCode_Database();
$atts = $options;
$marker_array = array();
$address_array = array();
// Fetch map information.
$modelFactory = new WPGMP_Model();
$category_obj = $modelFactory->create_object( 'group_map' );
$categories = $category_obj->fetch();
$all_categories = array();
$all_categories_name = array();

if ( ! empty( $categories ) ) {
	foreach ( $categories as $category ) {
		$all_categories[ $category->group_map_id ] = $category;
		$all_categories_name[ sanitize_title( $category->group_map_title ) ] = $category;
	}
}
if ( $atts ) {
	foreach ( $atts as $key => $value ) {
	    if ( strpos( $key, 'marker' ) === 0 ) {
	        $marker_array[ $key ] = $value;
			$first_marker = current( $marker_array );
	 		$explode_marker = explode( '|',$first_marker );
			$center_lat = $explode_marker[0];
	    	$center_lng = $explode_marker[1];
	    }
		if ( strpos( $key, 'address' ) === 0 ) {
			$address_array[ $key ] = $value;
			$first_address = current( $address_array );
			$rm_space_ads = str_replace( ' ','+',$first_address );
			$explode_ads = explode( '|',$rm_space_ads );
			$geocode = wp_remote_get( 'http://maps.google.com/maps/api/geocode/json?address='.$explode_ads[0].'&sensor=false' );

			if ( ! isset( $geocode->errors ) ) {
				$output = json_decode( $geocode['body'] );
				$center_lat = $output->results[0]->geometry->location->lat;
				$center_lng = $output->results[0]->geometry->location->lng;
			}
		}
	}
}

if ( empty( $atts['width'] ) ) {
	$map_width = '100%';
} else { $map_width = str_replace( 'px','',$atts['width'] ).'px'; }

if ( empty( $atts['height'] ) ) {
	$map_height = '500px';
} else { $map_height = str_replace( 'px','',$atts['height'] ).'px'; }

if ( empty( $atts['zoom'] ) ) {
	$zoom = 14;
} else { $zoom = $atts['zoom']; }

if ( empty( $atts['map_type'] ) ) {
	$map_type = 'ROADMAP';
} else { $map_type = $atts['map_type']; }

if ( empty( $atts['scroll_wheel'] ) ) {
	$scroll_wheel = 'true';
} else { $scroll_wheel = $atts['scroll_wheel']; }

if ( empty( $atts['map_draggable'] ) ) {
	$map_draggable = 'true';
} else { $map_draggable = $atts['map_draggable']; }

$wpgmp_local = array();
if ( $atts['language'] ) {
	$wpgmp_local['language'] = $atts['language'];
} else { $wpgmp_local['language'] = 'en'; }


wp_enqueue_script('wpgmp-google-api');
wp_enqueue_script('wpgmp-google-map-main');
wp_enqueue_script('wpgmp-frontend');
wp_enqueue_style('wpgmp-frontend');
wp_localize_script( 'wpgmp-google-map-main', 'wpgmp_local',$wpgmp_local );

$map_data['map_options'] = array(
	'center_lat'          => $center_lat,
	'center_lng'          => $center_lng,
	'zoom'                => $zoom,
	'scroll_wheel'		=> $scroll_wheel,
	'map_type_id'         => $map_type,
	'draggable'           => ( 'false' === $map_draggable ?false:true ),
	'infowindow_open_event' => 'click',

);

if ( is_array( $marker_array ) ) {
	if ( $marker_array ) {
		foreach ( $marker_array as $marker ) {
			$explode_marker = explode( '|',$marker );
			if ( ! empty( $explode_marker[4] ) ) {
	  			$wpgmp_marker_category_results = $all_categories_name[ sanitize_title( $explode_marker[4] ) ];
				$cat_id = $wpgmp_marker_category_results->group_map_id;
				$cat_title = $wpgmp_marker_category_results->group_map_title;
				$icon = $wpgmp_marker_category_results->group_marker;
			} else {
				$icon = '';
			}

			$id = rand( 1000000,10000000 );

			$map_data['places'][ $id ] = array(
	            'id'          => $id,
	            'title'       => $explode_marker[2],
	            'content'     => ($explode_marker[3]) ? stripcslashes( $explode_marker[3] ) : $explode_marker[2],
	            'location'    => array(
	                'icon'      		 => stripcslashes( $icon ),
	                'lat'       		 => $explode_marker[0],
	                'lng'       		 => $explode_marker[1],
	                'onclick_action' => 'marker',


	            ),
	        );

	        $map_data['places'][ $id ]['categories'][] = array(
				'id'      => stripcslashes( $cat_id ),
				'name'    => stripcslashes( $cat_title ),
				'type'    => 'category',
				'icon'    => stripcslashes( $icon ),
			);
		}
	}
}

if ( is_array( $address_array ) ) {
	if ( $address_array ) {
		foreach ( $address_array as $address ) {
			$explode_address = explode( '|',$address );
			$rm_space_ads = str_replace( ' ','+',$explode_address[0] );
			$geocode = wp_remote_get( 'http://maps.google.com/maps/api/geocode/json?address='.$rm_space_ads.'&sensor=false' );
			if ( ! isset( $geocode->errors ) ) {
				$output = json_decode( $geocode['body'] );
				$lat = $output->results[0]->geometry->location->lat;
				$lng = $output->results[0]->geometry->location->lng;
			}
			if ( ! empty( $explode_address[3] ) ) {
				$wpgmp_marker_category_results = $all_categories_name[ sanitize_title( $explode_address[3] ) ];
				$cat_id = $wpgmp_marker_category_results->group_map_id;
				$cat_title = $wpgmp_marker_category_results->group_map_title;
				$icon = $wpgmp_marker_category_results->group_marker;
			} else {
				$icon = '';
			}

			$id = rand( 1000000,10000000 );

			$map_data['places'][ $id ] = array(
	            'id'          => $id,
	            'title'       => $explode_address[1],
	            'content'     => ($explode_address[2]) ? stripcslashes( $explode_address[2] ) : $explode_address[1],
	            'location'    => array(
	                'icon'      		 => stripcslashes( $icon ),
	                'lat'       		 => $lat,
	                'lng'       		 => $lng,
	                'onclick_action' => 'marker',
	            ),
	        );

	        $map_data['places'][ $id ]['categories'][] = array(
				'id'      => stripcslashes( $cat_id ),
				'name'    => stripcslashes( $cat_title ),
				'type'    => 'category',
				'icon'    => stripcslashes( $icon ),
			);
		}
	}
}

$map_data_json = json_encode( $map_data );

$rand_mapid = rand( 1000000,10000000 );
$map_output = '';
$map_output .= "
<script type='text/javascript'>
	/* <![CDATA[ */
	var map_data_".$rand_mapid.' = '.$map_data_json.'
    /* ]]> */
</script>
<style>
    .wpgmp_display_map_'.$rand_mapid.' { width : '.$map_width.'; height: '.$map_height.'; }
    .wpgmp_display_map_'.$rand_mapid." img { max-width:none !important; }
</style>

<div class='wpgmp_display_map_".$rand_mapid."' id='wpgmp_display_map_".$rand_mapid."'></div>";
$map_output .= "<script>
	jQuery(document).ready(function($) {
	  var map = $('.wpgmp_display_map_".$rand_mapid."').maps(map_data_".$rand_mapid.").data('wpgmp_maps');
	});
</script>";
return $map_output;
