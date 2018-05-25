<?php
// Webba Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFColor extends SLFComponent {
	
	public function __construct( $param ) {
		parent::__construct( $param );
        $this->valid_type = 'slf-type-color';
	}

	public function render( $section ){ 
		$html = '<tr>
				 <th scope="row">
				' . $this->name . ' 						 
						<span class="slf-component-description slf-type-border-type">
							' . $this->desc . ' 							
						</span>
					</th>					
					<td>
						<input class="slf-component slf-color-hex slf-type-color" type="text"  data-prop="'. $this->css_prop .'"  data-class="'. $this->css_class .'"   data-section="' . $section . '" data-type="slf-type-color" value="' . $this->value . '" id="'. $this->slug  .'" name="' . $this->slug . '">  
					</td>
				</tr>';
		return $html;
	}

}