<?php
/**
 * @var $title string
 * @var $columns array
 * @var $before_title string
 * @var $after_title string
 * @var $button_attrs boolean
 * @var $equalize_row_heights boolean
 * @var $any_column_has_image boolean
 */

$initial_button_attrs = $button_attrs;
?>

<?php if ( ! empty( $title ) ) {
	echo $before_title . $title . $after_title;
} ?>

<div class="ow-pt-columns-atom<?php echo (bool) $equalize_row_heights ? ' sow-equalize-row-heights' : ''; ?>">

	<?php foreach ( $columns as $i => $column ) { ?>
		<div class="ow-pt-column <?php echo $this->get_column_classes( $column, $i, $columns ); ?>"
			 style="width: <?php echo round( 100 / count( $columns ), 3 ); ?>%">
			<div class="ow-pt-title">
				<?php echo wp_kses_post( $column['title'] ); ?>
				<?php if ( ! empty( $column['subtitle'] ) ) { ?>
					<div class="ow-pt-subtitle">
						<?php echo wp_kses_post( $column['subtitle'] ); ?>
					</div>
				<?php } ?>
			</div>

            <div class="ow-pt-details">
                <div class="ow-pt-price">
					<?php echo wp_kses_post( $column['price'] ); ?>
				</div>
				<?php if ( ! empty( $column['sale_price'] ) ) { ?>
					<span class="ow-pt-sale-price">
						<?php echo wp_kses_post( $column['sale_price'] ); ?>
					</span>
				<?php } ?>
                <div class="ow-pt-per"><?php echo wp_kses_post( $column['per'] ); ?></div>
            </div>

			<?php if ( ( $equalize_row_heights && $any_column_has_image ) || ! ( $equalize_row_heights || empty( $column['image'] ) ) ) { ?>
				<div class="ow-pt-image">
					<?php $this->column_image( $column ); ?>
				</div>
			<?php } ?>

			<div class="ow-pt-features">
				<?php foreach ( $column['features'] as $i => $feature ) { ?>
					<div class="ow-pt-feature ow-pt-feature-<?php echo $i % 2 == 0 ? 'even' : 'odd'; ?> ow-pt-feature-index-<?php echo $i; ?>" title="<?php echo ! empty( $feature['hover'] ) ? esc_attr( $feature['hover'] ) : ''; ?>">

						<?php
						if ( ! empty( $feature['icon_new'] ) ) { ?>
							<div class="sow-feature-icon">
								<?php
								$icon_styles = array();

							if ( ! empty( $feature['icon_color'] ) ) {
								$icon_styles[] = 'color: ' . $feature['icon_color'];
							}
							echo siteorigin_widget_get_icon( $feature['icon_new'], $icon_styles );
							?>
							</div>
						<?php } ?>
						<div class="sow-feature-text">
							<p>
								<?php echo wp_kses_post( $feature['text'] ); ?>
							</p>
						</div>
					</div>
				<?php } ?>
			</div>

			<?php if ( ! empty( $column['button'] ) ) { ?>
				<div class="ow-pt-button">
					<a
						href="<?php echo sow_esc_url( $column['url'] ); ?>"
						<?php
						$button_attrs = apply_filters(
							'siteorigin_widgets_price_table_button_attributes',
							$initial_button_attrs,
							$column
						);

						foreach ( $button_attrs as $attr => $val ) {
							echo siteorigin_sanitize_attribute_key( $attr ) . '="' . esc_attr( $val ) . '" ';
						}
						?>
					><?php echo esc_html( $column['button'] ); ?></a>
				</div>
			<?php } ?>
		</div>
	<?php
	}

	global $siteorigin_price_table_icons;

	if ( empty( $siteorigin_price_table_icons ) ) {
		$siteorigin_price_table_icons = array();
	}

	foreach ( $columns as $i => $column ) {
		foreach ( $column['features'] as $feature ) {
			if ( ! empty( $feature['icon'] ) && empty( $siteorigin_price_table_icons[ $feature['icon'] ] ) ) {
				$siteorigin_price_table_icons[ $feature['icon'] ] = true;
				echo '<div style="display:none" id="so-pt-icon-' . esc_url( $feature['icon'] ) . '">';
				readfile( plugin_dir_path( __FILE__ ) . '../fontawesome/' . sanitize_file_name( $feature['icon'] ) . '.svg' );
				echo '</div>';
			}
		}
	}
	?>
</div>
