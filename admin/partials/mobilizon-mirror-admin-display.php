<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    mobilizon-mirror
 * @subpackage mobilizon-mirror/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="mobilizon-mirror-settings-wrapper">
	<div id="mobilizon-mirror-settings-title">
	<div id="mobilizon-mirror-logo" ></div>
	</div>
	<?php
	// Grab all options.
	$options = get_option( 'mobilizon-mirror' );
	if ( isset( $options['instance_url'] ) && ! empty( $options['instance_url'] ) && isset( $options['group_names'] ) && ! empty( $options['group_names'] ) ) {
		?>
		<a class="button-secondary alert" href="<?php echo esc_url( get_post_type_archive_link( 'mobilizon_event' ) ); ?>"><?php esc_html_e( 'Go to local events', 'mobilizon-mirror' ); ?></a>
	<?php } ?>
	<form method="post" name="<?php echo esc_attr( 'mobilizon-mirror' ); ?>" action="options.php">
	<?php
		$instance_url = ( isset( $options['instance_url'] ) && ! empty( $options['instance_url'] ) ) ? esc_url( $options['instance_url'] ) : '';
		$group_names  = ( isset( $options['group_names'] ) && ! empty( $options['group_names'] ) ) ? $options['group_names'] : array( '' );

		$event_archive_view = ( isset( $options['event_archive_view'] ) && ! empty( $options['event_archive_view'] ) ) ? esc_attr( $options['event_archive_view'] ) : 'theme-default';

		$event_single_view = ( isset( $options['event_single_view'] ) && ! empty( $options['event_single_view'] ) ) ? esc_attr( $options['event_single_view'] ) : 'theme-default';

		$sync_interval  = ( isset( $options['sync_interval'] ) && ! empty( $options['sync_interval'] ) ) ? $options['sync_interval'] : 10;

		settings_fields( 'mobilizon-mirror' );
		do_settings_sections( 'mobilizon-mirror' );

	?>

	<!-- Mobilizon Account Settings -->
	<fieldset>
		<legend><h2> <?php esc_html_e( 'Account Information', 'mobilizon-mirror' ); ?> </h2></legend>
		<div class="components-base-control__wrapper">
			<!-- Mobilizon Instance URL -->
			<div class="components-base-control__field">
				<label><?php esc_html_e( 'Mobilizon Instance (URL)', 'mobilizon-mirror' ); ?></label>
				<legend class="screen-reader-text">
					<span><?php esc_html_e( 'Mobilizon Instance (URL)', 'mobilizon-mirror' ); ?></span>
				</legend>
				<div class="input-wrapper">
					<input type="text" class="textfield instance_url" list="mobilizon-mirror-instances" id="mobilizon-instance_url" name="<?php echo 'mobilizon-mirror'; ?>[instance_url]" value="<?php echo ( empty( $instance_url ) ? '' : esc_url( $instance_url ) ); ?>"/>
					<span id="mobilizon-instance_url-feedback-icon" class="form-control-feedback"></span>
				</div>
				<!-- TODO: Make sure that transient is reset, when it does not exist, maybe it should only be valid one day -->
				<?php
				echo wp_kses(
					get_transient( 'mobilizon_mirror_instance_datalist' ),
					array(
						'datalist' => array(
							'id' => array(),
						),
						'option'   => array(
							'value' => array(),
						),
					)
				);
				?>
			</div>

		<!-- Mobilizon Group Name -->
			<div class="components-base-control__field">
				<label><?php esc_html_e( 'Federated Group Name', 'mobilizon-mirror' ); ?>
					<!-- TODO: Review button css class and style -->
					<span class="btn tooltip" data-tooltip="<?php esc_html_e( 'Not the Display Name! &#10;The slug behind the @ in the url of your group page!', 'mobilizon-mirror' ); ?>">&#8505;</span>
				</label>
				<legend class="screen-reader-text">
					<span><?php esc_html_e( 'Federated Group Name', 'mobilizon-mirror' ); ?></span>
				</legend>
				<div class="input-wrapper" id="mobilizon-group-input-wrapper">
					<?php
					foreach ( $group_names as $group_name ) {
						?>
						<div class="mobilizon-group-item">
							<input type="text" class="textfield mobilizon-group_name"  list="mobilizon-group_names" name="mobilizon-mirror[group_names][]" value="<?php echo ( empty( $group_name ) ? '' : esc_attr( $group_name ) ); ?>"/>
							<span id="mobilizon-group_name-feedback-icon" class="form-control-feedback"></span>
							<button type='button' class="mobilizon-remove-group minus-button">-</button>
						</div>
					<?php } ?>
				</div>
				<button type='button' id="mobilizon-add-group" class="mobilizon-add-group plus-button"></button>
				<!-- TODO: Preload group names after instance is set correctly -->
				<datafield id="mobilizon-group_names"></datafield>
			</div>
		</div>
	</fieldset>


	<!-- Mobilizon Style Settings -->
	<fieldset>
		<legend><h2><?php esc_html_e( 'Display Options', 'mobilizon-mirror' ); ?></h2></legend>
		<div class="components-base-control__wrapper">
			<!-- Mobilizon Archive Style -->
			<div class="components-base-control__field">
				<label><h3><?php esc_html_e( 'Archive Style', 'mobilizon-mirror' ); ?></h3></label>
				<legend class="screen-reader-text">
					<span><?php esc_html_e( 'Archive Style', 'mobilizon-mirror' ); ?></span>
				</legend>
				<div class="radio-input-wrapper">
					<input type="radio" id="mobilizon-mirror-event_archive_view" name="mobilizon-mirror[event_archive_view]" value="theme-default" <?php echo ( 'theme-default' === $event_archive_view ? 'checked' : '' ); ?> >
					<label for="card"><?php esc_html_e( 'Theme Default', 'mobilizon-mirror' ); ?></label><br>
					<input type="radio" id="mobilizon-mirror-event_archive_view" name="mobilizon-mirror[event_archive_view]" value="card" <?php echo ( 'card' === $event_archive_view ? 'checked' : '' ); ?> >
					<label for="card"><?php esc_html_e( 'Card View', 'mobilizon-mirror' ); ?></label><br>
					<input type="radio" id="mobilizon-mirror'-event_archive_view" name="mobilizon-mirror[event_archive_view]" value="list" <?php echo ( 'list' === $event_archive_view ? 'checked' : '' ); ?> >
					<label for="list"><?php esc_html_e( 'Simple List', 'mobilizon-mirror' ); ?></label><br>
				</div>
			</div>
		</div>
		<div class="components-base-control__wrapper">
			<!-- Mobilizon Single Event Style -->
			<div class="components-base-control__field">
				<label><h3><?php esc_html_e( 'Single Event: Position of Featured Image', 'mobilizon-mirror' ); ?></h3></label>
				<legend class="screen-reader-text">
					<span><?php esc_html_e( 'Single Event: Position of Featured Image', 'mobilizon-mirror' ); ?></span>
				</legend>
				<div class="radio-input-wrapper">
					<input type="radio" id="'mobilizon-mirror-event_single_view" name="mobilizon-mirror[event_single_view]" value="theme-default" <?php echo ( 'theme-default' === $event_single_view ? 'checked' : '' ); ?> >
					<label for="top"><?php esc_html_e( 'Theme Default', 'mobilizon-mirror' ); ?></label><br>
					<input type="radio" id="'mobilizon-mirror-event_single_view" name="mobilizon-mirror[event_single_view]" value="top" <?php echo ( 'top' === $event_single_view ? 'checked' : '' ); ?> >
					<label for="top"><?php esc_html_e( 'As Header', 'mobilizon-mirror' ); ?></label><br>
					<input type="radio" id="'mobilizon-mirror-event_single_view" name="mobilizon-mirror[event_single_view]" value="side" <?php echo ( 'side' === $event_single_view ? 'checked' : '' ); ?> >
					<label for="side"><?php esc_html_e( 'In Sidebar', 'mobilizon-mirror' ); ?></label><br>
				</div>
			</div>
		</div>
	</fieldset>

		<!-- Mobilizon Sync Settings -->
		<fieldset>
		<legend><h2><?php esc_html_e( 'Sync Options', 'mobilizon-mirror' ); ?></h2></legend>
		<div class="components-base-control__wrapper">
			<!-- Mobilizon Sync Interval -->
			<div class="components-base-control__field" style="width: 500px;">
				<label><h3><?php esc_html_e( 'Sync Interval in Minutes', 'mobilizon-mirror' ); ?></h3></label>
				<legend class="screen-reader-text">
					<span><?php esc_html_e( 'Sync Interval in Minutes	', 'mobilizon-mirror' ); ?></span>
				</legend>
				<!-- TODO: PROPER CSS -->
				<input style="width: 400px;" type="range" id="mobilizon-mirror-sync_interval" name="mobilizon-mirror[sync_interval]" min="2" max="60" value="<?php echo ( empty( $sync_interval ) ? '' : absint( $sync_interval ) ); ?>" oninput="mobilizonMirrorSyncInterval.innerText = this.value">
				<span style="font-size: 19px; margin: 10px; line-height: 44px;" id="mobilizonMirrorSyncInterval"><?php echo ( empty( $sync_interval ) ? '' : absint( $sync_interval ) ); ?></span>
			</div>
		</div>
	</fieldset>

	<?php submit_button( esc_attr__( 'Save all changes', 'mobilizon-mirror' ), 'primary', 'submit', true ); ?>
	</form>
</div>
