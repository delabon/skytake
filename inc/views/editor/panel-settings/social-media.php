<?php

use Delabon\WP\HTML;

/**
 * Social Media 
**/
$panel->add_setting( 'social_open_link_new_tab', [
    'section' => 'social_media',
    'output' => HTML::checkbox(array( 
        'title' => __('Open Links In New Tabs', 'skytake'),
        'name' => 'social_open_link_new_tab',
        'value' => $campaign->setting('social_open_link_new_tab'),
    ))
]);

$panel->add_setting( 'social_icon_icon', [
    'section' => 'social_media',
    'output' => HTML::radio_icons(array( 
        'title' => __('Icons Design', 'skytake'),
        'name' => 'social_icon_icon',
        'value' => $campaign->setting('social_icon_icon'),
        'items' => skytake()->icons['facebook'],
    ))
]);

$panel->add_setting( 'social_icon_size', [
    'section' => 'social_media',
    'output' => HTML::number(array( 
        'title' => __('Icons Size (px)', 'skytake'),
        'name' => 'social_icon_size',
        'value' => $campaign->setting('social_icon_size'),
    ))
]);

$panel->add_setting( 'social_color_type', [
    'section' => 'social_media',
    'output' => HTML::select(array( 
        'title' => __('Social Icons Color Type', 'skytake'),
        'name' => 'social_color_type',
        'value' => $campaign->setting('social_color_type'),
        'items' => array(
            'default'  => __('Default Colors', 'skytake'),
            'custom' => __('Custom Color', 'skytake'),
        )
    ))
]);

$panel->add_setting( 'social_icon_color', [
    'section' => 'social_media',
    'output' => HTML::colorPicker(array( 
        'title' => __('Social Icons Color', 'skytake'),
        'name' => 'social_icon_color',
        'value' => $campaign->setting('social_icon_color'),
    ))
]);

$panel->add_setting( 'social_icon_facebook', [
    'section' => 'social_media',
    'output' => HTML::input(array( 
        'title' => __('Facebook Username', 'skytake'),
        'description' => 'ex: delabonWP',
        'name' => 'social_icon_facebook',
        'value' => $campaign->setting('social_icon_facebook'),
    ))
]);

$panel->add_setting( 'social_icon_twitter', [
    'section' => 'social_media',
    'output' => HTML::input(array( 
        'title' => __('Twitter Username', 'skytake'),
        'description' => 'ex: delabonWP',
        'name' => 'social_icon_twitter',
        'value' => $campaign->setting('social_icon_twitter'),
    ))
]);

$panel->add_setting( 'social_icon_instagram', [
    'section' => 'social_media',
    'output' => HTML::input(array( 
        'title' => __('Instagram Username', 'skytake'),
        'description' => 'ex: delabonWP',
        'name' => 'social_icon_instagram',
        'value' => $campaign->setting('social_icon_instagram'),
    ))
]);

$panel->add_setting( 'social_icon_pinterest', [
    'section' => 'social_media',
    'output' => HTML::input(array( 
        'title' => __('Pinterest Username', 'skytake'),
        'description' => 'ex: delabonWP',
        'name' => 'social_icon_pinterest',
        'value' => $campaign->setting('social_icon_pinterest'),
    ))
]);

$panel->add_setting( 'social_icon_linkedin', [
    'section' => 'social_media',
    'output' => HTML::input(array( 
        'title' => __('Linkedin Username', 'skytake'),
        'description' => 'ex: delabonWP',
        'name' => 'social_icon_linkedin',
        'value' => $campaign->setting('social_icon_linkedin'),
    ))
]);

$panel->add_setting( 'social_icon_youtube', [
    'section' => 'social_media',
    'output' => HTML::textarea(array( 
        'title' => __('Youtube URL', 'skytake'),
        'description' => 'ex: https://www.youtube.com/channel/UC9_a-pSWIzM5uq1EbAYQRXA',
        'name' => 'social_icon_youtube',
        'value' => $campaign->setting('social_icon_youtube'),
    ))
]);