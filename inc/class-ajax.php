<?php 

namespace SKYT;

use Delabon\WP\Helper;
use Delabon\WP\Request;
use SKYT\Campaigns\Popup\Popup;
use SKYT\Campaigns\Scroll_Box\Scroll_Box;
use SKYT\Campaigns\Widget_Form\Widget_Form;
use SKYT\Campaigns\Content_Form\Content_Form;
use SKYT\Campaigns\Floating_Bar\Floating_Bar;

class Ajax{

    const NONCE_KEY = 'skytake';

    /**
     * Add Listeners
     */
    public function __construct(){

        add_action( 'wp_ajax_skytake_get_popup', array( $this, 'get_popup' ) );
        add_action( 'wp_ajax_nopriv_skytake_get_popup', array( $this, 'get_popup' ) );
        
        add_action( 'wp_ajax_skytake_subscribe', array( $this, 'subscribe' ) );
        add_action( 'wp_ajax_nopriv_skytake_subscribe', array( $this, 'subscribe' ) );

        add_action( 'wp_ajax_skytake_update_campaign_views', array( $this, 'update_campaign_views' ) );
        add_action( 'wp_ajax_nopriv_skytake_update_campaign_views', array( $this, 'update_campaign_views' ) );

        add_action( 'wp_ajax_skytake_save_editor_settings', array( $this, 'save_editor_settings' ) );
        add_action( 'wp_ajax_skytake_duplicate_campaign', array( $this, 'duplicate_campaign' ) );
        add_action( 'wp_ajax_skytake_new_campaign', array( $this, 'new_campaign' ) );
        add_action( 'wp_ajax_skytake_delete_campaign', array( $this, 'delete_campaign' ) );
        add_action( 'wp_ajax_skytake_toggle_status_campaign', array( $this, 'toggle_status_campaign' ) );
        add_action( 'wp_ajax_skytake_envato_validation', array( $this, 'envato_purchase_validation' ) );
        add_action( 'wp_ajax_skytake_get_preview_template_markup', array( $this, 'get_preview_template_markup' ) );
        add_action( 'wp_ajax_skytake_get_mailchimp_list_items', array( $this, 'get_mailchimp_list_items' ) );
    }

    /**
     * Return mailchimp list items
     */
    function get_mailchimp_list_items(){

        $this->validateNounce();
        $this->validatePermission();

        if( ! isset( $_POST['campaign_id'] ) ){
            $this->response( false, 'Invalid campaign id' );
        }

        $campaign = Campaign::get( (int)$_POST['campaign_id'] );

        if( ! $campaign ){
            $this->response( false, 'Invalid campaign' );
        }

        if( ! isset( $_POST['api_key'] ) ){
            $this->response( false, 'Api key is not set!' );
        }

        $api_key = sanitize_text_field( $_POST['api_key'] );
        
        if( $api_key === '' ){
            $this->response( false, 'Api key is not set!' );
        }

        $lists = array( 0 => __('Select List', 'skytake') );

        $result = json_decode( mailchimpConnect( $api_key, 'lists', 'GET', array(
            'fields' => 'lists',
        )));
        
        if( empty($result->lists) ) {
            $this->response( false, 'Empty list');
        }

        foreach( $result->lists as $list ){
            $lists[ $list->id ] = $list->name . ' (' . $list->stats->member_count . ')';
        }

        $this->response( true, [ 'lists' => $lists, 'selected' => $campaign->setting('mailchimp_listid') ] );
    }

    /**
     * Verify purchase code
     */
    function envato_purchase_validation(){

        $this->validateNounce();
        $this->validatePermission();

        $request = new Request;
        $email = sanitize_email( $request->post('email', '') );
        $code = sanitize_text_field( $request->post( 'code', '' ) );

        if( ! is_email( $email ) ){
            $this->response( false, __('Invalid Email', 'skytake') );
        }

        if( $code === '' ){
            $this->response( false, __('Invalid Purchase Code', 'skytake') );
        }
        
		// Send request
        $result = Helper::curl( 
            SKYTAKE_ENVATO_VALIDATION_URL, 
            'POST', 
            array(
                'Content-Type: application/json',
            ),
            array(
                'email' => $email,
                'code' => $code,
                'item_name' => SKYTAKE_ENVATO_ITEM_NAME,
                'ip' => $request->ip(),
            )
        );
    
        if( ! $result ){
            $this->response( false, __('Something went wrong please try again later.') );
        }

        $result = json_decode( $result, true);

        if( isset( $result['errors'] ) ){
            $this->response( false, $result['errors'] );
        }

        update_option('skytake_envato_pro_licence', true );

        $this->response( true );
    }

    /**
     * New campaign
     */
    function new_campaign(){

        $this->validateNounce();
        $this->validatePermission();

        $type = 'popup';

        if( isset( $_POST['type'] ) ){
            $type = sanitize_text_field( $_POST['type'] );
        }

        $id = Campaign::add( $type );

        $this->response( true, admin_url('admin.php?action=skytake_editor&campaign_id=' . $id ) );
    }

    /**
     * Dupliacate comapign
     */
    function duplicate_campaign(){
        global $wpdb;

        $this->validateNounce();
        $this->validatePermission();

        if( ! isset( $_POST['campaign_id'] ) ){
            $this->response( false, 'Invalid campaign id' );
        }

        $campaign = Campaign::get( (int)$_POST['campaign_id'] );

        if( ! $campaign ){
            $this->response( false, 'Invalid campaign' );
        }

        $now = Helper::date_now();

        $wpdb->insert( 
            $wpdb->prefix.'skytake_campaigns', 
            array(
                'name' => $campaign->name() . ' (d)',
                'c_status' => 0,
                'c_type' => $campaign->type(),
                'c_statistics' => serialize(array( 'views' => 0, 'subscribers' => 0 )),
                'c_settings' => serialize( $campaign->settings() ),
                'custom_css' => generate_css( $campaign->id(), $campaign->type(), $campaign->settings() ),
                'change_token' => uniqid(),
                'date_created' => $now->format('Y-m-d H:i:s'),
                'date_updated' => $now->format('Y-m-d H:i:s'),
            ), 
            array(
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ) 
        ); 

        $this->response( true );
    }

    /**
     * Delete comapign
     */
    function delete_campaign(){
        global $wpdb;

        $this->validateNounce();
        $this->validatePermission();

        if( ! isset( $_POST['campaign_id'] ) ){
            $this->response( false, 'Invalid campaign id' );
        }

        $campaign = Campaign::get( (int)$_POST['campaign_id'] );

        if( ! $campaign ){
            $this->response( false, 'Invalid campaign' );
        }

        $wpdb->delete(
            "{$wpdb->prefix}skytake_campaigns",
            [ 'id' => $campaign->id() ],
            [ '%d' ]
        );

        $this->response( true );
    }

    /**
     * Change comapign status
     */
    function toggle_status_campaign(){

        $this->validateNounce();
        $this->validatePermission();

        if( ! isset( $_POST['campaign_id'] ) ){
            $this->response( false, 'Invalid campaign id' );
        }

        $campaign = Campaign::get( (int)$_POST['campaign_id'] );

        if( ! $campaign ){
            $this->response( false, 'Invalid campaign' );
        }

        toggle_campaign_status( $campaign, (int)$_POST['status'] );
    
        $this->response( true );
    }

    /**
     * Save editor settings
     */
    function save_editor_settings(){

        $this->validateNounce();
        $this->validatePermission();

        unset( $_POST['action'] );
        unset( $_POST['nonce'] );

        if( ! isset( $_POST['campaign_id'] ) ){
            $this->response( false, 'Invalid campaign id' );
        }

        $campaign = Campaign::get( (int)$_POST['campaign_id'] );

        if( ! $campaign ){
            $this->response( false, 'Invalid campaign' );
        }

        unset( $_POST['campaign_id'] );

        foreach( $_POST as $key => $value ){
            $campaign->setSetting( $key, stripslashes( $value ) );
        }
        
        $data_format = array( '%s', '%s', '%s' );
        $data = array(
            'c_settings' => serialize( $campaign->settings() ),
            'custom_css' => generate_css( $campaign->id(), $campaign->type(), $campaign->settings() ),
            'change_token' => uniqid(),
        );

        if( isset( $_POST['campaign_name'] ) ){
            $data['name'] = $_POST['campaign_name'];
            $data_format[] = '%s';
        }

        if( isset( $_POST['campaign_status'] ) ){
            toggle_campaign_status( $campaign, (int)$_POST['campaign_status'] );
        }

        $this->response( Campaign::update( $campaign->id(), $data, $data_format ) );
    }

    /**
     * Frontend -> Get Popup
     */
    function get_popup(){

        $this->validateNounce();

        $_return = [];
        $non_floating_ids = [];

        if( ! isset( $_POST['ids'] ) ){
            $this->response( false, 'No campaigns');
        }

        if( isset( $_POST['non_floating_ids'] ) ){
            $non_floating_ids = $_POST['non_floating_ids'];
        }
        
        foreach( $_POST['ids'] as $id ){
            
            $campaign = Campaign::get( (int)$id );

            if( in_array( $campaign->type(), [ 'widget-form', 'content-form' ] ) ){

                if( ! in_array( $campaign->id(), $non_floating_ids ) ){
                    continue;
                }
            }

            try {

                if( $campaign->type() === 'popup' ){

                    $ui = new Popup( $campaign );
                    $_return[] = $ui->render();
                }
                elseif( $campaign->type() === 'floating-bar' ){

                    $ui = new Floating_Bar( $campaign );
                    $_return[] = $ui->render();
                }
                elseif( $campaign->type() === 'scroll-box' ){

                    $ui = new Scroll_Box( $campaign );
                    $_return[] = $ui->render();
                }
                elseif( $campaign->type() === 'content-form' ){

                    $ui = new Content_Form( $campaign );
                    $_return[] = $ui->render();
                }
                elseif( $campaign->type() === 'widget-form' ){

                    $ui = new Widget_Form( $campaign );
                    $_return[] = $ui->render();
                }

            } 
            catch ( \Exception $e ) {
                $this->response( FALSE, $e->getMessage() );
            }
        }

        $this->response( true, $_return );
    }

    /**
     * Return preview template markup
     * Used when changing the template from the editor
     *
     * @return string
     */
    function get_preview_template_markup(){

        $this->validateNounce();
        $this->validatePermission();

        if( ! isset( $_POST['campaign_id'] ) ){
            $this->response( false, 'Skytake -> No campaign id');
        }

        if( ! isset( $_POST['skin'] ) ){
            $this->response( false, 'Skytake -> No skin');
        }

        $skin = sanitize_text_field( $_POST['skin'] );

        try {
            
            $campaign = Campaign::get( (int)$_POST['campaign_id'] );

            if( ! $campaign ){
                throw new \Exception('Invalid campaign');
                
            }

            if( $campaign->type() === 'popup' ){
                $campaign_ui = new Popup( $campaign );
            }
            elseif( $campaign->type() === 'floating-bar' ){
                $campaign_ui = new Floating_Bar( $campaign );
            }
            elseif( $campaign->type() === 'scroll-box' ){
                $campaign_ui = new Scroll_Box( $campaign );
            }
            elseif( $campaign->type() === 'widget-form' ){
                $campaign_ui = new Widget_Form( $campaign );
            }
            elseif( $campaign->type() === 'content-form' ){
                $campaign_ui = new Content_Form( $campaign );
            }

            $this->response( TRUE, [
                'markup' => $campaign_ui->render_preview( $skin ),
                'settings' => get_skin_settings( $skin, $campaign->type() ),
            ]);
        } 
        catch ( \Exception $e ) {
            $this->response( FALSE, $e->getMessage() );
        }
    }

    /**
     * Subscribe
     */
    function subscribe(){

        $this->validateNounce();
        
        $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $mobile = isset( $_POST['mobile'] ) ? sanitize_text_field( $_POST['mobile'] ) : '';

        if( ! isset( $_POST['campaign_id'] ) ){
            $this->response( false, 'No campaign id');
        }

        $campaign = Campaign::get( (int)$_POST['campaign_id'] );

        if( ! $campaign ){
            $this->response( false, __('Invalid Camapaign.', 'skytake') );
        }

        if( ! is_email( $email ) ){
            $this->response( false, __('Invalid Email.', 'skytake') );
        }

        $this->save_data( $campaign, $email, $name, $mobile );        
        $this->send_email( $campaign, $email );
        $this->update_campaign_subs( $campaign );
        $this->mailchimp( $campaign, $email, $name, $mobile );
        $this->response( true );
    }

    /**
     * Save data to database
     * 
     * @param Campaign $campaign
     * @param string $email
     * @param string $name
     * @param string $mobile 
     * @return void
     */
    private function save_data( $campaign, $email, $name, $mobile ){

        global $wpdb;

        $now = new \DateTime();
        $table_name = "{$wpdb->prefix}skytake_emails";
        $doesEmailExist = $wpdb->get_row( "SELECT * FROM {$table_name} WHERE email='{$email}'" );
        $coupon_id = 0;
        $coupon_code = '';
        $coupon = $campaign->coupon();

        // if coupon enabled
        if( $campaign->setting('is_coupon_enabled') == 1 && is_coupon_valid( $coupon ) ){
            $coupon_id = $coupon->get_id();
            $coupon_code = $coupon->get_code();
        }

        if( ! $doesEmailExist ){

            $wpdb->insert( 
                $table_name, 
                array( 
                    'coupon_id'     => $coupon_id,
                    'coupon_code'   => $coupon_code,
                    'campaign_id'   => $campaign->id(),
                    'email'         => $email, 
                    'name'         => $name, 
                    'mobile'         => $mobile, 
                    'date_added'    => $now->format('Y-m-d H:i:s'),
                ), 
                array( 
                    '%d',                
                    '%s',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                ) 
            );
        }
        else{

            $wpdb->update( 
                $table_name, 
                array( 
                    'date_added' => $now->format('Y-m-d H:i:s'),
                    'name'  => $name, 
                    'mobile'=> $mobile, 
                ), 
                array( 
                    'email' => $email, 
                ), 
                array( '%s', '%s', '%s' ), 
                array( '%s' ) 
            );
        }
    }

    /**
     * Send Email
     *
     * @param Campaign $campaign
     * @param string $email
     * @return void
     */
    private function send_email( $campaign, $email ) {

        if( $campaign->setting('is_welcome_email_enabled') == 0 ) return;
        
        $coupon = $campaign->coupon();
        $content = stripslashes( $campaign->setting('welcome_email_content') );
        $content = nl2br( $content );
        $subject = stripslashes( $campaign->setting('welcome_email_subject') );
        $subject = str_replace( '&amp;', '&', $subject );

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo( 'name' ) . ' <' . get_option('admin_email') . '>',
            'Reply-To: ' . get_option('admin_email'),
            'X-Mailer: PHP/' . phpversion(),
        );

        if( $campaign->setting('is_coupon_enabled') == 1 && is_coupon_valid( $coupon ) ){

            $coupon_code = $coupon->get_code();
            $coupon_expiry_date = ! $coupon->get_date_expires() ? 'never expires' : $coupon->get_date_expires()->format('Y-m-d');
            $coupon_value = $coupon->get_amount();

            $shop_url = get_permalink( wc_get_page_id( 'shop' ) );
            $button_shop_now = '<a href="'.$shop_url.'" target="_blank" style="text-decoration:none;display:inline-block;padding:10px 30px;margin:10px 0;font-size:18px;color:white;background:black;border-radius:4px">'.__('Shop Now', 'skytake').'</a>';

            $content         = str_replace( '[coupon_value]', $coupon_value, $content );
            $content         = str_replace( '[coupon_code]', '<span style="font-size: 25px;">' . $coupon_code . '</span>', $content );
            $content         = str_replace( '[coupon_expiry_date]', $coupon_expiry_date, $content );
            $content         = str_replace( '[store_button]', $button_shop_now, $content );            
        }

        wp_mail( $email, $subject, $content, $headers );
    }

    /**
     * Mailchimp
     *
     * @param Campaign $campaign
     * @param string $email
     * @param string $name
     * @param string $mobile
     * @return bool
     */
    private function mailchimp( $campaign, $email, $name, $mobile ){

        // Mailchimp
        if( ! (bool)$campaign->setting('is_mailchimp_enabled') ){
            return false;
        }
        
        $api_key = $campaign->setting('mailchimp_apikey');
        $list_id = $campaign->setting('mailchimp_listid');
        $campaign_url = $campaign->setting('mailchimp_campaign_url');

        try {
            mailchimpSubscribe( $api_key, $list_id, $email, $name, $mobile );
        } 
        catch (\Exception $e) {
            $this->response( false,  __('Email was not added to your Mailchimp list due to this error:') . ' ' . $e->getMessage() );
        }

        if( ! is_premium() ) return false;

        if( ! (bool)$campaign->setting('is_mailchimp_campaign_enabled') ){
            return false;
        }

        try {
            mailchimpAddToCampaign( $api_key, $campaign_url, $email );
        } 
        catch (\Exception $e) {
            $this->response( false,  __('Email was not added to your Mailchimp campaign due to this error:') . ' ' . $e->getMessage() );
        }

    }

    /**
     * Update campaign subscribers stats
     */
    private function update_campaign_subs( $campaign ){
        
        $subs = (int)$campaign->statistic('subscribers');
        $campaign->setStatistic('subscribers', $subs + 1);

        Campaign::update( $campaign->id(), [ 'c_statistics' => serialize( $campaign->statistics() )], [ '%s' ]);

        return true; // no $this->response
    }

    /**
     * Update campaign views
     */
    function update_campaign_views(){

        $this->validateNounce();
        
        if( ! isset( $_POST['campaign_id'] ) ){
            $this->response( false, 'No campaign id' );
        }

        $campaign = Campaign::get( (int)$_POST['campaign_id'] );

        if( ! $campaign ){
            $this->response( false, 'Invalid campaign' );
        }

        $views = (int)$campaign->statistic('views');
        $campaign->setStatistic( 'views', $views + 1 );

        Campaign::update( $campaign->id(), [ 'c_statistics' => serialize( $campaign->statistics() )], [ '%s' ]);

        $this->response( true );
    }

    /**
     * Return json response and die
     */
    private function response( $success, $data = array() ){
        if( $success ){
            wp_send_json_success( $data );
        }
        
        wp_send_json_error( $data );
    }

    /**
     * Create nonce
     * 
     * @return string
     */
    function create_nonce(){
		return wp_create_nonce( self::NONCE_KEY );
    }

    /**
     * Check for valid nonce
     * 
     * @return bool
     */
    private function validateNounce(){

        if( empty( $_POST['nonce'] ) ){
            $this->response( false, 'invalid nonce' );
        }

        if( ! wp_verify_nonce( $_POST['nonce'], self::NONCE_KEY ) ) {
            $this->response( false, 'invalid nonce' );
        }

    }

    /**
     * Validate current user permission
     */
    private function validatePermission(){
        if( ! current_user_can( 'manage_options' ) ) {
            $this->response( false );
        }
    }

}
