<?php
// Webba Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFText extends SLFComponent {
	
	public function __construct( $param ) {
        $this->name = $param['name'];
        $this->desc = $param['desc'];
        $this->slug = $param['slug'];
        $this->value = $param['value'];
        $this->valid_type = 'slf-type-text';
        $this->valid_min = $param['min'];;
        $this->valid_max = $param['max'];;
	}

	public function render(){
		$html = '<tr>
				<th scope="row">
				 ' . $this->name . '
						<span class="slf-component-description">
							' . $this->desc . ' 							
						</span>
					</th>					
					<td>
					 	<input type="text" data-type="slf-type-text" data-valid-min="' . $this->valid_min . '"   data-valid-max="' . $this->valid_max . '"   value="' . $this->value . '" id="'. $this->slug  .'" name="' . $this->slug . '">  
					</td>
				</tr>';
		echo $html;
	}

}