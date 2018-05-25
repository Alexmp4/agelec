<?php
/**
 * Class: WPGMP_Model_Location
 * @author Flipper Code <hello@flippercode.com>
 * @package Maps
 * @version 3.0.0
 */

if ( ! class_exists( 'WPGMP_Model_Location' ) ) {

	/**
	 * Location model for CRUD operation.
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Location extends FlipperCode_Model_Base
	{
		/**
		 * Validations on location properies.
		 * @var array
		 */
		public $validations = array(
		'location_title' => array( 'req' => 'Please enter location title.' ),
		'location_latitude' => array( 'req' => 'Please enter location latitude.','latlng' => 'Invalid latitude.' ),
		'location_longitude' => array( 'req' => 'Please enter location longitude.', 'latlng' => 'Invalid longitude.' ),
			);
		/**
		 * Intialize location object.
		 */
		public function __construct() {
			$this->table = TBL_LOCATION;
			$this->unique = 'location_id';
		}
		/**
		 * Admin menu for CRUD Operation
		 * @return array Admin meny navigation(s).
		 */
		public function navigation() {

			return array(
			'wpgmp_form_location' => __( 'Add Location', WPGMP_TEXT_DOMAIN ),
			'wpgmp_manage_location' => __( 'Manage Locations', WPGMP_TEXT_DOMAIN ),
			);
		}
		/**
		 * Install table associated with Location entity.
		 * @return string SQL query to install map_locations table.
		 */
		public function install() {

			global $wpdb;
			$map_location = 'CREATE TABLE '.$wpdb->prefix.'map_locations (
location_id int(11) NOT NULL AUTO_INCREMENT,
location_title varchar(255) DEFAULT NULL,
location_address varchar(255) DEFAULT NULL,
location_draggable varchar(255) DEFAULT NULL,
location_infowindow_default_open varchar(255) DEFAULT NULL,
location_animation varchar(255) DEFAULT NULL,
location_latitude varchar(255) DEFAULT NULL,
location_longitude varchar(255) DEFAULT NULL,
location_city varchar(255) DEFAULT NULL,
location_state varchar(255) DEFAULT NULL,
location_country varchar(255) DEFAULT NULL,
location_postal_code varchar(255) DEFAULT NULL,
location_zoom int(11) DEFAULT NULL,
location_author int(11) DEFAULT NULL,
location_messages text DEFAULT NULL,
location_settings text DEFAULT NULL,
location_group_map text DEFAULT NULL,
location_extrafields text DEFAULT NULL,
PRIMARY KEY  (location_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;';

			return $map_location;
		}
		/**
		 * Get Location(s)
		 * @param  array $where  Conditional statement.
		 * @return array         Array of Location object(s).
		 */
		public function fetch($where = array()) {

			$objects = $this->get( $this->table, $where );

			if ( isset( $objects ) ) {
				foreach ( $objects as $object ) {
					$object->location_settings = unserialize( $object->location_settings );
					$object->location_extrafields = unserialize( $object->location_extrafields );
					// Data convertion for version < 3.0.
					$is_category = unserialize( $object->location_group_map );
					if ( ! is_array( $is_category ) ) {
						$object->location_group_map = array( $object->location_group_map );
					} else {
						$object->location_group_map = $is_category;
					}
					// Data convertion for version < 3.0.
					$is_message = unserialize( base64_decode( $object->location_messages ) );
					if ( is_array( $is_message ) ) {
						$object->location_messages = $is_message['googlemap_infowindow_message_one'];
					}
				}
				return $objects;
			}
		}
		public function update_loc() {
			global $_POST;

			$entityID = '';

			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ); }

			if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

				die( 'Cheating...' );

			}
			$all_new_locations = json_decode(wp_unslash($_POST['fc-location-new-set'] ));
			if( is_array($all_new_locations) and !empty( $all_new_locations) )
			{
				foreach($all_new_locations as $location) {
					$data['location_latitude'] = sanitize_text_field( $location->latitude );
					$data['location_longitude'] = sanitize_text_field( $location->longitude );
					if ( $location->id > 0 ) {
						$where[ $this->unique ] = $location->id;
						$result = FlipperCode_Database::insert_or_update( TBL_LOCATION, $data, $where );
					} 
					}
			}
		
			if ( false === $result ) {
				$response['error'] = __( 'Something went wrong. Please try again.',WPGMP_TEXT_DOMAIN );
			} elseif ( $entityID > 0 ) {
				$response['success'] = __( 'Location updated successfully',WPGMP_TEXT_DOMAIN );
			} else {
				$response['success'] = __( 'Location added successfully.',WPGMP_TEXT_DOMAIN );
			}

			return $response;
		}
		/**
		 * Add or Edit Operation.
		 */
		public function save() {
			global $_POST;
			$entityID = '';
			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ); }

			if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

				die( 'Cheating...' );

			}

			$this->verify( $_POST );

			if ( is_array( $this->errors ) and ! empty( $this->errors ) ) {
				$this->throw_errors();
			}

			if ( isset( $_POST['entityID'] ) ) {
				$entityID = intval( wp_unslash( $_POST['entityID'] ) );
			}

			if ( isset( $_POST['location_messages'] ) ) {
				$data['location_messages'] = wp_unslash( $_POST['location_messages'] );
			}
			if ( isset( $_POST['extensions_fields'] ) ) {
				$_POST['location_settings']['extensions_fields'] = $_POST['extensions_fields'];
			}

			$data['location_settings'] = serialize( wp_unslash( $_POST['location_settings'] ) );
			$data['location_group_map'] = serialize( wp_unslash( $_POST['location_group_map'] ) );
			$data['location_title'] 		= sanitize_text_field( wp_unslash( $_POST['location_title'] ) );
			$data['location_address'] 		= sanitize_text_field( wp_unslash( $_POST['location_address'] ) );
			$data['location_latitude'] 		= sanitize_text_field( wp_unslash( $_POST['location_latitude'] ) );
			$data['location_longitude'] 	= sanitize_text_field( wp_unslash( $_POST['location_longitude'] ) );
			$data['location_city'] 			= sanitize_text_field( wp_unslash( $_POST['location_city'] ) );
			$data['location_state'] 		= sanitize_text_field( wp_unslash( $_POST['location_state'] ) );
			$data['location_country'] 		= sanitize_text_field( wp_unslash( $_POST['location_country'] ) );
			$data['location_postal_code'] 	= sanitize_text_field( wp_unslash( $_POST['location_postal_code'] ) );
			$data['location_zoom']  		= intval( wp_unslash( $_POST['location_zoom'] ) );
			$data['location_draggable']  	= sanitize_text_field( wp_unslash( $_POST['location_draggable'] ) );
			$data['location_infowindow_default_open']  = sanitize_text_field( wp_unslash( $_POST['location_infowindow_default_open'] ) );
			$data['location_animation']  	= sanitize_text_field( wp_unslash( $_POST['location_animation'] ) );
			if ( $entityID > 0 ) {
				$where[ $this->unique ] = $entityID;
			} else {
				$where = '';
			}

			$result = FlipperCode_Database::insert_or_update( $this->table, $data, $where );

			if ( false === $result ) {
				$response['error'] = __( 'Something went wrong. Please try again.',WPGMP_TEXT_DOMAIN );
			} elseif ( $entityID > 0 ) {
				$response['success'] = __( 'Location updated successfully',WPGMP_TEXT_DOMAIN );
			} else {
				$response['success'] = __( 'Location added successfully.',WPGMP_TEXT_DOMAIN );
			}
			return $response;
		}

		/**
		 * Delete location object by id.
		 */
		public function delete() {
			if ( isset( $_GET['location_id'] ) ) {
				$id = intval( wp_unslash( $_GET['location_id'] ) );
				$connection = FlipperCode_Database::connect();
				$this->query = $connection->prepare( "DELETE FROM $this->table WHERE $this->unique='%d'", $id );
				return FlipperCode_Database::non_query( $this->query, $connection );
			}
		}
		
	}
}
