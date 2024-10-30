<?php
/**
 * This is used for fetching names of all listed mobilizon instances out there, only fired once at plugin installation
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/includes
 */

/**
 * Class for fetching information from mobilizon https://instances.joinmobilizon.org/instances
 *
 * Via the API of this webservice: https://instances.joinmobilizon.org/api/v1/instances.
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/includes
 */
class Mobilizon_Mirror_Instances {
	/**
	 * This function fetches the number of mobilizon instances out there
	 *
	 * @since   1.0.0
	 * @access  private
	 * @return  mixed $event An event is returned if checks are passed, false if not!
	 */
	private static function get_number_of_instances() {
		$response = wp_remote_get( 'https://instances.joinmobilizon.org/api/v1/instances?start=0&count=1' );
		if ( ! is_wp_error( $response ) ) {
			if ( isset( $response['body'] ) ) {
				$instances = json_decode( $response['body'], true );
				if ( isset( $instances['total'] ) && is_int( $instances['total'] ) ) {
					return $instances['total'];
				}
			}
		}
		return null;
	}

	/**
	 * Get list with names of all mobilizon instances
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return   mixed $event Event is returned if checks are passed, false if not!
	 */
	public static function get_instance_list() {
		// Get number of instances to query.
		$number_of_instances = self::get_number_of_instances();

		// If it's null, there has been some error, so don't do further action.
		if ( is_null( $number_of_instances ) ) {
			return null;
		}

		// Convert the number to int, so that we can use it in our query url.
		$number_of_instances = strval( $number_of_instances );

		// Query all mobilizon instances!
		$response = wp_remote_get( "https://instances.joinmobilizon.org/api/v1/instances?start=0&count={$number_of_instances}" );
		if ( ! is_wp_error( $response ) ) {
			if ( isset( $response['body'] ) ) {
				$instances = json_decode( $response['body'], true );
				if ( isset( $instances['data'] ) ) {
					return array_column( $instances['data'], 'host' );
				}
			}
		}
		return null;
	}

	/**
	 * Packs the information of all the instances into an html <datalist>
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return   mixed $instances_datalist
	 */
	public static function get_instance_datalist() {
		// Get array with mobilizon instance hostnames, that means domains!
		$instances = self::get_instance_list();

		// If it's null, there has been some error, so don't do further action.
		if ( is_null( $instances ) ) {
			return null;
		}

		$instances_datalist = '<datalist id="mobilizon-mirror-instances">';
		foreach ( $instances as $instance ) {
			$instances_datalist = $instances_datalist . '<option value="' . $instance . '">';
		}
		$instances_datalist = $instances_datalist . '</datalist>';

		return $instances_datalist;
	}
}
