<?php
	/*	
	*	Goodlayers Sidebar Generator
	*	---------------------------------------------------------------------
	*	This file create the class that help you to controls the sidebar 
	*	at the appearance > widget area
	*	---------------------------------------------------------------------
	*/
	
	if( !class_exists('gdlr_core_sidebar_generator') ){
		
		class gdlr_core_sidebar_generator{

			private $sidebar_option_name = 'gdlrcst_sidebar_name';
			private $sidebars = array();
			private $footer_widgets = array();
			private $sidebar_args = array();
			
			function __construct( $sidebar_args = array() ){
				
				// initialize the variable
				$this->footer_widgets = array(
					array( 'name'=>esc_html__('Footer 1', 'infinite'), 'id'=>'footer-1', 'description'=>esc_html__('Footer Column 1', 'infinite') ), 
					array( 'name'=>esc_html__('Footer 2', 'infinite'), 'id'=>'footer-2', 'description'=>esc_html__('Footer Column 2', 'infinite') ), 
					array( 'name'=>esc_html__('Footer 3', 'infinite'), 'id'=>'footer-3', 'description'=>esc_html__('Footer Column 3', 'infinite') ), 
					array( 'name'=>esc_html__('Footer 4', 'infinite'), 'id'=>'footer-4', 'description'=>esc_html__('Footer Column 4', 'infinite') )
				);
				
				$this->sidebars = get_option($this->sidebar_option_name, array());
				if( !is_array($this->sidebars) ){ $this->sidebars = array(); }
				
				$this->sidebar_args = wp_parse_args( $sidebar_args, array(
					'before_widget' => '<div id="%1$s" class="widget %2$s gdlr-core-widget">',
					'after_widget'  => '</div>',
					'before_title'  => '<h3 class="gdlr-core-widget-title">',
					'after_title'   => '</h3><div class="clear"></div>' ) );
				
				// add action to register existing sidebar
				add_action('widgets_init', array(&$this, 'register_sidebar_widget'));
								
			}
			
			// register sidebar to use in widget area
			function register_sidebar_widget(){

				$footer_args = apply_filters('gdlr_core_footer_widget_args', $this->sidebar_args);
				$sidebar_args = apply_filters('gdlr_core_sidebar_widget_args', $this->sidebar_args);

				$sidebar_args['name'] = __('Preset', 'infinite');
				$sidebar_args['id'] = 'gdlr-core-sidebar-preset';
				$sidebar_args['description'] = esc_html__('Another Custom widget area', 'infinite');
				register_sidebar($sidebar_args);

				// widget for footer section	
				foreach ( $this->footer_widgets as $widget ){
					$footer_args['name'] = esc_html($widget['name']);
					$footer_args['id'] = sanitize_title($widget['id']);
					$footer_args['description'] = empty($widget['description'])? '': $widget['description'];

					register_sidebar($footer_args);
				}
				
				// widget for content section
				$sidebar_args['class'] = 'gdlr-core-dynamic-widget';
				foreach ( $this->sidebars as $sidebar ){
					$sidebar_args['name'] = esc_html($sidebar['name']);
					$sidebar_args['id'] = sanitize_title($sidebar['id']);
					$sidebar_args['description'] = esc_html__('Custom widget area', 'infinite');

					register_sidebar($sidebar_args);
				}
				
			}
			
		} // gdlr_core_sidebar_generator
		
	} // class_exists