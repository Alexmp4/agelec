<?php
/* CSV export template */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !current_user_can( 'wpsl_csv_manager_export' ) ) {
    wp_die( __( 'You do not have permission to export location data.', 'wpsl-csv' ), '', array( 'response' => 403 ) );
}
?>

<div id="wpsl-export">
    <form id="wpsl-csv-fields" method="post" action="<?php echo admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_csv&section=export' ); ?>">
        <?php wp_nonce_field( 'wpsl_csv_export', 'wpsl_csv_export_nonce' ); ?>
        <input type="hidden" name="wpsl_action" value="csv_export" />

        <?php 
        $total_count = $this->export->get_export_count();
        
        if ( !$total_count ) {
            echo '<p>' . __( 'There are currently no locations to export.', 'wpsl-csv' ) . '</p>';  
        } else {
            echo '<p>' . sprintf( _n( 'You have %d %slocation%s to export.', 'You have %d %slocations%s to export.', $total_count, 'wpsl-csv' ), $total_count, '<a href="' . admin_url( 'edit.php?post_type=wpsl_stores' ) . '">', '</a>' ) . '</p>';
            echo '<h3>' . __( 'Export Filters', 'wpsl-csv' ) . '</h3>';
            echo '<table class="form-table">';
            echo $this->export->filter_options();
            echo '</table>';
        }
        ?>
  
        <p><input type="submit" <?php if ( !$total_count ) { echo 'disabled="disabled"'; } ?> value="<?php _e( 'Export Locations', 'wpsl-csv' ); ?>" class="button-primary"></p>
    </form>
</div>