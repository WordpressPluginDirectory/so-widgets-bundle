<?php

/**
 * A full instance of SiteOrigin Page Builder as a field.
 *
 * Class SiteOrigin_Widget_Field_Builder
 */
class SiteOrigin_Widget_Field_Builder extends SiteOrigin_Widget_Field_Base {
	protected function render_field( $value, $instance ) {
		if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
			// Normal rendering code
			// In some contexts this is already encoded, e.g. accordion widget using a layout field for content,
			// inside a PB block in the block editor.
			$valid_string = is_string( $value ); // Required for PHP <5.4

			if ( empty( $valid_string ) ) {
				$value = json_encode( $value );
			}
			?>
			<div
				class="siteorigin-page-builder-field"
				data-mode="dialog"
				data-type="<?php echo isset( $this->field_options['builder_type'] ) ? esc_attr( $this->field_options['builder_type'] ) : 'sow-builder-field'; ?>"
				>
				<p>
					<button class="button-secondary siteorigin-panels-display-builder">
						<?php esc_html_e( 'Open Builder', 'so-widgets-bundle' ); ?>
					</button>
				</p>
				<input
					type="hidden"
					class="siteorigin-widget-input panels-data"
					value="<?php echo sow_esc_attr( $value, ENT_QUOTES, false, true ); ?>"
					name="<?php echo esc_attr( $this->element_name ); ?>"
					id="<?php echo esc_attr( $this->element_id ); ?>"
					/>
			</div>
			<?php
		} else {
			// Let the user know that they need Page Builder installed
			?>
			<p>
				<?php _e( 'This field requires: ', 'so-widgets-bundle' ); ?>
				<a href="https://siteorigin.com/page-builder/" target="_blank" rel="noopener noreferrer"><?php _e( 'SiteOrigin Page Builder', 'so-widgets-bundle' ); ?></a>
			</p>
			<?php
		}
	}

	/**
	 * Process the panels_data
	 *
	 * @param mixed $value
	 * @param array $instance
	 *
	 * @return array|mixed|object
	 */
	protected function sanitize_field_input( $value, $instance ) {
		if ( empty( $value ) ) {
			return array();
		}

		if ( is_string( $value ) ) {
			$value = json_decode( $value, true );
		}

		if ( function_exists( 'siteorigin_panels_process_raw_widgets' ) && ! empty( $value['widgets'] ) && is_array( $value['widgets'] ) ) {
			$value['widgets'] = siteorigin_panels_process_raw_widgets( $value['widgets'] );
		}

		// Add record of widget being inside of a builder field.
		if ( ! empty( $value['widgets'] ) ) {
			foreach ( $value['widgets'] as $widget_id => $widget ) {
				$value['widgets'][ $widget_id ]['panels_info']['builder'] = true;
			}
		}

		return $value;
	}
}
