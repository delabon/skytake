<?php

use Delabon\WP\HTML;

use function SKYT\upgrade_tag;

/**
 * Minimized Bar 
**/
$panel->add_setting( 'display_minimized_bar', [
    'section' => 'minimized_bar',
    'output' => HTML::select(array( 
        'title' => __('Display minimized bar', 'skytake'),
        'name' =>  'display_minimized_bar',
        'value' =>  $campaign->setting('display_minimized_bar'),
        'items' => array(
            'close' => __('When close', 'skytake'),
            'never' => __('Never', 'skytake'),
            'always' => __('Always', 'skytake') . upgrade_tag(),
        ),
    ))
]);

$panel->add_setting( 'minimized_bar_position', [
    'section' => 'minimized_bar',
    'output' => HTML::select(array( 
        'title' => __('Position', 'skytake'),
        'name' => 'minimized_bar_position',
        'value' => $campaign->setting('minimized_bar_position'),
        'items' => array(
            'bottom_right' => __('Bottom Right', 'skytake'),
            'bottom_left' => __('Bottom Left', 'skytake'),
            'top_right' => __('Top Right', 'skytake'),
            'top_left' => __('Top Left', 'skytake'),
        )
    ))
]);

$panel->add_setting( 'minimized_bar_size', [
    'section' => 'minimized_bar',
    'output' => HTML::number(array( 
        'title' => __('Size (px)', 'skytake'),
        'name' => 'minimized_bar_size',
        'value' => $campaign->setting('minimized_bar_size'),
    ))
]);

$panel->add_setting( 'minimized_bar', [
    'section' => 'minimized_bar',
    'output' => HTML::radio_icons(array( 
        'title' => __('Select Icon', 'skytake'),
        'name' => 'minimized_bar_icon',
        'value' => $campaign->setting('minimized_bar_icon'),
        'items' => skytake()->icons['gifts']
    ))
]);

$panel->add_setting( 'minimized_bar_color', [
    'section' => 'minimized_bar',
    'output' => HTML::colorPicker(array( 
        'title' => __('Icon Color', 'skytake'),
        'name' => 'minimized_bar_color',
        'value' => $campaign->setting('minimized_bar_color'),
    ))
]);

$panel->add_setting( 'minimized_bar_bg_color', [
    'section' => 'minimized_bar',
    'output' => HTML::colorPicker(array( 
        'title' => __('Minimized Bar Background Color', 'skytake'),
        'name' => 'minimized_bar_bg_color',
        'value' => $campaign->setting('minimized_bar_bg_color'),
    ))
]);
