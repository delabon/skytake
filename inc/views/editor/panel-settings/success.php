<?php

use Delabon\WP\HTML;


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