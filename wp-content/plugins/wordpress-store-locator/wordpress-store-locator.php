<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              http://plugins.db-dzine.com
 * @since             1.0.0
 * @package           WordPress_Store_Locator
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Store Locator
 * Plugin URI:        http://plugins.db-dzine.com
 * Description:       Add a Store Locator to your WordPress!
 * Version:           1.7.8
 * Author:            DB-Dzine
 * Author URI:        http://www.db-dzine.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordpress-store-locator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordpress-store-locator-activator.php
 */
function activate_WordPress_Store_Locator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-store-locator-activator.php';
	WordPress_Store_Locator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordpress-store-locator-deactivator.php
 */
function deactivate_WordPress_Store_Locator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-store-locator-deactivator.php';
	WordPress_Store_Locator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_WordPress_Store_Locator' );
register_deactivation_hook( __FILE__, 'deactivate_WordPress_Store_Locator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-store-locator.php';

/**
 * Run the Plugin
 * @author Daniel Barenkamp
 * @version 1.0.0
 * @since   1.0.0
 * @link    http://plugins.db-dzine.com
 */
function run_WordPress_Store_Locator() {

	$plugin_data = get_plugin_data( __FILE__ );
	$version = $plugin_data['Version'];

	$plugin = new WordPress_Store_Locator($version);
	$plugin->run();

	return $plugin;

}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
// Load the TGM init if it exists
if ( file_exists( plugin_dir_path( __FILE__ ) . 'admin/tgm/tgm-init.php' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/tgm/tgm-init.php';
}

if ( is_plugin_active('redux-framework/redux-framework.php')){
	$WordPress_Store_Locator = run_WordPress_Store_Locator();
} else {
	add_action( 'admin_notices', 'run_WordPress_Store_Locator_Not_Installed' );
}

function run_WordPress_Store_Locator_Not_Installed()
{
	?>
    <div class="error">
      <p><?php _e( 'WordPress Store Locator requires the Redux Framework plugin. Please install or activate it before!', 'wordpress-store-locator'); ?></p>
    </div>
    <?php
}
