<?php 

return array(

    'fonts' => array(
        "theme_font"        => [ '', '' ],
        "roboto"            => [ 'Roboto:400,700', "'Roboto', sans-serif" ],
        "open_sans"         => [ 'Open+Sans:400,700', "'Open Sans', sans-serif" ],
        "lato"              => [ 'Lato:400,700', "'Lato', sans-serif" ],
        "oswald"            => [ 'Oswald:400,700', "'Oswald', sans-serif" ],
        "source_sans_pro"   => [ 'Source+Sans+Pro:400,700', "'Source Sans Pro', sans-serif" ],
        "montserrat"        => [ 'Montserrat:400,700', "'Montserrat', sans-serif" ],
        "raleway"           => [ 'Raleway:400,700', "'Raleway', sans-serif" ],
        "pt_sans"           => [ 'PT+Sans:400,700', "'PT Sans', sans-serif" ],
        "lora"              => [ 'Lora:400,700', "'Lora', serif" ],
    ),

    'font_items' => array(
        "theme_font"        => __('Use theme font', 'skytake'),
        "roboto"            => 'Roboto',
        "open_sans"         => 'Open Sans',
        "lato"              => 'Lato',
        "oswald"            => 'Oswald',
        "source_sans_pro"   => 'Source Sans Pro',
        "montserrat"        => 'Montserrat',
        "raleway"           => 'Raleway',
        "pt_sans"           => 'PT Sans',
        "lora"              => 'Lora',
    ),

    'campaign_types' => array(
        'none'              => __('Select Type', 'skytake'),
        'popup'             => __('Lightbox Popup', 'skytake'),
        'floating-bar'      => __('Floating Bar', 'skytake'),
        'scroll-box'      => __('Slide-in Scroll Box', 'skytake'),
        'widget-form'       => __('Widget Form', 'skytake'),
        'content-form'      => __('Form Inside Content', 'skytake'),
    ),

    // Campaign Settings
    'campaign' => array(

        // general settings
        'display_trigger'       => 'exit',
        'display_after_x_seconds'       => 2,
        'scrolling_down_percentage'     => 30,
        'reminder_number_notsub'        => 30,
        'reminder_type_notsub'          => 'minute',
        'reminder_number_sub'           => 90,

        // optin
        'title'                     => 'Title Goes Here',
        'title_after_sub'           => 'Check Your Inbox!',
        'email_input_text'          => 'Your email address (*)',
        'submit_button_text'        => 'Subscribe',
        'is_input_name_enabled'     => 0,
        'is_mobile_input_enabled'   => 0,
        'name_input_text'           => 'Your name',
        'input_mobile_text'         => 'Your mobile',
        'message'                   => 'Subscribe now to receive your free gift immediately!',
        'message_after_sub'         => "We've just sent you the gift to your inbox.",
        'spam_text'                 => 'Spam free!',
        'is_gdpr_enabled'           => 1,
        'gdpr_text'                 => 'I agree with the <a href="#">terms</a> and <a href="#">privacy policy</a>.',

        // filters
        'is_loggedin_enabled'       => 1,
        'is_administrators_enabled' => 1,
        'filter_type'               => 'show',
        'filter_pages_excluded'     => '',
        'filter_hide_on_articles'   => 0,

        // minimized bar
        'display_minimized_bar'     => 'close',
        'minimized_bar_position'    => 'bottom_right',
        'minimized_bar_icon'        => 'dlbgifticon-1',
        'minimized_bar_size'        => 40, 
        'minimized_bar_color'       => '#ffffff',
        'minimized_bar_bg_color'    => '#800080',

        // woocommerce
        'is_coupon_enabled'             => 0,
        'selected_coupon'               => 0,

        // urgency
        'urgency_type'                  => 'disabled',
        'urgency_expire_date'           => '',
        'urgency_usage_limit'           => 25,
        'urgency_session_time'          => 2,
        'urgency_session_type'          => 'hour',
        'urgency_session_pause_time'    => 1,
        'urgency_color'                 => '#ffffff',
        'urgency_bg_color'              => '#D4145A',
        'urgency_font_size'             => 14,

        // mailchimp
        'is_mailchimp_enabled' => 0,
        'is_mailchimp_campaign_enabled' => 0,
        'mailchimp_apikey' => '',
        'mailchimp_listid' => 0,
        'mailchimp_campaign_url' => '',

        // social media
        'social_open_link_new_tab'  => 1,
        'social_icon_icon'          => 'dlbicons-facebook-small-radius',
        'social_icon_size'          => 20,
        'social_color_type'         => 'default',
        'social_icon_color'         => '#555555',
        'social_icon_facebook'      => '',
        'social_icon_twitter'       => '',
        'social_icon_linkedin'      => '',
        'social_icon_instagram'     => '',
        'social_icon_pinterest'     => '',
        'social_icon_youtube'       => '',

        // email 
        'is_welcome_email_enabled'  => 1,
        'welcome_email_subject'     => 'Thank you for subscribing',
        'welcome_email_content'     => "
Take the time to change this welcome email.
Include the gift URL or if you are using Woocommerce use the shortcodes to
include the coupon code and the store button ex: [store_button].",
        
        // style
        'template'              => 'bubble_sky',
        'layout'                => 'fullwidth',
        'animation'             => 'none',
        'overlay_color'         => 'rgba(0, 0, 0, 0.8)',
        'font_family'           => 'roboto',
        'title_font_size'      => 22,
        'body_font_size'        => 16,
        'input_font_size'       => 16,
        'title_color'           => '#000000',
        'body_color'            => '#2e2e2e',
        'body_bg_color'         => '#F8F8F8',
        'body_bg_image'         => '',
        'body_bg_repeat'        => 'no-repeat',
        'body_bg_size'          => 'auto',
        'body_bg_position'      => 'center center',
        'email_color'           => '#666666',
        'email_bg_color'        => '#ffffff',
        'submit_color'          => '#ffffff',
        'submit_bg_color'       => '#D4145A',
        'close_icon_color'      => '#000000',
        'close_icon_bg_color'   => '#cccccc',

        // custom settings
        'floating_bar_position' => 'top',
        'scroll_box_position'   => 'bottom_right',
    )

);
