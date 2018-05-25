<?php

class WordPress_Store_Locator_Public_Ajax
{

    private $plugin_name;
    private $version;
    private $options;

    /**
     * Store Locator Ajax Class 
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
     * Get Stores and echo json encoded Data
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  JSON    The Stores
     */
    public function get_stores()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            die('No AJAX call!');
        }

        if (!isset($_POST['lat']) || !isset($_POST['lng']) || !isset($_POST['radius'])) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            die('No Lat, Lng or Radius');
        }

        $lat = floatval($_POST['lat']);
        $lng = floatval($_POST['lng']);
        $radius = absint($_POST['radius']);
        if (!is_float($lat) || !is_float($lng) || !absint($radius)) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            die('Not a correct value for Lat, Lng or Radius!');
        }

        $stores = $this->query_stores($lat, $lng, $radius);
        echo json_encode($stores, JSON_FORCE_OBJECT);
        die();
    }

    /**
     * The Database query for getting the right Stores
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  array Stores
     */
    public function query_stores($lat, $lng, $radius)
    {
        global $wpdb, $wordpress_store_locator_options;

        $store_data = array();

        $distanceUnit = ($wordpress_store_locator_options['mapDistanceUnit'] == 'km') ? 6371 : 3959;

        if (!$radius || empty($radius) || $radius > 3999) {
            $radius = $wordpress_store_locator_options['mapRadius'];
        }

        $resultListMax = $wordpress_store_locator_options['resultListMax'];
        $sorting = $wordpress_store_locator_options['resultListOrder'];

        // Filtering
        if (isset($_POST['categories']) || isset($_POST['filter'])) {
            $filter = '';

            if (!empty($_POST['categories'][0])) {
                $categories_ids = array_map('absint', $_POST['categories']);
                $filter = $filter."
                        INNER JOIN $wpdb->term_relationships AS term_rel ON posts.ID = term_rel.object_id
                        INNER JOIN $wpdb->term_taxonomy AS term_tax ON term_rel.term_taxonomy_id = term_tax.term_taxonomy_id 
                        AND term_tax.taxonomy = 'store_category'
                        AND term_tax.term_id IN (".implode(',', $categories_ids).')';
            }

            if (!empty($_POST['filter'])) {
                $filter_ids = array_map('absint', $_POST['filter']);
                $c = 1;
                foreach ($filter_ids as $filter_id) {
                    $filter = $filter." 
                           INNER JOIN $wpdb->term_relationships AS term_rel".$c.' ON posts.ID = term_rel'.$c.".object_id
                           INNER JOIN $wpdb->term_taxonomy AS term_tax".$c.' ON term_rel'.$c.'.term_taxonomy_id = term_tax'.$c.'.term_taxonomy_id 
                           AND term_tax'.$c.".taxonomy = 'store_filter'
                            AND term_tax".$c.'.term_id = '.$filter_id;
                    ++$c;
                }
                // OR-Operator!
                // $filter = $filter . " 
                //        INNER JOIN $wpdb->term_relationships AS term_rel2 ON posts.ID = term_rel2.object_id
                //        INNER JOIN $wpdb->term_taxonomy AS term_tax2 ON term_rel2.term_taxonomy_id = term_tax2.term_taxonomy_id 
                //        AND term_tax2.taxonomy = 'store_filter'
                //        AND term_tax2.term_id = " . implode( ', ', $filter_ids );
            }
        } else {
            $filter = '';
        }

        if($sorting == "premium") {
            $sql = "SELECT 
                    posts.ID,
                    posts.post_title as na,
                    posts.guid as gu,
                    posts.post_content as de,
                    post_lat.meta_value AS lat,
                    post_lng.meta_value AS lng,
                    post_premium.meta_value AS premium,
                    ( %d * acos( cos( radians( %s ) ) * cos( radians( post_lat.meta_value ) ) * cos( radians( post_lng.meta_value ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( post_lat.meta_value ) ) ) )
                        AS distance
                    FROM $wpdb->posts AS posts
                    INNER JOIN $wpdb->postmeta AS post_lat ON post_lat.post_id = posts.ID AND post_lat.meta_key = 'wordpress_store_locator_lat'
                    INNER JOIN $wpdb->postmeta AS post_lng ON post_lng.post_id = posts.ID AND post_lng.meta_key = 'wordpress_store_locator_lng'
                    INNER JOIN $wpdb->postmeta AS post_premium ON post_premium.post_id = posts.ID AND post_premium.meta_key = 'wordpress_store_locator_premium'
                    $filter
                    WHERE posts.post_type = 'stores'
                    AND posts.post_status = 'publish'
                    GROUP BY lat  
                    HAVING distance < %d ORDER BY premium DESC LIMIT 0, %d";
        } else {
            $sql = "SELECT 
            			posts.ID,
            			posts.post_title as na,
                        posts.guid as gu,
            			posts.post_content as de,
    					post_lat.meta_value AS lat,
                       	post_lng.meta_value AS lng,
                       	( %d * acos( cos( radians( %s ) ) * cos( radians( post_lat.meta_value ) ) * cos( radians( post_lng.meta_value ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( post_lat.meta_value ) ) ) )
                    	AS distance
                  		FROM $wpdb->posts AS posts
    		            INNER JOIN $wpdb->postmeta AS post_lat ON post_lat.post_id = posts.ID AND post_lat.meta_key = 'wordpress_store_locator_lat'
    		            INNER JOIN $wpdb->postmeta AS post_lng ON post_lng.post_id = posts.ID AND post_lng.meta_key = 'wordpress_store_locator_lng'
                        $filter
                 		WHERE posts.post_type = 'stores'
                   		AND posts.post_status = 'publish'
                   		GROUP BY lat 
                       	HAVING distance < %d ORDER BY " . $sorting . " LIMIT 0, %d";
        }

        $values = array(
            $distanceUnit,
            $lat,
            $lng,
            $lat,
            $radius,
            $resultListMax,
        );

        $stores = $wpdb->get_results($wpdb->prepare($sql, $values));

        if ($stores) {
            $store_data = apply_filters('wordpress_store_locator_stores', $this->get_meta_data($stores));
        }

        return $store_data;
    }

    /**
     * Get the Stores Metadata
     * Also remove not needed Data and minify the data for the AJAX transfer
     * 
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   array $stores Stores
     * @return  array Stores with Meta Data
     */
    public function get_meta_data($stores)
    {
        global $wpdb, $wordpress_store_locator_options;

        $prefix = 'wordpress_store_locator_';

        foreach ($stores as $store_key => $store) {

            // Get the post meta data
            $store_metas = get_post_meta($store->ID);

            // Meta Data
            if ($wordpress_store_locator_options['showStreet']) {
                $store->st = '';
                if(isset($store_metas["{$prefix}address1"][0])) {
                    $store->st .= $store_metas["{$prefix}address1"][0];
                }
                if(isset($store_metas["{$prefix}address2"][0])) {
                    $store->st .= ' '. $store_metas["{$prefix}address2"][0];
                }
            }
            if ($wordpress_store_locator_options['showZip']) {
                $store->zp = '';
                if(isset($store_metas["{$prefix}zip"][0])) {
                    $store->zp = $store_metas["{$prefix}zip"][0];
                }
            }
            if ($wordpress_store_locator_options['showCity']) {
                $store->ct = '';
                if(isset($store_metas["{$prefix}city"][0])) {
                    $store->ct = ' '. $store_metas["{$prefix}city"][0];
                }
            }
            if ($wordpress_store_locator_options['showCountry']) {
                $store->co = '';
                if(isset($store_metas["{$prefix}country"][0])) {
                    $store->co = ' '. $store_metas["{$prefix}country"][0];
                }
            }
            if ($wordpress_store_locator_options['showRegion']) {
                $store->rg = '';
                if(isset($store_metas["{$prefix}region"][0])) {
                    $store->rg = $store_metas["{$prefix}region"][0];
                }
            }

            if (($wordpress_store_locator_options['showTelephone'] || $wordpress_store_locator_options['showCallNow'] || $wordpress_store_locator_options['resultListLinkAction'] == "tel" || $wordpress_store_locator_options['infowindowLinkAction'] == "tel") && isset($store_metas["{$prefix}telephone"][0])) {
                $store->te = $store_metas["{$prefix}telephone"][0];
            }

            if (($wordpress_store_locator_options['showMobile']) && isset($store_metas["{$prefix}mobile"][0])) {
                $store->mo = $store_metas["{$prefix}mobile"][0];
            }

            if (($wordpress_store_locator_options['showFax']) && isset($store_metas["{$prefix}fax"][0])) {
                $store->fa = $store_metas["{$prefix}fax"][0];
            }

            if (($wordpress_store_locator_options['showDistance']) && isset($store->distance)) {
                $store->dc = substr($store->distance, 0, 4) . ' ' . $wordpress_store_locator_options['mapDistanceUnit'];
            }

            $store->ic = "";
            $args = array('fields' => 'ids', 'orderby' => 'name', 'order' => 'ASC');
            $category_ids = wp_get_object_terms($store->ID, 'store_category', $args);
            if(!empty($category_ids) ){
                $category_icon = get_term_meta($category_ids[0], 'wordpress_store_locator_icon');
                if(isset($category_icon[0]) && !empty($category_icon[0]['url'])) {
                    $store->ic = $category_icon[0]['url'];
                }
            }

            if(isset($store_metas["{$prefix}icon"][0]) ) {
                $store_icon = $store_metas["{$prefix}icon"][0];
                if(isset($store_icon) && !empty($store_icon)) {
                    $store->ic = $store_icon;
                }
            }

            if (($wordpress_store_locator_options['showEmail'] || $wordpress_store_locator_options['showWriteEmail'] || $wordpress_store_locator_options['resultListLinkAction'] == "email" || $wordpress_store_locator_options['infowindowLinkAction'] == "email") && isset($store_metas["{$prefix}email"][0])) {
                $store->em = $store_metas["{$prefix}email"][0];
            }
            if (($wordpress_store_locator_options['showWebsite'] || $wordpress_store_locator_options['showVisitWebsite'] || $wordpress_store_locator_options['resultListLinkAction'] == "web" || $wordpress_store_locator_options['infowindowLinkAction']) && isset($store_metas["{$prefix}website"][0])) {
                $store->we = $store_metas["{$prefix}website"][0];
            }
            if ($wordpress_store_locator_options['resultListPremiumIconEnabled'] && isset($store_metas["{$prefix}premium"][0])) {
                $store->pr = $store_metas["{$prefix}premium"][0];
            }
            if ($wordpress_store_locator_options['showStoreFilter']) {
                $args = array('fields' => 'names', 'orderby' => 'name', 'order' => 'ASC');
                $store->fi = wp_get_object_terms($store->ID, 'store_filter', $args);
            }
            if ($wordpress_store_locator_options['showStoreCategories']) {
                $args = array('fields' => 'names', 'orderby' => 'name', 'order' => 'ASC');
                $store->ca = wp_get_object_terms($store->ID, 'store_category', $args);
            }

            if ($wordpress_store_locator_options['showOpeningHours']) {
                $weekdays = array(
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                    'Sunday',
                );
                $c = 0;
                foreach ($weekdays as $weekday) {
                    $store->op[$c++] = isset($store_metas["{$prefix}{$weekday}_open"]) ? $store_metas["{$prefix}{$weekday}_open"][0] : '';
                    $store->op[$c++] = isset($store_metas["{$prefix}{$weekday}_close"]) ? $store_metas["{$prefix}{$weekday}_close"][0] : '';
                }
            }
            // Unset not shown posts fields
            if (!$wordpress_store_locator_options['showName']) {
                unset($store->na);
            }
            if (!$wordpress_store_locator_options['showDescription']) {
                unset($store->de);
            }
            if ($wordpress_store_locator_options['showImage']) {
                $imageURL = $this->get_thumb($store_metas['_thumbnail_id'][0]);

                if(!empty($imageURL)) {
                    $store->im = $imageURL;
                }
            }
        }

        return $stores;
    }

    /**
     * Get Image Thumb
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   int                         $image_id Image ID
     * @return  string URL of image
     */
    public function get_thumb($image_id)
    {
        global $wordpress_store_locator_options;

        $width = substr($wordpress_store_locator_options['imageDimensions']['width'], 0, -2);
        $height = substr($wordpress_store_locator_options['imageDimensions']['height'], 0, -2);

        $image = wp_get_attachment_image_src($image_id, array($width, $height));

        return $image[0];
    }

   /**
     * Get Stores and echo json encoded Data
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  JSON    The Stores
     */
    public function get_all_stores()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            die('No AJAX call!');
        }

        $lat = floatval($_POST['lat']);
        $lng = floatval($_POST['lng']);

        $stores = $this->query_all_stores($lat, $lng);
        echo json_encode($stores, JSON_FORCE_OBJECT);
        die();
    }

    /**
     * The Database query for getting the right Stores
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  array Stores
     */
    public function query_all_stores($lat, $lng)
    {
        global $wpdb, $wordpress_store_locator_options;

        $store_data = array();

        $distanceUnit = ($wordpress_store_locator_options['mapDistanceUnit'] == 'km') ? 6371 : 3959;

        $sorting = $wordpress_store_locator_options['resultListOrder'];

        if(!empty($lat) && !empty($lng)) {
            if($sorting == "premium") {
                $sql = "SELECT 
                        posts.ID,
                        posts.post_title as na,
                        posts.guid as gu,
                        posts.post_content as de,
                        post_lat.meta_value AS lat,
                        post_lng.meta_value AS lng,
                        post_premium.meta_value AS premium,
                        ( %d * acos( cos( radians( %s ) ) * cos( radians( post_lat.meta_value ) ) * cos( radians( post_lng.meta_value ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( post_lat.meta_value ) ) ) )
                            AS distance
                        FROM $wpdb->posts AS posts
                        INNER JOIN $wpdb->postmeta AS post_lat ON post_lat.post_id = posts.ID AND post_lat.meta_key = 'wordpress_store_locator_lat'
                        INNER JOIN $wpdb->postmeta AS post_lng ON post_lng.post_id = posts.ID AND post_lng.meta_key = 'wordpress_store_locator_lng'
                        INNER JOIN $wpdb->postmeta AS post_premium ON post_premium.post_id = posts.ID AND post_premium.meta_key = 'wordpress_store_locator_premium'
                        $filter
                        WHERE posts.post_type = 'stores'
                        AND posts.post_status = 'publish'
                        GROUP BY lat  
                        HAVING distance < %d ORDER BY premium";
            } else {
                $sql = "SELECT 
                        posts.ID,
                        posts.post_title as na,
                        posts.guid as gu,
                        posts.post_content as de,
                        post_lat.meta_value AS lat,
                        post_lng.meta_value AS lng,
                        ( %d * acos( cos( radians( %s ) ) * cos( radians( post_lat.meta_value ) ) * cos( radians( post_lng.meta_value ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( post_lat.meta_value ) ) ) )
                        AS distance
                        FROM $wpdb->posts AS posts
                        INNER JOIN $wpdb->postmeta AS post_lat ON post_lat.post_id = posts.ID AND post_lat.meta_key = 'wordpress_store_locator_lat'
                        INNER JOIN $wpdb->postmeta AS post_lng ON post_lng.post_id = posts.ID AND post_lng.meta_key = 'wordpress_store_locator_lng'
                        WHERE posts.post_type = 'stores'
                        AND posts.post_status = 'publish'
                        GROUP BY lat 
                        HAVING distance < %d ORDER BY " . $sorting;
            }

            $values = array(
                $distanceUnit, 
                $lat, 
                $lng, 
                $lat, 
                99999, 
            );

            $stores = $wpdb->get_results($wpdb->prepare($sql, $values));
        } else {
            $sql = "SELECT 
                        posts.ID,
                        posts.post_title as na,
                        posts.guid as gu,
                        posts.post_content as de,
                        post_lat.meta_value AS lat,
                        post_lng.meta_value AS lng
                        FROM $wpdb->posts AS posts
                        INNER JOIN $wpdb->postmeta AS post_lat ON post_lat.post_id = posts.ID AND post_lat.meta_key = 'wordpress_store_locator_lat'
                        INNER JOIN $wpdb->postmeta AS post_lng ON post_lng.post_id = posts.ID AND post_lng.meta_key = 'wordpress_store_locator_lng'
                        WHERE posts.post_type = 'stores'
                        AND posts.post_status = 'publish'
                        GROUP BY lat 
                        ORDER BY posts.post_title";

            $stores = $wpdb->get_results($wpdb->prepare($sql, $values));
        }

        if ($stores) {
            $store_data = apply_filters('wordpress_store_locator_all_stores', $this->get_meta_data($stores));
        }

        return $store_data;
    }
}