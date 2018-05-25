<?php
// Webba Booking backend go premium class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Backend_GoPremium extends WBK_Backend_Component   {
	
	public function __construct() {

		slf_register_actions();

		//set component-specific properties
		$this->name          = 'wbk-gopremium';
		$this->title         = __( 'Go Premium!', 'wbk' );
		$this->main_template = 'tpl_wbk_backend_gopremium.php';
        $this->capability    = 'read';
         
	}
    

}
?>
