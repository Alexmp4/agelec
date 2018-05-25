<?php
/**
 * Controller class
 * @author Flipper Code<hello@flippercode.com>
 * @version 3.0.0
 * @package Posts
 */

if ( ! class_exists( 'WPGMP_Controller' ) ) {

	/**
	 * Controller class to display views.
	 * @author: Flipper Code<hello@flippercode.com>
	 * @version: 3.0.0
	 * @package: Maps
	 */

	class WPGMP_Controller extends Flippercode_Factory_Controller{


		function __construct() {

			parent::__construct(WPGMP_MODEL,'WPGMP_Model_');

		}

	}
	
}
