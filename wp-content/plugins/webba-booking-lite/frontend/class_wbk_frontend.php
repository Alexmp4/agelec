<?php
// Webba Booking frontend class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
// include frontend classes from /classes folder
foreach ( glob(dirname(__FILE__).'/classes/*.php') as $filename ) {
	try {
    
        include $filename;
    
    } catch (Exception $e) {
    
    	throw $e;
    }
}
// define main frontend class
class WBK_Frontend {
	public function __construct() {
 		
 		$booking = new WBK_Frontend_Booking();
 		
	}
}
?>