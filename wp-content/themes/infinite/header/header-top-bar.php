<?php
	/* a template for displaying the top bar */

	if( infinite_get_option('general', 'enable-top-bar', 'enable') == 'enable' ){

		$top_bar_width = infinite_get_option('general', 'top-bar-width', 'boxed');
		$top_bar_container_class = '';

		if( $top_bar_width == 'boxed' ){
			$top_bar_container_class = 'infinite-container ';
		}else if( $top_bar_width == 'custom' ){
			$top_bar_container_class = 'infinite-top-bar-custom-container ';
		}else{
			$top_bar_container_class = 'infinite-top-bar-full ';
		}
		
		echo '<div class="infinite-top-bar" >';
		echo '<div class="infinite-top-bar-background" ></div>';
		echo '<div class="infinite-top-bar-container clearfix ' . esc_attr($top_bar_container_class) . '" >';

		$language_flag = infinite_get_wpml_flag();
		$left_text = infinite_get_option('general', 'top-bar-left-text', '');
		if( !empty($left_text) || !empty($language_flag) ){
			echo '<div class="infinite-top-bar-left infinite-item-pdlr">';
			echo gdlr_core_escape_content($language_flag);
			echo gdlr_core_escape_content(gdlr_core_text_filter($left_text));
			echo '</div>';
		}

		$right_text = infinite_get_option('general', 'top-bar-right-text', '');
		$top_bar_social = infinite_get_option('general', 'enable-top-bar-social', 'enable');
		if( !empty($right_text) || $top_bar_social == 'enable' ){
			echo '<div class="infinite-top-bar-right infinite-item-pdlr">';
			if( !empty($right_text) ){
				echo '<div class="infinite-top-bar-right-text">';
				echo gdlr_core_escape_content(gdlr_core_text_filter($right_text));
				echo '</div>';
			}

			if( $top_bar_social == 'enable' ){
				echo '<div class="infinite-top-bar-right-social" >';
				get_template_part('header/header', 'social');
				echo '</div>';	
			}
			echo '</div>';	
		}
		echo '</div>';
		echo '</div>';

	}  // top bar
?>