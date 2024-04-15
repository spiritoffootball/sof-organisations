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
 * A class that encapsulates admin Settings Page functionality.
 *
 * @since 1.0
 */
class SOF_Organisations_Admin_Page_Settings {

	/**
	 * Plugin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations
	 */
	public $plugin;

	/**
	 * Admin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var object $admin The Admin object.
	 */
	public $admin;

	/**
	 * Parent Page.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $parent_page;

	/**
	 * Parent page slug.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $parent_page_slug = 'sof-hosts-parent';

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param object $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store reference to plugin.
		$this->plugin = $parent->plugin;
		$this->admin  = $parent;

		// Init when this plugin is loaded.
		add_action( 'sof_orgs/admin/loaded', [ $this, 'initialise' ] );

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
		do_action( 'sof_orgs/admin/page/settings/loaded' );

	}

	/**
	 * Includes files.
	 *
	 * @since 1.0
	 */
	public function include_files() {

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0
	 */
	public function setup_objects() {

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0
	 */
	public function register_hooks() {

		// Add menu item.
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 30 );

		// Add our meta boxes.
		add_action( 'add_meta_boxes', [ $this, 'meta_boxes_add' ], 11 );

	}

	// -------------------------------------------------------------------------

	/**
	 * Add admin pages for this plugin.
	 *
	 * @since 1.0
	 */
	public function admin_menu() {

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

		// Add the admin page to the "Ball Hosts" menu.
		$this->parent_page = add_submenu_page(
			'edit.php?post_type=' . $this->plugin->cpt->hosts->post_type_name, // Parent slug.
			__( 'Settings: Ball Hosts', 'sof-organisations' ), // Page title.
			__( 'Settings', 'sof-organisations' ), // Menu title.
			$capability, // Required caps.
			$this->parent_page_slug, // Slug name.
			[ $this, 'page_settings' ], // Callback.
			30
		);

		// Register our form submit hander.
		add_action( 'load-' . $this->parent_page, [ $this, 'form_submitted' ] );

		// Add WordPress scripts and help text.
		add_action( 'admin_head-' . $this->parent_page, [ $this, 'admin_head' ], 50 );

	}

	/**
	 * Adds WordPress scripts and help text.
	 *
	 * TODO: Add help text.
	 *
	 * @since 1.0
	 */
	public function admin_head() {

		// Enqueue WordPress scripts.
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'dashboard' );

	}

	// -------------------------------------------------------------------------

	/**
	 * Get Settings Page Tab URLs.
	 *
	 * @since 1.0
	 *
	 * @return array $urls The array of Settings Page Tab URLs.
	 */
	public function page_tab_urls_get() {

		// Only calculate once.
		if ( isset( $this->urls ) ) {
			return $this->urls;
		}

		// Init return.
		$this->urls = [];

		// Get Settings Page URL.
		$this->urls['settings'] = menu_page_url( $this->parent_page_slug, false );

		/**
		 * Filter the list of URLs.
		 *
		 * @since 1.0
		 *
		 * @param array $urls The existing list of URLs.
		 */
		$this->urls = apply_filters( 'sof_orgs/admin/settings/tab_urls', $this->urls );

		// --<
		return $this->urls;

	}

	/**
	 * Show our admin settings page.
	 *
	 * @since 1.0
	 */
	public function page_settings() {

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

		// Get Settings Page Tab URLs.
		$urls = $this->page_tab_urls_get();

		/**
		 * Do not show tabs by default but allow overrides.
		 *
		 * @since 1.0
		 *
		 * @param bool False by default - do not show tabs.
		 */
		$show_tabs = apply_filters( 'sof_orgs/admin/settings/show_tabs', false );

		// Get current screen.
		$screen = get_current_screen();

		/**
		 * Allow meta boxes to be added to this screen.
		 *
		 * The Screen ID to use are:
		 *
		 * * "civicrm_page_civi_eo_parent"
		 * * "civicrm_page_civi_eo_settings"
		 *
		 * @since 1.0
		 *
		 * @param string $screen_id The ID of the current screen.
		 */
		do_action( 'add_meta_boxes', $screen->id, null );

		// Grab columns.
		$columns = ( 1 === (int) $screen->get_columns() ? '1' : '2' );

		// Include template file.
		include SOF_ORGANISATIONS_PATH . 'assets/templates/wordpress/pages/page-admin-settings.php';

	}

	/**
	 * Get our Settings Page screens.
	 *
	 * @since 1.0
	 *
	 * @return array $settings_screens The array of Settings Page screens.
	 */
	public function page_settings_screens_get() {

		// Define this plugin's Settings Page screen IDs.
		$settings_screens = [
			'host_page_' . $this->parent_page_slug,
		];

		/**
		 * Filter the Settings Page screens.
		 *
		 * @since 1.0
		 *
		 * @param array $settings_screens The default array of Settings Page screens.
		 */
		return apply_filters( 'sof_orgs/admin/page/settings/screens', $settings_screens );

	}

	/**
	 * Get the URL for the Settings Page form action attribute.
	 *
	 * This happens to be the same as the Settings Page URL, but need not be.
	 *
	 * @since 1.0
	 *
	 * @return string $submit_url The URL for the Settings Page form action.
	 */
	public function page_settings_submit_url_get() {

		// Get Settings Page submit URL.
		$submit_url = menu_page_url( $this->parent_page_slug, false );

		/**
		 * Filter the Settings Page submit URL.
		 *
		 * @since 1.0
		 *
		 * @param array $submit_url The Settings Page submit URL.
		 */
		$submit_url = apply_filters( 'sof_orgs/admin/page/settings/submit_url', $submit_url );

		// --<
		return $submit_url;

	}

	// -------------------------------------------------------------------------

	/**
	 * Register meta boxes.
	 *
	 * @since 1.0
	 *
	 * @param string $screen_id The Admin Page Screen ID.
	 */
	public function meta_boxes_add( $screen_id ) {

		// Get our Settings Page screens.
		$settings_screens = $this->page_settings_screens_get();
		if ( ! in_array( $screen_id, $settings_screens, true ) ) {
			return;
		}

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

		// Create Submit metabox.
		add_meta_box(
			'submitdiv',
			__( 'Settings', 'sof-organisations' ),
			[ $this, 'meta_box_submit_render' ], // Callback.
			$screen_id, // Screen ID.
			'side', // Column: options are 'normal' and 'side'.
			'core' // Vertical placement: options are 'core', 'high', 'low'.
		);

		// Create "General Event Settings" metabox.
		add_meta_box(
			'general',
			__( 'General Settings', 'sof-organisations' ),
			[ $this, 'meta_box_general_render' ], // Callback.
			$screen_id, // Screen ID.
			'normal', // Column: options are 'normal' and 'side'.
			'core' // Vertical placement: options are 'core', 'high', 'low'.
		);

	}

	/**
	 * Render Save Settings meta box on Admin screen.
	 *
	 * @since 1.0
	 */
	public function meta_box_submit_render() {

		// Include template file.
		include SOF_ORGANISATIONS_PATH . 'assets/templates/wordpress/metaboxes/metabox-admin-settings-submit.php';

	}

	/**
	 * Render General Settings meta box on Admin screen.
	 *
	 * @since 1.0
	 */
	public function meta_box_general_render() {

		// Get all Custom Groups attached to CiviCRM Events.
		$groups = $this->plugin->civicrm->custom_groups_get_for_events();

		// Try and build a set of options.
		$custom_fields = [];
		if ( ! empty( $groups ) ) {
			foreach ( $groups as $group ) {
				if ( ! empty( $group['api.CustomField.get']['values'] ) ) {
					foreach ( $group['api.CustomField.get']['values'] as $field ) {
						$custom_fields[ $group['title'] ][ $field['id'] ] = $field['label'];
					}
				}
			}
		}

		// Get the "Event Ball Host" Custom Field ID.
		$custom_field_id = $this->admin->setting_get( 'event_ball_host_custom_field_id' );
		if ( empty( $custom_field_id ) ) {
			$custom_field_id = 0;
		}

		// Include template file.
		include SOF_ORGANISATIONS_PATH . 'assets/templates/wordpress/metaboxes/metabox-admin-settings-general.php';

	}

	// -------------------------------------------------------------------------

	/**
	 * Performs actions when a form has been submitted.
	 *
	 * @since 1.0
	 */
	public function form_submitted() {

		// Was the "Settings" form submitted?
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['sof_orgs_save'] ) ) {
			$this->form_nonce_check();
			$this->form_settings_update();
			$this->form_redirect();
		}

	}

	/**
	 * Update plugin settings.
	 *
	 * @since 1.0
	 */
	public function form_settings_update() {

		// Sanitise and save setting.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
		$event_organiser_custom_field_id = isset( $_POST['sof_orgs_event_org'] ) ? (int) wp_unslash( $_POST['sof_orgs_event_org'] ) : 0;
		$this->admin->setting_set( 'event_ball_host_custom_field_id', $event_organiser_custom_field_id );

		// Save settings.
		$this->admin->settings_save();

		/**
		 * Broadcast end of settings update.
		 *
		 * @since 1.0
		 */
		do_action( 'sof_orgs/settings/updated' );

	}

	/**
	 * Checks the nonce.
	 *
	 * @since 1.0
	 */
	private function form_nonce_check() {

		// Check that we trust the source of the data.
		check_admin_referer( 'sof_orgs_settings_action', 'sof_orgs_settings_nonce' );

	}

	/**
	 * Redirect to the Settings Page with an extra param.
	 *
	 * @since 1.0
	 */
	private function form_redirect() {

		// Our array of arguments.
		$args = [
			'settings-updated' => 'true',
		];

		// Get Settings Page Tab URLs.
		$urls = $this->page_tab_urls_get();

		// Get the Settings Page URL.
		$url = $urls['settings'];

		// The Settings Page URL needs to be unescaped.
		$url = str_replace( '&#038;', '&', $url );

		// Redirect to our Settings Page.
		wp_safe_redirect( add_query_arg( $args, $url ) );
		exit();

	}

}
