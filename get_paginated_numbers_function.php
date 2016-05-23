/**
 * @author Pieter Goosen (original)
 * @author Chris Stein
 * @license GPLv3 
 * @link http://www.gnu.org/licenses/gpl-3.0.html
 *
 * This function returns numbered pagination links or text pagination links
 * depending what is been set
 *
 * Paginated numbered links uses get_pagenum_link() to return links to the
 * required pages
 * @uses http://wpseek.com/function/get_pagenum_link/
 *
 * The pagination links uses next_posts_link() and previous_posts_link()
 * @uses http://codex.wordpress.org/Function_Reference/next_posts_link
 * @uses http://codex.wordpress.org/Template_Tags/previous_posts_link
 *
 * @param array $args An array of key => value arguments. Defaults below 
 * - mixed query variable                   'query'                 => $GLOBALS['wp_query'],
 * - string Previous page text              'previous_page_text'    => __( '&laquo;' ),
 * - string Next page text                  'next_page_text'        => __( '&raquo;' ),
 * - string First page link text            'first_page_text'       => __( 'First' ),
 * - string Last page link text             'last_page_text'        => __( 'Last' ),
 * - string Older posts text                'next_link_text'        => __( 'Older Entries' ),
 * - string Newer posts text                'previous_link_text'    => __( 'Newer Entries' ),
 * - bool Whether to use links              'show_posts_links'      => false,
 * - int Amount of numbered links to show   'range'                 => 5,
 *
 * @return string $paginated_text
*/ 
function get_paginated_numbers( $args = [] ) {

    //Set defaults to use
    $defaults = [
        'query'                 => $GLOBALS['wp_query'],
        'previous_page_text'    => __( '&laquo;' ),
        'next_page_text'        => __( '&raquo;' ),
        'first_page_text'       => __( 'First' ),
        'last_page_text'        => __( 'Last' ),
        'next_link_text'        => __( 'Older Entries' ),
        'previous_link_text'    => __( 'Newer Entries' ),
        'current_wrap'          => 'span',
        'current_class'         => 'current',
        'page_wrap'             => '',
        'page_class'            => '',
        'link_class'            => 'inactive',
        'output_wrap'           => 'div',
        'output_class'          => 'pagination',
        'show_page_count'       => true,
        'show_posts_links'      => false,
        'range'                 => 5,
    ];

    // Merge default arguments with user set arguments
    $args = wp_parse_args( $args, $defaults );

    /**
     * Get current page if query is paginated and more than one page exists
     * The first page is set to 1
     * 
     * Static front pages is included
     *
     * @see WP_Query pagination parameter 'paged'
     * @link http://codex.wordpress.org/Class_Reference/WP_Query#Pagination_Parameters
     *
    */ 
    if ( get_query_var('paged') ) { 

        $current_page = get_query_var('paged'); 

    }elseif ( get_query_var('page') ) { 

        $current_page = get_query_var('page'); 

    }else{ 

        $current_page = 1; 

    }

    // Get the amount of pages from the query
    if ( is_object( $args['query'] ) ) {
        $max_pages = (int) $args['query']->max_num_pages;
    } else {
        $max_pages = filter_var( $args['query'], FILTER_VALIDATE_INT );
    }

    /**
     * If $args['show_posts_links'] is set to false, numbered paginated links are returned
     * If $args['show_posts_links'] is set to true, pagination links are returned
    */
    if( false === $args['show_posts_links'] ) {

        // Don't display links if only one page exists
        if( 1 === $max_pages ) {

            $paginated_text = '';

        }else{

            /**
             * For multi-paged queries, we need to set the variable ranges which will be used to check
             * the current page against and according to that set the correct output for the paginated numbers
            */
            $mid_range      = (int) floor( $args['range'] / 2 );
            $start_range    = range( 1 , $mid_range );
            $end_range      = range( ( $max_pages - $mid_range +1 ) , $max_pages );
            $exclude        = array_merge( $start_range, $end_range );  

            /**
             * The amount of pages must now be checked against $args['range']. If the total amount of pages
             * is less than $args['range'], the numbered links must be returned as is
             *
             * If the total amount of pages is more than $args['range'], then we need to calculate the offset
             * to just return the amount of page numbers specified in $args['range']. This defaults to 5, so at any
             * given instance, there will be 5 page numbers displayed
            */
            $check_range    = ( $args['range'] > $max_pages )   ? true : false;

            if( true === $check_range ) {

                $range_numbers = range( 1, $max_pages );

            }elseif( false === $check_range ) {

                if( !in_array( $current_page, $exclude ) ) {

                    $range_numbers = range( ( $current_page - $mid_range ), ( $current_page + $mid_range ) );

                }elseif( in_array( $current_page, $start_range ) && ( $current_page - $mid_range ) <= 0 ) {

                    $range_numbers = range( 1, $args['range'] );

                }elseif(  in_array( $current_page, $end_range ) && ( $current_page + $mid_range ) >= $max_pages ) {

                    $range_numbers = range( ( $max_pages - $args['range'] +1 ), $max_pages );

                }

            }

            /**
             * The page numbers are set into an array through this foreach loop. The current page, or active page
             * gets the class 'current' assigned to it. All the other pages get the class 'inactive' assigned to it
            */
            foreach ( $range_numbers as $v ) {

                if ( $v == $current_page ) { 

//                    $page_numbers[] = '<span class="current">' . $v . '</span>';
                    $page_numbers[] = '<'. $args['current_wrap'] .' class="' . $args['current_class'] . '"><span>' . $v . '</span></'. $args['current_wrap'] .'>';
                }else{

//                    $page_numbers[] = '<a href="' . get_pagenum_link( $v ) . '" class="inactive">' . $v . '</a>';
                    $link = '<a href="' . get_pagenum_link( $v ) . '" class="' . $args['link_class'] . '">' . $v . '</a>';
                    if( !empty($args['page_wrap']) ){
                        $page_numbers[] = '<'. $args['page_wrap'] .' class="' . $args['page_class'] . '">' . $link . '</'. $args['page_wrap'] .'>';
                    }else{
                        $page_numbers[] = $link;
                    }
                    
                }

            }

            /** 
            * All the texts are set here and when they should be displayed which will link back to:
             * - $previous_page The previous page from the current active page
             * - $next_page The next page from the current active page
             * - $first_page Links back to page number 1
             * - $last_page Links to the last page
            */
            $previous_page  = ( $current_page !== 1 )                       ? '<a href="' . get_pagenum_link( $current_page - 1 ) . '">' . $args['previous_page_text'] . '</a>' : '';
            $next_page      = ( $current_page !== $max_pages )              ? '<a href="' . get_pagenum_link( $current_page + 1 ) . '">' . $args['next_page_text'] . '</a>'     : '';
            $first_page     = ( !in_array( 1, $range_numbers ) )            ? '<a href="' . get_pagenum_link( 1 ) . '">' . $args['first_page_text'] . '</a>'                    : '';
            $last_page      = ( !in_array( $max_pages, $range_numbers ) )   ? '<a href="' . get_pagenum_link( $max_pages ) . '">' . $args['last_page_text'] . '</a>'            : '';
            
            
            //Add the page_wrap if it exists
            if( !empty($args['page_wrap']) ){
                $previous_page = '<'. $args['page_wrap'] .'>' . $previous_page . '</'. $args['page_wrap'] .'>';
                $next_page = '<'. $args['page_wrap'] .'>' . $next_page . '</'. $args['page_wrap'] .'>';
            }

            /**
             * Text to display before the page numbers
             * This is set to the following structure:
             * - Page X of Y
            */
            if($args['show_page_count']){
                $page_text      = '<span>' . sprintf( __( 'Page %s of %s' ), $current_page, $max_pages ) . '</span>';
                
            }else{
                $page_text = '';
            }
            
            // Turn the array of page numbers into a string
            $numbers_string = implode( ' ', $page_numbers );
            

            // The final output of the function
            $paginated_text = '<' . $args['output_wrap'] . ' class="' . $args['output_class'] . '">';
            $paginated_text .= $page_text . $first_page . $previous_page . $numbers_string . $next_page . $last_page;
            $paginated_text .= '</' . $args['output_wrap'] . '>';

        }

    }elseif( true === $args['show_posts_links'] ) {

        /**
        * If $args['show_posts_links'] is set to true, only links to the previous and next pages are displayed
        * The $max_pages parameter is already set by the function to accommodate custom queries
        */
        $paginated_text = next_posts_link( '<div class="next-posts-link">' . $args['next_link_text'] . '</div>', $max_pages );
        $paginated_text .= previous_posts_link( '<div class="previous-posts-link">' . $args['previous_link_text'] . '</div>' );

    }

    // Finally return the output text from the function
    return $paginated_text;

}
