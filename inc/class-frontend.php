<?php 

namespace SKYT;

use Delabon\WP\Helper;

/**
 * Frontend class
 */
class Frontend{

    /**
     * Constructor
     */
    function __construct(){

        add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ), 8 );
    }

    /**
     * Enqueue assets if possible
     */
    function load_assets(){

        $campaigns = Campaign::active();

        if( ! $campaigns ){
            return false;
        }

        $campaigns_to_display = [];

        foreach( $campaigns as $campaign){

            // filters did not pass
            if( ! $this->filters( $campaign ) ){
                continue;
            }

            $campaigns_to_display[ $campaign->id() ] = $campaign;
        }

        $this->enqueue_scripts( array_keys( $campaigns_to_display ) );
        $this->enqueue_styles( $campaigns_to_display );
    }

    /**
     * Check all filters
     *
     * @return boolean
     */ 
    private function filters( $campaign ){
        
        if( is_user_logged_in() ){
            
            if( ! (bool)$campaign->setting('is_loggedin_enabled') ){
                return false;
            }

            if( current_user_can('manage_options') && ! (bool)$campaign->setting('is_administrators_enabled') ){
                return false;
            }        
        }
        
        if( (bool)$campaign->setting('filter_hide_on_articles') && is_single() ){
            return false;
        }

        $excluded = Helper::excludedListToArray( $campaign->setting('filter_pages_excluded') );

        if( $campaign->setting('filter_type') === 'show' ){
            foreach( $excluded as $url ){
                if( $url === skytake()->request->url() ){
                    return false;
                }
            }

            return true;
        }
        else{
            foreach( $excluded as $url ){
                if( $url === skytake()->request->url() ){
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Enqueue scripts
     *
     * @param array $ids
     * @return void
     */
    function enqueue_scripts( $ids ){

        wp_enqueue_script( 
            'skytake_front_countdown', 
            SKYTAKE_URL . '/assets/js/jquery.countdown.min.js', 
            array('jquery'), 
            SKYTAKE_VERSION, 
            true 
        );
        
        wp_enqueue_script( 
            'skytake_front', 
            SKYTAKE_URL . '/assets/js/front.min.js', 
            array('jquery', 'skytake_front_countdown'), 
            SKYTAKE_VERSION, 
            true 
        );

        $skytake_settings = array(
            'site_id'   => hash('crc32', get_home_url(), FALSE),
            'ajaxurl'   => admin_url('admin-ajax.php'),
            'nonce'     => wp_create_nonce('skytake'),
            'ids'       => $ids,
        );

        wp_localize_script( 'skytake_front', 'SKYT_PARAMS', $skytake_settings );
    }

    /**
     * Enqueue styles
     *
     * @param array $campaigns
     * @return void
     */
    function enqueue_styles( $campaigns ){

        // CSS
        wp_enqueue_style( 
            'skytake_front', 
            SKYTAKE_URL . '/assets/css/front.min.css', 
            array(), 
            SKYTAKE_VERSION 
        );

        $fonts = [];
        $custom_css = '';

        foreach( $campaigns as $campaign ){
        
            wp_enqueue_style( 
                'skytake_tmpl_' . $campaign->setting('template'), 
                SKYTAKE_URL . '/inc/skins/'.$campaign->setting('template').'/css/skin.min.css', 
                array('skytake_front'), 
                SKYTAKE_VERSION 
            );

            $font = $campaign->setting('font_family');

            if( $font !== 'theme_font' ){
                $fonts[ $font ] = skytake()->defaultConfig['fonts'][ $font ][ 0 ];
            }

            $custom_css .= $campaign->custom_css() . PHP_EOL;
        }
        
        wp_add_inline_style( 'skytake_front', $custom_css );

        if( is_rtl() ){
            wp_enqueue_style( 
                'skytake_front_rtl', 
                SKYTAKE_URL . '/assets/css/front-rtl.css', 
                array('skytake_front'), 
                SKYTAKE_VERSION 
            );
        }

        wp_enqueue_style( 
            'skytake-front-fonts', 
            'https://fonts.googleapis.com/css?family='.implode("|", $fonts).'&display=swap', 
            false 
        ); 
    }

}
