<?php
//WBK stat class

// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Admin_Notices {
	public static function labelUpdate(){
	 	return;
	}	 
	public static function colorUpdate(){		
	 	return;
	}	
	public static function appearanceUpdate(){
		if ( get_option( 'wbk_appearance_saved', '' ) != 'true' ) { 
			return '<div class="notice notice-warning is-dismissible"><p>WEBBA Booking: Please setup appearance settings. 				 
					</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
	  	} 
	 	return;
	}	
	public static function emailLandingUpdate(){
		if ( get_option( 'wbk_email_landing', '' ) == '' ) { 
			return '<div class="notice notice-warning is-dismissible"><p>WEBBA Booking: Please setup the <strong>Link to the page with Webba Booking form</strong> setting in the Email Notifications tab. 				 
					</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
	  	} 
	 	return;
	}	



}
?>