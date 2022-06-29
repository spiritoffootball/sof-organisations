<?php
/**
 * Organisations ACF Class.
 *
 * Handles ACF functionality for Organisations.
 *
 * @package SOF_Organisations
 * @since 1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Organisations ACF Class.
 *
 * A class that encapsulates ACF functionality for Organisations.
 *
 * @package SOF_Organisations
 */
class SOF_Organisations_ACF_Organisations {

	/**
	 * Plugin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;

	/**
	 * ACF Field Group prefix.
	 *
	 * @since 1.0
	 * @access public
	 * @var object $group_prefix The prefix of the ACF Field Group.
	 */
	public $group_prefix = 'group_sof_org_';

	/**
	 * ACF Field prefix.
	 *
	 * @since 1.0
	 * @access public
	 * @var object $group_prefix The prefix of the ACF Field.
	 */
	public $field_prefix = 'field_sof_org_';

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param object $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->plugin = $parent->plugin;
		$this->acf = $parent;

		// Init when this plugin is loaded.
		add_action( 'sof_orgs/acf/loaded', [ $this, 'register_hooks' ] );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0
	 */
	public function register_hooks() {

		// Add Field Group and Fields.
		add_action( 'acf/init', [ $this, 'field_groups_add' ] );
		add_action( 'acf/init', [ $this, 'fields_add' ] );

	}

	// -------------------------------------------------------------------------

	/**
	 * Add ACF Field Groups.
	 *
	 * @since 1.0
	 */
	public function field_groups_add() {

		// Attach the Field Group to our CPT.
		$field_group_location = [
			[
				[
					'param' => 'post_type',
					'operator' => '==',
					'value' => $this->plugin->cpt->organisations->post_type_name,
				],
			],
		];

		// Hide UI elements on our CPT edit page.
		$field_group_hide_elements = [
			//'the_content',
			//'excerpt',
			'discussion',
			'comments',
			//'revisions',
			'author',
			'format',
			'page_attributes',
			'featured_image',
			'tags',
			'send-trackbacks',
		];

		// Define Field Group.
		$field_group = [
			'key' => $this->group_prefix . 'data',
			'title' => __( 'Organisation Information', 'sof-organisations' ),
			'fields' => [],
			'location' => $field_group_location,
			'hide_on_screen' => $field_group_hide_elements,
		];

		// Now add the Field Group.
		acf_add_local_field_group( $field_group );

	}

	/**
	 * Add ACF Fields.
	 *
	 * @since 1.0
	 */
	public function fields_add() {

		// Add our ACF Fields.
		$this->field_link_add();

	}

	/**
	 * Add "Link" Field.
	 *
	 * @since 1.0
	 */
	public function field_link_add() {

		// Define Field.
		$field = [
			'key' => $this->field_prefix . 'link',
			'label' => __( 'Link to website', 'sof-organisations' ),
			'name' => 'link',
			'type' => 'url',
			'instructions' => '',
			'default_value' => '',
			'placeholder' => '',
			'parent' => $this->group_prefix . 'data',
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

}
