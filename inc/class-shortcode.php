<?php 

namespace SKYT;

use Delabon\WP\Helper;
use SKYT\Campaigns\Content_Form\Content_Form;

/**
 * Skytake shortcode.
 *
 * Skytake shortcode handler class is responsible for creating a shortcode for skytake content form campaign.
 * 
 */
class Shortcode{

    /**
     * Constructor
     */
    function __construct(){

        add_shortcode('skytake', [ $this, 'render' ]); 
    }

    /**
     * Renders the shortcode
     *
     * @param array $atts
     * @return void
     */
    function render( $atts ){

        $atts = shortcode_atts( array(
            'id' => 'none'
        ), $atts );

        if( $atts['id'] === 'none' ){
            return '';
        }

        try{

            $campaign = Campaign::get( $atts['id'] );
            $ui = new Content_Form( $campaign );
            $data = $ui->render();

            // tested wp_localize_script without success
            echo '<script>
                var skytake_settings_'.$campaign->id().' = '.json_encode( $data ).';
            </script>';

            return $data['campaign_markup'];
        }
        catch ( \Exception $e ) {
            return '';
        }
    }

}
