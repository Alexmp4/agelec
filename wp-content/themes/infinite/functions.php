<?php
	/*	
	*	Goodlayers Function File
	*	---------------------------------------------------------------------
	*	This file include all of important function and features of the theme
	*	---------------------------------------------------------------------
	*/

	// goodlayers core plugin function
	include_once(get_template_directory() . '/admin/core/sidebar-generator.php');
	include_once(get_template_directory() . '/admin/core/utility.php');
	include_once(get_template_directory() . '/admin/core/media.php' );
	
	// create admin page
	if( is_admin() ){
		include_once(get_template_directory() . '/admin/tgmpa/class-tgm-plugin-activation.php');
		include_once(get_template_directory() . '/admin/tgmpa/plugin-activation.php');
		include_once(get_template_directory() . '/admin/function/getting-start.php');	
	}
	
	// plugins
	include_once(get_template_directory() . '/plugins/wpml.php');
	include_once(get_template_directory() . '/plugins/revslider.php');
	
	/////////////////////
	// front end script
	/////////////////////
	
	include_once(get_template_directory() . '/include/utility.php' );
	include_once(get_template_directory() . '/include/function-regist.php' );
	include_once(get_template_directory() . '/include/navigation-menu.php' );
	include_once(get_template_directory() . '/include/include-script.php' );
	include_once(get_template_directory() . '/include/goodlayers-core-filter.php' );
	include_once(get_template_directory() . '/include/maintenance.php' );
	include_once(get_template_directory() . '/woocommerce/woocommerce-settings.php' );
	
	/////////////////////
	// execute module 
	/////////////////////
	
	// initiate sidebar structure
	$sidebar_atts = array(
		'before_widget' => '<div id="%1$s" class="widget %2$s infinite-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="infinite-widget-title">',
		'after_title'   => '</h3><span class="clear"></span>' );

	if( infinite_get_option('general', 'sidebar-title-divider', 'enable') == 'enable' ){
		$sidebar_atts['before_title'] = '<h3 class="infinite-widget-title"><span class="infinite-widget-head-text">';
		$sidebar_atts['after_title'] = '</span><span class="infinite-widget-head-divider"></span></h3><span class="clear"></span>';
	}
	new gdlr_core_sidebar_generator($sidebar_atts);

	// clear data for wpml translation
	add_action('init', 'infinite_clear_general_option');
	if( !function_exists('infinite_clear_general_option') ){
		function infinite_clear_general_option(){
			unset($GLOBALS[INFINITE_SHORT_NAME . '_general']);
		}	
	}	

	// remove the core default action to enqueue the theme script
	remove_action('after_setup_theme', 'gdlr_init_goodlayers_core_elements');
	add_action('after_setup_theme', 'infinite_init_goodlayers_core_elements');
	if( !function_exists('infinite_init_goodlayers_core_elements') ){
		function infinite_init_goodlayers_core_elements(){

			// create an admin option and customizer
			if( (is_admin() || is_customize_preview()) && class_exists('gdlr_core_admin_option') && class_exists('gdlr_core_theme_customizer') ){
				
				$infinite_admin_option = new gdlr_core_admin_option(array(
					'filewrite' => infinite_get_style_custom(true)
				));	
				
				include_once( get_template_directory() . '/include/options/general.php');
				include_once( get_template_directory() . '/include/options/typography.php');
				include_once( get_template_directory() . '/include/options/color.php');
				include_once( get_template_directory() . '/include/options/plugin-settings.php');

				if( is_customize_preview() ){
					new gdlr_core_theme_customizer($infinite_admin_option);
				}

				// clear an option for customize page
				add_action('wp', 'infinite_clear_option');
				
			}
			
			// add the script for page builder / page options / post option
			if( is_admin() ){
				
				if( class_exists('gdlr_core_revision') ){
					$revision_num = 5;
					new gdlr_core_revision($revision_num);
				}
				
				// create page option
				if( class_exists('gdlr_core_page_option') ){

					// for page post type
					new gdlr_core_page_option(array(
						'post_type' => array('page'),
						'options' => array(
							'layout' => array(
								'title' => esc_html__('Layout', 'infinite'),
								'options' => array(
									'enable-header-area' => array(
										'title' => esc_html__('Enable Header Area', 'infinite'),
										'type' => 'checkbox',
										'default' => 'enable'
									),
									'sticky-navigation' => array(
										'title' => esc_html__('Sticky Navigation', 'infinite'),
										'type' => 'combobox',
										'options' => array(
											'default' => esc_html__('Default', 'infinite'),
											'enable' => esc_html__('Enable', 'infinite'),
											'disable' => esc_html__('Disable', 'infinite'),
										),
										'condition' => array( 'enable-header-area' => 'enable' )
									),
									'enable-page-title' => array(
										'title' => esc_html__('Enable Page Title', 'infinite'),
										'type' => 'checkbox',
										'default' => 'enable',
										'condition' => array( 'enable-header-area' => 'enable' )
									),
									'page-caption' => array(
										'title' => esc_html__('Caption', 'infinite'),
										'type' => 'textarea',
										'condition' => array( 'enable-header-area' => 'enable', 'enable-page-title' => 'enable' )
									),					
									'title-background' => array(
										'title' => esc_html__('Page Title Background', 'infinite'),
										'type' => 'upload',
										'condition' => array( 'enable-header-area' => 'enable', 'enable-page-title' => 'enable' )
									),
									'body-background-type' => array(
										'title' => esc_html__('Body Background Type', 'infinite'),
										'type' => 'combobox',
										'options' => array(
											'default' => esc_html__('Default', 'infinite'),
											'image' => esc_html__('Image ( Only For Boxed Layout )', 'infinite'),
										)
									),
									'body-background-image' => array(
										'title' => esc_html__('Body Background Image', 'infinite'),
										'type' => 'upload',
										'data-type' => 'file', 
										'condition' => array( 'body-background-type' => 'image' )
									),
									'body-background-image-opacity' => array(
										'title' => esc_html__('Body Background Image Opacity', 'infinite'),
										'type' => 'fontslider',
										'data-type' => 'opacity',
										'default' => '100',
										'condition' => array( 'body-background-type' => 'image' )
									),
									'show-content' => array(
										'title' => esc_html__('Show WordPress Editor Content', 'infinite'),
										'type' => 'checkbox',
										'default' => 'enable',
										'description' => esc_html__('Disable this to hide the content in editor to show only page builder content.', 'infinite'),
									),
									'sidebar' => array(
										'title' => esc_html__('Sidebar', 'infinite'),
										'type' => 'radioimage',
										'options' => 'sidebar',
										'default' => 'none',
										'wrapper-class' => 'gdlr-core-fullsize'
									),
									'sidebar-left' => array(
										'title' => esc_html__('Sidebar Left', 'infinite'),
										'type' => 'combobox',
										'options' => 'sidebar',
										'condition' => array( 'sidebar' => array('left', 'both') )
									),
									'sidebar-right' => array(
										'title' => esc_html__('Sidebar Right', 'infinite'),
										'type' => 'combobox',
										'options' => 'sidebar',
										'condition' => array( 'sidebar' => array('right', 'both') )
									),
									'enable-footer' => array(
										'title' => esc_html__('Enable Footer', 'infinite'),
										'type' => 'combobox',
										'options' => array(
											'default' => esc_html__('Default', 'infinite'),
											'enable' => esc_html__('Enable', 'infinite'),
											'disable' => esc_html__('Disable', 'infinite'),
										)
									),
									'enable-copyright' => array(
										'title' => esc_html__('Enable Copyright', 'infinite'),
										'type' => 'combobox',
										'options' => array(
											'default' => esc_html__('Default', 'infinite'),
											'enable' => esc_html__('Enable', 'infinite'),
											'disable' => esc_html__('Disable', 'infinite'),
										)
									),

								)
							), // layout
							'title' => array(
								'title' => esc_html__('Title Style', 'infinite'),
								'options' => array(

									'title-style' => array(
										'title' => esc_html__('Page Title Style', 'infinite'),
										'type' => 'combobox',
										'options' => array(
											'default' => esc_html__('Default', 'infinite'),
											'small' => esc_html__('Small', 'infinite'),
											'medium' => esc_html__('Medium', 'infinite'),
											'large' => esc_html__('Large', 'infinite'),
											'custom' => esc_html__('Custom', 'infinite'),
										),
										'default' => 'default'
									),
									'title-align' => array(
										'title' => esc_html__('Page Title Alignment', 'infinite'),
										'type' => 'radioimage',
										'options' => 'text-align',
										'with-default' => true,
										'default' => 'default'
									),
									'title-spacing' => array(
										'title' => esc_html__('Page Title Padding', 'infinite'),
										'type' => 'custom',
										'item-type' => 'padding',
										'data-input-type' => 'pixel',
										'options' => array('padding-top', 'padding-bottom', 'caption-top-margin'),
										'wrapper-class' => 'gdlr-core-fullsize gdlr-core-no-link gdlr-core-large',
										'condition' => array( 'title-style' => 'custom' )
									),
									'title-font-size' => array(
										'title' => esc_html__('Page Title Font Size', 'infinite'),
										'type' => 'custom',
										'item-type' => 'padding',
										'data-input-type' => 'pixel',
										'options' => array('title-size', 'title-letter-spacing', 'caption-size', 'caption-letter-spacing'),
										'wrapper-class' => 'gdlr-core-fullsize gdlr-core-no-link gdlr-core-large',
										'condition' => array( 'title-style' => 'custom' )
									),
									'title-font-weight' => array(
										'title' => esc_html__('Page Title Font Weight', 'infinite'),
										'type' => 'custom',
										'item-type' => 'padding',
										'options' => array('title-weight', 'caption-weight'),
										'wrapper-class' => 'gdlr-core-fullsize gdlr-core-no-link gdlr-core-large',
										'condition' => array( 'title-style' => 'custom' )
									),
									'title-font-transform' => array(
										'title' => esc_html__('Page Title Font Transform', 'infinite'),
										'type' => 'combobox',
										'options' => array(
											'none' => esc_html__('None', 'infinite'),
											'uppercase' => esc_html__('Uppercase', 'infinite'),
											'lowercase' => esc_html__('Lowercase', 'infinite'),
											'capitalize' => esc_html__('Capitalize', 'infinite'),
										),
										'default' => 'uppercase',
										'condition' => array( 'title-style' => 'custom' )
									),
									'title-background-overlay-opacity' => array(
										'title' => esc_html__('Page Title Background Overlay Opacity', 'infinite'),
										'type' => 'text',
										'description' => esc_html__('Fill the number between 0.01 - 1 ( Leave Blank For Default Value )', 'infinite'),
										'condition' => array( 'title-style' => 'custom' )
									),
									'title-color' => array(
										'title' => esc_html__('Page Title Color', 'infinite'),
										'type' => 'colorpicker',
									),
									'caption-color' => array(
										'title' => esc_html__('Page Caption Color', 'infinite'),
										'type' => 'colorpicker',
									),
									'title-background-overlay-color' => array(
										'title' => esc_html__('Page Background Overlay Color', 'infinite'),
										'type' => 'colorpicker',
									),

								)
							), // title
							'header' => array(
								'title' => esc_html__('Header', 'infinite'),
								'options' => array(

									'header-slider' => array(
										'title' => esc_html__('Header Slider ( Above Navigation )', 'infinite'),
										'type' => 'combobox',
										'options' => array(
											'none' => esc_html__('None', 'infinite'),
											'layer-slider' => esc_html__('Layer Slider', 'infinite'),
											'master-slider' => esc_html__('Master Slider', 'infinite'),
											'revolution-slider' => esc_html__('Revolution Slider', 'infinite'),
										),
										'description' => esc_html__('For header style plain / bar / boxed', 'infinite'),
									),
									'layer-slider-id' => array(
										'title' => esc_html__('Choose Layer Slider', 'infinite'),
										'type' => 'combobox',
										'options' => gdlr_core_get_layerslider_list(),
										'condition' => array( 'header-slider' => 'layer-slider' )
									),
									'master-slider-id' => array(
										'title' => esc_html__('Choose Master Slider', 'infinite'),
										'type' => 'combobox',
										'options' => gdlr_core_get_masterslider_list(),
										'condition' => array( 'header-slider' => 'master-slider' )
									),
									'revolution-slider-id' => array(
										'title' => esc_html__('Choose Revolution Slider', 'infinite'),
										'type' => 'combobox',
										'options' => gdlr_core_get_revolution_slider_list(),
										'condition' => array( 'header-slider' => 'revolution-slider' )
									),

								) // header options
							), // header
							'bullet-anchor' => array(
								'title' => esc_html__('Bullet Anchor', 'infinite'),
								'options' => array(
									'bullet-anchor-description' => array(
										'type' => 'description',
										'description' => esc_html__('This feature is used for one page navigation. It will appear on the right side of page. You can put the id of element in \'Anchor Link\' field to let the bullet scroll the page to.', 'infinite')
									),
									'bullet-anchor' => array(
										'title' => esc_html__('Add Anchor', 'infinite'),
										'type' => 'custom',
										'item-type' => 'tabs',
										'options' => array(
											'title' => array(
												'title' => esc_html__('Anchor Link', 'infinite'),
												'type' => 'text',
											),
											'anchor-color' => array(
												'title' => esc_html__('Anchor Color', 'infinite'),
												'type' => 'colorpicker',
											),
											'anchor-hover-color' => array(
												'title' => esc_html__('Anchor Hover Color', 'infinite'),
												'type' => 'colorpicker',
											)
										),
										'wrapper-class' => 'gdlr-core-fullsize'
									),
								)
							)
						)
					));

					// for post post type
					new gdlr_core_page_option(array(
						'post_type' => array('post'),
						'options' => array(
							'layout' => array(
								'title' => esc_html__('Layout', 'infinite'),
								'options' => array(

									'show-content' => array(
										'title' => esc_html__('Show WordPress Editor Content', 'infinite'),
										'type' => 'checkbox',
										'default' => 'enable',
										'description' => esc_html__('Disable this to hide the content in editor to show only page builder content.', 'infinite'),
									),
									'sidebar' => array(
										'title' => esc_html__('Sidebar', 'infinite'),
										'type' => 'radioimage',
										'options' => 'sidebar',
										'with-default' => true,
										'default' => 'default',
										'wrapper-class' => 'gdlr-core-fullsize'
									),
									'sidebar-left' => array(
										'title' => esc_html__('Sidebar Left', 'infinite'),
										'type' => 'combobox',
										'options' => 'sidebar',
										'condition' => array( 'sidebar' => array('left', 'both') )
									),
									'sidebar-right' => array(
										'title' => esc_html__('Sidebar Right', 'infinite'),
										'type' => 'combobox',
										'options' => 'sidebar',
										'condition' => array( 'sidebar' => array('right', 'both') )
									),
								)
							),
							'metro-layout' => array(
								'title' => esc_html__('Metro Layout', 'infinite'),
								'options' => array(
									'metro-column-size' => array(
										'title' => esc_html__('Column Size', 'infinite'),
										'type' => 'combobox',
										'options' => array( 'default'=> esc_html__('Default', 'infinite'), 
											60 => '1/1', 30 => '1/2', 20 => '1/3', 40 => '2/3', 
											15 => '1/4', 45 => '3/4', 12 => '1/5', 24 => '2/5', 36 => '3/5', 48 => '4/5',
											10 => '1/6', 50 => '5/6'),
										'default' => 'default',
										'description' => esc_html__('Choosing default will display the value selected by the page builder item.', 'infinite')
									),
									'metro-thumbnail-size' => array(
										'title' => esc_html__('Thumbnail Size', 'infinite'),
										'type' => 'combobox',
										'options' => 'thumbnail-size',
										'with-default' => true,
										'default' => 'default',
										'description' => esc_html__('Choosing default will display the value selected by the page builder item.', 'infinite')
									)
								)
							),						
							'title' => array(
								'title' => esc_html__('Title', 'infinite'),
								'options' => array(

									'blog-title-style' => array(
										'title' => esc_html__('Blog Title Style', 'infinite'),
										'type' => 'combobox',
										'options' => array(
											'default' => esc_html__('Default', 'infinite'),
											'small' => esc_html__('Small', 'infinite'),
											'large' => esc_html__('Large', 'infinite'),
											'custom' => esc_html__('Custom', 'infinite'),
											'inside-content' => esc_html__('Inside Content', 'infinite'),
											'none' => esc_html__('None', 'infinite'),
										),
										'default' => 'default'
									),
									'blog-title-padding' => array(
										'title' => esc_html__('Blog Title Padding', 'infinite'),
										'type' => 'custom',
										'item-type' => 'padding',
										'data-input-type' => 'pixel',
										'options' => array('padding-top', 'padding-bottom'),
										'wrapper-class' => 'gdlr-core-fullsize gdlr-core-no-link gdlr-core-large',
										'condition' => array( 'blog-title-style' => 'custom' )
									),
									'blog-feature-image' => array(
										'title' => esc_html__('Blog Feature Image Location', 'infinite'),
										'type' => 'combobox',
										'options' => array(
											'default' => esc_html__('Default', 'infinite'),
											'content' => esc_html__('Inside Content', 'infinite'),
											'title-background' => esc_html__('Title Background', 'infinite'),
											'none' => esc_html__('None', 'infinite'),
										)
									),
									'blog-title-background-image' => array(
										'title' => esc_html__('Blog Title Background Image', 'infinite'),
										'type' => 'upload',
										'data-type' => 'file',
										'condition' => array( 
											'blog-title-style' => array('default', 'small', 'large', 'custom'),
											'blog-feature-image' => array('default', 'content', 'none')
										),
										'description' => esc_html__('Will be overridden by feature image if selected.', 'infinite'),
									),
									'blog-top-bottom-gradient' => array(
										'title' => esc_html__('Blog ( Feature Image ) Title Top/Bottom Gradient', 'infinite'),
										'type' => 'combobox',
										'options' => array(
											'default' => esc_html__('Default', 'infinite'),
											'enable' => esc_html__('Enable', 'infinite'),
											'disable' => esc_html__('Disable', 'infinite'),
										)
									),
									'blog-title-background-overlay-opacity' => array(
										'title' => esc_html__('Blog Title Background Overlay Opacity', 'infinite'),
										'type' => 'text',
										'description' => esc_html__('Fill the number between 0.01 - 1 ( Leave Blank For Default Value )', 'infinite'),
									),

								) // options
							) // title
						)
					));
				}

			}
			
			// create page builder
			if( class_exists('gdlr_core_page_builder') ){
				new gdlr_core_page_builder(array(
					'style' => array(
						'style-custom' => infinite_get_style_custom()
					)
				));
			}
			
		} // infinite_init_goodlayers_core_elements
	} // function_exists
