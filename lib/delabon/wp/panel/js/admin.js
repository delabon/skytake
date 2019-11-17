(function( $ ){

    /**
     * Panel Nav ( Mobile )
     */
    var nav = $('.dlb_panel_menu');
    var currentNavItem = nav.find('.__active a');
    var mobileNavOuput = $('<div class="dlb_panel_menu_mobile"><div><span></span><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path d="M12 21l-12-18h24z"/></svg></div></div>').append( nav.clone().removeClass('dlb_panel_menu') );
    mobileNavOuput.insertBefore('.dlb_panel_menu');
    mobileNavOuput.find('> div span').text(currentNavItem.text());

    mobileNavOuput.find('> div').on('click', function(){
        mobileNavOuput.toggleClass('__opened');
    });

    /**
     * Tab Toggle
     */
    $('.dlb_tabs > li > a').click(function( event ){
        event.preventDefault();
        $(this).parent().toggleClass('_open_');
    });

    /**
     * Select Input
     */
    $('.dlb_input_select').on('click', '.__selected', function(e){
        e.preventDefault();

        var parent = $(this).parent();

        if( parent.attr('data-open') === 'true' ){
            parent.attr('data-open', 'false');
        }
        else{
            parent.attr('data-open', 'true');
        }
    }).find('.dlb_upgrade_tag').remove();

    $('.dlb_input_select .__list').on('click',  '> div' ,function(e){
        e.preventDefault();

        var _self = $(this);
        var parent = $(this).parent().parent();

        if( _self.find('.dlb_upgrade_tag').length ){
            parent.attr('data-open', 'false');
            return false;
        }
        
        parent.find('.__input').val( _self.data('value') ).trigger('change');
        parent.attr('data-open', 'false');
    });

    $('.dlb_input_select').on('change', '.__input', function(e){
        var self = $(this);
        var parent = self.parent();
        var value = self.val();        
        
        parent.find('.__selected').text( self.find('[value="'+value+'"]').text() );
    });

    /**
     * Checkbox input
    **/
	$('.dlb_input_checkbox' ).click( function( event ){

        var _self = $(this);
		var hidden = _self.find('.__input');
                
		if( hidden.val() == 0 ){
            hidden.val(1);
            _self.attr('data-checked', 1);
		}
		else{
            hidden.val(0);   
            _self.attr('data-checked', 0);
        }
        
        hidden.trigger('input');
	});

    /**
	 * Color Picker
	 */
    $('.dlb_input_colorpicker .__input').wpColorPicker();

    /**
     * Date Picker
     */
    $( '.dlb_input_datepicker .__input' ).datepicker({
        dateFormat: 'yy-mm-dd',
        numberOfMonths: 1,
        showButtonPanel: true,
        onSelect: function(){            
            $(this).trigger('input');
        }
    });

    /** 
     * Radio Icons
     */
	$('.dlb_radio_icons_wrapper [type="radio"]').on('change', function(e){
		var _self = $(this);

		_self.closest('.dlb_radio_icons_wrapper').find('.__active').removeClass('__active');
		_self.parent().addClass('__active');		
	});
    
    /**
	 * Dismissible Alert 
	 */
	$( '.dlb_alert .__is_dismissible' ).on('click', function(e){
        e.preventDefault();
        $(this).parent().slideUp();
    });

    /**
     * Detect Outside Clicks
     */
    $('body').on('click', function(e){
        
        var select = $(e.target).parents('.dlb_input_select');

        if( ! select.length ){
            $('.dlb_input_select[data-open="true"]').attr('data-open', 'false');
        }
    });

    /**
     * Image upload
     */
    var wpframe;

    // ADD IMAGE LINK
    $('.dlb_input_upload .dlb_upload_holder, .dlb_input_upload .dlb_upload_btn').on( 'click', function( event ){
        event.preventDefault();

        var self_el = $(this);
        
        // If the media wpframe already exists, reopen it.
        if ( wpframe ) {
            wpframe.open();
            return;
        }

        // Create a new media wpframe
        wpframe = wp.media({
            title: '',
            button: {
                text: 'Done'
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected in the media wpframe...
        wpframe.on( 'select', function() {

            // Get media attachment details from the wpframe state
            var parent = self_el.closest('.dlb_input_upload');            
            var attachment = wpframe.state().get('selection').first().toJSON();

            parent.find('img').attr( 'src', attachment.url );
            parent.find('input').val( attachment.url ).trigger('input');
            parent.find('.dlb_upload_holder').addClass( '__hidden' );
            parent.find('.dlb_uploaded_image').removeClass( '__hidden' );
        });

        // Finally, open the modal on click
        wpframe.open();
    });

    // DELETE IMAGE LINK
    $('.dlb_input_upload .dlb_upload_remove_btn').on( 'click', function( event ){
        event.preventDefault();

        var self_el = $(this);
        var parent = self_el.closest('.dlb_input_upload');

        parent.find('img').attr( 'src', '' );
        parent.find('input').val( '' ).trigger('input');
        parent.find('.dlb_upload_holder').removeClass( '__hidden' );
        parent.find('.dlb_uploaded_image').addClass( '__hidden' );
    });

    // LISTEN FOR CHANGE
    
    $('.dlb_input_upload').on('input', '.__input', function(e){

        var self_el = $(this);
        var parent = self_el.closest('.dlb_input_upload');            

        if( self_el.val() === '' ){
            parent.find('img').attr( 'src', '' );
            parent.find('.dlb_upload_holder').removeClass( '__hidden' );
            parent.find('.dlb_uploaded_image').addClass( '__hidden' );
        }
        else{
            parent.find('img').attr( 'src', self_el.val() );
            parent.find('.dlb_upload_holder').addClass( '__hidden' );
            parent.find('.dlb_uploaded_image').removeClass( '__hidden' );
        }
    });

})( jQuery );
