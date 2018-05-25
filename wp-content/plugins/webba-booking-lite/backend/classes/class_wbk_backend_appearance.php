<?php
// Webba Booking backend appearance class
// check if accessed directly


if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Backend_Appearance extends WBK_Backend_Component   {
	
	public function __construct() {
		//set component-specific properties
		$this->name          = 'wbk-appearance';
		$this->title         = 'Appearance';
		$this->main_template = 'tpl_wbk_backend_appearance.php';
        $this->capability    = 'manage_options';
		
        // init scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts') );
         

 	    $slf= new SoloFramework( 'wbk_settings_data' );
 		if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wbk-appearance' ) { 
			slf_register_actions();
		 	$slf->loadSectionAssets( 'wbk_extended_appearance_options' );
		} 
          
	}
	// init styles and scripts
	public function enqueueScripts() {
	 
 	}
  	public function render() {
  	$slf = new SoloFramework( 'wbk_settings_data' );
 	echo '<div class="wrap">
			<h2 class="wbk_panel_title">'. __( 'Appearance', 'wbk' ) .'	<a style="text-decoration:none;" href="http://webba-booking.com/documentation/appearance-customization/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a> </h2>
						'. 
 						    $slf->renderSectionSet( 'wbk_extended_appearance_options' )  	
						 .
						' 	
		 </div>';
 
  	}
  
}
?>
