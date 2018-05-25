<?php
// Webba Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFBorder extends SLFComponent {
	
	public function __construct( $param ) {
		parent::__construct( $param );
        $this->valid_type = 'slf-type-border';
	}

	public function render( $section ){ 
		$parts = explode( ' ', $this->value );
		$width = intval( $parts[0] );
		$border_types = array( 'none', 'hidden', 'dotted', 'dashed', 'solid', 'double', 'groove', 'ridge', 'inset', 'outset' );

		$select = '<select id="'. $this->slug  .'_type"' . ' class="slf-type-border-sub" data-parent="' . $this->slug . '" >';
		foreach ($border_types  as $border_type ) {
			$selected = '';
			if ( $parts[1] == $border_type ){
				$selected = ' selected ';
			} else {
				$selected = '';
			}
			$select .= '<option ' . $selected . ' value="'.$border_type.'">' . $border_type . '</option>';
		}

		$select .= '</select>';
		$html = '<tr>
				<th scope="row">
				' . $this->name . ' 						 
						<span class="slf-component-description slf-type-border-type">
							' . $this->desc . ' 							
						</span>
					</th>					
					<td>
						<table class="slf-border-table">
							<tr>
								<td>
									<input id="'. $this->slug  .'_width"' . ' class="slf-type-border-sub slf-type-border-width" data-parent="' . $this->slug . '" type="text" value="' . $width . '"/>
									px
								</td>
								<td>
									 ' . $select . '
								</td>
								<td>
									<input id="'. $this->slug  .'_color"' . ' class="slf-type-border-sub slf-type-border-color slf-color-hex" data-parent="' . $this->slug . '" type="text" value="' . $parts[2] . '"/>
								</td>
							</tr>						 
						</table> 

					 	<input class="slf-component" type="hidden"  data-prop="'. $this->css_prop .'"  data-class="'. $this->css_class .'"   data-section="' . $section . '" data-type="slf-type-border" value="' . $this->value . '" id="'. $this->slug  .'" name="' . $this->slug . '">  
					</td>
				</tr>';
		return $html;

	}

}