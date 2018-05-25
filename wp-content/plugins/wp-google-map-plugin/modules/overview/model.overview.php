<?php
/**
 * Class: WPGMP_Model_Overview
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Overview' ) ) {

	/**
	 * Overview model for Plugin Overview.
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Overview extends FlipperCode_Model_Base {
		/**
		 * Intialize Backup object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Settings Operation
		 */
		function navigation() {
			return array(
			'wpgmp_how_overview' => __( 'How to Use', WPGMP_TEXT_DOMAIN ),
			);
		}
	}
}
