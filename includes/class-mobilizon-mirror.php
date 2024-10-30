<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://graz.social/@linos
 * @since      1.0.0
 *
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror/includes
 * @author     André Menrath <andre.menrath@posteo.de>
 */
class Mobilizon_Mirror {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Mobilizon_Mirror_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MOBILIZON_MIRROR_VERSION' ) ) {
			$this->version = MOBILIZON_MIRROR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'mobilizon-mirror';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Mobilizon_Mirror_Loader. Orchestrates the hooks of the plugin.
	 * - Mobilizon_Mirror_I18n. Defines internationalization functionality.
	 * - Mobilizon_Mirror_Admin. Defines all hooks for the admin area.
	 * - Mobilizon_Mirror_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mobilizon-mirror-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mobilizon-mirror-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mobilizon-mirror-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mobilizon-mirror-public.php';

		/**
		 * Custom Post Types (Adds the event post type where fetched mobilizon events are stored)
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mobilizon-mirror-post_types.php';

		/**
		 * The API for communicating with mobilizon for fetching events
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mobilizon-mirror-api.php';

		$this->loader = new Mobilizon_Mirror_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Mobilizon_Mirror_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Mobilizon_Mirror_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Mobilizon_Mirror_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$plugin_post_types = new Mobilizon_Mirror_Post_Types();

		$mobilizon_api = new Mobilizon_Mirror_API( $this->get_plugin_name() );

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
		$this->loader->add_action( 'init', $plugin_post_types, 'create_custom_post_type', 999 );

		// This is our main controller, which looks when to fetch events from the remote mobilizon instance.
		$this->loader->add_filter( 'cron_schedules', $mobilizon_api, 'mobilizon_mirror_add_minitly' );
		$this->loader->add_action( 'mobilizon_mirror_cron_refresh_events', $mobilizon_api, 'refresh_mobilizon_events' );

		// Add the mobilizon post_type to the tag archive pages.
		$this->loader->add_action( 'pre_get_posts', $plugin_post_types, 'add_mobilizon_to_tag_archive' );

		// Save/Update our plugin options.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'options_update' );

		// Add menu item!
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// Add Settings link to the plugin.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );

		// Add Ajax Callbacks function to check user input on the fly!
		$this->loader->add_action( 'wp_ajax_is_mobilizon_group', $plugin_admin, 'is_mobilizon_group_callback' );
		$this->loader->add_action( 'wp_ajax_is_mobilizon_instance', $plugin_admin, 'is_mobilizon_instance_callback' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Mobilizon_Mirror_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Add mobilizon events to search results.
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'mobilizon_mirror_include_mobilizon_event_in_search_results' );

		// Load templets according to the settings, if there is no theme override
		$this->loader->add_action( 'template_include', 	 $plugin_public, 'mobilizon_mirror_event_template' );

		// Add hooks so that the event start datetime is showed, not the the date when the event was updated last.
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'change_post_date_in_rest_api' );
		$this->loader->add_action( 'get_the_date', $plugin_public, 'change_post_date', 10, 1 );
		// Always sort the events by the event start date in ascending order.
		$this->loader->add_filter( 'pre_get_posts', $plugin_public, 'change_default_sort' );
		// Filter the post content.
		$this->loader->add_filter( 'the_content', $plugin_public, 'create_event_content' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Mobilizon_Mirror_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
