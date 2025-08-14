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
	 * Settings Page object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_Admin_Page_Settings
	 */
	public $page_settings;

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
			$this->option_set( $this->settings_key, $this->settings );
		}

		// Load settings array.
		$this->settings = $this->option_get( $this->settings_key, $this->settings );

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

		require SOF_ORGANISATIONS_PATH . 'includes/class-admin-page-settings.php';

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0
	 */
	private function setup_objects() {

		$this->page_settings = new SOF_Organisations_Admin_Page_Settings( $this );

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

	/**
	 * Adds a message to admin pages when an upgrade is required.
	 *
	 * @since 1.0
	 */
	public function upgrade_alert() {

		/**
		 * Set access capability but allow overrides.
		 *
		 * @since 1.0
		 *
		 * @param string The default capability for access to Settings.
		 */
		$capability = apply_filters( 'sof_orgs/admin/settings/cap', 'manage_options' );

		// Check user permissions.
		if ( ! current_user_can( $capability ) ) {
			return;
		}

		// Get current screen.
		$screen = get_current_screen();
		if ( ! ( $screen instanceof WP_Screen ) ) {
			return;
		}

		// Get our Settings Page screens.
		$settings_screens = $this->page_settings->page_settings_screens_get();
		if ( in_array( $screen->id, $settings_screens, true ) ) {
			return;
		}

		// Get Settings Page Tab URLs.
		$urls = $this->page_settings->page_tab_urls_get();

		// Construct message.
		$message = sprintf(
			/* translators: %s: The URL of the Settings Page. */
			__( 'SOF Organisations needs your attention. Please visit the <a href="%s">Settings Page</a>.', 'sof-organisations' ),
			$urls['settings']
		);

		// Show it.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="notice notice-error is-dismissible"><p>' . $message . '</p></div>';

	}

	// -----------------------------------------------------------------------------------

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
