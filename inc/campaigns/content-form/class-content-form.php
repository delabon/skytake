<?php 

namespace SKYT\Campaigns\Content_Form;

use SKYT\Campaign_UI;

/**
 * Handles Inline Content Form rendering
 * For both frontend and preview modes
 */
class Content_Form extends Campaign_UI{

    /** @var Campaign */
    protected $campaign;

    /**
     * Constructor      
     *
     * @param Campaign $campaign
     */
    function __construct( $campaign ){
        $this->campaign = $campaign;
    }

    /**
     * Frontend
     *
     * @return array
     */
    function render(){

        $campaign = $this->campaign;

        if( ! $campaign ){
            throw new \Exception("No campaign");
        }
        
        return array_merge( array(
            'ajaxurl'                   => admin_url('admin-ajax.php'),            
            'nonce'                     => skytake()->ajax->create_nonce(),
            'campaign_id'               => $campaign->id(),
            'campaign_type'             => $campaign->type(),
            'campaign_hash'             => $campaign->id() .'_'. md5( get_home_url() ),
            'site_hash'                 => md5( get_home_url() ),
            'is_editor_mode'            => FALSE,
            'last_settings_update'      => $campaign->change_token(),
            'campaign_markup'           => $this->get_markup( $campaign, false ),
            'minimized_bar_markup'      => $this->get_minimized_bar_markup( $campaign ), 
            'display_trigger'           => $campaign->setting('display_trigger'),
            'display_trigger_after'     => $campaign->setting('display_after_x_seconds'),
            'display_minimized_bar'     => $campaign->setting('display_minimized_bar'),
            'popup_scroll_percentage'   => $campaign->setting('scrolling_down_percentage'),
            'reminder_number_notsub'    => $campaign->setting('reminder_number_notsub'),
            'reminder_type_notsub'      => $campaign->setting('reminder_type_notsub'),
            'reminder_number_sub'       => $campaign->setting('reminder_number_sub'),
        ), $this->urgency( $campaign ) );
    }

    /**
     * Preview (Editor)
     *
     * @param string|null $skin
     * @return array
     */
    function render_preview( $skin = null ){

        $campaign = $this->campaign;

        if( ! $campaign ){
            throw new \Exception("No campaign");
        }

        return array(
            'ajaxurl'               => admin_url('admin-ajax.php'),
            'nonce'                 => skytake()->ajax->create_nonce(),
            'campaign_id'           => $campaign->id(),
            'campaign_hash'         => $campaign->id() .'_'. md5( get_home_url() ),
            'is_editor_mode'        => TRUE,
            'campaign_markup'       => $this->get_markup( $campaign, TRUE, $skin ),
            'minimized_bar_markup'  => $this->get_minimized_bar_markup( $campaign ), 
            'urgency_type'          => $campaign->setting('urgency_type'),
        );
    }

}
