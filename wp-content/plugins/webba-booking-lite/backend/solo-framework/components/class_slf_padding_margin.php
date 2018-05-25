<?php
// Webba Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFPaddingMargin extends SLFComponent {
	
	public function __construct( $param ) {
		parent::__construct( $param );
		$this->valid_type = 'slf-type-pm';
	}

	public function render( $section ){ 
		$parts = explode( ' ', $this->value );
		$html = '<tr>
				<th scope="row">
				' . $this->name . ' 						 
						<span class="slf-component-description">
							' . $this->desc . ' 							
						</span>
					</th>					
					<td>
						<table class="slf-pm-table">
							<tr>
								<td>
								</td>
								<td>
									<input id="'. $this->slug  .'_top"' . ' class="slf-type-pm-sub" data-parent="' . $this->slug . '" type="text" value="' . $parts[0] . '"/>
								</td>
								<td>
								</td>
							</tr>
							<tr>
								<td>
									<input id="'. $this->slug  .'_left"' . '  class="slf-type-pm-sub" data-parent="' . $this->slug . '" type="text" value="' . $parts[3] . '"/>
								</td>
								<td class="pm_center_cell">
									
								</td>
								<td>
									<input id="'. $this->slug  .'_right"' . '  class="slf-type-pm-sub"  data-parent="' . $this->slug . '" type="text" value="' . $parts[1] . '"/>
								</td>
							</tr>
							<tr>
								<td>
								</td>
								<td>
									<input id="'. $this->slug  .'_bottom"' . '  class="slf-type-pm-sub" data-parent="' . $this->slug . '" type="text" value="' . $parts[2] . '"/>
								</td>
								<td>
								</td>
							</tr>
						</table>
					 	<input class="slf-component" type="hidden"  data-prop="'. $this->css_prop .'"  data-class="'. $this->css_class .'"  data-section="' . $section . '" data-type="slf-type-pm" value="' . $this->value . '" id="'. $this->slug  .'" name="' . $this->slug . '">  
					</td>	
				</tr>';
		return $html;

	}

}