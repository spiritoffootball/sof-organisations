<?php
/**
 * Events ACF Class.
 *
 * Handles ACF functionality for Events.
 *
 * @package SOF_Organisations
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Events ACF Class.
 *
 * A class that encapsulates ACF functionality for Events.
 *
 * @since 1.0
 */
class SOF_Organisations_ACF_Events {

	/**
	 * Plugin object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations
	 */
	public $plugin;

	/**
	 * ACF object.
	 *
	 * @since 1.0
	 * @access public
	 * @var SOF_Organisations_ACF
	 */
	public $acf;

	/**
	 * ACF Field Group prefix.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $group_prefix = 'group_sof_event_';

	/**
	 * ACF Field prefix.
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $field_prefix = 'field_sof_event_';

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param SOF_Organisations_ACF $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->plugin = $parent->plugin;
		$this->acf    = $parent;

		// Init when this plugin is loaded.
		add_action( 'sof_orgs/acf/loaded', [ $this, 'initialise' ] );

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
		do_action( 'sof_orgs/acf/events/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0
	 */
	private function register_hooks() {

		// Add Field Group and Fields.
		add_action( 'acf/init', [ $this, 'field_groups_add' ] );
		add_action( 'acf/init', [ $this, 'fields_add' ] );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Adds the ACF Field Groups.
	 *
	 * @since 1.0
	 */
	public function field_groups_add() {

		// Add our ACF Field Groups.
		$this->field_group_pledgeball_add();

	}

	/**
	 * Adds "Pledgeball Information" Field.
	 *
	 * @since 1.0
	 */
	private function field_group_pledgeball_add() {

		// Attach the Field Group to our CPT.
		$field_group_location = [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'event',
				],
			],
		];

		// Hide UI elements on our CPT edit page.
		$field_group_hide_elements = [
			// 'the_content',
			// 'excerpt',
			// 'discussion',
			// 'comments',
			// 'revisions',
			'author',
			// 'format',
			// 'page_attributes',
			// 'featured_image',
			// 'tags',
			// 'send-trackbacks',
		];

		// Define Field Group.
		$field_group = [
			'key'            => $this->group_prefix . 'data',
			'title'          => __( 'Pledgeball Information', 'sof-organisations' ),
			'fields'         => [],
			'location'       => $field_group_location,
			'hide_on_screen' => $field_group_hide_elements,
			'position'       => 'normal',
			'menu_order'     => 100,
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
		$this->field_form_enabled_add();
		$this->field_use_country_add();

	}

	/**
	 * Add "Organiser" Field.
	 *
	 * @since 1.0
	 */
	private function field_organiser_add() {

		// Get the ID of the Ball Host Custom Field.
		$field_id = $this->plugin->admin->setting_get( 'event_ball_host_custom_field_id' );

		// Bail if we don't have one yet.
		if ( empty( $field_id ) ) {
			return;
		}

		// Define a Contact Reference Field.
		$field = [
			'key'                             => $this->field_prefix . 'organiser',
			'label'                           => __( 'Pledgeball Event Host', 'sof-organisations' ),
			'name'                            => 'ball_host',
			'type'                            => 'civicrm_contact',
			'instructions'                    => __( 'This is the Organisation that Pledgeball requires the Event to be associated with.', 'sof-organisations' ),
			'parent'                          => $this->group_prefix . 'data',
			'conditional_logic'               => 0,
			'wrapper'                         => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'field_cacf_civicrm_custom_field' => 'caicustom_' . $field_id,
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

	/**
	 * Add "Pledge Form Enabled" Field.
	 *
	 * @since 1.0
	 */
	private function field_form_enabled_add() {

		// Define a Repeater field.
		$field = [
			'key'               => $this->field_prefix . 'pledge_form_enabled',
			'label'             => __( 'Enable Pledge Form', 'sof-organisations' ),
			'name'              => 'pledge_form_enabled',
			'type'              => 'true_false',
			'parent'            => $this->group_prefix . 'data',
			'instructions'      => __( 'Is the Pledge Form enabled for this Event?', 'sof-organisations' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width'                      => '',
				'class'                      => '',
				'id'                         => '',
				'data-instruction-placement' => 'field',
			],
			'acfe_permissions'  => '',
			'message'           => '',
			'default_value'     => 0,
			'ui'                => 1,
			'ui_on_text'        => '',
			'ui_off_text'       => '',
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

	/**
	 * Add "Use Pledge List for Country" Field.
	 *
	 * @since 1.0
	 */
	private function field_use_country_add() {

		// Define a Repeater field.
		$field = [
			'key'               => $this->field_prefix . 'pledge_form_use_country',
			'label'             => __( 'Use Country-specific Pledge List', 'sof-organisations' ),
			'name'              => 'pledge_form_use_country',
			'type'              => 'true_false',
			'parent'            => $this->group_prefix . 'data',
			'instructions'      => __( 'If yes, the Pledge List for the Venue Country will be used.', 'sof-organisations' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width'                      => '',
				'class'                      => '',
				'id'                         => '',
				'data-instruction-placement' => 'field',
			],
			'acfe_permissions'  => '',
			'message'           => '',
			'default_value'     => 0,
			'ui'                => 1,
			'ui_on_text'        => '',
			'ui_off_text'       => '',
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

}
