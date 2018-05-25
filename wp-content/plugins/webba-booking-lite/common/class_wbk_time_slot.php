<?php
// Webba Booking time interval class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
require_once 'class_wbk_date_time_utils.php'; 
class WBK_Time_Slot	 {
	protected $start;
	protected $end;
	protected $status;
	public function __construct( $start, $end ) {
		$this->start = absint( $start );
		$this->end = absint( $end );
	}
	public function getStart() {
		return $this->start;
	}
	public function getEnd() {
		return $this->end;
	}
	public function setStatus( $value ) {
		if ( is_array( $value ) ){
			
			 
			$this->status = array();
			foreach ( $value as $item ) {
				array_push( $this->status, $item );
				 	
			}
		} else {
			$this->status = $value;
		}

	}
	public function getStatus() {
		return $this->status;
	}
	public function isTimeIn( $time ){
		if ( $time > $this->start && $time < $this->end ){
			return TRUE;
		} 
		return FALSE;
	}
}
?>