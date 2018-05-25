<?php
	/* a template for displaying the header top slider */

	$post_option = infinite_get_post_option(get_the_ID());

	echo '<div class="infinite-above-navigation-slider" >';
	if( $post_option['header-slider'] == 'layer-slider' ){
		echo do_shortcode('[layerslider id="' . esc_attr($post_option['layer-slider-id']) . '"]');
	}else if( $post_option['header-slider'] == 'master-slider' ){
		echo get_masterslider($post_option['master-slider-id']);
	}else if( $post_option['header-slider'] == 'revolution-slider' ){
		if( class_exists('RevSliderSlider') ){
			$slider_obj = new RevSliderSlider();
			$rev_slider = $slider_obj->getArrSlidersShort();

			if( !empty($rev_slider[$post_option['revolution-slider-id']]) ){
				echo do_shortcode('[rev_slider alias="' . esc_attr($post_option['revolution-slider-id']) . '"]');
			}else{
				echo '<p style="text-align: center;" >' . esc_html__('Please import and select the slides you want to show at the header area from the "page options" area again.', 'infinite') . '</p>';
			}
		}
	}
	echo '</div>';