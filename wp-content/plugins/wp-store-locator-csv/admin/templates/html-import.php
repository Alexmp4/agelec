<?php
/* CSV import template */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !current_user_can( 'wpsl_csv_manager' ) ) {
    wp_die( __( 'You do not have permission to import location data.', 'wpsl-csv' ), '', array( 'response' => 403 ) );
}

$max_upload_size = wp_max_upload_size();
?>

<div id="wpsl-import" class="wpsl-select-file">
    <form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_csv' ); ?>" autocomplete="off">
        <?php
        wp_nonce_field( 'wpsl_csv_upload', 'wpsl_csv_upload_nonce' );

        if ( !is_writable( WPSL_CSV_IMPORT_DIR ) ) {
            echo '<div class="error">';
            echo '<p>' . sprintf( __( '%sWarning!%s Before you can upload your CSV file, you need to make sure the %s directory is writeable.', 'wpsl-csv' ), '<strong>', '</strong>', '<code>' . WPSL_CSV_IMPORT_DIR . '</code>' ) . '</p>';
            echo '</div>';
        }
        ?>

        <p><?php echo sprintf( __( 'Before getting started %sprepare%s your CSV file, and view the %snotes%s.', 'wpsl-csv' ), '<a href="http://wpstorelocator.co/document/csv-manager/#import">', '</a>', '<a href="http://wpstorelocator.co/document/csv-manager/#notes">', '</a>' ); ?></p>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="wpsl-csv-file"><?php _e( 'Select CSV file', 'wpsl-csv' ); ?>:</label>
                    </th>
                    <td>
                        <input type="file" name="wpsl_csv_file" id="wpsl-csv-file" accept="text/csv">
                        <input type="hidden" name="max_file_size" value="<?php echo apply_filters( 'import_upload_size_limit', $max_upload_size ); ?>" />
                        <input type="hidden" name="wpsl_action" value="csv_upload" />
                        <em><?php printf( __( 'Maximum file size: %s.', 'wpsl-csv' ), size_format( $max_upload_size ) ); ?></em>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wpsl-csv-dup-check"><?php _e( 'Check for duplicate locations?', 'wpsl-csv' ); ?><span class="wpsl-info"><span class="wpsl-info-text wpsl-hide"><?php echo sprintf( __( 'A location is considered a duplicate when the name, address and city match with an existing location. %s %s %sNote:%s enabling this option will slow down the import process.%s', 'wpsl-csv'), '<br><br>', '<em>', '<strong>', '</strong>', '</em>' ); ?></span></span></label>
                    </th>
                    <td>
                        <input id="wpsl-csv-dup-check" type="checkbox" class="wpsl-slide-option" name="wpsl_csv_dup_check">
                    </td>
                </tr>
                <tr style="display:none;">
                    <th scope="row">
                        <label for="wpsl-csv-dup-handling"><?php _e( 'Duplicate Handling', 'wpsl-csv' ); ?></label>
                    </th>
                    <td>
                        <select id="wpsl-csv-dup-handling" name="wpsl_csv_dup_handling">
                            <option value="skip" selected="selected"><?php _e( 'Skip', 'wpsl-csv' ); ?></option>
                            <option value="update"><?php _e( 'Update', 'wpsl-csv' ); ?></option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <p><input type="submit" value="<?php _e( 'Continue', 'wpsl-csv' ); ?>" class="button-primary"></p>
    </form>
</div>