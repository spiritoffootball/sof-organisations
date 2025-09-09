<?php
/**
 * Organisations Custom Post Type Class.
 *
 * Handles providing an "Organisations" Custom Post Type.
 *
 * @package SOF_Organisations
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Organisations Custom Post Type Class.
 *
 * A class that encapsulates an "Organisations" Custom Post Type.
 *
 * @since 1.0
 */
class SOF_Organisations_CPT_Organisations {

	/**
	 * Plugin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations
	 */
	public $plugin;

	/**
	 * Custom Post Type object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_CPT
	 */
	public $cpt;

	/**
	 * Custom Post Type name.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $post_type_name = 'organisation';

	/**
	 * Custom Post Type REST base.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $post_type_rest_base = 'organisations';

	/**
	 * Taxonomy name.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_name = 'organisation-type';

	/**
	 * Taxonomy REST base.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_rest_base = 'organisation-type';

	/**
	 * Free Taxonomy name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_free_name = 'organisation-tag';

	/**
	 * Free Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_free_rest_base = 'organisation-tag';

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param SOF_Organisations_CPT $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->plugin = $parent->plugin;
		$this->cpt    = $parent;

		// Init when this plugin is loaded.
		add_action( 'sof_orgs/cpt/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this object.
	 *
	 * @since 1.0.1
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap class.
		$this->register_hooks();

		/**
		 * Fires when this class is loaded.
		 *
		 * @since 1.0.1
		 */
		do_action( 'sof_orgs/cpt/organisations/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0
	 */
	private function register_hooks() {

		// Activation and deactivation.
		add_action( 'sof_orgs/activate', [ $this, 'activate' ] );
		add_action( 'sof_orgs/deactivate', [ $this, 'deactivate' ] );

		// Always create post type.
		add_action( 'init', [ $this, 'post_type_create' ] );

		// Make sure our feedback is appropriate.
		add_filter( 'post_updated_messages', [ $this, 'post_type_messages' ] );

		// Make sure our UI text is appropriate.
		add_filter( 'enter_title_here', [ $this, 'post_type_title' ] );

		// Create primary taxonomy.
		add_action( 'init', [ $this, 'taxonomy_create' ] );
		add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_fix_metabox' ], 10, 2 );
		add_action( 'restrict_manage_posts', [ $this, 'taxonomy_filter_post_type' ] );

		// Create free tagging taxonomy.
		add_action( 'init', [ $this, 'taxonomy_free_create' ] );

	}

	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 1.0
	 */
	public function activate() {

		// Pass through.
		$this->post_type_create();
		$this->taxonomy_create();

		// Go ahead and flush.
		flush_rewrite_rules();

	}

	/**
	 * Actions to perform on plugin deactivation (NOT deletion).
	 *
	 * @since 1.0
	 */
	public function deactivate() {

		// Flush rules to reset.
		flush_rewrite_rules();

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Create our Custom Post Type.
	 *
	 * @since 1.0
	 */
	public function post_type_create() {

		// Only call this once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Define labels.
		$labels = [
			'name'               => __( 'Organisations', 'sof-organisations' ),
			'singular_name'      => __( 'Organisation', 'sof-organisations' ),
			'add_new'            => __( 'Add New', 'sof-organisations' ),
			'add_new_item'       => __( 'Add New Organisation', 'sof-organisations' ),
			'edit_item'          => __( 'Edit Organisation', 'sof-organisations' ),
			'new_item'           => __( 'New Organisation', 'sof-organisations' ),
			'all_items'          => __( 'All Organisations', 'sof-organisations' ),
			'view_item'          => __( 'View Organisation', 'sof-organisations' ),
			'search_items'       => __( 'Search Organisations', 'sof-organisations' ),
			'not_found'          => __( 'No matching Organisation found', 'sof-organisations' ),
			'not_found_in_trash' => __( 'No Organisations found in Trash', 'sof-organisations' ),
			'menu_name'          => __( 'Organisations', 'sof-organisations' ),
		];

		// Define Post Type args.
		$args = [

			'labels'              => $labels,

			// Defaults.
			'menu_icon'           => 'dashicons-groups',
			'description'         => __( 'An organisation post type', 'sof-organisations' ),
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => false,
			'query_var'           => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_position'       => 42,
			'map_meta_cap'        => true,

			// Rewrite.
			'rewrite'             => [
				'slug'       => 'organisations',
				'with_front' => false,
			],

			// Supports.
			'supports'            => [
				'title',
				'thumbnail',
			],

			// REST setup.
			'show_in_rest'        => true,
			'rest_base'           => $this->post_type_rest_base,

		];

		// Set up the post type called "Organisation".
		register_post_type( $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Override messages for a Custom Post Type.
	 *
	 * @since 1.0
	 *
	 * @param array $messages The existing messages.
	 * @return array $messages The modified messages.
	 */
	public function post_type_messages( $messages ) {

		// Access relevant globals.
		global $post, $post_ID;

		// Define custom messages for our Custom Post Type.
		$messages[ $this->post_type_name ] = [

			// Unused - messages start at index 1.
			0  => '',

			// Item updated.
			1  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Organisation updated. <a href="%s">View Organisation</a>', 'sof-organisations' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Custom fields.
			2  => __( 'Custom field updated.', 'sof-organisations' ),
			3  => __( 'Custom field deleted.', 'sof-organisations' ),
			4  => __( 'Organisation updated.', 'sof-organisations' ),

			// Item restored to a revision.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			5  => isset( $_GET['revision'] ) ?

				// Revision text.
				sprintf(
					/* translators: %s: The date and time of the revision. */
					__( 'Organisation restored to revision from %s', 'sof-organisations' ),
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wp_post_revision_title( (int) $_GET['revision'], false )
				) :

				// No revision.
				false,

			// Item published.
			6  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Organisation published. <a href="%s">View Organisation</a>', 'sof-organisations' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Item saved.
			7  => __( 'Organisation saved.', 'sof-organisations' ),

			// Item submitted.
			8  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Organisation submitted. <a target="_blank" href="%s">Preview Organisation</a>', 'sof-organisations' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// Item scheduled.
			9  => sprintf(
				/* translators: 1: The date, 2: The permalink. */
				__( 'Organisation scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Organisation</a>', 'sof-organisations' ),
				/* translators: Publish box date format - see https://php.net/date */
				date_i18n( __( 'M j, Y @ G:i', 'sof-organisations' ), strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Draft updated.
			10 => sprintf(
				/* translators: %s: The permalink. */
				__( 'Organisation draft updated. <a target="_blank" href="%s">Preview Organisation</a>', 'sof-organisations' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

		];

		// --<
		return $messages;

	}

	/**
	 * Override the "Add title" label.
	 *
	 * @since 1.0
	 *
	 * @param str $title The existing title - usually "Add title".
	 * @return str $title The modified title.
	 */
	public function post_type_title( $title ) {

		// Bail if not our post type.
		if ( get_post_type() !== $this->post_type_name ) {
			return $title;
		}

		// Overwrite with our string.
		$title = __( 'Add the name of the Organisation', 'sof-organisations' );

		// --<
		return $title;

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Create our Custom Taxonomy.
	 *
	 * @since 1.0
	 */
	public function taxonomy_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Arguments.
		$args = [

			// Same as "category".
			'hierarchical'      => true,

			// Labels.
			'labels'            => [
				'name'              => _x( 'Organisation Types', 'taxonomy general name', 'sof-organisations' ),
				'singular_name'     => _x( 'Organisation Type', 'taxonomy singular name', 'sof-organisations' ),
				'search_items'      => __( 'Search Organisation Types', 'sof-organisations' ),
				'all_items'         => __( 'All Organisation Types', 'sof-organisations' ),
				'parent_item'       => __( 'Parent Organisation Type', 'sof-organisations' ),
				'parent_item_colon' => __( 'Parent Organisation Type:', 'sof-organisations' ),
				'edit_item'         => __( 'Edit Organisation Type', 'sof-organisations' ),
				'update_item'       => __( 'Update Organisation Type', 'sof-organisations' ),
				'add_new_item'      => __( 'Add New Organisation Type', 'sof-organisations' ),
				'new_item_name'     => __( 'New Organisation Type Name', 'sof-organisations' ),
				'menu_name'         => __( 'Organisation Types', 'sof-organisations' ),
				'not_found'         => __( 'No Organisation Types found', 'sof-organisations' ),
			],

			// Rewrite rules.
			'rewrite'           => [
				'slug' => 'organisation-types',
			],

			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui'           => true,

			// REST setup.
			'show_in_rest'      => true,
			'rest_base'         => $this->taxonomy_rest_base,

		];

		// Register a taxonomy for this CPT.
		register_taxonomy( $this->taxonomy_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Fix the Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 1.0
	 *
	 * @param array $args The existing arguments.
	 * @param int   $post_id The WordPress post ID.
	 */
	public function taxonomy_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] === $this->taxonomy_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}

	/**
	 * Add a filter for this Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0
	 */
	public function taxonomy_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'sof-organisations' ), $taxonomy->label ),
			'taxonomy'        => $this->taxonomy_name,
			'name'            => $this->taxonomy_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->taxonomy_name ] ) ? wp_unslash( $_GET[ $this->taxonomy_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Create our free-tagging Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_free_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Define Taxonomy arguments.
		$args = [

			// General.
			'public'            => true,
			'hierarchical'      => false,

			// Labels.
			'labels'            => [
				'name'                       => _x( 'Organisation Tags', 'taxonomy general name', 'sof-organisations' ),
				'singular_name'              => _x( 'Organisation Tag', 'taxonomy singular name', 'sof-organisations' ),
				'menu_name'                  => __( 'Organisation Tags', 'sof-organisations' ),
				'search_items'               => __( 'Search Organisation Tags', 'sof-organisations' ),
				'all_items'                  => __( 'All Organisation Tags', 'sof-organisations' ),
				'edit_item'                  => __( 'Edit Organisation Tag', 'sof-organisations' ),
				'update_item'                => __( 'Update Organisation Tag', 'sof-organisations' ),
				'add_new_item'               => __( 'Add New Organisation Tag', 'sof-organisations' ),
				'new_item_name'              => __( 'New Organisation Tag Name', 'sof-organisations' ),
				'not_found'                  => __( 'No Organisation Tags found', 'sof-organisations' ),
				'popular_items'              => __( 'Popular Organisation Tags', 'sof-organisations' ),
				'separate_items_with_commas' => __( 'Separate Organisation Tags with commas', 'sof-organisations' ),
				'add_or_remove_items'        => __( 'Add or remove Organisation Tag', 'sof-organisations' ),
				'choose_from_most_used'      => __( 'Choose from the most popular Organisation Tags', 'sof-organisations' ),
			],

			// Rewrite rules.
			'rewrite'           => [
				'slug' => 'organisation-tags',
			],

			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui'           => true,

			// REST setup.
			'show_in_rest'      => true,
			'rest_base'         => $this->taxonomy_free_rest_base,

		];

		// Go ahead and register the Taxonomy now.
		register_taxonomy( $this->taxonomy_free_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

}
