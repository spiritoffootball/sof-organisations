<?php
/**
 * CiviCRM Class.
 *
 * Handles CiviCRM functionality.
 *
 * @package SOF_Organisations
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * CiviCRM Class.
 *
 * A class that encapsulates CiviCRM functionality.
 *
 * @since 1.0
 */
class SOF_Organisations_CiviCRM {

	/**
	 * Plugin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations
	 */
	public $plugin;

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
		do_action( 'sof_orgs/civicrm/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Includes files.
	 *
	 * @since 1.0
	 */
	private function include_files() {

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0
	 */
	private function setup_objects() {

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
	 * Check if CiviCRM is initialised.
	 *
	 * @since 1.0
	 *
	 * @return bool True if CiviCRM initialised, false otherwise.
	 */
	public function is_initialised() {

		// Init only when CiviCRM is fully installed.
		if ( ! defined( 'CIVICRM_INSTALLED' ) || ! CIVICRM_INSTALLED ) {
			return false;
		}

		// Bail if no CiviCRM init function.
		if ( ! function_exists( 'civi_wp' ) ) {
			return false;
		}

		// Try and initialise CiviCRM.
		return civi_wp()->initialize();

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Gets all the Custom Groups for CiviCRM Events.
	 *
	 * We are only interested in Custom Fields that have been added to all Events.
	 *
	 * @since 1.0
	 *
	 * @return array $custom_groups The array of Custom Groups.
	 */
	public function custom_groups_get_for_events() {

		// Only do this once.
		static $pseudocache;
		if ( isset( $pseudocache ) ) {
			return $pseudocache;
		}

		// Init array to build.
		$custom_groups = [];

		// Try and init CiviCRM.
		if ( ! $this->is_initialised() ) {
			return $custom_groups;
		}

		// Construct params.
		$params = [
			'version'             => 3,
			'sequential'          => 1,
			'is_active'           => 1,
			'extends'             => 'Event',
			'options'             => [
				'limit' => 0,
			],
			'api.CustomField.get' => [
				'is_active' => 1,
				'options'   => [
					'limit' => 0,
				],
			],
		];

		// Call the API.
		$result = civicrm_api( 'CustomGroup', 'get', $params );

		// Bail if there's an error.
		if ( ! empty( $result['is_error'] ) && 1 === (int) $result['is_error'] ) {
			return $custom_groups;
		}

		// Bail if there are no results.
		if ( empty( $result['values'] ) ) {
			return $custom_groups;
		}

		// The result set is what we want.
		$custom_groups = $result['values'];

		// Set "cache".
		$pseudocache = $custom_groups;

		// --<
		return $custom_groups;

	}

}
