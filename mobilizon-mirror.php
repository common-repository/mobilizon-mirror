<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://graz.social/@linos
 * @since             1.0.0
 * @package           Mobilizon_Mirror
 *
 * @wordpress-plugin
 * Plugin Name:       Mobilizon Mirror
 * Plugin URI:        https://codeberg.org/linos/mobilizon-mirror
 * Description:       Integrate Mobilizon
 * Version:           1.1.3
 * Author:            André Menrath
 * Author URI:        https://graz.social/@linos
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mobilizon-mirror
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MOBILIZON_MIRROR_VERSION', '1.1.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mobilizon-mirror-activator.php
 */
function activate_mobilizon_mirror() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobilizon-mirror-activator.php';
	Mobilizon_Mirror_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mobilizon-mirror-deactivator.php
 */
function deactivate_mobilizon_mirror() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobilizon-mirror-deactivator.php';
	Mobilizon_Mirror_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mobilizon_mirror' );
register_deactivation_hook( __FILE__, 'deactivate_mobilizon_mirror' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mobilizon-mirror.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mobilizon_mirror() {

	$plugin = new Mobilizon_Mirror();
	$plugin->run();

}
run_mobilizon_mirror();
