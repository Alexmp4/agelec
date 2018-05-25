<?php
	/* a template for displaying the header area */

	// header container
	$header_width = infinite_get_option('general', 'header-width', 'boxed');
	
	if( $header_width == 'boxed' ){
		$header_container_class = ' infinite-container';
	}else if( $header_width == 'custom' ){
		$header_container_class = ' infinite-header-custom-container';
	}else{
		$header_container_class = ' infinite-header-full';
	}

	$header_style = infinite_get_option('general', 'header-boxed-style', 'menu-right');
	$navigation_offset = infinite_get_option('general', 'fixed-navigation-anchor-offset', '');

	$header_wrap_class  = ' infinite-style-' . $header_style;
	$header_wrap_class .= ' infinite-sticky-navigation infinite-style-slide';
?>	
<header class="infinite-header-wrap infinite-header-style-boxed <?php echo esc_attr($header_wrap_class); ?>" <?php
		if( !empty($navigation_offset) ){
			echo 'data-navigation-offset="' . esc_attr($navigation_offset) . '" ';
		}
	?> >
	<div class="infinite-header-container clearfix <?php echo esc_attr($header_container_class); ?>">
		<div class="infinite-header-container-inner clearfix">	

			<div class="infinite-header-background  infinite-item-mglr" ></div>
			<div class="infinite-header-container-item clearfix">
				<?php

					if( $header_style == 'splitted-menu' ){
						add_filter('infinite_center_menu_item', 'infinite_get_logo');
					}else{
						echo infinite_get_logo();
					}

					$navigation_class = '';
					if( infinite_get_option('general', 'enable-main-navigation-submenu-indicator', 'disable') == 'enable' ){
						$navigation_class = 'infinite-navigation-submenu-indicator ';
					}
				?>
				<div class="infinite-navigation infinite-item-pdlr clearfix <?php echo esc_attr($navigation_class); ?>" >
				<?php
					// print main menu
					if( has_nav_menu('main_menu') ){
						echo '<div class="infinite-main-menu" id="infinite-main-menu" >';
						wp_nav_menu(array(
							'theme_location'=>'main_menu', 
							'container'=> '', 
							'menu_class'=> 'sf-menu',
							'walker' => new infinite_menu_walker()
						));
						$slide_bar = infinite_get_option('general', 'navigation-slide-bar', 'enable');
						if( $slide_bar == 'enable' ){
							echo '<div class="infinite-navigation-slide-bar" id="infinite-navigation-slide-bar" ></div>';
						}
						echo '</div>';
					}

					// menu right side
					$menu_right_class = '';
					if( $header_style == 'center-menu' || $header_style == 'splitted-menu' ){
						$menu_right_class = ' infinite-item-mglr infinite-navigation-top infinite-navigation-right';
					}

					$enable_search = (infinite_get_option('general', 'enable-main-navigation-search', 'enable') == 'enable')? true: false;
					$enable_cart = (infinite_get_option('general', 'enable-main-navigation-cart', 'enable') == 'enable' && class_exists('WooCommerce'))? true: false;
					$enable_right_button = (infinite_get_option('general', 'enable-main-navigation-right-button', 'disable') == 'enable')? true: false;
					if( has_nav_menu('right_menu') || $enable_search || $enable_cart || $enable_right_button ){
						echo '<div class="infinite-main-menu-right-wrap clearfix ' . esc_attr($menu_right_class) . '" >';

						// search icon
						if( $enable_search ){
							echo '<div class="infinite-main-menu-search" id="infinite-top-search" >';
							echo '<i class="fa fa-search" ></i>';
							echo '</div>';
							infinite_get_top_search();
						}

						// cart icon
						if( $enable_cart ){
							echo '<div class="infinite-main-menu-cart" id="infinite-main-menu-cart" >';
							echo '<i class="fa fa-shopping-cart" ></i>';
							infinite_get_woocommerce_bar();
							echo '</div>';
						}

						// menu right button
						if( $enable_right_button ){
							$button_class = 'infinite-style-' . infinite_get_option('general', 'main-navigation-right-button-style', 'default');
							$button_link = infinite_get_option('general', 'main-navigation-right-button-link', '');
							$button_link_target = infinite_get_option('general', 'main-navigation-right-button-link-target', '_self');
							echo '<a class="infinite-main-menu-right-button ' . esc_attr($button_class) . '" href="' . esc_url($button_link) . '" target="' . esc_attr($button_link_target) . '" >';
							echo infinite_get_option('general', 'main-navigation-right-button-text', '');
							echo '</a>';
						}

						// print right menu
						if( has_nav_menu('right_menu') && $header_style != 'splitted-menu' ){
							infinite_get_custom_menu(array(
								'container-class' => 'infinite-main-menu-right',
								'button-class' => 'infinite-right-menu-button infinite-top-menu-button',
								'icon-class' => 'fa fa-bars',
								'id' => 'infinite-right-menu',
								'theme-location' => 'right_menu',
								'type' => infinite_get_option('general', 'right-menu-type', 'right')
							));
						}

						echo '</div>'; // infinite-main-menu-right-wrap

						if( has_nav_menu('right_menu') && $header_style == 'splitted-menu' ){
							echo '<div class="infinite-main-menu-left-wrap clearfix infinite-item-pdlr infinite-navigation-top infinite-navigation-left" >';
							infinite_get_custom_menu(array(
								'container-class' => 'infinite-main-menu-right',
								'button-class' => 'infinite-right-menu-button infinite-top-menu-button',
								'icon-class' => 'fa fa-bars',
								'id' => 'infinite-right-menu',
								'theme-location' => 'right_menu',
								'type' => infinite_get_option('general', 'right-menu-type', 'right')
							));
							echo '</div>';
						}
					}
				?>
				</div><!-- infinite-navigation -->

			</div><!-- infinite-header-container-inner -->
		</div><!-- infinite-header-container-item -->
	</div><!-- infinite-header-container -->
</header><!-- header -->