<?php 

namespace Delabon\WP;

if( ! class_exists( Request::class ) ){
    return;
}

class Request{

    private $_method;
    private $_url;
    private $_path;
    private $_sanitized_path;

    /**
     * Return method
     * @return string
     */
    public function method(){
        if( $this->_method === null ){
            $this->_method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        }
        return $this->_method;
    }

    /**
     * @return string
     */
    public function url(){
        if( $this->_url === null ){
            $this->_url = isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
            $this->_url .= '://'.$_SERVER['HTTP_HOST'];
            //$this->_url .= in_array($_SERVER['SERVER_PORT'], array('80', '443')) ? '' : ':'.$_SERVER['SERVER_PORT'];
            $this->_url .= $_SERVER['REQUEST_URI'];    
        }
        return $this->_url;
    }

    /**
     * Return path
     * @return string
     */
    public function path(){
        if( $this->_path === null ){
            $this->_path = $_SERVER['REQUEST_URI'];
            $this->_path = preg_replace('`\?.*`', '', $this->_path );
            $this->_path = trim( $this->_path, '/' );
        }
        return $this->_path;
    }

    /**
     * Get all queries
     */
    public function queries(){
        return [
            'get' => $_GET,
            'post' => $_POST
        ];
    }

    /**
     * Check if key exists
     * @param string $key
     * @param string $method
     * @return mixed
     */
    public function isExists( $key, $method ){
        if( $method === 'get' ){
            return isset( $_GET[ $key ] );
        }
        else if( $method === 'post' ) {
            return isset( $_POST[ $key ] );
        }
    }

    /**
     * Get from post request
     * @param string $key
     * @param mixed $default
     * @return mixed|bool
     */
    public function post( $key, $default = null ){
        
        if( $this->isExists( $key, 'post' ) ){
            return $_POST[ $key ];
        }

        return $default;
    }

    /**
     * Get from get request
     * @param string $key
     * @param mixed $default
     * @return mixed|bool
     */
    public function get( $key, $default = null ){
        if( $this->isExists( $key, 'get' ) ){
            return $_GET[ $key ];
        }
        return $default;
    }

    /**
     * Method set
     */
    public function set( $method, $key, $value ){
        if( $method === 'get' ){
            $_GET[ $key ] = $value;
        }
        else if( $method === 'post' ){
            $_POST[ $key ] = $value;
        }
    }

    /**
     * Listening
     */
    public function listening( $method, $key, $callback ){
        if( $this->isExists( $key, $method ) ){
            $callback();
        }
    }

    /**
     * Get the request IP ( user ip )
     * @return string
     */
    function ip(){

        if ( !empty($_SERVER['HTTP_CLIENT_IP']) ){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

}
