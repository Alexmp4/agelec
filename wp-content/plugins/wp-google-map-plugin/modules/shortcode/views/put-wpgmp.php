<?php
/**
* Parse Shortcode and display maps.
* @package Maps
* @author Flipper Code <hello@flippercode.com>
*/

if ( isset( $options['id'] ) ) {
$map_id = $options['id'];
} else { return ''; }

if ( isset( $options['show'] ) ) {
$show_option = $options['show'];
} else {
$show_option = 'default' ;
}
$shortcode_filters = array();
if ( isset( $options['category'] ) ) {
$shortcode_filters['category'] = $options['category'];
}
// Fetch map information.
$modelFactory = new WPGMP_Model();
$map_obj = $modelFactory->create_object( 'map' );
$map_record = $map_obj->fetch( array( array( 'map_id', '=', $map_id ) ) );
$map = $map_record[0];

if(!empty($map)) {
$map->map_street_view_setting = unserialize( $map->map_street_view_setting );
$map->map_all_control = unserialize( $map->map_all_control );
$map->map_info_window_setting = unserialize( $map->map_info_window_setting );
$map->map_locations = unserialize( $map->map_locations );
$map->map_layer_setting = unserialize( $map->map_layer_setting );
$map->map_infowindow_setting = unserialize( $map->map_infowindow_setting );
}

$category_obj = $modelFactory->create_object( 'group_map' );
$categories = $category_obj->fetch();
$all_categories = array();
$all_child_categories = array();
$all_categories_name = array();
$location_obj = $modelFactory->create_object( 'location' );

if ( ! empty( $categories ) ) {
foreach ( $categories as $category ) {
$all_categories[ $category->group_map_id ] = $category;
$all_categories_name[ sanitize_title( $category->group_map_title ) ] = $category;
if($category->group_parent > 0)
$all_child_categories[$category->group_map_id] = $category->group_parent;
}
}

if ( ! empty( $map->map_locations ) ) {
$map_locations = $location_obj->fetch( array( array( 'location_id', 'IN', implode( ',',$map->map_locations ) ) ) );
}
$location_criteria = array(
'show_all_locations' => false,
'catetory__in' => false,
'catetory__not_in' => false,
'limit' => 0,
);

$location_criteria = apply_filters('wpgmp_location_criteria',$location_criteria,$map);

if( isset($location_criteria['show_all_locations']) and $location_criteria['show_all_locations'] == true ) {
$map_locations = $location_obj->fetch();
}


if( isset($location_criteria['limit']) and $location_criteria['limit'] > 0 ) {
$how_many = intval($location_criteria['limit']);
$map_locations = array_slice($map_locations,0,$how_many);
}

$apply_category_in = false;
$apply_category_not_in = false;

if( isset($location_criteria['category__in']) and is_array($location_criteria['category__in']) ) {
$apply_category_in = true;
}

if( isset($location_criteria['category__not_in']) and is_array($location_criteria['category__not_in']) ) {
$apply_category_not_in = true;
}

$map_data = array();
// Set map options.
$map_data['places'] = array();
if ( $map->map_all_control['infowindow_openoption'] == 'mouseclick' ) {
$map->map_all_control['infowindow_openoption'] = 'click';
} else if ( $map->map_all_control['infowindow_openoption'] == 'mousehover' ) {
$map->map_all_control['infowindow_openoption'] = 'mouseover';
} else if ( $map->map_all_control['infowindow_openoption'] == 'mouseover' ) {
$map->map_all_control['infowindow_openoption'] = 'mouseover';
} else {
$map->map_all_control['infowindow_openoption'] = 'click';
}

$infowindow_sourcecode = apply_filters('wpgmp_infowindow_message',$map->map_all_control['infowindow_setting'],$map);

$map_data['map_options'] = array(
'center_lat' => sanitize_text_field( $map->map_all_control['map_center_latitude'] ),
'center_lng' => sanitize_text_field( $map->map_all_control['map_center_longitude'] ),
'zoom' => (isset( $options['zoom'] )) ? intval( $options['zoom'] ): intval( $map->map_zoom_level ),
'map_type_id' => sanitize_text_field( $map->map_type ),
'draggable' => (sanitize_text_field( $map->map_all_control['map_draggable'] ) != 'false'),
'scroll_wheel' => (sanitize_text_field( $map->map_scrolling_wheel ) != 'false'),
'display_45_imagery' => sanitize_text_field( $map->map_45imagery ),
'marker_default_icon' => esc_url( $map->map_all_control['marker_default_icon'] ),
'infowindow_setting' => wpautop( wp_unslash( $infowindow_sourcecode ) ),
'infowindow_bounce_animation' => $map->map_all_control['infowindow_bounce_animation'],
'infowindow_drop_animation' => ('true' == $map->map_all_control['infowindow_drop_animation'] ),
'close_infowindow_on_map_click' => ('true' == $map->map_all_control['infowindow_close'] ),
'default_infowindow_open' => ('true' == $map->map_all_control['infowindow_open'] ),
'infowindow_open_event' => ($map->map_all_control['infowindow_openoption']) ? $map->map_all_control['infowindow_openoption'] : 'click',
'full_screen_control' => ($map->map_all_control['full_screen_control'] != 'false'),
'search_control' => ($map->map_all_control['search_control'] != 'false'),
'zoom_control' => ($map->map_all_control['zoom_control'] != 'false'),
'map_type_control' => ($map->map_all_control['map_type_control'] != 'false'),
'street_view_control' => ($map->map_all_control['street_view_control'] != 'false'),
'full_screen_control_position' => $map->map_all_control['full_screen_control_position'],
'search_control_position' => $map->map_all_control['search_control_position'],
'zoom_control_position' => $map->map_all_control['zoom_control_position'],
'map_type_control_position' => $map->map_all_control['map_type_control_position'],
'map_type_control_style' => $map->map_all_control['map_type_control_style'],
'street_view_control_position' => $map->map_all_control['street_view_control_position'],
'map_control' => ($map->map_all_control['map_control'] != 'false'),
'map_control_settings' => $map->map_all_control['map_control_settings'],
);

$map_data['map_options']['width'] = sanitize_text_field( $map->map_width );

$map_data['map_options']['height'] = sanitize_text_field( $map->map_height );

$map_data['map_options'] = apply_filters( 'wpgmp_maps_options',$map_data['map_options'],$map );

if ( isset( $map_data['map_options']['width'] ) ) {
$width = $map_data['map_options']['width'];
} else { 	$width = '100%'; }

if ( isset( $map_data['map_options']['height'] ) ) {
$height = $map_data['map_options']['height'];
} else { 	$height = '300px'; }

if ( '' != $width and strstr( $width, '%' ) === false ) {
$width = str_replace( 'px', '', $width ).'px';
}

if ( '' == $width ) {
$width = '100%';
}
if ( strstr( $height, '%' ) === false ) {
$height = str_replace( 'px', '', $height ).'px';
}


wp_enqueue_script( 'wpgmp-google-api' );
wp_enqueue_script( 'wpgmp-google-map-main' );
wp_enqueue_script( 'wpgmp-frontend' );
wp_enqueue_style( 'wpgmp-frontend' );


if ( is_array( $map_locations ) ) {
$loc_count = 0;
foreach ( $map_locations as $location ) {
$location_categories = array();
$is_continue = true;
if ( empty( $location->location_group_map ) ) {
$location_categories[] = array(
'id'      => '',
'name'    => 'Uncategories',
'type'    => 'category',
'extension_fields' => $loc_category->extension_fields,
'icon'    => WPGMP_ICONS.'marker_default_icon.png',
);
} else {

foreach ( $location->location_group_map as $key => $loc_category_id ) {
$loc_category = $all_categories[ $loc_category_id ];

if( $apply_category_in == true ) {
if( !in_array( $loc_category_id, $location_criteria['category__in'] ) and !in_array( strtolower($loc_category->group_map_title), $location_criteria['category__in'] ) ) {
$is_continue = false;
}
}

if( $apply_category_not_in == true ) {
if( in_array( $loc_category_id, $location_criteria['category__not_in'] ) or in_array( strtolower($loc_category->group_map_title), $location_criteria['category__not_in'] ) ) {
$is_continue = false;
}
}

if ( ! empty( $loc_category ) ) {
$location_categories[] = array(
'id'      => $loc_category->group_map_id,
'name'    => $loc_category->group_map_title,
'type'    => 'category',
'extension_fields' => $loc_category->extensions_fields,
'icon'    => $loc_category->group_marker,
);
}
}
}
if( $is_continue == false) {
continue;
}
// Extra Fields in location.

$onclick = isset( $location->location_settings['onclick'] ) ? $location->location_settings['onclick'] : 'marker';

$map_data['places'][ $loc_count ] = array(
'id'          => $location->location_id,
'title'       => $location->location_title,
'address'     => $location->location_address,
'source'	  => 'manual',
'content'     => ('' != $location->location_messages) ? do_shortcode( stripcslashes( $location->location_messages ) ) : $location->location_title,
'location' => array(
'icon'      => ($location_categories[0]['icon']) ? $location_categories[0]['icon'] : $map_data['map_options']['marker_default_icon'],
'lat'       => $location->location_latitude,
'lng'       => $location->location_longitude,
'city'      => $location->location_city,
'state'     => $location->location_state,
'country'   => $location->location_country,
'onclick_action' => $onclick,
'redirect_custom_link' => $location->location_settings['redirect_link'],
'marker_image' => $marker_image,
'open_new_tab' => $location->location_settings['redirect_link_window'],
'postal_code' => $location->location_postal_code,
'draggable' => ( 'true' == $location->location_draggable ),
'infowindow_default_open' => ('true' == $location->location_infowindow_default_open),
'animation' => $location->location_animation,
'infowindow_disable' => ($location->location_settings['hide_infowindow'] !== 'false'),
'zoom'      => 5,
'extra_fields' => $extra_fields),
'categories' => $location_categories,
'custom_filters' => $extra_fields_filters,
);

$loc_count++;
}
}


if ( ! empty( $map->map_layer_setting['choose_layer']['bicycling_layer'] ) && $map->map_layer_setting['choose_layer']['bicycling_layer'] == 'BicyclingLayer' ) {
$map_data['bicyle_layer'] = array(
'display_layer' => true,
);

$map_data['bicycling_layer'] = apply_filters('wpgmp_bicycling_layer',$map_data['bicycling_layer'],$map);

}

if ( ! empty( $map->map_layer_setting['choose_layer']['traffic_layer'] ) && $map->map_layer_setting['choose_layer']['traffic_layer'] == 'TrafficLayer' ) {
$map_data['traffic_layer']  = array(
'display_layer' => true,
);

$map_data['traffic_layer'] = apply_filters('wpgmp_traffic_layer',$map_data['traffic_layer'],$map);

}

if ( ! empty( $map->map_layer_setting['choose_layer']['transit_layer'] ) && $map->map_layer_setting['choose_layer']['transit_layer'] == 'TransitLayer' ) {
$map_data['transit_layer']  = array(
'display_layer' => true,
);

$map_data['transit_layer'] = apply_filters('wpgmp_transit_layer',$map_data['transit_layer'],$map);

}

// Here loop through all places and apply filter. Shortcode Awesome.
$filterd_places = array();
$render_shortcode = apply_filters('wpgmp_render_shortcode',true,$map);
if ( is_array( $map_data['places'] ) ) {

foreach ( $map_data['places'] as $place ) {
$use_me = true;

// Category filter here.
if ( isset( $shortcode_filters['category'] ) ) {
$found_category = false;
$show_categories_only = explode( ',', strtolower($shortcode_filters['category']) );

foreach ( $place['categories'] as $cat ) {
if ( in_array( strtolower( $cat['name'] ),$show_categories_only ) or in_array( strtolower( $cat['id'] ),$show_categories_only ) ) {
$found_category = true;
}
}
if ( false == $found_category ) {
$use_me = false;
}
}

if( true == $render_shortcode ) {
$place['content'] = do_shortcode($place['content']);	
}

$use_me = apply_filters( 'wpgmp_show_place',$use_me,$place,$map );

if ( true == $use_me ) {
$filterd_places[] = $place;
}
}
unset( $map_data['places'] );
}
$map_data['places'] = apply_filters( 'wpgmp_markers',$filterd_places, $map->map_id );

if ( '' == $map_data['map_options']['center_lat'] ) {
$map_data['map_options']['center_lat'] = $map_data['places'][0]['location']['lat'];
}

if ( '' == $map_data['map_options']['center_lng'] ) {
$map_data['map_options']['center_lng'] = $map_data['places'][0]['location']['lng'];
}


// Street view.
if ( $map->map_street_view_setting['street_control'] == 'true' ) {
$map_data['street_view'] = array(
'street_control'            => @$map->map_street_view_setting['street_control'],
'street_view_close_button'  => (@$map->map_street_view_setting['street_view_close_button'] === 'true'?true:false),
'links_control'             => (@$map->map_street_view_setting['links_control'] === 'true'?true:false),
'street_view_pan_control'   => (@$map->map_street_view_setting['street_view_pan_control'] === 'true'?true:false),
'pov_heading'				=> $map->map_street_view_setting['pov_heading'],
'pov_pitch'					=> $map->map_street_view_setting['pov_pitch'],
);
}
$map_data['street_view'] = apply_filters('wpgmp_map_streetview',$map_data['street_view'],$map);


$map_data['map_property'] = array( 'map_id' => $map->map_id );






$map_output = '<div class="wpgmp_map_container '.apply_filters('wpgmp_container_class','wpgmp-map-'.$map->map_id,$map).'" rel="map'.$map->map_id.'">';

$map_div  = apply_filters('wpgmp_before_map','',$map);

$map_div .= '<div class="wpgmp_map '.apply_filters('wpgmp_map_container_class','',$map).'" style="width:'.$width.'; height:'.$height.';" id="map'.$map->map_id.'" ></div>';

$map_div .= apply_filters('wpgmp_after_map','',$map);

$output = $map_div;

$map_output.= apply_filters( 'wpgmp_before_container','',$map);

$map_output .= apply_filters( 'wpgmp_map_output', $output,$map->map_id );

$map_output.= apply_filters( 'wpgmp_after_container','',$map);

$map_output .= '</div>';

$map_data = apply_filters('wpgmp_map_data',$map_data,$map);
$map_data_obj = json_encode( $map_data );

$map_output .= '<script>jQuery(document).ready(function($) {var map'.$map_id.' = $("#map'.$map_id.'").maps('.$map_data_obj.').data("wpgmp_maps");});</script>';

return $map_output;