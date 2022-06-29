<?php
/**
 * Events ACF Class.
 *
 * Handles ACF functionality for Events.
 *
 * @package SOF_Organisations
 * @since 1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Events ACF Class.
 *
 * A class that encapsulates ACF functionality for Events.
 *
 * @package SOF_Organisations
 */
class SOF_Organisations_ACF_Events {

	/**
	 * Plugin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;

	/**
	 * ACF object.
	 *
	 * @since 1.0
	 * @access public
	 * @var object $acf The ACF object.
	 */
	public $acf;

	/**
	 * ACF Field Group prefix.
	 *
	 * @since 1.0
	 * @access public
	 * @var object $group_prefix The prefix of the ACF Field Group.
	 */
	public $group_prefix = 'group_sof_event_';

	/**
	 * ACF Field prefix.
	 *
	 * @since 1.0
	 * @access public
	 * @var object $group_prefix The prefix of the ACF Field.
	 */
	public $field_prefix = 'field_sof_event_';

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
	 * Adds the ACF Field Groups.
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
					'value' => 'event',
				],
			],
		];

		// Hide UI elements on our CPT edit page.
		$field_group_hide_elements = [
			//'the_content',
			//'excerpt',
			//'discussion',
			//'comments',
			//'revisions',
			'author',
			//'format',
			//'page_attributes',
			//'featured_image',
			//'tags',
			//'send-trackbacks',
		];

		// Define Field Group.
		$field_group = [
			'key' => $this->group_prefix . 'data',
			'title' => __( 'Ball Host', 'sof-organisations' ),
			'fields' => [],
			'location' => $field_group_location,
			'hide_on_screen' => $field_group_hide_elements,
			'position' => 'normal',
			'menu_order' => 100,
		];

		// Now add the Field Group.
		acf_add_local_field_group( $field_group );

	}

	/**
	 * Adds the ACF Fields.
	 *
	 * @since 1.0
	 */
	public function fields_add() {

		// Add our ACF Fields.
		$this->field_organiser_add();
		//$this->field_organisations_add();

	}

	/**
	 * Add "Organiser" Field.
	 *
	 * @since 1.0
	 */
	public function field_organiser_add() {

		// Get the ID of the Ball Host Custom Field.
		$field_id = $this->plugin->admin->setting_get( 'event_ball_host_custom_field_id' );

		// Bail if we don't have one yet.
		if ( empty( $field_id ) ) {
			return;
		}

		// Define a Contact Reference Field.
		$field = [
			'key' => $this->field_prefix . 'organiser',
			'label' => __( 'Organisation', 'sof-organisations' ),
			'name' => 'ball_host',
			'type' => 'civicrm_contact',
			'instructions' => '',
			'parent' => $this->group_prefix . 'data',
			'conditional_logic' => 0,
			'wrapper' => [
				'width' => '',
				'class' => '',
				'id' => '',
			],
			'field_cacf_civicrm_custom_field' => 'caicustom_' . $field_id,
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

	/**
	 * Add "Organisations" Field.
	 *
	 * TESTING - NOT USED.
	 *
	 * @since 1.0
	 */
	public function field_organisations_add() {

		// Define a Repeater field.
		$field = [
			'key' => $this->field_prefix . 'organisations',
			'label' => __( 'Organisers', 'sof-organisations' ),
			'name' => 'organisations',
			'type' => 'repeater',
			'parent' => $this->group_prefix . 'data',
			'menu_order' => 0,
			'conditional_logic' => 0,
			'wrapper' => [
				'width' => '',
				'class' => '',
				'id' => '',
			],
			'min' => 0,
			'max' => 0,
			'button_label' => __( 'Add Organiser', 'sof-organisations' ),
			'sub_fields' => [],
		];

		// Add Contact Reference Field.
		$field['sub_fields'][] = [
			'key' => $this->field_prefix . 'organisation',
			'label' => __( 'Organiser', 'sof-organisations' ),
			'name' => 'organisation',
			'type' => 'civicrm_contact',
			'instructions' => '',
			'conditional_logic' => 0,
			'wrapper' => [
				'width' => '',
				'class' => '',
				'id' => '',
			],
			'field_cacf_civicrm_custom_field' => 'caicustom_1',
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

}