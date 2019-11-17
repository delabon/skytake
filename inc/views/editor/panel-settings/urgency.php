<?php

use Delabon\WP\HTML;

/**
 * Section Urgency
 */
$panel->add_setting( 'urgency_type', [
    'section' => 'urgency',
    'output' => HTML::select(array( 
        'title' => __('Urgency Type', 'skytake'),
        'name' => 'urgency_type',
        'value' => $urgency_type,
        'description' => __('"Visitor Session", each visitor has urgency session time (Best).', 'skytake'),
        'items' => [
            'disabled'      => __('Disabled', 'skytake'),
            'session'       => __('Visitor session', 'skytake'),
            'expiry_date'   => __('Expiry date', 'skytake'),
            'usage_limit'   => __('Coupon usage limit', 'skytake'),
        ],
        'wrapper_class' => '__premium_wrapper'
    ))
]);

$panel->add_setting( 'skytake_urgency_session', [
    'class' => 'skytake_urgency_session_container' . ( $urgency_type === 'session' ? '' : ' __hidden' ),
    'section' => 'urgency',
    'output' => HTML::twoInputs(
        array(
            'title' => __('Session time', 'skytake'),
        ),
        HTML::number(array(
            'name' => 'urgency_session_time', 
            'value' =>  $campaign->setting('urgency_session_time'),
            'min' => 1,
            'wrapper' => false,
        )),
        HTML::select(array( 
            'name' =>  'urgency_session_type',
            'value' =>  $campaign->setting('urgency_session_type'),
            'items' => array(
                'minute' => __('Minute(s)', 'skytake'),
                'hour' => __('Hour(s)', 'skytake'),
                'day' => __('Day(s)', 'skytake'),
            ),
            'wrapper' => false,
        ))
    )
]);

$panel->add_setting( 'skytake_urgency_session_recreate', [
    'class' => 'skytake_urgency_session_recreate_container' . ( $urgency_type === 'session' ? '' : ' __hidden' ),
    'section' => 'urgency',
    'output' => HTML::twoInputs(
        array(
            'title' => __('Re-create session after', 'skytake'),
        ),
        HTML::input(array(
            'name' => 'urgency_session_pause_time', 
            'value' =>  $campaign->setting('urgency_session_pause_time'),
            'wrapper' => false,
        )),
        HTML::span(__('Day(s)', 'skytake'))
    )
]);

$panel->add_setting( 'urgency_expire_date', [
    'class' => 'urgency_expire_date_container' . ( $urgency_type === 'expiry_date' ? '' : ' __hidden' ),
    'section' => 'urgency',
    'output' => HTML::datepicker(array( 
        'title' => __('Expiry date', 'skytake'),
        'name' => 'urgency_expire_date', 
        'value' => $expiry_date,
        'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
        'placeholder' => 'YYYY-MM-DD',
        'description' => __('Set expiry date.', 'skytake'),
    ))
]);

$panel->add_setting( 'urgency_usage_limit', [
    'class' => 'urgency_usage_limit_container' . ( $urgency_type === 'usage_limit' ? '' : ' __hidden' ),
    'section' => 'urgency',
    'output' => HTML::number(array( 
        'title' => __('Coupon usage limit (Only Woocommerce)', 'skytake'),
        'name' => 'urgency_usage_limit', 
        'value' => $usage_limit,
        'min' => 1,
        'novalidate' => true,
        'description' => __('Set usage limit before coupon expires.', 'skytake'),
    ))
]);

$panel->add_setting( 'urgency_font_size', [
    'section' => 'urgency',
    'output' => HTML::number(array( 
        'title' => __('Font Size (px)', 'skytake'),
        'name' => 'urgency_font_size',
        'value' => $campaign->setting('urgency_font_size'),
    ))
]);

$panel->add_setting( 'urgency_color', [
    'section' => 'urgency',
    'output' => HTML::colorPicker(array( 
        'title' => __('Text Color', 'skytake'),
        'name' => 'urgency_color',
        'value' => $campaign->setting('urgency_color'),
    ))
]);

$panel->add_setting( 'urgency_bg_color', [
    'section' => 'urgency',
    'output' => HTML::colorPicker(array( 
        'title' => __('Background Color', 'skytake'),
        'name' => 'urgency_bg_color',
        'value' => $campaign->setting('urgency_bg_color'),
    ))
]);
