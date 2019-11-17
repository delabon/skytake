<?php 

namespace SKYT;

/**
 * Skytake gutenberg.
 *
 * Skytake gutenberg handler class is responsible for creating a block for content form cmapaigns.
 * 
 */
class Gutenberg{

    /**
     * Constructor
     */
    function __construct(){

        // Gutenberg
		if( function_exists( 'register_block_type' ) ) {

            add_action( 'init', [ $this, 'load_gutenberg_assets' ] );
		}
    }

    /**
     * Enqueue block JavaScript and CSS for the editor
     */
    function load_gutenberg_assets() {

        wp_register_script(
            'skytake/gutenberg',
            SKYTAKE_URL . '/assets/js/gutenberg.min.js',
            [ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ],
            SKYTAKE_VERSION
        );
    
        // wp_register_style(
        //     'skytake/gutenberg',
        //     SKYTAKE_URL . '/assets/css/gutenberg.min.css',
        //     [ 'wp-edit-blocks' ],
        //     SKYTAKE_VERSION 
        // );

        register_block_type('skytake/gutenberg', array(
            'attributes' => array(
                'selected_campaign' => array(
                    'type' => 'string',
                    'default' => 'none',
                ),
                'list' => array(
                    'type' => 'array',
                    'default' => content_form_campaign_list(),
                ),
            ),
    		'render_callback' => [ $this, 'render_gutenberg_block' ],
            'editor_script' => [
                'skytake/gutenberg',
            ],
            //'style' => 'skytake/gutenberg'   
        ));
    
    }

    /**
     * Renders the gutenberg Block
     *
     * @param array $atts
     * @return void
     */
    function render_gutenberg_block( $atts ){

        if( $atts['selected_campaign'] === 'none' ){
            return;
        }

		return '<div class="skytake-placeholders" data-id="'.$atts['selected_campaign'].'"></div>';
    }

}
