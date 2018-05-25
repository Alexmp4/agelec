<?php
	/* a template for displaying the header area */
?>	
<header class="infinite-header-wrap infinite-header-style-side-toggle" >
	<?php
		$display_logo = infinite_get_option('general', 'header-side-toggle-display-logo', 'enable');
		if( $display_logo == 'enable' ){
			echo infinite_get_logo(array('padding' => false));
		}

		$navigation_class = '';
		if( infinite_get_option('general', 'enable-main-navigation-submenu-indicator', 'disable') == 'enable' ){
			$navigation_class = 'infinite-navigation-submenu-indicator ';
		}
	?>
	<div class="infinite-navigation clearfix <?php echo esc_attr($navigation_class); ?>" >
	<?php
		// print main menu
		if( has_nav_menu('main_menu') ){
			infinite_get_custom_menu(array(
				'container-class' => 'infinite-main-menu',
				'button-class' => 'infinite-side-menu-icon',
				'icon-class' => 'fa fa-bars',
				'id' => 'infinite-main-menu',
				'theme-location' => 'main_menu',
				'type' => infinite_get_option('general', 'header-side-toggle-menu-type', 'overlay')
			));
		}
	?>
	</div><!-- infinite-navigation -->
	<?php

		// menu right side
		$enable_search = (infinite_get_option('general', 'enable-main-navigation-search', 'enable') == 'enable')? true: false;
		$enable_cart = (infinite_get_option('general', 'enable-main-navigation-cart', 'enable') == 'enable' && class_exists('WooCommerce'))? true: false;
		if( $enable_search || $enable_cart ){ 
			echo '<div class="infinite-header-icon infinite-pos-bottom" >';

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

			echo '</div>'; // infinite-main-menu-right-wrap
		}

	?>
</header><!-- header -->