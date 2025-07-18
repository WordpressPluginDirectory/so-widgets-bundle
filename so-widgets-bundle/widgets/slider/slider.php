<?php
/*
Widget Name: Image Slider
Description: Create a responsive slider with customizable image and video frames, navigation, and appearance settings.
Author: SiteOrigin
Author URI: https://siteorigin.com
Documentation: https://siteorigin.com/widgets-bundle/slider-widget-documentation/
Keywords: gallery, image, video
*/

if ( ! class_exists( 'SiteOrigin_Widget_Base_Slider' ) ) {
	include_once plugin_dir_path( SOW_BUNDLE_BASE_FILE ) . '/base/inc/widgets/base-slider.class.php';
}

class SiteOrigin_Widget_Slider_Widget extends SiteOrigin_Widget_Base_Slider {
	public function __construct() {
		parent::__construct(
			'sow-slider',
			__( 'SiteOrigin Slider', 'so-widgets-bundle' ),
			array(
				'description' => __( 'Create a responsive slider with customizable image and video frames, navigation, and appearance settings.', 'so-widgets-bundle' ),
				'help' => 'https://siteorigin.com/widgets-bundle/slider-widget-documentation/',
				'panels_title' => false,
			),
			array(
			),
			false,
			plugin_dir_path( __FILE__ )
		);
	}

	public function get_widget_form() {
		$units = siteorigin_widgets_get_measurements_list();
		unset( $units[1] ); // Remove %;

		return parent::widget_form( array(
			'frames' => array(
				'type' => 'repeater',
				'label' => __( 'Slider frames', 'so-widgets-bundle' ),
				'item_name' => __( 'Frame', 'so-widgets-bundle' ),
				'item_label' => array(
					'selectorArray' => array(
						array(
							'selector' => '.siteorigin-widget-field-background_image .media-field-wrapper .current .title',
							'valueMethod' => 'html',
						),
						array(
							'selector' => '.siteorigin-widget-field-background_videos .siteorigin-widget-field-repeater-items .media-field-wrapper .current .title',
							'valueMethod' => 'html',
						),
						array(
							'selector' => ".siteorigin-widget-field-background_videos [id*='url']",
							'update_event' => 'change',
							'value_method' => 'val',
						),
						array(
							'selector' => '.siteorigin-widget-field-foreground_image .media-field-wrapper .current .title',
							'valueMethod' => 'html',
						),
					),
				),
				'fields' => array(
					'background_videos' => array(
						'type' => 'repeater',
						'item_name' => __( 'Video', 'so-widgets-bundle' ),
						'label' => __( 'Background videos', 'so-widgets-bundle' ),
						'item_label' => array(
							'selectorArray' => array(
								array(
									'selector' => '.siteorigin-widget-field-file .media-field-wrapper .current .title',
									'valueMethod' => 'html',
								),
								array(
									'selector' => "[id*='url']",
									'update_event' => 'change',
									'value_method' => 'val',
								),
							),
						),
						'fields' => $this->video_form_fields(),
					),

					'background_image' => array(
						'type' => 'media',
						'library' => 'image',
						'label' => __( 'Background image', 'so-widgets-bundle' ),
						'fallback' => true,
					),

					'background_color' => array(
						'type' => 'color',
						'label' => __( 'Background color', 'so-widgets-bundle' ),
					),

					'background_image_type' => array(
						'type' => 'select',
						'label' => __( 'Background image type', 'so-widgets-bundle' ),
						'options' => array(
							'cover' => __( 'Cover', 'so-widgets-bundle' ),
							'tile' => __( 'Tile', 'so-widgets-bundle' ),
						),
						'default' => 'cover',
					),

					'foreground_image' => array(
						'type' => 'media',
						'library' => 'image',
						'label' => __( 'Foreground image', 'so-widgets-bundle' ),
						'fallback' => true,
					),

					'url' => array(
						'type' => 'link',
						'label' => __( 'Destination URL', 'so-widgets-bundle' ),
					),

					'new_window' => array(
						'type' => 'checkbox',
						'label' => __( 'Open in new window', 'so-widgets-bundle' ),
						'default' => false,
					),
				),
			),
			'controls' => array(
				'type' => 'section',
				'label' => __( 'Controls', 'so-widgets-bundle' ),
				'fields' => $this->control_form_fields(),
			),

			'design' => array(
				'type' => 'section',
				'label' => __( 'Design', 'so-widgets-bundle' ),
				'fields' => array(
					'height' => array(
						'type' => 'measurement',
						'label' => __( 'Height', 'so-widgets-bundle' ),
						'units' => $units,
					),

					'height_responsive' => array(
						'type' => 'measurement',
						'label' => __( 'Responsive Height', 'so-widgets-bundle' ),
						'units' => $units,
					),
				),
			),
		) );
	}

	public function get_frame_background( $i, $frame ) {
		if ( ! empty( $frame['foreground_image'] ) ) {
			$background_image = siteorigin_widgets_get_attachment_image_src(
				$frame['background_image'],
				'full',
				! empty( $frame['background_image_fallback'] ) ? $frame['background_image_fallback'] : ''
			);
		}

		return array(
			'color' => ! empty( $frame['background_color'] ) ? $frame['background_color'] : false,
			'image' => ! empty( $background_image ) ? $background_image[0] : false,
			'image-width' => ! empty( $background_image[1] ) ? $background_image[1] : 0,
			'image-height' => ! empty( $background_image[2] ) ? $background_image[2] : 0,
			'image-sizing' => $frame['background_image_type'],
			'opacity' => 1,
			'videos' => $frame['background_videos'],
			'video-sizing' => empty( $frame['foreground_image'] ) ? 'full' : 'background',
			'url' => ! empty( $frame['url'] ) ? $frame['url'] : false,
			'new_window' => ! empty( $frame['new_window'] ) ? $frame['new_window'] : false,
		);
	}

	public function render_frame_contents( $i, $frame ) {
		// Clear out any empty background videos.
		if ( ! empty( $frame['background_videos'] ) && is_array( $frame['background_videos'] ) ) {
			for ( $i = 0; $i < count( $frame['background_videos'] ); $i++ ) {
				if ( empty( $frame['background_videos'][$i]['file'] ) && empty( $frame['background_videos'][$i]['url'] ) ) {
					unset( $frame['background_videos'][$i] );
				}
			}
		}

		$foreground_src = siteorigin_widgets_get_attachment_image_src(
			$frame['foreground_image'],
			'full',
			! empty( $frame['foreground_image_fallback'] ) ? $frame['foreground_image_fallback'] : ''
		);

		if ( ! empty( $foreground_src ) ) {
			// If a custom height is set, build the foreground style attribute.
			if ( ! empty( $frame['custom_height'] ) ) {
				$foreground_style_attr = 'height: ' . (int) $frame['custom_height'] . 'px; width: auto;';

				if ( ! empty( $foreground_src[2] ) ) {
					$foreground_style_attr .= 'max-height: ' . (int) $foreground_src[2] . 'px';
				}
			}
			?>
			<div class="sow-slider-image-container">
				<div class="sow-slider-image-wrapper" style="<?php if ( ! empty( $foreground_src[1] ) ) {
					echo 'max-width: ' . (int) $foreground_src[1] . 'px';
				} ?>">
					<?php if ( ! empty( $frame['url'] ) ) { ?>
						<a href="<?php echo sow_esc_url( $frame['url'] ); ?>"
						<?php foreach ( $frame['link_attributes'] as $att => $val ) { ?>
							<?php if ( ! empty( $val ) ) { ?>
								<?php echo siteorigin_sanitize_attribute_key( $att ) . '="' . esc_attr( $val ) . '" '; ?>
							<?php } ?>
						<?php } ?>>
					<?php } ?>
					<div class="sow-slider-image-foreground-wrapper">
						<?php
						echo siteorigin_widgets_get_attachment_image(
							$frame['foreground_image'],
							'full',
							! empty( $frame['foreground_image_fallback'] ) ? $frame['foreground_image_fallback'] : '',
							siteorigin_loading_optimization_attributes(
								apply_filters(
									'siteorigin_widgets_slider_attr',
									array(
										'class' => 'sow-slider-foreground-image',
										'style' => ! empty( $foreground_style_attr ) ? $foreground_style_attr : '',
									)
								),
								'sliders',
								new stdClass(),
								$this
							)
						);
						?>
					</div>
					<?php if ( ! empty( $frame['url'] ) ) { ?>
						</a>
					<?php } ?>
				</div>
			</div>
			<?php
		} elseif ( empty( $frame['background_videos'] ) ) {
			?>
			<?php if ( ! empty( $frame['url'] ) ) { ?>
				<a href="<?php echo sow_esc_url( $frame['url'] ); ?>"
				<?php foreach ( $frame['link_attributes'] as $att => $val ) { ?>
					<?php if ( ! empty( $val ) ) { ?>
						<?php echo siteorigin_sanitize_attribute_key( $att ) . '="' . esc_attr( $val ) . '" '; ?>
					<?php } ?>
				<?php } ?>>
			<?php
			}
			// Lets use the background image.
			echo siteorigin_widgets_get_attachment_image(
				$frame['background_image'],
				'full',
				! empty( $frame['background_image_fallback'] ) ? $frame['background_image_fallback'] : '',
				siteorigin_loading_optimization_attributes(
					apply_filters(
						'siteorigin_widgets_slider_attr',
						array(
							'class' => 'sow-slider-background-image',
							'style' => ! empty( $frame['custom_height'] ) ? 'height: ' . intval( $frame['custom_height'] ) . 'px; width: auto; margin: 0 auto;' : '',
						)
					),
					'sliders',
					new stdClass(),
					$this
				)
			);

			if ( ! empty( $frame['url'] ) ) {
				echo '</a>';
			}
		}
	}

	public function get_template_variables( $instance, $args ) {
		$frames = empty( $instance['frames'] ) ? array() : $instance['frames'];

		if ( ! empty( $frames ) ) {
			foreach ( $frames as &$frame ) {
				$link_atts = array();

				if ( ! empty( $frame['new_window'] ) ) {
					$link_atts['target'] = '_blank';
					$link_atts['rel'] = 'noopener noreferrer';
				}
				$frame['link_attributes'] = $link_atts;

				$frame['custom_height'] = ! empty( $instance['design']['height'] ) ? $instance['design']['height'] : 0;

				$frame['custom_height'] = ! empty( $instance['design']['height'] ) ? $instance['design']['height'] : 0;
				if ( ! empty( $frame['custom_height'] ) && empty( $frame['foreground_image'] )) {
					$frame['no_output'] = true;
				}
			}
		}

		return array(
			'controls' => $instance['controls'],
			'frames' => $frames,
		);
	}

	/**
	 * The less variables to control the design of the slider.
	 *
	 * @return array
	 */
	public function get_less_variables( $instance ) {
		$less = array();

		if ( ! empty( $instance['controls']['nav_color_hex'] ) ) {
			$less['nav_color_hex'] = $instance['controls']['nav_color_hex'];
		}

		if ( ! empty( $instance['controls']['nav_size'] ) ) {
			$less['nav_size'] = $instance['controls']['nav_size'];
		}

		$less['nav_align'] = ! empty( $instance['controls']['nav_align'] ) ? $instance['controls']['nav_align'] : 'right';
		$less['slide_height'] = ! empty( $instance['design']['height'] ) ? $instance['design']['height'] : false;
		$less['slide_height_responsive'] = ! empty( $instance['design']['height_responsive'] ) ? $instance['design']['height_responsive'] : false;

		$global_settings = $this->get_global_settings();

		if ( ! empty( $global_settings['responsive_breakpoint'] ) ) {
			$less['responsive_breakpoint'] = $global_settings['responsive_breakpoint'];
		}

		return $less;
	}

	/**
	 * Change the instance to the new one we're using for sliders
	 *
	 * @return mixed
	 */
	public function modify_instance( $instance ) {
		if ( empty( $instance['controls'] ) ) {
			if ( ! empty( $instance['speed'] ) ) {
				$instance['controls']['speed'] = $instance['speed'];
				unset( $instance['speed'] );
			}

			if ( ! empty( $instance['timeout'] ) ) {
				$instance['controls']['timeout'] = $instance['timeout'];
				unset( $instance['timeout'] );
			}

			if ( ! empty( $instance['nav_color_hex'] ) ) {
				$instance['controls']['nav_color_hex'] = $instance['nav_color_hex'];
				unset( $instance['nav_color_hex'] );
			}

			if ( ! empty( $instance['nav_style'] ) ) {
				$instance['controls']['nav_style'] = $instance['nav_style'];
				unset( $instance['nav_style'] );
			}

			if ( ! empty( $instance['nav_size'] ) ) {
				$instance['controls']['nav_size'] = $instance['nav_size'];
				unset( $instance['nav_size'] );
			}
		}

		return parent::modify_instance( $instance );
	}

	public function get_form_teaser() {
		if ( class_exists( 'SiteOrigin_Premium' ) ) {
			return false;
		}

		return array(
			sprintf(
				__( 'Add a Lightbox to your images with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/lightbox" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
			sprintf(
				__( 'Add a beautiful and customizable text overlay with animations to your images with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/image-overlay" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
			sprintf(
				__( 'Add multiple Slider frames in one go with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/multiple-media" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
			sprintf(
				__( 'Add parallax and fixed background images with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
				'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/parallax-sliders" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
		);
	}
}
siteorigin_widget_register( 'sow-slider', __FILE__, 'SiteOrigin_Widget_Slider_Widget' );
