<?php
/* CSV import match fields template */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !current_user_can( 'wpsl_csv_manager' ) ) {
    wp_die( __( 'You do not have permission to import location data.', 'wpsl-csv' ), '', array( 'response' => 403 ) );
}
?>

<div id="wpsl-import">
    <form id="wpsl-csv-fields" method="post" action="<?php echo admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_csv&section=import' ); ?>">
        <?php
        wp_nonce_field( 'wpsl_csv_import', 'wpsl_csv_import_nonce' );

        if ( isset( $_GET['duplicate_handling'] ) ) {
            $dup_handling = ( $_GET['duplicate_handling'] == 'skip' ) ? 'skip' : 'update';

            echo "<input type='hidden' name='wpsl_duplicate_handling' value='" . esc_attr( $dup_handling ) . "' />";
        }
        ?>
        <input type="hidden" name="wpsl_action" value="csv_import" />

        <p><?php _e( 'Map the CSV headers to the WPSL fields.', 'wpsl-csv' ); ?></p>

        <?php echo $this->import->match_fields(); ?>

        <p class="wpsl-import-btn">
            <input id="wpsl-csv-import" type="submit" value="<?php _e( 'Import Locations', 'wpsl-csv' ); ?>" class="button-primary">
        </p>
    </form>
</div>