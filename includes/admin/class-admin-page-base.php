<?php
/**
 * Settings Page abstract class.
 *
 * Handles common Settings Page functionality.
 *
 * @package SOF_Organisations
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Settings Page abstract class.
 *
 * A class that encapsulates common Settings Page functionality. The Page can be
 * styled as a "Settings" or "Dashboard" page depending on requirements.
 *
 * It can be either a "Parent" Page which sits either:
 *
 * * Directly below the "CiviCRM" menu item
 * * As an item in the "Settings" menu
 *
 * Or it can be a "Sub-page" which is accessible via a tab on the Parent Page.
 *
 * @since 1.0.1
 */
abstract class SOF_Organisations_Admin_Settings_Base {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var SOF_Organisations
	 */
	public $plugin;

	/**
	 * Admin object.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var SOF_Organisations_Admin
	 */
	public $admin;

	/**
	 * Plugin name.
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected $plugin_name = '';

	/**
	 * Hook prefix common to all Settings Pages.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var string
	 */
	public $hook_prefix_common = '';

	/**
	 * Hook prefix.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var string
	 */
	public $hook_prefix = 'sof_organisations';

	/**
	 * Settings Page location.
	 *
	 * Either "site" or "network". Default "site".
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected $page_location = 'site';

	/**
	 * Settings Page context.
	 *
	 * Something like "civicrm_page_", "settings_page_" or "admin_page_". Default "settings_page_".
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected $page_context = 'settings_page_';

	/**
	 * Settings Page handle.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var string
	 */
	public $page_handle;

	/**
	 * Settings Page slug.
	 *
	 * @since 1.0.1
	 * @var string
	 */
	public $page_slug = '';

	/**
	 * Settings Page layout.
	 *
	 * Either "settings" or "dashboard". Default "settings".
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected $page_layout = 'settings';

	/**
	 * Settings Page "Submit" Metabox label.
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected $metabox_submit_title = '';

	/**
	 * Absolute path to the plugin directory.
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected $path_plugin = '';

	/**
	 * Relative path to the template directory.
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected $path_template = 'assets/templates/wordpress/settings/';

	/**
	 * Relative path to the Page template directory.
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected $path_page = 'pages/';

	/**
	 * Relative path to the Metabox template directory.
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected $path_metabox = 'metaboxes/';

	/**
	 * Relative path to the Help template directory.
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected $path_help = 'help/';

	/**
	 * The name of the form nonce element.
	 *
	 * @since 1.0.1
	 * @access protected
	 * @var string
	 */
	protected $form_nonce_field = 'settings_nonce';

	/**
	 * The name of the form nonce value.
	 *
	 * @since 1.0.1
	 * @access protected
	 * @var string
	 */
	protected $form_nonce_action = 'settings_action';

	/**
	 * The "name" and "id" of the form.
	 *
	 * @since 1.0.1
	 * @access protected
	 * @var string
	 */
	protected $form_id = 'settings_form';

	/**
	 * The "name" and "id" of the form's submit input element.
	 *
	 * @since 1.0.1
	 * @access protected
	 * @var string
	 */
	protected $form_submit_id = 'settings_submit';

	/**
	 * The "name" and "id" of the "Organisation Enabled" select element.
	 *
	 * @since 1.0.1
	 * @access protected
	 * @var string
	 */
	protected $key_organisation_enabled = 'sof_organisation_enabled';

	/**
	 * The "name" and "id" of the "Partner Enabled" select element.
	 *
	 * @since 1.0.1
	 * @access protected
	 * @var string
	 */
	protected $key_partner_enabled = 'sof_partner_enabled';

	/**
	 * The "name" and "id" of the "Ball Host Enabled" select element.
	 *
	 * @since 1.0.1
	 * @access protected
	 * @var string
	 */
	protected $key_host_enabled = 'sof_host_enabled';

	/**
	 * The "name" and "id" of the "Pledgeball Enabled" select element.
	 *
	 * @since 1.0.1
	 * @access protected
	 * @var string
	 */
	protected $key_pledgeball_enabled = 'sof_pledgeball_enabled';

	/**
	 * Class constructor.
	 *
	 * @since 1.0.1
	 */
	public function __construct() {

		// Build form attributes.
		$this->form_nonce_field  = $this->hook_prefix . '_' . $this->form_nonce_field;
		$this->form_nonce_action = $this->hook_prefix . '_' . $this->form_nonce_action;
		$this->form_id           = $this->hook_prefix . '_' . $this->form_id;
		$this->form_submit_id    = $this->hook_prefix . '_' . $this->form_submit_id;

		// Add init actions.
		add_action( 'init', [ $this, 'initialise' ] );
		add_action( 'init', [ $this, 'register_hooks_common' ] );
		add_action( 'init', [ $this, 'register_hooks' ] );

	}

	/**
	 * Initialise this object.
	 *
	 * @since 1.0.1
	 */
	public function initialise() {}

	/**
	 * Registers common hooks.
	 *
	 * @since 1.0.1
	 */
	public function register_hooks_common() {

		// Add meta boxes to this Sub-page.
		add_action( $this->hook_prefix . '/settings/page/add_meta_boxes', [ $this, 'meta_boxes_add' ], 11 );
		add_action( $this->hook_prefix . '/settings/page/meta_boxes_added', [ $this, 'meta_boxes_register' ], 10, 2 );

	}

	/**
	 * Register hook callbacks.
	 *
	 * @since 1.0.1
	 */
	public function register_hooks() {}

	// -----------------------------------------------------------------------------------

	/**
	 * Adds the Menu Item.
	 *
	 * @since 1.0.1
	 *
	 * @param string $page_handle The handle of the Parent Page.
	 * @param string $page_slug The slug of the Parent Page.
	 */
	abstract protected function admin_menu( $page_handle = '', $page_slug = '' );

	/**
	 * Adds callbacks for admin page actions.
	 *
	 * @since 1.0.1
	 */
	public function admin_actions() {

		// Register our form submit hander.
		add_action( 'load-' . $this->page_handle, [ $this, 'form_submitted' ] );

		/*
		 * Add styles and scripts only on our Settings Page.
		 * @see wp-admin/admin-header.php
		 */
		add_action( 'admin_head-' . $this->page_handle, [ $this, 'admin_head' ] );
		add_action( 'admin_print_styles-' . $this->page_handle, [ $this, 'admin_styles' ] );
		add_action( 'admin_print_scripts-' . $this->page_handle, [ $this, 'admin_scripts' ] );

		// Add help text.
		add_action( 'load-' . $this->page_handle, [ $this, 'admin_help' ], 50 );

	}

	/**
	 * Adds metabox scripts.
	 *
	 * @since 1.0.1
	 */
	public function admin_head() {

		// Enqueue WordPress scripts.
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'dashboard' );

	}

	/**
	 * Enqueue stylesheet for this plugin's "Settings" page.
	 *
	 * @since 0.1
	 * @since 1.0.1 Renamed.
	 */
	public function admin_styles() {

		/*
		// Add stylesheet.
		wp_enqueue_style(
			$this->hook_prefix . '-css',
			plugins_url( 'assets/css/sof-organisations.css', SOF_ORGANISATIONS_FILE ),
			false,
			SOF_ORGANISATIONS_VERSION, // Version.
			'all' // Media.
		);
		*/

	}

	/**
	 * Enqueue Javascript for this plugin's "Settings" page.
	 *
	 * @since 0.1
	 * @since 1.0.1 Renamed.
	 */
	public function admin_scripts() {

		/*
		// Enqueue javascript.
		wp_enqueue_script(
			$this->hook_prefix . '-js',
			plugins_url( 'assets/js/sof-organisations.js', SOF_ORGANISATIONS_FILE ),
			[ 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ],
			SOF_ORGANISATIONS_VERSION, // Version.
			true
		);
		*/

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

	/**
	 * Gets the default help text.
	 *
	 * @since 1.0.1
	 *
	 * @return string $help The help text formatted as HTML.
	 */
	protected function admin_help_get() {

		// Build path to default help template.
		$template = $this->path_plugin . $this->path_template . $this->path_help . 'page-settings-help.php';

		// Use contents of help template.
		ob_start();
		require_once $template;
		$help = ob_get_clean();

		// --<
		return $help;

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Checks the access capability for this Page.
	 *
	 * @since 1.0.1
	 *
	 * @return string|bool The capability if the current User has it, false otherwise.
	 */
	public function page_capability() {

		/**
		 * Set access capability but allow overrides.
		 *
		 * @since 1.0.1
		 *
		 * @param string The default capability for access to Settings Page.
		 */
		$capability = apply_filters( $this->hook_prefix . '/settings/page/cap', 'manage_options' );

		// Check User permissions.
		if ( ! current_user_can( $capability ) ) {
			return false;
		}

		// --<
		return $capability;

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Renders the Settings Page.
	 *
	 * @since 1.0.1
	 */
	public function page_render() {

		// Check User permissions.
		if ( ! $this->page_capability() ) {
			return;
		}

		/**
		 * Do not show tabs by default but allow overrides.
		 *
		 * @since 1.0.1
		 *
		 * @param bool False by default - do not show tabs.
		 */
		$show_tabs = apply_filters( $this->hook_prefix . '/settings/page/show_tabs', false );

		// Get current screen.
		$screen = get_current_screen();

		/**
		 * Allows meta boxes to be added to this screen.
		 *
		 * The Screen ID to use is:
		 *
		 * * $this->page_context . $this->page_slug
		 *
		 * @since 1.0.1
		 *
		 * @param string $screen_id The ID of the current screen.
		 */
		do_action( $this->hook_prefix . '/settings/page/add_meta_boxes', $screen->id );

		// Configure layout.
		switch ( $this->page_layout ) {

			case 'dashboard':
				$template_name = 'page-dashboard.php';

				// Assign the column CSS class.
				$columns     = (int) $screen->get_columns();
				$columns_css = '';
				if ( $columns ) {
					$columns_css = " columns-$columns";
				}
				break;

			case 'settings':
				$template_name = 'page-settings.php';

				// Assign columns.
				$columns = ( 1 === (int) $screen->get_columns() ? '1' : '2' );
				break;

		}

		// Build path to Page template.
		$template = $this->path_plugin . $this->path_template . $this->path_page . $template_name;

		// Include template.
		require_once $template;

	}

	/**
	 * Gets the URL of the Settings Page.
	 *
	 * @since 1.0.1
	 *
	 * @return string $url The URL of the Settings Page.
	 */
	public function page_url_get() {

		// Get Settings Page URL.
		$url = menu_page_url( $this->page_slug, false );

		/**
		 * Filter the Settings Page URL.
		 *
		 * @since 1.0.1
		 *
		 * @param string $url The default Settings Page URL.
		 */
		$url = apply_filters( $this->hook_prefix . '/settings/page/url', $url );

		// --<
		return $url;

	}

	/**
	 * Renders the Settings Page Tab.
	 *
	 * @since 1.0.1
	 */
	public function page_tab_render() {

		// Add active class on our screen.
		$classes = [ 'nav-tab' ];
		$screen  = get_current_screen();
		if ( $screen->id === $this->page_handle ) {
			$classes[] = 'nav-tab-active';
		}

		echo sprintf(
			'<a href="%1$s" class="%2$s">%3$s</a>',
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$this->page_url_get(),
			esc_attr( implode( ' ', $classes ) ),
			esc_html( $this->page_tab_label )
		);

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Registers common meta boxes.
	 *
	 * @since 1.0.1
	 *
	 * @param string $screen_id The Admin Page Screen ID.
	 */
	public function meta_boxes_add( $screen_id ) {

		// Build Screen ID of this page.
		$page_screen_id = $this->page_context . $this->page_slug;
		if ( 'network' === $this->page_location ) {
			$page_screen_id .= '-network';
		}

		// Bail if not the Screen ID we want.
		if ( $screen_id !== $page_screen_id ) {
			return;
		}

		// Check User permissions.
		if ( ! $this->page_capability() ) {
			return;
		}

		// Get common data.
		$data = $this->meta_boxes_data( $screen_id );

		// Configure page layout.
		if ( 'settings' === $this->page_layout ) {

			// Create "Submit" metabox.
			add_meta_box(
				'submitdiv',
				$this->metabox_submit_title, // Metabox title.
				[ $this, 'meta_box_submit_render' ], // Callback.
				$screen_id, // Screen ID.
				'side', // Column: options are 'normal' and 'side'.
				'core', // Vertical placement: options are 'core', 'high', 'low'.
				$data
			);

		}

		/**
		 * Broadcast that the metaboxes have been added.
		 *
		 * @since 1.0.1
		 *
		 * @param string $screen_id The Screen indentifier.
		 * @param array $data The array of metabox data.
		 */
		do_action( $this->hook_prefix . '/settings/page/meta_boxes_added', $screen_id, $data );

	}

	/**
	 * Registers meta boxes.
	 *
	 * @since 1.0.1
	 *
	 * @param string $screen_id The Settings Page Screen ID.
	 * @param array  $data The array of metabox data.
	 */
	public function meta_boxes_register( $screen_id, $data ) {

		// Build Screen ID of this page.
		$page_screen_id = $this->page_context . $this->page_slug;
		if ( 'network' === $this->page_location ) {
			$page_screen_id .= '-network';
		}

		// Bail if not the Screen ID we want.
		if ( $screen_id !== $page_screen_id ) {
			return;
		}

		// Check User permissions.
		if ( ! $this->page_capability() ) {
			return;
		}

		// Define a handle for the following metabox.
		$handle = $this->hook_prefix . '_settings_general';

		// Add the metabox.
		add_meta_box(
			$handle,
			__( 'General Settings', 'sof-organisations' ),
			[ $this, 'meta_box_general_render' ], // Callback.
			$screen_id, // Screen ID.
			'normal', // Column: options are 'normal' and 'side'.
			'core', // Vertical placement: options are 'core', 'high', 'low'.
			$data
		);

		/*
		// Make this metabox closed by default.
		add_filter( "postbox_classes_{$screen_id}_{$handle}", [ $this, 'meta_box_closed' ] );
		*/

		/**
		 * Broadcast that the metaboxes have been added.
		 *
		 * @since 1.0.1
		 *
		 * @param string $screen_id The Screen indentifier.
		 * @param array $data The array of metabox data.
		 */
		do_action( $this->hook_prefix . '/settings/page/meta_boxes/added', $screen_id, $data );

	}

	/**
	 * Gets the array of data to be shared with all metaboxes.
	 *
	 * @since 1.0.1
	 *
	 * @param string $screen_id The Screen indentifier.
	 * @return array $data The array of data to be shared with all metaboxes.
	 */
	public function meta_boxes_data( $screen_id ) {

		/**
		 * Filters the array of data to be shared with all metaboxes.
		 *
		 * @since 1.0.1
		 *
		 * @param array $data The empty default array of metabox data.
		 * @param string $screen_id The Screen indentifier.
		 */
		$data = apply_filters( $this->hook_prefix . '/settings/page/meta_boxes_data', [], $screen_id );

		return $data;

	}

	/**
	 * Loads a metabox as closed by default.
	 *
	 * @since 1.0.1
	 *
	 * @param string[] $classes An array of postbox classes.
	 */
	public function meta_box_closed( $classes ) {

		// Add closed class.
		if ( is_array( $classes ) ) {
			if ( ! in_array( 'closed', $classes, true ) ) {
				$classes[] = 'closed';
			}
		}

		return $classes;

	}

	/**
	 * Renders a Submit metabox.
	 *
	 * @since 1.0.1
	 *
	 * @param mixed $unused Unused param.
	 * @param array $metabox Array containing id, title, callback, and args elements.
	 */
	public function meta_box_submit_render( $unused, $metabox ) {

		// Build path to "Submit" meta box template.
		$template = $this->path_plugin . $this->path_template . $this->path_metabox . 'metabox-settings-submit.php';

		// Include template file.
		require_once $template;

	}

	/**
	 * Renders "General Settings" meta box on Settings screen.
	 *
	 * @since 1.0.1
	 *
	 * @param mixed $unused Unused param.
	 * @param array $metabox Array containing id, title, callback, and args elements.
	 */
	public function meta_box_general_render( $unused, $metabox ) {

		// Get defaults.
		$defaults = $this->admin->settings_get_defaults();

		// Retrieve settings for each option.
		$organisation_enabled = $this->admin->setting_get( 'organisation_enabled', $defaults['organisation_enabled'] );
		$partner_enabled      = $this->admin->setting_get( 'partner_enabled', $defaults['partner_enabled'] );
		$host_enabled         = $this->admin->setting_get( 'host_enabled', $defaults['host_enabled'] );
		$pledgeball_enabled   = $this->admin->setting_get( 'pledgeball_enabled', $defaults['pledgeball_enabled'] );

		// Include template file.
		include $this->path_plugin . $this->path_template . $this->path_metabox . 'metabox-settings-general.php';

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

	/**
	 * Performs actions when the form has been submitted.
	 *
	 * @since 1.0.1
	 */
	public function form_submitted() {

		/**
		 * Filters the Form Submit identifier.
		 *
		 * Use this filter when a meta box has its own submit button.
		 *
		 * @since 1.0.1
		 *
		 * @param string $submit_id The Settings Page form submit ID.
		 */
		$submit_id = apply_filters( $this->hook_prefix . '/settings/form/submit_id', $this->form_submit_id );

		// Was the form submitted?
		if ( ! isset( $_POST[ $submit_id ] ) ) {
			return;
		}

		// Check that we trust the source of the data.
		check_admin_referer( $this->form_nonce_action, $this->form_nonce_field );

		/**
		 * Fires before the Settings have been saved.
		 *
		 * * Callbacks do not need to verify the nonce as this has already been done.
		 * * Callbacks should, however, implement their own data validation checks.
		 *
		 * @since 1.0.1
		 *
		 * @param string $submit_id The Settings Page form submit ID.
		 */
		do_action( $this->hook_prefix . '/settings/form/save_before', $submit_id );

		// Save settings.
		$this->form_save( $submit_id );

		/**
		 * Fires when the Settings have been saved.
		 *
		 * * Callbacks do not need to verify the nonce as this has already been done.
		 * * Callbacks should, however, implement their own data validation checks.
		 *
		 * @since 1.0.1
		 *
		 * @param string $submit_id The Settings Page form submit ID.
		 */
		do_action( $this->hook_prefix . '/settings/form/save_after', $submit_id );

		// Now redirect.
		$this->form_redirect( 'updated' );

	}

	/**
	 * Performs save actions when the form has been submitted.
	 *
	 * @since 1.0.1
	 *
	 * @param string $submit_id The Settings Page form submit ID.
	 */
	protected function form_save( $submit_id ) {

		// Check that we trust the source of the data.
		check_admin_referer( $this->form_nonce_action, $this->form_nonce_field );

		// Get default settings.
		$defaults = $this->admin->settings_get_defaults();

		// Get the data.
		$organisation_enabled = isset( $_POST[ $this->key_organisation_enabled ] ) ? sanitize_text_field( wp_unslash( $_POST[ $this->key_organisation_enabled ] ) ) : 'n';
		$partner_enabled      = isset( $_POST[ $this->key_partner_enabled ] ) ? sanitize_text_field( wp_unslash( $_POST[ $this->key_partner_enabled ] ) ) : 'n';
		$host_enabled         = isset( $_POST[ $this->key_host_enabled ] ) ? sanitize_text_field( wp_unslash( $_POST[ $this->key_host_enabled ] ) ) : 'n';
		$pledgeball_enabled   = isset( $_POST[ $this->key_pledgeball_enabled ] ) ? sanitize_text_field( wp_unslash( $_POST[ $this->key_pledgeball_enabled ] ) ) : 'n';

		// Set individual settings.
		$this->admin->setting_set( 'organisation_enabled', $organisation_enabled );
		$this->admin->setting_set( 'partner_enabled', $partner_enabled );
		$this->admin->setting_set( 'host_enabled', $host_enabled );
		$this->admin->setting_set( 'pledgeball_enabled', $pledgeball_enabled );

		// Save settings.
		$this->admin->settings_save();

		// Flush the Rewrite Rules after registration.
		add_action( 'init', 'flush_rewrite_rules', 100 );

	}

	/**
	 * Redirects to the Settings page with an optional extra param.
	 *
	 * Also responds to redirection requests made by calling:
	 *
	 * do_action( $this->hook_prefix . '/settings/form/redirect' );
	 *
	 * @since 1.0.1
	 *
	 * @param string $mode Pass 'updated' to append the extra param.
	 */
	public function form_redirect( $mode = '' ) {

		// Get the Settings Page URL.
		$url = $this->page_url_get();

		// Maybe append param.
		$args = [];
		if ( 'updated' === $mode ) {
			$args['updated'] = 'true';
		}

		// Redirect to our Settings Page.
		wp_safe_redirect( add_query_arg( $args, $url ) );
		exit;

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Get the URL to access a particular menu page.
	 *
	 * The URL based on the slug it was registered with. If the slug hasn't been
	 * registered properly no url will be returned.
	 *
	 * @since 1.0.1
	 *
	 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu).
	 * @param bool   $echo Whether or not to echo the url - default is true.
	 * @return string $url The URL.
	 */
	public function network_menu_page_url( $menu_slug, $echo = true ) {

		global $_parent_pages;

		if ( isset( $_parent_pages[ $menu_slug ] ) ) {
			$parent_slug = $_parent_pages[ $menu_slug ];
			if ( $parent_slug && ! isset( $_parent_pages[ $parent_slug ] ) ) {
				$url = network_admin_url( add_query_arg( 'page', $menu_slug, $parent_slug ) );
			} else {
				$url = network_admin_url( 'admin.php?page=' . $menu_slug );
			}
		} else {
			$url = '';
		}

		$url = esc_url( $url );

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $url;
		}

		// --<
		return $url;

	}

}
