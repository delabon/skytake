<?php 

namespace Delabon\WP;

if( ! class_exists( Helper::class ) ){
    return;
}

class Helper {

    /**
     * Dumper
     */
    static function dump( $d, $die = true, $var_dump = false ){

        if( $var_dump ){
            echo '<pre>', var_dump( $d ), '</pre>';
        }
        else{
            echo '<pre>', print_r( $d, true ), '</pre>';
        }

        if( $die ) die;
    }

    /**
     * Curl Helper
     *
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param array $post_data
     * @return array|false
     */
    static function curl( $url, $method = 'GET', $headers = array(), $post_data = array() ){
         
        $method = strtoupper( $method );
        $mch = curl_init();
        
        curl_setopt($mch, CURLOPT_URL, $url );
        curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($mch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
        curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $method); // POST/GET/PATCH/PUT/DELETE
        curl_setopt($mch, CURLOPT_TIMEOUT, 10);
        curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection
     
        if( $method !== 'GET' ) {
            curl_setopt($mch, CURLOPT_POST, true);
            curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($post_data) ); // send data in json
        }

        $result = curl_exec($mch);

        //close cURL resource
        curl_close($mch);
     
        return $result;
    }

    /**
     * Returns DateTime object of the current time with the WP timezone
     *
     * @return DateTime
     */
    static function date_now(){
        return new \DateTime( "now", Timezone::getWpTimezone() );
    }

    /**
     * Convert a date to the wordpress timezone
     *@param string|DateTime $date
    * @return DateTime
    */
    static function convert_timezone( $date = null ){

        $timezone = Timezone::getWpTimezone();

        if( ! $date ){
            $date = new \DateTime();
        }

        if( is_string( $date ) ) {
            $date = new \DateTime( $date );
        }

        $date->setTimezone( $timezone );

        return $date;
    }

    /**
     * Include file if exist
     *
     * @param string $path 
     * @return void
     */
    static function include( $path ){
        if( file_exists($path) ){
            include $path;
        }
    }

    /**
     * Return a sanitized url
     * @param string $url
     * @return string
     */
    static function sanitize( $url ){
        $url = str_replace( array('_','-'), '', $url );
        $url = trim( $url, '/' );
        $url = str_replace( '/', '_', $url );
        return preg_replace( '`[^A-Za-z0-9\_]`', '', $url );
    }

    /**
     * Sanitize path
     * @param string $path
     * @return string
     */
    static function sanitizedPath( $path ){
        $name = sanitize( $path );
        if( empty( $name ) ) $name = 'index';
        return $name;
    }

    /**
     * is user logged in from cookies
     * @return bool
     */
    static function is_loggedin(){
        foreach( $_COOKIE as $cookie => $value ) {
            if( strpos( $cookie, '_logged_in_') !== false ){
                return true;
            }
        }
        return false;
    }

    /**
     * Return excluded array from string
     * @return array
     */
    static function excludedListToArray( $value ){
        if( empty( $value ) ) return array();

        $value = preg_replace('/\s?\n/', ',', $value);
        $value = str_replace( ' ', ',', $value );
        $value = trim( preg_replace( '/,,/', ',', $value ), ',' );

        if( strpos( $value , ',' ) !== false ){
            return explode( ',', $value );
        }

        return array( $value );
    }

    /**
     * is asset expluded ?
     * @param string $link
     * @param array|string $list
     * @return bool
     */
    static function isExcluded( $link, $list ){

        if( ! is_array( $list ) ){
            $list = excludedListToArray( $list );
        }

        $target_parts = parse_url( $link );
        $target_path = $target_parts['path'];
        $target_path = preg_replace('`\/$`', '', $target_path);

        if( isset( $target_parts['query'] ) ){
            $target_path .= '?' . $target_parts['query'];
        }

        foreach( $list as $excluded ){

            $excluded_parts = parse_url( $excluded );
            $excluded_path = $excluded_parts['path'];
            $excluded_path = preg_replace('`\/$`', '', $excluded_path);

            if( isset( $excluded_parts['query'] ) ){
                $excluded_path .= '?' . $excluded_parts['query'];
            }

            if( $target_path === $excluded_path ){ 
                return true;
            }
        }

        return false;
    }

    /** 
     * days_in_month($month, $year) 
     * Returns the number of days in a given month and year, taking into account leap years. 
     * 
     * $month: numeric month (integers 1-12) 
     * $year: numeric year (any integer) 
     * 
     * Prec: $month is an integer between 1 and 12, inclusive, and $year is an integer. 
     * Post: none 
    **/ 
    static function days_in_month($month, $year) { 
        // calculate number of days in a month 
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); 
    } 


}
