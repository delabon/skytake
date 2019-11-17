/** Floating Bar Controller */
var TController = function( app ){

    this.app = app;

    this.ui = {};
    this.ui.floating_bar    = app.find('.skytake-floating-bar');
    this.ui.minimized_bar   = app.find('.skytake-bar');
    this.ui.urgency     = this.ui.floating_bar.find('.skytake-urgency');
    this.ui.closeBtn    = this.ui.floating_bar.find('.skytake-close');
    this.ui.email       = this.ui.floating_bar.find('.skytake-email');
    this.ui.submitBtn   = this.ui.floating_bar.find('.skytake-submit');
    
    this.showCampaign();
    this.events();
    return this;
}

TController.prototype.showMinimizedBar = function () {
        
    this.ui.minimized_bar.addClass('__show');
    
    return this;
}

TController.prototype.hideMinimizedBar = function () {
    
    this.ui.minimized_bar.removeClass('__show');

    return this;
}

TController.prototype.showCampaign = function(){

    this.ui.floating_bar.addClass('__show');
    
    return this;
}

TController.prototype.hideCampaign = function(){

    this.ui.floating_bar.removeClass('__show');

    return this;
}

TController.prototype.showSuccess = function(){

    this.ui.floating_bar.addClass('__show_response');

    return this;
}

TController.prototype.hideSuccess = function(){

    this.ui.floating_bar.removeClass('__show_response');
    
    return this;
}

TController.prototype.showUrgency = function(){
    this.ui.urgency.removeClass('__hidden');        
}

TController.prototype.hideUrgency = function(){
    this.ui.urgency.addClass('__hidden');                
}

TController.prototype.events = function(){

    var _self = this;

    this.app.on('skytake_editor_tab_changed', function( e, tab ){
                
        _self.hideSuccess();
        _self.hideMinimizedBar();

        if( tab === 'success' ){
            _self.showSuccess();
        }
        else if( tab === 'minimized_bar' ){
            _self.hideCampaign();
            _self.showMinimizedBar();
        }
        else{
            _self.showCampaign();
        }

    });
}

TController.prototype.off = function(){
    this.app.off('skytake_editor_tab_changed');    
}

/** 
 * Settings Listeners 
**/
teditor.onChange('floating_bar_position', function( e, value ){
    teditor.preview.el.attr('data-position', value );
});
