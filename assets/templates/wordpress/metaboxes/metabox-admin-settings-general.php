<?php
/**
 * Settings Page "General Settings" template.
 *
 * Handles markup for the Settings Page "General Settings" meta box.
 *
 * @package SOF_Organisations
 * @since 1.0
 */

?><!-- assets/templates/wordpress/metaboxes/metabox-admin-settings-general.php -->
<p><?php esc_html_e( 'The following options configure Ball Host defaults.', 'sof-organisations' ); ?></p>

<?php

/**
 * Before Settings table.
 *
 * @since 1.0
 */
do_action( 'sof_orgs/settings_table/before' );

?>

<table class="form-table">

	<?php

	/**
	 * Start of Settings table rows.
	 *
	 * @since 1.0
	 */
	do_action( 'sof_orgs/settings_table/first_row' );

	?>

	<?php if ( ! empty( $custom_fields ) ) : ?>
		<tr valign="top">
			<th scope="row"><label for="sof_orgs_event_org"><?php esc_html_e( 'Event Ball Host', 'sof-organisations' ); ?></label></th>
			<td>
				<select id="sof_orgs_event_org" name="sof_orgs_event_org">
					<option value="">
						<?php echo esc_html_e( '- Select Field -', 'sof-organisations' ); ?>
					</option>
					<?php foreach ( $custom_fields as $optgroup => $options ) : ?>
						<optgroup label="<?php echo esc_attr( $optgroup ); ?>">
							<?php foreach ( $options as $option_id => $label ) : ?>
								<option value="<?php echo esc_attr( $option_id ); ?>" <?php selected( $custom_field_id, $option_id ); ?>>
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach ?>
						</optgroup>
					<?php endforeach ?>
				</select>
				<p class="description"><?php esc_html_e( 'Select the CiviCRM Custom Field that defines the Contact ID of the Event Ball Host.', 'sof-organisations' ); ?></p>
			</td>
		</tr>
	<?php endif; ?>

	<?php

	/**
	 * End of Settings table rows.
	 *
	 * @since 1.0
	 */
	do_action( 'sof_orgs/settings_table/last_row' );

	?>

</table>

<?php

/**
 * After Settings table.
 *
 * @since 1.0
 */
do_action( 'sof_orgs/settings_table/after' );
