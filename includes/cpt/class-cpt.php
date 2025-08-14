<?php
/**
 * CPT Class.
 *
 * Handles CPT functionality by loading classes that provide functionality for
 * individual CPTs.
 *
 * @package SOF_Organisations
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * CPT Class.
 *
 * A class that encapsulates CPT functionality.
 *
 * @since 1.0
 */
class SOF_Organisations_CPT {

	/**
	 * Plugin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations
	 */
	public $plugin;

	/**
	 * Organisations CPT object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_CPT_Organisations
	 */
	public $organisations;

	/**
	 * Partners CPT object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_CPT_Partners
	 */
	public $partners;

	/**
	 * Ball Hosts CPT object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_CPT_Hosts
	 */
	public $hosts;

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

		// Bootstrap class.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Fires when this class is loaded.
		 *
		 * @since 1.0
		 */
		do_action( 'sof_orgs/cpt/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Includes required files.
	 *
	 * @since 1.0
	 */
	private function include_files() {

		// Get defaults.
		$defaults = $this->plugin->admin->settings_get_defaults();

		// Retrieve settings for each option.
		$organisation_enabled = $this->plugin->admin->setting_get( 'organisation_enabled', $defaults['organisation_enabled'] );
		$partner_enabled      = $this->plugin->admin->setting_get( 'partner_enabled', $defaults['partner_enabled'] );
		$host_enabled         = $this->plugin->admin->setting_get( 'host_enabled', $defaults['host_enabled'] );

		// Optionally include class files.
		if ( 'y' === $organisation_enabled ) {
			require SOF_ORGANISATIONS_PATH . 'includes/cpt/class-cpt-organisations.php';
		}
		if ( 'y' === $partner_enabled ) {
			require SOF_ORGANISATIONS_PATH . 'includes/cpt/class-cpt-partners.php';
		}
		if ( 'y' === $partner_enabled ) {
			require SOF_ORGANISATIONS_PATH . 'includes/cpt/class-cpt-hosts.php';
		}

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0
	 */
	private function setup_objects() {

		// Get defaults.
		$defaults = $this->plugin->admin->settings_get_defaults();

		// Retrieve settings for each option.
		$organisation_enabled = $this->plugin->admin->setting_get( 'organisation_enabled', $defaults['organisation_enabled'] );
		$partner_enabled      = $this->plugin->admin->setting_get( 'partner_enabled', $defaults['partner_enabled'] );
		$host_enabled         = $this->plugin->admin->setting_get( 'host_enabled', $defaults['host_enabled'] );

		// Optionally instantiate objects.
		if ( 'y' === $organisation_enabled ) {
			$this->organisations = new SOF_Organisations_CPT_Organisations( $this );
		}
		if ( 'y' === $partner_enabled ) {
			$this->partners = new SOF_Organisations_CPT_Partners( $this );
		}
		if ( 'y' === $host_enabled ) {
			$this->hosts = new SOF_Organisations_CPT_Hosts( $this );
		}

	}

	/**
	 * Register hook callbacks.
	 *
	 * @since 1.0
	 */
	private function register_hooks() {

	}

}
