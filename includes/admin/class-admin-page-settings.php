<?php
/**
 * Admin Settings Page Class.
 *
 * Handles Settings Page functionality.
 *
 * @package SOF_Organisations
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Admin Settings Page Class.
 *
 * This class provides Settings Page functionality.
 *
 * @since 1.0.1
 */
class SOF_Organisations_Admin_Settings extends SOF_Organisations_Admin_Settings_Base {

	/**
	 * Constructor.
	 *
	 * @since 1.0.1
	 *
	 * @param object $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->plugin = $parent->plugin;
		$this->admin  = $parent;

		// Set a unique prefix for all Pages.
		$this->hook_prefix_common = 'sof_organisations_admin';

		// Set a unique prefix.
		$this->hook_prefix = 'sof_organisations_settings';

		/*
		// No need to assign page location to "Single Site" - it's the default.
		$this->page_location = 'site';
		*/

		// Assign page context.
		$this->page_context = 'settings_page_';

		// Assign page slug.
		$this->page_slug = 'sof_organisations_settings';

		// Assign path to plugin directory.
		$this->path_plugin = SOF_ORGANISATIONS_PATH;

		// Bootstrap parent.
		parent::__construct();

	}

	/**
	 * Do stuff on init.
	 *
	 * @since 1.0.1
	 */
	public function initialise() {

		// Define plugin name.
		$this->plugin_name          = __( 'SOF Organisations', 'sof-organisations' );
		$this->metabox_submit_title = __( 'Settings', 'sof-organisations' );

	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.1
	 */
	public function register_hooks() {

		// Add menu item to Settings submenu.
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 50, 2 );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Add Settings menu item for this plugin.
	 *
	 * @since 1.0.1
	 *
	 * @param string $page_handle The handle of the Parent Page.
	 * @param string $page_slug The slug of the Parent Page.
	 */
	public function admin_menu( $page_handle = '', $page_slug = '' ) {

		// We must be network admin in multisite.
		if ( is_multisite() && ! is_super_admin() ) {
			return;
		}

		// Check user permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Add the admin page to the CiviCRM menu.
		$this->page_handle = add_options_page(
			__( 'SOF Organisations: Settings', 'sof-organisations' ), // Page title.
			__( 'SOF Organisations', 'sof-organisations' ), // Menu title.
			'manage_options', // Required cap.
			$this->page_slug, // Slug name.
			[ $this, 'page_render' ] // Callback.
		);

		// Add page callbacks.
		$this->admin_actions();

	}

	/**
	 * Adds help copy to admin page.
	 *
	 * @since 1.0.1
	 */
	public function admin_help() {

		// Get screen object.
		$screen = get_current_screen();

		// Bail if not our screen.
		if ( $screen->id !== $this->page_handle ) {
			return;
		}

		// Build tab args.
		$args = [
			'id'      => $this->hook_prefix,
			'title'   => __( 'SOF Organisations', 'sof-organisations' ),
			'content' => $this->admin_help_get(),
		];

		// Add a tab - we can add more later.
		$screen->add_help_tab( $args );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Gets the URL for the Settings Page form action attribute.
	 *
	 * This happens to be the same as the Settings Page URL, but need not be.
	 *
	 * @since 1.0.1
	 *
	 * @return string $submit_url The URL for the Settings Page form action.
	 */
	public function form_submit_url_get() {

		// Get Settings Page submit URL.
		$submit_url = menu_page_url( $this->page_slug, false );

		/**
		 * Filter the Settings Page submit URL.
		 *
		 * @since 1.0.1
		 *
		 * @param string $submit_url The Settings Page submit URL.
		 */
		$submit_url = apply_filters( $this->hook_prefix . '/settings/form/submit_url', $submit_url );

		// --<
		return $submit_url;

	}

}
