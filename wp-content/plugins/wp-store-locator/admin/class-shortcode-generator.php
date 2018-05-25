<?php
/**
 * Shortcode Generator class
 *
 * @author Tijmen Smit
 * @since  2.2.10
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Shortcode_Generator' ) ) {

    /**
     * Handle the generation of the WPSL shortcode through the media button
     *
     * @since 2.2.10
     */
    class WPSL_Shortcode_Generator {

        /**
         * Constructor
         */
        public function __construct() {
            add_action( 'media_buttons', array( $this, 'add_wpsl_media_button' ) );
            add_action( 'admin_init',    array( $this, 'show_thickbox_iframe_content' ) );
        }

        /**
         * Add the WPSL media button to the media button row
         *
         * @since 2.2.10
         * @return void
         */
        public function add_wpsl_media_button() {

            global $pagenow, $typenow;

            /* Make sure we're on a post/page or edit screen in the admin area */
            if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) && $typenow != 'wpsl_stores' ) {
                $changelog_link = self_admin_url( '?wpsl_media_action=store_locator&KeepThis=true&TB_iframe=true&width=783&height=800' );

                echo '<a href="' . esc_url( $changelog_link ) . '" class="thickbox button wpsl-thickbox" name="' . __( 'WP Store Locator' ,'wpsl' ) . '">' .  __( 'Insert Store Locator', 'wpsl' ) . '</a>';
            }
        }

        /**
         * Show the shortcode thickbox content
         *
         * @since 2.2.10
         * @return void
         */
        function show_thickbox_iframe_content() {

            global $wpsl_settings, $wpsl_admin;

            if ( empty( $_REQUEST['wpsl_media_action'] ) ) {
                return;
            }

            if ( !current_user_can( 'edit_pages' ) ) {
                wp_die( __( 'You do not have permission to perform this action', 'wpsl' ), __( 'Error', 'wpsl' ), array( 'response' => 403 ) );
            }

            $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

            // Make sure the required JS / CSS files are loaded in the Thickbox iframe
            wp_print_scripts( 'jquery-ui-core' );
            wp_print_scripts( 'jquery-ui-tabs' );
            wp_print_scripts( 'media-upload' );
            ?>
            <script type="text/javascript" src="<?php echo plugins_url( '/js/wpsl-shortcode-generator' . $min . '.js?ver='. WPSL_VERSION_NUM .'', __FILE__ ); ?>"></script>
            <?php
            wp_print_styles('buttons' );
            wp_print_styles('forms' );
            ?>

            <link rel="stylesheet" type="text/css" href="<?php echo plugins_url( '/css/style' . $min . '.css?ver='. WPSL_VERSION_NUM .'', __FILE__ ); ?>" media="all" />
            <style>
                body {
                    color: #444;
                    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                    font-size: 13px;
                    margin: 0;
                }

                #wpsl-media-tabs .ui-tabs-nav {
                     padding-left: 15px;
                     background: #fff !important;
                     border-bottom: 1px solid #dfdfdf;
                     border-collapse: collapse;
                     padding-top: .2em;
                 }

                #wpsl-media-tabs .ui-tabs-nav::after {
                    clear: both;
                    content: "";
                    display: table;
                    border-collapse: collapse;
                }

                #wpsl-media-tabs .ui-tabs-nav li {
                    list-style: none;
                    float: left;
                    position: relative;
                    top: 0;
                    margin: 1px .2em 0 0;
                    padding: 0;
                    white-space: nowrap;
                    border-bottom-width: 0;
                }

                #wpsl-media-tabs .ui-tabs-anchor {
                    float: left;
                    padding: .5em 1em;
                    text-decoration: none;
                    font-size: 14.3px;
                }

                #wpsl-media-tabs .ui-tabs-active a {
                    color: #212121;
                    cursor: text;
                }

                #wpsl-media-tabs .ui-tabs .ui-tabs-anchor {
                    float: left;
                    padding: .5em 1em;
                    text-decoration: none;
                }

                #wpsl-media-tabs.ui-widget-content {
                    border: none;
                    padding: 10px 0 0 0;
                }

                #wpsl-media-tabs .ui-tabs-anchor {
                    outline: none;
                }

                #wpsl-shortcode-config tr > td {
                    width: 25%;
                }

                #wpsl-markers-tab .wpsl-marker-list {
                    display: block;
                    overflow: hidden;
                    padding: 0;
                    list-style-type: none;
                }

                #wpsl-markers-tab .wpsl-marker-list li input {
                    padding: 0;
                    margin: 0;
                }

                #wpsl-shortcode-config .form-table,
                #wpsl-shortcode-config .form-table td,
                #wpsl-shortcode-config .form-table th,
                #wpsl-shortcode-config .form-table td p {
                    font-size: 13px;
                }

                #wpsl-shortcode-config .ui-tabs .ui-tabs-nav {
                    padding-left: 15px;
                    border-radius: 0;
                    margin: 0;
                }

                .wpsl-shortcode-markers {
                    padding: 0 10px;
                    margin-top: 27px;
                    font-size: 13px;
                }

                #wpsl-insert-shortcode {
                    margin-left: 19px;
                }

                #wpsl-shortcode-config .ui-state-default {
                    border: 1px solid #d3d3d3;
                    border-top-left-radius: 4px;
                    border-top-right-radius: 4px;
                    background: none;
                }

                #wpsl-shortcode-config .ui-state-default a {
                    color: #909090;
                }

                #wpsl-shortcode-config .ui-state-default.ui-tabs-active a {
                    color: #212121;
                }

                #wpsl-shortcode-config .ui-state-hover {
                    border-bottom: none;
                }

                #wpsl-shortcode-config .ui-state-hover a {
                    color: #72777c;
                }

                #wpsl-media-tabs .ui-state-active {
                    border: 1px solid #aaa;
                    border-bottom: 1px solid #fff;
                    padding-bottom: 0;
                }

                #wpsl-shortcode-config li.ui-tabs-active.ui-state-hover,
                #wpsl-shortcode-config li.ui-tabs-active {
                    border-bottom: 1px solid #fff;
                    padding-bottom: 0;
                }

                #wpsl-media-tabs li.ui-tabs-active {
                    margin-bottom: -1px;
                }

                #wpsl-general-tab,
                #wpsl-markers-tab {
                    border: 0;
                    padding: 1em 1.4em;
                    background: none;
                }

                @media ( max-width: 782px ) {
                    #wpsl-shortcode-config tr > td {
                        width: 100%;
                    }
                }
            </style>
            <div id="wpsl-shortcode-config" class="wp-core-ui">
                <div id="wpsl-media-tabs">
                    <ul>
                        <li><a href="#wpsl-general-tab"><?php _e( 'General Options', 'wpsl' ); ?></a></li>
                        <li><a href="#wpsl-markers-tab"><?php _e('Markers', 'wpsl' ); ?></a></li>
                    </ul>
                    <div id="wpsl-general-tab">
                        <table class="form-table wpsl-shortcode-config">
                            <tbody>
                            <tr>
                                <td><label for="wpsl-store-template"><?php _e('Select the used template', 'wpsl' ); ?></label></td>
                                <td><?php echo $wpsl_admin->settings_page->show_template_options(); ?></td>
                            </tr>
                            <tr>
                                <td><label for="wpsl-start-location"><?php _e( 'Start point', 'wpsl' ); ?></label><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'If nothing it set, then the start point from the %ssettings%s page is used.', '' ), '<a href=' . admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_settings#wpsl-map-settings' ) . '>', '</a>'  ); ?></span></span></p></td>
                                <td><input type="text" placeholder="Optional" value="" id="wpsl-start-location"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="wpsl-auto-locate"><?php _e( 'Attempt to auto-locate the user', 'wpsl' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'Most modern browsers %srequire%s a HTTPS connection before the Geolocation feature works.', 'wpsl_csv' ), '<a href="https://wpstorelocator.co/document/html-5-geolocation-not-working/">', '</a>' ); ?></span></span></label>
                                </td>
                                <td><input type="checkbox" value="" <?php checked( $wpsl_settings['auto_locate'], true ); ?> name="wpsl_map[auto_locate]" id="wpsl-auto-locate"></td>
                            </tr>
                            <?php
                            $terms = get_terms( 'wpsl_store_category', 'hide_empty=1' );

                            if ( $terms ) {
                                ?>
                                <tr>
                                    <td><label for="wpsl-cat-filter-types"><?php _e( 'Category filter type', 'wpsl' ); ?></label></p></td>
                                    <td>
                                        <select id="wpsl-cat-filter-types" autocomplete="off">
                                            <option value="" selected="selected"><?php _e( 'None', 'wpsl' ); ?></option>
                                            <option value="dropdown"><?php _e( 'Dropdown', 'wpsl' ); ?></option>
                                            <option value="checkboxes"><?php _e( 'Checkboxes', 'wpsl' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="wpsl-cat-restriction">
                                    <td style="vertical-align:top;"><label for="wpsl-cat-restriction"><?php _e('Automatically restrict the returned results to one or more categories?', 'wpsl' ); ?></label></td>
                                    <td>
                                        <?php
                                        $cat_restricton = '<select id="wpsl-cat-restriction" multiple="multiple" autocomplete="off">';

                                        foreach ( $terms as $term ) {
                                            $cat_restricton .= '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
                                        }

                                        $cat_restricton .= '</select>';

                                        echo $cat_restricton;
                                        ?>
                                    </td>
                                </tr>
                                <tr class="wpsl-cat-selection wpsl-hide">
                                    <td style="vertical-align:top;"><label for="wpsl-cat-selection"><?php _e('Set a selected category?', 'wpsl' ); ?></label></td>
                                    <td>
                                        <?php
                                        $cat_selection = '<select id="wpsl-cat-selection" autocomplete="off">';

                                        $cat_selection .= '<option value="" selected="selected">' . __( 'Select category', 'wpsl' ) . '</option>';

                                        foreach ( $terms as $term ) {
                                            $cat_selection .= '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
                                        }

                                        $cat_selection .= '</select>';

                                        echo $cat_selection;
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr class="wpsl-checkbox-options wpsl-hide">
                                <td><label for="wpsl-checkbox-columns"><?php _e('Checkbox columns', 'wpsl' ); ?></label></td>
                                <td>
                                    <?php
                                    echo '<select id="wpsl-checkbox-columns">';

                                    $i = 1;

                                    while ( $i <= 4 ) {
                                        $selected = ( $i == 3 ) ? "selected='selected'" : ''; // 3 is the default

                                        echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                        $i++;
                                    }

                                    echo '</select>';
                                    ?>
                                </td>
                            </tr>
                            <tr class="wpsl-checkbox-selection wpsl-hide">
                                <td><label for="wpsl-checkbox-columns"><?php _e('Set selected checkboxes', 'wpsl' ); ?></label></td>
                                <td>
                                    <?php
                                    $checkbox_selection = '<select id="wpsl-checkbox-selection" multiple="multiple" autocomplete="off">';

                                    foreach ( $terms as $term ) {
                                        $checkbox_selection .= '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
                                    }

                                    $checkbox_selection .= '</select>';

                                    echo $checkbox_selection;
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="wpsl-map-type"><?php _e( 'Map type', 'wpsl' ); ?>:</label></td>
                                <td><?php echo $wpsl_admin->settings_page->create_dropdown( 'map_types' ); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="wpsl-markers-tab">
                        <div class="wpsl-shortcode-markers">
                            <?php echo $wpsl_admin->settings_page->show_marker_options(); ?>
                        </div>
                    </div>
                </div>

                <p class="submit">
                    <input type="button" id="wpsl-insert-shortcode" class="button-primary" value="<?php echo _e( 'Insert Store Locator', 'wpsl' ); ?>" onclick="WPSL_InsertShortcode();" />
                </p>
            </div>

            <?php

            exit();
        }
    }

    new WPSL_Shortcode_Generator();
}