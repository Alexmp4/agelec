<?php
// Webba Booking backend appointments class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Backend_Email_Templates extends WBK_Backend_Component   {
	
	public function __construct() {

		slf_register_actions();

		//set component-specific properties
		$this->name          = 'wbk-email-templates';
		$this->title         = __( 'Email templates', 'wbk' );
		$this->main_template = 'tpl_wbk_backend_email_templates.php';
        $this->capability    = 'manage_options';
         
	}
    

}
?>
