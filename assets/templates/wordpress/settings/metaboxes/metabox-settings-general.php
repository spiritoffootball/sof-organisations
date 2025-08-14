<?php
/**
 * General Settings template.
 *
 * Handles markup for the General Settings meta box.
 *
 * @package SOF_Organisations
 * @since 1.0.1
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<!-- <?php echo esc_html( $this->path_template . $this->path_metabox ); ?>metabox-settings-general.php -->
<table class="form-table">

	<tr>
		<th scope="row">
			<label for="<?php echo esc_attr( $this->key_organisation_enabled ); ?>"><?php esc_html_e( 'Organisations Post Type', 'sof-organisations' ); ?></label>
		</th>
		<td>
			<select id="<?php echo esc_attr( $this->key_organisation_enabled ); ?>" name="<?php echo esc_attr( $this->key_organisation_enabled ); ?>">
				<option value="y" <?php selected( $organisation_enabled, 'y' ); ?>><?php esc_html_e( 'Yes', 'sof-organisations' ); ?></option>
				<option value="n" <?php selected( $organisation_enabled, 'n' ); ?>><?php esc_html_e( 'No', 'sof-organisations' ); ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'Choose whether to enable the Organisations Post Type.', 'sof-organisations' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="<?php echo esc_attr( $this->key_partner_enabled ); ?>"><?php esc_html_e( 'Partners Post Type', 'sof-organisations' ); ?></label>
		</th>
		<td>
			<select id="<?php echo esc_attr( $this->key_partner_enabled ); ?>" name="<?php echo esc_attr( $this->key_partner_enabled ); ?>">
				<option value="y" <?php selected( $partner_enabled, 'y' ); ?>><?php esc_html_e( 'Yes', 'sof-organisations' ); ?></option>
				<option value="n" <?php selected( $partner_enabled, 'n' ); ?>><?php esc_html_e( 'No', 'sof-organisations' ); ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'Choose whether to enable the Partners Post Type.', 'sof-organisations' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="<?php echo esc_attr( $this->key_host_enabled ); ?>"><?php esc_html_e( 'Ball Hosts Post Type', 'sof-organisations' ); ?></label>
		</th>
		<td>
			<select id="<?php echo esc_attr( $this->key_host_enabled ); ?>" name="<?php echo esc_attr( $this->key_host_enabled ); ?>">
				<option value="y" <?php selected( $host_enabled, 'y' ); ?>><?php esc_html_e( 'Yes', 'sof-organisations' ); ?></option>
				<option value="n" <?php selected( $host_enabled, 'n' ); ?>><?php esc_html_e( 'No', 'sof-organisations' ); ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'Choose whether to enable the Ball Hosts Post Type.', 'sof-organisations' ); ?></p>
		</td>
	</tr>

</table>
