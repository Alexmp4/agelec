<?php
/*
Plugin Name: WP Google Map Plugin
Plugin URI: http://www.flippercode.com/
Description: A Responsive Google Maps plugin to display custom markers on the google maps. Show custom message with links on a marker click.
Author: flippercode
Author URI: http://www.flippercode.com/
Version: 4.0.4
Text Domain: wp-google-map-plugin
Domain Path: /lang/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( ! class_exists( 'FC_Google_Maps_Lite' ) ) {

	/**
	 * Main plugin class
	 * @author Flipper Code <hello@flippercode.com>
	 * @package Maps
	 */
	class FC_Google_Maps_Lite
	{
		/**
		 * List of Modules.
		 * @var array
		 */
		private $modules = array();
		/**
		 * Intialize variables, files and call actions.
		 * @var array
		 */
		public function __construct() {
			error_reporting( E_ERROR | E_PARSE );
			$this->_define_constants();
			$this->_load_files();
			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
			if( is_multisite() ){
			  add_action( 'wpmu_new_blog', array( $this, 'wpgmp_on_blog_new_generate'), 10, 6 );
              add_filter( 'wpmu_drop_tables', array( $this, 'wpgmp_on_blog_delete') );
			}
			add_action( 'plugins_loaded', array( $this, 'load_plugin_languages' ) );
			add_action( 'init', array( $this, '_init' ) );
			add_action( 'widgets_init', array( $this, 'wpgmp_google_map_widget' ) );

		}
		/**
		 * Call WordPress hooks.
		 */
		function _init() {

			global $wpdb;

			// Actions.
			add_action( 'admin_menu', array( $this, 'create_menu' ) );
			add_action( 'media_upload_ell_insert_gmap_tab', array( $this, 'wpgmp_google_map_media_upload_tab' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wpgmp_frontend_scripts' ) );
			add_action( 'admin_print_scripts', array( $this, 'wpgmp_backend_styles' ) );

			// Filters.
			add_filter( 'media_upload_tabs', array( $this, 'wpgmp_google_map_tabs_filter' ) );

			// Shortodes.
			add_shortcode( 'put_wpgm', array( $this, 'wpgmp_show_location_in_map' ) );
			add_shortcode( 'display_map', array( $this, 'wpgmp_display_map' ) );

		}
		
		/**
		 * Register WP Google Map Widget
		 */
		function wpgmp_google_map_widget() {

			register_widget( 'WPGMP_Google_Map_Widget_Class' );
		}
		
		
		
		/**
		 * Eneque scripts at frontend.
		 */
		function wpgmp_frontend_scripts() {

			$scripts = array();
			wp_enqueue_script( 'jquery' );
			if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
				$wpgmp_apilocation = 'https';
			} else {
				$wpgmp_apilocation = 'http';
			}
			$language = get_option( 'wpgmp_language' );

			if ( $language == '' ) {
				$language = 'en';
			}

			if ( get_option( 'wpgmp_api_key' ) != '' ) {
				$wpgmp_apilocation .= '://maps.google.com/maps/api/js?key='.get_option( 'wpgmp_api_key' ).'&libraries=geometry,places,weather,panoramio,drawing&language='.$language;
			} else {
				$wpgmp_apilocation .= '://maps.google.com/maps/api/js?libraries=geometry,places,weather,panoramio,drawing&language='.$language;
			}

			$scripts[] = array(
			'handle'  => 'wpgmp-google-api',
			'src'   => $wpgmp_apilocation,
			'deps'    => array(),
			);

			$scripts[] = array(
			'handle'  => 'wpgmp-frontend',
			'src'   => WPGMP_JS.'frontend.js',
			'deps'    => array(),
			);

			$where = get_option( 'wpgmp_scripts_place' );

			if ( $where == 'header' ) {
				$where = false;
			} else {
				$where = true;
			}

			if ( $scripts ) {
				foreach ( $scripts as $script ) {
					wp_register_script( $script['handle'], $script['src'], $script['deps'], '', $where );
				}
			}
			
			$wpgmp_local = array();
			$wpgmp_local['all_location'] = __( 'All', WPGMP_TEXT_DOMAIN );
			$wpgmp_local['show_locations'] = __( 'Show Locations', WPGMP_TEXT_DOMAIN );
			$wpgmp_local['sort_by'] = __( 'Sort by', WPGMP_TEXT_DOMAIN );
			$wpgmp_local['wpgmp_not_working'] = __( 'not working...', WPGMP_TEXT_DOMAIN );
			$wpgmp_local['place_icon_url'] = WPGMP_ICONS;

			$scripts = array();

			$scripts[] = array(
			'handle'  => 'wpgmp-google-map-main',
			'src'   => WPGMP_JS.'maps.js',
			'deps'    => array( 'wpgmp-google-api' ),
			);

			if ( $scripts ) {
				foreach ( $scripts as $script ) {
					wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], '2.3.4', $where );
				}
			}

			wp_localize_script( 'wpgmp-google-map-main', 'wpgmp_local',$wpgmp_local );

			$frontend_styles = array(
			'wpgmp-frontend'  => WPGMP_CSS.'frontend.css',
			);

			if ( $frontend_styles ) {
				foreach ( $frontend_styles as $frontend_style_key => $frontend_style_value ) {
					wp_register_style( $frontend_style_key, $frontend_style_value );
				}
			}

		}
		/**
		 * Display map at the frontend using put_wpgmp shortcode.
		 * @param  array  $atts   Map Options.
		 * @param  string $content Content.
		 */
		function wpgmp_show_location_in_map($atts, $content = null) {
			error_reporting( E_ERROR | E_PARSE );

			try {
				$factoryObject = new WPGMP_Controller();
				$viewObject = $factoryObject->create_object( 'shortcode' );
				$output = $viewObject->display( 'put-wpgmp',$atts );
				 return $output;

			} catch (Exception $e) {
				echo WPGMP_Template::show_message( array( 'error' => $e->getMessage() ) );

			}

		}
		/**
		 * Display map at the frontend using display_map shortcode.
		 * @param  array $atts    Map Options.
		 */
		function wpgmp_display_map($atts) {

			try {
				$factoryObject = new WPGMP_Controller();
				$viewObject = $factoryObject->create_object( 'shortcode' );
				 $output = $viewObject->display( 'display-map',$atts );
				 return $output;

			} catch (Exception $e) {
				echo WPGMP_Template::show_message( array( 'error' => $e->getMessage() ) );

			}

		}
		/**
		 * Process slug and display view in the backend.
		 */
		function processor() {
			error_reporting( E_ERROR | E_PARSE );

			$return = '';
			if ( isset( $_GET['page'] ) ) {
				$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			} else {
				$page = 'wpgmp_view_overview';
			}

			$pageData = explode( '_', $page );
			$obj_type = $pageData[2];
			$obj_operation = $pageData[1];

			if ( count( $pageData ) < 3 ) {
				die( 'Cheating!' );
			}

			try {
				if ( count( $pageData ) > 3 ) {
					$obj_type = $pageData[2].'_'.$pageData[3];
				}

				$factoryObject = new WPGMP_Controller();
				$viewObject = $factoryObject->create_object( $obj_type );
				$viewObject->display( $obj_operation );

			} catch (Exception $e) {
				echo WPGMP_Template::show_message( array( 'error' => $e->getMessage() ) );

			}

		}
		/**
		 * Create backend navigation.
		 */
		function create_menu() {

			global $navigations;

			$pagehook1 = add_menu_page(
				__( 'Google Maps', WPGMP_TEXT_DOMAIN ),
				__( 'Google Maps', WPGMP_TEXT_DOMAIN ),
				'wpgmp_admin_overview',
				WPGMP_SLUG,
				array( $this,'processor' ),
				WPGMP_IMAGES.'/flippercode.png'
			);

			if ( current_user_can( 'manage_options' )  ) {
								$role = get_role( 'administrator' );
								$role->add_cap( 'wpgmp_admin_overview' );
			}

			$this->load_modules_menu();

			add_action( 'load-'.$pagehook1, array( $this, 'wpgmp_backend_scripts' ) );

		}
		/**
		 * Read models and create backend navigation.
		 */
		function load_modules_menu() {

			$modules = $this->modules;
			$pagehooks = array();
			if ( is_array( $modules ) ) {
				foreach ( $modules as $module ) {

						$object = new $module;

					if ( method_exists( $object,'navigation' ) ) {

						if ( ! is_array( $object->navigation() ) ) {
							continue;
						}

						foreach ( $object->navigation() as $nav => $title ) {

							if ( current_user_can( 'manage_options' ) && is_admin() ) {
								$role = get_role( 'administrator' );
								$role->add_cap( $nav );

							}

							$pagehooks[] = add_submenu_page(
								WPGMP_SLUG,
								$title,
								$title,
								$nav,
								$nav,
								array( $this,'processor' )
							);

						}
					}
				}
			}

			if ( is_array( $pagehooks ) ) {

				foreach ( $pagehooks as $key => $pagehook ) {
					add_action( 'load-'.$pagehooks[ $key ], array( $this, 'wpgmp_backend_scripts' ) );
				}
			}

		}
		/**
		 * Eneque scripts in the backend.
		 */
		function wpgmp_backend_scripts() {

			global $pagehook3, $pagehook6, $pagehook9, $pagehook11;

			if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
					$wpgmp_apilocation = 'https';
			} else {
				$wpgmp_apilocation = 'http';
			}

			if ( get_option( 'wpgmp_api_key' ) != '' ) {
				$wpgmp_apilocation .= '://maps.google.com/maps/api/js?key='.get_option( 'wpgmp_api_key' ).'&libraries=geometry,places,weather,panoramio,drawing&language=en';
			} else {
				$wpgmp_apilocation .= '://maps.google.com/maps/api/js?libraries=geometry,places,weather,panoramio,drawing&language=en';
			}

			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'wp-color-picker' );
			$wp_scripts = array( 'jQuery','thickbox', 'wp-color-picker','jquery-ui-datepicker' );

			if ( $wp_scripts ) {
				foreach ( $wp_scripts as $wp_script ) {
					wp_enqueue_script( $wp_script );
				}
			}

			$scripts = array();

			$scripts[] = array(
			'handle'  => 'wpgmp-backend-google-maps',
			'src'   => WPGMP_JS.'backend.js',
			'deps'    => array(),
			);

			$scripts[] = array(
			'handle'  => 'wpgmp-backend-google-api',
			'src'   => $wpgmp_apilocation,
			'deps'    => array(),
			);

			$scripts[] = array(
			'handle'  => 'wpgmp-map',
			'src'   => WPGMP_JS.'maps.js',
			'deps'    => array(),
			);

			$scripts[] = array(
			'handle'  => 'flippercode-ui',
			'src'   => WPGMP_JS.'flippercode-ui.js',
			'deps'    => array(),
			);

			if ( $scripts ) {
				foreach ( $scripts as $script ) {
					wp_enqueue_script( $script['handle'], $script['src'], $script['deps'],'2.3.4' );
				}
			}

			$wpgmp_local = array();
			$wpgmp_local['language'] = 'en';
			$wpgmp_local['urlforajax'] = admin_url( 'admin-ajax.php' );
			$wpgmp_local['hide'] = __( 'Hide',WPGMP_TEXT_DOMAIN );
			$wpgmp_local['nonce'] = wp_create_nonce('fc_communication');
			wp_localize_script( 'wpgmp-map', 'wpgmp_local', $wpgmp_local );
			wp_localize_script( 'flippercode-ui', 'wpgmp_local', $wpgmp_local );

			$wpgmp_js_lang = array();
			$wpgmp_js_lang['confirm'] = __( 'Are you sure to delete item?',WPGMP_TEXT_DOMAIN );
			wp_localize_script( 'wpgmp-backend-google-maps', 'wpgmp_js_lang', $wpgmp_js_lang );
			$admin_styles = array(
			'font_awesome_minimised' => WPGMP_CSS. 'font-awesome.min.css',
			'wpgmp-map-bootstrap' => WPGMP_CSS.'flippercode-ui.css',
			'wpgmp-backend-google-map' => WPGMP_CSS.'backend.css',
			);				

			if ( $admin_styles ) {
				foreach ( $admin_styles as $admin_style_key => $admin_style_value ) {
					wp_enqueue_style( $admin_style_key, $admin_style_value );
				}
			}
		}
		/**
		 * Metabox stylesheet.
		 */
		function wpgmp_backend_styles() {

			wp_enqueue_style( 'wpgmp-backend-metabox', WPGMP_CSS.'wpgmp-metabox-css.css' );
		}
		/**
		 * Load plugin language file.
		 */
		function load_plugin_languages() {

			$this->modules = apply_filters( 'wpgmp_extensions',$this->modules);
			load_plugin_textdomain( WPGMP_TEXT_DOMAIN, false, WPGMP_FOLDER.'/lang/' );
		}
		/**
		 * Call hook on plugin activation for both multi-site and single-site.
		 */
		function plugin_activation( $network_wide ) {

			if ( is_multisite() && $network_wide ) {
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated = array();
				$sql = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids = $wpdb->get_col( $wpdb->prepare( $sql, null ) );

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->wpgmp_activation();
					$activated[] = $blog_id;
				}

				switch_to_blog( $currentblog );
				update_site_option( 'op_activated', $activated );

			} else {
				$this->wpgmp_activation();
			}
		}
		/**
		 * Call hook on plugin deactivation for both multi-site and single-site.
		 */
		function plugin_deactivation() {

			if ( is_multisite() && $network_wide ) {
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated = array();
				$sql = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids = $wpdb->get_col( $wpdb->prepare( $sql, null ) );

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->wpgmp_deactivation();
					$activated[] = $blog_id;
				}

				switch_to_blog( $currentblog );
				update_site_option( 'op_activated', $activated );

			} else {
				$this->wpgmp_deactivation();
			}
		}

		/**
		 * Perform tasks on new blog create and table install.
		 */
		 
		 function wpgmp_on_blog_new_generate(  $blog_id, $user_id, $domain, $path, $site_id, $meta ){
		    
			if ( is_plugin_active_for_network( plugin_basename(__FILE__) ) ) {
               switch_to_blog( $blog_id );
               $this->wpgmp_activation();
               restore_current_blog();
             }	 
		 
		 }

		/**
		 * Perform tasks on when blog deleted and remove plugin tables.
		 */
		 
		 function wpgmp_on_blog_delete( $tables ){
			global $wpdb;
            $tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_LOCATION );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_GROUPMAP );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_MAP );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_ROUTES );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_BACKUPS );
            return $tables; 
		 }
		/**
		 * Create choose icon tab in media manager.
		 * @param  array $tabs Current Tabs.
		 * @return array       New Tabs.
		 */
		function wpgmp_google_map_tabs_filter($tabs) {

			$newtab = array( 'ell_insert_gmap_tab' => __( 'Choose Icons', WPGMP_TEXT_DOMAIN ) );
			return array_merge( $tabs, $newtab );
		}
		/**
		 * Intialize wp_iframe for icons tab
		 * @return [type] [description]
		 */
		function wpgmp_google_map_media_upload_tab() {

			return wp_iframe( array( $this, 'media_wpgmp_google_map_icon' ), $errors );
		}
		/**
		 * Read images/icons folder.
		 */
		function media_wpgmp_google_map_icon() {

			wp_enqueue_style( 'media' );
			media_upload_header();
			$form_action_url = site_url( "wp-admin/media-upload.php?type={$GLOBALS['type']}&tab=ell_insert_gmap_tab", 'admin' );
		?>

		<style type="text/css">
		#select_icons .read_icons {
		width: 32px;
		height: 32px;ß
		}
		#select_icons .active img {
		border: 3px solid #000;
		width: 26px;
		}
		</style>

		<script type="text/javascript">

		jQuery(document).ready(function($) {

		$(".read_icons").click(function () {

		$(".read_icons").removeClass('active');
		$(this).addClass('active');
		});

		$('input[name="wpgmp_search_icon"]').keyup(function() {
		if($(this).val() == '')
		$('.read_icons').show();
		else {
		$('.read_icons').hide();
        $('img[title^="' + $(this).val() + '"]').parent().show();
		}

    	});

		});

		function add_icon_to_images(target) {

		if(jQuery('.read_icons').hasClass('active'))
		{
		imgsrc = jQuery('.active').find('img').attr('src');
		var win = window.dialogArguments || opener || parent || top;
		win.send_icon_to_map(imgsrc,target);
		}
		else
		{
		alert('<?php _e( 'Choose marker icon',WPGMP_TEXT_DOMAIN ); ?>');
		}
		}
		</script>

		<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr( $form_action_url ); ?>" class="media-upload-form" id="library-form">
	<h3 class="media-title" style="color: #5A5A5A; font-family: Georgia, 'Times New Roman', Times, serif; font-weight: normal; font-size: 1.6em; margin-left: 10px;"><?php _e( 'Choose icon', WPGMP_TEXT_DOMAIN ) ?> 	<input name="wpgmp_search_icon" id="wpgmp_search_icon" type='text' value="" placeholder="<?php _e( 'Search icons',WPGMP_TEXT_DOMAIN ); ?>" />
</h3>
	<div style="margin-bottom:20px; float:left; width:100%;">
	<ul style="float:left; width:100%;" id="select_icons">
	<?php
	$dir = WPGMP_ICONS_DIR;
	$file_display = array( 'jpg', 'jpeg', 'png', 'gif' );

	if ( file_exists( $dir ) == false ) {
		echo 'Directory \'', $dir, '\' not found!';

	} else {
		$dir_contents = scandir( $dir );
		foreach ( $dir_contents as $file ) {
			$image_data = explode( '.', $file );
			$file_type = strtolower( end( $image_data ) );
			if ( '.' !== $file && '..' !== $file && true == in_array( $file_type, $file_display ) ) {
			?>
			<li class="read_icons" style="float:left;">
			<img alt="<?php echo $image_data[0]; ?>" title="<?php echo $image_data[0]; ?>" src="<?php echo WPGMP_ICONS.$file; ?>" style="cursor:pointer;" />
		</li>
		<?php
			}
		}
	}
		$target = esc_js($_GET['target']);
		?>
		</ul>
		<button type="button" class="button" style="margin-left:10px;" value="1" onclick="add_icon_to_images('<?php echo $target; ?>');" name="send[<?php echo $picid ?>]"><?php _e( 'Insert into Post', WPGMP_TEXT_DOMAIN ) ?></button>
	</div>
	</form>
	<?php
		}
		/**
		 * Perform tasks on plugin deactivation.
		 */
		function wpgmp_deactivation() {

		}

		/**
		 * Perform tasks on plugin deactivation.
		 */
		function wpgmp_activation() {

			global $wpdb;

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$modules = $this->modules;
			$pagehooks = array();
			if ( is_array( $modules ) ) {
				foreach ( $modules as $module ) {
					$object = new $module;
					if ( method_exists( $object,'install' ) ) {
								$tables[] = $object->install();
					}
				}
			}

			if ( is_array( $tables ) ) {
				foreach ( $tables as $i => $sql ) {
					dbDelta( $sql );
				}
			}

		}
		/**
		 * Define all constants.
		 */
		private function _define_constants() {

			global $wpdb;

			if ( ! defined( 'WPGMP_SLUG' ) ) {
				define( 'WPGMP_SLUG', 'wpgmp_view_overview' );
			}

			if ( ! defined( 'WPGMP_VERSION' ) ) {
				define( 'WPGMP_VERSION', '4.0.4' );
			}

			if ( ! defined( 'WPGMP_TEXT_DOMAIN' ) ) {
				define( 'WPGMP_TEXT_DOMAIN', 'wpgmp_google_map' );
			}

			if ( ! defined( 'WPGMP_FOLDER' ) ) {
				define( 'WPGMP_FOLDER', basename( dirname( __FILE__ ) ) );
			}

			if ( ! defined( 'WPGMP_DIR' ) ) {
				define( 'WPGMP_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'WPGMP_ICONS_DIR' ) ) {
				define( 'WPGMP_ICONS_DIR', WPGMP_DIR.'/assets/images/icons/' );
			}

			if ( ! defined( 'WPGMP_CORE_CLASSES' ) ) {
				define( 'WPGMP_CORE_CLASSES', WPGMP_DIR.'core/' );
			}
			
			if ( ! defined( 'WPGMP_PLUGIN_CLASSES' ) ) {
				define( 'WPGMP_PLUGIN_CLASSES', WPGMP_DIR . 'classes/' );
			}
			//
			if ( ! defined( 'WPGMP_MODEL' ) ) {
				define( 'WPGMP_MODEL', WPGMP_DIR . 'modules/' );
			}

			if ( ! defined( 'WPGMP_CONTROLLER' ) ) {
				define( 'WPGMP_CONTROLLER', WPGMP_CORE_CLASSES );
			}

			if ( ! defined( 'WPGMP_CORE_CONTROLLER_CLASS' ) ) {
				define( 'WPGMP_CORE_CONTROLLER_CLASS', WPGMP_CORE_CLASSES.'class.controller.php' );
			}

			if ( ! defined( 'WPGMP_MODEL' ) ) {
				define( 'WPGMP_MODEL', WPGMP_DIR.'modules/' );
			}

			if ( ! defined( 'WPGMP_URL' ) ) {
				define( 'WPGMP_URL', plugin_dir_url( WPGMP_FOLDER ).WPGMP_FOLDER.'/' );
			}

			if ( ! defined( 'FC_CORE_URL' ) ) {
				define( 'FC_CORE_URL', plugin_dir_url( WPGMP_FOLDER ).WPGMP_FOLDER.'/core/' );
			}

			if ( ! defined( 'WPGMP_INC_URL' ) ) {
				define( 'WPGMP_INC_URL', WPGMP_URL.'includes/' );
			}

			if ( ! defined( 'WPGMP_CSS' ) ) {
				define( 'WPGMP_CSS', WPGMP_URL.'assets/css/' );
			}

			if ( ! defined( 'WPGMP_JS' ) ) {
				define( 'WPGMP_JS', WPGMP_URL.'assets/js/' );
			}

			if ( ! defined( 'WPGMP_IMAGES' ) ) {
				define( 'WPGMP_IMAGES', WPGMP_URL.'assets/images/' );
			}

			if ( ! defined( 'WPGMP_FONTS' ) ) {
				define( 'WPGMP_FONTS', WPGMP_URL.'fonts/' );
			}

			

			if ( ! defined( 'WPGMP_ICONS' ) ) {
				define( 'WPGMP_ICONS', WPGMP_URL.'assets/images/icons/' );
			}
			$upload_dir = wp_upload_dir();
			if ( ! defined( 'WPGMP_BACKUP' ) ) {

				if ( ! is_dir( $upload_dir['basedir'].'/maps-backup' ) ) {
					mkdir( $upload_dir['basedir'].'/maps-backup' );
				}
				define( 'WPGMP_BACKUP',$upload_dir['basedir'].'/maps-backup/' );
				define( 'WPGMP_BACKUP_URL',$upload_dir['baseurl'].'/maps-backup/' );

			}

			if ( ! defined( 'TBL_LOCATION' ) ) {
				define( 'TBL_LOCATION', $wpdb->prefix.'map_locations' );
			}

			if ( ! defined( 'TBL_GROUPMAP' ) ) {
				define( 'TBL_GROUPMAP', $wpdb->prefix.'group_map' );
			}

			if ( ! defined( 'TBL_MAP' ) ) {
				define( 'TBL_MAP', $wpdb->prefix.'create_map' );
			}

			if ( ! defined( 'TBL_ROUTES' ) ) {
				define( 'TBL_ROUTES', $wpdb->prefix.'map_routes' );
			}

			if ( ! defined( 'TBL_BACKUPS' ) ) {
				define( 'TBL_BACKUPS', $wpdb->prefix.'wpgmp_backups' );
			}

		}
		/**
		 * Load all required core classes.
		 */
		private function _load_files() {

			
			$coreInitialisationFile = plugin_dir_path( __FILE__ ).'core/class.initiate-core.php';
			if ( file_exists( $coreInitialisationFile ) ) {
			   require_once( $coreInitialisationFile );
			}
			
			//Load Plugin Files	
			$plugin_files_to_include = array('wpgmp-template.php',
											 'wpgmp-controller.php',
											 'wpgmp-model.php',
											 'class.map-widget.php');
			foreach ( $plugin_files_to_include as $file ) {

				if(file_exists(WPGMP_PLUGIN_CLASSES . $file))
				require_once( WPGMP_PLUGIN_CLASSES . $file ); 
			}
			// Load all modules.
			$core_modules = array( 'overview','location','map','group_map','settings' );
			if ( is_array( $core_modules ) ) {
				foreach ( $core_modules as $module ) {

					$file = WPGMP_MODEL.$module.'/model.'.$module.'.php';
					
					if ( file_exists( $file ) ) {
						include_once( $file );
						$class_name = 'WPGMP_Model_'.ucwords( $module );
						array_push( $this->modules, $class_name );
					}
				}
			}

		}
	}
}

new FC_Google_Maps_Lite();
