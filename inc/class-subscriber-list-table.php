<?php 

namespace SKYT;

use Delabon\WP\Helper;
use Delabon\WP\List_Table;

class Subscriber_List_table extends List_Table {

	/** Class constructor */
	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Subscriber', 'skytake' ), //singular name of the listed records
			'plural'   => __( 'Subscribers', 'skytake' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		] );
    }

    /**
     * Retrieve subscribers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    static function get_subscribers( $per_page = 10, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}skytake_emails";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
        else{
            $sql .= ' ORDER BY date_added DESC';
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
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}skytake_emails";

        return $wpdb->get_var( $sql );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns() {
        $columns = [
            'email'         => __( 'Email', 'skytake' ),
            'name_mobile'   => __( 'Name/Mobile', 'skytake' ),
            'coupon_code'   => __( 'Coupon code', 'skytake' ),
            'date'          => __( 'Date', 'skytake' ),
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
    function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'email':
                return $item[ $column_name ];
            case 'name_mobile':
                return $item[ 'name' ] . '<br>' . $item[ 'mobile' ];
            case 'coupon_code':
                return $item[ 'coupon_code' ];
            case 'date':
                return $item[ 'date_added' ];
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    function get_sortable_columns() {
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

        $this->items = self::get_subscribers( $per_page, $current_page );
    }

}
