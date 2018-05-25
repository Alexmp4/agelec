<?php
//WBK appointment entity class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Appointment extends WBK_Entity {
	// customer e-mail 
	protected $email;
	// duration of appointment 
	protected $duration;
	// time
	protected $time;
	// day
	protected $day;
	// phone
	protected $phone;
	// extra
	protected $extra;
	// attachment
	protected $attachment;
	// service
	protected $service_id;
	// quantity
	protected $quantity;
	// time offset
	protected $time_offset;

	public function __construct() {
		parent::__construct();
		$this->table_name = 'wbk_appointments';
	}
	public function set( $id,
						 $name, 
						 $description,
						 $email,
						 $duration,
						 $time,
						 $day,
						 $phone,
						 $extra,
						 $attachment,
						 $quantity ) {
		if ( !$this->setId( $id ) ){
			 
			return false;
		}				 	
		if ( !$this->setName( $name ) ){
			 
			return false;
		}				 	
		if ( !$this->setDescription( $description ) ){
			 
			return false;
		}		
		if ( !$this->setEmail( $email ) ){
			 
			return false;
		}
		if ( !$this->setDuration( $duration ) ){
			 
			return false;
		}
		if ( !$this->setTime( $time ) ){
			 
			return false;
		}
		if ( !$this->setDay( $day ) ){
						 
			return false;
		}
		if ( !$this->setPhone( $phone ) ){
						 
			return false;
		}
		if ( !$this->setExtra( $extra ) ){
						 
			return false;
		}
		if ( !$this->setAttachment( $attachment ) ){
			 
			return false;
		}
		if ( !$this->setQuantity( $quantity ) ){
			 
			return false;
		}
		return true;
	}
	// set service id
	public function setService( $value ) {
		if ( WBK_Validator::checkInteger( $value, 1, 9999999 ) ){
			$this->service_id = $value;
			return true;
		} else {
			array_push( $this->error_messages, __( 'incorrect service', 'wbk' ) );
			return false;
		} 
	}
	// get service id 
	public function getService() {
		return absint( $this->service_id );
	}
	// set email
	public function setEmail( $value) {
		$value = strtolower($value);
		$value = sanitize_email( $value );
		if ( WBK_Validator::checkEmail( $value ) ){
			$this->email = $value;
			return true;
		} else {
			array_push( $this->error_messages, __( 'incorrect email', 'wbk' ) );
			return false;
		} 
 
	} 
	// get email
	public function getEmail() {
		$value = sanitize_email( $this->email );
		return $value;
	}
	// set duration
	public function setDuration( $value ) {
		$value = absint( $value );
		if ( WBK_Validator::checkInteger( $value, 1, 1440 ) ){
			$this->duration = $value;
			return true;
		} else {
			array_push( $this->error_messages, __( 'incorrect duration', 'wbk' ) );
			return false;
		} 
	}
	// get duration
	public function getDuration() {
		return absint( $this->duration );
	}
	// set quantity
	public function setQuantity( $value ) {
		$value = absint( $value );
		if ( WBK_Validator::checkInteger( $value, 1, 1000000 ) ){
			$this->quantity = $value;
			return true;
		} else {
			array_push( $this->error_messages, __( 'incorrect quantity', 'wbk' ) );
			return false;
		} 
	}
	// get quantity
	public function getQuantity() {
		return absint( $this->quantity );
	}
	// set time offset
	public function setTimeOffset( $value ) {
		$value = intval( $value );
		if ( WBK_Validator::checkInteger( $value, -10000, 10000 ) ){
			$this->time_offset = $value;			
			return true;
		} else {
			array_push( $this->error_messages, __( 'incorrect time offset', 'wbk' ) );
			return false;
		} 
	}
	// get time offset
	public function getTimeOffset() {
		return intval( $this->time_offset );
	}
	// set time
	public function setTime( $value ) {
		$value = absint( $value );
		if ( WBK_Validator::checkInteger( $value, 1438426800, 1754046000 ) ){
			$this->time = $value;
			return true;
		} else {
			array_push( $this->error_messages, __( 'incorrect time', 'wbk' ) );
			return false;
		} 
	}
	// get time
	public function getTime() {
		return absint( $this->time );
	}
	// get local time
	public function getLocalTime() {	
		$timezone = new DateTimeZone( get_option( 'wbk_timezone' ) );
		$current_offset =  $this->getTimeOffset() * -60 - $timezone->getOffset( new DateTime );
 		$local_time = absint( $this->time ) + $current_offset;
		return $local_time;
	}	
	// set day
	public function setDay( $value ) {
		$value = absint( $value );
		if ( WBK_Validator::checkInteger( $value, 1438426800, 1754046000 ) ){
			$this->day = $value;
			return true;
		} else {
			array_push( $this->error_messages, __( 'incorrect day', 'wbk' ) );
			return false;
		} 
	}
	// get day
	public function getDay() {
		return absint( $this->day );
	}
	// set attachment
	public function setAttachment( $value ) {
		$value = sanitize_text_field ( $value );
		if ( WBK_Validator::checkStringSize( $value, 0, 1024 ) ){
			$this->attachment = $value;
			return true;
		} else {
			array_push( $this->error_messages, __( 'incorrect attachment', 'wbk' ) );
			return false;
		} 
	}
 
 	// get attachment
	public function getAttachment() {
		$value = sanitize_text_field( $this->attachment );
		return  $value;
	}
	// set extra
	public function setExtra( $value ) {
		if( $value == '' ){
			return TRUE;
		}
	  	$extra = json_decode( $value );
 		if( $extra === NULL ){
 			return FALSE;
 		}
 		if( !is_array( $extra ) ){
 			return FALSE;
 		}
 		$result_array = array();
		foreach( $extra as $item ){
			if( !is_array( $item) ){
				return FALSE;
			}
			if( count( $item ) <> 3 ){
				 
				return FALSE;
			}
			$result_item = array();
			foreach ( $item  as $subitem ) {
				$result_item[] = sanitize_text_field( $subitem );
			}
			$result_array[] = $result_item;
		}
		$this->extra = json_encode( $result_array );
		return TRUE;
		 

	} 
 	// get extra
	public function getExtra() {
		return  $this->extra;
	}
	// get extra with field ids
	public function getExtraWithFieldIds() {
		$value = sanitize_text_field( $this->extra  );
		return  $value;
	} 
	 // set phone
	public function setPhone( $value ) {
		$value = sanitize_text_field ( $value );
		if ( WBK_Validator::checkStringSize( $value, 0, 30 ) ){
			$this->phone = $value;
			return true;
		} else {
			array_push( $this->error_messages, __( 'incorrect phone', 'wbk' ) );
			return false;
		} 
	}
 
 	// get phone
	public function getPhone() {
		$value = sanitize_text_field( $this->phone );
		return  $value;
	}
 
	// load row from db and put class properties
	public function load () {
 
		$result = parent::load(); 
 		if ( !$result ) {
 			return false;
 		} 
 		if ( !$this->setEmail( $result->email ) ) {
			return false;
		}
 		if ( !$this->setDuration( $result->duration ) ) {
			return false;
		}
		if ( !$this->setTime( $result->time ) ) {
			return false;
		}
		if ( !$this->setDay( $result->day ) ) {
			return false;
		}
		if ( !$this->setPhone( $result->phone ) ) {
			return false;
		}
		if ( !$this->setExtra( $result->extra ) ) {
			return false;
		}
		if ( !$this->setQuantity( $result->quantity ) ) {
			return false;
		}
		if ( !$this->setService( $result->service_id ) ) {
			return false;
		}
		if ( !$this->setTimeOffset( $result->time_offset ) ) {
			return false;
		}
		if ( !$this->setAttachment( $result->attachment ) ) {
			return false;
		}	
		return true;
  	}
  	// update appointment
	public function update() {
		global $wpdb;
 
		if ( parent::update() === false ) {
			return false;
		}
		if ( $wpdb->update( 
				$this->table_name, 
			
				array( 			
					'email' => $this->getEmail(),	 
					'duration' => $this->getDuration(), 
					'interval' => $this->getInterval(),
					'quantity' => $this->getQuantity()
				), 
				array( 'id' => $this->getId() ), 
				
				array( 
			
					'%s',	 
					'%d',	 
					'%d',
					'%d'	 
				), 
				array( '%d' ) 
			) === false ) {
			return false;
		} else {
			return true;
		}
	}
	// add service
	public function add() {
		global $wpdb;
  
		if ( $wpdb->insert( 
				$this->table_name, 
			
				array( 
					'name' => $this->getName(),
					'email' => $this->getEmail(),
					'phone' => $this->getPhone(),	 				
					'description' => $this->getDescription(),
					'service_id' => $this->getService(),
					'time' => $this->getTime(),
					'day' => $this->getDay(),
					'duration' => $this->getDuration(),
					'extra' => $this->getExtraWithFieldIds(),
					'quantity' => $this->getQuantity(),
					'time_offset' => $this->getTimeOffset(),
					'attachment' => $this->getAttachment()
				), 
				 
				
				array( 
					'%s',
					'%s',
					'%s',	
					'%s', 
					'%d',	 
					'%d',
					'%d',
					'%d',
					'%s',
					'%d',
					'%d',
					'%s'	 
				)
				
			) === false ) {

			return false;
		} else {
			$new_id = $wpdb->insert_id;		
			return  $new_id;
		}
	}
	public function getFormatedExtra(){
		$extra = json_decode( $this->extra );
    	$html = '';
		foreach( $extra as $item ){
    		 
			$html .=  $item[1] . ': '. $item[2]  . '<br/>';

    	}  
    	return $html;
	}

}
?>