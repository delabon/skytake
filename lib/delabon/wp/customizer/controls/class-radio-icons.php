<?php 

namespace Delabon\WP\Customizer\Controls;

if( ! class_exists( Radio_Icons::class ) ){
    return;
}

class Radio_Icons extends \WP_Customize_Control {

    /**
	 * Official control name.
	 */
    public $type = 'dlb_radio_icons';

    /**
	 * Render the control.
	 */
    public function render_content() {
        ?>
            <div class="customize-control-content">
                
                <?php
                    if ( ! empty( $this->label ) ) {
                        ?>
                            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                        <?php
                    }
                ?>

                <?php
                    if ( ! empty( $this->description ) ) {
                        ?>
                            <span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                        <?php
                    }
                    $class = $this->id;
                    $class = str_replace( '[', '-', $class );
                    $class = str_replace( ']', '', $class );
                ?>

                <div class="dlb-radio-icons-container <?php esc_attr_e( $class ); ?>">
                    <?php
                        foreach ( $this->choices as $key => $value ) {
                            ?>
                                <label class="<?php if ( $key === $this->value() ) echo '__active'; ?>">

                                    <input type="radio" style="display: none;" name="<?php esc_attr_e( $this->id ); ?>"
                                        value="<?php esc_attr_e( $key ); ?>" <?php $this->link(); ?> <?php checked( esc_attr( $key ), $this->value() ); ?>/>
                                    
                                    <span class="<?php esc_attr_e( $key ); ?>"></span>

                                </label>
                            <?php
                        }
                    ?>
                </div>

            </div>
        <?php
    }

}
