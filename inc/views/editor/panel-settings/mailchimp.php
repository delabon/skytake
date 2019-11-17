<?php

use Delabon\WP\HTML;

use function SKYT\upgrade_tag;

/**
 * Section Mailchimp
**/
$panel->add_setting( 'is_mailchimp_enabled', [
    'section' => 'mailchimp',
    'output' => HTML::checkbox(array( 
        'title' => __('Enable Mailchimp', 'skytake'),
        'name' => 'is_mailchimp_enabled', 
        'value' => $campaign->setting('is_mailchimp_enabled'),
        'text' => __('Enable','skytake'),
    ))
]);

$panel->add_setting( 'mailchimp_apikey', [
    'section' => 'mailchimp',
    'output' => HTML::input(array( 
        'title' => __('API Key', 'skytake'),
        'description' => __('Get your API key from here:','skytake') . ' <a href="https://admin.mailchimp.com/account/api" target="_blank">'.__('API Key', 'skytake').'</a>',
        'name' =>  'mailchimp_apikey', 
        'value' => $campaign->setting('mailchimp_apikey'),
    ))
]);

$panel->add_setting( 'mailchimp_listid', [
    'section' => 'mailchimp',
    'output' => HTML::select(array( 
        'title' => __('Lists', 'skytake'),
        'description' => __('Lists are where you store your subscriber’s information.','skytake'),
        'name' =>  'mailchimp_listid', 
        'value' => 0,
        'items' => [ 0 => __('Select List', 'skytake') ],
    ))
]);

$panel->add_setting( 'is_mailchimp_campaign_enabled', [
    'section' => 'mailchimp',
    'output' => HTML::checkbox(array( 
        'title' => __('Enable Campaign', 'skytake') . upgrade_tag(),
        'name' => 'is_mailchimp_campaign_enabled', 
        'value' => $campaign->setting('is_mailchimp_campaign_enabled'),
        'text' => __('Enable','skytake'),
    ))
]);

$panel->add_setting( 'mailchimp_campaign_url', [
    'section' => 'mailchimp',
    'output' => HTML::input(array( 
        'title' => __('Campaign API URL', 'skytake') . upgrade_tag(),
        'description' => __('Check out the documentation if you do not know where to get this url from.','skytake'),
        'name' =>  'mailchimp_campaign_url', 
        'value' => $campaign->setting('mailchimp_campaign_url'),
    ))
]);
