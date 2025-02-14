<div class="wrap" id="sow-widgets-page">
	<div class="page-banner">

		<span class="icon">
			<img src="<?php echo siteorigin_widgets_url( 'admin/images/icon-back.png' ); ?>" class="icon-back" width="50" height="43">
			<img src="<?php echo siteorigin_widgets_url( 'admin/images/icon-gear.png' ); ?>" class="icon-gear" width="26" height="26">
			<img src="<?php echo siteorigin_widgets_url( 'admin/images/icon-front.png' ); ?>" class="icon-front" width="50" height="43">
		</span>
		<h1>
			<?php echo esc_html__( 'SiteOrigin Widgets Bundle', 'so-widgets-bundle' ); ?>
		</h1>

		<div id="sow-widget-search">
			<input type="search" placeholder="<?php esc_attr_e( 'Filter Widgets', 'so-widgets-bundle' ); ?>" />
		</div>
	</div>

	<ul class="page-nav">
		<li class="active">
			<a href="#all">
				<?php echo esc_html__( 'All', 'so-widgets-bundle' ); ?>
			</a>
		</li>
		<li>
			<a href="#enabled">
				<?php echo esc_html__( 'Enabled', 'so-widgets-bundle' ); ?>
			</a>
		</li>
		<li>
			<a href="#disabled">
				<?php echo esc_html__( 'Disabled', 'so-widgets-bundle' ); ?>
			</a>
		</li>
	</ul>


	<div id="widgets-list">

		<?php
		foreach ( $widgets as $file => $widget ) {
			$file = wp_normalize_path( $file );
			?>
			<div class="so-widget-wrap">
				<div class="so-widget so-widget-is-<?php echo $widget['Active'] ? 'active' : 'inactive'; ?>" data-id="<?php echo esc_attr( $widget['ID'] ); ?>">

					<?php
					$banner = '';
					$widget_dir = dirname( $file );

					if ( file_exists( $widget_dir . '/assets/banner.svg' ) ) {
						$banner = str_replace( wp_normalize_path( WP_CONTENT_DIR ), content_url(), $widget_dir ) . '/assets/banner.svg';
					}
					$banner = apply_filters( 'siteorigin_widgets_widget_banner', $banner, $widget );
					?>
					<div class="so-widget-banner" data-seed="<?php echo esc_attr( substr( md5( $widget['ID'] ), 0, 6 ) ); ?>">
						<?php if ( ! empty( $banner ) ) { ?>
							<img src="<?php echo esc_url( $banner ); ?>" />
						<?php } ?>
					</div>

					<div class="so-widget-text">

						<div class="so-widget-active-indicator">
							<?php echo esc_html__( 'Active', 'so-widgets-bundle' ); ?>
						</div>

						<h3><?php echo esc_html( $widget['Name'] ); ?></h3>

						<div class="so-widget-description">
							<?php echo esc_html( $widget['Description'] ); ?>
						</div>

						<?php if ( ! empty( $widget['Author'] ) ) { ?>
							<div class="so-widget-byline">
								By
								<strong>
									<?php
									if ( ! empty( $widget['AuthorURI'] ) ) {
										echo '<a href="' . esc_url( $widget['AuthorURI'] ) . '" target="_blank" rel="noopener noreferrer">';
									}
									echo esc_html( $widget['Author'] );

									if ( ! empty( $widget['AuthorURI'] ) ) {
										echo '</a>';
									}
									?>
								</strong>
							</div>
						<?php } ?>
						<div class="so-action-links">
							<?php if ( empty( $widget['HideActivate'] ) ) { ?>
								<div class="so-widget-toggle-active">
									<button class="button-secondary so-widget-activate" data-status="1">
										<?php echo esc_html__( 'Activate', 'so-widgets-bundle' ); ?>
									</button>

									<button class="button-secondary so-widget-deactivate" data-status="0">
										<?php echo esc_html__( 'Deactivate', 'so-widgets-bundle' ); ?>
									</button>
								</div>
							<?php } ?>

							<?php
							/** @var SiteOrigin_Widget $widget_object */
							$widget_object = ! empty( $widget_objects[ $file ] ) ? $widget_objects[ $file ] : false;

							if ( ! empty( $widget_object ) && $widget_object->has_form( 'settings' ) ) {
								$rel_path = str_replace( wp_normalize_path( WP_CONTENT_DIR ), '', $file );

								$form_url = add_query_arg(
									array(
										'id' => $rel_path,
										'action' => 'so_widgets_setting_form',
									),
									admin_url( 'admin-ajax.php' )
								);
								$form_url = wp_nonce_url( $form_url, 'display-widget-form' );

								?>
								<button class="button-secondary so-widget-settings" data-form-url="<?php echo esc_url( $form_url ); ?>">
									<?php echo esc_html__( 'Settings', 'so-widgets-bundle' ); ?>
								</button>
								<?php
							}
							?>

							<?php if ( ! empty( $widget['Documentation'] ) ) { ?>
								<a href="<?php echo esc_url( $widget['Documentation'] ); ?>" target="_blank" rel="noopener noreferrer" class="so-widget-documentation">
									<?php echo esc_html__( 'Documentation', 'so-widgets-bundle' ); ?>
								</a>
							<?php } ?>
						</div>
					</div>

				</div>
			</div>
		<?php } ?>

	</div>

	<?php if ( ! class_exists( 'SiteOrigin_Panels' ) || ! class_exists( 'SiteOrigin_Premium' ) ) { ?>
		<div class="installer">
			<a href="#" class="installer-link">
				<?php echo esc_html__( 'General Widget Bundle Settings', 'so-widgets-bundle' ); ?>
			</a>

			<div class="installer-container" style="display: none;">
				<label>
					<?php echo __( 'Enable SiteOrigin Installer: ', 'so-widgets-bundle' ); ?>
					<input
						type="checkbox"
						name="installer_status"
						class="installer_status"
						<?php checked( get_option( 'siteorigin_installer', true ), 1 ); ?>
						data-nonce="<?php echo wp_create_nonce( 'siteorigin_installer_status' ); ?>"
					>
				</label>
			</div>
		</div>
	<?php } ?>


	<div class="developers-link">
		<?php echo esc_html__( 'Developers - create your own widgets for the Widgets Bundle.', 'so-widgets-bundle' ); ?>
		<a href="https://siteorigin.com/docs/widgets-bundle/" target="_blank" rel="noopener noreferrer">
			<?php echo esc_html__( 'Read More', 'so-widgets-bundle' ); ?>
		</a>.
	</div>

	<div id="sow-settings-dialog">
		<div class="so-overlay"></div>

		<div class="so-title-bar">
			<h3 class="so-title">
				<?php echo esc_html__( 'Widget Settings', 'so-widgets-bundle' ); ?>
			</h3>
			<a class="so-close" tabindex="0">
				<span class="so-dialog-icon"></span>
			</a>
		</div>

		<div class="so-content so-loading">
		</div>

		<div class="so-toolbar">
			<div class="so-buttons">
				<button class="button-primary so-save" tabindex="0">
					<?php echo esc_html__( 'Save', 'so-widgets-bundle' ); ?>
				</button>
			</div>
		</div>
	</div>

	<iframe id="so-widget-settings-save" name="so-widget-settings-save"></iframe>

</div>
