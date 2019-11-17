/** Floating Bar Controller */
var TController = function( app ){

    this.app = app;

    this.ui = {};
    this.ui.campaign    = app.find('.skytake-content-form');
    this.ui.urgency     = this.ui.campaign.find('.skytake-urgency');
    this.ui.closeBtn    = this.ui.campaign.find('.skytake-close');
    this.ui.email       = this.ui.campaign.find('.skytake-email');
    this.ui.submitBtn   = this.ui.campaign.find('.skytake-submit');
        
    this.showCampaign();
    this.events();
    return this;
}

TController.prototype.showCampaign = function(){

    this.ui.campaign.addClass('__show');

    return this;
}

TController.prototype.hideCampaign = function(){

    this.ui.campaign.removeClass('__show');

    return this;
}

TController.prototype.showSuccess = function(){

    this.ui.campaign.addClass('__show_response');

    return this;
}

TController.prototype.hideSuccess = function(){

    this.ui.campaign.removeClass('__show_response');
    
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

        if( tab === 'success' ){
            _self.showSuccess();
        }
        else{
            _self.showCampaign();
        }

    });
}

TController.prototype.off = function(){
    this.app.off('skytake_editor_tab_changed');    
}
