<?php 

namespace Delabon\WP\Customizer\Controls;

if( ! class_exists( Radio_Images::class ) ){
    return;
}

class Radio_Images extends \WP_Customize_Control {

    /**
	 * Official control name.
	 */
    public $type = 'dlb_radio_images';
        
    /**
     * Render the control in the customizer
     */
    public function render_content() {
        
        $class = $this->id;
        $class = str_replace( '[', '-', $class );
        $class = str_replace( ']', '', $class );

    ?>
        <div class="customize-control-content">

            <?php if( !empty( $this->label ) ) { ?>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php } ?>
            
            <?php if( !empty( $this->description ) ) { ?>
                <span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php } ?>

            <div class="dlb-radio-images-container <?php esc_attr_e( $class ); ?>">

                <?php foreach ( $this->choices as $key => $value ) { ?>
                    <label>
                        <input type="radio" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php $this->link(); ?> <?php checked( esc_attr( $key ), $this->value() ); ?>/>
                        <img src="<?php echo esc_attr( $value['image'] ); ?>" alt="<?php echo esc_attr( $value['name'] ); ?>" title="<?php echo esc_attr( $value['name'] ); ?>" />
                    </label>
                <?php	} ?>

            </div>
            
        </div>
    <?php
    }

}
