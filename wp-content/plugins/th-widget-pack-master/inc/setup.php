<?php

// Adding Custom Icons for Icon Control
if('embark' == THEMO_CURRENT_THEME || 'bellevue' == THEMO_CURRENT_THEME ){
    require_once THEMO_PATH . 'fields/icons.php' ;
}elseif('stratus' == THEMO_CURRENT_THEME || 'pursuit' == THEMO_CURRENT_THEME || 'entrepreneur' == THEMO_CURRENT_THEME){
    require_once THEMO_PATH . 'fields/stratus_icons.php' ;
}else{
    require_once THEMO_PATH . 'fields/icons.php' ;
}

require_once THEMO_PATH . 'inc/helper-functions.php' ;

if ( ! function_exists( 'themovation_elements' ) ) {
    function themovation_elements()
    {
        require_once THEMO_PATH . 'elements/slider.php';
        require_once THEMO_PATH . 'elements/header.php';
        require_once THEMO_PATH . 'elements/button.php';
        require_once THEMO_PATH . 'elements/call-to-action.php';
        require_once THEMO_PATH . 'elements/testimonial.php';
        require_once THEMO_PATH . 'elements/service-block.php';
        require_once THEMO_PATH . 'elements/formidable-form.php';
        require_once THEMO_PATH . 'elements/info-card.php';
        require_once THEMO_PATH . 'elements/team.php';

        if('embark' == THEMO_CURRENT_THEME || 'entrepreneur' == THEMO_CURRENT_THEME){
            require_once THEMO_PATH . 'elements/appointments.php';
        }elseif('stratus' == THEMO_CURRENT_THEME || 'pursuit' == THEMO_CURRENT_THEME){
            require_once THEMO_PATH . 'elements/appointments.php';
        }elseif('bellevue' == THEMO_CURRENT_THEME){
            require_once THEMO_PATH . 'elements/wp-booking-system.php';
        }

        if('embark' == THEMO_CURRENT_THEME){
            require_once THEMO_PATH . 'elements/tour-grid.php';
        }elseif('stratus' == THEMO_CURRENT_THEME || 'pursuit' == THEMO_CURRENT_THEME || 'entrepreneur' == THEMO_CURRENT_THEME){
            require_once THEMO_PATH . 'elements/portfolio-grid.php';
        }elseif('bellevue' == THEMO_CURRENT_THEME){
            require_once THEMO_PATH . 'elements/room-grid.php';
        }


        if('embark' == THEMO_CURRENT_THEME){
            require_once THEMO_PATH . 'elements/tour-info.php';
        }elseif('stratus' == THEMO_CURRENT_THEME || 'pursuit' == THEMO_CURRENT_THEME || 'entrepreneur' == THEMO_CURRENT_THEME){
            require_once THEMO_PATH . 'elements/info-bar.php';
        }elseif('bellevue' == THEMO_CURRENT_THEME){
            require_once THEMO_PATH . 'elements/room-info.php';
        }

        require_once THEMO_PATH . 'elements/package.php';

        if('embark' == THEMO_CURRENT_THEME || 'bellevue' == THEMO_CURRENT_THEME ){
            require_once THEMO_PATH . 'elements/itinerary.php';
        }elseif('stratus' == THEMO_CURRENT_THEME || 'pursuit' == THEMO_CURRENT_THEME || 'entrepreneur' == THEMO_CURRENT_THEME){
            require_once THEMO_PATH . 'elements/expand-list.php';
        }

        require_once THEMO_PATH . 'elements/pricing.php';
        require_once THEMO_PATH . 'elements/blog.php';
        require_once THEMO_PATH . 'elements/image-gallery.php';
        require_once THEMO_PATH . 'elements/google-maps.php';
    }
}
// Include Custom Widgets
add_filter( 'elementor/widgets/widgets_registered', 'themovation_elements' );

// Include scripts, custom post type, shortcodes
require_once THEMO_PATH . 'inc/elementor-section.php';
require_once THEMO_PATH . 'inc/enqueue.php';

if('embark' == THEMO_CURRENT_THEME){
    require_once THEMO_PATH . 'inc/cpt_tours.php' ;
}elseif('stratus' == THEMO_CURRENT_THEME || 'pursuit' == THEMO_CURRENT_THEME || 'entrepreneur' == THEMO_CURRENT_THEME){
    require_once THEMO_PATH . 'inc/cpt_portfolio.php' ;
}elseif('bellevue' == THEMO_CURRENT_THEME){
    require_once THEMO_PATH . 'inc/cpt_room.php' ;
}
require_once THEMO_PATH . 'inc/shortcodes.php' ;


// GLOBAL VARIABLES
global $th_map_id;
$th_map_id = 0;

// When plugin is installed for the first time, set global elementor settings.



if ( ! function_exists( 'themovation_so_widgets_bundle_setup_elementor_settings' ) ) {
    function themovation_so_widgets_bundle_setup_elementor_settings()
    {

        // Disable color schemes
        $elementor_disable_color_schemes = get_option('elementor_disable_color_schemes');
        if (empty($elementor_disable_color_schemes)) {
            update_option('elementor_disable_color_schemes', 'yes');
        }

        // Disable typography schemes
        $elementor_disable_typography_schemes = get_option('elementor_disable_typography_schemes');
        if (empty($elementor_disable_typography_schemes)) {
            update_option('elementor_disable_typography_schemes', 'yes');
        }

        // Disable global lightbox by default.
        update_option('elementor_global_image_lightbox', '');

        // Check for our custom post type, if it's not included, include it.
        $elementor_cpt_support = get_option('elementor_cpt_support');
        if (empty($elementor_cpt_support)) {
            $elementor_cpt_support = array();
        }

        if (!in_array("page", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"page");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("post", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"post");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("themo_tour", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"themo_tour");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("themo_portfolio", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"themo_portfolio");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("themo_room", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"themo_room");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("product", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"product");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

    }
}

// on plugin Activaton, set Elementor Global Options and register Custom Post Types.

if ( ! function_exists( 'themovation_so_widgets_bundle_install' ) ) {
    function themovation_so_widgets_bundle_install()
    {
        // trigger our function that sets up Elementor Global Settings
        themovation_so_widgets_bundle_setup_elementor_settings();

        if('embark' == THEMO_CURRENT_THEME){
            // Regsiter Custom Post Types
            themo_tour_custom_post_type();

            // Register Custom Taxonomy
            themo_tour_type();
        }elseif('stratus' == THEMO_CURRENT_THEME || 'pursuit' == THEMO_CURRENT_THEME || 'entrepreneur' == THEMO_CURRENT_THEME){
            // Regsiter Custom Post Types
            themo_portfolio_custom_post_type();

            // Register Custom Taxonomy
            themo_project_type();
        }elseif('bellevue' == THEMO_CURRENT_THEME){
            // Regsiter Custom Post Types
            themo_room_custom_post_type();

            // Register Custom Taxonomy
            themo_room_type();
        }



        // clear the permalinks after the post type has been registered
        flush_rewrite_rules();
    }
}
register_activation_hook( THEMO__FILE__, 'themovation_so_widgets_bundle_install' );


// Add custom controls to the Page Settings inside the Elementor Global Options.

// Top of section
if ( ! function_exists( 'th_add_custom_controls_elem_page_settings_top' ) ) {
    function th_add_custom_controls_elem_page_settings_top(\Elementor\Core\Settings\Page\Model $page)
    {

        if(isset($page) && $page->get_id() > ""){
            $th_post_type = false;
            $th_post_type = get_post_type($page->get_id());
            if($th_post_type == 'page' || $th_post_type == 'themo_tour' || $th_post_type == 'themo_portfolio' || $th_post_type == 'themo_room'){

                $page->add_control(
                    'themo_transparent_header',
                    [
                        'label' => __( 'Transparent Header', 'th-widget-pack' ),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'default' => 'Off',
                        'label_on' => __( 'On', 'th-widget-pack' ),
                        'label_off' => __( 'Off', 'th-widget-pack' ),
                        'return_value' => 'on',
                    ]
                );

                $page->add_control(
                    'themo_header_content_style',
                    [
                        'label' => __( 'Transparent Header Content Style', 'th-widget-pack' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'label_block' => true,
                        'default' => 'light',
                        'options' => [
                            'light' => __( 'Light', 'th-widget-pack' ),
                            'dark' => __( 'Dark', 'th-widget-pack' ),
                        ],
                        'condition' => [
                            'themo_transparent_header' => 'on',
                        ],
                    ]
                );

                $page->add_control(
                    'themo_alt_logo',
                    [
                        'label' => __( 'Use Alternative Logo', 'th-widget-pack' ),
                        'description' => __( 'You can upload an alternative logo under Appearance / Customize / Theme Options / Logo / ', 'th-widget-pack' ),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'default' => 'Off',
                        'label_on' => __( 'On', 'th-widget-pack' ),
                        'label_off' => __( 'Off', 'th-widget-pack' ),
                        'return_value' => 'on',
                        'condition' => [
                            'themo_transparent_header' => 'on',
                        ],
                    ]
                );

                $page_title_selector = get_option( 'elementor_page_title_selector' );
                if ( empty( $page_title_selector ) ) {
                    $page_title_selector = 'h1.entry-title';
                }


                $page->add_control(
                    'themo_page_title_margin',
                    [
                        'label' => __( 'Title  Margin', 'th-widget-pack' ),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'default' => [
                            'size' => 1,
                        ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 1000,
                                'step' => 5,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                            '{{WRAPPER}} ' . $page_title_selector => 'margin-top: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );
            }
        }




    }
}
// Bottom of section
if ( ! function_exists( 'th_add_custom_controls_elem_page_settings_bottom' ) ) {
    function th_add_custom_controls_elem_page_settings_bottom( \Elementor\Core\Settings\Page\Model $page )
    {

        if(isset($page) && $page->get_id() > "") {
            $th_post_type = false;
            $th_post_type = get_post_type($page->get_id());
            if ($th_post_type == 'page' || $th_post_type == 'themo_tour' || $th_post_type == 'themo_portfolio' || $th_post_type == 'themo_room') {

                $page->add_control(
                    'themo_page_layout',
                    [
                        'label' => __( 'Sidebar', 'th-widget-pack' ),
                        'type' => \Elementor\Controls_Manager::CHOOSE,
                        'default' => 'full',
                        'options' => [
                            'left'    => [
                                'title' => __( 'Left', 'th-widget-pack' ),
                                'icon' => 'fa fa-long-arrow-left',
                            ],
                            'full' => [
                                'title' => __( 'No Sidebar', 'th-widget-pack' ),
                                'icon' => 'fa fa-times',
                            ],
                            'right' => [
                                'title' => __( 'Right', 'th-widget-pack' ),
                                'icon' => 'fa fa-long-arrow-right',
                            ],

                        ],
                        'return_value' => 'yes',
                    ]
                );
            }
        }

    }
}
add_action( 'elementor/element/page-settings/section_page_settings/after_section_start', 'th_add_custom_controls_elem_page_settings_top',10, 2);
add_action( 'elementor/element/page-settings/section_page_settings/before_section_end', 'th_add_custom_controls_elem_page_settings_bottom',10, 2);

// Add Parallax Control (Switch) to Section Element in the Editor.
function add_elementor_section_background_controls( \Elementor\Element_Section $section ) {

    $section->add_control(
        'th_section_parallax',
        [
            'label' => __( 'Parallax', 'th-widget-pack' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_off' => __( 'Off', 'th-widget-pack' ),
            'label_on' => __( 'On', 'th-widget-pack' ),
            'default' => 'no',
        ]
    );
}

add_action( 'elementor/element/section/section_background/before_section_end', 'add_elementor_section_background_controls' );

// Render section backgrou]d parallax
function render_elementor_section_parallax_background( \Elementor\Element_Base $element ) {

    if('section' === $element->get_name()){

        if ( 'yes' === $element->get_settings( 'th_section_parallax' ) ) {

            //echo "<pre>";
            $th_background = $element->get_settings( 'background_image' );
            $th_background_URL = $th_background['url'];
            //echo "SECTION PARALLAX: ".$element->get_settings( 'th_section_parallax' );
            //echo "</pre>";

            $element->add_render_attribute( '_wrapper', [
                'class' => 'th-parallax',
                'data-parallax' => 'scroll',
                'data-image-src' => $th_background_URL,
            ] ) ;
        }
    }
}

add_action( 'elementor/frontend/element/before_render', 'render_elementor_section_parallax_background' );


// Future use - Get parallax working in Live Preview.
// https://github.com/pojome/elementor/issues/2588
/*add_action( 'elementor/element/print_template', function( $template, $element ) {
    if ( 'section' === $element->get_name() ) {
        echo '<pre>';
        echo 'OVERHERE';
        echo print_r($element);
        echo print_r($template);
        echo '</pre>';
        //$old_template = '<a href="\' + settings.link.url + \'">\' + title_html + \'</a>';
        //$new_template = '<a href="\' + settings.link.url + \'">\' + title_html + ( settings.link.is_external ? \'<i class="fa fa-external-link" aria-hidden="true"></i>\' : \'\' ) + \'</a>';
        $template = str_replace( 'data-id', 'data-id-zzz', $template );
    }

    return $template;
}, 10, 2 );*/