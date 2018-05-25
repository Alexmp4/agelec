<?php
defined('ABSPATH') or die;

$tabs = array(
    'basic' => __('BASIC OPTIONS', 'breeze'),
    'advanced' => __('ADVANCED OPTIONS', 'breeze'),
    'database' => __('DATABASE', 'breeze'),
    'cdn' => __('CDN', 'breeze'),
    'varnish' => __('VARNISH', 'breeze'),
    'faq' => __('FAQs', 'breeze'),
);

if (is_multisite() && get_current_screen()->base !== 'settings_page_breeze-network') {
    $tabs = array(
        'faq' => __('FAQs', 'breeze')
    );
}

wp_enqueue_script('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js');
?>
<?php if (isset($_REQUEST['database-cleanup']) && $_REQUEST['database-cleanup'] == 'success'): ?>
    <div id="message-save-settings" class="notice notice-success" style="margin: 10px 0px 10px 0;padding: 10px;"><strong><?php _e('Database cleanup successful', 'breeze'); ?></strong></div>
<?php endif; ?>
<!--save settings successfull message-->
<?php if (isset($_REQUEST['save-settings']) && $_REQUEST['save-settings'] == 'success'): ?>
     <div id="message-save-settings" class="notice notice-success" style="margin: 10px 0px 10px 0;padding: 10px;"><strong><?php _e('Configuration settings saved', 'breeze'); ?></strong></div>
<?php endif; ?>
<div class="wrap breeze-main">
    <div class="breeze-header">
        <a  href="https://www.cloudways.com" target="_blank">
        <div class="breeze-logo"></div>
        </a>
    </div>

    <h1 style="clear: both"></h1>

    <ul id="breeze-tabs" class="nav-tab-wrapper">
        <?php
        foreach ($tabs as $key => $name) {
            echo '<a id="tab-' . $key . '" class="nav-tab" href="#tab-' . $key . '" data-tab-id="' . $key . '"> ' . $name . ' </a> ';
        }
        ?>
    </ul>

    <div id="breeze-tabs-content" class="tab-content">
        <?php
        foreach ($tabs as $key => $name) {
            echo '<div id="tab-content-' . $key . '" class="tab-pane">';
            echo '<form class="breeze-form" method="post" action="">';
            echo '<div class="tab-child">';
            echo '<input type="hidden" name="breeze_'.$key.'_action" value="breeze_'.$key.'_settings">';
            wp_nonce_field('breeze_settings_' . $key, 'breeze_settings_' . $key . '_nonce');
            Breeze_Admin::render($key);
            echo '</div>';
            if ($key == 'database'){
                echo '<p class="submit">
                 <input type="submit" class="button button-primary" value="'. __('Optimize', 'breeze') .'"/>
                     </p>';
            }else{
                if ($key != 'faq') {
	                echo '<p class="submit">
                        <input type="submit" class="button button-primary" value="'. __('Save Changes', 'breeze') .'"/>
                        </p>';
                }
            }
            echo '</form>';
            echo '</div>';
        }
        ?>

        <!--Right-side content-->
        <div id="breeze-and-cloudways" class="rs-block">
            <h3 class="rs-title"><?php _e('Want to Experience Better Performance?', 'breeze') ?></h3>
            <div class="rs-content">
                <p><?php _e('Take advantage of powerful features by deploying WordPress and Breeze on Cloudways.', 'breeze') ?></p>
                <ul>
                    <li><?php _e('Fully Compatible with Varnish', 'breeze') ?></li>
                    <li><?php _e('One-Click setup of CloudwaysCDN', 'breeze') ?></li>
                    <li><?php _e('24/7 Expert Human Support', 'breeze') ?></li>
                    <li><?php _e('WooCommerce Compatible', 'breeze') ?></li>
                </ul>
                <button class="button button-primary">
                    <a href="https://www.cloudways.com/en/wordpress-cloud-hosting.php?utm_source=breeze-plugin&utm_medium=breeze&utm_campaign=breeze" target="_blank"><?php _e('Find Out More', 'breeze') ?></a>
                </button>
            </div>
            <div class="rs-content">
                <h4><?php _e('Rate Breeze', 'breeze') ?></h4>
                <p><?php _e('If you are satisfied with Breeze\'s performance, <a href="https://wordpress.org/plugins/breeze#reviews" target="_blank">drop us a rating here.</a>', 'breeze') ?></p>
            </div>
        </div>
    </div>
</div>
