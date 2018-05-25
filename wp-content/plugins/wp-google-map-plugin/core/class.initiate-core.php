<?php

	/**
	 *  Load All Core Initialisation class
	 *  @package Core
	 *  @author Flipper Code <hello@flippercode.com>
	 */

	if ( ! class_exists( 'FlipperCode_Initialise_Core' ) ) {


		 class FlipperCode_Initialise_Core {

		 	public function __construct() {

		 		$this->_load_core_files();
		 		$this->_register_flippercode_globals();
	 	    }

		 	public function _register_flippercode_globals() {

		 		add_action( 'wp_ajax_fc_communication',array( $this, 'fc_communication' ) );
		 		add_action( 'wp_ajax_fc_geocoding',array( $this, 'fc_geocoding' ) );
		 		add_action( 'wp_ajax_check_products_updates',array( $this, 'check_products_updates' ) );
		 		add_action( 'wp_ajax_verify_envanto_purchase',array( $this, 'verify_envanto_purchase' ) );
		 		add_action( 'wp_ajax_download_plugin',array( $this, 'download_plugin' ) );
		 		add_action( 'wp_ajax_submit_user_suggestion',array( $this, 'submit_user_suggestion' ) );
		 		add_action( 'admin_enqueue_scripts', array($this,'load_products_common_resources') );


		  	}
		  	function fc_geocoding() {
		  		print_r($_POST);
		  		exit;
		  	}
		  	function fc_communication() {

		  		$result = array();

		  		if (isset($_POST['action']) and $_POST['action'] == 'fc_communication' and isset( $_POST['nonce'] ) && wp_verify_nonce($_POST['nonce'], 'fc_communication') )
	        	{
	        		$url = 'http://guide.flippercode.com//wunpupdates/';
	        		$data = array();
	        		$data['wunpu_action'] = sanitize_text_field($_POST['operation']);
	        		$product = sanitize_text_field( wp_unslash( $_POST['product'] ) );
 	        		foreach($_POST as $key => $value ) {
	        			$data[sanitize_text_field($key)] = sanitize_text_field($value);
	        		}

	        		$args = array('method' => 'POST', 'timeout' => 45, 'body' => $data );
					$response = wp_remote_post($url,$args);
					
					if ( is_wp_error( $response ) ) {
						$result = array('status' => '0','error' => $response->get_error_message()) ;
					} else {
						$result = (array) json_decode($response['body']);
						if( $data['wunpu_action'] == 'get_plugin_details' ) {
							$plugin_updates =  update_option('fc_'.$product, serialize( (array) $result['plugin_details'] ) );
						}
						$result = array('status' => '1','title' => $result['title'],'content' => $result['content']) ;
					}
					
	        	} else {
	        			$result = array('status' => '0','title' => 'Error','content' => 'Something went wrong. Try again in few minutes.') ;
	        	}

	        	echo json_encode($result);
				exit;

		  	}

		  		function download_plugin() {


			if (isset($_POST['action']) and $_POST['action'] == 'download_plugin' and isset( $_POST['nonce'] ) && wp_verify_nonce($_POST['nonce'], 'wpgmp-nonce') )
	        {

				$submitData = $_POST;
				$url = 'http://guide.flippercode.com/wunpupdates/';

				$bodyargs = array( 'wunpu_action' => 'download-plugin',
								'purchasekey' => sanitize_text_field(wp_unslash($submitData['purchase_code'])),
								'ip' => $_SERVER['REMOTE_ADDR'],
								'site_url' => urlencode(site_url()),
								'currentTextDomain' => sanitize_text_field(wp_unslash($submitData['product_id'])),
								'admin_email' => get_bloginfo('admin_email'));
				$args = array('method' => 'POST', 'timeout' => 45, 'body' => $bodyargs );

				$response = wp_remote_post($url,$args);
				if ( is_wp_error( $response ) ) {
				$result = array('status' => '0','error' => $response->get_error_message()) ;
				} else {
				   $valid_purchase = (array) json_decode($response['body']);

				   if($response['response']['code'] == '200') {

						   $result = array('status' => '1','purchase_verified' => $valid_purchase['status']);
						   if(  $valid_purchase['status'] == 1 ) {
						   		$archive_file_name = $valid_purchase['download_link'];
						   		header("Content-type: application/zip"); 
								header("Content-Disposition: attachment; filename=$archive_file_name");
								header("Content-length: " . filesize($archive_file_name));
								header("Pragma: no-cache"); 
								header("Expires: 0"); 
								readfile("$archive_file_name");
						   }
			   	    } else {

					   $result = array('status' => '0','purchase_verified' => $valid_purchase['status'],'error' => 'Sorry! Server cannot be reached right now.');
				   }

				}
				echo json_encode($result);
				exit;

		 	}

		   }

		 	function load_products_common_resources($hook) {


				if (strpos($hook, 'view_overview') !== false) {

					// One of our product's overview page. Load necessary resources on this page only.

				}


			}

			function is_localhost() {

				$isLocalhost = ($_SERVER['SERVER_NAME']!= 'localhost') ? true : false;
				return $isLocalhost;
			}

		 	function submit_user_suggestion() {

				$current_user = wp_get_current_user();
				if (isset( $_POST['action'] )
				&& $_POST['action'] == 'submit_user_suggestion'
				&& isset( $_POST['uss'] )
				&& wp_verify_nonce($_POST['uss'],'user-suggestion-submitted')
				)
				{
					$data = $_POST;
					$current_user = wp_get_current_user();
					$sitename = get_bloginfo('name');
					$username = $current_user->user_nicename;
					$siteURL = get_bloginfo('url');
					$siteadminemail = get_bloginfo('admin_email');
					$suggestion = sanitize_text_field($data['suggestion']);
					$suggestionfor = sanitize_text_field($data['suggestionfor']);
					$url = 'http://plugins.flippercode.com/wunpupdates/';
					$bodyargs = array( 'wunpu_action' => 'submit-suggestion',
									   'username' =>   $username,
									   'sitename' =>   $sitename,
									   'siteurl' =>    urlencode($siteURL),
									   'useremail' =>  $siteadminemail,
									   'suggestion' => $suggestion,
									   'suggestion_for' => $suggestionfor);
					$args = array('method' => 'POST', 'timeout' => 45, 'body' => $bodyargs );
					$response = wp_remote_post($url,$args);
					if ( is_wp_error( $response ) ) {
					$result = array('status' => '0','error' => $response->get_error_message()) ;
					} else {
					$result = array('status' => '1','submission_saved' => $response['body']);
					echo $response['body'];

					}
				 }else {
					echo 'failed';
				}

				exit;

			}
		 	function verify_envanto_purchase() {


			if (isset($_POST['action']) and $_POST['action'] == 'verify_envanto_purchase' and isset( $_POST['pvn'] ) && wp_verify_nonce($_POST['pvn'], 'purchase-verification-request') )
	        {

				$submitData = $_POST;
				$url = 'http://plugins.flippercode.com/wunpupdates/';

				$bodyargs = array( 'wunpu_action' => 'verify-purchase',
								'purchasekey' => wp_unslash($submitData['purchasekey']),
								'ip' => $_SERVER['REMOTE_ADDR'],
								'site_url' => urlencode(site_url()),
								'currentTextDomain' => $submitData['current_text_domain'],
								'admin_email' => get_bloginfo('admin_email'));
				$args = array('method' => 'POST', 'timeout' => 45, 'body' => $bodyargs );

				$response = wp_remote_post($url,$args);

				if ( is_wp_error( $response ) ) {
				$result = array('status' => '0','error' => $response->get_error_message()) ;
				} else {
				   $valid_purchase = (array) json_decode($response['body']);
				   if($response['response']['code'] == '200') {

						   $result = array('status' => '1','purchase_verified' => $valid_purchase['status']);
						   if(  $valid_purchase['status'] == 'true') {
							   update_option( $submitData['current_text_domain'].'_user_has_license', 'yes' );
							   update_option( $submitData['current_text_domain'].'_license_key', $submitData['purchasekey'] );
							   update_option( $submitData['current_text_domain'].'_license_details', $valid_purchase );
						   }
			   	    } else {

					   $result = array('status' => '0','purchase_verified' => $valid_purchase['status'],'error' => 'Sorry! Server cannot be reached right now.');
				   }

				}
				echo json_encode($result);
				exit;

		 	}

		   }

		   public function check_products_updates() {

				$url = 'http://guide.flippercode.com/wunpupdates/';
				$plugin = wp_unslash($_POST['productslug']);
		 		$bodyargs = array( 'wunpu_action' => 'updates',
		 						   'plugin' => $plugin,
		 						   'get_info' => 'version',
		 						   );
		 		
		 		$args = array('method' => 'POST', 'timeout' => 45, 'body' => $bodyargs );
     	 		$response = wp_remote_post($url,$args);
     	 		$response = (array) unserialize($response['body']);

     	 		if ( is_wp_error( $response ) ) {
				   $summary = array('status' => '0','error' => $response->get_error_message()) ;
				} else {

				 update_option( $plugin.'_latest_version', serialize($response) );

				 $version = trim($response['new_version'], '"');
				 $summary = array('status' => '1','latestversion' => wp_unslash(trim($version))) ;
				}

		 		echo json_encode($summary);
		 		exit;

		 	}


		 	public function hide_promotional_products() {

		 		if(isset($_POST['productname']) and !empty($_POST['productname']))
		 		update_option($_POST['productname'].'_hide_promotional_products','yes');
		 		//echo '<pre>'; print_r($_POST); exit;

		 	}

		 	public function _load_core_files() {

		 		$corePath  = plugin_dir_path( __FILE__ );
				$coreFiles = array(
					'class.tabular.php',
					'class.template.php',
					'abstract.factory.php',
					'class.controller-factory.php',
					'class.model-factory.php',
					'class.controller.php',
					'class.model.php',
					'class.validation.php',
					'class.database.php',
					'class.importer.php',
					'class.plugin-overview.php',
				);

				/**
				 *  Load All Core Initialisation class from core folder
				 */
				foreach ( $coreFiles as $file ) {

					if ( file_exists( $corePath.$file ) ) {
					    	require_once( $corePath.$file );
				    }
				}


		 	}

		 }

	}

    return new FlipperCode_Initialise_Core();

