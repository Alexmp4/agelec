<?php
// Webba Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFFontStyle extends SLFComponent {
	
	public function __construct( $param ) {
		parent::__construct( $param );
        $this->valid_type = 'slf-type-font-style';
	}

	public function render( $section ){ 

		$styles = array( 'normal', 'italic', 'oblique', 'initial', 'inherit'  );
 			 

		$select = '<select class="slf-type-font-style slf-component" id="'. $this->slug  .'" name="' . $this->slug . '" data-prop="'. $this->css_prop .'"  data-class="'. $this->css_class .'"   data-section="' . $section . '"  >';

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