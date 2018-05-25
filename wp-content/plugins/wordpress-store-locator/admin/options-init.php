<?php

    global $wpdb;
    /**
     * For full documentation, please visit: http://docs.reduxframework.com/
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    $icons = array( "fa-glass", "fa-music", "fa-search", "fa-envelope-o", "fa-heart", "fa-star", "fa-star-o", "fa-user", "fa-film", "fa-th-large", "fa-th", "fa-th-list", "fa-check", "fa-remove", "fa-close", "fa-times", "fa-search-plus", "fa-search-minus", "fa-power-off", "fa-signal", "fa-gear", "fa-cog", "fa-trash-o", "fa-home", "fa-file-o", "fa-clock-o", "fa-road", "fa-download", "fa-arrow-circle-o-down", "fa-arrow-circle-o-up", "fa-inbox", "fa-play-circle-o", "fa-rotate-right", "fa-repeat", "fa-refresh", "fa-list-alt", "fa-lock", "fa-flag", "fa-headphones", "fa-volume-off", "fa-volume-down", "fa-volume-up", "fa-qrcode", "fa-barcode", "fa-tag", "fa-tags", "fa-book", "fa-bookmark", "fa-print", "fa-camera", "fa-font", "fa-bold", "fa-italic", "fa-text-height", "fa-text-width", "fa-align-left", "fa-align-center", "fa-align-right", "fa-align-justify", "fa-list", "fa-dedent", "fa-outdent", "fa-indent", "fa-video-camera", "fa-photo", "fa-image", "fa-picture-o", "fa-pencil", "fa-map-marker", "fa-adjust", "fa-tint", "fa-edit", "fa-pencil-square-o", "fa-share-square-o", "fa-check-square-o", "fa-arrows", "fa-step-backward", "fa-fast-backward", "fa-backward", "fa-play", "fa-pause", "fa-stop", "fa-forward", "fa-fast-forward", "fa-step-forward", "fa-eject", "fa-chevron-left", "fa-chevron-right", "fa-plus-circle", "fa-minus-circle", "fa-times-circle", "fa-check-circle", "fa-question-circle", "fa-info-circle", "fa-crosshairs", "fa-times-circle-o", "fa-check-circle-o", "fa-ban", "fa-arrow-left", "fa-arrow-right", "fa-arrow-up", "fa-arrow-down", "fa-mail-forward", "fa-share", "fa-expand", "fa-compress", "fa-plus", "fa-minus", "fa-asterisk", "fa-exclamation-circle", "fa-gift", "fa-leaf", "fa-fire", "fa-eye", "fa-eye-slash", "fa-warning", "fa-exclamation-triangle", "fa-plane", "fa-calendar", "fa-random", "fa-comment", "fa-magnet", "fa-chevron-up", "fa-chevron-down", "fa-retweet", "fa-shopping-cart", "fa-folder", "fa-folder-open", "fa-arrows-v", "fa-arrows-h", "fa-bar-chart-o", "fa-bar-chart", "fa-twitter-square", "fa-facebook-square", "fa-camera-retro", "fa-key", "fa-gears", "fa-cogs", "fa-comments", "fa-thumbs-o-up", "fa-thumbs-o-down", "fa-star-half", "fa-heart-o", "fa-sign-out", "fa-linkedin-square", "fa-thumb-tack", "fa-external-link", "fa-sign-in", "fa-trophy", "fa-github-square", "fa-upload", "fa-lemon-o", "fa-phone", "fa-square-o", "fa-bookmark-o", "fa-phone-square", "fa-twitter", "fa-facebook-f", "fa-facebook", "fa-github", "fa-unlock", "fa-credit-card", "fa-feed", "fa-rss", "fa-hdd-o", "fa-bullhorn", "fa-bell", "fa-certificate", "fa-hand-o-right", "fa-hand-o-left", "fa-hand-o-up", "fa-hand-o-down", "fa-arrow-circle-left", "fa-arrow-circle-right", "fa-arrow-circle-up", "fa-arrow-circle-down", "fa-globe", "fa-wrench", "fa-tasks", "fa-filter", "fa-briefcase", "fa-arrows-alt", "fa-group", "fa-users", "fa-chain", "fa-link", "fa-cloud", "fa-flask", "fa-cut", "fa-scissors", "fa-copy", "fa-files-o", "fa-paperclip", "fa-save", "fa-floppy-o", "fa-square", "fa-navicon", "fa-reorder", "fa-bars", "fa-list-ul", "fa-list-ol", "fa-strikethrough", "fa-underline", "fa-table", "fa-magic", "fa-truck", "fa-pinterest", "fa-pinterest-square", "fa-google-plus-square", "fa-google-plus", "fa-money", "fa-caret-down", "fa-caret-up", "fa-caret-left", "fa-caret-right", "fa-columns", "fa-unsorted", "fa-sort", "fa-sort-down", "fa-sort-desc", "fa-sort-up", "fa-sort-asc", "fa-envelope", "fa-linkedin", "fa-rotate-left", "fa-undo", "fa-legal", "fa-gavel", "fa-dashboard", "fa-tachometer", "fa-comment-o", "fa-comments-o", "fa-flash", "fa-bolt", "fa-sitemap", "fa-umbrella", "fa-paste", "fa-clipboard", "fa-lightbulb-o", "fa-exchange", "fa-cloud-download", "fa-cloud-upload", "fa-user-md", "fa-stethoscope", "fa-suitcase", "fa-bell-o", "fa-coffee", "fa-cutlery", "fa-file-text-o", "fa-building-o", "fa-hospital-o", "fa-ambulance", "fa-medkit", "fa-fighter-jet", "fa-beer", "fa-h-square", "fa-plus-square", "fa-angle-double-left", "fa-angle-double-right", "fa-angle-double-up", "fa-angle-double-down", "fa-angle-left", "fa-angle-right", "fa-angle-up", "fa-angle-down", "fa-desktop", "fa-laptop", "fa-tablet", "fa-mobile-phone", "fa-mobile", "fa-circle-o", "fa-quote-left", "fa-quote-right", "fa-spinner", "fa-circle", "fa-mail-reply", "fa-reply", "fa-github-alt", "fa-folder-o", "fa-folder-open-o", "fa-smile-o", "fa-frown-o", "fa-meh-o", "fa-gamepad", "fa-keyboard-o", "fa-flag-o", "fa-flag-checkered", "fa-terminal", "fa-code", "fa-mail-reply-all", "fa-reply-all", "fa-star-half-empty", "fa-star-half-full", "fa-star-half-o", "fa-location-arrow", "fa-crop", "fa-code-fork", "fa-unlink", "fa-chain-broken", "fa-question", "fa-info", "fa-exclamation", "fa-superscript", "fa-subscript", "fa-eraser", "fa-puzzle-piece", "fa-microphone", "fa-microphone-slash", "fa-shield", "fa-calendar-o", "fa-fire-extinguisher", "fa-rocket", "fa-maxcdn", "fa-chevron-circle-left", "fa-chevron-circle-right", "fa-chevron-circle-up", "fa-chevron-circle-down", "fa-html5", "fa-css3", "fa-anchor", "fa-unlock-alt", "fa-bullseye", "fa-ellipsis-h", "fa-ellipsis-v", "fa-rss-square", "fa-play-circle", "fa-ticket", "fa-minus-square", "fa-minus-square-o", "fa-level-up", "fa-level-down", "fa-check-square", "fa-pencil-square", "fa-external-link-square", "fa-share-square", "fa-compass", "fa-toggle-down", "fa-caret-square-o-down", "fa-toggle-up", "fa-caret-square-o-up", "fa-toggle-right", "fa-caret-square-o-right", "fa-euro", "fa-eur", "fa-gbp", "fa-dollar", "fa-usd", "fa-rupee", "fa-inr", "fa-cny", "fa-rmb", "fa-yen", "fa-jpy", "fa-ruble", "fa-rouble", "fa-rub", "fa-won", "fa-krw", "fa-bitcoin", "fa-btc", "fa-file", "fa-file-text", "fa-sort-alpha-asc", "fa-sort-alpha-desc", "fa-sort-amount-asc", "fa-sort-amount-desc", "fa-sort-numeric-asc", "fa-sort-numeric-desc", "fa-thumbs-up", "fa-thumbs-down", "fa-youtube-square", "fa-youtube", "fa-xing", "fa-xing-square", "fa-youtube-play", "fa-dropbox", "fa-stack-overflow", "fa-instagram", "fa-flickr", "fa-adn", "fa-bitbucket", "fa-bitbucket-square", "fa-tumblr", "fa-tumblr-square", "fa-long-arrow-down", "fa-long-arrow-up", "fa-long-arrow-left", "fa-long-arrow-right", "fa-apple", "fa-windows", "fa-android", "fa-linux", "fa-dribbble", "fa-skype", "fa-foursquare", "fa-trello", "fa-female", "fa-male", "fa-gittip", "fa-gratipay", "fa-sun-o", "fa-moon-o", "fa-archive", "fa-bug", "fa-vk", "fa-weibo", "fa-renren", "fa-pagelines", "fa-stack-exchange", "fa-arrow-circle-o-right", "fa-arrow-circle-o-left", "fa-toggle-left", "fa-caret-square-o-left", "fa-dot-circle-o", "fa-wheelchair", "fa-vimeo-square", "fa-turkish-lira", "fa-try", "fa-plus-square-o", "fa-space-shuttle", "fa-slack", "fa-envelope-square", "fa-wordpress", "fa-openid", "fa-institution", "fa-bank", "fa-university", "fa-mortar-board", "fa-graduation-cap", "fa-yahoo", "fa-google", "fa-reddit", "fa-reddit-square", "fa-stumbleupon-circle", "fa-stumbleupon", "fa-delicious", "fa-digg", "fa-pied-piper", "fa-pied-piper-alt", "fa-drupal", "fa-joomla", "fa-language", "fa-fax", "fa-building", "fa-child", "fa-paw", "fa-spoon", "fa-cube", "fa-cubes", "fa-behance", "fa-behance-square", "fa-steam", "fa-steam-square", "fa-recycle", "fa-automobile", "fa-car", "fa-cab", "fa-taxi", "fa-tree", "fa-spotify", "fa-deviantart", "fa-soundcloud", "fa-database", "fa-file-pdf-o", "fa-file-word-o", "fa-file-excel-o", "fa-file-powerpoint-o", "fa-file-photo-o", "fa-file-picture-o", "fa-file-image-o", "fa-file-zip-o", "fa-file-archive-o", "fa-file-sound-o", "fa-file-audio-o", "fa-file-movie-o", "fa-file-video-o", "fa-file-code-o", "fa-vine", "fa-codepen", "fa-jsfiddle", "fa-life-bouy", "fa-life-buoy", "fa-life-saver", "fa-support", "fa-life-ring", "fa-circle-o-notch", "fa-ra", "fa-rebel", "fa-ge", "fa-empire", "fa-git-square", "fa-git", "fa-y-combinator-square", "fa-yc-square", "fa-hacker-news", "fa-tencent-weibo", "fa-qq", "fa-wechat", "fa-weixin", "fa-send", "fa-paper-plane", "fa-send-o", "fa-paper-plane-o", "fa-history", "fa-circle-thin", "fa-header", "fa-paragraph", "fa-sliders", "fa-share-alt", "fa-share-alt-square", "fa-bomb", "fa-soccer-ball-o", "fa-futbol-o", "fa-tty", "fa-binoculars", "fa-plug", "fa-slideshare", "fa-twitch", "fa-yelp", "fa-newspaper-o", "fa-wifi", "fa-calculator", "fa-paypal", "fa-google-wallet", "fa-cc-visa", "fa-cc-mastercard", "fa-cc-discover", "fa-cc-amex", "fa-cc-paypal", "fa-cc-stripe", "fa-bell-slash", "fa-bell-slash-o", "fa-trash", "fa-copyright", "fa-at", "fa-eyedropper", "fa-paint-brush", "fa-birthday-cake", "fa-area-chart", "fa-pie-chart", "fa-line-chart", "fa-lastfm", "fa-lastfm-square", "fa-toggle-off", "fa-toggle-on", "fa-bicycle", "fa-bus", "fa-ioxhost", "fa-angellist", "fa-cc", "fa-shekel", "fa-sheqel", "fa-ils", "fa-meanpath", "fa-buysellads", "fa-connectdevelop", "fa-dashcube", "fa-forumbee", "fa-leanpub", "fa-sellsy", "fa-shirtsinbulk", "fa-simplybuilt", "fa-skyatlas", "fa-cart-plus", "fa-cart-arrow-down", "fa-diamond", "fa-ship", "fa-user-secret", "fa-motorcycle", "fa-street-view", "fa-heartbeat", "fa-venus", "fa-mars", "fa-mercury", "fa-intersex", "fa-transgender", "fa-transgender-alt", "fa-venus-double", "fa-mars-double", "fa-venus-mars", "fa-mars-stroke", "fa-mars-stroke-v", "fa-mars-stroke-h", "fa-neuter", "fa-genderless", "fa-facebook-official", "fa-pinterest-p", "fa-whatsapp", "fa-server", "fa-user-plus", "fa-user-times", "fa-hotel", "fa-bed", "fa-viacoin", "fa-train", "fa-subway", "fa-medium", "fa-yc", "fa-y-combinator", "fa-optin-monster", "fa-opencart", "fa-expeditedssl", "fa-battery-4", "fa-battery-full", "fa-battery-3", "fa-battery-three-quarters", "fa-battery-2", "fa-battery-half", "fa-battery-1", "fa-battery-quarter", "fa-battery-0", "fa-battery-empty", "fa-mouse-pointer", "fa-i-cursor", "fa-object-group", "fa-object-ungroup", "fa-sticky-note", "fa-sticky-note-o", "fa-cc-jcb", "fa-cc-diners-club", "fa-clone", "fa-balance-scale", "fa-hourglass-o", "fa-hourglass-1", "fa-hourglass-start", "fa-hourglass-2", "fa-hourglass-half", "fa-hourglass-3", "fa-hourglass-end", "fa-hourglass", "fa-hand-grab-o", "fa-hand-rock-o", "fa-hand-stop-o", "fa-hand-paper-o", "fa-hand-scissors-o", "fa-hand-lizard-o", "fa-hand-spock-o", "fa-hand-pointer-o", "fa-hand-peace-o", "fa-trademark", "fa-registered", "fa-creative-commons", "fa-gg", "fa-gg-circle", "fa-tripadvisor", "fa-odnoklassniki", "fa-odnoklassniki-square", "fa-get-pocket", "fa-wikipedia-w", "fa-safari", "fa-chrome", "fa-firefox", "fa-opera", "fa-internet-explorer", "fa-tv", "fa-television", "fa-contao", "fa-500px", "fa-amazon", "fa-calendar-plus-o", "fa-calendar-minus-o", "fa-calendar-times-o", "fa-calendar-check-o", "fa-industry", "fa-map-pin", "fa-map-signs", "fa-map-o", "fa-map", "fa-commenting", "fa-commenting-o", "fa-houzz", "fa-vimeo", "fa-black-tie", "fa-fonticons", "fa-reddit-alien", "fa-edge", "fa-credit-card-alt", "fa-codiepie", "fa-modx", "fa-fort-awesome", "fa-usb", "fa-product-hunt", "fa-mixcloud", "fa-scribd", "fa-pause-circle", "fa-pause-circle-o", "fa-stop-circle", "fa-stop-circle-o", "fa-shopping-bag", "fa-shopping-basket", "fa-hashtag", "fa-bluetooth", "fa-bluetooth-b", "fa-percent"); 

    sort( $icons );
    $iconArray = array();
    foreach ( $icons as $icon ) {
        $name                       = ucwords( str_replace( '-', ' ', str_replace( array(
            'fa-',
            '-play',
            '-square',
            '-alt',
            '-circle'
        ), '', $icon ) ) );
        $iconArray[ 'fa ' . $icon ] = $name;
    }

    $weekdays = array(
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    );

    // Get Store categories
    $sql = "SELECT *
    FROM " . $wpdb->prefix . "term_relationships
    LEFT JOIN " . $wpdb->prefix . "term_taxonomy
       ON (" . $wpdb->prefix . "term_relationships.term_taxonomy_id = " . $wpdb->prefix . "term_taxonomy.term_taxonomy_id)
    LEFT JOIN " . $wpdb->prefix . "terms on " . $wpdb->prefix . "term_taxonomy.term_taxonomy_id = " . $wpdb->prefix . "terms.term_id
    WHERE " . $wpdb->prefix . "term_taxonomy.taxonomy = 'store_category'
    GROUP BY " . $wpdb->prefix . "term_taxonomy.term_id";

    $store_categories = $wpdb->get_results($sql);
    $temp = array('' => 'None');
    foreach ($store_categories as $store_category) {
        $temp[$store_category->term_id] = $store_category->name;
    }
    $store_categories = $temp;

    // This is your option name where all the Redux data is stored.
    $opt_name = "wordpress_store_locator_options";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name' => 'wordpress_store_locator_options',
        'use_cdn' => TRUE,
        'dev_mode' => FALSE,
        'display_name' => 'WordPress Store Locator',
        'display_version' => '1.7.8',
        'page_title' => 'WordPress Store Locator',
        'update_notice' => TRUE,
        'intro_text' => '',
        'footer_text' => '&copy; '.date('Y').' DB-Dzine',
        'admin_bar' => TRUE,
        'menu_type' => 'submenu',
        'menu_title' => 'Settings',
        'allow_sub_menu' => TRUE,
        'page_parent' => 'edit.php?post_type=stores',
        'page_parent_post_type' => 'stores',
        'customizer' => FALSE,
        'default_mark' => '*',
        'hints' => array(
            'icon_position' => 'right',
            'icon_color' => 'lightgray',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output' => TRUE,
        'output_tag' => TRUE,
        'settings_api' => TRUE,
        'cdn_check_time' => '1440',
        'compiler' => TRUE,
        'page_permissions' => 'manage_options',
        'save_defaults' => TRUE,
        'show_import_export' => TRUE,
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => TRUE,
    );

    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */

    /*
     * ---> START HELP TABS
     */

    $tabs = array(
        array(
            'id'      => 'help-tab',
            'title'   => __( 'Information', 'wordpress-store-locator' ),
            'content' => __( '<p>Need support? Please use the comment function on codecanyon.</p>', 'wordpress-store-locator' )
        ),
    );
    Redux::setHelpTab( $opt_name, $tabs );

    // Set the help sidebar
    // $content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'wordpress-store-locator' );
    // Redux::setHelpSidebar( $opt_name, $content );


    /*
     * <--- END HELP TABS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */

    Redux::setSection( $opt_name, array(
        'title'  => __( 'Store Locator', 'wordpress-store-locator' ),
        'id'     => 'general',
        'desc'   => __( 'Need support? Please use the comment function on codecanyon.', 'wordpress-store-locator' ),
        'icon'   => 'el el-home',
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'General', 'wordpress-store-locator' ),
        // 'desc'       => __( '', 'wordpress-store-locator' ),
        'id'         => 'general-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enable',
                'type'     => 'checkbox',
                'title'    => __( 'Enable', 'wordpress-store-locator' ),
                'subtitle' => __( 'Enable store locator.', 'wordpress-store-locator' ),
                'default'  => '1',
            ),
            array(
                'id'       => 'apiKey',
                'type'     => 'text',
                'title'    => __( 'Google Api Key', 'wordpress-store-locator' ),
                'subtitle' => __( 'No key? <a href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend&keyType=CLIENT_SIDE&reusekey=true" target="_blank">Get it now!</a>', 'wordpress-store-locator' ),
            ),
            array(
                'id'       => 'serverApiKey',
                'type'     => 'text',
                'title'    => __( 'Google Api Key (Server Side)', 'wordpress-store-locator' ),
                'subtitle' => __( 'This will be used to fetch Lat / Lng when you import stores. It should not be your public key and have no http heder restrictions!', 'wordpress-store-locator' ),
            ),
            array(
                'id'       => 'excel2007',
                'type'     => 'checkbox',
                'title'    => __( 'Use Excel 2007', 'wordpress-store-locator' ),
                'subtitle' => __( 'If you can not work with xlsx (Excel 2007 and higher) files, check this. You then can work with normal .xls files.', 'wordpress-store-locator' ),
                'default'  => '0',
            ),
            array(
                'id'   => 'importer',
                'type' => 'info',
                'desc' => __('<div style="text-align:center;">
                    <a href="' . get_admin_url() . 'edit.php?post_type=stores&page=wordpress_store_locator_options_options&export-stores" class="button button-success">Export all Stores</a>
                    <a href="' . get_admin_url() . 'admin.php?page=wordpress-store-locator-importer" class="button button-primary">Import Stores</a>  
                    <a href="' . get_admin_url() . 'edit.php?post_type=stores&page=wordpress_store_locator_options_options&get-import-file" class="button button-primary">Get Sample Import File</a>
                    <a id="delete-stores" href="' . get_admin_url() . 'edit.php?post_type=stores&page=wordpress_store_locator_options_options&delete-stores" class="button button-danger">Delete all Stores</a> 
                    </div>', 'wordpress-store-locator')
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Map', 'wordpress-store-locator' ),
        'desc'       => __( 'Default Map Settings', 'wordpress-store-locator' ),
        'id'         => 'map',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'mapEnabled',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Map', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'mapColumns',
                'type'     => 'spinner',
                'title'    => __( 'Width (in Columns)', 'wordpress-store-locator' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '12',
                'default'  => '9',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapFullHeight',
                'type'     => 'checkbox',
                'title'    => __( 'Full Height Map', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'mapDistanceUnit',
                'type'     => 'select',
                'title'    => __( 'Distance Unit', 'wordpress-store-locator' ),
                'subtitle' => __( 'Choose the Distance Unit.', 'wordpress-store-locator' ),
                'options'  => array(
                    'km' => __('Kilometer (KM)', 'wordpress-store-locator'),
                    'mi' => __('Miles', 'wordpress-store-locator'),
                ),
                'default' => 'km',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapDefaultLat',
                'type'     => 'text',
                'title'    => __( 'Default Latitude', 'wordpress-store-locator' ),
                'default'  => '48.8620722',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapDefaultLng',
                'type'     => 'text',
                'title'    => __( 'Default Longitude', 'wordpress-store-locator' ),
                'default'  => '41.352047',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapDefaultType',
                'type'     => 'select',
                'title'    => __( 'Default Map Type', 'wordpress-store-locator' ),
                'subtitle' => __( 'Choose the default Map Type.', 'wordpress-store-locator' ),
                'options'  => array(
                    'ROADMAP' => __('Roadmap', 'wordpress-store-locator'),
                    'SATELLITE' => __('Satellite', 'wordpress-store-locator'),
                    'HYBRID' => __('Hybrid', 'wordpress-store-locator'),
                    'TERRAIN' => __('Terrain', 'wordpress-store-locator'),
                ),
                'default' => 'ROADMAP',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapDefaultZoom',
                'type'     => 'spinner',
                'title'    => __( 'Default Map Zoom', 'wordpress-store-locator' ),
                'subtitle' => __( 'Choose the default Zoom Level.', 'wordpress-store-locator' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '16',
                'default' => '10',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapRadiusSteps',
                'type'     => 'text',
                'title'    => __( 'Radius Select Steps', 'wordpress-store-locator' ),
                'subtitle' => __( 'Split the values by comma.', 'wordpress-store-locator' ),
                'default' => '5,10,25,50,100',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapRadius',
                'type'     => 'spinner',
                'title'    => __( 'Radius', 'wordpress-store-locator' ),
                'subtitle' => __( 'Choose the default Radius.', 'wordpress-store-locator' ),
                'default' => '25',
                'min'      => '1',
                'step'     => '5',
                'max'      => '9999',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapDrawRadiusCircle',
                'type'     => 'checkbox',
                'title'    => __( 'Draw Radius Circle', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapRadiusToZoom',
                'type'     => 'checkbox',
                'title'    => __( 'Adjust zoom level to Radius automatically', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('mapDrawRadiusCircle','equals','0'),
            ),
            array(
                'id'       => 'mapExtendRadius',
                'type'     => 'checkbox',
                'title'    => __( 'Extend map radius.', 'wordpress-store-locator' ),
                'subtitle'    => __( 'Automatically extend the search radius if no stores were found.', 'wordpress-store-locator' ),
                'default'  => 0,
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapPanToOnHover',
                'type'     => 'checkbox',
                'title'    => __( 'Pan to Marker on Mouse Hover.', 'wordpress-store-locator' ),
                'default'  => 0,
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapDefaultIcon',
                'type'     => 'text',
                'title'    => __( 'Default Store Icon', 'wordpress-store-locator' ),
                'default'  => 'https://maps.google.com/mapfiles/marker_grey.png',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapDefaultIconHover',
                'type'     => 'text',
                'title'    => __( 'Default Store Icon on Hover', 'wordpress-store-locator' ),
                'default'  => 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapDefaultUserIcon',
                'type'     => 'text',
                'title'    => __( 'Default User Icon', 'wordpress-store-locator' ),
                'default'  => 'https://plugins.db-dzine.com/wp-content/uploads/2016/04/home-2.png',
                'required' => array('mapEnabled','equals','1'),
            ),
            array(
                'id'       => 'mapStyling',
                'type'     => 'ace_editor',
                'mode'     => 'json',
                'title'    => __( 'Map Styling (JSON format)', 'wordpress-store-locator' ),
                'subtitle' => __( 'You can use <a href="https://mapstyle.withgoogle.com" target="_blank">https://mapstyle.withgoogle.com</a> OR <a href="https://snazzymaps.com" target="_blank">https://snazzymaps.com</a> for that.'),
                'default'  => '',
                'required' => array('mapEnabled','equals','1'),
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Infowindow ', 'wordpress-store-locator' ),
        'desc'       => __( 'Maps Infowindow settings', 'wordpress-store-locator' ),
        'id'         => 'infowindow',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'infowindowEnabled',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Infowindow', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'infowindowCheckClosed',
                'type'     => 'checkbox',
                'title'    => __( 'Check if Infowindow is closed', 'wordpress-store-locator' ),
                'subtitle'    => __( 'This prevents infowindow twiches when hovering on a map icon again.', 'wordpress-store-locator' ),
                'default'  => 0,
                'required' => array('infowindowEnabled','equals','1'),
            ),
            array(
                'id'       => 'infowindowLinkAction',
                'type'     => 'select',
                'title'    => __( 'Store Link Action', 'wordpress-store-locator' ),
                'subtitle' => __( 'What happens when store name is clicked in infowindow.', 'wordpress-store-locator' ),
                'options'  => array( 
                    'storepage' => __('Single Store Page', 'wordpress-store-locator'),
                    'web' => __('Website', 'wordpress-store-locator'),
                    'tel' => __('Telephone', 'wordpress-store-locator'),
                    'email' => __('Email', 'wordpress-store-locator'),
                    'none' => __('None', 'wordpress-store-locator'),
                ),
                'default'  => 'storepage',
                'required' => array('infowindowEnabled','equals','1'),
            ),
            array(
                'id'       => 'infowwindowWidth',
                'type'     => 'spinner',
                'title'    => __( 'Width', 'wordpress-store-locator' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '999',
                'default'  => '350',
                'required' => array('infowindowEnabled','equals','1'),
            ),
            array(
                'id'       => 'infowindowDetailsColumns',
                'type'     => 'spinner',
                'title'    => __( 'Details Width (in Columns)', 'wordpress-store-locator' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '12',
                'default'  => '12',
                'required' => array('infowindowEnabled','equals','1'),
            ),
            array(
                'id'       => 'infowindowImageColumns',
                'type'     => 'spinner',
                'title'    => __( 'Image Width (in Columns)', 'wordpress-store-locator' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '12',
                'default'  => '6',
                'required' => array('infowindowEnabled','equals','1'),
            ),
            array(
                'id'       => 'infowindowOpeningHoursColumns',
                'type'     => 'spinner',
                'title'    => __( 'Opening Hours Width (in Columns)', 'wordpress-store-locator' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '12',
                'default'  => '6',
                'required' => array('infowindowEnabled','equals','1'),
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Result List', 'wordpress-store-locator' ),
        'desc'       => __( 'Default Map Settings', 'wordpress-store-locator' ),
        'id'         => 'resultList',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'resultListEnabled',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Result List', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'resultListPosition',
                'type'     => 'select',
                'title'    => __( 'Position', 'wordpress-store-locator' ),
                'options'  => array(
                    'store-locator-pull-left' => __('Left Sidebar', 'wordpress-store-locator'),
                    'above' => __('Above Map', 'wordpress-store-locator'),
                    'below' => __('Below Map', 'wordpress-store-locator'),
                    'store-locator-pull-right' => __('Right Sidebar', 'wordpress-store-locator'),
                ),
                'default' => 'store-locator-pull-left',
                'required' => array('resultListEnabled','equals','1'),
            ),
            array(
                'id'       => 'resultListOrder',
                'type'     => 'select',
                'title'    => __( 'Sort Results by', 'wordpress-store-locator' ),
                'options'  => array(
                    'distance' => __('Distance', 'wordpress-store-locator'),
                    'post_title' => __('Alphabetically', 'wordpress-store-locator'),
                    'premium' => __('Premium Stores First', 'wordpress-store-locator'),
                ),
                'default' => 'distance',
                'required' => array('resultListEnabled','equals','1'),
            ),
            array(
                'id'       => 'resultListColumns',
                'type'     => 'spinner',
                'title'    => __( 'Width (in Columns)', 'wordpress-store-locator' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '12',
                'default'  => '3',
                'required' => array('resultListEnabled','equals','1'),
            ),
            array(
                'id'       => 'resultListShowTitle',
                'type'     => 'checkbox',
                'title'    => __( 'Show the title "Results"', 'wordpress-store-locator' ),
                'default'  => 0,
                'required' => array('resultListEnabled','equals','1'),
            ),
            array(
                'id'       => 'resultListHover',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Result List Hover', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('resultListEnabled','equals','1'),
            ),
            array(
                'id'       => 'resultListNoResultsText',
                'type'     => 'text',
                'title'    => __('No Results Text', 'wordpress-store-locator'),
                'default'  => 'No Stores found ... try again!',
                'required' => array('resultListEnabled','equals','1'),
            ),
            array(
                'id'       => 'resultListMax',
                'type'     => 'spinner',
                'title'    => __( 'Maximum Results', 'wordpress-store-locator' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '999',
                'default'  => '15',
                'required' => array('resultListEnabled','equals','1'),
            ),
            array(
                'id'       => 'resultListLinkAction',
                'type'     => 'select',
                'title'    => __( 'Store Link Action', 'wordpress-store-locator' ),
                'subtitle' => __( 'What happens when store name is clicked.', 'wordpress-store-locator' ),
                'options'  => array( 
                    'storepage' => __('Single Store Page', 'wordpress-store-locator'),
                    'web' => __('Website', 'wordpress-store-locator'),
                    'tel' => __('Telephone', 'wordpress-store-locator'),
                    'email' => __('Email', 'wordpress-store-locator'),
                    'none' => __('None', 'wordpress-store-locator'),
                ),
                'default'  => 'storepage',
            ),
            array(
                'id'       => 'resultListIconEnabled',
                'type'     => 'checkbox',
                'title'    => __( 'Show Result List Icon', 'wordpress-store-locator' ),
                'default'  => 0,
                'required' => array('resultListEnabled','equals','1'),
            ),
            array(
                'id'       => 'resultListIcon',
                'type'     => 'select',
                'title'    =>  __('Result List Icon', 'wordpress-store-locator'),
                'select2'  => array( 'containerCssClass' => 'fa' ),
                'class'    => ' font-icons',
                'options'  => $iconArray,
                'required' => array('resultListIconEnabled','equals','1'),
                'default'  => 'fa-map-marker'
            ),
            array(
                'id'       => 'resultListIconSize',
                'type'     => 'select',
                'title'    =>  __('Result List Icon Size', 'wordpress-store-locator'),
                'options'  => array(
                    '' => 'Normal',
                    'fa-lg' => 'Large',
                    'fa-2x' => '2x',
                    'fa-3x' => '3x',
                    'fa-4x' => '4x',
                    'fa-5x' => '5x',
                    ), 
                'default' => 'fa-3x',
                'required' => array('resultListIconEnabled','equals','1'),
            ),
            array(
                'id'     =>'resultListIconColor',
                'type'  => 'color',
                'title' => __('Result List Icon Color', 'wordpress-store-locator'), 
                'validate' => 'color',
                'default' => '#000000',
                'required' => array('resultListIconEnabled','equals','1'),
            ),
            array(
                'id'       => 'resultListPremiumIconEnabled',
                'type'     => 'checkbox',
                'title'    => __( 'Show Result List Premium Icon', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('resultListEnabled','equals','1'),
            ),
            array(
                'id'       => 'resultListPremiumIcon',
                'type'     => 'select',
                'title'    =>  __('Result List Premium Icon', 'wordpress-store-locator'),
                'select2'  => array( 'containerCssClass' => 'fa' ),
                'class'    => ' font-icons',
                'options'  => $iconArray,
                'required' => array('resultListPremiumIconEnabled','equals','1'),
                'default'  => 'fa-star'
            ),
            array(
                'id'       => 'resultListPremiumIconSize',
                'type'     => 'select',
                'title'    =>  __('Result List Premium Icon Size', 'wordpress-store-locator'),
                'options'  => array(
                    '' => 'Normal',
                    'fa-lg' => 'Large',
                    'fa-2x' => '2x',
                    'fa-3x' => '3x',
                    'fa-4x' => '4x',
                    'fa-5x' => '5x',
                    ), 
                'default' => 'fa-3x',
                'required' => array('resultListPremiumIconEnabled','equals','1'),
            ),
            array(
                'id'     =>'resultListPremiumIconColor',
                'type'  => 'color',
                'title' => __('Result List Premium Icon Color', 'wordpress-store-locator'), 
                'validate' => 'color',
                'default' => '#ffff00',
                'required' => array('resultListPremiumIconEnabled','equals','1'),
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Search Box', 'wordpress-store-locator' ),
        'desc'       => __( 'Search Box Settings', 'wordpress-store-locator' ),
        'id'         => 'searchBox',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'searchBoxEnabled',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Search Box', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'searchBoxShowGetMyPosition',
                'type'     => 'checkbox',
                'title'    => __( 'Show Get my Position', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchBoxShowButton',
                'type'     => 'checkbox',
                'title'    => __( 'Show Search Button', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchBoxShowShowAllStores',
                'type'     => 'checkbox',
                'title'    => __( 'Show a Show All stores Button', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'searchBoxShowShowAllStoresZoom',
                'type'     => 'spinner',
                'title'    => __( 'All Stores Zoom Level', 'wordpress-store-locator' ),
                'subtitle' => __( 'Choose the Zoom Level when you want to show all stores.', 'wordpress-store-locator' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '16',
                'default' => '10',
                'required' => array('searchBoxShowShowAllStores','equals','1'),
            ),
            array(
                'id'       => 'searchBoxShowShowAllStoresLat',
                'type'     => 'text',
                'title'    => __( 'All Stores Latitude', 'wordpress-store-locator' ),
                'default'  => '48.8620722',
                'required' => array('searchBoxShowShowAllStores','equals','1'),
            ),
            array(
                'id'       => 'searchBoxShowShowAllStoresLng',
                'type'     => 'text',
                'title'    => __( 'All Stores Longitude', 'wordpress-store-locator' ),
                'default'  => '41.352047',
                'required' => array('searchBoxShowShowAllStores','equals','1'),
            ),
            array(
                'id'       => 'searchBoxShowTitle',
                'type'     => 'checkbox',
                'title'    => __( 'Show Search Title', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchBoxShowActiveFilter',
                'type'     => 'checkbox',
                'title'    => __( 'Show Search Active Filter', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchBoxShowFilter',
                'type'     => 'checkbox',
                'title'    => __( 'Show Search Filter', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchBoxShowFilterOpen',
                'type'     => 'checkbox',
                'title'    => __( 'Filter open by default', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchBoxDefaultCategory',
                'type'     => 'select',
                'title'    =>  __('Default filter category', 'wordpress-store-locator'),
                'subtitle' =>  __('Set a default filter category', 'wordpress-store-locator'),
                'options'  => $store_categories,
                'default' => '',
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchButtonText',
                'type'     => 'text',
                'title'    => __('Button Text', 'wordpress-store-locator'),
                'subtitle' => __('Text for the search button.', 'wordpress-store-locator'),
                'default'  => 'Find in Store',
                 'required' => array('searchBoxShowButton','equals','1'),
            ),
            array(
                'id'       => 'searchBoxAutolocate',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Auto Geolocation', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchBoxAutolocate',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Auto Geolocation', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchBoxSaveAutolocate',
                'type'     => 'checkbox',
                'title'    => __( 'Save Auto Geolocation in Cookie?', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchBoxAutocomplete',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Autocomplete', 'wordpress-store-locator' ),
                'default'  => 1,
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'autocompleteType',
                'type'     => 'select',
                'title'    => __( 'Restrict Types', 'wordpress-store-locator' ),
                'subtitle'     => __('You may restrict results from a Place Autocomplete request to be of a certain type. If for example you want to hide the street from autocomplete search, you have to select the "Regions". <a href="https://developers.google.com/places/supported_types?hl=en" target="_blank">Learn More</a>!'),
                'options'  => array(
                    'geocode' => __('Geocode', 'wordpress-store-locator'),
                    '(regions)' => __('Regions', 'wordpress-store-locator'),
                    '(cities)' => __('Cities', 'wordpress-store-locator'),
                    'address' => __('Address', 'wordpress-store-locator'),
                    'establishment' => __('Establishment', 'wordpress-store-locator'),
                ),
                'default' => 'geocode',
                'required' => array('searchBoxAutocomplete','equals','1'),
            ),
            array(
                'id'       => 'autocompleteCountryRestrict',
                'type'     => 'text',
                'title'    => __('Restrict Autocomplete to a country', 'wordpress-store-locator'),
                'subtitle'     => '2 character ISO-Codes only! You can insert for example us to limit the autocomplete to only search in United States.',
                'default'  => '',
                'required' => array('searchBoxAutocomplete','equals','1'),
            ),
            array(
                'id'       => 'searchBoxPosition',
                'type'     => 'select',
                'title'    => __( 'Position', 'wordpress-store-locator' ),
                'options'  => array(
                    'before' => __('Before Result List', 'wordpress-store-locator'),
                    'above' => __('Above Map', 'wordpress-store-locator'),
                    'below' => __('Below Map', 'wordpress-store-locator'),
                    'after' => __('After Result List', 'wordpress-store-locator'),
                ),
                'default' => 'before',
                'required' => array('searchBoxEnabled','equals','1'),
            ),
            array(
                'id'       => 'searchBoxColumns',
                'type'     => 'spinner',
                'title'    => __( 'Width (in Columns)', 'wordpress-store-locator' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '12',
                'default'  => '6',
                'required' => array('searchBoxEnabled','equals','1'),
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Loading', 'wordpress-store-locator' ),
        'desc'       => __( 'Loading Settings', 'wordpress-store-locator' ),
        'id'         => 'loading',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'loadingIcon',
                'type'     => 'select',
                'title'    =>  __('Loading Icon', 'wordpress-store-locator'),
                'select2'  => array( 'containerCssClass' => 'fa' ),
                'class'    => ' font-icons',
                'options'  => $iconArray,
                'default'  => 'fa-spinner'
            ),
            array(
                'id'       => 'loadingAnimation',
                'type'     => 'select',
                'title'    =>  __('Loading Animation', 'wordpress-store-locator'),
                'options'  => array(
                    '' => 'None',
                    'fa-spin' => 'Spin',
                    'fa-pules' => 'Pules'
                    ), 
                'default' => 'fa-spin'
            ),
            array(
                'id'       => 'loadingIconSize',
                'type'     => 'select',
                'title'    =>  __('Loading Icon Size', 'wordpress-store-locator'),
                'options'  => array(
                    '' => 'Normal',
                    'fa-lg' => 'Large',
                    'fa-2x' => '2x',
                    'fa-3x' => '3x',
                    'fa-4x' => '4x',
                    'fa-5x' => '5x',
                    ), 
                'default' => 'fa-3x'
            ),
            array(
                'id'     =>'loadingIconColor',
                'type'  => 'color',
                'title' => __('Loading Icon Color', 'wordpress-store-locator'), 
                'validate' => 'color',
                'default' => '#000000'
            ),
            array(
                'id'     =>'loadingOverlayColor',
                'type'  => 'color',
                'title' => __('Overlay Color', 'wordpress-store-locator'), 
                'validate' => 'color',
                'default' => '#FFFFFF'
            ),
            array(
                'id'     =>'loadingOverlayTransparency',
                'type'  => 'spinner',
                'title' => __('Overlay Transparency', 'wordpress-store-locator'), 
                'default'  => '0.8',
                'min'      => '0',
                'max'      => '1',
                'step'     => '0.1'
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Data to Show', 'wordpress-store-locator' ),
        'desc'       => __( 'Data you want to show.', 'wordpress-store-locator' ),
        'id'         => 'fields',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'showName',
                'type'     => 'checkbox',
                'title'    => __( 'Show Name', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showDescription',
                'type'     => 'checkbox',
                'title'    => __( 'Show Description', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showStreet',
                'type'     => 'checkbox',
                'title'    => __( 'Show Street', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showZip',
                'type'     => 'checkbox',
                'title'    => __( 'Show ZIP', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showCity',
                'type'     => 'checkbox',
                'title'    => __( 'Show City', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showRegion',
                'type'     => 'checkbox',
                'title'    => __( 'Show Region', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showCountry',
                'type'     => 'checkbox',
                'title'    => __( 'Show Country', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showAddressStyle',
                'type'     => 'select',
                'title'    =>  __('Address Format', 'wordpress-store-locator'),
                'options'  => array(
                    'standard' => 'Standard',
                    'american' => 'American / Australian'
                    ), 
                'default' => 'europe'
            ),
            array(
                'id'       => 'showWebsite',
                'type'     => 'checkbox',
                'title'    => __( 'Show Website', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showWebsiteText',
                'type'     => 'text',
                'title'    => __('Website Text', 'wordpress-store-locator'),
                'default'  => 'Website',
                'required' => array('showWebsite','equals','1'),
            ),
            array(
                'id'       => 'showEmail',
                'type'     => 'checkbox',
                'title'    => __( 'Show Email', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showEmailText',
                'type'     => 'text',
                'title'    => __('Email Text', 'wordpress-store-locator'),
                'default'  => 'Email',
                'required' => array('showEmail','equals','1'),
            ),
            array(
                'id'       => 'showTelephone',
                'type'     => 'checkbox',
                'title'    => __( 'Show Telephone', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showTelephoneText',
                'type'     => 'text',
                'title'    => __('Telephone Text', 'wordpress-store-locator'),
                'default'  => 'Tel.',
                'required' => array('showTelephone','equals','1'),
            ),
            array(
                'id'       => 'showMobile',
                'type'     => 'checkbox',
                'title'    => __( 'Show Mobile Phone', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'showMobileText',
                'type'     => 'text',
                'title'    => __('Mobile Text', 'wordpress-store-locator'),
                'default'  => 'Mobile',
                'required' => array('showMobile','equals','1'),
            ),
            array(
                'id'       => 'showFax',
                'type'     => 'checkbox',
                'title'    => __( 'Show Fax', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'showFaxText',
                'type'     => 'text',
                'title'    => __('Fax Text', 'wordpress-store-locator'),
                'default'  => 'Fax',
                'required' => array('showFax','equals','1'),
            ),
            array(
                'id'       => 'showDistance',
                'type'     => 'checkbox',
                'title'    => __( 'Show Distance', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'showDistanceText',
                'type'     => 'text',
                'title'    => __('Distance Text', 'wordpress-store-locator'),
                'default'  => 'Distance',
                'required' => array('showDistance','equals','1'),
            ),
            array(
                'id'       => 'showStoreCategories',
                'type'     => 'checkbox',
                'title'    => __( 'Show Stores Categories', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'showStoreFilter',
                'type'     => 'checkbox',
                'title'    => __( 'Show Stores Filter', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'showActiveFilter',
                'type'     => 'checkbox',
                'title'    => __( 'Show Active Filter', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showFilter',
                'type'     => 'checkbox',
                'title'    => __( 'Show Filter', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showFilterCategoriesAsImage',
                'type'     => 'checkbox',
                'title'    => __( 'Display Category Filters as Image', 'wordpress-store-locator' ),
                'default'  => 0,
                'required' => array('showFilter','equals','1'),
            ),
            array(
                'id'       => 'showContactStore',
                'type'     => 'checkbox',
                'title'    => __( 'Show Contact Store', 'wordpress-store-locator' ),
                'subtitle' => __( 'Contact dealer via a contact form on a new pag.', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showContactStoreText',
                'type'     => 'text',
                'title'    => __('Contact Store Text', 'wordpress-store-locator'),
                'default'  => 'Contact Store',
                'required' => array('showContactStore','equals','1'),
            ),
            array(
                'id'       => 'showContactStorePage',
                'type'     => 'select',
                'title'    => __('Contact Page', 'wordpress-helpdesk'),
                'subtitle' => __('Make sure a form is embedded on the site. A tutorial how to create a form can be found here.', 'wordpress-helpdesk'),
                'data'     => 'pages',
                'required' => array('showContactStore','equals','1'),
            ),
            array(
                'id'       => 'showGetDirection',
                'type'     => 'checkbox',
                'title'    => __( 'Show Get Direction', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showGetDirectionText',
                'type'     => 'text',
                'title'    => __('Get Direction Text', 'wordpress-store-locator'),
                'default'  => 'Get Direction',
                'required' => array('showGetDirection','equals','1'),
            ),
            array(
                'id'       => 'showCallNow',
                'type'     => 'checkbox',
                'title'    => __( 'Show Call Now Button', 'wordpress-store-locator' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'showCallNowText',
                'type'     => 'text',
                'title'    => __('Call Now Text', 'wordpress-store-locator'),
                'default'  => 'Call Now',
                'required' => array('showCallNow','equals','1'),
            ),
            array(
                'id'       => 'showVisitWebsite',
                'type'     => 'checkbox',
                'title'    => __( 'Show Visit Website Button', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'showVisitWebsiteText',
                'type'     => 'text',
                'title'    => __('Visit Website Text', 'wordpress-store-locator'),
                'default'  => 'Visit Website',
                'required' => array('showVisitWebsite','equals','1'),
            ),
            array(
                'id'       => 'showWriteEmail',
                'type'     => 'checkbox',
                'title'    => __( 'Show Write Email Button', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'showWriteEmailText',
                'type'     => 'text',
                'title'    => __('Write Email Text', 'wordpress-store-locator'),
                'default'  => 'Write Email',
                'required' => array('showWriteEmail','equals','1'),
            ),
            array(
                'id'       => 'showShowOnMap',
                'type'     => 'checkbox',
                'title'    => __( 'Show Show on Map Button', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'showShowOnMapText',
                'type'     => 'text',
                'title'    => __('Show on Map Text', 'wordpress-store-locator'),
                'default'  => 'Show on Map',
                'required' => array('showShowOnMap','equals','1'),
            ),
            array(
                'id'       => 'showImage',
                'type'     => 'checkbox',
                'title'    => __( 'Show Image', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'imageDimensions',
                'type'     => 'dimensions',
                'units'    => array('px'),
                'title'    => __('Image Dimensions', 'wordpress-store-locator'),
                'default'  => array(
                    'width'   => '150', 
                    'height'   => '100', 
                ),
                'required' => array('showImage','equals','1'),
            ),
            array(
                'id'       => 'imagePosition',
                'type'     => 'select',
                'title'    => __( 'Image Position', 'wordpress-store-locator' ),
                'options'  => array(
                    'store-locator-pull-left' => __('Left', 'wordpress-store-locator'),
                    'store-locator-pull-right' => __('Right', 'wordpress-store-locator'),
                ),
                'default' => 'store-locator-pull-left',
                'required' => array('showImage','equals','1'),
            ),
            array(
                'id'       => 'showOpeningHours',
                'type'     => 'checkbox',
                'title'    => __( 'Show Opening Hours', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'showOpeningHoursText',
                'type'     => 'text',
                'title'    => __('Opening Hours Text', 'wordpress-store-locator'),
                'default'  => 'Opening Hours',
                'required' => array('showOpeningHours','equals','1'),
            ),
            array(
                'id'       => 'showOpeningHoursClock',
                'type'     => 'text',
                'title'    => __('Opening Hours Clock', 'wordpress-store-locator'),
                'default'  => "o'Clock",
                'required' => array('showOpeningHours','equals','1'),
            ),
            array(
                'id'       => 'showOpeningHoursMonday',
                'type'     => 'text',
                'title'    => __('Monday Text', 'wordpress-store-locator'),
                'default'  => 'Monday',
                'required' => array('showOpeningHours','equals','1'),
            ),
            array(
                'id'       => 'showOpeningHoursTuesday',
                'type'     => 'text',
                'title'    => __('Tuesday Text', 'wordpress-store-locator'),
                'default'  => 'Tuesday',
                'required' => array('showOpeningHours','equals','1'),
            ),
            array(
                'id'       => 'showOpeningHoursWednesday',
                'type'     => 'text',
                'title'    => __('Wednesday Text', 'wordpress-store-locator'),
                'default'  => 'Wednesday',
                'required' => array('showOpeningHours','equals','1'),
            ),
            array(
                'id'       => 'showOpeningHoursThursday',
                'type'     => 'text',
                'title'    => __('Thursday Text', 'wordpress-store-locator'),
                'default'  => 'Thursday',
                'required' => array('showOpeningHours','equals','1'),
            ),
            array(
                'id'       => 'showOpeningHoursFriday',
                'type'     => 'text',
                'title'    => __('Friday Text', 'wordpress-store-locator'),
                'default'  => 'Friday',
                'required' => array('showOpeningHours','equals','1'),
            ),
            array(
                'id'       => 'showOpeningHoursSaturday',
                'type'     => 'text',
                'title'    => __('Saturday Text', 'wordpress-store-locator'),
                'default'  => 'Saturday',
                'required' => array('showOpeningHours','equals','1'),
            ),
            array(
                'id'       => 'showOpeningHoursSunday',
                'type'     => 'text',
                'title'    => __('Sunday Text', 'wordpress-store-locator'),
                'default'  => 'Sunday',
                'required' => array('showOpeningHours','equals','1'),
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Button', 'wordpress-store-locator' ),
        'id'         => 'button',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'buttonEnabled',
                'type'     => 'checkbox',
                'title'    => __( 'Enable', 'wordpress-store-locator' ),
                'subtitle' => __( 'Enables the single product custom button.', 'wordpress-store-locator' ),
                'default'  => '1',
            ),
            array(
                'id'       => 'buttonText',
                'type'     => 'text',
                'title'    => __('Button Text', 'wordpress-store-locator'),
                'subtitle' => __('Text inside the custom button.', 'wordpress-store-locator'),
                'default'  => 'Find in Store',
            ),
            array(
                'id'       => 'buttonPosition',
                'type'     => 'select',
                'title'    => __('Button Position', 'wordpress-store-locator'),
                'subtitle' => __('Specify the positon of the Button.', 'wordpress-store-locator'),
                'default'  => 'wordpress_single_product_summary',
                'options'  => array( 
                    'woocommerce_before_single_product' => __('Before Single Product', 'wordpress-store-locator'),
                    'woocommerce_before_single_product_summary' => __('Before Single Product Summary', 'wordpress-store-locator'),
                    'woocommerce_single_product_summary' => __('In Single Product Summary', 'wordpress-store-locator'),
                    'woocommerce_product_meta_start' => __('Before Meta Information', 'wordpress-store-locator'),
                    'woocommerce_product_meta_end' => __('After Meta Information', 'wordpress-store-locator'),
                    'woocommerce_after_single_product_summary' => __('After Single Product Summary', 'wordpress-store-locator'),
                    'woocommerce_after_single_product' => __('After Single Product', 'wordpress-store-locator'),
                    'woocommerce_after_main_content' => __('After Main Product', 'wordpress-store-locator'),
                ),
            ),
            array(
                'id'       => 'buttonAction',
                'type'     => 'select',
                'title'    => __('Button Action', 'wordpress-store-locator'), 
                'subtitle' => __('What happens when the User clicks the button.', 'wordpress-store-locator'),
                'options'  => array(
                    '1' => __('Open Store Locator Modal', 'wordpress-store-locator' ),
                    '2' => __('Go to custom URL', 'wordpress-store-locator' ),
                ),
                'default'  => '1',
            ),
            array(
                'id'       => 'buttonActionURL',
                'type'     => 'text',
                'title'    => __('Button custom URL', 'wordpress-store-locator'),
                'subtitle' => __('The URL where the user will be sent to when he clicked the button.', 'wordpress-store-locator'),
                'validate' => 'url',
                'required' => array('buttonAction','equals','2'),
            ),
            array(
                'id'       => 'buttonActionURLTarget',
                'type'     => 'select',
                'title'    => __('Custom Button URL target', 'wordpress-store-locator'),
                'subtitle' => __('The target attribute of the link.', 'wordpress-store-locator'),
                'options'  => array(
                    '_self' => __('_self (same Window)', 'wordpress-store-locator'),
                    '_blank' => __('_blank (new Window)', 'wordpress-store-locator'),
                    '_parent' => __('_parent (parent Window)', 'wordpress-store-locator'),
                    '_top' => __('_top (full body of the Window)', 'wordpress-store-locator'),
                ),
                'default'  => '_self',
                'required' => array('buttonAction','equals','2'),
            ),
            array(
                'id'       => 'buttonModalTitle',
                'type'     => 'text',
                'title'    => __('Store locator Title', 'wordpress-store-locator'),
                'subtitle' => __('The title of the Store locator Modal', 'wordpress-store-locator'),
                'default'  => 'Find in Store',
                'required' => array('buttonAction','equals','1'),
            ),
            array(
                'id'       => 'buttonModalPosition',
                'type'     => 'select',
                'title'    => __('Modal Code Position', 'wordpress-store-locator'),
                'subtitle' => __('The position of the Store locator.', 'wordpress-store-locator'),
                'default'  => 'wp_footer',
                'options'  => array(
                    'wp_footer' => __('Footer', 'wordpress-store-locator'),      
                    'woocommerce_before_main_content' => __('Before Main Content', 'wordpress-store-locator'),
                    'woocommerce_before_single_product' => __('Before Single Product', 'wordpress-store-locator'),
                    'woocommerce_before_single_product_summary' => __('Before Single Product Summary', 'wordpress-store-locator'),
                    'woocommerce_single_product_summary' => __('In Single Product Summary', 'wordpress-store-locator'),
                    'woocommerce_product_meta_start' => __('Before Meta Information', 'wordpress-store-locator'),
                    'woocommerce_product_meta_end' => __('After Meta Information', 'wordpress-store-locator'),
                    'woocommerce_after_single_product_summary' => __('After Single Product Summary', 'wordpress-store-locator'),
                    'woocommerce_after_single_product' => __('After Single Product', 'wordpress-store-locator'),
                    'woocommerce_after_main_content' => __('After Main Product', 'wordpress-store-locator'),
                ),
                'required' => array('buttonAction','equals','1'),
            ),
            array(
                'id'       => 'buttonModalSize',
                'type'     => 'select',
                'title'    => __('Modal size', 'wordpress-store-locator'),
                'subtitle' => __('Size of the modal.', 'wordpress-store-locator'),
                'options'  => array(
                    '' => __('Normal', 'wordpress-store-locator'),
                    'modal-sm' => __('Small', 'wordpress-store-locator'),
                    'modal-lg' => __('Large', 'wordpress-store-locator'),
                ),
                'default'  => 'modal-lg',
                'required' => array('buttonAction','equals','1'),
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Defaults', 'wordpress-store-locator' ),
        'id'         => 'defaults',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'defaultAddress1',
                'type'     => 'text',
                'title'    => __('Default Address 1', 'wordpress-store-locator'),
                'default'  => '',
            ),
            array(
                'id'       => 'defaultAddress2',
                'type'     => 'text',
                'title'    => __('Default Address 2', 'wordpress-store-locator'),
                'default'  => '',
            ),
            array(
                'id'       => 'defaultZIP',
                'type'     => 'text',
                'title'    => __('Default ZIP', 'wordpress-store-locator'),
                'default'  => '',
            ),
            array(
                'id'       => 'defaultCity',
                'type'     => 'text',
                'title'    => __('Default City', 'wordpress-store-locator'),
                'default'  => '',
            ),
            array(
                'id'       => 'defaultRegion',
                'type'     => 'text',
                'title'    => __('Default Region', 'wordpress-store-locator'),
                'default'  => '',
            ),
            array(
                'id'       => 'defaultCountry',
                'type'     => 'select',
                'title'    => __('Default Country', 'wordpress-store-locator'),
                'options' => array( "AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "BQ" => "British Antarctic Territory", "IO" => "British Indian Ocean Territory", "VG" => "British Virgin Islands", "BN" => "Brunei", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CT" => "Canton and Enderbury Islands", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos [Keeling] Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo - Brazzaville", "CD" => "Congo - Kinshasa", "CK" => "Cook Islands", "CR" => "Costa Rica", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "CI" => "Côte d’Ivoire", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "NQ" => "Dronning Maud Land", "DD" => "East Germany", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "FQ" => "French Southern and Antarctic Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and McDonald Islands", "HN" => "Honduras", "HK" => "Hong Kong SAR China", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle of Man", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JT" => "Johnston Island", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Laos", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macau SAR China", "MK" => "Macedonia", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "FX" => "Metropolitan France", "MX" => "Mexico", "FM" => "Micronesia", "MI" => "Midway Islands", "MD" => "Moldova", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar [Burma]", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NT" => "Neutral Zone", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "KP" => "North Korea", "VD" => "North Vietnam", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PC" => "Pacific Islands Trust Territory", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territories", "PA" => "Panama", "PZ" => "Panama Canal Zone", "PG" => "Papua New Guinea", "PY" => "Paraguay", "YD" => "People's Democratic Republic of Yemen", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn Islands", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RO" => "Romania", "RU" => "Russia", "RW" => "Rwanda", "RE" => "Réunion", "BL" => "Saint Barthélemy", "SH" => "Saint Helena", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "CS" => "Serbia and Montenegro", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "KR" => "South Korea", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syria", "ST" => "São Tomé and Príncipe", "TW" => "Taiwan", "TJ" => "Tajikistan", "TZ" => "Tanzania", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UM" => "U.S. Minor Outlying Islands", "PU" => "U.S. Miscellaneous Pacific Islands", "VI" => "U.S. Virgin Islands", "UG" => "Uganda", "UA" => "Ukraine", "SU" => "Union of Soviet Socialist Republics", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "ZZ" => "Unknown or Invalid Region", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VA" => "Vatican City", "VE" => "Venezuela", "VN" => "Vietnam", "WK" => "Wake Island", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe", "AX" => "Åland Islands" ),
            ),
            array(
                'id'       => 'defaultTelephone',
                'type'     => 'text',
                'title'    => __('Default Telephone', 'wordpress-store-locator'),
                'default'  => '',
            ),
            array(
                'id'       => 'defaultMobile',
                'type'     => 'text',
                'title'    => __('Default Mobile', 'wordpress-store-locator'),
                'default'  => '',
            ),
            array(
                'id'       => 'defaultFax',
                'type'     => 'text',
                'title'    => __('Default Fax', 'wordpress-store-locator'),
                'default'  => '',
            ),
            array(
                'id'       => 'defaultEmail',
                'type'     => 'text',
                'title'    => __('Default Email', 'wordpress-store-locator'),
                'default'  => 'info@',
            ),
            array(
                'id'       => 'defaultWebsite',
                'type'     => 'text',
                'title'    => __('Default Website', 'wordpress-store-locator'),
                'default'  => 'http://',
            ),
            array(
                'id'       => 'defaultOpen',
                'type'     => 'text',
                'title'    => __('Default Open (Mo - Fr)', 'wordpress-store-locator'),
                'default'  => '08:00',
            ),
            array(
                'id'       => 'defaultClose',
                'type'     => 'text',
                'title'    => __('Default Close (Mo - Fr)', 'wordpress-store-locator'),
                'default'  => '17:00',
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => __( 'Advanced settings', 'wordpress-store-locator' ),
        'desc'       => __( 'Custom stylesheet / javascript.', 'wordpress-store-locator' ),
        'id'         => 'advanced',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'useOutputBuffering',
                'type'     => 'checkbox',
                'title'    => __( 'Use Output Buffering', 'wordpress-store-locator' ),
                'subtitle' => __( 'Uncheck this if you see blank strings.', 'wordpress-store-locator' ),
                'default'  => '0',
            ),
            array(
                'id'       => 'doNotLoadBootstrap',
                'type'     => 'checkbox',
                'title'    => __( 'Do Not load Bootstrap', 'wordpress-store-locator' ),
                'default'  => 0,
            ),
            array(
                'id'       => 'customCSS',
                'type'     => 'ace_editor',
                'mode'     => 'css',
                'title'    => __( 'Custom CSS', 'wordpress-store-locator' ),
                'subtitle' => __( 'Add some stylesheet if you want.', 'wordpress-store-locator' ),
            ),
            array(
                'id'       => 'customJS',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __( 'Custom JS', 'wordpress-store-locator' ),
                'subtitle' => __( 'Add some javascript if you want.', 'wordpress-store-locator' ),
            ),           
        )
    ));


    /*
     * <--- END SECTIONS
     */
