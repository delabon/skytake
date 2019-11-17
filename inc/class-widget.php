<?php 

namespace SKYT;

use SKYT\Campaigns\Widget_Form\Widget_Form;

/**
 * Adds widget.
 */
class Widget extends \WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		parent::__construct(
			'skytake_widget_output', // Base ID
			esc_html__( 'Skytake Widget', 'skytake' ), // Name
			array( 'description' => esc_html__( 'Skytake Widget Form', 'skytake' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

        $campaign = Campaign::get( (int)$instance['campaign_selected'] );
        
        if( ! $campaign ){
			return;
		}

		echo $args['before_widget'];
		echo '<div class="skytake-placeholders" data-id="'.$campaign->id().'"></div>';
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

        $list = widget_form_campaign_list();
        $campaign_selected = ! empty( $instance['campaign_selected'] ) ? $instance['campaign_selected'] : 'none';

        ?>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'campaign_selected' ) ); ?>"><?php esc_attr_e( 'Select Campaign:', 'skytake' ); ?></label>

                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'campaign_selected' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'campaign_selected' ) ); ?>">
                    <?php foreach ($list as $id => $name ): ?>
                        <option value="<?php echo esc_attr($id); ?>" <?php if( $campaign_selected == $id ) echo 'selected="selected"'; ?> ><?php echo esc_html($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
    
        <?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['campaign_selected'] = ( ! empty( $new_instance['campaign_selected'] ) ) ? sanitize_text_field( $new_instance['campaign_selected'] ) : 'none';

		return $instance;
	}

}

// Register
add_action( 'widgets_init', function(){
    register_widget( 'SKYT\Widget' );
});

