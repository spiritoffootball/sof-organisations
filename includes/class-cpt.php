<?php
/**
 * CPT Class.
 *
 * Handles CPT functionality by loading classes that provide functionality for
 * individual CPTs.
 *
 * @package SOF_Organisations
 * @since 1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * CPT Class.
 *
 * A class that encapsulates CPT functionality.
 *
 * @package SOF_Organisations
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
	 * @var object $organisations The Organisations CPT object.
	 */
	public $organisations;

	/**
	 * Partners CPT object.
	 *
	 * @since 1.0
	 * @access public
	 * @var object $partners The Partners CPT object.
	 */
	public $partners;

	/**
	 * Ball Hosts CPT object.
	 *
	 * @since 1.0
	 * @access public
	 * @var object $hosts The Ball Hosts CPT object.
	 */
	public $hosts;

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param object $plugin The plugin object.
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

		// Bootstrap class.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Broadcast that this class is active.
		 *
		 * @since 1.0
		 */
		do_action( 'sof_orgs/cpt/loaded' );

	}

	/**
	 * Includes files.
	 *
	 * @since 1.0
	 */
	public function include_files() {

		include SOF_ORGANISATIONS_PATH . 'includes/class-cpt-organisations.php';
		include SOF_ORGANISATIONS_PATH . 'includes/class-cpt-partners.php';
		include SOF_ORGANISATIONS_PATH . 'includes/class-cpt-hosts.php';

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0
	 */
	public function setup_objects() {

		$this->organisations = new SOF_Organisations_CPT_Organisations( $this );
		$this->partners      = new SOF_Organisations_CPT_Partners( $this );
		$this->hosts         = new SOF_Organisations_CPT_Hosts( $this );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0
	 */
	public function register_hooks() {

	}

}
