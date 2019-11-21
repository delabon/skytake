<?php

namespace Delabon\WP\Panel;

use Delabon\WP\Helper;

if( ! class_exists( Panel::class ) ){
    return;
}

class Panel{

    protected $args;
    protected $slug;
    protected $version;
    protected $nonce;
    private $tabs_path; 
    private $panel_url;
    protected $tabs = array();

    function __construct( $args ){
        
        $this->args = wp_parse_args($args, array(
            'slug' => 'delabon',
            'panel_url' => '',
            'tabs_path' => __DIR__ . '/views',
            'discover_path' => null,
        ));

        $this->l10n = array(
            'documentation' => 'Documentation',
            'version' => 'Version',
            'saved_settings_message' => 'Settings Are Saved.'
        );

        if( isset( $this->args['l10n'] ) ){
            $this->l10n = wp_parse_args( $this->args['l10n'], $this->l10n );
        }

        $this->slug = $this->args['slug'];
        $this->tabs_path = $this->args['tabs_path'];
        $this->nonce = $this->slug;
        $this->panel_url = $this->args['panel_url'];
        $this->discover_path = $this->args['discover_path'];
    }

    /**
     * Enqueue scripts.
     *
     * @param string $slug
     * @param string $assets_url
     * @param string $version
     * @return void
     */
	static function enqueue_scripts( $slug, $assets_url, $version ) {
         
        wp_enqueue_media();

        wp_enqueue_script( 
            $slug . '-admin-panel-colorpicker-rgba', 
            $assets_url . '/delabon/wp/assets/js/color-picker-rgba.js', 
            array('jquery', 'wp-color-picker'), 
            $version, 
            true 
        );

        wp_enqueue_script( 
            $slug . '-admin-panel', 
            $assets_url . '/delabon/wp/assets/js/admin.js', 
            array('jquery', 'wp-color-picker', 'jquery-ui-datepicker', $slug . '-admin-panel-colorpicker-rgba' ), 
            $version, 
            true 
        );

    }

    /**
     * Enqueue styles.
     *
     * @param string $slug
     * @param string $assets_url
     * @param string $version
     * @return void
     */
	static function enqueue_styles( $slug, $assets_url, $version ) {
         
        wp_enqueue_style( 'wp-color-picker' );

        wp_enqueue_style( 
            $slug . '-admin-panel-jquery-ui', 
            $assets_url . '/delabon/wp/assets/css/jquery-ui.min.css', 
            array(), 
            $version 
        );

        wp_enqueue_style( 
            $slug . '-admin-panel', 
            $assets_url . '/delabon/wp/assets/css/admin.css', 
            array(), 
            $version 
        );
    
        if( is_rtl() ){
            wp_enqueue_style( 
                $slug . '-admin-panel-rtl', 
                $assets_url . '/delabon/wp/assets/css/admin-rtl.css', 
                array(), 
                $version 
            );
        }

    }
    
    /**
     * Add Admin Menu Item
     */
    function add_admin_menu() {            
        add_menu_page( 
            $this->title, 
            $this->title, 
            $this->permission, 
            $this->slug, 
            array( $this, 'render' ), 
            $this->icon, 
            $this->position  
        );
    }
    
    function render(){

        ?> <div class="wrap"> <?php

        require_once __DIR__ . '/views/panel.php';
        
        ?> </div> <?php 
    }
    
    /**
     * Save settings
     */
    function save_settings(){

        if( ! isset($_POST['dlb_nonce']) ) return;
        if( ! current_user_can( $this->permission ) ) return;
        if( ! wp_verify_nonce( $_POST['dlb_nonce'], $this->nonce ) ) return;

        add_action( 'admin_notices', array( $this, 'admin_notice_save_message' ), 0 );

        do_action( $this->slug . '_before_saving_settings', $_POST );

        foreach ( $_POST as $key => $value ) {
            update_option( sanitize_text_field($key), $value );
        }
        
        do_action( $this->slug . '_after_saving_settings', $_POST );
    }

    function admin_notice_save_message(){
        ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo $this->l10n['saved_settings_message'] ?></p>
            </div>
        <?php
    }

    /**
     * Add panel tab
     *
     * @return void
     */
    function add_tab( $title, $slug ){

        $this->tabs[] = array(
            'title' => $title,
            'slug' => $slug,
        );

    }

    /**
     * Find tab
     *
     * @param string $slug
     * @return boolean
     */
    function find_tab( $slug ){

        foreach ( $this->tabs as $tab ) {
            if( $tab['slug'] === $slug ) return true;
        }

        return false;
    }

}
