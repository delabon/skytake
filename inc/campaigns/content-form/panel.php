<?php

use Delabon\WP\HTML;
use SKYT\Panel;

use function SKYT\upgrade_tag;

require_once __DIR__ . '/../panels-variables.php';

// ShowTime
$panel = new Panel();
 
$panel->add_panel('setup', [
    'title' => __('Setup', 'skytake'),
]);

$panel->add_section( 'general_settings', [
    'title' => __('General Settings', 'skytake'),
    'panel' => 'setup',
]);

$panel->add_section( 'welcome_email', [
    'title' => __('Welcome Email', 'skytake'),
    'panel' => 'setup',
]);

$panel->add_section( 'filters', [
    'title' => __('Filters', 'skytake'),
    'panel' => 'setup',
]);

// Optin / Success
$panel->add_panel( 'optin_success', [
    'title' => __( 'Optin / Success', 'skytake'),
]);

$panel->add_section( 'templates', [
    'title' => __('Templates', 'skytake'),
    'panel' => 'optin_success',
]);

$panel->add_section( 'optin', [
    'title' => __('Optin', 'skytake'),
    'panel' => 'optin_success',
]);

$panel->add_section( 'success', [
    'title' => __('Success', 'skytake'),
    'panel' => 'optin_success',
]);

$panel->add_section( 'style', [
    'title' => __('Style', 'skytake'),
    'panel' => 'optin_success',
]);

// Features
$panel->add_panel( 'features', [
    'title' => __( 'Features', 'skytake'),
]);

$panel->add_section( 'minimized_bar', [
    'title' => __('Minimized Bar', 'skytake'),
    'panel' => 'features',
]);

$panel->add_section( 'social_media', [
    'title' => __('Social Media', 'skytake'),
    'panel' => 'features',
]);

// $panel->add_section( 'urgency', [
//     'title' => __('Urgency (Countdown Timer)', 'skytake'),
//     'panel' => 'features',
// ]);

// integrations
$panel->add_panel( 'integrations', [
    'title' => __( 'Integrations', 'skytake'),
]);

$panel->add_section( 'woocommerce', [
    'title' => __('Woocommerce', 'skytake'),
    'panel' => 'integrations',
]);

$panel->add_section( 'mailchimp', [
    'title' => __('Mailchimp', 'skytake'),
    'panel' => 'integrations',
]);

require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/welcome-email.php';
require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/filters.php';
require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/minimized-bar.php';
// require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/urgency.php';
require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/social-media.php';
require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/woocommerce.php';
require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/mailchimp.php';


/**
 * General Settings
**/
$panel->add_setting( 'campaign_name', [
    'section' => 'general_settings',
    'output' => HTML::input(array( 
        'title' => __('Campaign Name', 'skytake'),
        'name' => 'campaign_name', 
        'value' =>  $campaign->name(),
    ))
]);

$panel->add_setting( 'skytake_reminder_sub', [
    'section' => 'general_settings',
    'output' => HTML::twoInputs(
        array(
            'title' => __('Subscription reminder ( subscribe )', 'skytake'),
            'description' => __('Sometime a subscriber unsubscribe. Setting a reminded after an amount of time is a smart idea.', 'skytake'),
        ),
        HTML::number(array(
            'name' => 'reminder_number_sub', 
            'value' => $campaign->setting('reminder_number_sub'),
            'min' => 1,
            'wrapper' => false,
        )),
        HTML::span(__('Day(s)', 'skytake'))
    )
]);


/**
 * Section Templates
**/
$panel->add_setting( 'template', [
    'section' => 'templates',
    'output' => HTML::radio_images(array( 
        'title' => __('Select Template', 'skytake'),
        'name' => 'template',
        'value' => $campaign->setting('template'),
        'items' => array(
            'bubble_sky' => array(
                'url' => SKYTAKE_URL . '/assets/img/templates/popup/default.png',
                'title' => __('Bubble Sky', 'skytake'),
            ),
            // 'black_friday__premium_only' => array(
            //     'url' => SKYTAKE_URL . '/assets/img/templates/popup/cyber_monday.png',
            //     'title' => __('Black Friday', 'skytake'),
            // ),
            // 'cyber_monday__premium_only' => array(
            //     'url' => SKYTAKE_URL . '/assets/img/templates/popup/default.png',
            //     'title' => __('Cyber Monday', 'skytake'),
            // ),
            // 'christmas__premium_only' => array(
            //     'url' => SKYTAKE_URL . '/assets/img/templates/popup/default.png',
            //     'title' => __('Christmas', 'skytake'),
            // ),
            // 'valentines__premium_only' => array(
            //     'url' => SKYTAKE_URL . '/assets/img/templates/popup/default.png',
            //     'title' => __('Valentine\'s Day', 'skytake'),
            // ),
            // 'womens_day__premium_only' => array(
            //     'url' => SKYTAKE_URL . '/assets/img/templates/popup/default.png',
            //     'title' => __('Women\'s Day', 'skytake'),
            // ),
            // 'mens_day__premium_only' => array(
            //     'url' => SKYTAKE_URL . '/assets/img/templates/popup/default.png',
            //     'title' => __('Men\'s Day', 'skytake'),
            // ),
        )
    ))
]);


/**
 * Section Optin
**/
$panel->add_setting( 'main_image', [
    'section' => 'optin',
    'output' => HTML::image_upload(array( 
        'title' => $is_vertical_skin ? __('Header Image') : __('Side Image', 'skytake'),
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

/**
 * Section Success
 */
$panel->add_setting( 'title_after_sub', [
    'section' => 'success',
    'output' => HTML::input(array( 
        'title' => __('Title', 'skytake'),
        'name' => 'title_after_sub',
        'value' => $campaign->setting('title_after_sub'),
    ))
]);

$panel->add_setting( 'message_after_sub', [
    'section' => 'success',
    'output' => HTML::textarea(array( 
        'title' => __('Message', 'skytake'),
        'name' => 'message_after_sub',
        'value' => $campaign->setting('message_after_sub'),
    ))
]);


/**
 * Section Style
**/
$panel->add_setting( 'layout', [
    'class' => 'skytake_layout_container' . ($is_vertical_skin ? ' __hidden' : ''),
    'section' => 'style',
    'output' => HTML::radio_images(array( 
        'title' => __('Layout', 'skytake'),
        'name' => 'layout',
        'value' => $campaign->setting('layout'),
        'items' => array(
            'left' => [ 
                'title' => __('Left', 'skytake'), 
                'url' => SKYTAKE_URL . '/assets/img/layout-left.jpg'
            ],
            'right' => [ 
                'title' => __('Right', 'skytake'), 
                'url' => SKYTAKE_URL . '/assets/img/layout-right.jpg'
            ],
        )
    ))
]);

$panel->add_setting( 'overlay_color', [
    'section' => 'style',
    'output' => HTML::colorPicker(array( 
        'title' => __('Overlay Color', 'skytake'),
        'name' => 'overlay_color',
        'value' => $campaign->setting('overlay_color'),
    ))
]); 

$panel->add_setting( 'font_family', [
    'section' => 'style',
    'output' => HTML::select(array( 
        'title' => __('Font Family', 'skytake'),
        'name' => 'font_family',
        'value' => $campaign->setting('font_family'),
        'items' => skytake()->defaultConfig['font_items'],
    ))
]);

$panel->add_setting( 'title_font_size', [
    'section' => 'style',
    'output' => HTML::number(array( 
        'title' => __('Title Font Size (px)', 'skytake'),
        'name' => 'title_font_size',
        'value' => $campaign->setting('title_font_size'),
    ))
]);

$panel->add_setting( 'title_color', [
    'section' => 'style',
    'output' => HTML::colorPicker(array( 
        'title' => __('Title Color', 'skytake'),
        'name' => 'title_color',
        'value' => $campaign->setting('title_color'),
    ))
]);

$panel->add_setting( 'body_font_size', [
    'section' => 'style',
    'output' => HTML::number(array( 
        'title' => __('Body Font Size (px)', 'skytake'),
        'name' => 'body_font_size',
        'value' => $campaign->setting('body_font_size'),
    ))
]);

$panel->add_setting( 'body_color', [
    'section' => 'style',
    'output' => HTML::colorPicker(array( 
        'title' => __('Body Text Color', 'skytake'),
        'name' => 'body_color',
        'value' => $campaign->setting('body_color'),
    ))
]);

$panel->add_setting( 'email_color', [
    'section' => 'style',
    'output' => HTML::colorPicker(array( 
        'title' => __('Email Input Text Color', 'skytake'),
        'name' => 'email_color',
        'value' => $campaign->setting('email_color'),
    ))
]);

$panel->add_setting( 'email_bg_color', [
    'section' => 'style',
    'output' => HTML::colorPicker(array( 
        'title' => __('Email Input Background Color', 'skytake'),
        'name' => 'email_bg_color',
        'value' => $campaign->setting('email_bg_color'),
    ))
]);

$panel->add_setting( 'input_font_size', [
    'section' => 'style',
    'output' => HTML::number(array( 
        'title' => __('Email Input Font Size (px)', 'skytake'),
        'name' => 'input_font_size',
        'value' => $campaign->setting('input_font_size'),
    ))
]);

$panel->add_setting( 'submit_color', [
    'section' => 'style',
    'output' => HTML::colorPicker(array( 
        'title' => __('Button Color', 'skytake'),
        'name' => 'submit_color',
        'value' => $campaign->setting('submit_color'),
    ))
]);

$panel->add_setting( 'submit_bg_color', [
    'section' => 'style',
    'output' => HTML::colorPicker(array( 
        'title' => __('Button Background color', 'skytake'),
        'name' => 'submit_bg_color',
        'value' => $campaign->setting('submit_bg_color'),
    ))
]);

$panel->add_setting( 'close_icon_color', [
    'section' => 'style',
    'output' =>  HTML::colorPicker(array( 
        'title' => __('Close Icon Color', 'skytake'),
        'name' => 'close_icon_color',
        'value' => $campaign->setting('close_icon_color'),
    ))
]);

$panel->add_setting( 'close_icon_bg_color', [
    'section' => 'style',
    'output' => HTML::colorPicker(array( 
        'title' => __('Close Icon Background Color', 'skytake'),
        'name' => 'close_icon_bg_color',
        'value' => $campaign->setting('close_icon_bg_color'),
    ))
]);

$panel->add_setting( 'body_bg_color', [
    'section' => 'style',
    'output' => HTML::colorPicker(array( 
        'title' => __('Background Color', 'skytake'),
        'name' => 'body_bg_color',
        'value' => $campaign->setting('body_bg_color'),
    ))
]);

$panel->add_setting( 'body_bg_image', [
    'section' => 'style',
    'output' => HTML::image_upload(array( 
        'title' => __('Background Image', 'skytake'),
        'name' => 'body_bg_image',
        'value' => $campaign->setting('body_bg_image'),
        'placeholder' => __('Select/Upload image', 'skytake'),
    ))
]);

$panel->add_setting( 'body_bg_position', [
    'section' => 'style',
    'output' => HTML::select(array( 
        'title' => __('Background Image Position', 'skytake'),
        'name' => 'body_bg_position',
        'value' => $campaign->setting('body_bg_position'),
        'items' => array(
            'left top' => 'left top',
            'left center' => 'left center',
            'left bottom' => 'left bottom',
            'right top' => 'right top',
            'right center' => 'right center',
            'right bottom' => 'right bottom',
            'center top' => 'center top',
            'center center' => 'center center',
            'center bottom' => 'center bottom',
        ),
    ))
]);

$panel->add_setting( 'body_bg_size', [
    'section' => 'style',
    'output' => HTML::select(array( 
        'title' => __('Background Image Size', 'skytake'),
        'name' => 'body_bg_size',
        'value' => $campaign->setting('body_bg_size'),
        'items' => array(
            'auto' => 'auto',
            'cover' => 'cover',
            'inherit' => 'inherit',
            'contain' => 'contain',
        ),
    ))
]);

$panel->add_setting( 'body_bg_repeat', [
    'section' => 'style',
    'output' => HTML::select(array( 
        'title' => __('Background Image Repeat', 'skytake'),
        'name' => 'body_bg_repeat',
        'value' => $campaign->setting('body_bg_repeat'),
        'items' => array(
            'inherit' => 'inherit',
            'no-repeat' => 'no-repeat',
            'repeat' => 'repeat',
            'repeat-x' => 'repeat-x',
            'repeat-y' => 'repeat-y',
            'round' => 'round',
            'space' => 'space',
        ),
    ))
]);

return $panel;
