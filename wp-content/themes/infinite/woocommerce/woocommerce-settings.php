<?php

	// declare woocommerce support
	add_action('after_setup_theme', 'infinite_woocommerce_support');
	if( !function_exists( 'infinite_woocommerce_support' ) ){
		function infinite_woocommerce_support(){
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
		}
	}	

	// modify woocommerce wrapper
	remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
	remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

	add_action('woocommerce_before_main_content', 'infinite_woocommerce_wrapper_start', 10);
	if( !function_exists( 'infinite_woocommerce_wrapper_start' ) ){
		function infinite_woocommerce_wrapper_start(){
			echo '<div class="infinite-content-container infinite-container">';
			echo '<div class="infinite-content-area infinite-item-pdlr infinite-sidebar-style-none clearfix" >';
		}
	}

	add_action('woocommerce_after_main_content', 'infinite_woocomemrce_wrapper_end', 10);
	if( !function_exists( 'infinite_woocomemrce_wrapper_end' ) ){
		function infinite_woocomemrce_wrapper_end(){
			echo '</div>'; // infinite-content-area
			echo '</div>'; // infinite-content-container
		}
	}

	// remove breadcrumbs on single product
	add_action('wp', 'infinite_init_woocommerce_hook');
	if( !function_exists( 'infinite_init_woocommerce_hook' ) ){
		function infinite_init_woocommerce_hook(){
			if( is_single() && get_post_type() == 'product' ){ 
				add_filter('woocommerce_product_description_heading', 'infinite_remove_woocommerce_tab_heading');
				add_filter('woocommerce_product_additional_information_heading', 'infinite_remove_woocommerce_tab_heading');

				remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
				remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
				remove_action('woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating', 10);

				add_action('woocommerce_review_after_comment_text', 'woocommerce_review_display_rating', 10);
			}
		}
	}
	
	if( !function_exists( 'infinite_remove_woocommerce_tab_heading' ) ){
		function infinite_remove_woocommerce_tab_heading( $title ){
			return '';
		}
	}

	add_filter('woocommerce_review_gravatar_size', 'infinite_woocommerce_review_gravatar_size');
	if( !function_exists( 'infinite_woocommerce_review_gravatar_size' ) ){
		function infinite_woocommerce_review_gravatar_size( $size ){
			return 120;
		}
	}

	if( !function_exists('infinite_get_woocommerce_bar') ){
		function infinite_get_woocommerce_bar(){

			global $woocommerce;
			
			if(!empty($woocommerce)){
				
				echo '<span class="infinite-top-cart-count">' . $woocommerce->cart->cart_contents_count . '</span>';
				echo '<div class="infinite-top-cart-hover-area" ></div>';

				echo '<div class="infinite-top-cart-content-wrap" >';
				echo '<div class="infinite-top-cart-content" >';
				echo '<div class="infinite-top-cart-count-wrap" >';
				echo '<span class="head">' . esc_html__('Items : ', 'infinite') . ' </span>';
				echo '<span class="infinite-top-cart-count">' . $woocommerce->cart->cart_contents_count . '</span>'; 
				echo '</div>';
				
				echo '<div class="infinite-top-cart-amount-wrap" >';
				echo '<span class="head">' . esc_html__('Subtotal :', 'infinite') . ' </span>';
				echo '<span class="infinite-top-cart-amount">' . $woocommerce->cart->get_cart_total() . '</span>';
				echo '</div>';
				
				echo '<a class="infinite-top-cart-button" href="' . esc_url($woocommerce->cart->get_cart_url()) . '" >';
				echo esc_html__('View Cart', 'infinite');
				echo '</a>';

				echo '<a class="infinite-top-cart-checkout-button" href="' . esc_url($woocommerce->cart->get_checkout_url()) . '" >';
				echo esc_html__('Check Out', 'infinite');
				echo '</a>';
				echo '</div>';
				echo '</div>';
			}
		}
	}

	add_filter('woocommerce_add_to_cart_fragments', 'infinite_woocommerce_cart_ajax');
	if( !function_exists('infinite_woocommerce_cart_ajax') ){
		function infinite_woocommerce_cart_ajax($fragments){
			global $woocommerce;

			$fragments['span.infinite-top-cart-count'] = '<span class="infinite-top-cart-count">' . $woocommerce->cart->cart_contents_count . '</span>'; 
			$fragments['span.infinite-top-cart-amount'] = '<span class="infinite-top-cart-amount">' . $woocommerce->cart->get_cart_total() . '</span>';

			return $fragments;
		}
	}	

	add_filter('woocommerce_output_related_products_args', 'infinite_related_products_args');
	if( !function_exists('infinite_related_products_args') ){
		function infinite_related_products_args($args){
			if( class_exists('gdlr_core_pb_element_product') ){
				$num_fetch = infinite_get_option('general', 'woocommerce-related-product-num-fetch', '4');
				$args['posts_per_page'] = $num_fetch;
			}
			
			return $args;
		}
	}