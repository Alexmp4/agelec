<?php
/**
 * WPGMP_Google_Map_Widget_Class File.
 * @author Flipper Code <hello@flippercode.com>
 * @package CORE
 */

if ( ! class_exists( 'WPGMP_Google_Map_Widget_Class' ) ) {

	/**
	 * Initilize google map widget.
	 * @author Flipper Code <hello@flippercode.com>
	 * @version 3.0.0
	 * @package Maps
	 */
	class WPGMP_Google_Map_Widget_Class extends WP_Widget {
		/**
		 * Initlize parent constructer.
		 */
		public function __construct() {

			parent::__construct(
				'WPGMP_Google_Map_Widget_Class',
				'WP Google Map Plugin',
				array( 'description' => __( 'A widget to display google maps' , WPGMP_TEXT_DOMAIN ) )
			);
		}
		/**
		 * Display widget at frontend.
		 * @param  array $args     Widget Arguments.
		 * @param  int   $instance Instance of Widget.
		 */
		function widget( $args, $instance ) {

			global $wpdb,$map;
			extract( $args );

			$map_id = apply_filters( 'widget_title', empty( $instance['map_id'] ) ? '' : $instance['map_id'], $instance, $this->id_base );
	   		$map_title = apply_filters( 'widget_text', empty( $instance['map_title'] ) ? '' : $instance['map_title'], $instance );

    		echo $before_widget;
	      	if ( ! empty( $map_title ) ) {
				echo $before_title.$map_title.$after_title; }

			if ( ! empty( $map_id ) ) {
				echo do_shortcode( '[put_wpgm id='.$map_id.']' ); }
			echo $after_widget;
		}
		/**
		 * Update widget options.
		 * @param  array $new_instance New Options values.
		 * @param  array $old_instance Old Options values.
		 * @return array               Modified Options values.
		 */
		function update( $new_instance, $old_instance ) {

			$instance = $old_instance;
			$instance['map_title'] = strip_tags( $new_instance['map_title'] );
			$instance['map_id'] = strip_tags( $new_instance['map_id'] );
			return $instance;
		}
		/**
		 * Backend Widget Form.
		 * @param  array $instance Widget options values.
		 */
		function form( $instance ) {

			global $wpdb,$map;
			$map_records = $wpdb->get_results( 'SELECT * FROM '.TBL_MAP.'' );
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'map_title' );?>">
					<?php _e( 'Title:' , WPGMP_TEXT_DOMAIN );?>
				</label>
				<input type="text" value="<?php echo $instance['map_title']; ?>" name="<?php echo $this->get_field_name( 'map_title' ); ?>" class="widefat" style="margin-top:6px;">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'map_id' );?>">
					<?php _e( 'Select Your Map:' , WPGMP_TEXT_DOMAIN );?>
				</label>
				<select id="<?php echo $this->get_field_id( 'map_id' ); ?>" name="<?php echo $this->get_field_name( 'map_id' ); ?>" class="widefat" style="margin-top:6px;">
		        <option value=""><?php _e( 'Select map', WPGMP_TEXT_DOMAIN ) ?></option>
				<?php
				if ( ! empty( $map_records ) ) {
					foreach ( $map_records as $key => $map_record ) {
					?>
						<option value="<?php echo $map_record->map_id; ?>"<?php selected( $map_record->map_id,$instance['map_id'] ); ?>><?php echo $map_record->map_title; ?></option>
					<?php
					}
				}
				?>
				</select>
		    </p>
		<?php
		}
	}
}
