<?php
/**
 * Handle the metaboxes
 *
 * @author Tijmen Smit
 * @since  2.0.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Metaboxes' ) ) {

    /**
     * Handle the meta boxes
     *
     * @since 2.0.0
     */
    class WPSL_Metaboxes {

        public function __construct() {
            add_action( 'add_meta_boxes',        array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post',             array( $this, 'save_post' ) );
            add_action( 'post_updated_messages', array( $this, 'store_update_messages' ) );
        }

        /**
         * Add the meta boxes.
         *
         * @since 2.0.0
         * @return void
         */
        public function add_meta_boxes() {
            add_meta_box( 'wpsl-store-details', __( 'Store Details', 'wpsl' ), array( $this, 'create_meta_fields' ), 'wpsl_stores', 'normal', 'high' );
            add_meta_box( 'wpsl-map-preview', __( 'Store Map', 'wpsl' ), array( $this, 'map_preview' ), 'wpsl_stores', 'side' );
        }

        /**
         * The store locator meta box fields.
         *
         * @since 2.0.0
         * @return array $meta_fields The meta box fields used for the store details
         */
        public function meta_box_fields() {

            global $wpsl_settings;

            $meta_fields = array(
                __( 'Location', 'wpsl' ) => array(
                    'address' => array(
                        'label'    => __( 'Address', 'wpsl' ),
                        'required' => true
                    ),
                    'address2' => array(
                        'label' => __( 'Address 2', 'wpsl' )
                    ),
                    'city' => array(
                        'label'    => __( 'City', 'wpsl' ),
                        'required' => true
                    ),
                    'state' => array(
                        'label' => __( 'State', 'wpsl' )
                    ),
                    'zip' => array(
                        'label' => __( 'Zip Code', 'wpsl' )
                    ),
                    'country' => array(
                        'label'    => __( 'Country', 'wpsl' ),
                        'required' => true
                    ),
                    'country_iso' => array(
                        'type' => 'hidden'
                    ),
                    'lat' => array(
                        'label' => __( 'Latitude', 'wpsl' )
                    ),
                    'lng' => array(
                        'label' => __( 'Longitude', 'wpsl' )
                    )
                ),
                __( 'Opening Hours', 'wpsl' ) => array(
                    'hours' => array(
                        'label' => __( 'Hours', 'wpsl' ),
                        'type'  => $wpsl_settings['editor_hour_input'] //Either set to textarea or dropdown. This is defined through the 'Opening hours input format: ' option on the settings page
                    )
                ),
                __( 'Additional Information', 'wpsl' ) => array(
                    'phone' => array(
                        'label' => __( 'Tel', 'wpsl' )
                    ),
                    'fax' => array(
                        'label' => __( 'Fax', 'wpsl' )
                    ),
                    'email' => array(
                        'label' => __( 'Email', 'wpsl' )
                    ),
                    'url' => array(
                        'label' => __( 'Url', 'wpsl' )
                    )
                )
            );

            return apply_filters( 'wpsl_meta_box_fields', $meta_fields );
        }

        /**
         * Create the store locator metabox input fields.
         *
         * @since 2.0.0
         * @return void
         */
        function create_meta_fields() {

            global $wpsl_settings, $wp_version;

            $i         = 0;
            $j         = 0;
            $tab_items = '';

            wp_nonce_field( 'save_store_meta', 'wpsl_meta_nonce' );
            ?>

            <div class="wpsl-store-meta <?php if ( floatval( $wp_version ) < 3.8 ) { echo 'wpsl-pre-38'; } // Fix CSS issue with < 3.8 versions ?>">
                <?php

                // Create the tab navigation for the meta boxes.
                foreach ( $this->meta_box_fields() as $tab => $meta_fields ) {
                    $active_class = ( $i == 0 ) ? ' wpsl-active' : '';

                    if ( $wpsl_settings['hide_hours'] && $tab == __( 'Opening Hours', 'wpsl' ) ) {
                        continue;
                    } else {
                        $tab_items .= $this->meta_field_nav( $tab, $active_class );
                    }

                    $i++;
                }

                echo '<ul id="wpsl-meta-nav">' . $tab_items . '</ul>';

                // Create the input fields for the meta boxes.
                foreach ( $this->meta_box_fields() as $tab => $meta_fields ) {
                    $active_class = ( $j == 0 ) ? ' wpsl-active' : '';

                    if ( $wpsl_settings['hide_hours'] && $tab == __( 'Opening Hours', 'wpsl' ) ) {
                        continue;
                    } else {
                        echo '<div class="wpsl-tab wpsl-' . esc_attr( strtolower( str_replace( ' ', '-', $tab ) ) ) . $active_class . '">';

                        foreach ( $meta_fields as $field_key => $field_data ) {

                            // If no specific field type is set, we set it to text.
                            $field_type = ( empty( $field_data['type'] ) ) ? 'text' : $field_data['type'];
                            $args = array(
                                'key'  => $field_key,
                                'data' => $field_data
                            );

                            // Check for a class method, otherwise enable a plugin hook.
                            if ( method_exists( $this, $field_type . '_input' ) ) {
                                call_user_func( array( $this, $field_type . '_input' ), $args );
                            } else {
                                do_action( 'wpsl_metabox_' . $field_type . '_input', $args );
                            }
                        }

                        echo '</div>';
                    }

                    $j++;
                }
                ?>
            </div>
            <?php
        }

        /**
         * Create the li elements that are used in the tabs above the store meta fields.
         *
         * @since 2.0.0
         * @param  string $tab          The name of the tab
         * @param  string $active_class Either the class name or empty
         * @return string $nav_item     The HTML for the nav list
         */
        public function meta_field_nav( $tab, $active_class ) {

            $tab_lower = strtolower( str_replace( ' ', '-', $tab ) );
            $nav_item  = '<li class="wpsl-' . esc_attr( $tab_lower ) . '-tab ' . $active_class . '"><a href="#wpsl-' . esc_attr( $tab_lower ) . '">' . esc_html( $tab ) . '</a></li>';

            return $nav_item;
        }

        /**
         * Set the CSS class that tells JS it's an required input field.
         *
         * @since 2.0.0
         * @param  array       $args     The css classes
         * @param  string      $single   Whether to return just the class name, or also include the class=""
         * @return string|void $response The required CSS class or nothing
         */
        public function set_required_class( $args, $single = false ) {

            if ( isset( $args['required'] ) && ( $args['required'] ) ) {
                if ( !$single ) {
                    $response = 'class="wpsl-required"';
                } else {
                    $response = 'wpsl-required';
                }

                return $response;
            }
        }

        /**
         * Check if the current field is required.
         *
         * @since 2.0.0
         * @param  array $args The CSS classes
         * @return string|void The HTML for the required element or nothing
         */
        public function is_required_field( $args ) {

            if ( isset( $args['required'] ) && ( $args['required'] ) ) {
                $response = '<span class="wpsl-star"> *</span>';

                return $response;
            }
        }

        /**
         * Get the prefilled field data.
         *
         * @since 2.0.0
         * @param  string $field_name The name of the field to get the data for
         * @return string $field_data The field data
         */
        public function get_prefilled_field_data( $field_name ) {

            global $wpsl_settings, $pagenow;

            $field_data = '';

            // Prefilled values are only used for new pages, not when a user edits an existing page.
            if ( $pagenow == 'post.php' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
                return;
            }

            $prefilled_fields = array(
                'country',
                'hours'
            );

            if ( in_array( $field_name, $prefilled_fields ) ) {
                $field_data = $wpsl_settings['editor_' . $field_name];
            }

            return $field_data;
        }

        /**
         * Create a text input field.
         *
         * @since 2.0.0
         * @param array $args The input name and label
         * @return void
         */
        public function text_input( $args ) {

            $saved_value = $this->get_store_meta( $args['key'] );

            // If there is no existing meta value, check if a prefilled value exists for the input field.
            if ( !$saved_value ) {
                $saved_value = $this->get_prefilled_field_data( $args['key'] );
            }
            ?>

            <p>
                <label for="wpsl-<?php echo esc_attr( $args['key'] ); ?>"><?php echo esc_html( $args['data']['label'] ) . ': ' . $this->is_required_field( $args['data'] ); ?></label>
                <input id="wpsl-<?php echo esc_attr( $args['key'] ); ?>" <?php echo $this->set_required_class( $args['data'] ); ?> type="text" name="wpsl[<?php echo esc_attr( $args['key'] ); ?>]" value="<?php echo esc_attr( $saved_value ); ?>" />
            </p>

            <?php
        }

        /**
         * Create a hidden input field.
         *
         * @since 2.0.0
         * @param array $args The name of the meta value
         * @return void
         */
        public function hidden_input( $args ) {

            $saved_value = $this->get_store_meta( $args['key'] );
            ?>

            <input id="wpsl-<?php echo esc_attr( $args['key'] ); ?>" type="hidden" name="wpsl[<?php echo esc_attr( $args['key'] ); ?>]" value="<?php echo esc_attr( $saved_value ); ?>" />

            <?php
        }

        /**
         * Create a textarea field.
         *
         * @since 2.0.0
         * @param array $args The textarea name and label
         * @return void
         */
        public function textarea_input( $args ) {

            $saved_value = $this->get_store_meta( $args['key'] );

            if ( $args['key'] == 'hours' && gettype( $saved_value ) !== 'string' ) {
                $saved_value = '';
            }

            // If there is no existing meta value, check if a prefilled value exists for the textarea.
            if ( !$saved_value ) {
                $prefilled_value = $this->get_prefilled_field_data( $args['key'] );

                if ( isset( $prefilled_value['textarea'] ) ) {
                    $saved_value = $prefilled_value['textarea'];
                }
            }
            ?>

            <p>
                <label for="wpsl-<?php echo esc_attr( $args['key'] ); ?>"><?php echo esc_html( $args['data']['label'] ) . ': ' . $this->is_required_field( $args['data'] ); ?></label>
                <textarea id="wpsl-<?php echo esc_attr( $args['key'] ); ?>" <?php echo $this->set_required_class( $args['data'] ); ?> name="wpsl[<?php echo esc_attr( $args['key'] ); ?>]" cols="5" rows="5"><?php echo esc_html( $saved_value ); ?></textarea>
            </p>

            <?php
        }

        /**
         * Create a wp editor field.
         *
         * @since 2.1.1
         * @param array $args The wp editor name and label
         * @return void
         */
        public function wp_editor_input( $args ) {

            $saved_value = $this->get_store_meta( $args['key'] );
            ?>

            <p>
                <label for="wpsl-<?php echo esc_attr( $args['key'] ); ?>"><?php echo esc_html( $args['data']['label'] ) . ': ' . $this->is_required_field( $args['data'] ); ?></label>
                <?php wp_editor( $saved_value, 'wpsleditor_' . wpsl_random_chars(), $settings = array('textarea_name' => 'wpsl['. esc_attr( $args['key'] ).']') ); ?>
            </p>

            <?php
        }

        /**
         * Create a checkbox field.
         *
         * @since 2.0.0
         * @param array $args The checkbox name and label
         * @return void
         */
        public function checkbox_input( $args ) {

            $saved_value = $this->get_store_meta( $args['key'] );
            ?>

            <p>
                <label for="wpsl-<?php echo esc_attr( $args['key'] ); ?>"><?php echo esc_html( $args['data']['label'] ) . ': ' . $this->is_required_field( $args['data'] ); ?></label>
                <input id="wpsl-<?php echo esc_attr( $args['key'] ); ?>" <?php echo $this->set_required_class( $args['data'] ); ?> type="checkbox" name="wpsl[<?php echo esc_attr( $args['key'] ); ?>]" <?php checked( $saved_value, true ); ?> value="1" />
            </p>

            <?php
        }

        /**
         * Create a dropdown field.
         *
         * @since 2.0.0
         * @param array $args The dropdown name and label
         * @return void
         */
        public function dropdown_input( $args ) {

            // The hour dropdown requires a different structure with multiple dropdowns.
            if ( $args['key'] == 'hours' ) {
                $this->opening_hours();
            } else {
                $option_list = $args['data']['options'];
                $saved_value = $this->get_store_meta( $args['key'] );
                ?>

                <p>
                    <label for="wpsl-<?php echo esc_attr( $args['key'] ); ?>"><?php echo esc_html( $args['data']['label'] ) . ': ' . $this->is_required_field( $args['data'] ); ?></label>
                    <select id="wpsl-<?php echo esc_attr( $args['key'] ); ?>" <?php echo $this->set_required_class( $args['data'] ); ?>  name="wpsl[<?php echo esc_attr( $args['key'] ); ?>]" autocomplete="off" />
                    <?php foreach ( $option_list as $key => $option ) { ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php if ( isset( $saved_value ) ) { selected( $saved_value, $key ); } ?>><?php echo esc_html( $option ); ?></option>
                    <?php } ?>
                    </select>
                </p>

                <?php
            }
        }

        /**
         * Create the openings hours table with the hours as dropdowns.
         *
         * @since 2.0.0
         * @param string $location The location were the opening hours are shown.
         * @return void
         */
        public function opening_hours( $location = 'store_page' ) {

            global $wpsl_settings, $wpsl_admin, $post;

            $name          = ( $location == 'settings' ) ? 'wpsl_editor[dropdown]' : 'wpsl[hours]'; // the name of the input or select field
            $opening_days  = wpsl_get_weekdays();
            $opening_hours = '';
            $hours         = '';

            if ( $location == 'store_page' ) {
                $opening_hours = get_post_meta( $post->ID, 'wpsl_hours' );
            }

            // If we don't have any opening hours, we use the defaults.
            if ( !isset( $opening_hours[0]['monday'] ) ) {
                $opening_hours = $wpsl_settings['editor_hours']['dropdown'];
            } else {
                $opening_hours = $opening_hours[0];
            }

            // Find out whether we have a 12 or 24hr format.
            $hour_format = $this->find_hour_format( $opening_hours );

            if ( $hour_format == 24 ) {
                $hour_class = 'wpsl-twentyfour-format';
            } else {
                $hour_class = 'wpsl-twelve-format';
            }

            /*
             * Only include the 12 / 24hr dropdown switch if we are on store page,
             * otherwise just show the table with the opening hour dropdowns.
             */
            if ( $location == 'store_page' ) {
                ?>
                <p class="wpsl-hours-dropdown">
                    <label for="wpsl-editor-hour-input"><?php _e( 'Hour format', 'wpsl' ); ?>:</label>
                    <?php echo $wpsl_admin->settings_page->show_opening_hours_format( $hour_format ); ?>
                </p>
            <?php } ?>

                <table id="wpsl-store-hours" class="<?php echo $hour_class; ?>">
                    <tr>
                        <th><?php _e( 'Days', 'wpsl' ); ?></th>
                        <th><?php _e( 'Opening Periods', 'wpsl' ); ?></th>
                        <th></th>
                    </tr>
                    <?php
                    foreach ( $opening_days as $index => $day ) {
                        $i          = 0;
                        $hour_count = count( $opening_hours[$index] );
                        ?>
                        <tr>
                            <td class="wpsl-opening-day"><?php echo esc_html( $day ); ?></td>
                            <td id="wpsl-hours-<?php echo esc_attr( $index ); ?>" class="wpsl-opening-hours" data-day="<?php echo esc_attr( $index ); ?>">
                                <?php
                                if ( $hour_count > 0 ) {
                                    // Loop over the opening periods.
                                    while ( $i < $hour_count ) {
                                        if ( isset( $opening_hours[$index][$i] ) ) {
                                            $hours = explode( ',', $opening_hours[$index][$i] );
                                        } else {
                                            $hours = '';
                                        }

                                        // If we don't have two parts or one of them is empty, then we set the store to closed.
                                        if ( ( count( $hours ) == 2 ) && ( !empty( $hours[0] ) ) && ( !empty( $hours[1] ) ) ) {
                                            $args = array(
                                                'day'         => $index,
                                                'name'        => $name,
                                                'hour_format' => $hour_format,
                                                'hours'       => $hours
                                            );
                                            ?>
                                            <div class="wpsl-current-period <?php if ( $i > 0 ) { echo 'wpsl-multiple-periods'; } ?>">
                                                <?php echo $this->opening_hours_dropdown( $args, 'open' ); ?>
                                                <span> - </span>
                                                <?php echo $this->opening_hours_dropdown( $args, 'close' ); ?>
                                                <div class="wpsl-icon-cancel-circled"></div>
                                            </div>
                                            <?php
                                        } else {
                                            $this->show_store_closed( $name, $index );
                                        }

                                        $i++;
                                    }
                                } else {
                                    $this->show_store_closed( $name, $index );
                                }
                                ?>
                            </td>
                            <td>
                                <div class="wpsl-add-period">
                                    <div class="wpsl-icon-plus-circled"></div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            <?php
        }

        /**
         * Show the 'store closed' message.
         *
         * @since 2.0.0
         * @param  string $name The name for the input field
         * @param  string $day  The day the store is closed
         * @return void
         */
        public function show_store_closed( $name, $day ) {
            echo '<p class="wpsl-store-closed">' . __( 'Closed', 'wpsl' ) . '<input type="hidden" name="' . esc_attr( $name ) . '[' . esc_attr( $day ) . ']" value="closed"></p>';
        }

        /**
         * Find out whether the opening hours are set in the 12 or 24hr format.
         *
         * We use this to determine the selected value for the dropdown in the store editor.
         * So a user can decide to change the opening hour format.
         *
         * @since 2.0.0
         * @param  array  $opening_hours The opening hours for the whole week
         * @return string The hour format used in the opening hours
         */
        public function find_hour_format( $opening_hours ) {

            $week_days = wpsl_get_weekdays();

            foreach ( $week_days as $key => $day ) {
                if ( isset( $opening_hours[$key][0] ) ) {
                    $time = $opening_hours[$key][0];

                    if ( ( strpos( $time, 'AM' ) !== false ) || ( strpos( $time, 'PM' ) !== false ) ) {
                        return '12';
                    } else {
                        return '24';
                    }
                }
            }
        }

        /**
         * Create the opening hours dropdown.
         *
         * @since 2.0.0
         * @param  array  $args   The data to create the opening hours dropdown
         * @param  string $period Either set to open or close
         * @return string $select The html for the dropdown
         */
        public function opening_hours_dropdown( $args, $period ) {

            $select_index  = ( $period == 'open' ) ? 0 : 1;
            $selected_time = $args['hours'][$select_index];
            $select_name   = $args['name'] . '[' . strtolower( $args['day'] ) . '_' . $period . ']';
            $open          = strtotime( '12:00am' );
            $close         = strtotime( '11:59pm' );
            $hour_interval = 900;

            if ( $args['hour_format'] == 12 ) {
                $format = 'g:i A';
            } else {
                $format = 'H:i';
            }

            $select = '<select class="wpsl-' . esc_attr( $period ) . '-hour" name="' . esc_attr( $select_name ) . '[]" autocomplete="off">';

            for ( $i = $open; $i <= $close; $i += $hour_interval ) {

                // If the selected time matches the current time then we set it to active.
                if ( $selected_time == date( $format, $i ) ) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }

                $select .= "<option value='" . date( $format, $i ) . "' $selected>" . date( $format, $i ) . "</option>";
            }

            $select .= '</select>';

            return $select;
        }

        /**
         * Get the store post meta.
         *
         * @since 2.0.0
         * @param  string     $key        The name of the meta value
         * @return mixed|void $store_meta Meta value for the store field
         */
        public function get_store_meta( $key ) {

            global $post;

            $store_meta = get_post_meta( $post->ID, 'wpsl_' . $key, true );

            if ( $store_meta ) {
                return $store_meta;
            } else {
                return;
            }
        }

        /**
         * Save the custom post data.
         *
         * @since 2.0.0
         * @param  integer $post_id store post ID
         * @return void
         */
        public function save_post( $post_id ) {

            global $wpsl_admin;

            if ( empty( $_POST['wpsl_meta_nonce'] ) || !wp_verify_nonce( $_POST['wpsl_meta_nonce'], 'save_store_meta' ) )
                return;

            if ( !isset( $_POST['post_type'] ) || 'wpsl_stores' !== $_POST['post_type'] )
                return;

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return;

            if ( is_int( wp_is_post_revision( $post_id ) ) )
                return;

            if ( !current_user_can( 'edit_post', $post_id ) )
                return;

            $this->store_data = $_POST['wpsl'];

            // Check if the hours are set through dropdowns.
            if ( isset( $this->store_data['hours'] ) && is_array( $this->store_data['hours'] ) && ( !empty( $this->store_data['hours'] ) ) ) {
                $this->store_data['hours'] = $this->format_opening_hours();
            }

            // Loop over the meta fields defined in the meta_box_fields and update the post meta data.
            foreach ( $this->meta_box_fields() as $tab => $meta_fields ) {
                foreach ( $meta_fields as $field_key => $field_data ) {

                    // Either update or delete the post meta.
                    if ( isset( $this->store_data[ $field_key ] ) && ( $this->store_data[ $field_key ] != "" ) ) {
                        if ( isset( $field_data['type'] ) && $field_data['type'] ) {
                            $field_type = $field_data['type'];
                        } else {
                            $field_type = '';
                        }

                        switch ( $field_type ) {
                            case 'thumbnail':
                                update_post_meta( $post_id, 'wpsl_' . $field_key, absint( $this->store_data[ $field_key ] ) );
                                break;
                            case 'checkbox':
                                $checkbox_val = ( isset( $this->store_data[ $field_key ] ) ) ? 1 : 0;
                                update_post_meta( $post_id, 'wpsl_' . $field_key, $checkbox_val );
                                break;
                            case 'wp_editor':
                            case 'textarea':
                                update_post_meta( $post_id, 'wpsl_' . $field_key, wp_kses_post( trim( stripslashes( $this->store_data[ $field_key ] ) ) ) );
                                break;
                            default:
                                if ( is_array( $this->store_data[ $field_key ] ) ) {
                                    if ( wpsl_is_multi_array( $this->store_data[ $field_key ] ) ) {
                                        array_walk_recursive( $this->store_data[ $field_key ], 'wpsl_sanitize_multi_array' );
                                        update_post_meta( $post_id, 'wpsl_' . $field_key, $this->store_data[ $field_key ] );
                                    } else {
                                        update_post_meta( $post_id, 'wpsl_' . $field_key, array_map( 'sanitize_text_field', $this->store_data[ $field_key ] ) );
                                    }
                                } else {
                                    update_post_meta( $post_id, 'wpsl_' . $field_key, sanitize_text_field( $this->store_data[ $field_key ] ) );
                                }
                                break;
                        }
                    } else {
                        delete_post_meta( $post_id, 'wpsl_' . $field_key );
                    }
                }
            }

            do_action( 'wpsl_save_post', $this->store_data );

            /*
             * If all the required fields contain data, then check if we need to
             * geocode the address and if we should delete the autoload transient.
             *
             * Otherwise show a notice for 'missing data' and set the post status to pending.
             */
            if ( !$this->check_missing_meta_data( $post_id ) ) {
                $wpsl_admin->geocode->check_geocode_data( $post_id, $this->store_data );
                $wpsl_admin->maybe_delete_autoload_transient( $post_id );
            } else {
                $wpsl_admin->notices->save( 'error', __( 'Failed to publish the store. Please fill in the required store details.', 'wpsl' ) );
                $this->set_post_pending( $post_id );
            }
        }

        /**
         * Loop through the opening hours and structure the data in a new array.
         *
         * @since 2.0.0
         * @return array $opening_hours The formatted opening hours
         */
        public function format_opening_hours() {

            $week_days = wpsl_get_weekdays();

            // Use the opening hours from the editor page or the add/edit store page.
            if ( isset( $_POST['wpsl_editor']['dropdown'] ) ) {
                $store_hours = $_POST['wpsl_editor']['dropdown'];
            } else if ( isset( $this->store_data['hours'] ) ) {
                $store_hours = $this->store_data['hours'];
            }

            foreach ( $week_days as $day => $value ) {
                $i       = 0;
                $periods = array();

                if ( isset( $store_hours[$day . '_open'] ) && $store_hours[$day . '_open'] ) {
                    foreach ( $store_hours[$day . '_open'] as $opening_hour ) {
                        $hours     = $this->validate_hour( $store_hours[$day.'_open'][$i] ) . ',' . $this->validate_hour( $store_hours[$day.'_close'][$i] );
                        $periods[] = $hours;
                        $i++;
                    }
                }

                $opening_hours[$day] = $periods;
            }

            return $opening_hours;
        }

        /*
         * Validate the 12 or 24 hr time format.
         *
         * @since 2.0.0
         * @param string $hour The opening hour
         * @return boolean true if the $hour format is valid
         */
        public function validate_hour( $hour ) {

            global $wpsl_settings;

            /*
             * On the add/edit store we can always use the $wpsl_settings value.
             * But if validate_hour is called from the settings page then we
             * should use the $_POST value to make sure we have the correct value.
             */
            if ( isset( $_POST['wpsl_editor']['hour_format'] ) ) {
                $hour_format = ( $_POST['wpsl_editor']['hour_format'] == 12 ) ? 12 : 24;
            } else {
                $hour_format = $wpsl_settings['editor_hour_format'];
            }

            if ( $hour_format == 12 ) {
                $format = 'g:i A';
            } else {
                $format = 'H:i';
            }

            if ( date( $format, strtotime( $hour ) ) == $hour ) {
                return $hour;
            }
        }

        /**
         * Set the post status to pending instead of publish.
         *
         * @since 2.0.0
         * @param integer $post_id store post ID
         * @return void
         */
        public function set_post_pending( $post_id ) {

            global $wpdb;

            $wpdb->update( $wpdb->posts, array( 'post_status' => 'pending' ), array( 'ID' => $post_id ) );

            add_filter( 'redirect_post_location', array( $this, 'remove_message_arg' ) );
        }

        /**
         * Remove the message query arg.
         *
         * If one or more of the required fields are empty, we show a custom msg.
         * So no need for the normal post update messages arg.
         *
         * @since 2.0.0
         * @param string $location The destination url
         * @return void
         */
        public function remove_message_arg( $location ) {
            return remove_query_arg( 'message', $location );
        }

        /**
         * Make sure all the required post meta fields contain data.
         *
         * @since 2.0.0
         * @param integer $post_id store post ID
         * @return boolean
         */
        public function check_missing_meta_data( $post_id ) {

            foreach ( $this->meta_box_fields() as $tab => $meta_fields ) {
                foreach ( $meta_fields as $field_key => $field_data ) {

                    if ( isset( $field_data['required'] ) && $field_data['required'] ) {
                        $post_meta = get_post_meta( $post_id, 'wpsl_' . $field_key, true );

                        if ( empty( $post_meta ) ) {
                            return true;
                        }
                    }
                }
            }
        }

        /**
         * The html for the map preview in the sidebar.
         *
         * @since 2.0.0
         * @return void
         */
        public function map_preview() {
            ?>
            <div id="wpsl-gmap-wrap"></div>
            <p class="wpsl-submit-wrap">
                <a id="wpsl-lookup-location" class="button-primary" href="#wpsl-meta-nav"><?php _e( 'Preview Location', 'wpsl' ); ?></a>
                <span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'The map preview is based on the provided address, city and country details. %s It will ignore any custom latitude or longitude values.', 'wpsl' ), '<br><br>' ); ?></span></span>
                <em class="wpsl-desc"><?php _e( 'You can drag the marker to adjust the exact location of the marker.', 'wpsl' ); ?></em>
            </p>
            <?php
        }

        /**
         * Store update messages.
         *
         * @since 2.0.0
         * @param  array $messages Existing post update messages.
         * @return array $messages Amended post update messages with new CPT update messages.
         */
        function store_update_messages( $messages ) {

            $post             = get_post();
            $post_type        = get_post_type( $post );
            $post_type_object = get_post_type_object( $post_type );

            $messages['wpsl_stores'] = array(
                0  => '', // Unused. Messages start at index 1.
                1  => __( 'Store updated.', 'wpsl' ),
                2  => __( 'Custom field updated.', 'wpsl' ),
                3  => __( 'Custom field deleted.', 'wpsl' ),
                4  => __( 'Store updated.', 'wpsl' ),
                5  => isset( $_GET['revision'] ) ? sprintf( __( 'Store restored to revision from %s', 'wpsl' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                6  => __( 'Store published.', 'wpsl' ),
                7  => __( 'Store saved.', 'wpsl' ),
                8  => __( 'Store submitted.', 'wpsl' ),
                9  => sprintf(
                    __( 'Store scheduled for: <strong>%1$s</strong>.', 'wpsl' ),
                    date_i18n( __( 'M j, Y @ G:i', 'wpsl' ), strtotime( $post->post_date ) )
                ),
                10 => __( 'Store draft updated.', 'wpsl' )
            );

            if ( ( 'wpsl_stores' == $post_type ) && ( $post_type_object->publicly_queryable ) ) {
                $permalink = get_permalink( $post->ID );

                $view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View store', 'wpsl' ) );
                $messages[ $post_type ][1] .= $view_link;
                $messages[ $post_type ][6] .= $view_link;
                $messages[ $post_type ][9] .= $view_link;

                $preview_permalink = add_query_arg( 'preview', 'true', $permalink );
                $preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview store', 'wpsl' ) );
                $messages[ $post_type ][8]  .= $preview_link;
                $messages[ $post_type ][10] .= $preview_link;
            }

            return $messages;
        }

    }
}