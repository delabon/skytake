<?php

namespace SKYT;

require_once __DIR__ . '/functions.php';

use Delabon\WP\Request;

class Plugin{

    private static $_instance;

    /**
     * Request Instance
     *
     * @var Request
     */
    public $request;

    /**
     * Default settings
     *
     * @var array
     */
    public $defaultConfig;


    /**
     * Create plugin instance
     */
    static function instance(){

        if( self::$_instance === null ){
            self::$_instance = new Plugin;
        }

        return self::$_instance;
    }

    /**
     * Init
     */
    private function __construct(){

        $this->icons = require_once __DIR__ . '/icons.php';
        $this->defaultConfig = require_once __DIR__ . '/config.php';

        $this->request = new Request;
        $this->ajax = new Ajax;
        $this->backend = new Backend;
        $this->frontend = new Frontend;
        $this->preview = new Preview;
        $this->editor = new Editor;
        new Widget;
        new Gutenberg;
        new Shortcode;

        add_action( 'plugins_loaded', array( $this, 'load_translation') );
        register_activation_hook( str_replace( 'inc/class-plugin.php', 'index.php', __FILE__ ), array( $this, 'activation_hook' ) );
        add_action( 'admin_init', array( $this, 'redirect_after_activation') );
    }
    
    /**
     * Plugin Activation
     */
    function activation_hook() {

        add_option( 'skytake_do_activation_redirect', true );
    
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
        global $wpdb;
    
        $charset_collate = $wpdb->get_charset_collate();    
        $table = $wpdb->prefix . 'skytake_emails';
        
        if( $wpdb->get_var( "show tables like '{$table}'" ) != $table ) {
    
            $sql = "CREATE TABLE $table (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                coupon_id bigint(20) NOT NULL,
                coupon_code VARCHAR(250) NOT NULL,
                campaign_id bigint(20) NOT NULL,
                email  VARCHAR(250) NOT NULL,
                name  VARCHAR(250) NOT NULL,
                mobile  VARCHAR(250) NOT NULL,
                date_added DATETIME NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
                
            dbDelta( $sql );
        }

        $table = $wpdb->prefix . 'skytake_campaigns';
        
        if( $wpdb->get_var( "show tables like '{$table}'" ) != $table ) {
    
            $sql = "CREATE TABLE $table (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                name VARCHAR(250) NOT NULL,
                c_type VARCHAR(50) NOT NULL, 
                c_status int(1) NOT NULL,
                c_statistics TEXT NOT NULL,
                c_settings TEXT NOT NULL,
                custom_css TEXT NOT NULL,
                change_token VARCHAR(250) NOT NULL, 
                date_created DATETIME NOT NULL,
                date_updated DATETIME NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
                
            dbDelta( $sql );
        }
    
    }

    /**
     * Redirect to the admin page on single plugin activation
     */
    function redirect_after_activation() {
        
        if ( get_option( 'skytake_do_activation_redirect', false ) ) {

            delete_option( 'skytake_do_activation_redirect' );
        
            if( ! isset( $_GET['activate-multi'] ) ) {

                wp_redirect( "admin.php?page=skytake" );
            
            }
        }

    }

    /**
     * Load Plugin Text Domain
     */
    function load_translation() {
        // wp-content/plugins/plugin-name/languages/textdomain-de_DE.mo
        load_plugin_textdomain( 'skytake', FALSE,  'skytake/languages/' );
    }

}
