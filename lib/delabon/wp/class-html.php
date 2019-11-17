<?php 

namespace Delabon\WP;

if( ! class_exists( HTML::class ) ){
    return;
}

class HTML{

    static $attrs_pattern = '/([^\s]+)=[\"\'](.*?)[\"\']/i';

    /**
     * Return attributtes string
     * @param string $tag
     * @return array 
     */
    static function getAttributes( $tag ){

        $attrs = array();
        preg_match_all( self::$attrs_pattern, $tag, $matches );

        if( is_array( $matches ) && isset( $matches[1] ) ){

            $iii = 0;

            foreach( $matches[1] as $attr ){
                $attrs[ $attr ] = $matches[2][ $iii ];
                $iii += 1;
            }

        }

        return $attrs;
    }

    /**
     * Return tag properties string
     * @param array $atts
     * @return string 
     */
    static function tag_properties( $atts ){

        $atts = self::removeAtts( $atts, array( 'id', 'class', 'title', 'description', 'wrapper', 'wrapper_class' ) );
        $attributesStr = '';
        
        foreach( $atts as $attr => $value ){

            if( is_array( $value ) ) {
                continue; // skip arrays
            }

            if( $value === null ){
                continue;
            }

            $attributesStr .= ' ' . $attr . '="' .$value. '"';
        }

        return $attributesStr;
    }

    /**
     * Remove unnecessary keys
     * @param array $atts
     * @param array $keys
     */
    static function removeAtts( $atts, $keys ){

        foreach ( $keys as $key ) {
            unset( $atts[ $key ] );
        }

        return $atts;
    }

    /**
     * Create title tag
     * @param string|null $title 
     */
    static function titleTag( $title ){

        if( ! $title ) return;

        return '<h3 class="dlb_input_title">' . $title . '</h3>';
    }

    /**
     * Create description tag
     * @param string|null $description 
     */
    static function descriptionTag( $description ){

        if( ! $description ) return;

        return '<p class="dlb_input_description">' . $description . '</p>';
    }

    /**
     * Create wrapper
     * @param boolean $condition 
     * @param string $output
     * @param string $class 
     */
    static function wrapper( $output, $condition = true, $class = '' ){

        if( ! $condition ) return $output;

        return '<div class="dlb_input_wrapper ' . $class . '">' . $output . '</div>';
    }

    /**
     * html input ( text,email,password,hidden,file ...)
     * @param array $atts
     * @return string
     */
    static function input( $atts = array() ){
        
        $atts = wp_parse_args( $atts, array(
            'type' => 'text',
            'id' => '',
            'name' => '',
            'value' => '',
            'class' => '',
            'placeholder' => '',
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
        ));

        $atts['value'] = htmlentities( $atts['value'] ); // fixes input quotes issue

        extract( $atts );

        $input_attributes = self::tag_properties( self::removeAtts( $atts, [ 'id', 'class', 'title', 'description', 'wrapper', 'wrapper_class' ] ) );

        $output = '
            <div id="'.$id.'" class="dlb_input dlb_input_text '.$class.'">
                <input class="__input" '.$input_attributes.'>
            </div>    
        ';
        
        return self::wrapper( 
            self::titleTag( $title ) . $output . self::descriptionTag( $description ), 
            $wrapper, 
            $wrapper_class 
        );
    }

    /**
     * html date picker
     * @param array $atts
     * @return string
     */
    static function datepicker( $atts = array() ){

        $atts = wp_parse_args( $atts, array(
            'type' => 'text',
            'id' => '',
            'name' => '',
            'value' => '',
            'class' => '',
            'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
            'placeholder' => '',
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
        ));

        $atts['class'] = $atts['class'] . ' dlb_input_datepicker';

        return self::input($atts);
    }

    /**
     * html color picker
     * @param array $atts
     * @return string
     */
    static function colorPicker( $atts = array() ){

        $atts = wp_parse_args( $atts, array(
            'type' => 'text',
            'id' => '',
            'name' => '',
            'value' => '',
            'class' => '',
            'placeholder' => '',
            'data-default-color' => '',
            'data-show-opacity' => true,
            'data-palette'	=> 'rgba( 150, 50, 220, 0.8)|rgba( 50, 50, 50, 0.8)|rgba( 120, 90, 150, 0.8 )|rgba(108, 92, 231, 0.8)|rgba(253, 203, 110, 0.8)|rgba(253, 121, 168, 0.8)',
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
        ));

        $atts['class'] = $atts['class'] . ' dlb_input_colorpicker';

        return self::input($atts);
    }

    /**
     * html color picker
     * @param array $atts
     * @return string
     */
    static function number( $atts = array() ){

        $atts = wp_parse_args( $atts, array(
            'type' => 'number',
            'id' => '',
            'name' => '',
            'value' => '',
            'class' => '',
            'placeholder' => '',
            'min'   => 0,
            'max'   => 9999999,
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
        ));

        return self::input($atts);
    }

    /**
     * html textarea
     * @param array $atts
     * @return string
     */
    static function textarea( $atts = array() ){

        $atts = wp_parse_args( $atts, array(
            'id' => '',
            'class' => '',
            'name' => '',
            'value' => '',
            'style' => 'min-height:100px;',
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
        ));
        
        extract( $atts );

        $input_attributes = self::tag_properties( self::removeAtts( $atts, [ 'id', 'class', 'title', 'description', 'wrapper', 'wrapper_class', 'value' ] ) );

        $output = '
            <div id="'.$id.'" class="dlb_input dlb_input_textarea '.$class.'">
                <textarea class="__input" name="'.$name.'" '.$input_attributes.' >' . $value . '</textarea>
            </div>        
        ';

        return self::wrapper( 
            self::titleTag( $title ) . $output . self::descriptionTag( $description ), 
            $wrapper, 
            $wrapper_class 
        );
    }

    /**
     * html select
     * @param array $atts
     * @return string
     */
    static function select( $atts = array() ){

        $atts = wp_parse_args( $atts, array(
            'id' => '',
            'class' => '',
            'name' => '',
            'value' => '',
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
            'items' => array()
        ));
                    
        extract( $atts );            

        $input_attributes = self::tag_properties( self::removeAtts( $atts, [ 'id', 'class', 'title', 'description', 'wrapper', 'wrapper_class', 'items', 'value' ] ) );

        $list_options = '';
        $select_options = '';
        foreach( $items as $item_key => $item_name ){
            $list_options .= '<div data-value="'.$item_key.'">'.$item_name.'</div>';
            $select_options .= '<option value="'.$item_key.'" '.( $item_key === $value ? ' selected="selected" ' : '' ).'>'.$item_name.'</option>';
        }
        
        $output  = '
            <div id="'.$id.'" class="dlb_input dlb_input_select '.$class.'" >
                <select class="__input" '.$input_attributes.' >'.$select_options.'</select>
                <div class="__selected">'.$items[ $value ].'</div>
                <div class="__list">'.$list_options.'</div>
            </div>
        ';

        return self::wrapper( 
            self::titleTag( $title ) . $output . self::descriptionTag( $description ), 
            $wrapper, 
            $wrapper_class 
        );
    }

    /**
     * html radio
     * @param array $atts
     * @return string
     */
    static function radio( $atts = array() ){
        
        $atts = wp_parse_args( $atts, array(
            'id' => '',
            'name' => '',
            'value' => '',
            'class' => '',
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
            'items' => array(),
        ));

        extract( $atts );

        $output = '';
        
        foreach( $items as $item => $item_key ){
            $output .= '
                <div class="dlb_input dlb_input_radio '.( $item == $value ? '__selected' : '' ).'">
                    <input type="radio" class="__input" '.( $item == $value ? 'checked="checked"' : '' ).' name="'.$name.'" value="'.$item.'" >
                    '.$item_key.'
                </div>';
        }

        return self::wrapper( 
            self::titleTag( $title ) . $output . self::descriptionTag( $description ), 
            $wrapper, 
            $wrapper_class 
        );
    }

    /**
     * html radio images
     * @param array $atts
     * @return string
     */
    static function radio_images( $atts = array() ){
        
        $atts = wp_parse_args( $atts, array(
            'type' => 'text',
            'name' => '',
            'value' => '',
            'class' => '',
            'id' => '',
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
            'items' => array(),
        ));

        extract( $atts );

        $output = '<div class="dlb_input dlb_input_radio dlb_input_radio_images">';
        
        foreach( $items as $key => $item ){
            $output .= '
                <label>
                    <span>'.$item['title'].'</span>
                    <input class="__input" name="'.$name.'" type="radio" '.( $key == $value ? 'checked="checked"' : '' ).' value="'.$key.'" >
                    <img src="'.$item['url'].'" >
                </label>';
        }

        $output .= '</div>';

        return self::wrapper( 
            self::titleTag( $title ) . $output . self::descriptionTag( $description ), 
            $wrapper, 
            $wrapper_class
        );
    }

    /**
     * html radio icons
     * @param array $atts
     * @return string
     */
    static function radio_icons( $atts = array() ){
        
        $atts = wp_parse_args( $atts, array(
            'name' => '',
            'value' => '',
            'class' => '',
            'id' => '',
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
            'items' => array(),
        ));

        extract( $atts );

        $output = '<div class="dlb_input dlb_input_radio dlb_radio_icons_wrapper">';
        
        foreach( $items as $key ){
            $output .= '
                <label '.( $key == $value ? 'class="__active"' : '' ).'>
                    <input class="__input" name="'.$name.'" type="radio" '.( $key == $value ? 'checked="checked"' : '' ).' value="'.$key.'" >
                    <span class="'.$key .'"></span>
                </label>';
        }

        $output .= '</div>';

        return self::wrapper( 
            self::titleTag( $title ) . $output . self::descriptionTag( $description ), 
            $wrapper, 
            $wrapper_class
        );
    }

    /**
     * html wp image upload
     * @param array $atts
     * @return string
     */
    static function image_upload( $atts = array() ){

        $atts = wp_parse_args( $atts, array(
            'id' => '',
            'name' => '',
            'value' => '',
            'class' => '',
            'placeholder' => 'Select Image',
            'change_btn_text' => 'Change Image',
            'remove_btn_text' => 'Remove',
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
        ));

        extract( $atts );

        $output = '
            <div id="'.$id.'" class="dlb_input dlb_input_upload '.$class.'">

                <input class="__input" type="hidden" value="'.$value.'" name="'.$name.'">

                <div class="dlb_upload_holder '.($value === '' ? '' : '__hidden' ).'">'.$placeholder.'</div>

                <div class="dlb_uploaded_image '.($value === '' ? '__hidden' : '' ).'">
                    
                    <img src="'.$value.'" >

                    <div>
                        <button class="button dlb_upload_btn">'.$change_btn_text.'</button>
                        <button class="button dlb_upload_remove_btn">'.$remove_btn_text.'</button>
                    </div>
                </div>

            </div>    
        ';

        return self::wrapper( 
            self::titleTag( $title ) . $output . self::descriptionTag( $description ), 
            $wrapper, 
            $wrapper_class 
        );
    }

    /**
     * html checkbox
     * @param array $items
     * @param array $atts
     * @return string
     */
    static function checkbox( $atts = array() ){

        $atts = wp_parse_args( $atts, array(
            'id' => '',
            'name' => '',
            'value' => 0,
            'class' => '',
            'text' => '',
            'title' => null,
            'description' => null,
            'wrapper' => true,
            'wrapper_class' => '',
        ));

        extract( $atts );

        $input_attributes = self::tag_properties( self::removeAtts( $atts, [ 'id', 'class', 'title', 'description', 'wrapper', 'wrapper_class', 'text' ] ) );

        $output  = '
            <label id="'.$id.'" class="dlb_input dlb_input_checkbox '.$class.'" data-checked="'.$value.'">

                <input class="__input" type="hidden" '.$input_attributes.' >
                                        
                <span class="__toggle"></span>  

                <span class="__text">'.$text.'</span>

            </label>
        ';

        return self::wrapper( 
            self::titleTag( $title ) . $output . self::descriptionTag( $description ), 
            $wrapper, 
            $wrapper_class 
        );
    }

    /**
     * html two inputs
     * @param array $atts
     * @param string $input_1
     * @param string $input_2 
     * @return string
     */
    static function twoInputs( $atts = array(), $input_1, $input_2 ){
        
        $atts = wp_parse_args( $atts, array(
            'id' => '',
            'class' => '',
            'title' => null,
            'description' => null,
            'wrapper_class' => '',
        ));
        
        extract( $atts );
        
        return self::wrapper( 
            self::titleTag( $title ) . '<div id="'.$id.'" class="__two_inputs_content">' . $input_1 . $input_2 . '</div>' . self::descriptionTag( $description ), 
            true, 
            ' __two_inputs ' . $wrapper_class 
        );
    }

    /**
     * Magic method for open/close tag
     */
    static function __callStatic( $method, $args ){

        $tagname = strtolower( $method );
        $content = isset($args[0]) ? $args[0] : '';
        $atts = isset($args[1]) ? $args[1] : array();

        $atts = wp_parse_args( $atts, array(
            'id' => '',
            'class' => '',
        ));

        return '<' .$tagname.' id="'.$atts['id'].'" class="'.$atts['class'].'" '.self::tag_properties($atts).' >' . $content . '</' .$tagname . '>';
    }

}
