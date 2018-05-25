<?php
// Webba Booking backend appointments class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
function wbk_gg_render_auth(){
	$calendar_id = $_GET['clid'];
    if( !is_numeric( $calendar_id ) ){
    	$html = __( 'Error: invalid calendar ID', 'wbk');
        return $html;
    }
    $html = '';

    $google = new WBK_Google();
    $google->init( $calendar_id );
    
    $html .= '<h2>' . $google->getCalendarName() . '</h2>'; 

    if( isset( $_GET['code'] ) ){
    	// processing the return from gg after authorization
        $auth_code =  $_GET['code'];       
        $fetch_result =  $google->processAuthCode( $auth_code );
	} 
    if( isset( $_GET['action'] ) && $_GET['action'] == 'revoke'  && !isset( $_GET['code'] ) ){
        $google->clearToken();
    }

    $html .= $google->renderCalendarBlock();
    return $html;
}
class WBK_Backend_GG_Calendars extends WBK_Backend_Component   {
	public function __construct() {
		slf_register_actions();
		//set component-specific properties
		$this->name          = 'wbk-gg-calendars';
		$this->title         = __( 'Google calendars', 'wbk' );
		$this->main_template = 'tpl_wbk_backend_gg_calendars.php';
        $this->capability    = 'read';     
	}
    
}
?>