<?php

use Delabon\WP\HTML;

/**
 * Filters 
**/
$panel->add_setting( 'is_loggedin_enabled', [
    'section' => 'filters',
    'output' => HTML::checkbox(array( 
        'title' => __('Logged-in users', 'skytake'),
        'name' =>  'is_loggedin_enabled',
        'description' => __('Enable to show coupon popup for all logged-in users', 'skytake'),
        'text' => __('Enable', 'skytake'),
        'value' =>  $campaign->setting('is_loggedin_enabled'),
    ))
]);

$panel->add_setting( 'is_administrators_enabled', [
    'section' => 'filters',
    'output' => HTML::checkbox(array( 
        'title' => __('Administrator', 'skytake'),
        'name' =>  'is_administrators_enabled',
        'description' => __('Enable to show coupon popup for administrator(s)', 'skytake'),
        'text' => __('Enable', 'skytake'),
        'value' =>  $campaign->setting('is_administrators_enabled'),
    ))
]);

$panel->add_setting( 'filter_hide_on_articles', [
    'section' => 'filters',
    'output' => HTML::checkbox(array( 
        'title' => __('Hide on all articles', 'skytake'),
        'text' => __('Enable','skytake'),
        'name' => 'filter_hide_on_articles', 
        'value' => $campaign->setting('filter_hide_on_articles'),    
    ))
]);

$panel->add_setting( 'filter_type', [
    'section' => 'filters',
    'output' => HTML::select(array( 
        'title' => __('Show / Hide on all pages', 'skytake'),
        'description' => __( "Add the pages exluded below.",'skytake'),
        'name' =>  'filter_type', 
        'value' =>  $campaign->setting('filter_type'),
        'items' => array(
            'show' => __('Show','skytake'),
            'hide' => __('Hide','skytake'),
        ),
    ))
]);

$panel->add_setting( 'filter_pages_excluded', [
    'section' => 'filters',
    'output' => HTML::textarea(array( 
        'title' => __('Pages Excluded', 'skytake'),
        'description' => __( "Add the excluded pages URL's here. ex:",'skytake') . '<br> http://mysite.com, http://mysite.com/product/hoodie-with-zipper/',
        'name' =>  'filter_pages_excluded', 
        'value' => $campaign->setting('filter_pages_excluded'),    
        'placeholder' => 'http://mysite.com, http://mysite.com/product/hoodie-with-zipper/',
    ))
]);
