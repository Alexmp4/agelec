<?php
// Webba Booking backend appointments class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Backend_Appointments extends WBK_Backend_Component   {
	
	public function __construct() {

		slf_register_actions();

		//set component-specific properties
		$this->name          = 'wbk-appointments';
		$this->title         = __( 'Appointments', 'wbk' );
		$this->main_template = 'tpl_wbk_backend_appointments.php';
        $this->capability    = 'read';
         
	}
    

}
?>
