<?php
/**
 * Template class
 * @author Flipper Code<hello@flippercode.com>
 * @version 3.0.0
 * @package Posts
 */

if ( ! class_exists( 'WPGMP_Template' ) ) {

	/**
	 * Controller class to display views.
	 * @author: Flipper Code<hello@flippercode.com>
	 * @version: 3.0.0
	 * @package: Maps
	 */

	class WPGMP_Template extends FlipperCode_HTML_Markup{


		function __construct($options = array()) {
			$premium_features = "<ul class='fc-pro-features'>
			<li>Show Custom Posts type.</li>
			<li>Posts Information in Info Window.</li>
			<li>Show Multiple Routes.</li>
			<li>Apply Beautiful Skins.</li>
			<li>Export/Import Markers.</li>
			<li>Get Directions.</li>
			<li>Show Nearby Amenities.</li>
			<li>Marker Clustering.</li>
			<li>ACF Supported.</li>
			<li>Show Listing with Filters.</li>
			<li>Show Polygons, Polylines, Circles & Rectangles.</li>
			<li>Html Overlays and many more...</li>
			</ul>";
			$productInfo = array('productName' => __('WP Google Map Plugin',WPGMP_TEXT_DOMAIN),
                        'productSlug' => 'wp-google-map-plugin',
                        'product_tag_line' => 'worlds most advanced google map plugin',
                        'productTextDomain' => WPGMP_TEXT_DOMAIN,
                        'productVersion' => WPGMP_VERSION,
                        'premium_features' => $premium_features,
                        'videoURL' => 'https://www.youtube.com/playlist?list=PLlCp-8jiD3p2PYJI1QCIvjhYALuRGBJ2A',
                        'docURL' => 'http://wpgmp.flippercode.com/tutorials/',
                        'demoURL' => 'https://wpgmp.flippercode.com/?utm_source=wordpress&utm_medium=link&utm_campaign=freemium',
                        'productSaleURL' => 'https://codecanyon.net/item/advanced-google-maps-plugin-for-wordpress/5211638/?utm_source=wordpress&utm_medium=link&utm_campaign=freemium',
                        'multisiteLicence' => 'http://codecanyon.net/item/advanced-google-maps-plugin-for-wordpress/5211638?license=extended&open_purchase_for_item_id=5211638&purchasable=source'
   			 );
			$productInfo = array_merge($productInfo, $options);
			parent::__construct($productInfo);

		}

	}
	
}