<?php

use Delabon\WP\Alert;
use Delabon\WP\HTML;

use function SKYT\coupon_select_list;

/**
 * Woocommerce 
**/

if( ! class_exists('Woocommerce') ){

    $panel->add_setting( 'enabled_woocommerce', [
        'section' => 'woocommerce',
        'output' => Alert::warning(__('Please install and activate Woocommerce first.'), false),
    ]);
    
    return;
}

$panel->add_setting( 'is_coupon_enabled', [
    'section' => 'woocommerce',
    'output' => HTML::checkbox(array( 
        'title' => __('Enable Woocommerce Coupon', 'skytake'),
        'text' => __('Enable', 'skytake'),
        'description' => __('Enable this option if you want to offer a coupon code for your customers.', 'skytake')
            .'<br>'
            . '<strong>' . __('When your cuopon expires the lightbox popup will not show.', 'skytake') . '</strong>',
        'name' =>  'is_coupon_enabled',
        'value' =>  $campaign->setting('is_coupon_enabled'),
    ))
]);

$panel->add_setting( 'selected_coupon', [
    'section' => 'woocommerce',
        'output' => HTML::select(array( 
        'title' => __('Select coupon', 'skytake'),
        'description' => __('Do not have any coupon yet?', 'skytake') . ' <a href="'.admin_url('post-new.php?post_type=shop_coupon').'">'.__('Create New One','skytake').'</a>',
        'name' =>  'selected_coupon',
        'value' =>  $selected_coupon,
        'items' => coupon_select_list(),
    ))
]);

