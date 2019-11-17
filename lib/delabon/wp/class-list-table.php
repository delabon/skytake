<?php 

namespace Delabon\WP;

if( ! class_exists( List_Table::class ) ){
    return;
}

/**
 * Our version of WP_List_Table
 */
class List_Table {

    protected $items;
    protected $_pagination_args;
    protected $_args = [];

    function __construct($args) {
        $this->_args = $args;
    }

	/**
	 * Message to be displayed when there are no items
	 */
	function no_items() {
		_e( 'No items found.' );
	}

    /**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @return array
	 */
	function get_columns() {
		die( 'function WP_List_Table::get_columns() must be over-ridden in a sub-class.' );
	}

    /**
     * How to display each columns
	 * @param array $item
	 * @param string $column_name
	 */
	protected function column_default( $item, $column_name ) {}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array();
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 */
	function prepare_items() {
		die( 'function WP_List_Table::prepare_items() must be over-ridden in a sub-class.' );
	}

	/**
	 * Get the current page number
	 *
	 * @return int
	 */
	function get_pagenum() {
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;

		if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] ) {
			$pagenum = $this->_pagination_args['total_pages'];
		}

		return max( 1, $pagenum );
	}

    /**
	 * An internal method that sets all the necessary pagination arguments
	 *
	 * @param array|string $args Array or string of arguments with information about the pagination.
	 */
	protected function set_pagination_args( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'total_items' => 0,
				'total_pages' => 0,
				'per_page'    => 0,
			)
		);

		if ( ! $args['total_pages'] && $args['per_page'] > 0 ) {
			$args['total_pages'] = ceil( $args['total_items'] / $args['per_page'] );
		}

		// Redirect if page number is invalid and headers are not already sent.
		if ( ! headers_sent() && ! wp_doing_ajax() && $args['total_pages'] > 0 && $this->get_pagenum() > $args['total_pages'] ) {
			wp_redirect( add_query_arg( 'paged', $args['total_pages'] ) );
			exit;
		}

		$this->_pagination_args = $args;
	}

	/**
	 * Return number of visible columns
	 *
	 * @return int
	 */
	function get_column_count() {
		return count( $this->get_columns() );
	}

	/**
	 * Display the table
	 */
	function display() {
		$singular = $this->_args['singular'];

		$this->display_tablenav( 'top' );

		?>
            <table class="dlb_list_table">

                <thead>
                    <tr>
                        <?php $this->print_column_headers(); ?>
                    </tr>
                </thead>

                <tbody id="the-list"
                    <?php
                    if ( $singular ) {
                        echo " data-wp-lists='list:$singular'";
                    }
                    ?>
                    >
                    <?php $this->display_rows_or_placeholder(); ?>
                </tbody>

                <tfoot>
                    <tr>
                        <?php $this->print_column_headers( false ); ?>
                    </tr>
                </tfoot>

            </table>
		<?php
		$this->display_tablenav( 'bottom' );
    }

    /**
	 * Generate the tbody element for the list table.
	 */
	public function display_rows_or_placeholder() {
        
        if( ! $this->has_items() ) {

			echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
			    $this->no_items();
            echo '</td></tr>';
            
            return;
        }

        foreach ( $this->items as $item ) {

            echo '<tr>';

            foreach ( $this->get_columns() as $column_name => $column_display_name ) {

				$classes = "$column_name column-$column_name";
				$data = 'data-colname="' . wp_strip_all_tags( $column_display_name ) . '"';
				$attributes = "class='$classes' $data";
	
                echo "<td $attributes>";
                echo $this->column_default( $item, $column_name );
                echo '</td>';
            
            }
            echo '</tr>';
        }

    }
    
    /**
	 * Print column headers, accounting for hidden and sortable columns.
	 *
	 * @staticvar int $cb_counter
	 *
	 * @param bool $with_id Whether to set the id attribute or not
	 */
    protected function print_column_headers( $with_id = true  ){

        $columns = $this->get_columns();
        $sortable_columns = $this->get_sortable_columns();
        $markup = '';

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( 'paged', $current_url );

        foreach ( $columns as $key => $text ){
            
            // sortable
            if( array_key_exists( $key, $sortable_columns ) ){

                $key_data = $sortable_columns[ $key ];

                $markup .= '
                    <th scope="col" id="'.$key.'" class="manage-column column-name column-primary sortable asc">
                        <a href="'.$current_url.'&amp;orderby=name&amp;order=desc">
                            <span>'.$text.'</span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>';
            }
            else{
				
                $markup .= '<th scope="col" id="'.$key.'" class="manage-column column-'.$key.'">'.$text.'</th>';
            }

        }

        echo $markup;
    }
 
	/**
	 * Generate the table navigation above or below the table
	 *
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
		?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>">

                <?php
                    $this->pagination( $which );
                ?>

                <br class="clear" />
            </div>
		<?php
    }
    
	/**
	 * Whether the table has items to display or not
	 *
	 * @return bool
	 */
	function has_items() {
		return ! empty( $this->items );
    }
	
	/**
	 * Display the pagination.
	 *
	 * @since 3.1.0
	 *
	 * @param string $which
	 */
	protected function pagination( $which ) {
		if ( empty( $this->_pagination_args ) ) {
			return;
		}

		$total_items     = $this->_pagination_args['total_items'];
		$total_pages     = $this->_pagination_args['total_pages'];
		$infinite_scroll = false;
		if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
			$infinite_scroll = $this->_pagination_args['infinite_scroll'];
		}

		$output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current              = $this->get_pagenum();
		$removable_query_args = wp_removable_query_args();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( $removable_query_args, $current_url );

		$page_links = array();

		$total_pages_before = '<span class="paging-input">';
		$total_pages_after  = '</span></span>';

		$disable_first = $disable_last = $disable_prev = $disable_next = false;

		if ( $current == 1 ) {
			$disable_first = true;
			$disable_prev  = true;
		}
		if ( $current == 2 ) {
			$disable_first = true;
		}
		if ( $current == $total_pages ) {
			$disable_last = true;
			$disable_next = true;
		}
		if ( $current == $total_pages - 1 ) {
			$disable_last = true;
		}

		if ( $disable_first ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='first-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				__( 'First page' ),
				'&laquo;'
			);
		}

		if ( $disable_prev ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='prev-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', max( 1, $current - 1 ), $current_url ) ),
				__( 'Previous page' ),
				'&lsaquo;'
			);
		}

		if ( 'bottom' === $which ) {
			$html_current_page  = $current;
			$total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
		} else {
			$html_current_page = sprintf(
				"%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
				'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
				$current,
				strlen( $total_pages )
			);
		}
		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[]     = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

		if ( $disable_next ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='next-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ) ),
				__( 'Next page' ),
				'&rsaquo;'
			);
		}

		if ( $disable_last ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='last-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
				__( 'Last page' ),
				'&raquo;'
			);
		}

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) ) {
			$pagination_links_class .= ' hide-if-js';
		}
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			$page_class = ' no-pages';
		}
		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}
}
