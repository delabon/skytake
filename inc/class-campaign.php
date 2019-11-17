<?php 

namespace SKYT;

use Delabon\WP\Helper;

class Campaign{

    private $data;
    private $coupon;

    function __construct( $campaign ){
        $this->data = $campaign;
    }

    /**
     * Retrieves active campaigns
     *
     * @return object|false
     */
    static function active(){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}skytake_campaigns WHERE c_status=1 OR c_type in ('widget-form', 'content-form')";
        $campaigns = $wpdb->get_results( $sql, ARRAY_A );
    
        if( ! $campaigns ){
            return false;
        }

        $_return = [];

        foreach ( $campaigns as $campaign ){
            $campaign['c_settings'] = unserialize( $campaign['c_settings'] );
            $campaign['c_statistics'] = unserialize( $campaign['c_statistics'] );

            $_return[] = new Campaign( $campaign );
        }
        
        return $_return;
    }

    /**
     * Retrieves a campaign by id
     * 
     * @param int $id
     * @return object|false
     */
    static function get( $id ){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}skytake_campaigns WHERE id=" . (int)$id;        
        $campaign = $wpdb->get_row( $sql, ARRAY_A );
    
        if( ! $campaign ){
            return false;
        }
    
        $campaign['c_settings'] = unserialize( $campaign['c_settings'] );
        $campaign['c_statistics'] = unserialize( $campaign['c_statistics'] );

        return new Campaign( $campaign );
    }

    /**
     * Update campaign
     *
     * @param int $id
     * @param array $data
     * @param array $format
     * @return bool
     */
    static function update( $id, $data, $format = null ){
        global $wpdb;
    
        $now = Helper::date_now();
        $data['date_updated'] = $now->format('Y-m-d H:i:s');
    
        if( $format ){
            $format[] = '%s';
        }
    
        $result = $wpdb->update( 
            $wpdb->prefix . 'skytake_campaigns',
            $data, 
            array( 'id' => (int)$id ), // where 
            $format, 
            array('%d') // where format
        );
    
        if( $result === false ){
            return false;
        }
    
        return true;
    }

    /**
     * add new campaign
     *
     * @param int $id
     * @param array $data
     * @param array $format
     * @return int
     */
    static function add( $type ){
        global $wpdb;
    
        $now = Helper::date_now();
        $settings = skytake()->defaultConfig['campaign'];
        $template_settings = get_skin_settings( $settings['template'], $type );

        $settings = wp_parse_args( $template_settings, $settings );

        $wpdb->insert(
            $wpdb->prefix.'skytake_campaigns', 
            array(
                'name' => '---',
                'c_type' => $type,
                'c_status' => 0,
                'c_statistics' => serialize(array( 'views' => 0, 'subscribers' => 0 )),
                'c_settings' => serialize( $settings ),
                'change_token' => uniqid(),
                'custom_css' => '',
                'date_created' => $now->format('Y-m-d H:i:s'),
                'date_updated' => $now->format('Y-m-d H:i:s'),
            ), 
            array(
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ) 
        );
    
        self::update( $wpdb->insert_id, [
            'name' => skytake()->defaultConfig['campaign_types'][ $type ] . ' ('.$wpdb->insert_id.')',
            'custom_css' => generate_css( $wpdb->insert_id, $type, $settings ),
        ]);

        return $wpdb->insert_id;
    }
    
    /**
     * Get setting value
     *
     * @param string $name
     * @param mixed $template
     * @return mixed
     */
    function setting( $name, $default = null ){

        if( isset( $this->data['c_settings'][ $name ] ) ){
            return $this->data['c_settings'][ $name ];
        }

        if( isset( skytake()->defaultConfig['campaign'][ $name ] ) ){
            return skytake()->defaultConfig['campaign'][ $name ];
        }

        return $default;
    }

    /**
     * Get setting value
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    function setSetting( $name, $value ){
        $this->data['c_settings'][ $name ] = $value;
    }

    /**
     * Get stats value
     *
     * @param string $key
     * @return int|false
     */
    function statistic( $key ){

        if( isset( $this->data['c_statistics'][ $key ] ) ){
            return $this->data['c_statistics'][ $key ];
        }

        return false;
    }

    /**
     * Get stats value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function setStatistic( $key, $value ){
        $this->data['c_statistics'][ $key ] = $value;
    }

    /**
     * Get id
     *
     * @return array
     */
    function id(){
        return $this->data['id'];
    }

    /**
     * Get Status
     *
     * @return int
     */
    function status(){
        return (int)$this->data['c_status'];
    }

    /**
     * Get name
     *
     * @return array
     */
    function name(){
        return $this->data['name'];
    }

    /**
     * Set name
     *
     * @param string $value
     * @return void
     */
    function setName( $value ){
        $this->data['name'] = $value;
    }

    /**
     * Get name
     *
     * @return array
     */
    function date_updated(){
        return $this->data['date_updated'];
    }
    
    /**
     * Get all settings
     *
     * @return array
     */
    function settings(){
        return $this->data['c_settings'];
    }

    /**
     * Get all statistics
     *
     * @return array
     */
    function statistics(){
        return $this->data['c_statistics'];
    }

    /**
     * Get coupon
     *
     * @return mixed
     */
    function coupon(){

        if( $this->coupon ){
            return $this->coupon;
        }

        $this->coupon = get_coupon( $this->setting('selected_coupon') );

        return $this->coupon;
    }

    /**
     * Get custom css
     *
     * @return array
     */
    function custom_css(){
        return $this->data['custom_css'];
    }

    /**
     * Get type
     *
     * @return array
     */
    function type(){
        return $this->data['c_type'];
    }

    /**
     * Get change token
     *
     * @return array
     */
    function change_token(){
        return $this->data['change_token'];
    }
    
}
