<?php
/**
 * Admin Notices
 *
 * @author Tijmen Smit
 * @since  2.0.0
*/

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Notices' ) ) {
    
    /**
     * Handle the meta boxes.
     *
     * @since 2.0.0
     */
	class WPSL_Notices {
        
        /**
         * Holds the notices.
         * @since 2.0.0
         * @var array
         */
        private $notices = array();
                
        public function __construct() {
            
            $this->notices = get_option( 'wpsl_notices' ); 
            
            add_action( 'all_admin_notices', array( $this, 'show' ) );
        }

        /**
         * Show one or more notices.
         * 
         * @since 2.0.0
         * @return void
         */
        public function show() {
            
            if ( !empty( $this->notices ) ) {
                $allowed_html = array(
                    'a' => array(
                        'href'       => array(),
                        'id'         => array(),
                        'class'      => array(),
                        'data-nonce' => array(),
                        'title'      => array(),
                        'target'     => array()
                    ),
                    'p'  => array(),
                    'br' => array(),
                    'em' => array(),
                    'strong' => array(
                        'class' => array()
                    ),
                    'span' => array(
                        'class' => array()
                    ),
                    'ul' => array(
                        'class' => array()
                    ),
                    'li' => array(
                        'class' => array()
                    )
                );
                
                if ( wpsl_is_multi_array( $this->notices ) ) {
                    foreach ( $this->notices as $k => $notice ) {
                        $this->create_notice_content( $notice, $allowed_html );
                    }
                } else {
                    $this->create_notice_content( $this->notices, $allowed_html );
                }

                // Empty the notices.
                $this->notices = array();
                update_option( 'wpsl_notices', $this->notices );
            }
        }
        
        /**
         * Create the content shown in the notice.
         * 
         * @since 2.1.0
         * @param array $notice
         * @param array $allowed_html
         */
        public function create_notice_content( $notice, $allowed_html ) {
            
            $class = ( 'update' == $notice['type'] ) ? 'updated' : 'error';

            if ( isset( $notice['multiline'] ) && $notice['multiline'] ) {
                $notice_msg = wp_kses( $notice['message'], $allowed_html );
            } else {
                $notice_msg = '<p>' . wp_kses( $notice['message'], $allowed_html ) . '</p>';
            }

            echo '<div class="' . esc_attr( $class ) . '">' . $notice_msg . '</div>';
        }
                
        /**
         * Save the notice.
         * 
         * @since 2.0.0
         * @param  string $type      The type of notice, either 'update' or 'error'
         * @param  string $message   The user message
         * @param  bool   $multiline True if the message contains multiple lines ( used with notices created in add-ons ).
         * @return void
         */
        public function save( $type, $message, $multiline = false ) {

            $current_notices = get_option( 'wpsl_notices' );

            $new_notice = array(
                'type'    => $type,
                'message' => $message
            );

            if ( $multiline ) {
                $new_notice['multiline'] = true;
            }

            if ( $current_notices ) {
                if ( !wpsl_is_multi_array( $current_notices ) ) {
                    $current_notices = array( $current_notices );
                }

                array_push( $current_notices, $new_notice );

                update_option( 'wpsl_notices', $current_notices );  
            } else {
                update_option( 'wpsl_notices', $new_notice );    
            }             
        }
    }
}