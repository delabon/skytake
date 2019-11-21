<?php 

namespace SKYT;

use Delabon\WP\Panel\Panel;
use Delabon\WP\CSV;
use Delabon\WP\HTML;

/**
 * Backend Class
 */
class Backend{

    /**
     * Constructor
     */
    function __construct(){
                
        if( ! is_admin() ){
            return;
        }

        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts') );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles') );

        add_action( 'init', array( $this, 'hide_notices') );
        add_action( 'admin_notices', array( $this, 'no_active_campaign_alert') );
        add_action( 'admin_notices', array( $this, 'review_upgrade_notices') );
        // add_action( 'admin_notices', array( $this, 'envato_validation_box' ) );

        add_action( 'admin_init', array( $this, 'export_email_list') );
    }

    /**
	 * Enqueue scripts.
	 */
	function enqueue_scripts( $hook ) { 
    
        if( strpos( $hook, 'skytake') === false ) return;

        Panel::enqueue_scripts( 'skytake', SKYTAKE_URL . '/lib/', SKYTAKE_VERSION );

        wp_enqueue_script(
            'skytake-admin',
            SKYTAKE_URL . '/assets/js/admin.js', 
            array('jquery'), 
            SKYTAKE_VERSION, 
            true 
        );  
        
        wp_localize_script( 'skytake-admin', 'skytake_admin_settings', array(
            'panel_url' => admin_url('admin.php?page=skytake'),
            'nonce' => wp_create_nonce('skytake'),
            'delete_confirm_text' => __('Are you sure?', 'skytake'),
            'status_confirm_text' => __('Are you sure? activating this campaign will deactivate any active campain with the same type.', 'skytake'),
        ));        
    }

    /**
	 * Enqueue styles.
	 */
	function enqueue_styles( $hook ) { 
            
        if( strpos( $hook, 'skytake') === false ) return;

        Panel::enqueue_styles( 'skytake', SKYTAKE_URL . '/lib/', SKYTAKE_VERSION );

        wp_enqueue_style(
            'skytake-admin', 
            SKYTAKE_URL . '/assets/css/admin.css', 
            array(), 
            SKYTAKE_VERSION
        );
    }

    /**
     * Admin menus
     */
    function add_admin_menu() {
        
        $this->subscriber_list = new Subscriber_List_table();
        $this->campaign_list = new Campaign_List_table();

        add_menu_page(
            'Skytake Campaigns',
            'Skytake Leads & Sales',
            'manage_options',
            'skytake',
            array( $this, 'render_campaigns_page' ),
            'dashicons-email',
            58
        );

        add_submenu_page( 
            'skytake', 
            __('skytake Campaigns', 'skytake'), 
            __('Campaigns', 'skytake'), 
            'manage_options',
            'skytake_campaigns', 
            array( $this, 'render_campaigns_page' )
        );

        add_submenu_page( 
            'skytake', 
            __('Subscribers', 'skytake'), 
            __('Subscribers', 'skytake'), 
            'manage_options',
            'skytake_subscribers', 
            array( $this, 'render_subscribers_page' )
        );

        add_submenu_page( 
            'skytake', 
            __('Documentation', 'skytake'), 
            __('Documentation', 'skytake'), 
            'manage_options',
            'skytake_documentation', 
            function(){
                echo '<script>location.href = "'.SKYTAKE_DOC_URL.'";</script>';
            }
        );

        add_submenu_page( 
            'skytake', 
            __('Get Support', 'skytake'), 
            __('Get Support', 'skytake'), 
            'manage_options',
            'skytake_support', 
            function(){
                echo '<script>location.href = "https://delabon.com/support";</script>';
            }
        );

        // if( ! is_premium() ){
        //     add_submenu_page( 
        //         'skytake', 
        //         __('Try Premium Version', 'skytake'), 
        //         __('Try Premium Version', 'skytake'), 
        //         'manage_options',
        //         'skytake_premium', 
        //         function(){
        //             echo '<script>location.href = "'.SKYTAKE_UPGRADE_URL.'";</script>';
        //         }
        //     );
        // }

    }

    /**
    * Render campaigns page
    */
    function render_campaigns_page() {

        $nonce = wp_create_nonce( 'skytake_add_campaign' );
        $add_link = admin_url('admin.php?page=skytake_add_campaign&_wpnonce='.$nonce);

        ?>

            <?php require SKYTAKE_PATH . '/inc/views/admin/header.php' ?>
            
            <div class="wrap">

                <div class="dlb_container">

                    <h1 class="wp-heading-inline"><?php _e('Campaigns', 'skytake'); ?></h1>
                    
                    <a href="<?php echo $add_link; ?>" class="page-title-action open-skytake-new-campaign"><?php _e('Add New', 'skytake'); ?></a>
                    <?php require SKYTAKE_PATH . '/inc/views/admin/buttons.php' ?>

                    <hr class="wp-header-end">

                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <div class="meta-box-sortables ui-sortable">
                                    <form method="post">
                                        <?php
                                            $this->campaign_list->prepare_items();
                                            $this->campaign_list->display(); 
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <br class="clear">
                    </div>

                    <div class="skytake-new-campaign __hidden">
        
                        <div class="skytake-new-campaign-box">
        
                            <header>
            
                                <?php echo HTML::select(array(
                                    'name' => 'campaign_type',
                                    'value' => 'none',
                                    'title' => __('Select Campaign Type', 'skytake'),
                                    'items' => skytake()->defaultConfig['campaign_types']
                                ));?>
        
                            </header>
        
                            <section>
                                <button class="button button-primary create-skytake-new-campaign"><?php _e('Create', 'skytake') ?></button>
                                <button class="button close-skytake-new-campaign"><?php _e('Close', 'skytake') ?></button>
                            </section>
        
                        </div>
        
                    </div>

                </div>

            </div>
            
        <?php
    }

    /**
    * Render subscribers page
    */
    function render_subscribers_page() {
        
        ?>
            <?php require SKYTAKE_PATH . '/inc/views/admin/header.php' ?>

            <div class="wrap">

                <div class="dlb_container">

                    <h1 class="wp-heading-inline"><?php _e('Subscribers', 'skytake'); ?></h1>
                    <a href="#list-exporter" class="page-title-action"><?php _e('Export All', 'skytake'); ?></a>
                    <?php require SKYTAKE_PATH . '/inc/views/admin/buttons.php' ?>
                    
                    <hr class="wp-header-end">

                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <div class="meta-box-sortables ui-sortable">
                                    <form method="post">
                                        <?php
                                            $this->subscriber_list->prepare_items();
                                            $this->subscriber_list->display(); 
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <br class="clear">
                    </div>

                    <?php 

                    echo 
                            
                    HTML::twoInputs(
                        array(
                            'id' => 'list-exporter',
                            'title' => __('Export all emails to CSV', 'skytake'),
                            'description' => __('Leave start date empty to export all emails.', 'skytake'),
                            'wrapper_class' => 'skytake_export_csv_date',
                        ),
                        HTML::datepicker(array(
                            'wrapper' => false,
                            'placeholder' => __('Start date', 'skytake'),
                        )),
                        HTML::a(__('Export Now', 'skytake'), array(
                            'href'  => admin_url('admin.php?skytake_email_export=true'),
                            'class'  => 'button button-primary'
                        ))
                    )
                    ;

                    ?>
                
                </div>

            </div>
        <?php
    }
    
    /**
     * Export email list to csv
     */
    function export_email_list(){
        
        if( ! isset( $_GET['skytake_email_export'] ) ) return false;

        global $wpdb;

        $table_name = $wpdb->prefix . 'skytake_emails';
        $query = 'SELECT * FROM '.$table_name;

        if( isset( $_GET['skytake_email_export_date'] ) ){
            $query .= ' WHERE date_added > "'.esc_sql( $_GET['skytake_email_export_date'] ).'"';
        }

        $results = $wpdb->get_results( $query . ' ORDER BY date_added DESC', OBJECT );

        $csv = new CSV;
        $csv->add_header_columns(array( 'Email Address', 'First Name', 'Phone' ));

        foreach ( $results as $item ) {
            $csv->add_row(array( $item->email, $item->name, $item->mobile ));
        }
        
        $csv->export();            
    }

    /**
     * Admin notice when no campaign is active
     */
    function no_active_campaign_alert() {

        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }
        
        $campaigns = Campaign::active();
        $total_campaigns = total_campaigns();

        if( ! $campaigns && $total_campaigns > 0 ){

            ?>
                <div class="notice notice-error">
                    <p><?php _e( 'Please activate one of your Skytake campaigns.', 'skytake' ); ?></p>
                </div>
            <?php

            return false;// important
        }

    }

    /**
     * Shows the review and upgrade notices for the admin every 4 days
     */
    function review_upgrade_notices() {

        $hide_review_notice = get_option( 'skytake_hide_review_notice', 0 );

        if( get_transient('skytake_hide_notices') ){
            return ;
        }

        if( $hide_review_notice == 1 && is_premium() ){
            return;
        }
        
        ?>
            <div class="notice notice-warning">

                <p style="font-size: 15px;">
                    <?php _e("Hi there! you have been using <strong>Skytake Leads & Sales</strong> for few days. I hope it is helpful.", 'skytake'); ?>
                    <br>

                    <?php if( $hide_review_notice == 0 ): ?>
                        <?php _e("Would you mind give it 5-stars review to help spread the word? And to keep me updating it & adding more features to it!", 'skytake' ); ?>
                    <?php else: ?>
                        <?php _e("Would you want to get awesome features?", 'skytake' ); ?>     
                    <?php endif; ?>
                </p>

                <p>
                    <?php if( $hide_review_notice == 0 ): ?>
                        <a class="button button-primary" href="<?php echo add_query_arg( 'skytake_hide_review_notice', 'true', skytake()->request->url() ); ?>">Give it 5-stars</a>
                    <?php endif; ?>
                    
                    <a class="button button-primary" href="<?php echo add_query_arg( 'skytake_hide_premium_notice', 'true', skytake()->request->url() ); ?>">Try premium version</a>
                    <a class="button" href="<?php echo add_query_arg( 'skytake_hide_notices', 'true', skytake()->request->url() ); ?>">Thanks, later</a>
                </p>

            </div>
        <?php

    }

    /**
     * Hide admin notices
     */
    function hide_notices() {

        if( ! get_option( 'skytake_first_notice_hide', 0 ) ){
            
            update_option('skytake_first_notice_hide', 1 );
            set_transient('skytake_hide_notices', 1, 4 * DAY_IN_SECONDS ); 
        }

        if( skytake()->request->get('skytake_hide_review_notice') ){
    
            update_option('skytake_hide_review_notice', 1 );
            set_transient('skytake_hide_notices', 1, 4 * DAY_IN_SECONDS ); 
            echo '<script>location.href = "'.SKYTAKE_REVIEW_URL.'";</script>';
        }
    
        if( skytake()->request->get('skytake_hide_premium_notice') ){
    
            set_transient('skytake_hide_notices', 1, 4 * DAY_IN_SECONDS ); // 4 days
            echo '<script>location.href = "'.SKYTAKE_UPGRADE_URL.'";</script>';
        }
    
        if( skytake()->request->get('skytake_hide_notices') ){
    
            set_transient('skytake_hide_notices', 1, 4 * DAY_IN_SECONDS ); // 4 days
        }
    }

    /**
     * Envato Validation Box
     */
    function envato_validation_box(){

        if( ! defined('SKYTAKE_ENVATO_ITEM_NAME') ) return;
        if( ! defined('SKYTAKE_ENVATO_VALIDATION_URL') ) return;
        if( is_premium() ) return;

        ?>
            <div class="skytake-envato-box">

                <h3><?php _e('Skytake Purchase Validation', 'skytake') ?></h3>

                <form id="skytake_envato_form">

                    <input type="hidden" name="envato_nonce" value="<?php echo wp_create_nonce('skytake'); ?>">
                    <input type="email" name="envato_email" placeholder="<?php _e('Email Address', 'skytake'); ?>"  required>
                    <input type="text" name="envato_code" placeholder="<?php _e('Codecanyon Purchase Code', 'skytake'); ?>" required>
                    
                    <p>
                        <input type="checkbox" name="envato_agree" required>
                        <?php _e('I agree with the <a href="https://delabon.com/terms" target="_blank">terms</a> and <a href="https://delabon.com/privacy-policy" target="_blank">privacy policy</a>.', 'skytake'); ?>
                    </p>
                    
                    <button class="button button-primary" type="submit"><?php _e('Validate', 'skytake') ?>
                </form>

            </div>
        <?php
    }

}
