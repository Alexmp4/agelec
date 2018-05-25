<?php
/* CSV tools template */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !current_user_can( 'wpsl_csv_manager_tools' ) ) {
    wp_die( __( 'You do not have permission to access the tools page.', 'wpsl-csv' ), '', array( 'response' => 403 ) );
}

$max_upload_size = wp_max_upload_size();
?> 

<div id="wpsl-tools">
    <form method="post" id="wpsl-csv-tools-form" action="<?php echo admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_csv&section=tools' ); ?>">
        <?php
        wp_nonce_field( 'wpsl_csv_tools', 'wpsl_csv_tools_nonce' );
        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="wpsl-bulk-delete"><?php _e( 'Delete All Locations', 'wpsl-csv' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="wpsl-bulk-delete" name="wpsl_csv_tools[bulk_delete]" value="">
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="hidden" name="wpsl_action" value="csv_tools" />
        <p><input type="submit" value="<?php _e( 'Submit', 'wpsl-csv' ); ?>" class="button-primary"></p>
    </form>
</div>
<div id="dialog" title="Please confirm" style="display: none;">
    <p><?php _e( 'Are you sure you want to delete all locations? This cannot be undone, unless you restore a backup.', 'wpsl-csv' ); ?></p>
</div>