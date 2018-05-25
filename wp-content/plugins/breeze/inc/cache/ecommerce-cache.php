<?php
defined( 'ABSPATH' ) or die('Not allow!');

/*
 * Class for E-commerce Cache
 */
class Breeze_Ecommerce_Cache {
	public function __construct() {
		add_action( 'activated_plugin', array($this,'detect_ecommerce_activation') );
		add_action( 'deactivated_plugin', array($this,'detect_ecommerce_deactivation') );
		add_action( 'wp_loaded', array($this,'update_ecommerce_activation') );
	}

	// After woocommerce active,merge array disable page config
	public function detect_ecommerce_activation($plugin){
		if( 'woocommerce/woocommerce.php' == $plugin){
			update_option('breeze_ecommerce_detect',1);
		}
	}

	// Delete option detect when deactivate woo
	public function detect_ecommerce_deactivation($plugin){
		if( 'woocommerce/woocommerce.php' == $plugin){
			delete_option('breeze_ecommerce_detect');
		}
	}

	// Update option when Woocimmerce active
	public function update_ecommerce_activation() {
		$check = get_option('breeze_ecommerce_detect');
		if( stripos($_SERVER['REQUEST_URI'],'wc-setup&step=locale') !== false){
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
			Breeze_ConfigCache::write_config_cache();
		}
		if (!empty($check)) {
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
			Breeze_ConfigCache::write_config_cache();
			update_option('breeze_ecommerce_detect', 0);
		}
	}

	/**
	 * Exclude pages of e-commerce from cache
	 */
	public function ecommerce_exclude_pages(){
		$urls = array();
		$regex = '*';

		if(class_exists('WooCommerce') && function_exists('wc_get_page_id')){
			$cardId = wc_get_page_id('cart');
			$checkoutId = wc_get_page_id('checkout');
			$myaccountId = wc_get_page_id('myaccount');

			if($cardId > 0){
				$urls[] = $this->get_basic_urls($cardId);
				// Get url through multi-languages plugin
				$urls = $this->get_translate_urls($urls, $cardId);
			}

			if($checkoutId > 0){
				$urls[] =  $this->get_basic_urls($checkoutId , $regex);
				// Get url through multi-languages plugin
				$urls = $this->get_translate_urls($urls, $checkoutId, $regex );
			}

			if($myaccountId > 0){
				$urls[] = $this->get_basic_urls($myaccountId , $regex);
				// Get url through multi-languages plugin
				$urls = $this->get_translate_urls($urls, $myaccountId, $regex );
			}

			// Process urls to return
			$urls = array_unique($urls);
			$urls = array_map(array($this,'rtrim_urls'),$urls);
		}

		return $urls;
	}

	/*
	 * Return basic url without translate plugin
	 */
	public function get_basic_urls($postID , $regex = null){
		$permalink = get_option('permalink_structure');

		if(!empty($permalink)) {
			// Custom URL structure
			$url = parse_url(get_permalink($postID),PHP_URL_PATH);
		}else {
			$url = get_permalink($postID);
		}

		return $url . $regex;
	}

	/*
	* Return translate url without translate plugin
	*/

	public function get_translate_urls($urls ,$postID , $regex = null){
		// WPML plugin
		if ( class_exists('SitePress')){
			global $sitepress;
			if(isset($sitepress)){
				$active_languages = $sitepress->get_active_languages();

				if(!empty($active_languages)){
					$languages = array_keys($active_languages);
					foreach ($languages as $language){
						$translatedId = icl_object_id($postID, 'page', false, $language);

						if(empty($translatedId)) continue;

						$urls[] = $this->get_basic_urls($translatedId,$regex);
					}
				}
			}
		}

		// Polylang plugin
		if( class_exists('Polylang') && function_exists('pll_languages_list') && function_exists('PLL')){
			$translatedId = pll_get_post_translations($postID);

			if(!empty($translatedId)){
				foreach ($translatedId as $id){
					$urls[] = $this->get_basic_urls($id,$regex);
				}
			}
		}

		// qTranslate-x plugin
		require_once (ABSPATH.'wp-admin/includes/plugin.php');
		if(is_plugin_active('qtranslate-x/qtranslate.php')){
			global $q_config;
			if(isset($q_config) && function_exists('qtranxf_convertURL')){
				$url = $this->get_basic_urls($postID);

				if(!empty($q_config['enabled_languages'])){
					foreach ($q_config['enabled_languages'] as $language){
						$urls[] =  qtranxf_convertURL( $url, $language , true);
					}
				}

			}
		}

		return $urls;
	}

	/*
	 * Remove '/' chacracter of end url
	 */
	public function rtrim_urls($url){
		return rtrim($url,'/');
	}

	public static function factory() {
		static $instance;

		if ( ! $instance ) {
			$instance = new self();
		}
		return $instance;
	}
}