<?php

use SKYT\Panel;
use Delabon\WP\HTML;

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


require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/general-settings.php';
require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/welcome-email.php';
require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/filters.php';
require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/minimized-bar.php';
// require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/urgency.php';
require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/woocommerce.php';
require_once SKYTAKE_PATH . '/inc/views/editor/panel-settings/mailchimp.php';


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
            // 'halloween__premium_only' => array(
            //     'url' => SKYTAKE_URL . '/assets/img/templates/popup/halloween.png',
            //     'title' => __('Halloween', 'skytake'),
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
$panel->add_setting( 'title', [
    'section' => 'optin',
    'output' => HTML::input(array( 
        'title' => __('Title', 'skytake'),
        'name' => 'title',
        'value' => $campaign->setting('title'),
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


/**
 * Section Success
**/
$panel->add_setting( 'title_after_sub', [
    'section' => 'success',
    'output' => HTML::input(array( 
        'title' => __('Title', 'skytake'),
        'name' => 'title_after_sub',
        'value' => $campaign->setting('title_after_sub'),
    ))
]);


/**
 * Style
**/
$panel->add_setting( 'animation', [
    'section' => 'style',
    'output' => HTML::select(array( 
        'title' => __('Display Animation', 'skytake'),
        'name' => 'animation',
        'value' => $campaign->setting('animation'),
        'items' => array(
            'none' => __( 'None', 'skytake' ),
            'fade' => __( 'Fade', 'skytake' ),
            'scale' => __( 'Scale out', 'skytake'  ),
            'scale_in' => __( 'Scale in', 'skytake'  ),
            'slide_top' => __( 'Slide in from top', 'skytake'  ),
            'slide_bottom' => __( 'Slide in from bottom', 'skytake'  ),
            'slide_right' => __( 'Slide in from right', 'skytake'  ),
            'slide_left' => __( 'Slide in from left', 'skytake'  ),
        ),
    ))
]);  

$panel->add_setting( 'floating_bar_position', [
    'section' => 'style',
    'output' => HTML::select(array( 
        'title' => __('Position', 'skytake'),
        'name' => 'floating_bar_position',
        'value' => $campaign->setting('floating_bar_position'),
        'items' => [
            'top' => __('Top', 'skytake'),
            'bottom' => __('Bottom', 'skytake'),
        ]
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
