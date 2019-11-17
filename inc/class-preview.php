<?php 

namespace SKYT;

use Delabon\WP\Helper;
use SKYT\Campaigns\Popup\Popup;
use SKYT\Campaigns\Scroll_Box\Scroll_Box;
use SKYT\Campaigns\Widget_Form\Widget_Form;
use SKYT\Campaigns\Content_Form\Content_Form;
use SKYT\Campaigns\Floating_Bar\Floating_Bar;

class Preview{

    private $campaign;

    /**
     * Preview constructor
     */
    function __construct(){

        if( ! $this->is_preview_mode() ){
            return;
        }
              
        // fire it after everything is set
        add_action( 'admin_init', [ $this, 'init' ], PHP_INT_MAX );
    }

    /**
     * Init
     * 
     * Fires when 'wp_loaded' action called
     *
     * @param boolean $die
     * @return void
     */
    function init( $die = true ){

		add_filter( 'show_admin_bar', '__return_false' );
        
		// Remove all WordPress actions
		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_footer' );

		// Add back WP native actions that we need
		add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
		add_action( 'wp_head', 'wp_print_styles', 8 );
		add_action( 'wp_head', 'wp_print_head_scripts', 9 );
		add_action( 'wp_head', 'wp_site_icon' );

		add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );
		//add_action( 'wp_footer', 'wp_auth_check_html', 30 ); // no need for this in the preview

		// Also remove all scripts hooked into after_wp_tiny_mce.
		remove_all_actions( 'after_wp_tiny_mce' );

        // Remove all scripts and styles
        remove_all_actions( 'wp_enqueue_scripts' );
        
        // add ours
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 999999 );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 999999 );

        // Setup default heartbeat options
		add_filter( 'heartbeat_settings', function( $settings ) {
			$settings['interval'] = 15;
			return $settings;
        });
        
        $this->print_campaign_template();
        
        if( $die ) die;
    }

    /**
	 * Reset & Enqueue scripts.
	 */
    function enqueue_scripts(){
        
		global $wp_styles, $wp_scripts;

		// Reset global variables
		$wp_styles = new \WP_Styles(); // WPCS: override ok.
		$wp_scripts = new \WP_Scripts(); // WPCS: override ok.
    }

    /**
	 * Enqueue styles.
	 */
    function enqueue_styles(){

        wp_enqueue_style(
            'skytake-preview', 
            SKYTAKE_URL . '/assets/css/preview.css', 
            array('wp-auth-check'), 
            SKYTAKE_VERSION
        );

        wp_enqueue_style( 
            'skytake_front', 
            SKYTAKE_URL . '/assets/css/front.min.css', 
            array(),
            SKYTAKE_VERSION
        );

        wp_enqueue_style( 
            'skytake_front_skin', 
            SKYTAKE_URL . '/inc/skins/'.$this->campaign->setting('template').'/css/skin.min.css', 
            array('skytake_front'), 
            SKYTAKE_VERSION 
        );

        wp_add_inline_style( 'skytake_front_skin', $this->campaign->custom_css() );

        if( is_rtl() ){
            wp_enqueue_style( 
                'skytake_front_rtl', 
                SKYTAKE_URL . '/assets/css/front-rtl.css', 
                array('skytake_front'), 
                SKYTAKE_VERSION 
            );
        }

        $font = $this->campaign->setting('font_family');

        if( $font !== 'theme_font' ){
            wp_enqueue_style( 
                'skytake_front_fonts', 
                'https://fonts.googleapis.com/css?family='.skytake()->defaultConfig['fonts'][ $font ][0].'&display=swap', 
                false 
            ); 
        }
    }

    /**
     * Prints the campaign template
     */
    function print_campaign_template(){

        $output = '';

        if( $this->campaign->type() === 'popup' ){
            $campaign_ui = new Popup( $this->campaign );
        }
        else if( $this->campaign->type() === 'floating-bar' ){
            $campaign_ui = new Floating_Bar( $this->campaign );
        }
        else if( $this->campaign->type() === 'scroll-box' ){
            $campaign_ui = new Scroll_Box( $this->campaign );
        }
        else if( $this->campaign->type() === 'widget-form' ){
            $campaign_ui = new Widget_Form( $this->campaign );
        }
        else if( $this->campaign->type() === 'content-form' ){
            $campaign_ui = new Content_Form( $this->campaign );
        }

        $data = $campaign_ui->render_preview();
        $output = $data['campaign_markup'];

        if( isset( $data['minimized_bar_markup'] ) ){
            $output .= $data['minimized_bar_markup'];
        }

		include SKYTAKE_PATH . '/inc/views/preview/wrapper.php';
    }

    /**
     * Used to determine whether we are in the preview mode.
     * and to set the preview campaign
     *
     * @return boolean
     */
    function is_preview_mode(){

        if( ! isset( $_REQUEST['campaign_id'], $_REQUEST['action'] ) ) {
            return false;
        }

        if( $_REQUEST['action'] !== 'skytake_preview' ){
            return false;
        }

        $this->campaign = Campaign::get( (int)$_REQUEST['campaign_id'] );

        if( ! $this->campaign ){
            return false;
        }

        return true;
    }

}
