	<?php
// Webba Booking time interval class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
require_once 'class_wbk_date_time_utils.php'; 
class WBK_Time_Slot	 {
	protected $start;
	protected $end;
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
}