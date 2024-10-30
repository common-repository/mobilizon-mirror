<?php
/**
 * Fired during plugin activation
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/includes
 * @author     André Menrath <andre.menrath@posteo.de>
 */
class Mobilizon_Mirror_Activator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		/**
		 * Custom Post Types
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mobilizon-mirror-post_types.php';
		$plugin_post_types = new Mobilizon_Mirror_Post_Types();

		/**
		 * The problem with the initial activation code is that when the activation hook runs, it's after the init hook has run,
		 * so hooking into init from the activation hook won't do anything.
		 * You don't need to register the CPT within the activation function unless you need rewrite rules to be added
		 * via flush_rewrite_rules() on activation. In that case, you'll want to register the CPT normally, via the
		 * loader on the init hook, and also re-register it within the activation function and
		 * call flush_rewrite_rules() to add the CPT rewrite rules.
		 *
		 * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/261
		 */
		$plugin_post_types->create_custom_post_type();

		/**
		 * This is only required if the custom post type has rewrite!
		 *
		 * Remove rewrite rules and then recreate rewrite rules.
		 *
		 * This function is useful when used with custom post types as it allows for automatic flushing of the WordPress
		 * rewrite rules (usually needs to be done manually for new custom post types).
		 * However, this is an expensive operation so it should only be used when absolutely necessary.
		 * See Usage section for more details.
		 *
		 * Flushing the rewrite rules is an expensive operation, there are tutorials and examples that suggest
		 * executing it on the 'init' hook. This is bad practice. It should be executed either
		 * on the 'shutdown' hook, or on plugin/theme (de)activation.
		 *
		 * @link https://codex.wordpress.org/Function_Reference/flush_rewrite_rules
		 */
		flush_rewrite_rules();

		/**
		 * Set up transient for the Mobilizon list
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mobilizon-mirror-instances.php';
		$instances_datalist = Mobilizon_Mirror_Instances::get_instance_datalist();
		if ( ! is_null( $instances_datalist ) ) {
			set_transient( 'mobilizon_mirror_instance_datalist', $instances_datalist );
		}

		/**
		 * If the Mobilizon Mirror has already been set up an deactivated afterwards, but not uninstalled, the settings are still saved.
		 * So then set up cron job for checking for new or updated remote mobilizon events, of the last settings
		 */
		$past_options = get_option( 'mobilizon-mirror' );
		if ( $past_options && isset( $past_options['group_names'] ) && isset( $past_options['instance_url'] ) ) {
			if ( '' !== get_option( 'mobilizon-mirror' )['group_names'] && '' !== get_option( 'mobilizon-mirror' )['instance_url'] ) {
				// This check is supposed to be not needed, but just to be sure.
				if ( ! wp_next_scheduled( 'mobilizon_mirror_cron_refresh_events' ) ) {
					wp_schedule_event( time(), 'mobilizon_mirror_refresh_interval', 'mobilizon_mirror_cron_refresh_events' );
				}
			}
		}

	}

}
