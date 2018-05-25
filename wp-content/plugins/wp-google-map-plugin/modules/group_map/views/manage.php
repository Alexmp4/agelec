<?php
/**
 * Manage Marker Categories
 * @package Maps
 */

  $form  = new WPGMP_Template();
  echo $form->show_header();
  
if ( class_exists( 'WP_List_Table_Helper' ) and ! class_exists( 'Wpgmp_Manage_Group_Table' ) ) {

	/**
	 * Display categories manager.
	 */
	class Wpgmp_Manage_Group_Table extends WP_List_Table_Helper {

	  	/**
	  	 * Intialize manage category table.
	  	 * @param array $tableinfo Table's properties.
	  	 */
	  	public function __construct($tableinfo) {
			parent::__construct( $tableinfo ); }
		/**
		 * Show marker image assigned to category.
		 * @param  array $item Category row.
		 * @return html       Image tag.
		 */
	  	public function column_group_marker($item) {
	  		if ( strstr( $item->group_marker, 'wp-google-map-pro/icons/' ) !== false ) {
	  			$item->group_marker = str_replace( 'icons', 'assets/images/icons', $item->group_marker );
	  		}
			return sprintf( '<img src="'.$item->group_marker.'" name="group_image[]" value="%s" />', $item->group_map_id );
		}
		/**
		 * Show category's parent name.
		 * @param  [type] $item Category row.
		 * @return string       Category name.
		 */
	  	public function column_group_parent($item) {

			 global $wpdb;
			 $parent = $wpdb->get_col( $wpdb->prepare( 'SELECT group_map_title FROM '.$this->table.' where group_map_id = %d',$item->group_parent ) );
			 $parent = ( ! empty( $parent )) ? ucwords( $parent[0] ) : '---';
			 return $parent;

		}


	}
	global $wpdb;
	$columns   = array(
	'group_map_title'  => __('Category Title',WPGMP_TEXT_DOMAIN),
			           'group_marker' => __('Marker Image',WPGMP_TEXT_DOMAIN),
			           'group_parent' => __('Parent Category',WPGMP_TEXT_DOMAIN),
			           'group_added' => __('Updated On', WPGMP_TEXT_DOMAIN),
	);
	$sortable  = array( 'group_map_title' );
	$tableinfo = array(
	'table' => $wpdb->prefix.'group_map',
	                    'textdomain' => WPGMP_TEXT_DOMAIN,
					    'singular_label' => 'marker category',
					    'plural_label' => 'Categories',
					    'admin_listing_page_name' => 'wpgmp_manage_group_map',
					    'admin_add_page_name' => 'wpgmp_form_group_map',
					    'primary_col' => 'group_map_id',
					    'columns' => $columns,
					    'sortable' => $sortable,
					    'per_page' => 20,
					    'col_showing_links' => 'group_map_title',
					    'searchExclude' => array( 'group_parent' ),
	);
	return new Wpgmp_Manage_Group_Table( $tableinfo );

}
?>
