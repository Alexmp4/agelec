<?php
/**
 * The template for displaying the footer
 */
	
	$post_option = infinite_get_post_option(get_the_ID());
	if( empty($post_option['enable-footer']) || $post_option['enable-footer'] == 'default' ){
		$enable_footer = infinite_get_option('general', 'enable-footer', 'enable');
	}else{
		$enable_footer = $post_option['enable-footer'];
	}	
	if( empty($post_option['enable-copyright']) || $post_option['enable-copyright'] == 'default' ){
		$enable_copyright = infinite_get_option('general', 'enable-copyright', 'enable');
	}else{
		$enable_copyright = $post_option['enable-copyright'];
	}

	$fixed_footer = infinite_get_option('general', 'fixed-footer', 'disable');
	echo '</div>'; // infinite-page-wrapper

	if( $enable_footer == 'enable' || $enable_copyright == 'enable' ){

		if( $fixed_footer == 'enable' ){
			echo '</div>'; // infinite-body-wrapper

			echo '<footer class="infinite-fixed-footer" id="infinite-fixed-footer" >';
		}else{
			echo '<footer>';
		}

		if( $enable_footer == 'enable' ){

			$infinite_footer_layout = array(
				'footer-1'=>array('infinite-column-60'),
				'footer-2'=>array('infinite-column-15', 'infinite-column-15', 'infinite-column-15', 'infinite-column-15'),
				'footer-3'=>array('infinite-column-15', 'infinite-column-15', 'infinite-column-30',),
				'footer-4'=>array('infinite-column-20', 'infinite-column-20', 'infinite-column-20'),
				'footer-5'=>array('infinite-column-20', 'infinite-column-40'),
				'footer-6'=>array('infinite-column-40', 'infinite-column-20'),
			);
			$footer_style = infinite_get_option('general', 'footer-style');
			$footer_style = empty($footer_style)? 'footer-2': $footer_style;

			$count = 0;
			$has_widget = false;
			foreach( $infinite_footer_layout[$footer_style] as $layout ){ $count++;
				if( is_active_sidebar('footer-' . $count) ){ $has_widget = true; }
			}

			if( $has_widget ){ 	

				$footer_column_divider = infinite_get_option('general', 'enable-footer-column-divider', 'enable');
				$extra_class  = ($footer_column_divider == 'enable')? ' infinite-with-column-divider': '';

				echo '<div class="infinite-footer-wrapper ' . esc_attr($extra_class) . '" >';
				echo '<div class="infinite-footer-container infinite-container clearfix" >';
				
				$count = 0;
				foreach( $infinite_footer_layout[$footer_style] as $layout ){ $count++;
					echo '<div class="infinite-footer-column infinite-item-pdlr ' . esc_attr($layout) . '" >';
					if( is_active_sidebar('footer-' . $count) ){
						dynamic_sidebar('footer-' . $count); 
					}
					echo '</div>';
				}
				
				echo '</div>'; // infinite-footer-container
				echo '</div>'; // infinite-footer-wrapper 
			}
		} // enable footer

		if( $enable_copyright == 'enable' ){
			$copyright_style = infinite_get_option('general', 'copyright-style', 'center');
			
			if( $copyright_style == 'center' ){
				$copyright_text = infinite_get_option('general', 'copyright-text');

				if( !empty($copyright_text) ){
					echo '<div class="infinite-copyright-wrapper" >';
					echo '<div class="infinite-copyright-container infinite-container">';
					echo '<div class="infinite-copyright-text infinite-item-pdlr">';
					echo gdlr_core_escape_content(gdlr_core_text_filter($copyright_text));
					echo '</div>';
					echo '</div>';
					echo '</div>'; // infinite-copyright-wrapper
				}
			}else if( $copyright_style == 'left-right' ){
				$copyright_left = infinite_get_option('general', 'copyright-left');
				$copyright_right = infinite_get_option('general', 'copyright-right');

				if( !empty($copyright_left) || !empty($copyright_right) ){
					echo '<div class="infinite-copyright-wrapper" >';
					echo '<div class="infinite-copyright-container infinite-container clearfix">';
					if( !empty($copyright_left) ){
						echo '<div class="infinite-copyright-left infinite-item-pdlr">';
						echo gdlr_core_escape_content(gdlr_core_text_filter($copyright_left));
						echo '</div>';
					}

					if( !empty($copyright_right) ){
						echo '<div class="infinite-copyright-right infinite-item-pdlr">';
						echo gdlr_core_escape_content(gdlr_core_text_filter($copyright_right));
						echo '</div>';
					}
					echo '</div>';
					echo '</div>'; // infinite-copyright-wrapper
				}
			}
		}

		echo '</footer>';

		if( $fixed_footer == 'disable' ){
			echo '</div>'; // infinite-body-wrapper
		}
		echo '</div>'; // infinite-body-outer-wrapper

	// disable footer	
	}else{
		echo '</div>'; // infinite-body-wrapper
		echo '</div>'; // infinite-body-outer-wrapper
	}

	$header_style = infinite_get_option('general', 'header-style', 'plain');
	
	if( $header_style == 'side' || $header_style == 'side-toggle' ){
		echo '</div>'; // infinite-header-side-nav-content
	}

	$back_to_top = infinite_get_option('general', 'enable-back-to-top', 'disable');
	if( $back_to_top == 'enable' ){
		echo '<a href="#infinite-top-anchor" class="infinite-footer-back-to-top-button" id="infinite-footer-back-to-top-button"><i class="fa fa-angle-up" ></i></a>';
	}
?>

<?php wp_footer(); ?>

</body>
</html>