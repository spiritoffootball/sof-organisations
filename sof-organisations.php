<?php
/**
 * SOF Organisations
 *
 * Plugin Name:       SOF Organisations
 * Description:       Provides Organisation Custom Post Types for The Ball website.
 * Version:           1.0.2a
 * Plugin URI:        https://github.com/spiritoffootball/sof-organisations
 * GitHub Plugin URI: https://github.com/spiritoffootball/sof-organisations
 * Author:            Christian Wach
 * Author URI:        https://haystack.co.uk
 * Text Domain:       sof-organisations
 * Domain Path:       /languages
 *
 * @package SOF_Organisations
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set our version here.
define( 'SOF_ORGANISATIONS_VERSION', '1.0.2a' );

// Store reference to this file.
if ( ! defined( 'SOF_ORGANISATIONS_FILE' ) ) {
	define( 'SOF_ORGANISATIONS_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'SOF_ORGANISATIONS_URL' ) ) {
	define( 'SOF_ORGANISATIONS_URL', plugin_dir_url( SOF_ORGANISATIONS_FILE ) );
}

// Store PATH to this plugin's directory.
if ( ! defined( 'SOF_ORGANISATIONS_PATH' ) ) {
	define( 'SOF_ORGANISATIONS_PATH', plugin_dir_path( SOF_ORGANISATIONS_FILE ) );
}

/**
 * Main Plugin Class.
 *
 * A class that encapsulates plugin functionality.
 *
 * @since 1.0
 */
class SOF_Organisations {

	/**
	 * Admin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_Admin
	 */
	public $admin;

	/**
	 * CiviCRM object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_CiviCRM
	 */
	public $civicrm;

	/**
	 * Custom Post Type loader object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_CPT
	 */
	public $cpt;

	/**
	 * ACF loader object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_ACF
	 */
	public $acf;

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// Initialise when all plugins are loaded.
		add_action( 'plugins_loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this plugin.
	 *
	 * @since 1.0
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap plugin.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Broadcast that this plugin is now loaded.
		 *
		 * @since 1.0
		 */
		do_action( 'sof_orgs/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Include files.
	 *
	 * @since 1.0
	 */
	private function include_files() {

		// Include class files.
		require SOF_ORGANISATIONS_PATH . 'includes/admin/class-admin.php';
		require SOF_ORGANISATIONS_PATH . 'includes/class-civicrm.php';
		require SOF_ORGANISATIONS_PATH . 'includes/cpt/class-cpt.php';
		require SOF_ORGANISATIONS_PATH . 'includes/acf/class-acf.php';

	}

	/**
	 * Set up this plugin's objects.
	 *
	 * @since 1.0
	 */
	private function setup_objects() {

		// Init objects.
		$this->admin   = new SOF_Organisations_Admin( $this );
		$this->civicrm = new SOF_Organisations_CiviCRM( $this );
		$this->cpt     = new SOF_Organisations_CPT( $this );
		$this->acf     = new SOF_Organisations_ACF( $this );

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0.1
	 */
	private function register_hooks() {

		// Use translation.
		add_action( 'init', [ $this, 'translation' ] );

	}

	/**
	 * Enable translation.
	 *
	 * @since 1.0
	 */
	public function translation() {

		// Load translations.
		// phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
		load_plugin_textdomain(
			'sof-organisations', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( SOF_ORGANISATIONS_FILE ) ) . '/languages/' // Relative path to files.
		);

	}

	/**
	 * Perform plugin activation tasks.
	 *
	 * @since 1.0
	 */
	public function activate() {

		// Maybe init.
		$this->initialise();

		/**
		 * Broadcast plugin activation.
		 *
		 * @since 1.0
		 */
		do_action( 'sof_orgs/activate' );

	}

	/**
	 * Perform plugin deactivation tasks.
	 *
	 * @since 1.0
	 */
	public function deactivate() {

		// Maybe init.
		$this->initialise();

		/**
		 * Broadcast plugin deactivation.
		 *
		 * @since 1.0
		 */
		do_action( 'sof_orgs/deactivate' );

	}

}

/**
 * Utility to get a reference to this plugin.
 *
 * @since 1.0
 *
 * @return SOF_Organisations $sof_organisations The plugin reference.
 */
function sof_organisations() {

	// Store instance in static variable.
	static $sof_organisations = false;

	// Maybe return instance.
	if ( false === $sof_organisations ) {
		$sof_organisations = new SOF_Organisations();
	}

	// --<
	return $sof_organisations;

}

// Initialise plugin now.
sof_organisations();

// Activation.
register_activation_hook( __FILE__, [ sof_organisations(), 'activate' ] );

// Deactivation.
register_deactivation_hook( __FILE__, [ sof_organisations(), 'deactivate' ] );

/*
 * Uninstall uses the 'uninstall.php' method.
 *
 * @see https://codex.wordpress.org/Function_Reference/register_uninstall_hook
 */
