<?php

use Delabon\WP\HTML;

use function SKYT\upgrade_tag;

/**
 * Section General Settings
**/
$panel->add_setting( 'campaign_name', [
    'section' => 'general_settings',
    'output' => HTML::input(array( 
        'title' => __('Campaign Name', 'skytake'),
        'name' => 'campaign_name', 
        'value' =>  $campaign->name(),
    ))
]);

$panel->add_setting( 'display_trigger', [
    'section' => 'general_settings',
    'output' => HTML::select(array( 
        'title' => __('Popup trigger', 'skytake'),
        'name' => 'display_trigger',
        'value' => $campaign->setting('display_trigger'),
        'items' => array(
            'exit' => __('When a visitor is about to exit.', 'skytake'),
            'load' => __('After X seconds.', 'skytake'),
            'scroll' => __('After scrolling down X amount.', 'skytake') . upgrade_tag(),
            'random' => __('Random.', 'skytake') . upgrade_tag(),
        ),
    ))
]);

$panel->add_setting( 'display_after_x_seconds', [
    'class' => 'display_after_x_seconds_container' . ( in_array( $trigger_type, [ 'load', 'random' ] ) ? '' : ' __hidden'),
    'section' => 'general_settings',
    'output' => HTML::input(array( 
        'title' => __('Display popup after', 'skytake'),
        'name' => 'display_after_x_seconds', 
        'value' =>  $campaign->setting('display_after_x_seconds'),
        'description' => __('Set how many seconds to wait before displaying the popup.', 'skytake'),
    ))
]);

$panel->add_setting( 'scrolling_down_percentage', [
    'class' => 'scrolling_down_percentage_container' . ( in_array( $trigger_type, [ 'scroll', 'random' ] ) ? '' : ' __hidden'),
    'section' => 'general_settings',
    'output' => HTML::number(array( 
        'title' => __('Vertical scroll percentage', 'skytake') . upgrade_tag(),
        'name' => 'scrolling_down_percentage', 
        'value' =>  $campaign->setting('scrolling_down_percentage'),
        'min' => 1,
    ))
]);

$panel->add_setting( 'skytake_reminder_notsub', [
    'section' => 'general_settings',
    'output' => HTML::twoInputs(
        array(
            'title' => __('Subscription reminder ( Not subscribe )', 'skytake'),
            'description' => __('Time to show popup again if visitor does not subscribe.', 'skytake'),
        ),
        HTML::number(array(
            'name' => 'reminder_number_notsub', 
            'value' =>  $campaign->setting('reminder_number_notsub'),
            'wrapper' => false,
            'min' => 1,
        )),
        HTML::select(array( 
            'name' =>  'reminder_type_notsub',
            'value' =>  $campaign->setting('reminder_type_notsub'),
            'items' => array(
                'minute' => __('Minute(s)', 'skytake'),
                'hour' => __('Hour(s)', 'skytake'),
                'day' => __('Day(s)', 'skytake'),
            ),
            'wrapper' => false,
        ))
    )
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
            'value' =>  $campaign->setting('reminder_number_sub'),
            'min' => 1,
            'wrapper' => false,
        )),
        HTML::span(__('Day(s)', 'skytake'))
    )
]);
