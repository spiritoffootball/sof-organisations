<?php
/**
 * Ball Host Settings Page submit template.
 *
 * Handles markup for the Ball Host Settings Page submit meta box.
 *
 * @package SOF_Organisations
 * @since 1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<!-- assets/templates/wordpress/metaboxes/metabox-ball-host-settings-submit.php -->
<div class="submitbox">
	<div id="minor-publishing">
		<div id="misc-publishing-actions">
			<div class="misc-pub-section">
				<span><?php esc_html_e( 'Save your settings here.', 'sof-organisations' ); ?></span>
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<div id="major-publishing-actions">
		<div id="publishing-action">
			<?php submit_button( esc_html__( 'Update', 'sof-organisations' ), 'primary', 'sof_orgs_save', false ); ?>
			<input type="hidden" name="action" value="update" />
		</div>
		<div class="clear"></div>
	</div>
</div>
