<?php
// Solo Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFSection extends stdClass {

	public $components = array(); 
	
	public function __construct( $param = array() ) {
        $this->name = $param['name'];
        $this->description = $param['description'];
        $this->slug = $param['slug'];

	}
	public function addCompontnet( $obj ){
		$this->components[$obj->slug] = $obj;
	}
	public function render(){
		$html = '<h3>' . $this->name . '</h3><hr/>';
		$html .= '<table class="form-table">';		 	
		foreach ( $this->components as $component ) {
			if( $component->slug == 'checkbox_label_margin' ){
				continue;
			}
			$html .= $component->render( $this->slug );
		}
        $html = str_replace( 'backround', 'background', $html );
        $html = str_replace( 'Backround', 'Background', $html );

		$html .= '</table>';		 	
		return $html;
	}
	public function setComponentValue( $component_slug, $value ){
		$this->components[ $component_slug ]->value = $value;
	}
}