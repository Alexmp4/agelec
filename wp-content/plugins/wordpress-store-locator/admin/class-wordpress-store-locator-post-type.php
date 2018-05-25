<?php
/**
 * Custom Post Type for Stores and Taxonomies.
 */
class WordPress_Store_Locator_Post_Type
{
    private $plugin_name;
    private $version;
    /**
     * Constructor.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @param string $plugin_name
     * @param string $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->prefix = 'wordpress_store_locator_';

        add_filter('manage_stores_posts_columns', array($this, 'columns_head'));
        add_action('manage_stores_posts_custom_column', array($this, 'columns_content'), 10, 1);
    }

    /**
     * Init.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @return bool
     */
    public function init()
    {
        $this->register_store_locator_post_type();
        $this->register_store_locator_taxonomy();
        $this->add_custom_meta_fields();
    }

    /**
     * Register Store Post Type.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @return bool
     */
    public function register_store_locator_post_type()
    {
        $singular = __('Store', 'wordpress-store-locator');
        $plural = __('Stores', 'wordpress-store-locator');

        $labels = array(
            'name' => __('Store Locator', 'wordpress-store-locator'),
            'all_items' => sprintf(__('All %s', 'wordpress-store-locator'), $plural),
            'singular_name' => $singular,
            'add_new' => sprintf(__('New %s', 'wordpress-store-locator'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'wordpress-store-locator'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'wordpress-store-locator'), $singular),
            'new_item' => sprintf(__('New %s', 'wordpress-store-locator'), $singular),
            'view_item' => sprintf(__('View %s', 'wordpress-store-locator'), $plural),
            'search_items' => sprintf(__('Search %s', 'wordpress-store-locator'), $plural),
            'not_found' => sprintf(__('No %s found', 'wordpress-store-locator'), $plural),
            'not_found_in_trash' => sprintf(__('No %s found in trash', 'wordpress-store-locator'), $plural),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'exclude_from_search' => false,
            'show_ui' => true,
            'menu_position' => 57,
            'rewrite' => array(
                'slug' => 'store',
                'with_front' => FALSE
            ),
            'query_var' => 'stores',
            'supports' => array('title', 'editor', 'author', 'revisions', 'thumbnail'),
            'menu_icon' => 'dashicons-location-alt',
            // 'taxonomies' => array('post_tag'),
        );

        register_post_type('stores', $args);

    }

    /**
     * Register Store Categories and Store Filter Taxonomies.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @return bool
     */
    public function register_store_locator_taxonomy()
    {
    	// Store Category
        $singular = __('Store Category', 'wordpress-store-locator');
        $plural = __('Store Categories', 'wordpress-store-locator');

        $labels = array(
            'name' => sprintf(__('%s', 'wordpress-store-locator'), $plural),
            'singular_name' => sprintf(__('%s', 'wordpress-store-locator'), $singular),
            'search_items' => sprintf(__('Search %s', 'wordpress-store-locator'), $plural),
            'all_items' => sprintf(__('All %s', 'wordpress-store-locator'), $plural),
            'parent_item' => sprintf(__('Parent %s', 'wordpress-store-locator'), $singular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'wordpress-store-locator'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'wordpress-store-locator'), $singular),
            'update_item' => sprintf(__('Update %s', 'wordpress-store-locator'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'wordpress-store-locator'), $singular),
            'new_item_name' => sprintf(__('New %s Name', 'wordpress-store-locator'), $singular),
            'menu_name' => sprintf(__('%s', 'wordpress-store-locator'), $plural),
        );

        $args = array(
                'labels' => $labels,
                'public' => true,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array(
                    'slug' => 'store-categories',
                    'with_front' => FALSE
                ),
        );

        register_taxonomy('store_category', 'stores', $args);

        // Store Filter
        $singular = __('Store Filter', 'wordpress-store-locator');
        $plural = __('Store Filter', 'wordpress-store-locator');
        $labels = array(
            'name' => sprintf(__('%s', 'wordpress-store-locator'), $plural),
            'singular_name' => sprintf(__('%s', 'wordpress-store-locator'), $singular),
            'search_items' => sprintf(__('Search %s', 'wordpress-store-locator'), $plural),
            'all_items' => sprintf(__('All %s', 'wordpress-store-locator'), $plural),
            'parent_item' => sprintf(__('Parent %s', 'wordpress-store-locator'), $singular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'wordpress-store-locator'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'wordpress-store-locator'), $singular),
            'update_item' => sprintf(__('Update %s', 'wordpress-store-locator'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'wordpress-store-locator'), $singular),
            'new_item_name' => sprintf(__('New %s Name', 'wordpress-store-locator'), $singular),
            'menu_name' => sprintf(__('%s', 'wordpress-store-locator'), $plural),
        );

        $args = array(
                'labels' => $labels,
                'public' => false,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array('slug' => 'store-filter'),
        );

        register_taxonomy('store_filter', 'stores', $args);
    }

    /**
     * Add Custom Meta Fields to Store Categories and Filters.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @return bool
     */
    public function add_custom_meta_fields()
    {
        $custom_taxonomy_meta_config = array(
            'id' => 'stores_meta_box',
            'title' => 'Stores Meta Box',
            'pages' => array('store_category', 'store_filter'),
            'context' => 'side',
            'fields' => array(),
            'local_images' => false,
            'use_with_theme' => false,
        );

        $custom_taxonomy_meta_fields = new Tax_Meta_Class($custom_taxonomy_meta_config);
        // $custom_taxonomy_meta_fields->addImage($prefix.'image', array('name' => __('Map Icon ', 'wordpress-store-locator')));
        // No ID!
        // $custom_taxonomy_meta_fields->addTaxonomy($prefix.'product_category',array('taxonomy' => 'product_cat'),array('name'=> __('Link to Product Category ','wordpress-store-locator')));

        $options = array('' => 'Select Category');
        $categories = get_terms('product_cat');
        if(is_array($categories)) {
            foreach ($categories as $category) {
                $options[$category->term_id] = $category->name;
            }
            $custom_taxonomy_meta_fields->addSelect($this->prefix . 'product_category', $options, array('name' => __('Link to Product Category ', 'wordpress-store-locator')));
        }
        $custom_taxonomy_meta_fields->addImage($this->prefix . 'icon', array('name'=> __('Custom Icon ','wordpress-store-locator')));
        $custom_taxonomy_meta_fields->Finish();
    }

    /**
     * Columns Head.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @param string $columns Columnd
     *
     * @return string
     */
    public function columns_head($columns)
    {
        $output = array();
        foreach ($columns as $column => $name) {
            $output[$column] = $name;

            if ($column === 'title') {
                $output['address'] = __('Address', 'wordpress-store-locator');
                $output['contact'] = __('Contact', 'wordpress-store-locator');
                $output['coordinates'] = __('Coordinates', 'wordpress-store-locator');
            }
        }

        return $output;
    }

    /**
     * Columns Content.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @param string $column_name Column Name
     *
     * @return string
     */
    public function columns_content($column_name)
    {
        global $post;

        if ($column_name == 'address') {
            $address = array();
            $address['address1'] = get_post_meta($post->ID, 'wordpress_store_locator_address1', true);
            $address['address2'] = get_post_meta($post->ID, 'wordpress_store_locator_address2', true);
            $address['city'] = get_post_meta($post->ID, 'wordpress_store_locator_zip', true).', '.get_post_meta($post->ID, 'wordpress_store_locator_city', true);
            $address['country'] = get_post_meta($post->ID, 'wordpress_store_locator_region', true).', '.get_post_meta($post->ID, 'wordpress_store_locator_country', true);

            echo implode('<br/>', array_filter($address));
        }

        if ($column_name == 'contact') {
            $contact = array();
            $contact['telephone'] = __('Tel.:', 'wordpress-store-locator').' '.get_post_meta($post->ID, 'wordpress_store_locator_telephone', true);
            $contact['mobile'] = __('Mobile:', 'wordpress-store-locator').' '.get_post_meta($post->ID, 'wordpress_store_locator_mobile', true);
            $contact['email'] = __('Email:', 'wordpress-store-locator').' <a href="mailto'.get_post_meta($post->ID, 'wordpress_store_locator_email', true).'"> '.get_post_meta($post->ID, 'wordpress_store_locator_email', true).'</a>';
            $contact['website'] = __('Website:', 'wordpress-store-locator') .' <a href="'.get_post_meta($post->ID, 'wordpress_store_locator_website', true).'"> '.get_post_meta($post->ID, 'wordpress_store_locator_website', true).'</a>';

            echo implode('<br/>', array_filter($contact));
        }

        if ($column_name == 'coordinates') {
            $coordinates = array();
            $coordinates['lat'] = __('Lat:', 'wordpress-store-locator') . ' ' . get_post_meta($post->ID, 'wordpress_store_locator_lat', true);
            $coordinates['lng'] = __('Lng:', 'wordpress-store-locator') . ' ' . get_post_meta($post->ID, 'wordpress_store_locator_lng', true);

            echo implode('<br/>', array_filter($coordinates));
        }
    }

/**
     * Add custom ticket metaboxes
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $post_type [description]
     * @param   [type]                       $post      [description]
     */
    public function add_custom_metaboxes($post_type, $post)
    {
        add_meta_box('wordpress-store-locator-address', 'Address', array($this, 'address'), 'stores', 'normal', 'high');
        add_meta_box('wordpress-store-locator-contact', 'Contact Information', array($this, 'contact'), 'stores', 'normal', 'high');
        add_meta_box('wordpress-store-locator-additional', 'Additional', array($this, 'additional'), 'stores', 'normal', 'high');
        add_meta_box('wordpress-store-locator-opening', 'Opening Hours', array($this, 'opening'), 'stores', 'normal', 'high');
    }

    /**
     * Display Metabox Address
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function address()
    {
        global $post, $wordpress_store_locator_options;

        wp_nonce_field(basename(__FILE__), 'wordpress_store_locator_meta_nonce');

        if($this->is_new_store()) {
            $address1 = $wordpress_store_locator_options['defaultAddress1'];
            $address2 = $wordpress_store_locator_options['defaultAddress2'];
            $zip = $wordpress_store_locator_options['defaultZIP'];
            $city = $wordpress_store_locator_options['defaultCity'];
            $region = $wordpress_store_locator_options['defaultRegion'];
            $lat = '';
            $lng = '';

            $country = $wordpress_store_locator_options['defaultCountry'];

        } else {
            $address1 = get_post_meta($post->ID, $this->prefix . 'address1', true);
            $address2 = get_post_meta($post->ID, $this->prefix . 'address2', true);
            $zip = get_post_meta($post->ID, $this->prefix . 'zip', true);
            $city = get_post_meta($post->ID, $this->prefix . 'city', true);
            $region = get_post_meta($post->ID, $this->prefix . 'region', true);
            $country = get_post_meta($post->ID, $this->prefix . 'country', true);
            $lat = get_post_meta($post->ID, $this->prefix . 'lat', true);
            $lng = get_post_meta($post->ID, $this->prefix . 'lng', true);
        }

        echo '<div class="wordpress-store-locator-container">';
            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'address1">' . __( 'Address Line 1', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'address1" value="' . $address1 . '" type="text">';
                echo '</div>';
            
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'address2">' . __( 'Address Line 1', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'address2" value="' . $address2 . '" type="text">';
                echo '</div>';
            echo '</div>';

            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'zip">' . __( 'ZIP', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'zip" value="' . $zip . '" type="text">';
                echo '</div>';
            
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'city">' . __( 'City', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'city" value="' . $city . '" type="text">';
                echo '</div>';
            echo '</div>';

            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'region">' . __( 'State / Province / Region', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'region" value="' . $region . '" type="text">';
                echo '</div>';
            
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'country">' . __( 'Country', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<select name="' . $this->prefix . 'country" class="wordpress-store-locator-input-field">';
                    $countries = $this->get_countries();
                    foreach ($countries as $code => $countryName) {
                        $selected = "";
                        if($country == $code) {
                            $selected = 'selected="selected"';
                        }
                        echo '<option value="' . $code . '" ' . $selected . '>' . $countryName . '</option>';
                    }
                    echo '</select>';
                echo '</div>';
            echo '</div>';

            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-12">';
                    echo '<a href="#" id="wordpress-store-locator-get-position" class="btn button">Get Position</a>';
                    echo '<div class="wordpress-store-locator-map" data-lat="' . $lat . '" data-lng="' . $lng . '">';
                        echo '<div id="wordpress-store-locator-map-container"></div>';
                    echo '</div>';
                echo '</div>';    
            echo '</div>';
            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'lat">' . __( 'Latitude', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input id="wordpress-store-locator-lat" class="wordpress-store-locator-input-field" name="' . $this->prefix . 'lat" value="' . $lat . '" type="text">';
                echo '</div>';
            
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'lng">' . __( 'Longitude', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input id="wordpress-store-locator-lng" class="wordpress-store-locator-input-field" name="' . $this->prefix . 'lng" value="' . $lng . '" type="text">';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    /**
     * Display Metabox Address
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function contact()
    {
        global $post, $wordpress_store_locator_options;

        wp_nonce_field(basename(__FILE__), 'wordpress_store_locator_meta_nonce');

        if($this->is_new_store()) {
            $telephone = $wordpress_store_locator_options['defaultTelephone'];
            $mobile = $wordpress_store_locator_options['defaultMobile'];
            $fax = $wordpress_store_locator_options['defaultFax'];
            $email = $wordpress_store_locator_options['defaultEmail'];
            $website = $wordpress_store_locator_options['defaultWebsite'];
        } else {
            $telephone = get_post_meta($post->ID, $this->prefix . 'telephone', true);
            $mobile = get_post_meta($post->ID, $this->prefix . 'mobile', true);
            $fax = get_post_meta($post->ID, $this->prefix . 'fax', true);
            $email = get_post_meta($post->ID, $this->prefix . 'email', true);
            $website = get_post_meta($post->ID, $this->prefix . 'website', true);
        }

        echo '<div class="wordpress-store-locator-container">';
            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'telephone">' . __( 'Telephone', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'telephone" value="' . $telephone . '" type="text">';
                echo '</div>';
            
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'mobile">' . __( 'Mobile', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'mobile" value="' . $mobile . '" type="text">';
                echo '</div>';
            echo '</div>';

            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'fax">' . __( 'Fax', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'fax" value="' . $fax . '" type="text">';
                echo '</div>';
            
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'email">' . __( 'Email', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'email" value="' . $email . '" type="text">';
                echo '</div>';
            echo '</div>';

            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . 'website">' . __( 'Website', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'website" value="' . $website . '" type="text">';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    /**
     * Display Metabox Address
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function additional()
    {
        global $post;

        $premium = get_post_meta($post->ID, $this->prefix . 'premium', true) == "1" ? 'checked="checked"' : '';
        $icon = get_post_meta($post->ID, $this->prefix . 'icon', true);

        echo '<div class="wordpress-store-locator-container">';
            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-12">';
                    echo '<label for="' . $this->prefix . 'premium">' . __( 'Premium Store', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'premium" value="1" ' . $premium . ' type="checkbox">';
                echo '</div>';
            echo '</div>';
            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-12">';
                    echo '<label for="' . $this->prefix . 'icon">' . __( 'Custom Icon (URL)', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . 'icon" value="' . $icon . '" type="url">';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    /**
     * Display Metabox Address
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function opening()
    {
        global $post, $wordpress_store_locator_options;

        $weekdays = array(
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        );

        echo '<div class="wordpress-store-locator-container">';
        $openingHours = array();
        foreach ($weekdays as $weekday) {
            $open = "";
            $close = "";

            if($this->is_new_store() && ($weekday != "Saturday" && $weekday != "Sunday")) {
                $open = $wordpress_store_locator_options['defaultOpen'];
                $close = $wordpress_store_locator_options['defaultClose'];
            } else {
                $open = get_post_meta($post->ID, $this->prefix . $weekday . '_open', true);
                $close = get_post_meta($post->ID, $this->prefix . $weekday . '_close', true);
            }

            echo '<div class="wordpress-store-locator-row">';
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . $weekday . '_open">' . __( $weekday . ' (open)', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . $weekday . '_open" value="' . $open .'" type="text">';
                echo '</div>';
                echo '<div class="wordpress-store-locator-col-sm-6">';
                    echo '<label for="' . $this->prefix . $weekday . '_close">' . __( $weekday . ' (close)', 'wordpress-store-locator' ) . '</label><br/>';
                    echo '<input class="wordpress-store-locator-input-field" name="' . $this->prefix . $weekday . '_close" value="' . $close .'" type="text">';
                echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    /**
     * Save Custom Metaboxes
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $post_id [description]
     * @param   [type]                       $post    [description]
     * @return  [type]                                [description]
     */
    public function save_custom_metaboxes($post_id, $post)
    {
        global $wordpress_store_locator_options;

        if($post->post_type !== "stores") {
            return false;
        }

        // Is the user allowed to edit the post or page?
        if (!current_user_can('edit_post', $post->ID)) {
            return $post->ID;
        }

        if ($post->post_type == 'revision') {
            return false;
        }

        if (!isset($_POST['wordpress_store_locator_meta_nonce']) || !wp_verify_nonce($_POST['wordpress_store_locator_meta_nonce'], basename(__FILE__))) {
            return false;
        }

        $possible_inputs = array(
            'wordpress_store_locator_meta_nonce',
            'wordpress_store_locator_address1',
            'wordpress_store_locator_address2',
            'wordpress_store_locator_zip',
            'wordpress_store_locator_city',
            'wordpress_store_locator_region',
            'wordpress_store_locator_country',
            'wordpress_store_locator_lat',
            'wordpress_store_locator_lng',
            'wordpress_store_locator_meta_nonce',
            'wordpress_store_locator_telephone',
            'wordpress_store_locator_mobile',
            'wordpress_store_locator_fax',
            'wordpress_store_locator_email',
            'wordpress_store_locator_website',
            'wordpress_store_locator_premium',
            'wordpress_store_locator_icon',
            'wordpress_store_locator_Monday_open',
            'wordpress_store_locator_Monday_close',
            'wordpress_store_locator_Tuesday_open',
            'wordpress_store_locator_Tuesday_close',
            'wordpress_store_locator_Wednesday_open',
            'wordpress_store_locator_Wednesday_close',
            'wordpress_store_locator_Thursday_open',
            'wordpress_store_locator_Thursday_close',
            'wordpress_store_locator_Friday_open',
            'wordpress_store_locator_Friday_close',
            'wordpress_store_locator_Saturday_open',
            'wordpress_store_locator_Saturday_close',
            'wordpress_store_locator_Sunday_open',
            'wordpress_store_locator_Sunday_close',
        );

        // Add values of $ticket_meta as custom fields
        foreach ($possible_inputs as $possible_input) {
            $val = isset($_POST[$possible_input]) ? $_POST[$possible_input] : '';
            update_post_meta($post->ID, $possible_input, $val);
        }
    }

    private function is_new_store()
    {
        global $pagenow;

        if (!is_admin()) return false;

        return in_array( $pagenow, array( 'post-new.php' ) );
    }

    private function get_countries()
    {
        $countries = array( "AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "BQ" => "British Antarctic Territory", "IO" => "British Indian Ocean Territory", "VG" => "British Virgin Islands", "BN" => "Brunei", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CT" => "Canton and Enderbury Islands", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos [Keeling] Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo - Brazzaville", "CD" => "Congo - Kinshasa", "CK" => "Cook Islands", "CR" => "Costa Rica", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "CI" => "Côte d’Ivoire", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "NQ" => "Dronning Maud Land", "DD" => "East Germany", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "FQ" => "French Southern and Antarctic Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and McDonald Islands", "HN" => "Honduras", "HK" => "Hong Kong SAR China", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle of Man", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JT" => "Johnston Island", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Laos", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macau SAR China", "MK" => "Macedonia", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "FX" => "Metropolitan France", "MX" => "Mexico", "FM" => "Micronesia", "MI" => "Midway Islands", "MD" => "Moldova", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar [Burma]", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NT" => "Neutral Zone", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "KP" => "North Korea", "VD" => "North Vietnam", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PC" => "Pacific Islands Trust Territory", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territories", "PA" => "Panama", "PZ" => "Panama Canal Zone", "PG" => "Papua New Guinea", "PY" => "Paraguay", "YD" => "People's Democratic Republic of Yemen", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn Islands", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RO" => "Romania", "RU" => "Russia", "RW" => "Rwanda", "RE" => "Réunion", "BL" => "Saint Barthélemy", "SH" => "Saint Helena", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "CS" => "Serbia and Montenegro", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "KR" => "South Korea", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syria", "ST" => "São Tomé and Príncipe", "TW" => "Taiwan", "TJ" => "Tajikistan", "TZ" => "Tanzania", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UM" => "U.S. Minor Outlying Islands", "PU" => "U.S. Miscellaneous Pacific Islands", "VI" => "U.S. Virgin Islands", "UG" => "Uganda", "UA" => "Ukraine", "SU" => "Union of Soviet Socialist Republics", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "ZZ" => "Unknown or Invalid Region", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VA" => "Vatican City", "VE" => "Venezuela", "VN" => "Vietnam", "WK" => "Wake Island", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe", "AX" => "Åland Islands" );

        return $countries;
    }
}
