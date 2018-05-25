<?php
// Webba Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFSizePx extends SLFComponent {
	
	public function __construct( $param ) {
		parent::__construct( $param );
        $this->valid_type = 'slf-type-size-px';
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
						<input type="text" class="slf-component slf-type-size-px" type="text"  data-prop="'. $this->css_prop .'"  data-class="'. $this->css_class .'"   data-section="' . $section . '" data-type="slf-type-color" value="' . $this->value . '" id="'. $this->slug  .'" name="' . $this->slug . '">
					</td>
				</tr>';
		return $html;
	}

}