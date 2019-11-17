<?php 

namespace SKYT;

use Delabon\WP\HTML;
use Delabon\WP\List_Table;

class Campaign_List_table extends List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Campaign', 'skytake' ), //singular name of the listed records
			'plural'   => __( 'Campaigns', 'skytake' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		] );
    }

    /**
     * Retrieve campaigns data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    static function get_campaigns( $per_page = 10, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}skytake_campaigns";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
        else{
            $sql .= ' ORDER BY c_status DESC, id DESC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    static function record_count() {
        return total_campaigns();
    }
    
    /** Text displayed when no customer data is available */
    function no_items() {
        _e( 'No campaigns avaliable.', 'skytake' );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
            'name'      => __( 'Name', 'skytake' ),
            'c_type'    => __( 'Type', 'skytake' ),
            'c_status'  => __( 'Status', 'skytake' ),
            'tags'      => __( 'Tags', 'skytake' ),
            'c_statistics'    => __( 'Statistics', 'skytake' )
        ];

        return $columns;
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {

        $item['c_settings'] = unserialize( $item['c_settings'] );
        $item['c_statistics'] = unserialize( $item['c_statistics'] );

        switch ( $column_name ) {
            
            case 'name': 

                return '<strong>' . $item['name'] . '</strong>
                    <br><br>
                    <a href="'.admin_url('admin.php?action=skytake_editor&campaign_id='.absint( $item['id'] ) ).'" class="button button-primary">'.__('Edit', 'skytake').'</a>
                    <a class="button skytake_list_action __action_duplicate" data-id="'.absint( $item['id'] ).'" >'.__('Duplicate', 'skytake').'</a>
                    <a class="button skytake_list_action __action_delete" data-id="'.absint( $item['id'] ).'">'.__('Delete', 'skytake').'</a>';

            case 'c_type':

                return skytake()->defaultConfig['campaign_types'][ $item[ $column_name ] ];
            
            case 'tags':

                $output = '';

                if( $item['c_settings']['is_coupon_enabled'] == 1 ){
                    $output .= '<span class="skytake_tag __purple">
                                    <span class="dashicons dashicons-yes"></span>
                                    Woocommerce
                                </span>';
                }

                if( $item['c_settings']['is_mailchimp_enabled'] == 1 ){
                    $output .= '<span class="skytake_tag __yellow">
                                    <span class="dashicons dashicons-yes"></span>
                                    Mailchimp
                                </span>';
                }

                if( $item['c_settings']['urgency_type'] != 'disabled' ){
                    $output .= '<span class="skytake_tag __red">
                                    <span class="dashicons dashicons-yes"></span>
                                    '.__('Urgency', 'skytake').'
                                </span>';
                }

                if( $item['c_settings']['urgency_type'] != 'never' ){
                    $output .= '<span class="skytake_tag __green">
                                    <span class="dashicons dashicons-yes"></span>
                                    '.__('Minimized Bar', 'skytake').'
                                </span>';
                }

                return $output;

            case 'c_status':

                if( in_array( $item['c_type'], [ 'widget-form', 'content-form' ] ) ){
                    return __('Always enabled', 'skytake');
                }
                else{
                    return HTML::checkbox(array(
                        'value' => $item[ $column_name ],
                        'class' => 'skytake_list_action __action_status_toggle',
                        'data-campaign' => $item['id'],
                        'wrapper' => false,
                    ));
                }
            
            case 'c_statistics':
                
                if( ! is_premium() ){
                    return upgrade_tag();
                }
                else{
                    $conversion = 0;
    
                    if( (int)$item['c_statistics']['views'] != 0 ){
                        $conversion = ((int)$item['c_statistics']['subscribers'] / (int)$item['c_statistics']['views']) * 100;
                        $conversion = number_format( $conversion, 2 );
                    }
    
                    return '
                        <p>'.__('Views', 'skytake').': <strong>'.$item['c_statistics']['views'].'</strong></p>
                        <p>'.__('Conversions', 'skytake').': <strong>'.$item['c_statistics']['subscribers'].'</strong></p>
                        <p>'.__('Conversion Rate', 'skytake').': <strong>'.$conversion.'%</strong></p>
                    ';
                }

            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        return array();
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $per_page     = 10;
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );

        $this->items = self::get_campaigns( $per_page, $current_page );
    }

}
