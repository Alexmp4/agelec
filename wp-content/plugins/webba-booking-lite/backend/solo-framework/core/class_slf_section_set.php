<?php
// Webba Framework main class
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFSectionSet extends stdClass {

	public $sections = array();

	public function __construct( $param = array() ) {
        $this->slug = $param['slug'];
        $this->css_default = $param['css_default'];
		$this->css_custom = $param['css_custom'];
		$this->name = $param['name'];
	}
	public function addSection( $obj ){
		$this->sections[$obj->slug] = $obj;
	}
	public function render(){
		$html = '';
		foreach( $this->sections as $section ){
			$html .= '<div class="slf-section" id="'. $section->slug .'">';
				$html .= $section->render();
			$html .= '</div>';

		}
		return $html;
	}
	public function renderMenu(){
		$html = '<ul>';
		$first = true;
		foreach( $this->sections as $section ){
			$class = '';
			if ( $first ){
				$class = 'active';
				$first = false;
			} else {
				$class = '';
			}
		 	$html .= '<li class="'.$class.' slf-menu-item"><a class="slf-menu-link" href="#'. $section->slug .'">' . $section->name . '</a></li>';
		}
		$html .= '</ul>';
		return $html;
	}
	public function loadSectionAssets(){
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueStyle') );
	}
	public function enqueueStyle(){
		$style_name =  $this->slug . '-default';
		wp_enqueue_style( $style_name, plugins_url( $this->css_default, __FILE__ ) );
 	}
	public function compileAdminCss( $data ){
		$result = '';
		$class_names = array();
		foreach ( $data->components as $component ) {

			// checkbox moz fix
			if ( $component['css_class'] == 'wbk-checkbox' || $component['css_class'] == 'wbk-checkbox:after'  ){
				$component['css_class'] = 'wbk-checkbox + label::before, .wbk-checkbox + span::before';
			}
			// checkbox moz fix
			$class_names[] = $component['css_class'];

		}
		$class_names = array_unique( $class_names );
		foreach ( $class_names as $class_name ) {
			$result .= '.' . $class_name . '{' . PHP_EOL;
				foreach ( $data->components as $component ) {
					if ( $class_name == 'wbk-checkbox-label' && $component['css_prop'] == 'margin' ){
						continue;
					}
					// checkbox moz fix
					if ( $component['css_class'] == 'wbk-checkbox' || $component['css_class'] == 'wbk-checkbox:after'  ){
						$component['css_class'] = 'wbk-checkbox + label::before, .wbk-checkbox + span::before';
					}
					// checkbox moz fix

					$class_names[] = $component['css_class'];


					if ( $class_name == $component[ 'css_class'] ){
						$result .= 	$component[ 'css_prop'] . ': '. $component[ 'value' ] . ';' . PHP_EOL;
					}
				}
			$result .= '}' . PHP_EOL;
		}
		$path_to_css =   dirname( __FILE__ ) . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'preview'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR. $this->slug .'.css';

		$css_file = fopen( $path_to_css, 'w' ) or die( 'Unable to create css file ');
		fwrite( $css_file, $result );
		fclose( $css_file );
	}
	public function compileFrontendCss( $data ){
		$result = '';
		$class_names = array();
		foreach ( $data->components as $component ) {
			// checkbox moz fix
			if ( $component['css_class'] == 'wbk-checkbox' || $component['css_class'] == 'wbk-checkbox:after'  ){
				$component['css_class'] = 'wbk-checkbox + label::before, .wbk-checkbox + span::before';
			}
			// checkbox moz fix
			$class_names[] = $component['css_class'];


		}
		$class_names = array_unique( $class_names );
		foreach ( $class_names as $class_name ) {
			$modifier = '';
			if ( $class_name == 'wbk-input' ){
				$modifier = ', .wbk-text, .wbk-textarea, .wbk-select, .wbk-email-custom, .StripeElement ';
			}  
			if ( $class_name == 'wbk-input-label' ){
				$modifier = ', .wbk-amount-label ';
			}  
			$result .= '.' . $class_name . $modifier . '{' . PHP_EOL;
				foreach ( $data->components as $component ) {
					if ( $class_name == 'wbk-checkbox-label' && $component['css_prop'] == 'margin' ){
						continue;
					}
					// checkbox moz fix
					if ( $component['css_class'] == 'wbk-checkbox' || $component['css_class'] == 'wbk-checkbox:after'  ){
						$component['css_class'] = 'wbk-checkbox + label::before, .wbk-checkbox + span::before';
					}
					// checkbox moz fix
					$class_names[] = $component['css_class'];

					if ( $class_name == $component[ 'css_class'] ){
						$result .= 	$component[ 'css_prop'] . ': '. $component[ 'value' ] . ' !important;' . PHP_EOL;
					}
				}
			$result .= '}' . PHP_EOL;
		}
		$path_to_css =  WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'webba-booking-lite' . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'wbk-frontend-custom-style.css';
 		$css_file = fopen( $path_to_css, 'w' ) or die( 'Unable to create css file ');
		fwrite( $css_file, $result );
		fclose( $css_file );
	}
	public function compileFrontendCssFromStored(){
		$result = '';
		$class_names = array();
		$components = array();
		foreach ( $this->sections as $section ) {
			 foreach ( $section->components as $component ) {
				if (  $component->css_class == 'wbk-checkbox-label' && $component->css_prop == 'margin' ){
					continue;
				}
		 		// checkbox moz fix
				if ( $component->css_class == 'wbk-checkbox' || $component->css_class == 'wbk-checkbox:after'  ){
					$component->css_class = 'wbk-checkbox + label::before, .wbk-checkbox + span::before';
				}
				// checkbox moz fix

 				$class_names[] = $component->css_class;
 				$components[] = $component;
 			 }
		}
		$class_names = array_unique( $class_names );
		foreach ( $class_names as $class_name ) {
			if ( $class_name == 'wbk-input' ){
				$modifier = ', .wbk-text, .wbk-textarea, .wbk-select, .StripeElement ';
			} else {
				$modifier = '';
			}
			$result .= '.' . $class_name . $modifier . '{' . PHP_EOL;
			foreach ( $components as $component ) {
				if ( $component->class_name == 'wbk-checkbox-label' && $component->css_prop  == 'margin' ){
					continue;
				}
				// checkbox moz fix
				if ( $component->css_class == 'wbk-checkbox' || $component->css_class == 'wbk-checkbox:after'  ){
					$component->css_class =  'wbk-checkbox + label::before, .wbk-checkbox + span::before';
				}
				// checkbox moz fix
				if ( $class_name == $component->css_class ){
						$result .= 	$component->css_prop . ': '. $component->value . ' !important;' . PHP_EOL;
				}
			}
			$result .= '}' . PHP_EOL;
		}
		$path_to_css =  WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'webba-booking-lite' . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'wbk-frontend-custom-style.css';
 		$css_file = fopen( $path_to_css, 'w' ) or die( 'Unable to create css file ');
		fwrite( $css_file, $result );
		fclose( $css_file );
	}
	public function loadFromPreset(){
		echo 'preset loaded';
	}
	public function hasSection( $slug ){		 
		foreach( $this->sections as $section ){
			if( $section->slug == $slug ){
				return TRUE;
			}
		}
		return FALSE;
	}

}