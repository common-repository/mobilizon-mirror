<?php
/**
 * Wrapper for fetching data from mobilizon instances
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/includes
 */

/**
 *
 * This class defines all code necessary to fetch data from mobilizon
 *
 * @since      1.0.0
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/includes
 * @author     André Menrath <andre.menrath@posteo.de>
 */
class Mobilizon_Mirror_API {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The base URL of the Mobilizon Instance
	 *
	 * @since    1.1.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $base_url;

	/**
	 * The API endpoint of the Mobilizon Instance
	 *
	 * @since    1.1.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $endpoint;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 * @param  string $plugin_name The name of this plugin.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
		// Get API-endpoint and cleaned base URL from user input instance URL.
		$base_url       = esc_url( get_option( $this->plugin_name )['instance_url'] );
		$this->base_url = rtrim( $base_url, '/' );
		$url_array      = array( $this->base_url, 'api' );
		$endpoint       = implode( '/', $url_array );
		$this->endpoint = $this->addhttp( $endpoint );
	}

	/**
	 * Adds https:// protokoll if no url scheme is present
	 *
	 * @since   1.0.0
	 * @access  private
	 * @param   string $url The URL which is processed.
	 */
	private function addhttp( $url ) {
		if ( ! preg_match( '~^(?:f|ht)tps?://~i', $url ) ) {
			$url = 'https://' . $url;
		}
		return $url;
	}

	/**
	 * Adds https:// protokoll if no url scheme is present
	 *
	 * @since   1.0.0
	 * @access  private
	 * @param   string $query    the graphql query string see https://framagit.org/framasoft/mobilizon/-/blob/master/schema.graphql.
	 */
	private function do_mobilizon_query( $query ) {
		// Define default GraphQL headers.
		$headers = array( 'Content-Type: application/json', 'User-Agent: Minimal GraphQL client' );
		$body    = array( 'query' => $query );
		$args    = array(
			'body'    => $body,
			'headers' => $headers,
		);

		// Send HTTP-Query and return the response.
		return( wp_remote_post( $this->endpoint, $args ) );
	}

	/**
	 * Inserts or updates a new post of post type mobilizon_events
	 *
	 * @since   1.0.0
	 * @access  private
	 * @param   array $event    Contains all the WordPress default and metadata fields for a post.
	 * @return  mixed $post_id  Is the post_id as int on success, else WP_Error or 0.
	 */
	private function insert_or_update_mobilizon_event_as_post( $event ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$my_post = array(
			'post_title'        => $event['title'],
			'post_date_gmt'     => str_replace( 'T', ' ', str_replace( 'Z', '', $event['insertedAt'] ) ),
			'post_modified_gmt' => str_replace( 'T', ' ', str_replace( 'Z', '', $event['updatedAt'] ) ),
			'post_content'      => $event['description'],
			'post_status'       => 'publish',
			'post_type'         => 'mobilizon_event',
			'post_status'       => ( 'PUBLIC' === $event['visibility'] ) ? 'publish' : 'private',
		);

		if ( isset( $event['post_id'] ) ) {
			// In this case it is an updated event from Mobilizon.
			$my_post['ID'] = $event['post_id'];
			// Update the post!
			$post_id = wp_update_post( $my_post );
		} else {
			// Else it is a new Event from Mobilizon. Save it locally.
			$post_id = wp_insert_post( $my_post );
		}

		// Abort if inserting or updating the post didn't work.
		if ( 0 === $post_id || is_wp_error( $post_id ) ) {
			return $post_id;
		}

		// Set Mobilizon tags as WordPress post tags
		wp_set_post_tags( $post_id, array_column( $event['tags'], 'title' ) );

		// Set the federated group name of Mobilizon as term
		wp_set_object_terms( $post_id, $event['attributedTo']['preferredUsername'], 'mobilizon_group');

		/**
		 * Some notes on the bedingsOn and endsOn keys:
		 *
		 * The `DateTime` scalar type represents a date and time in the UTC
		 * timezone. The DateTime appears in a JSON response as an ISO8601 formatted
		 * string, including UTC timezone ("Z"). The parsed date and time string will
		 * be converted to UTC if there is an offset.
		 */
		update_post_meta( $post_id, 'beginsOn', $event['beginsOn'] );
		update_post_meta( $post_id, 'endsOn', $event['endsOn'] );
		update_post_meta( $post_id, 'onlineAddress', $event['onlineAddress'] );
		update_post_meta( $post_id, 'organizerName', ( isset( $event['organizerActor']['name'] ) ) ? $event['organizerActor']['name'] : $event['attributedTo']['name'] );
		update_post_meta( $post_id, 'organizerURL', ( isset( $event['organizerActor']['url'] ) ) ? $event['organizerActor']['url'] : $event['attributedTo']['url'] );
		update_post_meta( $post_id, 'street', ( isset( $event['physicalAddress']['street'] ) ) ? $event['physicalAddress']['street'] : '' );
		update_post_meta( $post_id, 'place', ( isset( $event['physicalAddress']['description'] ) ) ? $event['physicalAddress']['description'] : '' );
		update_post_meta( $post_id, 'city', ( isset( $event['physicalAddress']['locality'] ) ) ? $event['physicalAddress']['locality'] : '' );
		update_post_meta( $post_id, 'postalCode', ( isset( $event['physicalAddress']['postalCode'] ) ) ? $event['physicalAddress']['postalCode'] : '' );
		update_post_meta( $post_id, 'updatedAt', $event['updatedAt'] );
		update_post_meta( $post_id, 'url', $event['url'] );
		update_post_meta( $post_id, 'uuid', $event['uuid'] );
		update_post_meta( $post_id, 'status', $event['status'] );
		// Try download the events header image to the local wordpress medias and add attach it to the wordpress event-post.
		if ( isset( $event['picture']['url'] ) ) {
			$image = media_sideload_image( $event['picture']['url'], $post_id, $event['picture']['alt'], 'id' );
		}
		if ( isset( $image ) && ! is_wp_error( $image ) ) {
			set_post_thumbnail( $post_id, $image );
		} else {
			// Inset the default card image if the other one failed or was not present.
			$mobilizon_default_card = $this->base_url . '/img/mobilizon_default_card.png';
			$image = media_sideload_image( $mobilizon_default_card, $post_id, $event['picture']['alt'], 'id' );
			if ( ! is_wp_error( $image ) ) {
				set_post_thumbnail( $post_id, $image );
			}
		}
		return $post_id;
	}

	/**
	 * Sets up a query to fetch an list of events from the set mobilizon server, not the details yet
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function mobilizon_set_up_event_list_query() {
		$group_names = get_option( $this->plugin_name )['group_names'];
		$date_now    = gmdate( 'c' );
		$query       = 'query  {';
		foreach ( $group_names as $group_name ) {
			$query .= "${group_name}: group(preferredUsername: \"${group_name}\") {
							organizedEvents(afterDatetime: \"{$date_now}\", limit: 1000) {
								elements {
									uuid,
									updatedAt,
								}
							}
						}";
		}
		$query .= '}';
		return $query;
	}

	/**
	 * Sanitizes a mobilizon event (don't trust any third party server)
	 * And perform some checks on the data as well!
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array $array Contains the event infomrations.
	 * @return   array $array Sanitzed version.
	 */
	private function recursive_sanitize_event( $array ) {
		foreach ( $array as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = $this->recursive_sanitize_event( $value );
			} else {
				if ( 'url' === $key || 'onlineAddress' === $key ) {
					$value = esc_url_raw( $value );
				} elseif ( 'description' === $key ) {
					$value = wp_kses_post( $value );
				} else {
					$value = sanitize_text_field( $value );
				}
			}
		}
		return $array;
	}

	/**
	 * Checks and sanitizes mobilizon event (don't trust any third party server)
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    mixed $event Raw version.
	 * @return   mixed $event Event is returned if checks are passed, false if not!
	 */
	private function check_and_sanitize_mobilizon_event( $event ) {
		// Check the responded event from the moblizon server.
		if ( ! is_array( $event ) ) {
			return false;
		}

		// Check if all the requeset fields are set.
		$required_fields = array(
			'uuid',
			'updatedAt',
			'insertedAt',
			'title',
			'attributedTo',
			'url',
			'beginsOn',
			'status',
			'visibility',
			'tags',
		);
		foreach ( $required_fields as $required_field ) {
			if ( ! isset( $event[ $required_field ] ) ) {
				log( 'Could not mirror event because field ' . $required_field . ' was missing' );
				return false;
			}
		}
		// Check if uuid is a valid v4 UUID.
		$uuid_v4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
		if ( ! preg_match( $uuid_v4, $event['uuid'] ) ) {
			return false;
		}

		// TODO: check the dates and maybe already process them here from "2022-01-23T08:39:12Z" format to "2022-01-23 08:39:12" at the moment it is done in funciton insert_or_update_mobilizon_event_as_post!

		// Check the tags!
		if ( ! isset( $event['tags'] ) || ! is_array( $event['tags'] ) ) {
			return false;
		}
		foreach ( $event['tags'] as $tag ) {
			if ( ! isset( $tag['title'] ) ) {
				return false;
			}
		}

		// Sanitize everything!
		$event = $this->recursive_sanitize_event( $event );

		return $event;
	}

	/**
	 * Gets an event and all its details from an mobilizon server by the events uuid
	 *
	 * @since   1.0.0
	 * @access  private
	 * @param   string $uuid See definition at https://de.wikipedia.org/wiki/Universally_Unique_Identifier.
	 */
	private function get_mobilizon_event( $uuid ) {
		$query = "query  {
					event(uuid: \"${uuid}\") {
						uuid,
						insertedAt,
						updatedAt,
						title,
						organizerActor {
							name,
							url
						},
						attributedTo {
							name,
							url,
							preferredUsername
						},
						url,
						beginsOn,
						endsOn,
						description,
						onlineAddress,
						status,
						visibility,
						picture {
							url,
							alt
						},
						tags {
							slug,
							title
						},
						physicalAddress {
							street,
							description,
							locality,
							postalCode
						}
					}
				}";

		// Execute the event query to the mobilizon instance.
		$response = $this->do_mobilizon_query( $query );

		// Check if the HTTP-Query was successful.
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return false;
		}

		$event = json_decode( wp_remote_retrieve_body( $response ), true )['data']['event'];
		$event = $this->check_and_sanitize_mobilizon_event( $event );
		return $event;
	}

	/**
	 * Gets a list of all local posts of post_type 'mobilizon_event'
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return   array      keys: 'uuid', values: subarray with keys 'udpatedAt' and 'post_id'
	 */
	private function get_local_mobilizon_events() {
		$args         = array(
			'numberposts' => -1,
			'post_type'   => 'mobilizon_event',
		);
		$the_query    = new WP_Query( $args );
		$local_events = array();

		if ( $the_query->have_posts() ) :
			while ( $the_query->have_posts() ) :
				$the_query->the_post();
				$local_events[ get_post_meta( get_the_ID(), 'uuid', true ) ] =
					array(
						'updatedAt' => get_post_meta( get_the_ID(), 'updatedAt', true ), // TODO Replace this with post_modified.
						'post_id'   => get_the_ID(),
					);
			endwhile;
		endif;

		wp_reset_postdata();
		return $local_events;
	}

	/**
	 * This functions validates the response scheme from the mobilizon event query
	 *
	 * It should like like this, for example:
	 * {
	 * 	"data": {
	 *	  "groupname1": {
	 *		"organizedEvents": {
	 *		  "elements": [
	 * 			{
	 * 			  "updatedAt": "2021-12-29T08:36:27Z",
	 * 			  "uuid": "6e76dcf9-1618-4728-bc05-e572503156c0"
	 *			},
	 *			{
	 *		      "updatedAt": "2022-01-22T09:58:28Z",
	 * 			  "uuid": "02560cac-9524-46c1-95c6-aae18c984004"
	 * 			}
	 *		  ]
	 * 		}
	 * 	  },
	 * 	  "groupname2": {
	 * 		"organizedEvents": {
	 * 		  "elements": [
	 * 			{
	 * 			  "updatedAt": "2022-01-04T09:03:19Z",
	 * 			  "uuid": "e2b1e563-18ba-4fdf-8643-0a08638dc7fe"
	 * 			}
	 * 		  ]
	 * 		}
	 * 	  }
	 *  }
	 * }
	 *
	 * @since   1.1.0
	 * @access  private
	 * @param   mixed   $value the decoded json response
	 * @return  boolean If the validation passed or not.
	 */
	function validate_mobilizon_event_list_response ( $value ) {
		$group_names_regex = implode( '|', get_option( $this->plugin_name )['group_names'] );
		$args = array(
			'type' => 'object',
			'additionalProperties' => false,
			'required' => true,
			'properties' => array(
				'data' => array(
					'type' => 'object',
					'additionalProperties' => false,
					'required' => true,
					'patternProperties'    => array(
						$group_names_regex => array(
							'type' => 'object',
							'additionalProperties' => false,
							'required' => true,
							'properties' => array(
								'organizedEvents' => array(
									'type'       => 'object',
									'additionalProperties' => false,
									'required' => true,
									'properties' => array(
										'elements' => array(
											'type' => array(
												'type'  => 'array',
												'items' => array(
													'type'       => 'object',
													'properties' => array(
														'updatedAt'  => array(
															'type'     => 'string',
															'format'   => 'date-time',
															'required' => true,
														),
														'uuid' => array(
															'type'     => 'string',
															'format'   => 'uuid',
															'required' => true,
														),
													),
												),
											),
										),
									),
								),
							),
						),
					),
				),
			),
		);
		$param = "Mobilizon response with the event list is not valid!";
		return rest_validate_value_from_schema( $value, $args, $param );
	}

	/**
	 * Gets a list of all remote events from the mobilizon server and group as set up in the options.
	 *
	 * The value 'updatedAt' represents a date and time in the UTCtimezone.
	 * It is an ISO8601 formatted string, including UTC timezone ("Z").
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return   array      keys: 'uuid', values: 'updatedAt'
	 */
	private function get_remote_mobilizon_events() {
		// Get the query for all events (uuid, updatedAT).
		$query = $this->mobilizon_set_up_event_list_query();

		// Execute the event query to the mobilizon instance.
		$response = $this->do_mobilizon_query( $query );

		// Check if the HTTP-Query was successful.
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return false;
		}

		// Extract the events as an array from the query's response body.
		$body = json_decode( wp_remote_retrieve_body( $response ), true, 7 );

		// Validate response we got, its a remote server: don't trust it!
		if ( is_wp_error( $this->validate_mobilizon_event_list_response( $body ) ) ) {
			return false;
		}

		// The response is still nested multiple times, but at this state we don't need to proccess by group.
		$uuid_list = [];
		$updated_list = [];
		foreach ( $body['data'] as $events_per_group ) {
			$uuid_list = array_merge( array_column( $events_per_group['organizedEvents']['elements'], 'uuid' ), $uuid_list );
			$updated_list = array_merge( array_column( $events_per_group['organizedEvents']['elements'], 'updatedAt' ), $updated_list );
		}

		// Remote Event List by uuid as key:
		// The uuid serves as the array key, the updatedAt's are directly in the values!
		return array_combine( $uuid_list, $updated_list );
	}

	/**
	 * Compares list of remote event with list of local events and insert, update or delete events local ones
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array $local    local_events.
	 * @param    array $remote   remote_events.
	 */
	private function update_mobilizon_events( $local, $remote ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Get events which uuids are not in remote but on local.
		$to_delete = array_keys( array_diff_key( $local, $remote ) );

		// Delete this events from WordPress.
		foreach ( $to_delete as $uuid ) {
			$post_id = $local[ $uuid ]['post_id'];
			wp_delete_attachment( get_post_thumbnail_id( $local[ $uuid ]['post_id'] ), true );
			if ( wp_delete_post( $post_id, true ) ) {
				unset( $local [ $uuid ] );
			} else {
				// Maybe the transient with the local mirrored events got currupted
				// This should not happen anyway...
				delete_transient( 'mobilizon_mirror_cached_events_list' );
				log( 'Mobilizon Mirror failed to delete an event, please report. ID: ' . $post_id . ', uuid: ' . $uuid );
			}
		}

		// Get events which uuids are in remote but not in local.
		$to_add = array_keys( array_diff_key( $remote, $local ) );
		// Insert the new events locally in wordress.
		// TODO: Hack with array_slice, only sync 3 events per update cycle because of rate limiting.
		foreach ( array_slice($to_add, 0, 3) as $uuid ) {
			// Fetch event details from the remote mobilizon server, = false when no error.
			$event_details = $this->get_mobilizon_event( $uuid );
			// Skip this event, if there was an error getting it from remote.
			if ( ! $event_details ) {
				continue;
			}
			// If there was success go ahead an insert it locally.
			$post_id = $this->insert_or_update_mobilizon_event_as_post( $event_details );
			if ( 0 !== $post_id && ! is_wp_error( $post_id ) ) {
				// Update the local event list, if the insertion was successfull.
				$local[ $uuid ] = array(
					'post_id'   => $post_id,
					'updatedAt' => $remote[ $uuid ],
				);
			}
		}

		// Get events with same uuids but different updatedAt values.
		$to_update = array_keys( array_diff_assoc( array_intersect_key( $remote, $local ), array_combine( array_keys( $local ), array_column( $local, 'updatedAt' ) ) ) );
		// Update the local events.
		foreach ( $to_update as $uuid ) {
			// Attachment will also be fetched again, so delete the old one!
			wp_delete_attachment( get_post_thumbnail_id( $local[ $uuid ]['post_id'] ), true );
			// Fetch event details again from the remote mobilizon server.
			$event_details = $this->get_mobilizon_event( $uuid );
			// Skip this event, if there was an error getting it from remote.
			if ( ! $event_details ) {
				continue;
			}
			// Insert the existing local post id, so that we update in the next step.
			$event_details['post_id'] = $local[ $uuid ]['post_id'];
			$post_id                  = $this->insert_or_update_mobilizon_event_as_post( $event_details );

			if ( 0 !== $post_id && ! is_wp_error( $post_id ) ) {
				// update the local event list, if the update was successfull.
				$local[ $uuid ] = array(
					'post_id'   => $post_id,
					'updatedAt' => $remote[ $uuid ],
				);
			}
		}

		// Update the transient with the local mobilizon events, but only if anything has changed!
		if ( count( $to_delete ) + count( $to_add ) + count( $to_update ) !== 0 ) {
			delete_transient( 'mobilizon_mirror_cached_events_list' );
			set_transient( 'mobilizon_mirror_cached_events_list', $local );
		}
	}


	/**
	 * This is the main function which takes care of mirroring the mobilizon events on WordPress
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function refresh_mobilizon_events() {
		// Return if relevant options are not set!
		if ( '' === get_option( $this->plugin_name )['group_names'] || '' === get_option( $this->plugin_name )['instance_url'] ) {
			return;
		}

		// Get list of local events.
		if ( get_transient( 'mobilizon_mirror_cached_events_list' ) ) {
			$local_events = get_transient( 'mobilizon_mirror_cached_events_list' );
		} else {
			$local_events = $this->get_local_mobilizon_events();
			set_transient( 'mobilizon_mirror_cached_events_list', $local_events );
		}

		// Get list of remote events.
		$remote_events = $this->get_remote_mobilizon_events();

		// Result is an array with the remote events (may be empty if no events scheduled) if the response of the server was successful.
		if ( is_array( $remote_events ) ) {
			// Compare remote with local and insert, update or delete events.
			$this->update_mobilizon_events( $local_events, $remote_events );
		}
	}

	/**
	 * Add the custom interval how often the events are synced
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array $schedules Is used/needed for the WordPress scheduling system.
	 * @return   array $schedules Is used/needed for the WordPress scheduling system.
	 */
	public function mobilizon_mirror_add_minitly( $schedules ) {
		// Get the sync interval: it is set, if the options page has been visited and saved once.
		$interval_in_minutes = absint( get_option( $this->plugin_name )['sync_interval'] );
		if ( empty( $interval_in_minutes ) ) {
			return $schedules;
		}

		// Make sure the interval is valid, TODO: already sanitize the setting when it is being set?
		if ( 2 > $interval_in_minutes || 60 < $interval_in_minutes  ) {
			$interval_in_minutes = 10;
		}

		$interval_in_seconds = $interval_in_minutes * 60;

		// Add a 'weekly' schedule to the existing set!
		$schedules['mobilizon_mirror_refresh_interval'] = array(
			'interval' => $interval_in_seconds,
			'display'  => esc_html__( 'Every ' . strval( $interval_in_minutes ) .  ' minutes', 'mobilizon-mirror' ),
		);

		return $schedules;
	}
}
