<?php
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();
// include abstract component class
include 'class_wbk_backend_component.php';
require 'solo-framework/solo-framework.php';

// include backend classes from /classes folder
foreach ( glob(dirname(__FILE__).'/classes/*.php') as $filename ) {
	try {
        include $filename;
    } catch (Exception $e) {
    	throw $e;
    }
}
// define main backend class
class WBK_Backend {
	// 	available components of backend (based on files in classes folder)
	private $components;
	public function __construct() {
		add_action( 'init', array( $this,'inline_upload_enquene' ) );
		//add action for wp menu construction
		add_action( 'admin_menu', array( $this, 'createAdminMenu' ) );
		//set components of backend
		$this->components = array();
		foreach ( glob(dirname(__FILE__).'/classes/*.php') as $filename ) {
			$component_name = str_replace ('class_', '', basename( $filename, ".php" ) );
     		$this->components[$component_name] = new $component_name();
		}
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}
	public	function settings_updated() {
		if( isset($_GET['settings-updated']) && $_GET['settings-updated'] ) {
			date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );

			$time_corr = intval( get_option( 'wbk_email_admin_daily_time', '68400' ));
  			$midnight = strtotime('today midnight');
			$timestamp = strtotime('today midnight') + $time_corr;
			if ( $timestamp <  time() ){
				$timestamp += 86400;
			}
			wp_clear_scheduled_hook( 'wbk_daily_event' );
			wp_schedule_event( $timestamp, 'daily', 'wbk_daily_event' );

			 
			date_default_timezone_set(  'UTC'  );			  
			 
 		}
 	}

	public function inline_upload_enquene(){

		// add common css
		if ( isset( $_GET[ 'page' ] ) && ( $_GET[ 'page' ] == 'wbk-options' || $_GET[ 'page' ] == 'wbk-schedule' || $_GET[ 'page' ] == 'wbk-services'  ||  $_GET[ 'page' ] == 'wbk-forms'  ) ) {
		 	wp_enqueue_style( 'wbk-backend-style', plugins_url( '/css/wbk-backend.css', __FILE__ ) );
		}
		// edit post/page scripts
		if ( $this->is_edit_page() ) {
			wp_enqueue_script( 'jquery-ui-dialog'  );
			wp_enqueue_script( 'jquery-ui-core'  );
			wp_enqueue_script( 'wbk-service-dialog', plugins_url( '/js/wbk-post-buttons.js', __FILE__ )  );

            $translation_array = array(
                'cancel' => __( 'Cancel', 'wbk' ),
                'add' => __( 'Add', 'wbk' ),
				'formtitle' =>	__( 'Add Webba Booking form', 'wbk' )
            );
            wp_localize_script( 'wbk-service-dialog', 'wbkl10n', $translation_array );

			wp_enqueue_style( 'wbk-shortcode-dialog-style', plugins_url( '/css/wbk-shortcode-dialog.css', __FILE__ ) );

	 		wp_enqueue_style ( 'wp-jquery-ui-dialog' );
			// add shortcode dialog to admion
			add_action( 'admin_footer', array( $this, 'createServiceDialog' ) );
			// add shortcode button
			add_action( 'media_buttons', array( $this, 'createShortcodeButton' ));

		}
	}
	public function createAdminMenu() {

        global $current_user;
        // check if current user has role of admin
        if ( in_array( 'administrator', $current_user->roles ) || WBK_Validator::checkAccessToSchedule() || WBK_Validator::checkAccessToGgCalendarPage() ){
            if ( !empty($this->components) ){

	        	add_menu_page(__( 'Webba Booking', 'wbk' ), __( 'Webba Booking', 'wbk' ), 'read', 'wbk-main', array( $this->components['wbk_backend_schedule'], 'render'), plugins_url( 'images/webba-booking.png', __FILE__) );
	        	foreach ( $this->components as  $component ) {
	        		if( $component->getName() == 'wbk-gopremium' ){
	        			$component_premium = $component;
	        			continue;
	        		}
		        	$component_title = $component->getTitle();

		        	$hook = add_submenu_page( 'wbk-main', $component->getTitle(), $component->getTitle(), $component->getCapability(), $component->getName(), array( $component, 'render' ) );

	        	 	if (  $component->getName() == 'wbk-options' ){
	        	    add_action( 'load-'.$hook, array( $this, 'settings_updated' ) );

	        		}
	        	}
	        	$hook = add_submenu_page( 'wbk-main', $component_premium->getTitle(), $component_premium->getTitle(), $component_premium->getCapability(), $component_premium->getName(), array( $component_premium, 'render' ) );
	        	global $submenu;
                unset( $submenu['wbk-main'][0] );
        	}

        }

	}
	public function createServiceDialog() {
		$service_list = '<select class="wbk-input wbk-width-100" id="wbk-service-id">';
		$service_list .= '<option value="0" selected="selected">' . __( 'All services', 'wbk' ) . '</option>';
		$arrIds = WBK_Db_Utils::getServices();
		foreach ( $arrIds as $id ) {
			$service = new WBK_Service();
			if ( !$service->setId( $id ) ) {
				continue;
			}
		 	if ( !$service->load() ) {

		 		continue;
			}
			$service_list .=  '<option value="' . $service->getId() . '"" >' . $service->getName() . '</option>';
		}
		$service_list .=  '</select>';

		$caregory_list = '<select class="wbk-input wbk-width-100" id="wbk-category-id">';
		$caregory_list .= '<option value="0" selected="selected">' . __( 'All categories', 'wbk' ) . '</option>';
		$arrIds = WBK_Db_Utils::getServiceCategoryList();
		foreach ( $arrIds as $key => $value ) {
			$service = new WBK_Service();
			if ( !$service->setId( $id ) ) {
				continue;
			}
		 	if ( !$service->load() ) {
		 		continue;
			}
			$caregory_list .=  '<option value="' . $key . '"" >' . $value . '</option>';
		}
		$caregory_list .=  '</select>';



		$html = '<div id="wbk-service-dialog" >
				   	<div id="wbk-service-dialog-content">
						<label for="wbk-service">' . __( 'Select service', 'wbk' ) . '<span class="input-error" id="error-name"></span></label><br/>' .
     				       $service_list
				   	.   '</div>
						<label for="wbk-service">' . __( 'Or category', 'wbk' ) . '<span class="input-error" id="error-name"></span></label><br/>' .
     				       $caregory_list
				   	.   '</div>

				</div>';
		echo $html;
	}
	public function createShortcodeButton() {
		echo '<a href="#" class = "button" id = "wbk-add-shortcode" title = "Webba Booking form">' . __( 'Webba Booking form', 'wbk' ) . '</a>';
		echo '<a href="#" class = "button" id = "wbk-add-shortcode-landing" title = "Webba Booking Email landing">' . __( 'Webba Booking Email landing', 'wbk' ) . '</a>';
	}
	protected function is_edit_page($new_edit = null){
	    global $pagenow;
	    //make sure we are on the backend
	    if ( !is_admin() ) {
	    	return false;
	    }
	    if ( $new_edit == 'edit' ) {
	        return in_array( $pagenow, array( 'post.php',  ) );
	    } elseif ( $new_edit == 'new' ) {
	        return in_array( $pagenow, array( 'post-new.php' ) );
	    }
	    else {
	        return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
		}
	}
	public function admin_notices() {
	    echo  WBK_Admin_Notices::labelUpdate();
	    echo  WBK_Admin_Notices::appearanceUpdate();
	    echo  WBK_Admin_Notices::emailLandingUpdate();
	}
	

}

?>