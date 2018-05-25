<?php
	
	add_filter('revslider_getUsedFonts', 'infinite_revslider_getUsedFonts');
	if( !function_exists('infinite_revslider_getUsedFonts') ){
		function infinite_revslider_getUsedFonts( $used_font ){

			global $gdlr_core_font_loader;
			if( empty($gdlr_core_font_loader) ){
				$gdlr_core_font_loader = new gdlr_core_font_loader();
			}

			$theme_fonts = $gdlr_core_font_loader->get_google_font('used-font');
			if( !empty($theme_fonts) ){
				foreach( $theme_fonts as $font_family => $val ){
					unset($used_font[$font_family]);
				}
			}
			return $used_font;
		}
	}
	