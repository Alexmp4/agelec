<?php
// Webba Booking backend appointments class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Backend_Coupons extends WBK_Backend_Component   {
	
	public function __construct() {

		slf_register_actions();

		//set component-specific properties
		$this->name          = 'wbk-coupons';
		$this->title         = __( 'Coupons', 'wbk' );
		$this->main_template = 'tpl_wbk_backend_coupons.php';
        $this->capability    = 'manage_options';
         
	}
    

}
?>