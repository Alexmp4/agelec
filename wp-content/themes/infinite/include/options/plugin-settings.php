<?php
	/*	
	*	Goodlayers Option
	*	---------------------------------------------------------------------
	*	This file store an array of theme options
	*	---------------------------------------------------------------------
	*/	

	// save the css/js file 
	add_action('gdlr_core_after_save_theme_option', 'infinite_gdlr_core_after_save_theme_option');
	if( !function_exists('infinite_gdlr_core_after_save_theme_option') ){
		function infinite_gdlr_core_after_save_theme_option(){
			if( function_exists('gdlr_core_generate_combine_script') ){
				infinite_clear_option();

				gdlr_core_generate_combine_script(array(
					'lightbox' => infinite_gdlr_core_lightbox_type()
				));
			}
		}
	}

	// add the option
	$infinite_admin_option->add_element(array(
	
		// plugin head section
		'title' => esc_html__('Miscellaneous', 'infinite'),
		'slug' => INFINITE_SHORT_NAME . '_plugin',
		'icon' => get_template_directory_uri() . '/include/options/images/plugin.png',
		'options' => array(
		
			// starting the subnav
			'thumbnail-sizing' => array(
				'title' => esc_html__('Thumbnail Sizing', 'infinite'),
				'customizer' => false,
				'options' => array(
				
					'enable-srcset' => array(
						'title' => esc_html__('Enable Srcset', 'infinite'),
						'type' => 'checkbox',
						'default' => 'disable',
						'description' => esc_html__('Enable this option will improve the performance by resizing the image based on the screensize. Please be cautious that this will generate multiple images on your server.', 'infinite')
					),
					'thumbnail-sizing' => array(
						'title' => esc_html__('Add Thumbnail Size', 'infinite'),
						'type' => 'custom',
						'item-type' => 'thumbnail-sizing',
						'wrapper-class' => 'gdlr-core-fullsize'
					),
					
				) // thumbnail-sizing-options
			), // thumbnail-sizing-nav		
			'plugins' => array(
				'title' => esc_html__('Plugins', 'infinite'),
				'options' => array(

					'lightbox' => array(
						'title' => esc_html__('Lightbox Type', 'infinite'),
						'type' => 'combobox',
						'options' => array(
							'ilightbox' => esc_html__('ilightbox', 'infinite'),
							'strip' => esc_html__('Strip', 'infinite'),
						)
					),
					'ilightbox-skin' => array(
						'title' => esc_html__('iLightbox Skin', 'infinite'),
						'type' => 'combobox',
						'options' => array(
							'dark' => esc_html__('Dark', 'infinite'),
							'light' => esc_html__('Light', 'infinite'),
							'mac' => esc_html__('Mac', 'infinite'),
							'metro-black' => esc_html__('Metro Black', 'infinite'),
							'metro-white' => esc_html__('Metro White', 'infinite'),
							'parade' => esc_html__('Parade', 'infinite'),
							'smooth' => esc_html__('Smooth', 'infinite'),		
						),
						'condition' => array( 'lightbox' => 'ilightbox' )
					),
					'link-to-lightbox' => array(
						'title' => esc_html__('Turn Image Link To Open In Lightbox', 'infinite'),
						'type' => 'checkbox',
						'default' => 'enable'
					),
					'lightbox-video-autoplay' => array(
						'title' => esc_html__('Enable Video Autoplay On Lightbox', 'infinite'),
						'type' => 'checkbox',
						'default' => 'enable'
					),
					
				) // plugin-options
			), // plugin-nav		
			'additional-script' => array(
				'title' => esc_html__('Custom Css/Js', 'infinite'),
				'options' => array(
				
					'additional-css' => array(
						'title' => esc_html__('Additional CSS ( without <style> tag )', 'infinite'),
						'type' => 'textarea',
						'data-type' => 'text',
						'selector' => '#gdlr#',
						'wrapper-class' => 'gdlr-core-fullsize'
					),
					'additional-mobile-css' => array(
						'title' => esc_html__('Mobile CSS ( screen below 767px )', 'infinite'),
						'type' => 'textarea',
						'data-type' => 'text',
						'selector' => '@media only screen and (max-width: 767px){ #gdlr# }',
						'wrapper-class' => 'gdlr-core-fullsize'
					),
					'additional-head-script' => array(
						'title' => esc_html__('Additional Head Script ( without <script> tag )', 'infinite'),
						'type' => 'textarea',
						'wrapper-class' => 'gdlr-core-fullsize',
						'descriptin' => esc_html__('Eg. For analytics', 'infinite')
					),
					'additional-script' => array(
						'title' => esc_html__('Additional Script ( without <script> tag )', 'infinite'),
						'type' => 'textarea',
						'wrapper-class' => 'gdlr-core-fullsize'
					),
					
				) // additional-script-options
			), // additional-script-nav	
			'maintenance' => array(
				'title' => esc_html__('Maintenance Mode', 'infinite'),
				'options' => array(		
					'enable-maintenance' => array(
						'title' => esc_html__('Enable Maintenance / Coming Soon Mode', 'infinite'),
						'type' => 'checkbox',
						'default' => 'disable'
					),					
					'maintenance-page' => array(
						'title' => esc_html__('Select Maintenance / Coming Soon Page', 'infinite'),
						'type' => 'combobox',
						'options' => 'post_type',
						'options-data' => 'page'
					),

				) // maintenance-options
			), // maintenance
			'pre-load' => array(
				'title' => esc_html__('Preload', 'infinite'),
				'options' => array(		
					'enable-preload' => array(
						'title' => esc_html__('Enable Preload', 'infinite'),
						'type' => 'checkbox',
						'default' => 'disable'
					),
					'preload-image' => array(
						'title' => esc_html__('Preload Image', 'infinite'),
						'type' => 'upload',
						'data-type' => 'file', 
						'selector' => '.infinite-page-preload{ background-image: url(#gdlr#); }',
						'condition' => array( 'enable-preload' => 'enable' ),
						'description' => esc_html__('Upload the image (.gif) you want to use as preload animation. You could search it online at https://www.google.com/search?q=loading+gif as well', 'infinite')
					),
				)
			),
			'import-export' => array(
				'title' => esc_html__('Import / Export', 'infinite'),
				'options' => array(

					'export' => array(
						'title' => esc_html__('Export Option', 'infinite'),
						'type' => 'export',
						'action' => 'gdlr_core_theme_option_export',
						'options' => array(
							'all' => esc_html__('All Options(general/typography/color/miscellaneous) exclude widget, custom template', 'infinite'),
							INFINITE_SHORT_NAME . '_general' => esc_html__('General Option', 'infinite'),
							INFINITE_SHORT_NAME . '_typography' => esc_html__('Typography Option', 'infinite'),
							INFINITE_SHORT_NAME . '_color' => esc_html__('Color Option', 'infinite'),
							INFINITE_SHORT_NAME . '_plugin' => esc_html__('Miscellaneous', 'infinite'),
							'widget' => esc_html__('Widget', 'infinite'),
							'page-builder-template' => esc_html__('Custom Page Builder Template', 'infinite'),
						),
						'wrapper-class' => 'gdlr-core-fullsize'
					),
					'import' => array(
						'title' => esc_html__('Import Option', 'infinite'),
						'type' => 'import',
						'action' => 'gdlr_core_theme_option_import',
						'wrapper-class' => 'gdlr-core-fullsize'
					),

				) // import-options
			), // import-export
			
		
		) // plugin-options
		
	), 8);	