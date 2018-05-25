<?php
/**
 * Class: WPGMP_Model_Shortcode
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Shortcode' ) ) {

	/**
	 * Shortcode model to display output on frontend.
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Shortcode extends FlipperCode_Model_Base {
		/**
		 * Intialize Shortcode object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Settings Operation
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array();
		}
	}
}
