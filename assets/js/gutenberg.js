(function () {

    wp.blocks.registerBlockType( 'skytake/gutenberg', {

        title: 'Skytake Campaign',
        keywords: [ 'skytake', 'campaign', 'email' ],
        icon: 'email',
        category: 'common',
        
        supports: {
            html: false,
        },

        edit: function( self ) {

            return wp.element.createElement(
                'div',
                null,
                [
                    wp.element.createElement(
                        'h3',
                        null,
                        'Skytake'
                    ),

                    wp.element.createElement(
                        wp.components.SelectControl,
                        {
                            multiple: false,
                            label: wp.i18n.__('Select Campaign', 'skytake'),
                            value: self.attributes.selected_campaign,
                            onChange: function( value ) { self.setAttributes({ 'selected_campaign' : value }) },
                            options: self.attributes.list,
                        },
                        null
                    ),
                ]
            );
        },

        save: function() { return null; },

    });

})();