<?php 

namespace Delabon\WP;

if( ! class_exists( Autoload::class ) ){
    return;
}

class Autoload{

    private static $namespaces = array();

    /**
     * Add Namespace
     */
    static function add( $namespace, $path ){
        if( ! isset( self::$namespaces[ $namespace] ) ){
            self::$namespaces[ $namespace ] = $path;
        }
    }

    /**
     * Get Namespaces
     */
    static function get(){
        return self::$namespaces;
    }
    
    /**
     * Load called classes
     */
    static function init(){

        spl_autoload_register( function( $className ){

            $parts = explode('\\', $className);

            if( ! is_array( $parts ) || ! isset( $parts[0] ) ){
                return false;
            }

            if( ! isset( self::$namespaces[ $parts[0] ] ) ) return;

            $path = self::$namespaces[ $parts[0] ];
            $count = count( $parts );

            for( $i=0; $i < $count; $i++) { 

                if( $i === 0 ){
                    continue;
                }
                else if( $i === $count - 1 ){
                    $path .= '/class-' . $parts[ $i ] . '.php';
                }
                else{
                    $path .= '/' . $parts[ $i ];
                }
            }

            require_once strtolower( str_replace( '_', '-', $path ) );
        });

    }

}
