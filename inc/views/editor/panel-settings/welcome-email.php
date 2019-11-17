<?php

use Delabon\WP\HTML;

/**
 * Section Welcome email
**/
$panel->add_setting( 'is_welcome_email_enabled', [
    'section' => 'welcome_email',
    'output' => HTML::checkbox(array( 
        'title' => __('Send Email When Subscribe', 'skytake'),
        'description' => __('It will be sent automatically after a visitor subscribe.', 'skytake')
        .'<br>'.__('Useful when offering a coupon code or something to download.', 'skytake'),
        'name' =>  'is_welcome_email_enabled',
        'value' =>  $campaign->setting('is_welcome_email_enabled'),
        'text' => __('Enable', 'skytake'),
    ))
]);

$panel->add_setting( 'welcome_email_subject', [
    'section' => 'welcome_email',
    'output' => HTML::input(array( 
        'title' => __('Subject', 'skytake'),
        'name' =>  'welcome_email_subject',
        'value' =>  $campaign->setting('welcome_email_subject'),
    ))
]);

$panel->add_setting( 'welcome_email_content', [
    'section' => 'welcome_email',
    'output' => HTML::textarea(array( 
        'title' => __('Content', 'skytake'),
        'name' =>  'welcome_email_content',
        'value' =>  $campaign->setting('welcome_email_content'),
        'style' => "min-height: 300px;",
        'description' => 
        '[coupon_value] : ' . __('Fixed value or percentage, depending on discount type you have chosen.', 'skytake') . '<br>'
        .'[coupon_code] : ' . __('Code used by the customer to apply the coupon.', 'skytake') . '<br>'
        .'[coupon_expiry_date] : ' . __('Date the coupon should expire and can no longer be used.', 'skytake') . '<br>'
        .'[store_button] : ' . __('When clicked customers will be redirected to your shop.', 'skytake') . '<br>',
    ))
]);
