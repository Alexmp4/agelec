<?php
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
//define abstract class for backend component
class WBK_Backend_Component {
	// service name of component
	protected $name;
	// title of component
	protected $title;
	// main admin template
	protected $main_template;
	// capbility
	protected $capability;
	// help url
	protected $help_url;
	// get $name
	public function getName() {
		return $this->name;
	}
	// set name
	public function setName( $name ) {
		$this->name = $name;
	}
	// get $title
	public function getTitle() {
		return __(  $this->title, 'wbk' );
	} 
	// get capability 
	public function getCapability() {
		return $this->capability;
	}
	// render main template of component
	public function render(){
   
   		wp_get_current_user();
    	
    	global $current_user;
		
		// load and output view template
		ob_start();
        ob_implicit_flush(0);
		
		try {
            include  (dirname(__FILE__)).'/templates/'.$this->main_template;
        
        } catch (Exception $e) {
        
        	ob_end_clean();
            throw $e;
        
        }
        echo ob_get_clean();
	}
  
}
?>