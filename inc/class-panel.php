<?php 

namespace SKYT;

use Delabon\WP\Helper;

class Panel{
    
    private $panels = [];
    private $sections = [];
    
    /**
     * Add new panel
     *
     * @param string $id
     * @param array $args
     * @return void
     */
    function add_panel( $id, $args ){

        $args = wp_parse_args($args, [
            'priority' => 10,
            'title' => '',
        ]);

        if( isset( $this->panels[ $id] ) ){
            throw new \Exception("Panel Id exists");
        }

        $args['_sections'] = [];
        $this->panels[ $id ] = $args;
    }

    /**
     * Add new section
     *
     * @param string $id
     * @param array $args
     * @return void
     */
    function add_section( $id, $args ){

        $args = wp_parse_args($args, [
            'priority' => 10,
            'title' => '',
            'panel' => '',
        ]);

        if( ! isset( $this->panels[ $args['panel'] ] ) ){
            throw new \Exception("Panel does not exist");
        }

        if( isset( $this->sections[ $id ] ) ){
            throw new \Exception("Section Id exists");
        }

        $args['_settings'] = [];

        $this->panels[ $args['panel'] ][ '_sections' ][ $id ] = $args;
        $this->sections[ $id ] = $args;
    }

    /**
     * Add new setting
     *
     * @param string $id
     * @param string $section
     * @param string $setting
     * @return void
     */
    function add_setting( $id, $args ){

        $args = wp_parse_args($args, [
            'priority' => 10,
            'section' => '',
            'output' => '',
            'class' => '',
        ]);
        
        if( ! isset( $this->sections[ $args['section'] ] ) ){
            throw new \Exception("Section does not exist");
        }

        if( isset( $this->sections[ $args['section'] ][ '_settings' ][ $id ] ) ){
            throw new \Exception("Setting ".$id." exists");
        }

        $this->sections[ $args['section'] ][ '_settings' ][ $id ] = $args;
    }

    /**
     * Render Panel
     *
     * @return void
     */
    function render_panel(){

        $output = '<div id="skt_editor_panel" data-level="1">';
        $output .= '<div class="skt__content">';
        $output .= '<div class="skt__level_1">';

        // level 1
        foreach ( $this->panels as $panel_id => $panel_data ) {

            $output .= '<div class="skt__group skt__group_'.$panel_id.'">';
            $output .= '<span class="skt__group_title">'.$panel_data['title'].'</span>';
            $output .= '<ul>';

            foreach ( $panel_data['_sections'] as $section_id => $section_data ){
                
                $output .= '
                <li data-target="'.$section_id.'" data-title="'.$section_data['title'].'">
                    <span>'.$section_data['title'].'</span>
                </li>';
            }

            $output .= '</ul>';
            $output .= '</div><!--skt__group-->';
        }

        $output .= '</div>'; // end level 1

        $output .= '<div class="skt__level_2_title">
                        <span class="__arrow dashicons dashicons-arrow-left-alt2"></span>
                        <span class="__text">NONE</span>
                    </div>';

        // level 2 
        foreach( $this->sections as $section_id => $section_data ) {

            $output .= '<ul class="skt__level_2 __target_'.$section_id.'">';

            foreach( $section_data['_settings'] as $setting_id => $setting_data ){
                $output .= '<li class="skt__setting skt__setting_'.$setting_id.' '.$setting_data['class'].'">' . $setting_data['output'] . '</li>';
            }

            $output .= '</ul>';
        }

        $output .= '</div><!--skt__content-->';
        $output .= '</div><!--skt_editor_panel-->';

        echo $output;
    }
}