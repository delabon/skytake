<?php

use function SKYT\get_coupon;

$is_coupon_enabled = class_exists('Woocommerce') && $campaign->setting('is_coupon_enabled') == 1;
$selected_coupon = 0;
$expiry_date = $campaign->setting('urgency_expire_date');
$usage_limit = 0;
$urgency_type = $campaign->setting('urgency_type');
$trigger_type = $campaign->setting('display_trigger');
$skin = $campaign->setting('template');
$is_vertical_skin = strpos( $skin, '400' ) === false ? false : true;

if( $is_coupon_enabled ) {

    $selected_coupon = $campaign->setting('selected_coupon');
    $coupon = get_coupon( $selected_coupon );
    
    if( $coupon ){
        $usage_limit = $coupon->get_usage_limit();
        $expiry_date = $coupon->get_date_expires();
    
        if( $expiry_date instanceof \WC_DateTime ){
            $expiry_date = $expiry_date->format('Y-m-d');
        }
        else{
            $expiry_date = '';
        }
    }
}
