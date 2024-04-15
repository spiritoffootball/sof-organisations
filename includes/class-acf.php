<?php
/**
 * ACF Class.
 *
 * Handles ACF functionality by loading classes that provide functionality for
 * individual CPTs.
 *
 * @package SOF_Organisations
 * @since 1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ACF Class.
 *
 * A class that encapsulates ACF functionality.
 *
 * @package SOF_Organisations
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
	 * @param object $plugin The plugin object.
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

		// Bootstrap class.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Broadcast that this class is active.
		 *
		 * @since 1.0
		 */
		do_action( 'sof_orgs/acf/loaded' );

	}

	/**
	 * Includes files.
	 *
	 * @since 1.0
	 */
	public function include_files() {

		// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
		// include SOF_ORGANISATIONS_PATH . 'includes/class-acf-organisations.php';
		include SOF_ORGANISATIONS_PATH . 'includes/class-acf-events.php';

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0
	 */
	public function setup_objects() {

		// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
		// $this->organisations = new SOF_Organisations_ACF_Organisations( $this );
		$this->events = new SOF_Organisations_ACF_Events( $this );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0
	 */
	public function register_hooks() {

	}

}
