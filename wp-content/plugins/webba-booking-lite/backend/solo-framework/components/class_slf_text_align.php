<?php
// Webba Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFTextAlign extends SLFComponent {
	
	public function __construct( $param ) {
		parent::__construct( $param );
        $this->valid_type = 'slf-type-text-align';
	}

	public function render( $section ){ 

		$styles = array( 'left', 'center', 'right' );

		$select = '<select class="slf-type-text-align slf-component" id="'. $this->slug  .'" name="' . $this->slug . '" data-prop="'. $this->css_prop .'"  data-class="'. $this->css_class .'"   data-section="' . $section . '" >';

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