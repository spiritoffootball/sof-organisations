<?php
/**
 * Ball Hosts Custom Post Type Class.
 *
 * Handles providing a "Ball Hosts" Custom Post Type.
 *
 * @package SOF_Organisations
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Ball Hosts Custom Post Type Class.
 *
 * A class that encapsulates a "Ball Hosts" Custom Post Type.
 *
 * @since 1.0
 */
class SOF_Organisations_CPT_Hosts {

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
	public $post_type_name = 'host';

	/**
	 * Custom Post Type REST base.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $post_type_rest_base = 'hosts';

	/**
	 * Taxonomy name.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_name = 'host-type';

	/**
	 * Taxonomy REST base.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_rest_base = 'host-type';

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
		do_action( 'sof_orgs/cpt/hosts/loaded' );

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
			'name'               => __( 'Ball Hosts', 'sof-organisations' ),
			'singular_name'      => __( 'Ball Host', 'sof-organisations' ),
			'add_new'            => __( 'Add New', 'sof-organisations' ),
			'add_new_item'       => __( 'Add New Ball Host', 'sof-organisations' ),
			'edit_item'          => __( 'Edit Ball Host', 'sof-organisations' ),
			'new_item'           => __( 'New Ball Host', 'sof-organisations' ),
			'all_items'          => __( 'All Ball Hosts', 'sof-organisations' ),
			'view_item'          => __( 'View Ball Host', 'sof-organisations' ),
			'search_items'       => __( 'Search Ball Hosts', 'sof-organisations' ),
			'not_found'          => __( 'No matching Ball Host found', 'sof-organisations' ),
			'not_found_in_trash' => __( 'No Ball Hosts found in Trash', 'sof-organisations' ),
			'menu_name'          => __( 'Ball Hosts', 'sof-organisations' ),
		];

		// Build Post Type args.
		$args = [

			'labels'              => $labels,

			// Defaults.
			'menu_icon'           => 'dashicons-groups',
			'description'         => __( 'A ball host organisation post type', 'sof-organisations' ),
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
			'menu_position'       => 44,
			'map_meta_cap'        => true,

			// Rewrite.
			'rewrite'             => [
				'slug'       => 'hosts',
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

		// Set up the post type called "Ball Host".
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
				__( 'Ball Host updated. <a href="%s">View Ball Host</a>', 'sof-organisations' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Custom fields.
			2  => __( 'Custom field updated.', 'sof-organisations' ),
			3  => __( 'Custom field deleted.', 'sof-organisations' ),
			4  => __( 'Ball Host updated.', 'sof-organisations' ),

			// Item restored to a revision.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			5  => isset( $_GET['revision'] ) ?

				// Revision text.
				sprintf(
					/* translators: %s: The date and time of the revision. */
					__( 'Ball Host restored to revision from %s', 'sof-organisations' ),
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wp_post_revision_title( (int) $_GET['revision'], false )
				) :

				// No revision.
				false,

			// Item published.
			6  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Ball Host published. <a href="%s">View Ball Host</a>', 'sof-organisations' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Item saved.
			7  => __( 'Ball Host saved.', 'sof-organisations' ),

			// Item submitted.
			8  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Ball Host submitted. <a target="_blank" href="%s">Preview Ball Host</a>', 'sof-organisations' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// Item scheduled.
			9  => sprintf(
				/* translators: 1: The date, 2: The permalink. */
				__( 'Ball Host scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Ball Host</a>', 'sof-organisations' ),
				/* translators: Publish box date format - see https://php.net/date */
				date_i18n( __( 'M j, Y @ G:i', 'sof-organisations' ), strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Draft updated.
			10 => sprintf(
				/* translators: %s: The permalink. */
				__( 'Ball Host draft updated. <a target="_blank" href="%s">Preview Ball Host</a>', 'sof-organisations' ),
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
		$title = __( 'Add the name of the Ball Host', 'sof-organisations' );

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
				'name'              => _x( 'Ball Host Types', 'taxonomy general name', 'sof-organisations' ),
				'singular_name'     => _x( 'Ball Host Type', 'taxonomy singular name', 'sof-organisations' ),
				'search_items'      => __( 'Search Ball Host Types', 'sof-organisations' ),
				'all_items'         => __( 'All Ball Host Types', 'sof-organisations' ),
				'parent_item'       => __( 'Parent Ball Host Type', 'sof-organisations' ),
				'parent_item_colon' => __( 'Parent Ball Host Type:', 'sof-organisations' ),
				'edit_item'         => __( 'Edit Ball Host Type', 'sof-organisations' ),
				'update_item'       => __( 'Update Ball Host Type', 'sof-organisations' ),
				'add_new_item'      => __( 'Add New Ball Host Type', 'sof-organisations' ),
				'new_item_name'     => __( 'New Ball Host Type Name', 'sof-organisations' ),
				'menu_name'         => __( 'Ball Host Types', 'sof-organisations' ),
				'not_found'         => __( 'No Ball Host Types found', 'sof-organisations' ),
			],

			// Rewrite rules.
			'rewrite'           => [
				'slug' => 'host-types',
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

}
