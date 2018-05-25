<?php
// Webba Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFFontWeight extends SLFComponent {
	
	public function __construct( $param ) {
		parent::__construct( $param );
        $this->valid_type = 'slf-type-font-weight';
	}

	public function render( $section ){ 

		$styles = array( 'normal', 'bold', 'bolder', 'lighter', 'initial', 'inherit','900'  );
 			 
		$select = '<select class="slf-type-font-weight slf-component" id="'. $this->slug  .'" name="' . $this->slug . '" data-prop="'. $this->css_prop .'"  data-class="'. $this->css_class .'"   data-section="' . $section . '" >';

		foreach ( $styles  as $style ) {
			$selected = '';
			if (  $this->value == $style ){
				$selected = ' selected ';
			} else {
				$selected = '';
			}
			$select .= '<option ' . $selected . ' value="'.$style.'">' . $style . '</option>';
		}

		$select .= '</select>';
		$html = '<tr>
				 <th scope="row">
				' . $this->name . ' 						 
						<span class="slf-component-description slf-type-border-type">
							' . $this->desc . ' 							
						</span>
					</th>					
					<td>'.
 						$select	
 					.'</td>
				</tr>';
		return $html;
	}

}