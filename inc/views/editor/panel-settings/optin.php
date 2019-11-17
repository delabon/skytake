<?php

use Delabon\WP\HTML;

use function SKYT\upgrade_tag;

/**
 * Section Optin
**/
$panel->add_setting( 'main_image', [
    'section' => 'optin',
    'output' => HTML::image_upload(array( 
        'title' => __('Side/Header Image'),
        'name' => 'main_image',
        'value' => $campaign->setting('main_image'),
        'placeholder' => __('Select/Upload image', 'skytake'),
    ))
]);

$panel->add_setting( 'title', [
    'section' => 'optin',
    'output' => HTML::input(array( 
        'title' => __('Title', 'skytake'),
        'name' => 'title',
        'value' => $campaign->setting('title'),
    ))
]);

$panel->add_setting( 'message', [
    'section' => 'optin',
    'output' => HTML::textarea(array( 
        'title' => __('Message', 'skytake'),
        'name' => 'message',
        'value' => $campaign->setting('message'),
    ))
]);

$panel->add_setting( 'is_input_name_enabled', [
    'section' => 'optin',
    'output' => HTML::checkbox(array( 
        'title' => __('Enable Name Input', 'skytake') . upgrade_tag(),
        'name' => 'is_input_name_enabled',
        'value' => $campaign->setting('is_input_name_enabled'),
    ))
]);

$panel->add_setting( 'name_input_text', [
    'section' => 'optin',
    'output' => HTML::input(array( 
        'title' => __('Name Input Text', 'skytake') . upgrade_tag(),
        'name' => 'name_input_text',
        'value' => $campaign->setting('name_input_text'),
    ))
]);

$panel->add_setting( 'is_mobile_input_enabled', [
    'section' => 'optin',
    'output' => HTML::checkbox(array( 
        'title' => __('Enable Mobile Input', 'skytake') . upgrade_tag(),
        'name' => 'is_mobile_input_enabled',
        'value' => $campaign->setting('is_mobile_input_enabled'),
    ))
]);

$panel->add_setting( 'input_mobile_text', [
    'section' => 'optin',
    'output' => HTML::input(array( 
        'title' => __('Mobile Input Text', 'skytake') . upgrade_tag(),
        'name' => 'input_mobile_text',
        'value' => $campaign->setting('input_mobile_text'),
    ))
]);

$panel->add_setting( 'email_input_text', [
    'section' => 'optin',
    'output' => HTML::input(array( 
        'title' => __('Email Input Text', 'skytake'),
        'name' => 'email_input_text',
        'value' => $campaign->setting('email_input_text'),
    ))
]);

$panel->add_setting( 'submit_button_text', [
    'section' => 'optin',
    'output' => HTML::input(array( 
        'title' => __('Submit Button Text', 'skytake'),
        'name' => 'submit_button_text',
        'value' => $campaign->setting('submit_button_text'),
    ))
]);

$panel->add_setting( 'spam_text', [
    'section' => 'optin',
    'output' => HTML::input(array( 
        'title' => __('Spam Text', 'skytake'),
        'name' => 'spam_text',
        'value' => $campaign->setting('spam_text'),
    ))
]);

$panel->add_setting( 'is_gdpr_enabled', [
    'section' => 'optin',
    'output' => HTML::checkbox(array( 
        'title' => __('Enable GDPR Checkbox', 'skytake'),
        'name' => 'is_gdpr_enabled',
        'value' => $campaign->setting('is_gdpr_enabled'),
    ))
]);

$panel->add_setting( 'gdpr_text', [
    'section' => 'optin',
    'output' => HTML::textarea(array( 
        'title' => __('GDPR Text', 'skytake'),
        'name' => 'gdpr_text',
        'value' => $campaign->setting('gdpr_text'),
    ))
]);
