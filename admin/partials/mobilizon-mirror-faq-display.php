<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="mobilizon-mirror-faq-wrapper">
	<div id="mobilizon-mirror-logo" ></div>
	<h1> <?php esc_html_e( 'Frequently Asked Questions', 'mobilizon_mirror' ); ?> </h1>

		<h2> <?php esc_html_e( 'Where do I find the mirrored events on my website?', 'mobilizon_mirror' ); ?> </h2>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'mobilizon_event' ) ); ?>"><?php echo esc_url( get_post_type_archive_link( 'mobilizon_event' ) ); ?></a>

		<h2> <?php esc_html_e( 'Does the plugin conflict with other Event-Plugins or Post-Types?', 'mobilizon_mirror' ); ?></h2>
		<?php esc_html_e( 'By default this plugin uses a prefixed post type called mobilizon_event, but the slug events, may conflict and cause problems. This may be addressed in the future.', 'mobilizon_mirror' ); ?>

		<h2> <?php esc_html_e( 'How are the events synced?', 'mobilizon_mirror' ); ?></h2>
		<?php esc_html_e( 'This plugin syncs up to 1000 events per mobilizon group. But it only fetches up to 3 events per sync cycle.', 'mobilizon_mirror' ); ?>

		<h2> <?php esc_html_e( 'How can I add my synced events to the navigation menu of my website?', 'mobilizon_mirror' ); ?></h2>
		<?php esc_html_e( 'In your admin navigation menu go to "Appearance"->"Menu". Then make sure that the "Screen Options" (accessable on the top right) "Mobilizon Events" are marked as visible. Then under "Add menu items" you can select "Mobilizon Events"->"View All"->"Mobilizon Event List". Then you can choose the Navigation Label (the name as it appears for your sites visitors) by yourself.', 'mobilizon_mirror' ); ?>

		</div>
