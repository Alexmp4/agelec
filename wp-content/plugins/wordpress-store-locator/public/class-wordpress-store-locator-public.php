<?php

class WordPress_Store_Locator_Public
{
    private $plugin_name;
    private $version;
    private $options;

    /**
     * Store Locator Plugin Construct
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   string                         $plugin_name 
     * @param   string                         $version    
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Enqueue Styles
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function enqueue_styles()
    {
        global $wordpress_store_locator_options;

        $this->options = $wordpress_store_locator_options;

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wordpress-store-locator-public.css', array(), $this->version, 'all');

        $doNotLoadBootstrap = $this->get_option('doNotLoadBootstrap');
        if (!$doNotLoadBootstrap) {
            wp_enqueue_style($this->plugin_name . '-bootsrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        }
        wp_enqueue_style($this->plugin_name . '-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), $this->version, 'all');

        $css = '';
        if (!$this->get_option('showName')) {
            $css .= ' .store_locator_name{display:none;}';
        }
        if (!$this->get_option('showStreet')) {
            $css .= ' .store_locator_street{display:none;}';
        }
        if (!$this->get_option('showCity')) {
            $css .= ' .store_locator_city{display:none;}';
        }
        if (!$this->get_option('showCountry')) {
            $css .= ' .store_locator_country{display:none;}';
        }
        if (!$this->get_option('showTelephone')) {
            $css .= ' .store_locator_tel{display:none;}';
        }
        if (!$this->get_option('showFax')) {
            $css .= ' .store_locator_fax{display:none;}';
        }
        if (!$this->get_option('showDistance')) {
            $css .= ' .store_locator_distance{display:none;}';
        }
        if (!$this->get_option('showMobile')) {
            $css .= ' .store_locator_mobile{display:none;}';
        }
        if (!$this->get_option('showWebsite')) {
            $css .= ' .store_locator_website{display:none;}';
        }
        if (!$this->get_option('showEmail')) {
            $css .= ' .store_locator_email{display:none;}';
        }
        if (!$this->get_option('showDescription')) {
            $css .= ' .store_locator_description{display:none;}';
        }
        if (!$this->get_option('resultListEnabled')) {
            $css .= ' .store_locator_result_list_box{display:none;}';
        }
        if (!$this->get_option('mapEnabled')) {
            $css .= ' .store_locator_main{display:none;}';
        }
        if (!$this->get_option('searchBoxEnabled')) {
            $css .= ' .store_locator_search_box{display:none;}';
        }
        if (!$this->get_option('searchBoxEnabled') && !$this->get_option('resultListEnabled')) {
            $css .= ' .store_locator_sidebar{display:none;}';
        }
        if (!$this->get_option('showActiveFilter')) {
            $css .= '#store_locator_filter_active_filter_box{display:none;}';
        }
        if (!$this->get_option('showFilter')) {
            $css .= '#store_locator_filter{display:none;}';
        }
        if (!$this->get_option('showGetDirection')) {
            $css .= ' .store_locator_get_direction{display:none !important;}';
        }
        if (!$this->get_option('showCallNow')) {
            $css .= ' .store_locator_call_now{display:none !important;}';
        }
        if (!$this->get_option('showVisitWebsite')) {
            $css .= ' .store_locator_visit_website{display:none !important;}';
        }
        if (!$this->get_option('showWriteEmail')) {
            $css .= ' .store_locator_write_email{display:none !important;}';
        }
        if (!$this->get_option('showShowOnMap')) {
            $css .= ' .store_locator_show_on_map{display:none !important;}';
        }
        if (!$this->get_option('showImage')) {
            $css .= ' .store_locator_image{display:none !important;}';
        }

        $opacity = $this->get_option('loadingOverlayTransparency');
        $css .= ' .store_locator_loading{background-color:' . $this->get_option('loadingOverlayColor') . ';opacity: ' . $opacity . ';}';
        $css .= ' .store_locator_loading i{color:' . $this->get_option('loadingIconColor') . ';}';
        $css .= ' .store_locator_infowindow{width: ' . $this->get_option('infowwindowWidth') . 'px;}';

        $customCSS = $this->get_option('customCSS');

        file_put_contents(dirname(__FILE__)  . '/css/wordpress-store-locator-custom.css', $css.$customCSS);

        wp_enqueue_style($this->plugin_name . '-custom', plugin_dir_url(__FILE__) . 'css/wordpress-store-locator-custom.css', array(), $this->version, 'all');

        return true;
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function enqueue_scripts()
    {
        global $wordpress_store_locator_options;

        $this->options = $wordpress_store_locator_options;

        $doNotLoadBootstrap = $this->get_option('doNotLoadBootstrap');
        if (!$doNotLoadBootstrap) {
            wp_enqueue_script($this->plugin_name . '-bootsrap', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), $this->version, true);
        }

        $mapsJS = 'https://maps.google.com/maps/api/js?libraries=places';
        $googleApiKey = $this->get_option('apiKey');
        if (!empty($googleApiKey)) {
            $mapsJS = $mapsJS . '&key=' . $googleApiKey;
        }

        wp_enqueue_script($this->plugin_name . '-gmaps', $mapsJS, array(), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-single', plugin_dir_url(__FILE__) . 'js/wordpress-store-locator-public-single.js', array('jquery', $this->plugin_name . '-gmaps'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-public', plugin_dir_url(__FILE__) . 'js/wordpress-store-locator-public.js', array('jquery', $this->plugin_name . '-gmaps'), $this->version, true);


        $forJS = $wordpress_store_locator_options;
        $forJS['ajax_url'] = admin_url('admin-ajax.php');
        unset($forJS['showContactStore']);
        unset($forJS['showContactStorePage']);
        if(!empty($wordpress_store_locator_options['showContactStore']) && !empty($wordpress_store_locator_options['showContactStorePage'])) {
            $forJS['showContactStorePage'] = get_permalink($wordpress_store_locator_options['showContactStorePage']);
        }

        unset($forJS['serverApiKey']);
        wp_localize_script($this->plugin_name . '-public', 'store_locator_options', $forJS);

        $customJS = $this->get_option('customJS');
        if (empty($customJS)) {
            return false;
        }

        file_put_contents(dirname(__FILE__)  . '/js/wordpress-store-locator-custom.js', $customJS);

        wp_enqueue_script($this->plugin_name . '-custom', plugin_dir_url(__FILE__) . 'js/wordpress-store-locator-custom.js', array('jquery'), $this->version, false);

        return true;
    }

    /**
     * Get Options
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   mixed                         $option The option key
     * @return  mixed                                 The option value
     */
    private function get_option($option)
    {
        if(!is_array($this->options)) {
            return false;
        }

        if (!array_key_exists($option, $this->options)) {
            return false;
        }

        return $this->options[$option];
    }

    /**
     * Init the Store Locator
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function init()
    {
        global $wordpress_store_locator_options;

        $this->options = $wordpress_store_locator_options;

        if (!$this->get_option('enable')) {
            return false;
        }

        add_shortcode('wordpress_store_locator', array($this, 'get_store_locator'));
        add_shortcode('wordpress_store_locator_search', array($this, 'get_store_locator_search'));

        // Single Product Button
        if ($this->get_option('buttonEnabled') && class_exists( 'WooCommerce' )) {
            $buttonPosition = $this->get_option('buttonPosition');
            !empty($buttonPosition) ? $buttonPosition = $buttonPosition : $buttonPosition = 'wordpress_single_product_summary';

            // Go to product page
            if ($this->get_option('buttonAction') == 1) {
                $modalPosition = $this->get_option('buttonModalPosition');
                !empty($modalPosition) ? $modalPosition = $modalPosition : $modalPosition = 'wp_footer';

                add_action($buttonPosition, array($this, 'store_modal_button'), 30);
                add_action($modalPosition, array($this, 'store_modal'), 10);
            }

            // Go to custom URL
            if ($this->get_option('buttonAction') == 2) {
                add_action($buttonPosition, array($this, 'custom_link'), 30);
            }
        }

        add_filter( 'the_content', array($this, 'stores_single_content') );
        add_filter( 'the_excerpt', array($this, 'stores_single_content') );
        add_filter( 'the_title', array($this, 'stores_single_title') );

        return true;
    }

    /**
     * Store Title
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.1.0
     * @link    http://plugins.db-dzine.com
     */
    public function stores_single_title($title) 
    {
        global $post;

        if(!isset($post->post_type)) {
            return $title;
        }

        if ($post->post_type == 'stores') {
            if(in_the_loop()){
                $prefix = "wordpress_store_locator_";
                $meta = get_post_meta($post->ID);

                $title = $title . ' ' . __('Store in', 'wordpress-store-locator') . ' ' . $meta[ $prefix . 'city' ][0];
            }
        }

        return $title;
    }

    /**
     * Single store page
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.1.0
     * @link    http://plugins.db-dzine.com
     */
    public function stores_single_content($content) 
    {
        global $post;

        if(!isset($post->post_type)) {
            return $content;
        }

        if ($post->post_type == 'stores') {

            $prefix = "wordpress_store_locator_";
            $meta = get_post_meta($post->ID);

            $args = array('fields' => 'names', 'orderby' => 'name', 'order' => 'ASC');

            // Categories
            $store_cats = wp_get_object_terms($post->ID, 'store_category', $args);
            $categories = '<div class="store_locator_single_categories">';
                $categories .= '<strong class="store_locator_single_categories_title">' . __('Categories: ', 'wordpress-store-locator') . '</strong>' . implode(', ', $store_cats);
            $categories .= '</div>';

            $store_filter = wp_get_object_terms($post->ID, 'store_filter', $args);
            $filter = '<div class="store_locator_single_filter">';
                $filter .= '<strong class="store_locator_single_filter_title">' . __('Filter: ', 'wordpress-store-locator') . '</strong>' . implode(', ', $store_filter);
            $filter .= '</div>';

            $address1 = isset($meta[ $prefix . 'address1' ][0]) ? $meta[ $prefix . 'address1' ][0] : '';
            $address2 = isset($meta[ $prefix . 'address2' ][0]) ? $meta[ $prefix . 'address2' ][0] : '';
            $zip = isset($meta[ $prefix . 'zip' ][0]) ? $meta[ $prefix . 'zip' ][0] : '';
            $city = isset($meta[ $prefix . 'city' ][0]) ? $meta[ $prefix . 'city' ][0] : '';
            $region = isset($meta[ $prefix . 'region' ][0]) ? $meta[ $prefix . 'region' ][0] : '';
            $country = isset($meta[ $prefix . 'country' ][0]) ? $meta[ $prefix . 'country' ][0] : '';
            $telephone = isset($meta[ $prefix . 'telephone' ][0]) ? $meta[ $prefix . 'telephone' ][0] : '';
            $mobile = isset($meta[ $prefix . 'mobile' ][0]) ? $meta[ $prefix . 'mobile' ][0] : '';
            $fax = isset($meta[ $prefix . 'fax' ][0]) ? $meta[ $prefix . 'fax' ][0] : '';
            $email = isset($meta[ $prefix . 'email' ][0]) ? $meta[ $prefix . 'email' ][0] : '';
            $website = isset($meta[ $prefix . 'website' ][0]) ? $meta[ $prefix . 'website' ][0] : '';

            $description = "";
            if($this->get_option('showAddressStyle') == "american") {
                $address = '<div class="store_locator_single_address">';
                    $address .=  '<h2>' . __('Address ', 'wordpress-store-locator') . '</h2>';
                    $address .= !empty($address1) ? $address1 . '<br/>' : '';
                    $address .= !empty($address2) ? $address2 . '<br/>' : '';
                    $address .= !empty($city) ? $city . ', ' : '';
                    $address .= !empty($region) ? $region . ' ' : '';
                    $address .= !empty($zip) ? $zip . '<br/>' : '';
                    $address .= !empty($country) ? $country : '';
                $address .= '</div>';
            } else {
                $address = '<div class="store_locator_single_address">';
                    $address .=  '<h2>' . __('Address ', 'wordpress-store-locator') . '</h2>';
                    $address .= !empty($address1) ? $address1 . '<br/>' : '';
                    $address .= !empty($address2) ? $address2 . '<br/>' : '';
                    $address .= !empty($zip) ? $zip . ', ' : '';
                    $address .= !empty($city) ? $city . ', ' : '';
                    $address .= !empty($region) ? $region . ', ' : '';
                    $address .= !empty($country) ? $country : '';
                $address .= '</div>';
            }

            $contact = '<div class="store_locator_single_contact">';
                $contact .=  '<h2>' . __('Contact ', 'wordpress-store-locator') . '</h2>';
                $contact .= !empty($telephone) ? $this->get_option('showTelephoneText') . ': <a href="tel:' .  $telephone  . '">' . $telephone . '</a><br/>' : '';
                $contact .= !empty($mobile) ? $this->get_option('showMobileText') . ': <a href="tel:' .  $mobile  . '">' . $mobile . '</a><br/>' : '';
                $contact .= !empty($fax) ? $this->get_option('showFaxText') . ': <a href="tel:' .  $fax  . '">' . $fax . '</a><br/>' : '';
                $contact .= !empty($email) ? $this->get_option('showEmailText') . ': <a href="mailto:' .  $email  . '">' . $email . '</a><br/>' : '';
                $contact .= !empty($website) ? $this->get_option('showWebsiteText') . ': <a href="' .  $website  . '" target="_blank">' . $website . '</a><br/>' : '';
            $contact .= '</div>
                        <div class="store_locator_single_clear"></div>';

            $map = "";
            $opening_hours = "";
            if(is_single()) {

                $weekdays = array(
                    __('Monday', 'wordpress-store-locator'),
                    __('Tuesday', 'wordpress-store-locator'),
                    __('Wednesday', 'wordpress-store-locator'),
                    __('Thursday', 'wordpress-store-locator'),
                    __('Friday', 'wordpress-store-locator'),
                    __('Saturday', 'wordpress-store-locator'),
                    __('Sunday', 'wordpress-store-locator'),
                );
                $c = 0;
                foreach ($weekdays as $weekday) {
                    $open = isset($meta[ $prefix . $weekday . "_open"]) ? $meta[ $prefix . $weekday . "_open"][0] : '';
                    $close = isset($meta[ $prefix . $weekday . "_close"]) ? $meta[ $prefix . $weekday . "_close"][0] : '';
                    
                    if(!empty($open) && !empty($close)) {
                        $opening_hours .= $weekday . ': ' . $open . ' – ' . $close . '<br/>';
                    } elseif(!empty($open)) {
                        $opening_hours .= $weekday . ': ' . $open . '<br/>';
                    } elseif(!empty($close)) {
                        $opening_hours .= $weekday . ': ' . $close . '<br/>';
                    }
                }
                if(!empty($opening_hours)) {
                    $opening_hours = '<div class="store_locator_single_opening_hours">' . 
                                        '<h2>' . __('Opening Hours ', 'wordpress-store-locator') . '</h2>' .
                                        $opening_hours . 
                                    '</div>';
                }

                $contactStore = "";
                if($this->get_option('showContactStore')) {
                    $contactStorePage = $this->get_option('showContactStorePage');
                    $contactStoreText = $this->get_option('showContactStoreText');
                    if(!empty($contactStorePage)) {
                        $contactStorePage = get_permalink($contactStorePage) . '?store_id=' . $post->ID;
                    }
                    $contactStore = '<div class="store_locator_single_contact_store">' . 
                                        '<a href="' . $contactStorePage . '" class="store_locator_contact_store_button btn button et_pb_button btn-primary theme-button btn-lg center">' . $contactStoreText . '</a>'. 
                                    '</div>';
                }

                $map .= '<div id="store_locator_single_map" class="store_locator_single_map" 
                                    data-lat="' . $meta[ $prefix . 'lat' ][0] . '" 
                                    data-lng="' . $meta[ $prefix . 'lng' ][0] . '"></div>';
            }

            $content = $categories . $filter . $content . $address . $contact . $opening_hours . $contactStore . $map;
        }

        return $content;
    }

    /**
     * Create the single product button.
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     */
    public function store_modal_button()
    {
        $buttonText = $this->get_option('buttonText');

		echo 
        '<button id="store_modal_button" type="button" class="btn button et_pb_button btn-primary btn-lg">'
			. $buttonText . 
		'</button>';
    }

    /**
     * Create the store locator modal
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     */
    public function store_modal()
    {
        $modalSize = $this->get_option('buttonModalSize');

        // Only render Maps Plugin on Product Page (Performance)
        if (!is_product()) {
            return false;
        }
        ?>

		<!-- WordPress Store Locator Modal -->
		<div id="store_modal" class="store_modal store-locator-modal store-locator-fade" tabindex="-1" role="dialog">
			<div class="modal-dialog <?php echo $modalSize ?>" role="document">
				<div class="modal-content">
					<?php echo $this->get_store_locator(); ?>
				</div>
			</div>
		</div>
	<?php
    }

    /**
     * Create the store locator
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     */
    public function get_store_locator($atts = array())
    {
        $args = shortcode_atts(array(
            'categories' => 'all',
            'show_children' => 'yes',
            'show_all' => 'yes',
        ), $atts);

        $this->shortcode_categories = $args['categories'];
        $this->shortcode_show_children = $args['show_children'];
        $this->shortcode_show_all = $args['show_all'];

        // Layout
        $resultListPosition = $this->get_option('resultListPosition');
        $searchBoxPosition = $this->get_option('searchBoxPosition');
        $mapColumns = $this->get_option('mapColumns');
        // Loading Screen
        $loadingIcon = $this->get_option('loadingIcon');
        $loadingAnimation = $this->get_option('loadingAnimation');
        $loadingIconSize = $this->get_option('loadingIconSize');
        $icon = $loadingIcon . ' ' . $loadingAnimation . ' ' . $loadingIconSize;

        if($this->get_option('useOutputBuffering')) {
            ob_start();
        }
        ?>

		<div id="store_locator" class="store_locator modal-body">
			<div class="store-locator-row">
				<?php
                if ($searchBoxPosition == 'above') {
                    $this->get_search_box();
                }

                if ($resultListPosition != 'below') {
                    $this->get_sidebar_content();
                }
                ?>
				<div id="store_locator_main" class="store_locator_main  store-locator-col-xs-12 store-locator-col-sm-12 store-locator-col-md-<?php echo $mapColumns ?>">
					<div id="store_locator_map" style="height: 100%;"></div>
                    <div id="store_locator_dragged_button" class="btn button et_pb_button button-primary theme-button store_locator_dragged_button">
                        <?php echo __('Search in this Area', 'wordpress-store-locator' ) ?>
                    </div>
				</div>
			    <?php
                if ($resultListPosition == 'below') {
                    $this->get_sidebar_content();
                }
                if ($searchBoxPosition == 'below') {
                    $this->get_search_box();
                }
                ?>
			</div>
            <button type="button" id="store_modal_close" class="store_modal_close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<div id="store_locator_loading" class="store_locator_loading store-locator-hidden"><i class="fa <?php echo $icon ?>"></i></div>
		</div>

	<?php
        if($this->get_option('useOutputBuffering')) {
            $output_string = strtr(ob_get_contents(), array("\t" => "", "\n" => "", "\r" => ""));
            ob_end_clean();
            return $output_string;
        }
    }

    /**
     * Create the sidebar
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     */
    private function get_sidebar_content()
    {
        $searchBoxPosition = $this->get_option('searchBoxPosition');
        $resultListColumns = $this->get_option('resultListColumns');
        $resultListPosition = $this->get_option('resultListPosition');
        ?>
		<div id="store_locator_sidebar" class="store_locator_sidebar store-locator-col-xs-12 store-locator-col-sm-12 store-locator-col-md-<?php echo $resultListColumns ?> <?php echo $resultListPosition ?>">
			<div id="store_locator_sidebar_content" class="store_locator_sidebar_content store-locator-row">
    		<?php
            if ($searchBoxPosition == 'before') {
                $this->get_search_box();
                $this->get_result_list();
            } elseif ($searchBoxPosition == 'after') {
                $this->get_result_list();
                $this->get_search_box();
            } else {
                $this->get_result_list();
            }
            ?>
			</div>
		</div>

		<?php

    }

    /**
     * Get the Search Box
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     */
    private function get_search_box()
    {
        global $post;
        $modalTitle = $this->get_option('buttonModalTitle');
        $searchButtonText = $this->get_option('searchButtonText');
        $searchBoxPosition = $this->get_option('searchBoxPosition');
        $searchBoxColumns = $this->get_option('searchBoxColumns');

        $searchBoxShowGetMyPosition = $this->get_option('searchBoxShowGetMyPosition');
        $searchBoxShowShowAllStores = $this->get_option('searchBoxShowShowAllStores');
        $searchBoxShowButton = $this->get_option('searchBoxShowButton');
        $searchBoxShowTitle = $this->get_option('searchBoxShowTitle');
        $searchBoxShowActiveFilter = $this->get_option('searchBoxShowActiveFilter');
        $searchBoxShowFilter = $this->get_option('searchBoxShowFilter');

        if ($searchBoxPosition == 'above' || $searchBoxPosition == 'below') {
            $searchBoxColumns = 'store-locator-col-md-' . $searchBoxColumns;
        } else {
            $searchBoxColumns = '';
        }
        ?>

		<div id="store_locator_search_box" class="store_locator_search_box store-locator-col-xs-12 store-locator-col-sm-12 <?php echo $searchBoxColumns ?>">
			<div class="store-locator-row">
				<div class="store-locator-col-xs-12 store-locator-col-sm-12">

                    <?php if($searchBoxShowTitle) { ?>
					<h2 class="store_modal_title"><?php echo $modalTitle ?></h2>
                    <?php } ?>

                    <?php if($searchBoxShowActiveFilter) { ?>
                    <span id="store_locator_filter_active_filter_box" class="store_locator_filter_active_filter_box">
					   <small><?php echo __('Active Filter', 'wordpress-store-locator' ) ?>:</small> <span id="store_locator_filter_active_filter"></span>
                    </span>
                    <?php } ?>

				</div>
			</div>
			<div class="store-locator-row">
				<div class="store-locator-col-xs-12 store-locator-col-sm-12">

					<input id="store_locator_address_field" class="store_locator_address_field" type="text" name="location" placeholder="<?php echo __('Enter your address', 'wordpress-store-locator') ?>">
                    <?php if($searchBoxShowGetMyPosition) { ?>
					<a href="#" id="store_locator_get_my_position"><i><?php echo __('Get my Position', 'wordpress-store-locator' ) ?></i></a>
                    <?php } ?>
                    <?php if($searchBoxShowShowAllStores) { ?>
                    <a href="#" id="store_locator_get_all_stores"><i><?php echo __('Show all Stores', 'wordpress-store-locator' ) ?></i></a>
                    <?php } ?>
                    

				</div>
			</div>

            <?php if($searchBoxShowButton) { ?>
			<div class="store-locator-row">
				<div class="store-locator-col-xs-12 store-locator-col-sm-12">
					<button id="store_locator_find_stores_button" type="button" class="store_locator_find_stores_button btn button et_pb_button btn-primary btn-lg">
						<?php echo $searchButtonText ?>
					</button>
				</div>
			</div>
            <?php } ?>

            <?php if($searchBoxShowFilter) { ?>
			<div class="store-locator-row">
				<?php
                $this->get_filter();
                ?>
			</div>
            <?php } ?>
		</div>

		<?php

    }

    public function get_store_locator_search($atts = array())
    {
        global $post;

        $args = shortcode_atts(array(
            'style' => '1',
            'url' => '',
            'show_filter' => 'yes',
            'show_all' => 'yes',
        ), $atts);

        $url = $args['url'];
        $searchBoxShowFilter = $args['show_filter'];
        $searchBoxStyle = $args['style'];

        if(empty($url)) {
            return 'You need a valid store locator URL where this search should redirect to!';
        }

        $modalTitle = $this->get_option('buttonModalTitle');
        $searchButtonText = $this->get_option('searchButtonText');
        $searchBoxShowGetMyPosition = $this->get_option('searchBoxShowGetMyPosition');

        if($this->get_option('useOutputBuffering')) {
            ob_start();
        }
        ?>

        <form id="store_locator_embedded_search" class="store_locator_embedded_search" action="<?php echo $url ?>" method="GET">
            <div id="store_locator_search_box" class="store_locator store_locator_search_box store-locator-col-xs-12 store-locator-col-sm-12">
                <div class="store-locator-row">
                    <div class="store-locator-col-xs-12 store-locator-col-sm-12">

                        <input id="store_locator_address_field" class="store_locator_address_field" type="text" name="location" placeholder="<?php echo __('Enter your address', 'wordpress-store-locator') ?>">
                        <?php if($searchBoxShowGetMyPosition) { ?>
                        <a href="#" id="store_locator_get_my_position"><i><?php echo __('Get my Position', 'wordpress-store-locator' ) ?></i></a>
                        <?php } ?>

                    </div>
                </div>

                <?php if($searchBoxShowFilter == "yes") { ?>
                <div class="store-locator-row">
                    <?php
                    $this->get_search_box_filter();
                    ?>
                </div>
                <?php } ?>

                <div class="store-locator-row">
                    <div class="store-locator-col-xs-12 store-locator-col-sm-12">
                        <button id="store_locator_find_stores_button" type="submit" class="store_locator_find_stores_button btn button et_pb_button btn-primary btn-lg">
                            <?php echo $searchButtonText ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <?php
        if($this->get_option('useOutputBuffering')) {
            $output_string = strtr(ob_get_contents(), array("\t" => "", "\n" => "", "\r" => ""));
            ob_end_clean();
            return $output_string;
        }
    }

    /**
     * Get the result list
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     */
    private function get_result_list()
    {
        $resultListShowTitle = $this->get_option('resultListShowTitle');
        ?>
		<div id="store_locator_result_list_box" class="store_locator_result_list_box store-locator-col-xs-12 store-locator-col-sm-12">
            <hr class="grey">
            <?php if($resultListShowTitle) { ?>
			<h3 class="store_locator_result_list_title"><?php echo __('Results', 'wordpress-store-locator' ) ?></h3>
            <?php } ?>
			<div id="store_locator_result_list">
                
            </div>
		</div>
		<?php

    }

    /**
     * Get the filter
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     */
    private function get_filter()
    {
        global $post;

        $mapRadiusSteps = $this->get_option('mapRadiusSteps');
        $mapRadius = $this->get_option('mapRadius');
        $mapDistanceUnit = $this->get_option('mapDistanceUnit');

        // Store Categories & Filter
        $filter = get_terms('store_filter');
        $categories = array();
        if($this->shortcode_categories !== "all" && !empty($this->shortcode_categories)) {

            $shortcode_categories = explode(',', $this->shortcode_categories);
            foreach ($shortcode_categories as $cat) {
                $categories[] = get_term($cat);

                if($this->shortcode_show_children == "yes") {
                    $args = array('parent' => $cat);
                    $children_categories = get_terms('store_category', $args);
                    foreach ($children_categories as $child_category) {
                        $categories[] = $child_category;
                    }
                }
            }
            
        } else {
            $categories = get_terms('store_category');
        }

        $searchBoxDefaultCategory = $this->get_option('searchBoxDefaultCategory');
        $temp = array();
        $this->sort_terms_hierarchicaly($categories, $temp);
        if(!empty($temp)) {
            $categories = $temp;
        }

        // Preselect store category if it is connected to a product category
        $product_categories = array();
        $terms = get_the_terms($post->ID, 'product_cat');
        if (isset($terms) && !empty($terms) && is_array($terms)) {
            foreach ($terms as $term) {
                $product_categories[] = $term->term_id;
            }
        }
        $showFilterOpen = "none";
        if($this->get_option('searchBoxShowFilterOpen')) {
            $showFilterOpen = "block";
        }
        ?>

		<div id="store_locator_filter" class="store_locator_filter">
			<div id="store_locator_filter_open_close" class="store_locator_filter_open_close store-locator-col-xs-12 store-locator-col-sm-12">
				<h3 class="store_locator_filter_title"><?php echo __('Filter', 'wordpress-store-locator' ) ?></h3> <i class="fa fa-chevron-down text-right"></i>
			</div>
			<div id="store_locator_filter_content" class="store_locator_filter_content" style="display: <?php echo $showFilterOpen ?>;">
				<div class="store-locator-col-xs-12 store-locator-col-sm-12 single_filter">
                    <?php 
                    if($this->get_option('showFilterCategoriesAsImage')) {

                        $defaultMapMarker = $this->get_option('mapDefaultIcon');

                        if($this->shortcode_show_all == "yes") { 
                            echo    '<a class="store_locator_category_filter_image">' .
                                        '<img src="' . $defaultMapMarker . '" data-category=""><br>' . 
                                        '<span class="store_locator_category_filter_image_name">' . __('All Stores', 'wordpress-store-locator' ) . '</span>' .
                                    '</a>';
                        }
                        foreach ($categories as $category) {
                            $linked_category = get_term_meta($category->term_id, 'wordpress_store_locator_product_category');
                            $category_icon_url = "";
                            $category_icon = get_term_meta($category->term_id, 'wordpress_store_locator_icon');
                            if(isset($category_icon[0]) && !empty($category_icon[0]['url'])) {
                                $category_icon_url = $category_icon[0]['url'];
                            } else {
                                $category_icon_url = $defaultMapMarker;
                            }
                            $selected = '';

                            if (in_array($linked_category[0], $product_categories)) {
                                $selected = 'data-selected="selected"';
                            }

                            if (!empty($searchBoxDefaultCategory)) {
                                if($searchBoxDefaultCategory == $category->term_id) {
                                    $selected = 'data-selected="selected"';
                                }
                            }

                            echo    '<a class="store_locator_category_filter_image" ' . $selected . ' data-category="' . $category->term_id . '" data-icon="' . $category_icon_url . '">' .
                                        '<img src="' . $category_icon_url . '"><br>' . 
                                        '<span class="store_locator_category_filter_image_name">' .  $category->name . '</span>' .
                                    '</a>';

                            if(isset($category->children)) {
                                foreach ($category->children as $childCategory) {
                                    $linked_category = get_term_meta($childCategory->term_id, 'wordpress_store_locator_product_category');

                                    $category_icon_url = "";
                                    $category_icon = get_term_meta($childCategory->term_id, 'wordpress_store_locator_icon');
                                    if(isset($category_icon[0]) && !empty($category_icon[0]['url'])) {
                                        $category_icon_url = $category_icon[0]['url'];
                                    } else {
                                        $category_icon_url = $defaultMapMarker;
                                    }

                                    $selected = '';
                                    if (isset($linked_category[0]) && (in_array($linked_category[0], $product_categories))) {
                                        $selected = 'selected="selected"';
                                    }

                                     if (!empty($searchBoxDefaultCategory)) {
                                        if($searchBoxDefaultCategory == $childCategory->term_id) {
                                            $selected = 'selected="selected"';
                                        }
                                    }
                                    echo    '<a class="store_locator_category_filter_image" ' . $selected . ' data-category="' . $childCategory->term_id . '" data-icon="' . $category_icon_url . '">' .
                                        '<img src="' . $category_icon_url . '"><br>' . 
                                        '<span class="store_locator_category_filter_image_name">' .  $childCategory->name . '</span>' .
                                    '</a>';
                                }
                            }
                        }
                    } else {
                    ?>
					<select name="categories" id="store_locator_filter_categories" class="select store_locator_filter_categories">

                        <?php
                        if($this->shortcode_show_all == "yes") { ?>
						<option value=""><?php echo __('Select a Category', 'wordpress-store-locator' ) ?></option>
    					<?php
                        }

                        foreach ($categories as $category) {
                            $linked_category = get_term_meta($category->term_id, 'wordpress_store_locator_product_category');
                            $category_icon_url = "";
                            $category_icon = get_term_meta($category->term_id, 'wordpress_store_locator_icon');
                            if(isset($category_icon[0]) && !empty($category_icon[0]['url'])) {
                                $category_icon_url = $category_icon[0]['url'];
                            }
                            $selected = '';

                            if (in_array($linked_category[0], $product_categories)) {
                                $selected = 'selected="selected"';
                            }

                            if (!empty($searchBoxDefaultCategory)) {
                                if($searchBoxDefaultCategory == $category->term_id) {
                                    $selected = 'selected="selected"';
                                }
                            }

                            echo '<option value="' . $category->term_id . '" ' . $selected . ' data-icon="' . $category_icon_url . '">' . $category->name . '</option>';

                            if(isset($category->children)) {
                                foreach ($category->children as $childCategory) {
                                    $linked_category = get_term_meta($childCategory->term_id, 'wordpress_store_locator_product_category');

                                    $category_icon_url = "";
                                    $category_icon = get_term_meta($childCategory->term_id, 'wordpress_store_locator_icon');
                                    if(isset($category_icon[0]) && !empty($category_icon[0]['url'])) {
                                        $category_icon_url = $category_icon[0]['url'];
                                    }
                                    $selected = '';

                                    if (isset($linked_category[0]) && (in_array($linked_category[0], $product_categories))) {
                                        $selected = 'selected="selected"';
                                    }

                                     if (!empty($searchBoxDefaultCategory)) {
                                        if($searchBoxDefaultCategory == $childCategory->term_id) {
                                            $selected = 'selected="selected"';
                                        }
                                    }

                                    echo '<option value="' . $childCategory->term_id . '" ' . $selected . ' data-icon="' . $category_icon_url . '">-- ' . $childCategory->name . '</option>';
                                }
                            }
                        }
                        ?>
					</select>
                    <?php 
                    }
                    ?>
				</div>
				<div class="store-locator-col-xs-12 store-locator-col-sm-12 single_filter">
					<h5>Radius</h5>
					<select name="radius" id="store_locator_filter_radius" class="select store_locator_filter_radius">
					<?php
                        $mapRadiusSteps = explode(',', $mapRadiusSteps);
                        foreach ($mapRadiusSteps as $mapRadiusStep) {
                            if ($mapRadius == $mapRadiusStep) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo '<option value="' . $mapRadiusStep . '" ' . $selected . '>' . $mapRadiusStep . ' ' . $mapDistanceUnit . '</option>';
                        }
                        ?>
					</select>
				</div>
				<?php
                $temp = array();
                $this->sort_terms_hierarchicaly($filter, $temp);
                $filter = $temp;
                foreach ($filter as $singleFilter) {
                    echo '<div class="store-locator-col-xs-12 store-locator-col-sm-12 single_filter">';
                    echo '<h5>' . $singleFilter->name . '</h5>';

                    if (isset($singleFilter->children)) {
                        foreach ($singleFilter->children as $singleFilterChild) {
                            $linked_category = get_term_meta($singleFilterChild->term_id, 'wordpress_store_locator_product_category');

                            if (isset($linked_category[0]) && (in_array($linked_category[0], $product_categories) )) {
                                $checked = 'checked';
                            } else {
                                $checked = '';
                            }

                            echo '<label class="single_filter_checkbox control control--checkbox"><input ' . $checked . ' name="' . $singleFilterChild->term_id . '" type="checkbox" class="store_locator_filter_checkbox" value="' . $singleFilterChild->name . '">' . $singleFilterChild->name . '<div class="control__indicator"></div></label>';
                        }
                    }
                    echo '</div>';
                }
                ?>
			</div>
		</div>
		<?php

    }

    /**
     * Create a Custom Link
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     */
    public function custom_link()
    {
        $buttonText = $this->get_option('buttonText');
        $buttonURL = $this->get_option('buttonActionURL');
        $buttonTarget = $this->get_option('buttonActionURLTarget');

        echo    '<a id="store_locator_custom_bottom" target="' . $buttonTarget . '" href="' . $buttonURL . '" class="store_locator_custom_bottom button alt">'
                    . $buttonText .
                '</a>';
    }

    /**
     * Sort Wordpress Terms Hierarchicaly
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   array                          &$cats
     * @param   array                          &$into
     * @param   integer                        $parentId
     * @return  array
     */
    private function sort_terms_hierarchicaly(array &$cats, array &$into, $parentId = 0)
    {
        foreach ($cats as $i => $cat) {
            if ($cat->parent == $parentId) {
                $into[$cat->term_id] = $cat;
                unset($cats[$i]);
            }
        }

        foreach ($into as $topCat) {
            $topCat->children = array();
            $this->sort_terms_hierarchicaly($cats, $topCat->children, $topCat->term_id);
        }
    }

    /**
     * Get the filter
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     */
    private function get_search_box_filter()
    {
        global $post;

        // Store Categories & Filter
        $filter = get_terms('store_filter');
        $categories = array();

        $categories = get_terms('store_category');

        $searchBoxDefaultCategory = $this->get_option('searchBoxDefaultCategory');
        $temp = array();
        $this->sort_terms_hierarchicaly($categories, $temp);
        if(!empty($temp)) {
            $categories = $temp;
        }

        // Preselect store category if it is connected to a product category
        $product_categories = array();
        $terms = get_the_terms($post->ID, 'product_cat');
        if (isset($terms) && !empty($terms) && is_array($terms)) {
            foreach ($terms as $term) {
                $product_categories[] = $term->term_id;
            }
        }
        ?>

        <div id="store_locator_filter" class="store_locator_filter">
            <div id="store_locator_filter_content" class="store_locator_filter_content">
                <div class="store-locator-col-xs-12 store-locator-col-sm-4 single_filter category_filter">
                    <h5><?php echo __('Category', 'wordpress-store-locator' ) ?></h5>
                    <select name="category" id="store_locator_filter_categories" class="select store_locator_filter_categories">
                        <option value=""><?php echo __('Select a Category', 'wordpress-store-locator' ) ?></option>
                        <?php

                        foreach ($categories as $category) {
                            $category_icon_url = "";
                            $category_icon = get_term_meta($category->term_id, 'wordpress_store_locator_icon');
                            if(isset($category_icon[0]) && !empty($category_icon[0]['url'])) {
                                $category_icon_url = $category_icon[0]['url'];
                            }
                            $selected = '';

                            if (!empty($searchBoxDefaultCategory)) {
                                if($searchBoxDefaultCategory == $category->term_id) {
                                    $selected = 'selected="selected"';
                                }
                            }

                            echo '<option value="' . $category->term_id . '" ' . $selected . ' data-icon="' . $category_icon_url . '">' . $category->name . '</option>';

                            if(isset($category->children)) {
                                foreach ($category->children as $childCategory) {

                                    $category_icon_url = "";
                                    $category_icon = get_term_meta($childCategory->term_id, 'wordpress_store_locator_icon');
                                    if(isset($category_icon[0]) && !empty($category_icon[0]['url'])) {
                                        $category_icon_url = $category_icon[0]['url'];
                                    }
                                    $selected = '';

                                     if (!empty($searchBoxDefaultCategory)) {
                                        if($searchBoxDefaultCategory == $childCategory->term_id) {
                                            $selected = 'selected="selected"';
                                        }
                                    }

                                    echo '<option value="' . $childCategory->term_id . '" ' . $selected . ' data-icon="' . $category_icon_url . '">-- ' . $childCategory->name . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
                $temp = array();
                $this->sort_terms_hierarchicaly($filter, $temp);
                $filter = $temp;
                foreach ($filter as $singleFilter) {
                    echo '<div class="store-locator-col-xs-12 store-locator-col-sm-4 single_filter">';
                    echo '<h5>' . $singleFilter->name . '</h5>';

                    if (isset($singleFilter->children)) {
                        foreach ($singleFilter->children as $singleFilterChild) {
                            $linked_category = get_term_meta($singleFilterChild->term_id, 'wordpress_store_locator_product_category');

                            if (isset($linked_category[0]) && (in_array($linked_category[0], $product_categories) )) {
                                $checked = 'checked';
                            } else {
                                $checked = '';
                            }

                            echo '<label class="single_filter_checkbox control control--checkbox"><input ' . $checked . ' name="filter[]" type="checkbox" class="store_locator_filter_checkbox" value="' . $singleFilterChild->term_id . '">' . $singleFilterChild->name . '<div class="control__indicator"></div></label>';
                        }
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <?php

    }
}
