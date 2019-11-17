(function( $ ){

    var body = jQuery('body');
    var win = jQuery( window );
    var doc = jQuery( document ); 
    var html = jQuery( 'html' );

    /**
     * Get query value from a URL
     * @param {string} name 
     * @param {string} url 
     */
    function getUrlQuery(name, url) {
        
        if (!url) url = window.location.href;

        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';

        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    /**
     * Adds time to a date. Modelled after MySQL DATE_ADD function.
     * Example: dateAdd(new Date(), 'minutes', 30)  //returns 30 minutes from now.
     * 
     * http://jsfiddle.net/2rgcosfe/
     * 
     * @param date  Date to start with
     * @param interval  One of: year, quarter, month, week, day, hour, minute, second
     * @param units  Number of units of the given interval to add.
     */
    function dateAdd(date, interval, units) {

        interval = interval.toLowerCase();
        units = parseInt(units);
        
        var ret = date;

        if( ! date instanceof Date ){
            ret = new Date(date); //don't change original date
        }

        var checkRollover = function() { if(ret.getDate() != date.getDate()) ret.setDate(0);};
        
        switch( interval ) {
            case 'year'   :  ret.setFullYear(ret.getFullYear() + units); checkRollover();  break;
            case 'quarter':  ret.setMonth(ret.getMonth() + 3*units); checkRollover();  break;
            case 'month'  :  ret.setMonth(ret.getMonth() + units); checkRollover();  break;
            case 'week'   :  ret.setDate(ret.getDate() + 7*units);  break;
            case 'day'    :  ret.setDate(ret.getDate() + units); break;
            case 'hour'   :  ret.setTime(ret.getTime() + units*3600000);  break;
            case 'minute' :  ret.setTime(ret.getTime() + units*60000);  break;
            case 'second' :  ret.setTime(ret.getTime() + units*1000);  break;
            default       :  ret = undefined;  break;
        }

        return ret;
    }

    /*************************************************
     * App
    *************************************************/
    function App( settings ){

        this.allowLog = true;
        this.settings = settings;
        this.id = settings.campaign_id;
        this.type = settings.campaign_type;
        this.ui = {};
        this.slugs = {};

        this.init();
    }

	// The init method that starts the fun.
    App.prototype.init = function(){

        this.log('# Init');

        this.createSlugs();
        this.checkIfSettingsChanged();

        if( this.isSubscribePaused() ) return;
        if( this.isSessionPaused() ) return;

        this.render();
        this.setupBar();
        this.setupUrgency();
        this.onCloseClick();
        this.ajaxSubscribeListener();

        if( this.type === 'widget-form' || this.type === 'content-form' ){
            this.showCampaign();
        }
        else{
            var myArray = [ 'load', 'exit', 'scroll' ];
            var rand = this.settings.display_trigger !== 'random' ? this.settings.display_trigger : myArray[Math.floor(Math.random() * myArray.length)];
                    
            if( rand === 'exit' ){
                this.onMouseLeave();
            }
            else if( rand === 'load' ){
                this.onPageLoad();
            }
            else if( rand === 'scroll' ){
                this.onPageScroll();
            }
        }

        return this;
    }

    // Create slugs
    App.prototype.createSlugs = function(){
        this.slugs.campaign = 'SKYT['+SKYT_PARAMS.site_id+'][' + this.id + ']_';
        this.slugs.last_change = this.slugs.campaign + 'last_change'
        this.slugs.pause_date = this.slugs.campaign + 'pause_date';
        this.slugs.session_date = this.slugs.campaign + 'session_date';
        this.slugs.session_pause_date = this.slugs.campaign + 'session_pause_date';
        this.slugs.sub_pause_date = this.slugs.campaign + 'sub_pause_date';
        this.slugs.has_subscribed = 'SKYT['+SKYT_PARAMS.site_id+']_has_subscribed';
    }

    // Reset if the settings have changed
    App.prototype.checkIfSettingsChanged = function(){
    
        if( this.getStorage( this.slugs.last_change ) == this.settings.last_settings_update ){
            this.log('Settings have not changed');
            return false;
        }

        this.setStorage( this.slugs.last_change, this.settings.last_settings_update );

        this.deleteStorage( this.slugs.pause_date );
        this.deleteStorage( this.slugs.session_date );
        this.deleteStorage( this.slugs.session_pause_date );
        this.deleteStorage( this.slugs.sub_pause_date );

        this.log('Settings have changed');
        return true;
    }
    
    // Render
    App.prototype.render = function(){
        
        if( this.type === 'widget-form' || this.type === 'content-form' ){
            body.find('.skytake-placeholders[data-id="'+ this.id +'"]').replaceWith(this.settings.campaign_markup);
        }
        else{
            body.append(this.settings.campaign_markup);
        }

        this.ui.campaign = body.find( '#skytake-' + this.id );
        this.ui.urgency = this.ui.campaign.find('.skytake-urgency');
        this.ui.container = this.ui.campaign.find('.skytake-container');

        if( this.type === 'popup' ){
            this.ui.overlay = body.find( '.skytake-overlay[data-target="'+this.id+'"]' );
        }

        if( this.settings.hasOwnProperty('minimized_bar_markup') ){
            body.append(this.settings.minimized_bar_markup);
            this.ui.bar = body.find( '.skytake-bar[data-target="' + this.id + '"]' );
        }
    }

    // Setup the minimized bar
    App.prototype.setupBar = function () {

        this.log('# Minimized bar setup called');
        this.log('Has minimized bar? ' + ( this.ui.hasOwnProperty('bar') ? 'YES' : 'NO' ) );
        this.log('Minimized bar display: ' + this.settings.display_minimized_bar );

        if( ! this.ui.hasOwnProperty('bar') ){
            return;
        }

        // never
        if( this.settings.display_minimized_bar === 'never' ){
            return;
        }

        // always
        if( this.settings.display_minimized_bar === 'always' ){
            this.showBar(); 
        }

        var app = this;

        app.ui.bar.on('click', function( e ){
            e.preventDefault();
            app.deleteStorage( app.slugs.pause_date );
            app.showCampaign();
        });
    }

    // Show minimized bar
    App.prototype.showBar = function () {
        
        this.log('# Hide bar called');

        if( ! this.ui.hasOwnProperty('bar') ) return;

        this.ui.bar.addClass('__show');

        return this;
    }

    // Hide minimized bar
    App.prototype.hideBar = function () {
        
        this.log('# Hide bar called');

        if( ! this.ui.hasOwnProperty('bar') ) return;

        this.ui.bar.removeClass('__show');

        return this;
    }

    // Setup urgency
    App.prototype.setupUrgency = function(){

        this.log('# Urgency setup called');
        this.log('Urgency type: ' + this.settings.urgency_type );

        if( this.settings.urgency_type === 'disabled' ) return false;
        
        var plugin = this;
        var date;

        plugin.showUrgency();

        if( plugin.settings.urgency_type === 'usage_limit' ){
            return plugin;
        }
        else if( plugin.settings.urgency_type === 'expiry_date' ){
            date = new Date( parseInt( this.settings.urgency_date_timestamp ) );
        }
        else if( plugin.settings.urgency_type === 'session' ){
            
            if( ! plugin.getSessionDate() || new Date() > plugin.getSessionPauseDate() ){    
                plugin.setSessionDate();
                plugin.setSessionPauseDate();
            }

            date = plugin.getSessionDate();
        }

        if( isNaN( date.getTime() ) ) return plugin;

        // Timer Countdown
        var urgency_timer_el = plugin.ui.urgency.find('.skytake-urgency-timer');

        plugin.ui.urgency.countdown( date, function(event) {
                            
            urgency_timer_el.find('.__days span:first').text( event.strftime( '%D' ) );
            urgency_timer_el.find('.__hours span:first').text( event.strftime( '%H' ) );
            urgency_timer_el.find('.__minutes span:first').text( event.strftime( '%M' ) );
            urgency_timer_el.find('.__seconds span:first').text( event.strftime( '%S' ) );

        }).on('finish.countdown', function(){

            if( plugin.hasSubscribed() ) return;

            plugin.hideCampaign();
            plugin.hideBar();
        });

        return plugin;
    }

    // Show urgency ui
    App.prototype.showUrgency = function(){
        this.ui.urgency.removeClass('__hidden');        
    }

    // Hide urgency ui
    App.prototype.hideUrgency = function(){
        this.ui.urgency.addClass('__hidden');                
    }

    // Event: on close button clicked
    App.prototype.onCloseClick = function () {
        var plugin = this;

        plugin.ui.campaign.find('.skytake-close').on('click', function( e ){
            e.preventDefault();
            plugin.hideCampaign();
        });
    }

    // Event: on mouse leave the window
    App.prototype.onMouseLeave = function () {

        var plugin = this;
        var event = 'mouseleave.skytake_' + this.id;

        doc.on( event, function(e){
            plugin.log('Mouse left the window');
            plugin.showCampaign();
        });
    }

    // Event: on page load
    App.prototype.onPageLoad = function () {
        var plugin = this;

        setTimeout(function(){
            plugin.showCampaign();
        }, parseInt( plugin.settings.display_trigger_after ) * 1000 );
    }

    // Event: on page scroll
    App.prototype.onPageScroll = function () {
        var plugin = this;
        var timer;

        // no scrollbar execute immediatly
        if ( doc.height() === win.height() ) {
            plugin.showCampaign();
            return this;
        }

        win.on('scroll', function(){

            clearTimeout(timer);

            timer = setTimeout(function(){
                var s = win.scrollTop(),
                    d = doc.height(),
                    c = win.height();
              
                var scrollPercent = (s / (d - c)) * 100;
                
                if( scrollPercent > parseFloat( plugin.settings.popup_scroll_percentage ) ){
                    plugin.showCampaign();
                }

            }, 20);
        }).scroll();

        return this;
    }

    // Show campaign interface
    App.prototype.showCampaign = function(){

        this.log('# Show campaign called');

        if( this.isSubscribePaused() ) return;
        if( this.isSessionPaused() ) return;

        if( this.type === 'popup' || this.type === 'floating-bar' || this.type === 'scroll-box' ){
            
            if( this.isPaused() ){
                return;
            }

            this.setPauseDate();
        }

        if( this.type === 'popup' ){
            this.ui.overlay.addClass('__show');
        }

        this.ui.campaign.addClass('__show');

        if( this.type === 'popup' ){

            if( this.ui.campaign.height() >= win.height() ){
                this.ui.campaign.addClass('__top_zero');
            }

            html.addClass('__skytake_opened');
        }
        
        this.hideBar();
        this.updateViewsStat();

        return this;
    }

    // Hide campaign interface
    App.prototype.hideCampaign = function(){

        this.log( '# Hide campaign called' );

        this.ui.campaign.removeClass('__show');

        if( this.type === 'popup' ){
            this.ui.overlay.removeClass('__show');
            html.removeClass('__skytake_opened');
        }

        if( this.settings.display_minimized_bar !== 'never' ){
            this.showBar();
        }

        return this;
    }

    // Show success view
    App.prototype.showSuccess = function(){

        this.ui.campaign.addClass('__show_response');

        return this;
    }

    // Hide success view
    App.prototype.hideSuccess = function(){

        this.ui.campaign.removeClass('__show_response');
        
        return this;
    }

    // Is paused
    App.prototype.isPaused = function ( now ) {

        now = now || new Date();
        var pauseDate = this.getPauseDate();

        if( pauseDate && now < pauseDate ){
            this.log('Is close paused? YES');
            return true;
        }

        this.log('Is close paused? NO');
        return false;
    }

    // Get pause date
    App.prototype.getPauseDate = function(){

        var date = this.getStorage( this.slugs.pause_date );

        if( ! date ){
            return false;
        }

        return new Date( date );
    }

    // Set pause date
    App.prototype.setPauseDate = function(){

        var type = this.settings.reminder_type_notsub.toLowerCase();
        var number = parseInt( this.settings.reminder_number_notsub );
        var date = dateAdd( new Date(), type, number );

        this.setStorage( this.slugs.pause_date, date );

        return date;
    }

    // Ajax subscribe listener
    App.prototype.ajaxSubscribeListener = function(){

        var plugin = this;
        var form = plugin.ui.campaign.find('.skytake-form');

        form.on('submit', function(e){
            e.preventDefault();

            plugin.log('Form submit called');

            form.addClass('__loading');

            var gdpr_el = form.find('.skytake-gdpr');

            if( gdpr_el.length && ! gdpr_el.is(':checked') ){
                return false;
            }

            $.ajax({
                type: "POST",
                url: plugin.settings.ajaxurl,
                data: {
                    'action': 'skytake_subscribe',
                    'nonce': plugin.settings.nonce,
                    'campaign_id': plugin.settings.campaign_id,
                    'email': form.find('.skytake-email').val(),
                    'name': form.find('.skytake-name').val(),
                    'mobile': form.find('.skytake-mobile').val(),
                },
            }).done(function(response){

                if( response.success ){
                    plugin.subscribe();
                }
                else{
                    alert( response.data );
                }

                form.removeClass('__loading');
            });

        });
    }

    // Subscribe handler
    App.prototype.subscribe = function () {

        this.setStorage( this.slugs.has_subscribed, true );
        this.setSubscribePauseDate();
        this.showSuccess();
        this.hideBar();

        // hide other campaigns
        Object.keys( skytake ).map(function( id ){

            if( id != this.id ){
                skytake[id].hideCampaign();
                skytake[id].hideBar();
                skytake[id].setSubscribePauseDate();
            }
        }.bind(this));

        return this;
    }

    // Update views stat
    App.prototype.updateViewsStat = function(){

        $.ajax({
            type: "POST",
            url: this.settings.ajaxurl,
            data: {
                'action': 'skytake_update_campaign_views',
                'nonce' : this.settings.nonce,
                'campaign_id': this.id,
            },
        });
    }

    // Checks if the current user has subscribed
    App.prototype.hasSubscribed = function () {

        if( ! this.getStorage( this.slugs.has_subscribed ) ){
            this.log('Current user has not subscribed');
            return false;
        }

        this.log('Current user has subscribed');
        return true;
    }

    // Checks if subscribed and paused
    App.prototype.isSubscribePaused = function ( now ) {

        if( ! this.hasSubscribed() ){
            return false;
        }

        now = now || new Date();
        var reminderDate = this.getSubscribePauseDate();

        if( reminderDate && now < reminderDate ){
            this.log('Is subscribe paused? YES');
            return true;
        }

        this.log('Is subscribe paused? NO');
        return false;
    }

    // Set subscribe pause date
    App.prototype.setSubscribePauseDate = function () {
        
        var date = dateAdd( new Date(), 'day', parseInt( this.settings.reminder_number_sub ) );
        this.setStorage( this.slugs.sub_pause_date, date );

        return date;
    }

    // Get subscribe pause date
    App.prototype.getSubscribePauseDate = function(){

        var date = this.getStorage( this.slugs.sub_pause_date );

        if( ! date ){
            return false;
        }

        return new Date( date );
    }

    // Checks if urgency session paused
    App.prototype.isSessionPaused = function( now ){

        if( this.settings.urgency_type !== 'session' ) return false;

        now = now || new Date();
        var sessionDate = this.getSessionDate();
        var sessionPauseDate = this.getSessionPauseDate();

        if( sessionDate 
            && sessionPauseDate
            && now > sessionDate 
            && now < sessionPauseDate 
        ){
            this.log('Is urgency session paused? YES');
            return true;
        }

        this.log('Is urgency session paused? NO');
        return false;
    }

    // Set urgency session date
    App.prototype.setSessionDate = function(){

        var date = dateAdd( new Date(), this.settings.urgency_session_type, parseInt( this.settings.urgency_session_time ) );
        this.setStorage( this.slugs.session_date, date );
        
        return date;
    }

    // Get urgency session date
    App.prototype.getSessionDate = function(){

        var date = this.getStorage( this.slugs.session_date );

        if( ! date ){
            return false;
        }

        return new Date( date );
    }

    // Set urgency session pause date
    App.prototype.setSessionPauseDate = function(){
        
        var session_date = this.getSessionDate();
        
        if( ! session_date || session_date === 'Invalid Date' ){
            session_date = this.setSessionDate();
        }

        var pause_date = dateAdd( session_date, 'day', this.settings.urgency_session_pause_time );
        this.setStorage( this.slugs.session_pause_date, pause_date );

        return pause_date;
    }

    // Get urgency session pause date
    App.prototype.getSessionPauseDate = function(){

        var date = this.getStorage( this.slugs.session_pause_date );

        if( ! date ){
            return false;
        }

        return new Date( date );
    }

    // Wrapper for localStorage.getItem
    App.prototype.getStorage = function( key ){
        return localStorage.getItem(key);
    }

    // Wrapper for localStorage.setItem
    App.prototype.setStorage = function( key, value ){
        localStorage.setItem( key, value );
    }

    // Wrapper for localStorage.deleteStorage
    App.prototype.deleteStorage = function( key, value ){
        localStorage.removeItem( key );
    }

    // Logger
    App.prototype.log = function( text ){

        if( ! this.allowLog ) return;

		(typeof console === 'object') ? console.log( '[SKYT]['+this.id+']['+this.type+'] ' + text ) : '';
    }

    /*************************************************
     * Show Time
    *************************************************/

    window.skytake = {};
    var non_floating_ids = [];

    $('.skytake-placeholders').each(function (i) {
        non_floating_ids.push( $(this).data('id') );
    });

    /**
     * Show Time
     */
    if( window.hasOwnProperty('SKYT_PARAMS') ){
        
        $.ajax({
            type: "POST",
            url: SKYT_PARAMS.ajaxurl,
            data: {
                'action': 'skytake_get_popup',
                'nonce' : SKYT_PARAMS.nonce,
                'ids' : SKYT_PARAMS.ids,
                'non_floating_ids': non_floating_ids,
            },
        }).done(function( response ){

            if( ! response.success ){
                return;
            }

            response.data.map(function (item) {                
                skytake[ item.campaign_id ] = new App( item );
            });            
        });
    }

})( jQuery );