<?php 

namespace SKYT;

use Delabon\WP\Helper;

/**
 * Campaign UI.
 *
 * An abstract class for creating new campaign UI.
 *
 * @abstract
 */
abstract class Campaign_UI{

    abstract function render(); 
    abstract function render_preview( $skin = null ); 

    /**
     * Setup & return urgency data
     *
     * @param Campaign $camapign
     * @return array
     */
    protected function urgency( $campaign ){

        $_return = [
            'urgency_type'                  => $campaign->setting('urgency_type'),
            'urgency_session_time'          => $campaign->setting('urgency_session_time'),
            'urgency_session_type'          => $campaign->setting('urgency_session_type'),
            'urgency_session_pause_time'    => $campaign->setting('urgency_session_pause_time'),    
            'urgency_date_expires'          => $campaign->setting('urgency_expire_date'),
            'urgency_date_timestamp'        => '',
            'urgency_coupon_left_text'      => '',
        ];

        if( $_return['urgency_type'] === 'disabled' ){
            return $_return;
        }

        $expiry_date = $_return['urgency_date_expires'];
        $_return['urgency_date_timestamp'] = $expiry_date === '' ? null : Helper::convert_timezone($expiry_date)->getTimestamp() * 1000;

        // Woocommerce
        if( (bool)$campaign->setting('is_coupon_enabled') ){

            if( ! class_exists('Woocommerce') ) {
                throw new \Exception("Woocommerce is required because coupon feature is enabled");
            }

            $coupon = $campaign->coupon();
    
            if( ! is_coupon_valid( $coupon ) ) {
                throw new \Exception("Invalid coupon");
            }

            $expiry_date_obj = $coupon->get_date_expires();
            $expiry_date = ! $expiry_date_obj ? null : $expiry_date_obj->format('Y-m-d');
            $expiry_date_timestamp = ! $expiry_date_obj ? null : $expiry_date_obj->getTimestamp() * 1000;

            $_return['urgency_date_expires'] = $expiry_date;
            $_return['urgency_date_timestamp'] = $expiry_date_timestamp;

            $coupons_left = $coupon->get_usage_limit() - $coupon->get_usage_count();
            $_return['urgency_coupon_left_text'] = esc_html( _n( '%d coupon left', '%d coupons left', $coupons_left, 'skytake') );
        }

        return $_return;
    }

    /**
     * Return the popup markup
     *
     * @param Campaign $campaign
     * @param bool $is_editor_mode
     * @return string
     */
    protected function get_markup( $campaign, $is_editor_mode, $skin = null ){

        $markup = '';

        ob_start();

            if( ! $skin ){
                $skin = $campaign->setting('template');
            }

            $tmpl_path = SKYTAKE_PATH . '/inc/skins/'.$skin.'/views/tmpl-'.$campaign->type().'.php';
            
            if( file_exists( $tmpl_path ) ){
                require $tmpl_path;
            }
            else{
                require SKYTAKE_PATH . '/inc/campaigns/'.$campaign->type().'/views/frontend/tmpl-index.php';
            }

            $markup = ob_get_contents();
        ob_end_clean();

        return $markup;
    }

    /**
     * Return the minimized bar markup
     *
     * @param Campaign $campaign
     * @return string
     */
    protected function get_minimized_bar_markup( $campaign ){

        $markup = '';

        ob_start();
            require SKYTAKE_PATH . '/inc/campaigns/'.$campaign->type().'/views/frontend/tmpl-bar.php';
            $markup = ob_get_contents();
        ob_end_clean();

        return $markup;
    }

}
