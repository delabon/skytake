/** Popup Controller */
var TController = function( app ){

    this.app = app;

    this.ui = {};
    this.ui.popup       = app.find('.skytake-popup');
    this.ui.overlay     = app.find('.skytake-overlay');
    this.ui.bar         = app.find('.skytake-bar');
    this.ui.container   = this.ui.popup.find('.skytake-container');
    this.ui.urgency     = this.ui.popup.find('.skytake-urgency');
    this.ui.closeBtn    = this.ui.popup.find('.skytake-close');
    this.ui.email       = this.ui.popup.find('.skytake-email');
    this.ui.submitBtn   = this.ui.popup.find('.skytake-submit');
    
    this.showPopup();
    this.events();
    return this;
}

TController.prototype.showBar = function () {
        
    this.ui.bar.addClass('__show');
    
    return this;
}

TController.prototype.hideBar = function () {
    
    this.ui.bar.removeClass('__show');

    return this;
}

TController.prototype.showPopup = function(){

    this.ui.overlay.addClass('__show');
    this.ui.popup.addClass('__show');

    if( this.ui.popup.height() > jQuery( teditor.preview.window ).height() ){
        this.ui.popup.addClass('__top_zero');
    }
    else{
        this.ui.popup.removeClass('__top_zero');
    }

    return this;
}

TController.prototype.hidePopup = function(){

    this.ui.overlay.removeClass('__show');
    this.ui.popup.removeClass('__show');

    return this;
}

TController.prototype.showSuccess = function(){

    this.ui.popup.addClass('__show_response');

    return this;
}

TController.prototype.hideSuccess = function(){

    this.ui.popup.removeClass('__show_response');
    
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
        _self.hideBar();

        if( tab === 'success' ){
            _self.showSuccess();
        }
        else if( tab === 'minimized_bar' ){
            _self.hidePopup();
            _self.showBar();
        }
        else{
            _self.showPopup();
        }

    });
}

TController.prototype.off = function(){

    this.app.off('skytake_editor_tab_changed');    

}
