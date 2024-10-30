<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    Mobilizon_Mirror
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check if the $_REQUEST content actually is the plugin name.
if ( isset( $_REQUEST['plugin'] ) && 'mobilizon-mirror/mobilizon-mirror.php' !== $_REQUEST['plugin'] ) {
	exit;
}

// Delete all bridged events from the database.
wp_reset_postdata();
$args = array(
	'numberposts' => -1,
	'post_type'   => 'mobilizon_event',
);

$the_query = new WP_Query( $args );
if ( $the_query->have_posts() ) :
	while ( $the_query->have_posts() ) :
		$the_query->the_post();
		wp_delete_attachment( get_post_thumbnail_id(), $force_delete = true );
		wp_delete_post( get_the_ID(), $force_delete = true );
	endwhile;
endif;
wp_reset_postdata();

// Delete Options and unregister setting.
delete_option( 'mobilizon-mirror' );
unregister_setting( 'mobilizon-mirror', 'mobilizon-mirror' );
