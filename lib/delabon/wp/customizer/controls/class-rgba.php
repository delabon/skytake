<?php

namespace Delabon\WP\Customizer\Controls;

if( ! class_exists( RGBA::class ) ){
    return;
}

/**
 * Alpha Color Picker Customizer Control
 *
 * This control adds a second slider for opacity to the stock WordPress color picker,
 * and it includes logic to seamlessly convert between RGBa and Hex color values as
 * opacity is added to or removed from a color.
 *
 * This Alpha Color Picker is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this Alpha Color Picker. If not, see <http://www.gnu.org/licenses/>.
 */
class RGBA extends \WP_Customize_Control {

	/**
	 * Official control name.
	 */
	public $type = 'dlb-alpha-color';

	/**
	 * Add support for palettes to be passed in.
	 *
	 * Supported palette values are true, false, or an array of RGBa and Hex colors.
	 */
	public $palette;

	/**
	 * Add support for showing the opacity value on the slider handle.
	 */
	public $show_opacity;

	public $caller_plugin_url;

	/**
	 * Constructor Override
	 *
     * @param WP_Customize_Manager $manager Customizer bootstrap instance.
     * @param string $id      Control ID.
     * @param array $args
	 * @param string $plugin_url
	 */
	function __construct( $manager, $id, $args = array(), $plugin_url ){

		parent::__construct( $manager, $id, $args );
				
		$this->caller_plugin_url = $plugin_url;
	}

	/**
	 * Render the control.
	 */
	public function render_content() {

		// Process the palette
		if ( is_array( $this->palette ) ) {
			$palette = implode( '|', $this->palette );
		} 
		else {
			// Default to true.
			$palette = ( false === $this->palette || 'false' === $this->palette ) ? 'false' : 'true';
		}

		// Support passing show_opacity as string or boolean. Default to true.
		$show_opacity = ( false === $this->show_opacity || 'false' === $this->show_opacity ) ? 'false' : 'true';

		// Begin the output. 
		?>
			<label>
				<?php // Output the label and description if they were passed in.
				if ( isset( $this->label ) && '' !== $this->label ) {
					echo '<span class="customize-control-title">' . sanitize_text_field( $this->label ) . '</span>';
				}
				if ( isset( $this->description ) && '' !== $this->description ) {
					echo '<span class="description customize-control-description">' . sanitize_text_field( $this->description ) . '</span>';
				} ?>
			</label>

			<div class="customize-control-content">

				<input class="dlb-alpha-color-control" type="text" data-show-opacity="<?php echo $show_opacity; ?>" data-palette="<?php echo esc_attr( $palette ); ?>" data-default-color="<?php echo esc_attr( $this->settings['default']->default ); ?>" <?php $this->link(); ?>  />
				
			</div>
		<?php
	}
}