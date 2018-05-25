<?php
	/*	
	*	Goodlayers Utility File
	*	---------------------------------------------------------------------
	*	This file contains utility function in the goodlayers core plugin
	*	---------------------------------------------------------------------
	*/
	
	// page builder content/text filer to execute the shortcode	
	if( !function_exists('gdlr_core_content_filter') ){
		add_filter( 'gdlr_core_the_content', 'wptexturize'        ); add_filter( 'gdlr_core_the_content', 'convert_smilies'    );
		add_filter( 'gdlr_core_the_content', 'convert_chars'      ); add_filter( 'gdlr_core_the_content', 'wpautop'            );
		add_filter( 'gdlr_core_the_content', 'shortcode_unautop'  ); add_filter( 'gdlr_core_the_content', 'prepend_attachment' );	
		add_filter( 'gdlr_core_the_content', 'do_shortcode', 11   );
		function gdlr_core_content_filter( $content, $main_content = false ){
			if($main_content) return str_replace( ']]>', ']]&gt;', apply_filters('the_content', $content) );
			
			$content = preg_replace_callback( '|(https?://[^\s"<]+)|im', 'gdlr_core_content_oembed', $content );
			
			return apply_filters('gdlr_core_the_content', $content);
		}		
	}
	if( !function_exists('gdlr_core_content_oembed') ){
		function gdlr_core_content_oembed( $link ){
			$html = wp_oembed_get($link[1]);
			
			if( $html ) return $html;
			return $link[1];
		}
	}
	if( !function_exists('gdlr_core_text_filter') ){
		add_filter( 'gdlr_core_text_filter', 'do_shortcode', 11 );
		function gdlr_core_text_filter( $text ){
			return apply_filters('gdlr_core_text_filter', $text);
		}
	}			
	
	// escape content with html
	if( !function_exists('gdlr_core_escape_content') ){
		function gdlr_core_escape_content( $content ){
			return apply_filters('gdlr_core_escape_content', $content);
		}
	}		

	// gdlr esc size
	if( !function_exists('gdlr_core_esc_style') ){
		function gdlr_core_esc_style($atts, $wrap = true){
			if( empty($atts) ) return '';

			$att_style = '';
			foreach($atts as $key => $value){
				if( empty($value) ) continue;
				
				switch($key){
					
					case 'border-radius': 
						$att_style .= "border-radius: {$value};";
						$att_style .= "-moz-border-radius: {$value};";
						$att_style .= "-webkit-border-radius: {$value};";
						break;
					
					case 'gradient': 
						if( is_array($value) && sizeOf($value) > 1 ){
							$att_style .= "background: linear-gradient({$value[0]}, {$value[1]});";
							$att_style .= "-moz-background: linear-gradient({$value[0]}, {$value[1]});";
							$att_style .= "-o-background: linear-gradient({$value[0]}, {$value[1]});";
							$att_style .= "-webkit-background: linear-gradient({$value[0]}, {$value[1]});";
						}
						break;
					
					case 'background':
					case 'background-color':
						if( is_array($value) ){
							$rgba_value = gdlr_core_format_datatype($value[0], 'rgba');
							$att_style .= "{$key}: rgba({$rgba_value}, {$value[1]});";
						}else{
							$att_style .= "{$key}: {$value};";
						}
						break;

					case 'background-image':
						if( is_numeric($value) ){
							$image_url = wp_get_attachment_url($value);
							$att_style .= "background-image: url({$image_url});";
						}else{
							$att_style .= "background-image: url({$value});";
						}
						break;
					
					case 'padding':
					case 'margin':
					case 'border-width':
						if( is_array($value) ){
							if( !empty($value['top']) && $value['right'] && $value['bottom'] && $value['left'] ){
								$att_style .= "{$key}: {$value['top']} {$value['right']} {$value['bottom']} {$value['left']};";
							}else{
								foreach($value as $pos => $val){
									if( $pos != 'settings' ){
										if( $key == 'border-width' ){
											$att_style .= "border-{$pos}-width: {$val};";
										}else{
											$att_style .= "{$key}-{$pos}: {$val};";
										}
									}
								}
							}
						}else{
							$att_style .= "{$key}: {$value};";
						}
						break;
					
					default: 
						$value = is_array($value)? $value[0]: $value;
						$att_style .= "{$key}: {$value};";
				}
			}
			
			if( !empty($att_style) ){
				if( $wrap ){
					return 'style="' . esc_attr($att_style) . '" ';
				}
				return $att_style;
			}
			return '';
		}
	}