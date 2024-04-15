<?php
/**
 * Settings Page template.
 *
 * Handles markup for the Settings Page.
 *
 * @package SOF_Organisations
 * @since 1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<!-- assets/templates/wordpress/pages/page-admin-settings.php -->
<div class="wrap">

	<h1><?php esc_html_e( 'Ball Host Settings', 'sof-organisations' ); ?></h1>

	<?php if ( $show_tabs ) : ?>
		<h2 class="nav-tab-wrapper">
			<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
			<a href="<?php echo $urls['settings']; ?>" class="nav-tab nav-tab-active"><?php esc_html_e( 'Settings', 'sof-organisations' ); ?></a>
			<?php

			/**
			 * Allow others to add tabs.
			 *
			 * @since 1.0
			 *
			 * @param array $urls The array of subpage URLs.
			 * @param string The key of the active tab in the subpage URLs array.
			 */
			do_action( 'sof_orgs/admin/settings/nav_tabs', $urls, 'settings' );

			?>
		</h2>
	<?php else : ?>
		<hr />
	<?php endif; ?>

	<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
	<form method="post" id="sof_orgs_settings_form" action="<?php echo $this->page_settings_submit_url_get(); ?>">

		<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
		<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
		<?php wp_nonce_field( 'sof_orgs_settings_action', 'sof_orgs_settings_nonce' ); ?>

		<div id="poststuff">

			<div id="post-body" class="metabox-holder columns-<?php echo esc_attr( $columns ); ?>">

				<!--<div id="post-body-content">
				</div>--><!-- #post-body-content -->

				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes( $screen->id, 'side', null ); ?>
				</div>

				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( $screen->id, 'normal', null ); ?>
					<?php do_meta_boxes( $screen->id, 'advanced', null ); ?>
				</div>

			</div><!-- #post-body -->
			<br class="clear">

		</div><!-- #poststuff -->

	</form>

</div><!-- /.wrap -->
