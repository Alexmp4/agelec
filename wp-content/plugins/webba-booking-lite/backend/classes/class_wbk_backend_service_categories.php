<?php
// Webba Booking backend service categories class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Backend_Service_Categories extends WBK_Backend_Component   {
	
	public function __construct() {

		slf_register_actions();

		//set component-specific properties
		$this->name          = 'wbk-service-categories';
		$this->title         = __( 'Service categories', 'wbk' );
		$this->main_template = 'tpl_wbk_backend_service_categories.php';
        $this->capability    = 'manage_options';
         
	}
    

}
?>
