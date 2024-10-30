<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/public
 * @author     André Menrath <andre.menrath@posteo.de>
 */
class Mobilizon_Mirror_Public {

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
	 * @since   1.0.0
	 * @param   string $plugin_name  The name of the plugin.
	 * @param   string $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mobilizon-mirror-public.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mobilizon-mirror-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 *  Filter and selectively load the mobilizon template inside plugin.
	 *
	 * @since    1.0.0
	 */
	public function mobilizon_mirror_event_template($template) {
		if ( is_post_type_archive('mobilizon_event') ) {
			$theme_files = array('archive-mobilizon-mirror-event.php', 'mobilizon-mirror/archive-mobilizon-mirror-event.php');
			$exists_in_theme = locate_template($theme_files, false);
			if ( $exists_in_theme != '' ) {
				return $exists_in_theme;
			} else if (get_option($this->plugin_name)['event_archive_view'] == 'theme-default') {
				return $template;
			} else if (get_option($this->plugin_name)['event_archive_view'] == 'list') {
				return plugin_dir_path(__FILE__) . 'archive-mobilizon-mirror-event-list.php';
			} else {
				return plugin_dir_path(__FILE__) . 'archive-mobilizon-mirror-event-card.php';
			}
		} else if ( get_post_type() == 'mobilizon_event' ) {
			$theme_files = array('single-mobilizon-mirror-event.php', 'mobilizon-mirror/single-mobilizon-mirror-event.php');
			$exists_in_theme = locate_template($theme_files, false);
			if ( $exists_in_theme != '' ) {
				return $exists_in_theme;
			} else if (get_option($this->plugin_name)['event_single_view'] == 'theme-default') {
				return $template;
			} else if (get_option($this->plugin_name)['event_single_view'] == 'top') {
				return plugin_dir_path(__FILE__) . 'single-mobilizon-mirror-event-header-image.php';
			} else {
				return plugin_dir_path(__FILE__) . 'single-mobilizon-mirror-event-side-image.php';
			}
		}
		return $template;
	}

	/**
	 *  Function that hooks into the main search and adds the mobilizon events to it
	 *
	 * @since    1.0.0
	 *
	 * @param object $query   The search query object of WordPress.
	 */
	public function mobilizon_mirror_include_mobilizon_event_in_search_results( $query ) {
		if ( $query->is_main_query() && $query->is_search() && ! is_admin() ) {
			// TODO: maybe there is a better solution to just add mobilizon_event!
			$query->set( 'post_type', array( 'post', 'page', 'mobilizon_event' ) );
		}
	}

	/**
	 * Replace the post datetime with the event start datetime in the rest api
	 *
	 * @link       https://graz.social/@linos
	 * @since      1.1.0
	 *
	 * @package    Mobilizon_Mirror
	 * @subpackage Mobilizon_Mirror/includes
	 */
	// add_action(
	// 	'rest_api_init',
	public function change_post_date_in_rest_api() {
			register_rest_field(
				'mobilizon_event',
				'date',
				array(
					'get_callback' => function( $event ) {
						return get_post_meta( $event['id'], 'beginsOn', true );
					},
				),
			);
			register_rest_field(
				'mobilizon_event',
				'date_gmt',
				array(
					'get_callback' => function( $event ) {
						return get_post_meta( $event['id'], 'beginsOn', true );
					},
				),
			);
			register_rest_field(
				'mobilizon_event',
				'modified',
				array(
					'get_callback' => function( $event ) {
						return get_post_meta( $event['id'], 'beginsOn', true );
					},
				),
			);
			register_rest_field(
				'mobilizon_event',
				'modified_gmt',
				array(
					'get_callback' => function( $event ) {
						return get_post_meta( $event['id'], 'beginsOn', true );
					},
				),
			);
		}

	/**
	 * Replace the post datetime with the event start datetime in get_the_date() calls
	 *
	 * @link       https://graz.social/@linos
	 * @since      1.1.0
	 *
	 * @package    Mobilizon_Mirror
	 * @subpackage Mobilizon_Mirror/includes
	 *
	 * @return string $the_event_start_date ... as it is displayed in the frontend
	 */
	function change_post_date($the_date) {
		if ( 'mobilizon_event' === get_post()->post_type ) {
			$event_start_time     = get_post_meta( get_post()->ID, 'beginsOn', true );
			$event_start_time     = str_replace( 'T', ' ', $event_start_time );
			$event_start_time     = str_replace( 'Z', '', $event_start_time );
			$timezone             = new DateTimeZone( 'UTC' );
			$event_start_datetime = date_create_immutable_from_format( 'Y-m-d H:i:s', $event_start_time, $timezone );
			$the_date             = wp_date( get_option( 'date_format' ), $event_start_datetime->getTimestamp(), $timezone );
		}
		return $the_date;
	}

	/**
	 * Sort queries of the events always ascending by the event start datetime
	 *
	 * @link       https://graz.social/@linos
	 * @since      1.1.0
	 *
	 * @package    Mobilizon_Mirror
	 * @subpackage Mobilizon_Mirror/includes
	 *
	 * @return string $query the adopted query
	 */
	function change_default_sort ( $query ) {
		// Only fire if we want it!
		// old version: if ( $query->is_archive && 'mobilizon_event' === $query->query['post_type'] && 'date' === $query->query['orderby'] ) {
		if ( $query->is_post_type_archive && 'mobilizon_event' === $query->query['post_type'] ) {
			$query->query_vars['order'] = 'ASC';
			$query->query_vars['orderby'] = 'meta_value';
			$query->query_vars['meta_key'] = 'beginsOn';
			$query->query_vars['meta_type'] = 'DATETIME';
		}
		return $query;
	}

	/**
	 * Add the event metadata via modifying the content, not via some template.
	 *
	 * @link       https://graz.social/@linos
	 * @since      1.1.0
	 *
	 * @package    Mobilizon_Mirror
	 * @subpackage Mobilizon_Mirror/includes
	 *
	 * @return string $query the adopted query
	 */
	function create_event_content( $content ) {
		if ( get_post_type() !== 'mobilizon_event' || 'theme-default' !== get_option('mobilizon-mirror')['event_single_view'] ) {
			return $content;
		}

		$metadata = get_post_custom();

		$begin_unix_time = strtotime( $metadata['beginsOn'][0] );
		$end_unix_time   = strtotime( $metadata['endsOn'][0] );

		if ( $metadata['place'][0] && $metadata['street'][0] && $metadata['postalCode'][0] ) {
			$place_header = esc_html( translate ( 'Place', 'mobilizon-mirror' ) );
			$place = join(
				'<br>',
				array(
					esc_attr( $metadata['place'][0] ),
					esc_attr( $metadata['street'][0] ),
					esc_attr( $metadata['postalCode'][0] ) . ' ' . esc_attr( $metadata['city'][0] ),
				)
			);
			$place_html = '<h4>' . $place_header . '</h4><div class="eventMetaDataBlock">' . $place . '</div>';
		} else {
			$place_html = '';
		}

		$datetime_header = esc_html( translate ( 'Date and Time', 'mobilizon-mirror' ) );
		if ( wp_date( 'd. M. Y', $begin_unix_time ) === wp_date( 'd. M. Y', $end_unix_time ) ) {
			$datetime = esc_html( wp_date( 'D, d. M. Y ', $begin_unix_time ) ) . esc_html__( 'from', 'mobilizon-mirror' ) . esc_html( wp_date( ' G:i ', $begin_unix_time ) ) . ' ' . esc_html__( 'to', 'mobilizon-mirror' ) . ' ' . esc_html( wp_date( 'G:i', $end_unix_time ) );
		} else {
			$datetime = esc_html( wp_date( 'D, d. M. Y ', $begin_unix_time ) ) . esc_html__( 'from', 'mobilizon-mirror' ) . esc_html( wp_date( ' G:i ', $begin_unix_time ) ) . ' ' . esc_html__( 'to', 'mobilizon-mirror' ) . ' <nobr>' . esc_html( wp_date( 'D, d. M. Y ', $end_unix_time ) ) . '</nobr> ' . esc_html( wp_date( 'G:i', $end_unix_time ) );
		}

		$organizer_header = esc_html( translate ( 'Organized by', 'mobilizon-mirror' ) );
		$organizer_url = esc_url( $metadata['organizerURL'][0] );
		$organizer_name = esc_attr( $metadata['organizerName'][0] );

		$website_header = esc_html( translate ( 'Website', 'mobilizon-mirror' ) );
		$website_url = esc_url( $metadata['onlineAddress'][0] );
		$website_name = esc_url( wp_parse_url( $metadata['onlineAddress'][0], PHP_URL_HOST ) );

		$origin_header = esc_html( translate ( 'Originally posted on ', 'mobilizon-mirror' ) );
		$origin_url = esc_url( $metadata['url'][0] );
		$url_parts = wp_parse_url( $metadata['url'][0] );
		$origin_name=  esc_url( 'https://' . $url_parts['host'] );

		return '
			<div id="event">
				<div class="event-description-wrapper">
					<aside class="event-metadata">
						' . $place_html . '
						<h4>' . $datetime_header . '</h4>
						<div class="eventMetaDataBlock">' . $datetime . '</div>
						<h4>'.  $organizer_header . '</h4>
						<div class="eventMetaDataBlock">
							<a href="' . $organizer_url .'" target="blank">'. $organizer_name .'</a>
						</div>
						<h4>'. $website_header . '</h4>
						<div class="eventMetaDataBlock">
							<a href="'. $website_url .'" target="blank">' . $website_name . '</a>
						</div>
					</aside>
					<article class="event-description">' . $content . '</article>
				</div>
				<hr>
				<footer class="event-url">
					' . $origin_header . '<a href="' . $origin_url . '">' . $origin_name . '</a>
				</footer>
			</div>
		';
	}
}
