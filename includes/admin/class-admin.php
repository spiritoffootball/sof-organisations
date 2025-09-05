<?php
/**
 * Admin Class.
 *
 * Handles admin functionality.
 *
 * @package SOF_Organisations
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SOF Organisations Admin Class
 *
 * A class that encapsulates admin functionality.
 *
 * @since 1.0
 */
class SOF_Organisations_Admin {

	/**
	 * Plugin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations
	 */
	public $plugin;

	/**
	 * General Settings Page object.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var SOF_Organisations_Admin_Settings
	 */
	public $settings_page;

	/**
	 * Ball Host Settings Page object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_Admin_Settings_Ball_Host
	 */
	public $settings_page_ball_host;

	/**
	 * Plugin version option key.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $plugin_version_key = 'sof_orgs_version';

	/**
	 * Plugin version.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $plugin_version;

	/**
	 * Settings option key.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $settings_key = 'sof_orgs_settings';

	/**
	 * Plugin settings.
	 *
	 * @since 1.0
	 * @access public
	 * @var array
	 */
	public $settings = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param SOF_Organisations $plugin The plugin object.
	 */
	public function __construct( $plugin ) {

		// Store reference to plugin.
		$this->plugin = $plugin;

		// Init when this plugin is loaded.
		add_action( 'sof_orgs/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this object.
	 *
	 * @since 1.0
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Init settings.
		$this->settings_initialise();

		// Bootstrap class.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Broadcast that this class is active.
		 *
		 * @since 1.0
		 */
		do_action( 'sof_orgs/admin/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Initialise settings.
	 *
	 * @since 1.0
	 */
	private function settings_initialise() {

		// Assign plugin version.
		$this->plugin_version = $this->version_get();

		// Do upgrade tasks.
		$this->upgrade_tasks();

		// Store default settings if none exist.
		if ( ! $this->option_exists( $this->settings_key ) ) {
			$this->option_set( $this->settings_key, $this->settings_get_defaults() );
		}

		// Load settings array.
		$this->settings = $this->option_get( $this->settings_key, $this->settings_get_defaults() );

		// Store version if there has been a change.
		if ( SOF_ORGANISATIONS_VERSION !== $this->plugin_version ) {
			$this->version_set();
		}

	}

	/**
	 * Includes required files.
	 *
	 * @since 1.0
	 */
	private function include_files() {

		// Get enabled settings.
		$defaults     = $this->settings_get_defaults();
		$host_enabled = $this->setting_get( 'host_enabled', $defaults['host_enabled'] );

		// Always include Base class.
		require SOF_ORGANISATIONS_PATH . 'includes/admin/class-admin-page-base.php';

		// General Settings Page class.
		require SOF_ORGANISATIONS_PATH . 'includes/admin/class-admin-page-settings.php';

		// Ball Host Settings Page class.
		if ( 'y' === $host_enabled ) {
			require SOF_ORGANISATIONS_PATH . 'includes/admin/class-admin-settings-ball-host.php';
		}

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0
	 */
	private function setup_objects() {

		// Get enabled settings.
		$defaults     = $this->settings_get_defaults();
		$host_enabled = $this->setting_get( 'host_enabled', $defaults['host_enabled'] );

		// General Settings Page.
		$this->settings_page = new SOF_Organisations_Admin_Settings( $this );

		// Ball Host Settings Page.
		if ( 'y' === $host_enabled ) {
			$this->settings_page_ball_host = new SOF_Organisations_Admin_Settings_Ball_Host( $this );
		}

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0
	 */
	private function register_hooks() {

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Gets the stored plugin version.
	 *
	 * @since 1.0
	 *
	 * @return string|bool $version The stored plugin version, or false if not set.
	 */
	public function version_get() {

		// Bail if not set.
		if ( ! $this->option_exists( $this->plugin_version_key ) ) {
			return false;
		}

		// Get version.
		$version = $this->option_get( $this->plugin_version_key, false );

		// --<
		return $version;

	}

	/**
	 * Stores the plugin version.
	 *
	 * @since 1.0
	 */
	public function version_set() {

		// Store version.
		$this->option_set( $this->plugin_version_key, SOF_ORGANISATIONS_VERSION );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Performs tasks when an upgrade is required.
	 *
	 * @since 1.0
	 */
	public function upgrade_tasks() {

		// Bail if this is a new install.
		if ( false === $this->plugin_version ) {
			return;
		}

		// Bail if this not WordPress admin.
		if ( ! is_admin() ) {
			return;
		}

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Gets the default settings for this plugin.
	 *
	 * @since 1.0.1
	 *
	 * @return array $settings The default settings for this plugin.
	 */
	public function settings_get_defaults() {

		// Init return.
		$settings = [];

		// Default Organisation CPT enabled.
		$settings['organisation_enabled'] = 'y';

		// Default Partners CPT enabled.
		$settings['partner_enabled'] = 'y';

		// Default Ball Hosts CPT enabled.
		$settings['host_enabled'] = 'y';

		// Default Ball Host CiviCRM Custom Field ID.
		$settings['event_ball_host_custom_field_id'] = '';

		// Default Pledgeball Event settings enabled.
		$settings['pledgeball_enabled'] = 'y';

		/**
		 * Filter default settings.
		 *
		 * @since 1.0.1
		 *
		 * @param array $settings The existing array of default settings.
		 */
		$settings = apply_filters( 'sof_orgs/admin/settings_default', $settings );

		// --<
		return $settings;

	}

	/**
	 * Gets all settings.
	 *
	 * @since 1.0
	 *
	 * @return array $settings The array of settings.
	 */
	public function settings_get() {

		// --<
		return $this->settings;

	}

	/**
	 * Saves the settings array.
	 *
	 * @since 1.0
	 */
	public function settings_save() {

		// Update option.
		$this->option_set( $this->settings_key, $this->settings );

	}

	/**
	 * Deletes the settings array.
	 *
	 * @since 1.0
	 */
	public function settings_delete() {

		// Delete the settings option.
		$this->option_delete( $this->settings_key );

	}

	/**
	 * Gets the value for a given setting.
	 *
	 * @since 1.0
	 *
	 * @param string $name The name of the setting.
	 * @return mixed $setting The value of the setting, or false if none exists.
	 */
	public function setting_get( $name ) {

		// Init as false.
		$setting = false;

		// Overwrite if a setting exists.
		if ( isset( $this->settings[ $name ] ) ) {
			$setting = $this->settings[ $name ];
		}

		// --<
		return $setting;

	}

	/**
	 * Adds or updates a setting.
	 *
	 * @since 1.0
	 *
	 * @param string $name The name of the setting.
	 * @param mixed  $data The value of the setting.
	 */
	public function setting_set( $name, $data ) {

		// Overwrite (or add) setting.
		$this->settings[ $name ] = $data;

	}

	/**
	 * Deletes a setting.
	 *
	 * @since 1.0
	 *
	 * @param string $name The name of the setting.
	 */
	public function setting_delete( $name ) {

		// If a setting exists, delete it.
		if ( isset( $this->settings[ $name ] ) ) {
			unset( $this->settings[ $name ] );
		}

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Tests for the existence of a given option.
	 *
	 * @since 1.0
	 *
	 * @param string $key The option name.
	 * @return bool $exists Whether or not the option exists.
	 */
	public function option_exists( $key ) {

		// Test by getting option with unlikely default.
		if ( $this->option_get( $key, 'fenfgehgefdfdjgrkj' ) === 'fenfgehgefdfdjgrkj' ) {
			return false;
		} else {
			return true;
		}

	}

	/**
	 * Gets an option.
	 *
	 * @since 1.0
	 *
	 * @param string $key The option name.
	 * @param mixed  $default The default option value if none exists.
	 * @return mixed $value The value of the requested option.
	 */
	public function option_get( $key, $default = null ) {

		// Get local site option.
		$value = get_option( $key, $default );

		// --<
		return $value;

	}

	/**
	 * Adds or updates an option.
	 *
	 * @since 1.0
	 *
	 * @param string $key The option name.
	 * @param mixed  $value The value to save.
	 */
	public function option_set( $key, $value ) {

		// Update local site option.
		update_option( $key, $value );

	}

	/**
	 * Deletes an option.
	 *
	 * @since 1.0
	 *
	 * @param string $key The option name.
	 */
	public function option_delete( $key ) {

		// Delete local site option.
		delete_option( $key );

	}

}
