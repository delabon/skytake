<?php 

namespace SKYT;

use Delabon\WP\Helper;

class Editor{

    private $campaign;

    /**
     * Editor constructor
     */
    function __construct(){

        if( ! $this->is_edit_mode() ){
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
		add_action( 'wp_footer', 'wp_auth_check_html', 30 );

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
        
        $this->print_editor_template();
        
        if( $die ) die;
    }

    /**
	 * Reset & Enqueue scripts.
	 */
    function enqueue_scripts(){
        
		global $wp_styles, $wp_scripts;

		// Reset global variable
		$wp_styles = new \WP_Styles(); // WPCS: override ok.
		$wp_scripts = new \WP_Scripts(); // WPCS: override ok.

        skytake()->backend->enqueue_scripts('skytake');

        wp_enqueue_script(
            'skytake-teditor',
            SKYTAKE_URL . '/assets/js/teditor.min.js', 
            array( 'jquery', 'wp-auth-check', 'heartbeat'), 
            SKYTAKE_VERSION, 
            true
        );

        wp_enqueue_script(
            'skytake-teditor-preview',
            SKYTAKE_URL . '/assets/js/teditor-'.$this->campaign->type().'.js', 
            array( 'skytake-teditor' ), 
            SKYTAKE_VERSION, 
            true
        );
        
        wp_localize_script( 'skytake-teditor', 'teditor_params', [
            'fonts' => skytake()->defaultConfig['fonts'],
            'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
            'skins_url' => SKYTAKE_URL . '/inc/skins/',
            'campaign_type' => $this->campaign->type(),
            'is_premium' => is_premium(),
        ]);
    }

    /**
	 * Enqueue styles.
	 */
    function enqueue_styles(){

        skytake()->backend->enqueue_styles('skytake');
        
        wp_enqueue_style('wp-auth-check');

        wp_enqueue_style( 
            'skytake-editor', 
            SKYTAKE_URL . '/assets/css/teditor.min.css', 
            array(), 
            SKYTAKE_VERSION
        );  

        wp_enqueue_style( 
            'skytake-editor-social-icons', 
            SKYTAKE_URL . '/assets/css/social-media.css', 
            array(), 
            SKYTAKE_VERSION
        );  
        
        wp_enqueue_style( 
            'skytake-editor-gift-icons', 
            SKYTAKE_URL . '/assets/css/gift-boxes.css', 
            array(), 
            SKYTAKE_VERSION
        );

        wp_enqueue_style( 
            'skytake-editor-font', 
            'https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap', 
            false 
        ); 
    }

	/**
	 * Print Editor Template.
	 */
	function print_editor_template() {

        $campaign = Campaign::get( (int)$_GET['campaign_id'] );

        if( ! $campaign ){
            die( __('Invalid Campaign', 'skytake') );
        }
        
        $panel = require_once SKYTAKE_PATH . '/inc/campaigns/'.$campaign->type().'/panel.php';
		include SKYTAKE_PATH . '/inc/views/editor/wrapper.php';
	}

    /**
     * Used to determine whether we are in the edit mode.
     * and to set the editor campaign
     * 
     * @return boolean
     */
    function is_edit_mode(){

        if( ! isset( $_REQUEST['campaign_id'], $_REQUEST['action'] ) ) {
            return false;
        }

        if( $_REQUEST['action'] !== 'skytake_editor' ){
            return false;
        }

        $this->campaign = Campaign::get( (int)$_REQUEST['campaign_id'] );

        if( ! $this->campaign ){
            return false;
        }

        return true;
    }

}
