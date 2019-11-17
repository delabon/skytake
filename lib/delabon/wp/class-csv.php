<?php 

namespace Delabon\WP;

if( ! class_exists( CSV::class ) ){
    return;
}

class CSV{

    private $header_columns = array();
    private $rows = array();

    /**
     * Add header columns
     *
     * @param array $columns
     * @return void
     */
    function add_header_columns( $columns = array() ){
        if( ! empty( $columns ) ){
            $this->header_columns = $columns;
        }
    }

    /**
     * Add row
     *
     * @param array $columns
     * @return void
     */
    function add_row( $columns = array() ){
        if( ! empty( $columns ) ){
            $this->rows[] = $columns;
        }
    }

    /**
     * Export date to csv file for download
     * 
     * @param string $filename
     */
    function export( $filename = 'export'){

        $str = '';

        foreach( $this->header_columns as $column ) {
            $str .= ','.$column;
        } 

        $str = trim( $str, ',' );

        foreach( $this->rows as $row ) {
            
            $str .= PHP_EOL;
            $str_col = '';

            foreach( $row as $column ) {
                $str_col .= ',' . $column;
            } 

            $str .= trim( $str_col, ',' );
        } 
        
        header( 'Content-Disposition: attachment; filename="'.$filename.'.csv"' );
        header( 'Content-Type: text/csv' );
        header( 'Content-Length: ' . strlen($str) );
        header( 'Connection: close' );

        echo $str;
    }
    
}
