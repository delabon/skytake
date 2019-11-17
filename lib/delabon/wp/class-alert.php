<?php 

namespace Delabon\WP;

if( ! class_exists( Alert::class ) ){
    return;
}

class Alert{

    /**
     * Success alert
     * @param string $message
     * @param bool $dismiss
     */
    public static function success( $message, $dismiss = true ){
        return self::notice( 'success', $message, $dismiss );
    } 

    /**
     * Error alert
     * @param string $message
     * @param bool $dismiss
     */
    public static function error( $message, $dismiss = true ){
        return self::notice( 'error', $message, $dismiss );
    } 

    /**
     * Info alert
     * @param string $message
     * @param bool $dismiss
     */
    public static function info( $message, $dismiss = true ){
        return self::notice( 'info', $message, $dismiss );
    } 

    /**
     * Warning alert
     * @param string $message
     * @param bool $dismiss
     */
    public static function warning( $message, $dismiss = true ){
        return self::notice( 'warning', $message, $dismiss );
    } 

    /**
     * Success alert
     * @param string $type error, warning, success, or info
     * @param string $message
     * @param bool $dismiss
     */
    public static function notice( $type, $message, $dismiss = true ){
        return '
            <div class="dlb_alert __'.$type.'">
                <p>'.$message.'</p>
                '.( $dismiss ? '<span class="__is_dismissible">&times;</span>' : '' ).'
            </div>
        ';
    } 
}
