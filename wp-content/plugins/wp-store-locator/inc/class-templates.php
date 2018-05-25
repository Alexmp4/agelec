<?php
/**
 * Handle the WPSL and Add-on templates
 *
 * @author Tijmen Smit
 * @since  2.2.11
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Templates' ) ) {

    class WPSL_Templates {

        /**
         * Get the list of available templates
         *
         * @since 2.2.11
         * @param string $type The template type to return
         * @return array|void
         */
        public function get_template_list( $type = 'store_locator' ) {

            $template_list = array();

            // Add the WPSL templates or the add-on templates.
            if ( $type == 'store_locator' ) {
                $template_list['store_locator'] = wpsl_get_templates();
            } else {
                $template_list = apply_filters( 'wpsl_template_list', $template_list );
            }

            if ( isset( $template_list[$type] ) && !empty( $template_list[$type] ) ) {
                return $template_list[$type];
            }
        }

        /**
         * Get the template details
         *
         * @since 2.2.11
         * @param  string $used_template The name of the template
         * @param  string $type          The type of template data to load
         * @return array  $template_data The template data ( id, name, path )
         */
        public function get_template_details( $used_template, $type = 'store_locator' ) {

            $used_template = ( empty( $used_template ) ) ? 'default' : $used_template;
            $templates     = $this->get_template_list( $type );
            $template_data = '';
            $template_path = '';

            if ( $templates ) {
                // Grab the the correct template data from the available templates.
                foreach ( $templates as $template ) {
                    if ( $used_template == $template['id'] ) {
                        $template_data = $template;
                        break;
                    }
                }
            }

            // Old structure ( WPSL only ) was only the path, new structure ( add-ons ) expects the file name as well.
            if ( isset( $template_data['path'] ) && isset( $template_data['file_name'] ) ) {
                $template_path = $template_data['path'] . $template_data['file_name'];
            } else if ( isset( $template_data['path'] ) ) {
                $template_path = $template_data['path'];
            }

            // If no match exists, or the template file doesnt exist, then use the default template.
            if ( !$template_data || ( !file_exists( $template_path ) ) ) {
                $template_data = $this->get_default_template( $type );

                // If no template can be loaded, then show a msg to the admin user.
                if ( !$template_data && current_user_can( 'administrator' ) ) {
                    echo '<p>' . sprintf( __( 'No template found for %s', 'wpsl' ), $type ) . '</p>';
                    echo '<p>' . sprintf( __( 'Make sure you call the %sget_template_details%s function with the correct parameters.', 'wpsl' ), '<code>', '</code>' ) . '</p>';
                }
            }

            return $template_data;
        }

        /**
         * Locate the default template
         *
         * @since 2.2.11
         * @param string $type    The type of default template to return
         * @return array $default The default template data
         */
        public function get_default_template( $type = 'store_locator' ) {

            $template_list = $this->get_template_list( $type );
            $default       = '';

            if ( $template_list ) {
                foreach ( $template_list as $template ) {
                    if ( $template['id'] == 'default' ) {
                        $default = $template;
                        break;
                    }
                }
            }

            return $default;
        }

        /**
         * Include the template file.
         *
         * @since  2.2.11
         * @param  array  $args          The template path details
         * @param  array  $template_data The template data ( address, phone, fax etc ).
         * @return string The location template.
         */
        function get_template( $args, $template_data ) {

            // Don't continue if not path and file name is set.
            if ( !isset( $args['path'] ) || !isset( $args['file_name'] ) ) {
                return;
            }

            ob_start();

            include( $this->find_template_path( $args ) );

            return ob_get_clean();
        }

        /**
         * Locate the template file in either the
         * theme folder or the plugin folder itself.
         *
         * @since 2.2.11
         * @param  array  $args     The template data
         * @return string $template The path to the template.
         */
        function find_template_path( $args ) {

            // Look for the template in the theme folder.
            $template = locate_template(
                array( trailingslashit( 'wpsl-templates' ) . $args['file_name'], $args['file_name'] )
            );

            // If the template doesn't exist in the theme folder load the one from the plugin dir.
            if ( !$template ) {
                $template = $args['path'] . $args['file_name'];
            }

            return $template;
        }
    }
}