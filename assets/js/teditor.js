/**
 * Exit confirmation
 * @param {object} settings 
 */
function skytake_exit_confirm( settings ){
    
    if( Object.keys( settings ).length ){
        if( ! confirm( 'The changes you made will be lost if you navigate away from this page.') ){
            return false;
        }
    }

    return true;
}

/**
 * Fetches the mailchimp audience list by api_key
**/
function skytake_set_mailchimp_lists(){

    teditor.ajax('get_mailchimp_list_items', {
        api_key: teditor.getValue('mailchimp_apikey'),
    }).done(function( response ){

        if( ! response.success ){
            return false;
        }

        var lists = response.data.lists;
        var el = teditor.getElement('mailchimp_listid');
        var list = el.parent().find('.__list');

        el.html('');
        list.html('');

        Object.keys( lists ).map(function( key ){

            el.append('<option value="'+key+'">'+lists[ key ]+'</option>');
            list.append('<div data-value="'+key+'">'+lists[ key ]+'</div>');
        });
        
        // dont use the teditor api here
        el.val( response.data.selected ).trigger('change');
        teditor.save_el.attr('disabled', 'disabled');
    });
}

/**
 * Get markup and template settings
 * @param {mixed} value 
 */
function skytake_change_template( value ){

    if( ! teditor_params.is_premium && value.search('premium_only') !== -1 ){
        teditor.upgrade_popup_el.show();
        return;
    }
    
    teditor.loader_el.show();
    teditor.body_el.find('#skytake_editor').attr('data-template', value);

    // load skin custom style
    var style_el = teditor.preview.html.find('#skytake_front_skin-css');
    var style_src = teditor_params.skins_url + value +'/css/skin.min.css';

    // get template markup
    teditor.ajax('get_preview_template_markup', {
        skin: value
    }).done(function( response ){
                
        if( ! response.success ){
            return false;
        }

        style_el.attr( 'href', style_src );
        teditor.preview.html.find('#skytake_front_skin-inline-css').remove();
        
        // replace popup markup
        teditor.preview.el.replaceWith( response.data.markup.campaign_markup );
        teditor.preview.el = teditor.preview.html.find('.skytake'); // required
        teditor.preview.el.attr('data-template', value ); // required
        
        // Set template settings
        Object.keys( response.data.settings ).map(function( key ){

            var el = teditor.getElement( key );
            
            if( el === undefined ){
                return null;
            }

            if( typeof response.data.settings[key] !== 'object' ){
                teditor.setValue( key, response.data.settings[key] );
            }
            else{
                
                if( response.data.settings[key].hasOwnProperty('value') ){
                    teditor.setValue( key, response.data.settings[key].value );
                }
                
                if( response.data.settings[key].hasOwnProperty('class') ){

                    var parent = el.closest('.skt__setting');

                    if( parent.data('class') == undefined ){
                        parent.attr('data-class', parent.attr('class') );
                    }
                    
                    parent.attr('class', parent.data('class') + ' ' + response.data.settings[key].class );
                }

                if( response.data.settings[key].hasOwnProperty('title') ){
                    el.closest('.skt__setting').find('.dlb_input_title').text( response.data.settings[key].title );
                }

                if( response.data.settings[key].hasOwnProperty('description') ){
                    el.closest('.skt__setting').find('.dlb_input_description').text( response.data.settings[key].description );
                }
            }

            // fixes wpColorPicker not updating
            if( el.closest('.dlb_input_colorpicker').length ){
                el.trigger('change');
            }
        });

        // reset controller
        teditor.preview.controller.off();
        delete teditor.preview.controller;
        
        setTimeout(function(){
            teditor.preview.controller = new TController( teditor.preview.html );
            teditor.loader_el.hide();            
        }, 200 );
    });

}

/**
 * Change google font
 * @param {string} value 
 */
function skytake_change_font( value ){
        
    var link_el = teditor.preview.html.find('#skytake_front_fonts-css');

    if( ! link_el.length ){
        teditor.preview.html.find('body').prepend('<link rel="stylesheet" id="skytake_front_fonts-css" type="text/css" media="all" href="https://fonts.googleapis.com/css?family=&display=swap">');
        link_el = teditor.preview.html.find('#skytake_front_fonts-css');
    }

    if( value === "theme_font" ){
        teditor.preview.el.css('font-family', 'initial' );
        return;        
    }

    var url = link_el.attr('href').replace( new RegExp("family=.*?&", "gm"), "family=" + teditor_params.fonts[ value ][ 0 ] + "&" );

    link_el.attr('href', url );

    setTimeout(function(){
        teditor.preview.el.css('font-family', teditor_params.fonts[ value ][ 1 ]);
    }, 100);
}

/**
 * Urgency type control
 * @param {string} value 
 */
function skytake_urgency_control( value ){

    var session_container = jQuery('.skytake_urgency_session_container');
    var session_recreate_container = jQuery('.skytake_urgency_session_recreate_container');
    var expiry_date_container = jQuery('.urgency_expire_date_container');
    var usage_limit_container = jQuery('.urgency_usage_limit_container');

    if( value === 'disabled'){
        session_container.addClass('__hidden');
        session_recreate_container.addClass('__hidden');
        expiry_date_container.addClass('__hidden');
        usage_limit_container.addClass('__hidden');
    }
    else if ( value === 'session' ){
        session_container.removeClass('__hidden');
        session_recreate_container.removeClass('__hidden');
        expiry_date_container.addClass('__hidden');
        usage_limit_container.addClass('__hidden');
    }
    else if ( value === 'expiry_date' ){
        session_container.addClass('__hidden');
        session_recreate_container.addClass('__hidden');
        expiry_date_container.removeClass('__hidden');
        usage_limit_container.addClass('__hidden');
    }
    else if ( value === 'usage_limit' ){
        session_container.addClass('__hidden');
        session_recreate_container.addClass('__hidden');
        expiry_date_container.addClass('__hidden');
        usage_limit_container.removeClass('__hidden');
    }
}

/**
 * Camapign name control
 * @param {string} value 
 */
function skytake_campaign_name_control( value ){
    jQuery('#skt_editor_bar > header .skt__name').text(value);
}

/**
 * Input name control
 * @param {string} value 
 */
function skytake_input_name_control( value ){
    var self = jQuery(this);
    var counter_el = teditor.preview.el.find('.skytake-form-more');

    if( value == 1 ){
        self.closest('li').next().removeClass('__hidden');
        teditor.preview.el.find('.skytake-name').addClass('__show');
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) + 1 );
    }
    else{
        self.closest('li').next().addClass('__hidden');
        teditor.preview.el.find('.skytake-name').removeClass('__show');
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) - 1 );
    }
}

/**
 * Input mobile control
 * @param {string} value 
 */
function skytake_input_mobile_control( value ){
    var self = jQuery(this);
    var counter_el = teditor.preview.el.find('.skytake-form-more');

    if( value == 1 ){
        self.closest('li').next().removeClass('__hidden');
        teditor.preview.el.find('.skytake-mobile').addClass('__show');
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) + 1 );
    }
    else{
        self.closest('li').next().addClass('__hidden');
        teditor.preview.el.find('.skytake-mobile').removeClass('__show');
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) - 1 );
    } 
}

/**
 * Input bg image control
 * @param {string} value 
 */
function skytake_bg_image_control( value ){

    if( value == '' ){
        teditor.preview.el.attr('data-background', 0 );    
        teditor.preview.el.find('.skytake-container').css('background-image', 'none');
    }
    else{
        teditor.preview.el.attr('data-background', 1 );    
        teditor.preview.el.find('.skytake-container').css('background-image', 'url('+value+')');
    }

}

/**
 * TEditor class
 */
var TEditor = function(){

    this.campaign_type = jQuery('input[name="skytake_campaign_type"]').val();

    this.body_el = jQuery('body');
    this.win_el = jQuery(window);
    this.loader_el = jQuery('#skytake_loader');

    this.panel_el = jQuery('#skt_editor_panel');
    this.lvl2_el = this.panel_el.find('.skt__level_2');
    this.lvl2_title_el = this.panel_el.find('.skt__level_2_title');
    this.back_el = this.lvl2_title_el.find('.__arrow');

    this.bar_el = jQuery('#skt_editor_bar');
    this.save_el = this.bar_el.find('.skt__btn_save');
    this.exit_el = this.bar_el.find('.skt__btn_exit');

    this.upgrade_popup_el = this.body_el.find('.skytake_upgrade_popup');
    this.exit_upgrade_popup_el = this.upgrade_popup_el.find('> span');

    this.iframe_el = jQuery('#skytake_editor_iframe iframe');

    this.preview = {};

    this.changed_settings = {};
    this.listener_list = {};

    this.init();
}

/**
 * Init
 */
TEditor.prototype.init = function(){

    var _p = this;

    /** Listen for iframe load */
    this.iframe_el.on('load', function(e){

        var iframe = _p.iframe_el[0];
        
        _p.preview.window = iframe.contentWindow ? iframe.contentWindow : iframe.contentDocument.defaultView;
        _p.preview.document = iframe.contentDocument || _p.iframe_window_el.document;
        
        _p.loader_el.hide();
        
        jQuery( _p.preview.document ).ready(function(){
            _p.preview.html = _p.iframe_el.contents();
                        
            if( teditor_params.campaign_type === 'popup' ){
                
                _p.preview.el = _p.preview.html.find('.skytake-popup');
                _p.preview.bar = _p.preview.html.find('.skytake-bar');
            }
            else if( teditor_params.campaign_type === 'floating-bar' ){
                
                _p.preview.el = _p.preview.html.find('.skytake-floating-bar');
                _p.preview.bar = _p.preview.html.find('.skytake-bar');
            }
            else if( teditor_params.campaign_type === 'scroll-box' ){
                
                _p.preview.el = _p.preview.html.find('.skytake-scroll-box');
                _p.preview.bar = _p.preview.html.find('.skytake-bar');
            }
            else if( teditor_params.campaign_type === 'content-form' ){
                
                _p.preview.el = _p.preview.html.find('.skytake-content-form');
            }
            else if( teditor_params.campaign_type === 'widget-form' ){
                
                _p.preview.el = _p.preview.html.find('.skytake-widget-form');
            }

            _p.preview.controller = new TController( _p.preview.html );

            _p.events();
            _p.register_listeners();
            _p.body_el.trigger('skytake_editor_loaded');
        });
    });

    /** Listen for upgrade popup close */
    this.exit_upgrade_popup_el.on('click', function(e){
        e.preventDefault();
        _p.upgrade_popup_el.hide();
    });

}

/**
 * Save editor setting event to fire later
 * 
 * @param {string} name 
 * @param {function} callback 
 */
TEditor.prototype.onChange = function( name, callback ){
    
    var _p = this;
    var data = {
        name: name,
        el: jQuery('.dlb_input .__input[name="'+name+'"]'),
        callback: callback || null,
    };

    if( data.el.is('select') ){
        data.event = 'change';                
    }
    else if( data.el.is('[type="radio"]') ){
        data.event = 'change';                
    }
    else {
        data.event = 'input';
    }

    _p.listener_list[ name ] = data;
}

/**
 * Set setting by name
 * 
 * @param {string} name 
 * @param {function} callback 
 */
TEditor.prototype.setValue = function( name, value ){
    
    var _p = this;   
    
    if( ! _p.listener_list.hasOwnProperty( name ) ){
        return;
    }

    var item = _p.listener_list[ name ];

    item.el.val( value ).trigger( item.event );
}

/**
 * Get the value of the setting
 * 
 * @param {string} name 
 * @param {function} callback 
 * @return {string}
 */
TEditor.prototype.getValue = function( name ){
    
    var _p = this;
    
    if( ! _p.listener_list.hasOwnProperty( name ) ){
        return;
    }

    return _p.listener_list[ name ].el.val();
}

/**
 * Get the jQuery element of the setting
 * 
 * @param {string} name 
 * @param {function} callback 
 * @return {string}
 */
TEditor.prototype.getElement = function( name ){
    
    var _p = this;
    
    if( ! _p.listener_list.hasOwnProperty( name ) ){
        return;
    }

    return _p.listener_list[ name ].el;
}

/**
 * Start listen for setting change
 */
TEditor.prototype.register_listeners = function(){    
            
    var _p = this;

    Object.keys( _p.listener_list ).map(function( key ){

        var setting = _p.listener_list[ key ];

        setting.el.on( setting.event, function( e ){
            var self = jQuery(this);
            var value = self.val();
            
            _p.changed_settings[ setting.name ] = value;
            _p.save_el.removeAttr('disabled' );

            if( setting.callback ){
                setting.callback.call( this, e, value );
            }
        });
    });

}

/**
 * Editor events
 */
TEditor.prototype.events = function(){

    var _p = this;

    /** Save button listener */
    _p.save_el.on('click', function( e ){
        e.preventDefault();
        
        _p.save_el.attr('disabled', 'disabled');
        _p.loader_el.show();

        _p.ajax( 'save_editor_settings', _p.changed_settings ).done(function( response ){
                            
            _p.changed_settings = {};
            _p.loader_el.hide();
        });
    });

    /** on window exit */
    _p.win_el.on('beforeunload', function( e ){
        if( ! skytake_exit_confirm( _p.changed_settings ) ){
            return false;
        }
    });

    /** Bar back click */
    _p.back_el.on('click', function(e){
        e.preventDefault();

        _p.panel_el.attr('data-level', '1');
        _p.bar_el.attr('data-level', '1');
        _p.lvl2_el.css('display', 'none');

        _p.preview.html.trigger('skytake_editor_tab_changed', '' );
    });

    /** Panel Tab click */
    _p.panel_el.find('.skt__level_1 li').on('click', function(e){
        var self = jQuery(this);
        var tab = self.data('target');

        _p.panel_el.attr('data-level', '2');
        _p.bar_el.attr('data-level', '2');

        _p.lvl2_title_el.find('.__text').text( self.data('title') );

        _p.lvl2_el.css('display', 'none')
            .filter( '.__target_' + tab ).css('display', 'block');

        _p.preview.html.trigger('skytake_editor_tab_changed', tab );
    });
}

/**
 * Wrapper for $.ajax
 * @param {String} action 
 * @param {Object} data 
 */
TEditor.prototype.ajax  = function ( action, data ){

    data = typeof data === 'object' ? data : {};
    data.action = 'skytake_' + action;
    data.nonce = jQuery('[name="skytake_nonce"]').val();
    data.campaign_id = jQuery('[name="skytake_campaign_id"]').val();
        
    return jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: data,
        async: true,
        cache: false,
    });
}

// exec
window.teditor = new TEditor();


/**
 * Common Settings
**/
teditor.onChange('campaign_status');
teditor.onChange('campaign_name', function( e, value ){
    skytake_campaign_name_control(value);    
});


/**
 * General Settings 
**/
teditor.onChange('display_trigger', function( e, value ){

    var display_after = jQuery('.display_after_x_seconds_container');
    var scroll_percentage = jQuery('.scrolling_down_percentage_container');

    if( value === 'random' ){
        display_after.removeClass('__hidden');
        scroll_percentage.removeClass('__hidden');
    }
    else if( value === 'load' ){
        display_after.removeClass('__hidden');
        scroll_percentage.addClass('__hidden');
    }
    else if( value === 'scroll' ){
        display_after.addClass('__hidden');
        scroll_percentage.removeClass('__hidden');
    }
    else{
        display_after.addClass('__hidden');
        scroll_percentage.addClass('__hidden');
    }

});

teditor.onChange('display_after_x_seconds');
teditor.onChange('scrolling_down_percentage');
teditor.onChange('reminder_number_notsub');
teditor.onChange('reminder_type_notsub');
teditor.onChange('reminder_number_sub');


/**
 * Optin
**/
teditor.onChange('main_image', function( e, value ){  

    teditor.preview.el.attr( 'data-mainimage', value == '' ? 0 : 1 );

    var one_el = teditor.preview.el.find('.skytake-box-one');
    
    if( one_el.find('img').length || one_el.find('span').length ){

        if( value == '' ){
            one_el.find('img').replaceWith('<span></span>');
        }
        else{
            one_el.find('span').replaceWith('<img src="'+value+'" >');
        }
    }
    else{
        teditor.preview.el.find('.skytake-box-one').css( 'background-image', 'url('+value+')' );
    }
});

teditor.onChange('title', function( e, value ){
    teditor.preview.el.find('.skytake-view.__request .skytake-title').html( value );
});

teditor.onChange('message', function( e, value ){
    teditor.preview.el.find('.skytake-view.__request .skytake-message').html( value );
});

teditor.onChange('is_input_name_enabled', function( e, value ){
    skytake_input_name_control.call( this, value );
});

teditor.onChange('is_mobile_input_enabled', function( e, value ){
    skytake_input_mobile_control.call( this, value );
});

teditor.onChange('name_input_text', function( e, value ){
    teditor.preview.el.find('.skytake-name').val( value );
});

teditor.onChange('input_mobile_text', function( e, value ){
    teditor.preview.el.find('.skytake-mobile').val( value );
});

teditor.onChange('email_input_text', function( e, value ){
    teditor.preview.el.find('.skytake-email').val( value );
});

teditor.onChange('submit_button_text', function( e, value ){
    teditor.preview.el.find('.skytake-submit').text( value );
});

teditor.onChange('spam_text', function( e, value ){
    teditor.preview.el.find('.skytake-spam').text( value );

    if( value == '' ){
        teditor.preview.el.find('.skytake-spam').addClass('__hidden');
    }
    else{
        teditor.preview.el.find('.skytake-spam').removeClass('__hidden');
    }
});

teditor.onChange('is_gdpr_enabled', function( e, value ){
    var self = jQuery(this);

    if( value == 1 ){
        self.closest('li').next().removeClass('__hidden');
        teditor.preview.el.find('.skytake-gdpr-container ').removeClass('__hidden');
    }
    else{
        self.closest('li').next().addClass('__hidden');
        teditor.preview.el.find('.skytake-gdpr-container ').addClass('__hidden');
    } 
});

teditor.onChange('gdpr_text', function( e, value ){
    teditor.preview.el.find('.skytake-gdpr-text').html( value );
});

/** success */
teditor.onChange('title_after_sub', function( e, value ){
    teditor.preview.el.find('.skytake-view.__response .skytake-title').html( value );
});

teditor.onChange('message_after_sub', function( e, value ){
    teditor.preview.el.find('.skytake-view.__response .skytake-message').html( value );
});


/** Template Change */
teditor.onChange('template', function( e, value ){
    skytake_change_template(value);
});

/** style */
teditor.onChange('animation', function( e, value ){
    teditor.preview.el.attr('data-animation', value).removeClass('__show');

    setTimeout(function(){
        teditor.preview.el.addClass('__show');
    }, 400);
});

teditor.onChange('overlay_color', function( e, value ){    
    teditor.preview.html.find('.skytake-overlay').css('background-color', value );
});

teditor.onChange('layout', function( e, value ){
    teditor.preview.el.attr('data-layout', value );

    if( value === 'right' ){
        teditor.setValue('body_bg_position', 'left center');
    }
    else if( value === 'left' ){
        teditor.setValue('body_bg_position', 'right center');
    }
    else{
        teditor.setValue('body_bg_position', 'center center');
    }
});

teditor.onChange('body_bg_color', function( e, value ){
    teditor.preview.el.find('.skytake-container').css('background-color', value );
});

teditor.onChange('body_bg_image', function( e, value ){
    skytake_bg_image_control( value );
});

teditor.onChange('body_bg_repeat', function( e, value ){
    teditor.preview.el.find('.skytake-container').css('background-repeat', value );
});

teditor.onChange('body_bg_size', function( e, value ){
    teditor.preview.el.find('.skytake-container').css('background-size', value );
});

teditor.onChange('body_bg_position', function( e, value ){
    teditor.preview.el.find('.skytake-container').css('background-position', value );
});

teditor.onChange('font_family', function( e, value ){
    skytake_change_font( value );
});

teditor.onChange('title_color', function( e, value ){
    teditor.preview.el.find('.skytake-title').css('color', value );
});

teditor.onChange('title_font_size', function( e, value ){
    teditor.preview.el.find('.skytake-title').css('font-size', value + 'px' );
});

teditor.onChange('body_color', function( e, value ){
    teditor.preview.el.find('.skytake-view, .skytake-view a').css('color', value );
});

teditor.onChange('body_font_size', function( e, value ){
    teditor.preview.el.find('.skytake-view').css('font-size', value + 'px' );
});

teditor.onChange('email_color', function( e, value ){
    teditor.preview.el.find('.skytake-email, .skytake-name, .skytake-mobile').css('color', value );
});

teditor.onChange('email_bg_color', function( e, value ){
    teditor.preview.el.find('.skytake-email, .skytake-name, .skytake-mobile').css('background-color', value );
});

teditor.onChange('input_font_size', function( e, value ){
    teditor.preview.el.find('.skytake-email, .skytake-name, .skytake-mobile, .skytake-submit').css('font-size', value + 'px' );
});

teditor.onChange('submit_color', function( e, value ){
    teditor.preview.el.find('.skytake-submit').css('color', value );
});

teditor.onChange('submit_bg_color', function( e, value ){
    teditor.preview.el.find('.skytake-submit').css('background-color', value );
});

teditor.onChange('close_icon_color', function( e, value ){
    teditor.preview.el.find('.skytake-close').css('fill', value );
});

teditor.onChange('close_icon_bg_color', function( e, value ){
    teditor.preview.el.find('.skytake-close').css('background-color', value );
});


/**
 * Welcome email 
**/
teditor.onChange('is_welcome_email_enabled');
teditor.onChange('welcome_email_subject');
teditor.onChange('welcome_email_content');


/**
 * Filters 
**/
teditor.onChange('is_loggedin_enabled');
teditor.onChange('is_administrators_enabled');
teditor.onChange('filter_hide_on_articles');
teditor.onChange('filter_type');
teditor.onChange('filter_pages_excluded');

/**
 * Woocommerce 
**/
teditor.onChange('is_coupon_enabled');
teditor.onChange('selected_coupon');

/**
 * Mailchimp 
**/
teditor.onChange('is_mailchimp_enabled');
teditor.onChange('mailchimp_listid');
teditor.onChange('is_mailchimp_campaign_enabled');
teditor.onChange('mailchimp_campaign_url');

teditor.onChange('mailchimp_apikey', function(e){
    skytake_set_mailchimp_lists();
});

teditor.body_el.on('skytake_editor_loaded', function( e ){
    skytake_set_mailchimp_lists();
});

/**
 * Minimized bar
 */
teditor.onChange('display_minimized_bar');

teditor.onChange('minimized_bar_position', function( e, value ){
    teditor.preview.bar.attr( 'data-position', value );
});

teditor.onChange('minimized_bar_icon', function( e, value ){
    teditor.preview.bar.find('span').attr( 'class', value );
});

teditor.onChange('minimized_bar_size', function( e, value ){
    teditor.preview.bar.find('span').css({
        'font-size': value + 'px',
        'line-height': value + 'px',
    });
});

teditor.onChange('minimized_bar_color', function( e, value ){
    teditor.preview.bar.find('span').css('color', value);
});

teditor.onChange('minimized_bar_bg_color', function( e, value ){
    teditor.preview.bar.css('background-color', value);
});


/**
 * Urgency
**/
teditor.onChange('urgency_type', function(e , value){

    teditor.preview.el.find('.skytake-urgency').attr('data-type', value);

    var session_container = jQuery('.skytake_urgency_session_container');
    var session_recreate_container = jQuery('.skytake_urgency_session_recreate_container');
    var expiry_date_container = jQuery('.urgency_expire_date_container');
    var usage_limit_container = jQuery('.urgency_usage_limit_container');

    if( value === 'disabled'){
        session_container.addClass('__hidden');
        session_recreate_container.addClass('__hidden');
        expiry_date_container.addClass('__hidden');
        usage_limit_container.addClass('__hidden');
    }
    else if ( value === 'session' ){
        session_container.removeClass('__hidden');
        session_recreate_container.removeClass('__hidden');
        expiry_date_container.addClass('__hidden');
        usage_limit_container.addClass('__hidden');
    }
    else if ( value === 'expiry_date' ){
        session_container.addClass('__hidden');
        session_recreate_container.addClass('__hidden');
        expiry_date_container.removeClass('__hidden');
        usage_limit_container.addClass('__hidden');
    }
    else if ( value === 'usage_limit' ){
        session_container.addClass('__hidden');
        session_recreate_container.addClass('__hidden');
        expiry_date_container.addClass('__hidden');
        usage_limit_container.removeClass('__hidden');
    }
});

teditor.onChange('urgency_session_time');
teditor.onChange('urgency_session_type');
teditor.onChange('skytake_urgency_session_recreate');
teditor.onChange('urgency_expire_date');
teditor.onChange('urgency_usage_limit');

teditor.onChange('urgency_font_size', function( e, value ){
    teditor.preview.el.find('.skytake-urgency').css('font-size', value + 'px');
});

teditor.onChange('urgency_color', function( e, value ){
    teditor.preview.el.find('.skytake-urgency-usage-limit > div, .skytake-urgency-timer > div').css('color', value );
});

teditor.onChange('urgency_bg_color', function( e, value ){
    teditor.preview.el.find('.skytake-urgency-usage-limit > div, .skytake-urgency-timer > div').css('background-color', value );    
});


/**
 * Social Media
 */
teditor.onChange('social_open_link_new_tab');

teditor.onChange('social_icon_icon', function( e, value ){
    teditor.preview.el.find('.skytake-social-icons a').each(function(){
        var self = jQuery(this).find('span');
        self.attr('class', self.data('icon') + value.replace('dlbicons-facebook', '') );
    }); 
});

teditor.onChange('social_icon_size', function( e, value ){
    teditor.preview.el.find('.skytake-social-icons span').css('font-size', value + 'px' );
});

teditor.onChange('social_color_type', function( e, value ){
    if( value === 'default' ){
        teditor.preview.el.find('.skytake-social-icons').removeClass('__color_type_custom').addClass('__color_type_default');
    }
    else{
        teditor.preview.el.find('.skytake-social-icons').removeClass('__color_type_default').addClass('__color_type_custom');
    }
});

teditor.onChange('social_icon_color', function( e, value ){
    teditor.preview.el.find('.skytake-social-icons.__color_type_custom span').css('color', value );
});

teditor.onChange('social_icon_facebook', function( e, value ){

    var counter_el = teditor.preview.el.find('.skytake-social-icons');

    if( value === '' ){
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) - 1 );
        teditor.preview.el.find('.skytake-social-icon-facebook').addClass('__hidden');
    }         
    else{
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) + 1 );
        teditor.preview.el.find('.skytake-social-icon-facebook').removeClass('__hidden');
    }
});

teditor.onChange('social_icon_twitter', function( e, value ){

    var counter_el = teditor.preview.el.find('.skytake-social-icons');

    if( value === '' ){
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) - 1 );
        teditor.preview.el.find('.skytake-social-icon-twitter').addClass('__hidden');
    }         
    else{
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) + 1 );
        teditor.preview.el.find('.skytake-social-icon-twitter').removeClass('__hidden');
    }
});

teditor.onChange('social_icon_instagram', function( e, value ){

    var counter_el = teditor.preview.el.find('.skytake-social-icons');

    if( value === '' ){
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) - 1 );
        teditor.preview.el.find('.skytake-social-icon-instagram').addClass('__hidden');
    }         
    else{
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) + 1 );
        teditor.preview.el.find('.skytake-social-icon-instagram').removeClass('__hidden');
    }
});

teditor.onChange('social_icon_pinterest', function( e, value ){

    var counter_el = teditor.preview.el.find('.skytake-social-icons');

    if( value === '' ){
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) - 1 );
        teditor.preview.el.find('.skytake-social-icon-pinterest').addClass('__hidden');
    }         
    else{
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) + 1 );
        teditor.preview.el.find('.skytake-social-icon-pinterest').removeClass('__hidden');
    }
});

teditor.onChange('social_icon_linkedin', function( e, value ){

    var counter_el = teditor.preview.el.find('.skytake-social-icons');

    if( value === '' ){
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) - 1 );
        teditor.preview.el.find('.skytake-social-icon-linkedin').addClass('__hidden');
    }         
    else{
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) + 1 );
        teditor.preview.el.find('.skytake-social-icon-linkedin').removeClass('__hidden');
    }
});

teditor.onChange('social_icon_youtube', function( e, value ){

    var counter_el = teditor.preview.el.find('.skytake-social-icons');

    if( value === '' ){
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) - 1 );
        teditor.preview.el.find('.skytake-social-icon-youtube').addClass('__hidden');
    }         
    else{
        counter_el.attr('data-count', parseInt( counter_el.attr('data-count') ) + 1 );
        teditor.preview.el.find('.skytake-social-icon-youtube').removeClass('__hidden');
    }
});
