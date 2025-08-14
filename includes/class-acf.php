<?php
/**
 * ACF Class.
 *
 * Handles ACF functionality by loading classes that provide functionality for
 * individual CPTs.
 *
 * @package SOF_Organisations
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ACF Class.
 *
 * A class that encapsulates ACF functionality.
 *
 * @since 1.0
 */
class SOF_Organisations_ACF {

	/**
	 * Plugin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations
	 */
	public $plugin;

	/**
	 * ACF for Organisations object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_ACF_Organisations
	 */
	public $organisation;

	/**
	 * ACF for Events object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_ACF_Events
	 */
	public $events;

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param SOF_Organisations $plugin The plugin object.
	 */
	public function __construct( $plugin ) {

		// Bail if ACF isn't found.
		if ( ! function_exists( 'acf' ) ) {
			return;
		}

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
		do_action( 'sof_orgs/acf/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Includes required files.
	 *
	 * @since 1.0
	 */
	private function include_files() {

		// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
		// require SOF_ORGANISATIONS_PATH . 'includes/class-acf-organisations.php';
		require SOF_ORGANISATIONS_PATH . 'includes/class-acf-events.php';

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0
	 */
	private function setup_objects() {

		// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
		// $this->organisations = new SOF_Organisations_ACF_Organisations( $this );
		$this->events = new SOF_Organisations_ACF_Events( $this );

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0
	 */
	private function register_hooks() {

	}

}
