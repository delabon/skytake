<?php 

namespace Delabon\WP\Customizer\Controls;

if( ! class_exists( Init::class ) ){
    return;
}

class Init {

    private $base_url;

    function __construct( $base_url ){
        
        $this->base_url = $base_url;

        add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue'), 30 );
    }

    /**
	 * Enqueue scripts and styles.
	 */
	function enqueue() {

		wp_enqueue_script(
			'delabon-customizer-controls',
			$this->base_url . '/delabon/wp/customizer/controls/js/script.js',
			array( 'jquery', 'wp-color-picker' ),
			'1.0.0',
			true
		);

		wp_enqueue_style(
			'delabon-customizer-controls',
			$this->base_url . '/delabon/wp/customizer/controls/css/style.css',
			array( 'wp-color-picker' ),
			'1.0.0'
		);
    }
    
}
