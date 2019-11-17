(function( $ ){

    var settings = skytake_admin_settings;

    /**
     * Wrapper for $.ajax
     * @param {String} action 
     * @param {Object} data 
     */
    function ajax( action, data ){

        data = typeof data === 'object' ? data : {};

        data.action = 'skytake_' + action;

        if( ! data.hasOwnProperty('nonce') ){
            data.nonce = settings.nonce;
        }

        return $.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
        });
    }

    /**
     * Open New Campaign Lightbox
    **/
    var lightbox_new_campaign = $('.skytake-new-campaign');
    $('.open-skytake-new-campaign').on('click', function(e){
        e.preventDefault();

        lightbox_new_campaign.removeClass('__hidden');
    });

    $('.close-skytake-new-campaign').on('click', function(e){
        e.preventDefault();

        lightbox_new_campaign.addClass('__hidden');
    });

    var create_new_campaign_el = $('.create-skytake-new-campaign');
    create_new_campaign_el.on('click', function(e){
        e.preventDefault();

        create_new_campaign_el.prop('disabled', true);

        ajax( 'new_campaign', {
            'type': lightbox_new_campaign.find('.dlb_input_select .__input').val(),
        }).done(function( response ){

            lightbox_new_campaign.addClass('__hidden');
            location.href = response.data;
        });
    });

    /**
     * Envato Purchase Validation
     */
    var envato_btn_el = $('#skytake_envato_form button[type="submit"]');

    $('#skytake_envato_form').on('submit', function(e){
        e.preventDefault();

        var btn_text = envato_btn_el.text();
        envato_btn_el.text('...').prop( "disabled", true );

        ajax('envato_validation', {
            nonce: $('#skytake_envato_form input[name="envato_nonce"]').val(),
            email: $('#skytake_envato_form input[name="envato_email"]').val(),
            code: $('#skytake_envato_form input[name="envato_code"]').val(),
        }).done(function( response ){

            if( ! response.success ){

                if( Array.isArray( response.data ) ){
                    for( var index = 0; index < response.data.length; index++ ){
                        alert(response.data[index]);
                    }
                }

                alert(response.data);
                envato_btn_el.text(btn_text).prop( "disabled", false );
            }
            else{
                location.reload();
            }            
        });
    });

    /**
     * Datepicker CSV export change event
     */
    var export_button_el = $('.skytake_export_csv_date a.button');
    $('.skytake_export_csv_date input').on('change', function(){
        var value = $(this).val();
        
        if( value !== '' ){
            export_button_el.attr('href', export_button_el.attr('href') + "&skytake_email_export_date=" + value );
        }
    });

    /**
     * Duplicate campaign
     */
    $('.skytake_list_action.__action_duplicate').on('click', function(e){
        e.preventDefault();

        var self = $(this);

        ajax('duplicate_campaign', {
            'campaign_id': self.data('id'),
        }).done(function(e){
            location.reload();
        });
    });

    /**
     * Delete campaign
     */
    $('.skytake_list_action.__action_delete').on('click', function(e){
        e.preventDefault();

        var self = $(this);

        if( confirm( settings.delete_confirm_text ) ){
            ajax('delete_campaign', {
                'campaign_id': self.data('id'),
            }).done(function(e){
                location.reload();
            });
        }
    });

    /**
     * Status update campaign
     */
    $('.__action_status_toggle.dlb_input_checkbox .__input').on('input', function(e){
        var self = $(this);

        if( self.val() == 1 && ! confirm( settings.status_confirm_text ) ){
            self.val(0).parent().attr('data-checked', 0);
            return false;
        }
        
        ajax('toggle_status_campaign', {
            'campaign_id': self.data('campaign'),
            'status': self.val(),
        }).done(function(e){
            location.reload();
        });
    });

})( jQuery );
