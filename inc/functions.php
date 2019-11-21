<?php 

namespace SKYT;

use Delabon\WP\Helper;

/**
 * Return the upgrade a tag
 *
 * @return string
 */
function upgrade_tag(){
    if( ! is_premium() ){
        return '<a class="dlb_upgrade_tag" target="_blank" href="'.SKYTAKE_UPGRADE_URL.'">'.__('Upgrade(Soon)','skytake').'</a>';
    }
}

/**
 * Check if the current version is premium
 *
 * @return boolean
 */
function is_premium(){
    return false;
    // return get_option('skytake_envato_pro_licence', false);
}

/**
 * Get skin custom settings
 *
 * @param string $skin
 * @param string $campaign_type
 * @return array
 */
function get_skin_settings( $skin, $campaign_type ){
    $settings = require_once SKYTAKE_PATH . '/inc/skins/' . $skin . '/settings-' . $campaign_type . '.php'; 
    return $settings;
}

/**
 * Returns total campaigns
 *
 * @return null|int
 */
function total_campaigns() {
    global $wpdb;

    $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}skytake_campaigns";

    return (int)$wpdb->get_var( $sql );
}

/**
 * Toggle campaign status
 * @param Campaign $campaign
 * @param int $status
 */
function toggle_campaign_status( $campaign, $status ){

    $active_campaigns = Campaign::active();

    if( $active_campaigns ){
        foreach ( $active_campaigns as $active_campaign ){
        
            if( $active_campaign->type() !== $campaign->type() ){
                continue;
            }

            if( $active_campaign->id() !== $campaign->id() ){
                Campaign::update( $active_campaign->id(), [ 'c_status' => 0 ], [ '%d' ] );
            }
    
        }
    }
    
    Campaign::update( $campaign->id(), [ 'c_status' => $status ], [ '%d' ] );

}

/**
 * Returns widget form campaigns
 * used for the widget select
 *
 * @return array|false
 */
function widget_form_campaign_list() {
    global $wpdb;

    $sql = "SELECT * FROM {$wpdb->prefix}skytake_campaigns WHERE c_type='widget-form'";

    $list = [
        'none' => __('None', 'skytake'),
    ];

    $result = $wpdb->get_results( $sql, ARRAY_A );

    if( ! $result ){
        return $list;
    }

    foreach ( $result as $campaign ){
        $list[ $campaign['id'] ] = $campaign['name'];
    }

    return $list;
}

/**
 * Returns content form campaigns
 * used for the gutenberg block
 *
 * @return array|false
 */
function content_form_campaign_list() {
    global $wpdb;

    $sql = "SELECT * FROM {$wpdb->prefix}skytake_campaigns WHERE c_type='content-form'";

    $list = [
        [ 'value' => 'none', 'label' => __('None', 'skytake') ],
    ];

    $result = $wpdb->get_results( $sql, ARRAY_A );

    if( ! $result ){
        return $list;
    }

    foreach ( $result as $campaign ){
        $list[] = [ 'value' =>  $campaign['id'], 'label' => $campaign['name'] ];
    }

    return $list;
}

/**
 * Set upgrade notice time
 *
 * @param DateTime $now
 * @return DateTime
 */
function set_upgrade_notice_time( $now ){

    $notice_time = $now;
    $notice_time->modify('+4 day');
    update_option('skytake_upgrade_notice_time', $notice_time->format('Y-m-d H:i:s') );

    return $notice_time;
}

/**
 * Get upgrade notice time
 *
 * @param DateTime $now
 * @return DateTime
 */
function get_upgrade_notice_time( $now ){

    $notice_time = get_option('skytake_upgrade_notice_time', null );
    
    if( ! $notice_time ){
        return set_upgrade_notice_time( $now );
    }

    return new \DateTime( $notice_time );
}

/**
 * get coupons
 *
 * @return array
 */
function get_coupon_list(){

    if( ! class_exists('Woocommerce') ) {
        return array();
    }

    global $wpdb;
    
    $couponsArr = array();
    $coupons = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts AS posts 
        WHERE posts.post_type='shop_coupon' AND posts.post_status='publish' 
    ");

    foreach ( $coupons as $coupon ) {
        $obj = new \WC_Coupon( (int)$coupon->ID );
        $couponsArr[ (int)$coupon->ID ] = $obj;
    }
    
    return $couponsArr;
}

/**
 * Return array of coupons
 * Used for admin panel
 *
 * @return array
 */
function coupon_select_list(){

    $id = (int)get_option('selected_coupon', 0);
    $arr = array( 0 => __('Coupon List', 'skytake') );

    if( ! class_exists('Woocommerce') ) {
        return $arr;
    }

    $coupons = get_coupon_list();

    foreach( $coupons as $id => $coupon ) {

        if( is_coupon_expired( $coupon ) ){
            $arr[ $id ] = $coupon->get_code() . ' ' . __('[Expired]');
        }
        else{
            $arr[ $id ] = $coupon->get_code();
        }

    }

    return $arr;
}

/**
 * get selected coupon
 *
 * @return string|WC_Coupon
 */
function get_coupon( $id ){

    if( ! class_exists('Woocommerce') ) {
        return false;
    }

    $list = get_coupon_list();

    // no coupons
    if( empty( $list ) ){
        return false;
    }

    // panel selection
    if( isset( $list[ $id ] ) ){
        return $list[ $id ];
    }

    return false;
}

/**
 * Return coupon value
 *
 * @param WC_Coupon $coupon
 * @return string
 */
function get_coupon_value( $coupon ){
    
    if ( $coupon->get_discount_type() === 'percent' ) {
        return $coupon->get_amount() . __('%', 'skytake');
    } 
    
    return wc_price( $coupon->get_amount() );
}

/**
 * Checks if a coupon is valid
 * checks for everything
 *
 * @param WC_Coupon $coupon
 * @return boolean
 */
function is_coupon_valid( $coupon ){

    if( ! $coupon ){
        return false;
    }

    try {
        validate_coupon_exists( $coupon );
        validate_coupon_usage_limit( $coupon );
        validate_coupon_user_usage_limit( $coupon );
        validate_coupon_expiry_date( $coupon );
        validate_coupon_excluded_products( $coupon );
        validate_coupon_excluded_categories( $coupon );
    } 
    catch ( \Exception $e ) {
        // dump( $e->getMessage() );
        return false;
    }

    return true;
}

/**
 * Only checks for coupon expires date && coupon usage
 * used for admin panel
 * 
 * @param WC_Coupon $coupon 
 * @return boolean
 */
function is_coupon_expired( $coupon ){
 
    try {
        validate_coupon_exists( $coupon );
        validate_coupon_usage_limit( $coupon );
        validate_coupon_expiry_date( $coupon );
    } 
    catch ( \Exception $e ) {
        // $e->getMessage()
        return true;
    }
 
    return false;
}

/**
 * Ensure coupon exists or throw exception.
 *
 * @throws Exception Error message.
 * @param  WC_Coupon $coupon Coupon data.
 * @return bool
 */
function validate_coupon_exists( $coupon ) {

    if ( ! $coupon->get_id() && ! $coupon->get_virtual() ) {
        throw new \Exception( 'Coupon does not exist' );
    }

    return true;
}

/**
 * Ensure coupon usage limit is valid or throw exception.
 *
 * @throws Exception Error message.
 * @param  WC_Coupon $coupon Coupon data.
 * @return bool
 */
function validate_coupon_usage_limit( $coupon ) {
    if ( $coupon->get_usage_limit() > 0 && $coupon->get_usage_count() >= $coupon->get_usage_limit() ) {
		throw new \Exception( 'Coupon usage limit has been reached.' );
    }

    return true;
}

/**
 * Ensure coupon user usage limit is valid or throw exception.
 *
 * Per user usage limit - check here if user is logged in (against user IDs).
 * Checked again for emails later on in WC_Cart::check_customer_coupons().
 *
 * @throws Exception Error message.
 * @param  WC_Coupon $coupon  Coupon data.
 * @return bool
 */
function validate_coupon_user_usage_limit( $coupon ) {

    $user_id = get_current_user_id();
        
    if( $user_id && $coupon->get_usage_limit_per_user() > 0 ){
    
        $used_by = array_count_values( $coupon->get_used_by() );

        if( isset( $used_by[ $user_id ] ) ){
            if( $used_by[ $user_id ] == $coupon->get_usage_limit_per_user() ){
                throw new \Exception( 'Coupon usage limit has been reached.' );
            }
        }
    }

    return true;
}

/**
 * Ensure coupon date is valid or throw exception.
 *
 * @throws Exception Error message.
 * @param  WC_Coupon $coupon Coupon data.
 * @return bool
 */
function validate_coupon_expiry_date( $coupon ) {
    if ( $coupon->get_date_expires() && ( current_time( 'timestamp', true ) > $coupon->get_date_expires()->getTimestamp() ) ) {
        throw new \Exception( 'This coupon has expired.' );
    }

    return true;
}

/**
 * Ensure coupon is valid for products in the list is valid or throw exception.
 *
 * @throws Exception Error message.
 * @param  WC_Coupon $coupon Coupon data.
 * @return bool
 */
function validate_coupon_product_ids( $coupon ) {
    global $post;

    if( ! is_product() ){
        return true;
    }

    if( count( $coupon->get_product_ids() ) > 0 ) {
        $item = new \WC_Product( $post->ID );

        if( ! $item->get_id() ){
            return true;
        }

        if( in_array( $item->get_id(), $coupon->get_product_ids(), true ) ){
            return true;
        }
            
        if( in_array( $item->get_parent_id(), $coupon->get_product_ids(), true ) ) {
            return true;
        }

        throw new \Exception( 'Sorry, this coupon is not applicable to selected products.' );
    }

    return true;
}

/**
 * Ensure coupon is valid for product categories in the list is valid or throw exception.
 *
 * @throws Exception Error message.
 * @param  WC_Coupon $coupon Coupon data.
 * @return bool
 */
function validate_coupon_product_categories( $coupon ) {

    if ( count( $coupon->get_product_categories() ) == 0 ) {
        return true;
    }

    $item = get_queried_object();

    // this is a term
    if( $item instanceof \WP_Term ){

        $product_cats = array( $item->term_id );

        if( $item->parent ) {
            $product_cats[] = $item->parent;
        }

        // If we find an item with a cat in our allowed cat list, the coupon is valid.
        if ( count( array_intersect( $product_cats, $coupon->get_product_categories() ) ) > 0 ) {
            return true;
        }

        throw new \Exception( 'Sorry, this coupon is not applicable to selected categories.' );
    }
    // this is a post
    elseif( $item instanceof \WP_Post ){

        $product_cats = wc_get_product_cat_ids( $item->ID );

        // If we find an item with a cat in our allowed cat list, the coupon is valid.
        if ( count( array_intersect( $product_cats, $coupon->get_product_categories() ) ) > 0 ) {
            return true;
        }

        throw new \Exception( 'Sorry, this coupon is not applicable to selected categories.' );
    }

    return true;
}

/**
 * Ensure current product is not excluded
 *
 * @throws Exception Error message.
 * @param  WC_Coupon $coupon Coupon data.
 * @return bool
 */
function validate_coupon_excluded_products( $coupon ){
    global $post;

    if( ! is_product() ){
        return true;
    }

    if( count( $coupon->get_excluded_product_ids() ) > 0 ){
        if( in_array( $post->ID, $coupon->get_excluded_product_ids() ) ){   
            throw new \Exception( 'Sorry, this coupon is not applicable to this product.' );
        }
    }
    
    return true;
}

/**
 * Ensure current category is not excluded
 *
 * @throws Exception Error message.
 * @param  WC_Coupon $coupon Coupon data.
 * @return bool
 */
function validate_coupon_excluded_categories( $coupon ){

    $item = get_queried_object();

    // this is a term
    if( ! $item instanceof \WP_Term ){
        return true;
    }
    
    if( count( $coupon->get_excluded_product_categories() ) > 0 ){
        if( in_array( $item->term_id, $coupon->get_excluded_product_categories() ) ){ 
            throw new \Exception( 'Sorry, this coupon is not applicable to this category.' );
        }
    }

    return true;
}

/**
 * Mailchimp Connect
 *
 * @param string $api_key
 * @param string $endpoint
 * @param string $request_type
 * @param array $data
 * @return string|false
 */
function mailchimpConnect( $api_key, $endpoint, $request_type, $data = array() ) {

    $request_type = strtoupper( $request_type );
    
    if( $api_key === '' ){
        throw new \Exception(__('Invalid Mailchimp API key.', 'skytake'));
    }

    $data_center = substr( $api_key, strpos( $api_key, '-') + 1 );
    $url = 'https://'.$data_center.'.api.mailchimp.com/3.0/' . $endpoint;

	if( $request_type === 'GET' ){
		$url .= '?' . http_build_query($data);
    }
    
    return Helper::curl( 
        $url, 
        $request_type,
        array(
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode( 'user:'. $api_key )
        ),
        $data
    );
}

/**
 * Add member to a mailchimp list
 *
 * @param string $api_key
 * @param string $list_id
 * @param string $email
 * @param string $firstname
 * @param string $mobile
 * @return array
 */
function mailchimpSubscribe( $api_key, $list_id, $email, $firstname = '', $mobile = '' ){

    if( ! is_email( $email ) ){
        throw new \Exception(__('Invalid email address.', 'skytake'));
    }

    if( $list_id === '' ){
        throw new \Exception(__('Invalid Mailchimp list id.', 'skytake'));
    }

    // dont leave it empty
    $merge_fields = array(
        'FNAME' => '',
        'LNAME' => ''
    );

    if( $firstname != '' ){
        $merge_fields['FNAME'] = $firstname;
    }

    if( $mobile != '' ){
        $merge_fields['PHONE'] = $mobile;
    }

    return mailchimpConnect( $api_key, 'lists/' . $list_id . '/members/', 'POST', array(
        'email_address' => $email,
        'status'        => 'subscribed',
        'merge_fields'  => $merge_fields,
    ));
}

/**
 * Add member to a mailchimp campaign ( Workflow )
 *
 * @param string $api_key
 * @param string $campaign_url
 * @param string $email
 * @return array
 */
function mailchimpAddToCampaign( $api_key, $campaign_url, $email ){

    if( ! is_email( $email ) ){
        throw new \Exception(__('Invalid email address.', 'skytake'));
    }

    $url = $campaign_url;

    if( $url === '' ){
        throw new \Exception(__('Invalid Mailchimp campaign url.', 'skytake'));
    }

    if( strpos( $url, '/automations/' ) === false ){
        throw new \Exception(__('Invalid Mailchimp campaign url.', 'skytake'));
    }

    $endpoint = preg_replace('`(.*?)\/3.0\/`', '', $url );

    return mailchimpConnect( $api_key, $endpoint, 'POST', array(
        'email_address' => $email,
    ));
}

/**
 * Generate custom css
 * 
 * @param int $id
 * @param string $type
 * @param array $settings
 * @return string
 */
function generate_css( $id, $type, $settings ){

    $css = generate_common_css( $id, $settings );

    return $css;
}

/**
 * Generate common custom css
 *
 * @param int $id
 * @param array $settings
 * @return string
 */
function generate_common_css( $id, $settings ){

    $css = '';
    $data = $settings;
    $body_bg_image = $data['body_bg_image'];

    if( $body_bg_image !== '' ){
        $body_bg_image = 'url('. $body_bg_image .')'; 
    }

    if( $data['font_family'] !== 'theme_font' ){
        $css .= '
            #skytake-'.$id.'{
                font-family: '. skytake()->defaultConfig['fonts'][ $data['font_family'] ][1] .';
            }
        ';
    }
        
    $css .= '
        #skytake-'.$id.' .skytake-container{
            background-color: '.esc_attr( $data['body_bg_color'] ).';
            background-image: '.esc_attr( $body_bg_image ).';
            background-repeat: '.esc_attr( $data['body_bg_repeat'] ).';
            background-size: '.esc_attr( $data['body_bg_size'] ).';
            background-position: '.esc_attr( $data['body_bg_position'] ).';
        }

        #skytake-'.$id.' .skytake-view,
        #skytake-'.$id.' .skytake-view a,
        #skytake-'.$id.' .skytake-view a:focus,
        #skytake-'.$id.' .skytake-view a:hover{
            color: '.esc_attr( $data['body_color']).';
            font-size: '.esc_attr( $data['body_font_size']).'px;
            color: '.esc_attr( $data['body_color']).';
        } 

        #skytake-'.$id.' .skytake-title{
            font-size: '.esc_attr( $data['title_font_size']).'px;
            color: '.esc_attr( $data['title_color']).';
        }

        #skytake-'.$id.' .skytake-close,
        #skytake-'.$id.' .skytake-close:hover{
            background-color: '.esc_attr( $data['close_icon_bg_color']).';
            fill: '.esc_attr( $data['close_icon_color']).';
            color: '.esc_attr( $data['close_icon_color']).';
        }

        #skytake-'.$id.' input[type="email"],
        #skytake-'.$id.' input[type="text"]{
            font-size: '.esc_attr( $data['input_font_size']).'px;
            color: '.esc_attr( $data['email_color']).';
            background-color: '.esc_attr( $data['email_bg_color']).';
        }

        #skytake-'.$id.' button.skytake-submit{
            font-size: '.esc_attr( $data['input_font_size']).'px;
            color: '.esc_attr( $data['submit_color']).';
            background-color: '.esc_attr( $data['submit_bg_color']).';
        }

        #skytake-'.$id.' .skytake-social-icons span{
            font-size: '.esc_attr( $data['social_icon_size']).'px;            
        }

        #skytake-'.$id.' .skytake-social-icons.__color_type_custom span{
            color: '.esc_attr( $data['social_icon_color']).';            
        }

        #skytake-'.$id.' .skytake-urgency{
            font-size: '.esc_attr( $data['urgency_font_size']).'px; 
        }

        #skytake-'.$id.' .skytake-urgency-timer{
            color: '.esc_attr( $data['urgency_color']).';   
            background-color: '.esc_attr( $data['urgency_bg_color']).';   
        }

        .skytake-overlay[data-target="'.$id.'"]{
            background-color: '.esc_attr($data['overlay_color']).';
        }

        .skytake-bar[data-target="'.$id.'"]{
            background-color: '.esc_attr( $data['minimized_bar_bg_color']).';            
        }

        .skytake-bar[data-target="'.$id.'"] span{
            color: '.esc_attr( $data['minimized_bar_color']).';
            font-size: '.esc_attr( $data['minimized_bar_size']).'px;
            line-height: '.esc_attr( $data['minimized_bar_size']).'px;
        }

    ';

    return $css;
}
