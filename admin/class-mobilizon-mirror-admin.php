<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/admin
 * @author     André Menrath <andre.menrath@posteo.de>
 */
class Mobilizon_Mirror_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mobilizon_Mirror_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mobilizon_Mirror_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mobilizon-mirror-admin.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mobilizon_Mirror_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mobilizon_Mirror_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mobilizon-mirror-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/**
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
		 *
		 * @link https://codex.wordpress.org/Function_Reference/add_options_page
		 *
		 * If you want to list plugin options page under a custom post type, then change 'plugin.php' to e.g. 'edit.php?post_type=your_custom_post_type'
		 */
		// Second-Last argument is none, we add the Mobilizon icon via css, because it's not available as an svg at the moment!
		// We add it where the custom post type menu would be as well
		add_menu_page(  'Mobilizon Settings', 'Mobilizon Events', 'manage_options', $this->plugin_name, array( $this, 'display_plugin_setup_page' ), 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgaWQ9Im1vYmlsaXpvbi1sb2dvIgogICB2aWV3Qm94PSIwIDAgNTQuOTk5OTk5IDU1LjAwMDAwMSIKICAgdmVyc2lvbj0iMS4xIgogICBzb2RpcG9kaTpkb2NuYW1lPSJtb2JpbGl6b24tbWlycm9yLnN2ZyIKICAgd2lkdGg9IjU1IgogICBoZWlnaHQ9IjU1IgogICBpbmtzY2FwZTp2ZXJzaW9uPSIxLjEgKGM0ZThmOWVkNzQsIDIwMjEtMDUtMjQpIgogICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSIKICAgeG1sbnM6c29kaXBvZGk9Imh0dHA6Ly9zb2RpcG9kaS5zb3VyY2Vmb3JnZS5uZXQvRFREL3NvZGlwb2RpLTAuZHRkIgogICB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogIDxkZWZzCiAgICAgaWQ9ImRlZnM4MSIgLz4KICA8c29kaXBvZGk6bmFtZWR2aWV3CiAgICAgaWQ9Im5hbWVkdmlldzc5IgogICAgIHBhZ2Vjb2xvcj0iIzUwNTA1MCIKICAgICBib3JkZXJjb2xvcj0iI2VlZWVlZSIKICAgICBib3JkZXJvcGFjaXR5PSIxIgogICAgIGlua3NjYXBlOnBhZ2VzaGFkb3c9IjAiCiAgICAgaW5rc2NhcGU6cGFnZW9wYWNpdHk9IjAiCiAgICAgaW5rc2NhcGU6cGFnZWNoZWNrZXJib2FyZD0iMCIKICAgICBzaG93Z3JpZD0iZmFsc2UiCiAgICAgaW5rc2NhcGU6em9vbT0iOC4wMzM3MDE1IgogICAgIGlua3NjYXBlOmN4PSIyMy43NzQ4NDQiCiAgICAgaW5rc2NhcGU6Y3k9IjI3LjY5NTgyNiIKICAgICBpbmtzY2FwZTp3aW5kb3ctd2lkdGg9IjE1MzYiCiAgICAgaW5rc2NhcGU6d2luZG93LWhlaWdodD0iNzk1IgogICAgIGlua3NjYXBlOndpbmRvdy14PSIwIgogICAgIGlua3NjYXBlOndpbmRvdy15PSIwIgogICAgIGlua3NjYXBlOndpbmRvdy1tYXhpbWl6ZWQ9IjEiCiAgICAgaW5rc2NhcGU6Y3VycmVudC1sYXllcj0iZzc0IgogICAgIHVuaXRzPSJpbiIgLz4KICA8ZwogICAgIGRhdGEtbmFtZT0iQ2FscXVlIDIiCiAgICAgaWQ9Imc3NiI+CiAgICA8ZwogICAgICAgZGF0YS1uYW1lPSJoZWFkZXIiCiAgICAgICBpZD0iZzc0Ij4KICAgICAgPHBhdGgKICAgICAgICAgZmlsbD0iIzQ3NDQ2NyIKICAgICAgICAgZD0ibSA0MS4wNjU0MTgsMzEuMTMxNjQ3IGMgMCw0Ljk2NDEyMiAtMS4zNDk4NjYsOC43ODAyOTEgLTQuMDQ5NTk0LDExLjQ0ODUwNyAtNC45MDE2MDcsNC4yNDQ4MTcgLTEyLjI2NjM5MSw0LjI0NDgxNyAtMTcuMTY3OTk3LDAgLTIuNjk5NzI5LC0yLjY0MzM5NSAtNC4wNDk1OTQsLTYuNDU5NTY0IC00LjA0OTU5NCwtMTEuNDQ4NTA3IDAsLTQuOTg4OTQxIDEuMzQ5ODY1LC04LjgwODIxMyA0LjA0OTU5NCwtMTEuNDU3ODE0IDQuOTAxNjA2LC00LjI0NDgxNiAxMi4yNjYzOSwtNC4yNDQ4MTYgMTcuMTY3OTk3LDAgMi42OTk3MjgsMi42NDk2MDEgNC4wNDk1OTQsNi40Njg4NzMgNC4wNDk1OTQsMTEuNDU3ODE0IHogTSAyOC40MzE4MjUsMjEuNzQ5NDU4IGMgLTMuNTkzMzAxLDAgLTUuMzg5OTUyLDMuMTI3Mzk3IC01LjM4OTk1Miw5LjM4MjE4OSAwLDYuMjU0Nzk1IDEuNzk2NjUxLDkuMzgyMTkxIDUuMzg5OTUyLDkuMzgyMTkxIDMuNTkzMzAxLDAgNS4zODk5NTIsLTMuMTI3Mzk2IDUuMzg5OTUyLC05LjM4MjE5MSAwLC02LjI1NDc5MiAtMS43OTY2NTEsLTkuMzgyMTg5IC01LjM4OTk1MiwtOS4zODIxODkgeiIKICAgICAgICAgaWQ9InBhdGg3MCIKICAgICAgICAgc29kaXBvZGk6bm9kZXR5cGVzPSJzY2NzY2Nzc3Nzc3MiCiAgICAgICAgIHN0eWxlPSJzdHJva2Utd2lkdGg6MC45NDA2MzkiIC8+CiAgICAgIDxwYXRoCiAgICAgICAgIGZpbGw9IiNmZmQ1OTkiCiAgICAgICAgIGQ9Im0gMjUuMTA4NTQ3LDEyLjQzNTc4NSBjIC0wLjM4NzUwNiwtMC44ODE0NjMgLTAuNTgxODE5LC0xLjgzMjcyOSAtMC41NzAzNjUsLTIuNzkyMzE5IC0wLjAxMTM1LC0wLjk1OTU4OSAwLjE4Mjg1OSwtMS45MTA4NTM5IDAuNTcwMzY1LC0yLjc5MjMxNzIgMS4xNDExOTcsLTAuNTU1MDA1OCAyLjQwNTM1NSwtMC44MjM2NzEyIDMuNjc4ODU3LC0wLjc4MTg1MDEgMS4yMjAxNzMsLTAuMDU3MjcyIDIuNDMzMTIyLDAuMjEzMDg2MyAzLjUwNzc0NywwLjc4MTg1MDEgMC4zODc1MDcsMC44ODE0NjMzIDAuNTgxODIsMS44MzI3MjgyIDAuNTcwMzY2LDIuNzkyMzE3MiAwLjAxMTUsMC45NTk1OSAtMC4xODI4NTksMS45MTA4NTYgLTAuNTcwMzY2LDIuNzkyMzE5IC0xLjEzMTk1NywwLjU4MDM3IC0yLjQwMzAyNSwwLjg1MDUwNSAtMy42Nzg4NTYsMC43ODE4NDkgLTEuMjIyNjE2LDAuMDg2MzcgLTIuNDQzNzg4LC0wLjE4NTgxOSAtMy41MDc3NDgsLTAuNzgxODQ5IHoiCiAgICAgICAgIGlkPSJwYXRoNzIiCiAgICAgICAgIHNvZGlwb2RpOm5vZGV0eXBlcz0iY2NjY2NjY2NjIgogICAgICAgICBzdHlsZT0ic3Ryb2tlLXdpZHRoOjAuOTQwNjM5IiAvPgogICAgPC9nPgogIDwvZz4KPC9zdmc+Cg==', 21  );
		add_submenu_page( $this->plugin_name, esc_html__( 'Instructions', $this->plugin_name ) , esc_html__( 'Instructions', $this->plugin_name ), 'manage_options', 'mobilizon_mirror_faq', array( $this, 'display_plugin_faq_page'), 1 );
		global $submenu;
		$submenu[$this->plugin_name][0][0] = esc_html__( 'Settings', $this->plugin_name );

	}


	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		/**
		 * Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
		 * The "plugins.php" must match with the previously added add_submenu_page first option.
		 * For custom post type you have to change 'plugins.php?page=' to 'edit.php?post_type=your_custom_post_type&page='
		 */
		$settings_link = array( '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '">' . esc_html__( 'Settings', $this->plugin_name ) . '</a>', );

		return array_merge( $settings_link, $links );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {

		include_once( 'partials/' . $this->plugin_name . '-admin-display.php' );

	}

	/**
	 * Render the FAQ page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_faq_page() {

		include_once( 'partials/' . $this->plugin_name . '-faq-display.php' );

	}




	/**
	 * Validate if the url is a valid mobilizon instance using webfinger
	 * https://webfinger.net/
	 * @since    1.0.0
	 *
	 * @param  string url
	 * @return bool
	 */
	public static function is_mobilizon_instance($url) {

		if (wp_http_validate_url($url)) {
			$response = wp_remote_get( $url . '/.well-known/nodeinfo/2.1');
			if (!is_wp_error($response)) {
				if ( isset($response['body']) ) {
					$webfinger = json_decode($response['body'], true);
					if( isset($webfinger['software']['name']) ){
						if ($webfinger['software']['name'] === "Mobilizon") {
							return true;
						}
					}
				}
			}
		}
		return false;

	}


	/**
	 * Validate if a group exists on a mobilizon server
	 * Only
	 * @param  string url
	 * @return bool
	 */
	public static function is_mobilizon_group($url, $group_name) {

		// Get API-endpoint from Instance URL
		$url = rtrim($url, '/');
		$url_array = array($url, "api");
		$endpoint = implode('/', $url_array);

		// Define query
		$query = "query {
						group(preferredUsername: \"${group_name}\") {
							type
						}
					}
					";

		// Define default GraphQL headers
		$headers = ['Content-Type: application/json', 'User-Agent: Minimal GraphQL client'];
		$body = array ('query' => $query);
		$args = array(
			'body'        => $body,
			'headers' 	  => $headers,
		);

		// Send HTTP-Query and return the response
		$response = wp_remote_post( $endpoint, $args );
		if ( ! is_wp_error($response) && isset( $response['body'] ) ) {
			$body = json_decode( $response['body'], true );
			if( isset( $body['data']['group']['type'] ) ){
				if ( $body['data']['group']['type'] === "GROUP" ) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Adds https scheme to user input url, if it's not present
	 * @since    1.0.0
	 *
	 * @param  string $url
	 * @return string $url with https
	 */
	public function add_http_if_not_present($url) {
		// Add https protokoll if not present
		if ( $ret = parse_url($url) && !isset($ret["scheme"]) )  {
			$host = parse_url($url, PHP_URL_HOST);
			if ($host != null) {
				$url = "https://{$host}";
			} else {
				$url = "https://{$url}";
			}
		}
		return $url;
	}

	/**
	 * Validate fields from admin area plugin settings form
	 * @since    1.0.0
	 *
	 * @param  mixed $input as field form settings form
	 * @return mixed as validated fields
	 */
	public function validate($input) {

		$options = get_option( $this->plugin_name );

		unset( $options['group_name'] );

		$old_group_names = $options['group_names'];
		$old_instance_url =  $options['instance_url'];

		// Instance URL
		// make input save:
		$instance_url_input = ( isset( $input['instance_url'] ) && ! empty( $input['instance_url'] ) ) ? esc_url_raw(  $input['instance_url'] ) : '';

		// Add https protokoll if not present
		$instance_url_input = $this->add_http_if_not_present($instance_url_input);

		// Set option if the input was a valid mobilizon url, other clean it
		$options['instance_url'] = ( $this->is_mobilizon_instance($instance_url_input) ) ? $instance_url_input : '';

		// Only check, if valid instance url is already set
		if ( $options['instance_url'] != '' ) {
			// If group name is not set, make it an empty array
			$group_names_input = ( isset( $input['group_names'] ) && ! empty( $input['group_names'] ) ) ? $input['group_names'] : array( '' );

			// Sanitize each group name.
			foreach ($group_names_input as $array_key => $group_name_input) {
				$group_names_input[ $array_key ] = sanitize_text_field( $group_name_input );
			}

			// Check if each group is existing on the remote Mobilizon server
			$validated_group_names = array();
			foreach ($group_names_input as $array_key => $group_name_input) {
				// Set option if the input was a group that exists
				if ( $this->is_mobilizon_group( $options['instance_url'], $group_name_input ) ) {
					array_push( $validated_group_names, $group_name_input );
				}
			}
			$options['group_names'] = $validated_group_names;
		} else {
			$options['group_names'] = array( '' );
		}

		// Set the "easy" options
		$options['event_archive_view'] = ( isset( $input['event_archive_view'] ) && ! empty( $input['event_archive_view'] ) ) ? sanitize_key( $input['event_archive_view'] ) : 'theme-default';
		$options['event_single_view'] =  ( isset( $input['event_single_view'] ) && ! empty( $input['event_single_view'] ) ) ? sanitize_key( $input['event_single_view'] ) : 'theme-default';

		$options['sync_interval'] = ( isset( $input['sync_interval'] ) && ! empty( $input['sync_interval'] ) ) ? absint( $input['sync_interval'] ) : 5;

		// Check if the settings for instance or group have changed
		if ( $old_instance_url != $options['instance_url'] || $old_group_names != $options['group_names'] ) {
			// If we have changed to a valid instance and group delte (old) cronjob if its there
			$timestamp = wp_next_scheduled( 'mobilizon_mirror_cron_refresh_events' );
			if ($timestamp) {
				wp_unschedule_event( $timestamp, 'mobilizon_mirror_cron_refresh_events');
			}
			// and create a new cron job for fetching the events if instance and group are valid!
			if (  $options['instance_url'] != '' && $options['group_names'] != array( '' ) ) {
				//  But of course only, if the cronjob does not exist yet, just ut be 100% sure!
				if ( ! wp_next_scheduled( 'mobilizon_mirror_cron_refresh_events' ) ) {
					wp_schedule_event( time(), 'mobilizon_mirror_refresh_interval', 'mobilizon_mirror_cron_refresh_events' );
				}
			}
		}


		// $options['group_names'] = array( 'strike', 'loophole' );
		return $options;

	}


	/**
	 * Main function which registers the plugins settings and updates it's options
	 * @since    1.0.0
	 */
	public function options_update() {
		register_setting( $this->plugin_name, $this->plugin_name, array(
		'sanitize_callback' => array( $this, 'validate' ),
		) );

	}


	/**
	 * Callback for inline form validation in the admin area, whether the entered
	 * mobilizon group exists on the server
	 * @since    1.0.0
	 *
	 * @return bool
	 */
	public function is_mobilizon_group_callback() {
		// Receive entered instance_url
		$instance_url =  esc_url_raw( $_POST['instance_url'] );
		$group_name = sanitize_text_field(  $_POST['group_name'] );

		$instance_url = $this->add_http_if_not_present($instance_url);

		// Send json respone https://developer.wordpress.org/reference/hooks/wp_ajax_action/
		// as boolean without key, see https://datatracker.ietf.org/doc/html/rfc7159
		wp_send_json( $this->is_mobilizon_group($instance_url, $group_name) );

		// Don't forget to stop execution afterward.
		wp_die();

	}


	/**
	 * Callback for inline form validation in the admin area, whether the entered
	 * mobilizon instance (=server) exists.
	 * @since    1.0.0
	 *
	 * @return bool
	 */
	public function is_mobilizon_instance_callback() {
		// Receive entered instance_url
		$instance_url = esc_url_raw(  $_POST['instance_url'] ) ;

		$instance_url = $this->add_http_if_not_present($instance_url);

		// Send json respone https://developer.wordpress.org/reference/hooks/wp_ajax_action/
		// as boolean without key, see https://datatracker.ietf.org/doc/html/rfc7159
		wp_send_json( $this->is_mobilizon_instance($instance_url) );

		// Don't forget to stop execution afterward.
		wp_die();

	}


}
