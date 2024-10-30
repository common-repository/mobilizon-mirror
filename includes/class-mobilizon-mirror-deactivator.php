<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/includes
 */

/**
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/includes
 * @author     André Menrath <andre.menrath@posteo.de>
 */
class Mobilizon_Mirror_Deactivator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// This only required if custom post type has rewrite!
		flush_rewrite_rules();

		// Delete all Transients!
		delete_transient( 'mobilizon_mirror_instance_datalist' );
		delete_transient( 'mobilizon_mirror_cached_events_list' );

		// Clear cron job hooks for fetching the remote mobilizon events!
		wp_clear_scheduled_hook( 'mobilizon_mirror_cron_refresh_events' );
	}
}
