<?php
if ( !defined( 'ABSPATH' ) ) exit;

$campaign_params = '?utm_source=wpsl-add-ons&utm_medium=banner&utm_campaign=add-ons';

// Load the add-on data from an existing transient, or grab new data from the remote URL.
if ( false === ( $add_ons = get_transient( 'wpsl_addons' ) ) ) {
    $response = wp_remote_get( 'https://s3.amazonaws.com/wpsl/add-ons.json' );

    if ( !is_wp_error( $response ) ) {
        $add_ons = json_decode( wp_remote_retrieve_body( $response ) );

        if ( $add_ons ) {
            set_transient( 'wpsl_addons', $add_ons, WEEK_IN_SECONDS );
        }
    }
}
?>

<div class="wrap wpsl-add-ons">
    <h2><?php _e( 'WP Store Locator Add-Ons', 'wpsl' ); ?></h2>

    <?php
    if ( $add_ons ) {
        foreach ( $add_ons as $add_on ) {
    ?>
        <div class="wpsl-add-on">
            <?php if ( !empty( $add_on->url ) ) { ?>
            <a title="<?php echo esc_attr( $add_on->name ); ?>" href="<?php echo esc_url( $add_on->url ) . $campaign_params; ?>">
                <img src="<?php echo esc_url( $add_on->img ); ?>"/>
            </a>
            <?php } else { ?>
            <img src="<?php echo esc_url( $add_on->img ); ?>"/>
            <?php } ?>

            <div class="wpsl-add-on-desc">
                <p><?php echo esc_html( $add_on->desc ); ?></p>
                
                <div class="wpsl-add-on-status">
                    <?php if ( !empty( $add_on->class ) && class_exists( $add_on->class ) ) { ?>
                    <p><strong><?php _e( 'Already Installed.', 'wpsl' ); ?></strong></p>
                    <?php } else if ( isset( $add_on->soon ) && $add_on->soon ) { ?>
                    <p><strong><?php _e( 'Coming soon!', 'wpsl' ); ?></strong></p>
                    <?php } else { ?>
                    <a class="button-primary" href="<?php echo esc_url( $add_on->url ) . $campaign_params; ?>">
                        <?php esc_html_e( 'Get This Add-On', 'wpsl' ); ?>
                    </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php 
        }
    } else {
        echo '<p>'. __( 'Failed to load the add-on list from the server.', 'wpsl' ) . '</p>';
        echo '<p>'. __( 'Please try again later!', 'wpsl' ) . '</p>';
    }
    ?>  
</div>