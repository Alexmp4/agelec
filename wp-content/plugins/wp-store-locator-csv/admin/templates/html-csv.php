<?php
if ( !defined( 'ABSPATH' ) ) exit;

$section = ( isset( $_GET['section'] ) ) ? $_GET['section'] : '';

ob_start();

if ( isset( $_GET['error_type'] ) && isset( $_GET['code'] ) ) {
    $this->import->handle_errors();
}
?>

<div class="wrap wpsl-csv">
    <h2><?php _e( 'CSV Manager', 'wpsl-csv' ); ?></h2>

    <h2 class="nav-tab-wrapper" id="wpsl-tabs">
        <a class="nav-tab <?php if ( in_array( $section, array( 'import', 'match_fields', '' ) ) ) { echo 'nav-tab-active'; } ?>" href="<?php echo admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_csv&section=import' ); ?>"><?php _e( 'Import', 'wpsl-csv' ); ?></a>
        <?php if ( current_user_can( 'wpsl_csv_manager_export' ) ) { ?>
            <a class="nav-tab <?php if ( $section == 'export' ) { echo 'nav-tab-active'; } ?>" href="<?php echo admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_csv&section=export' ); ?>"><?php _e( 'Export', 'wpsl-csv' ); ?></a>
        <?php } ?>
        <?php if ( current_user_can( 'wpsl_csv_manager_tools' ) ) { ?>
            <a class="nav-tab <?php if ( $section == 'tools' ) { echo 'nav-tab-active'; } ?>" href="<?php echo admin_url( 'edit.php?post_type=wpsl_stores&page=wpsl_csv&section=tools' ); ?>"><?php _e( 'Tools', 'wpsl-csv' ); ?></a>
        <?php } ?>
    </h2>
    
    <?php 
    switch ( $section ) {
        case 'export':
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/templates/html-export.php' );
            break;
        case 'match_fields':
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/templates/html-match-fields.php' );
            break;
        case 'tools':
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/templates/html-tools.php' );
            break;
        case 'import':
        default:
            $this->import->clean_up();
            
            require_once( WPSL_CSV_PLUGIN_DIR . 'admin/templates/html-import.php' );
            break;
    }
    ?>        
</div>

<?php
echo ob_get_clean();
?>