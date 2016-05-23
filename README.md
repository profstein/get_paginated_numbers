# get_paginated_numbers
This function exports a set of paginated links and works even when using custom wp_query() to get the posts or pages.

## The Problem
When using wp_query() to get a customized set of posts or pages the built-in pagination functions don't work ( ). The main reason for this is that they expect the query to be the default global query named $wp_query. There are solutions that involve hijacking the $wp_query variable but those felt too hacky for me.

## The Solution
Pieter Goosen is the orginal author of this solution. He posted the code on Stack Exchange http://wordpress.stackexchange.com/questions/172816/paginate-links-with-ugly-and-pretty-permalinks/172818#172818 and released under GPLv2.

I have taken his code and made some modifications to remove some of the hard-coding of the HTML elements that wrap the links.

## Usage
As is often the case with WP there are a few steps to get this working.

1. Copy the [https://github.com/profstein/get_paginated_numbers/blob/master/get_paginated_numbers_function.php](get_paginated_numbers function) and put it in your functions.php file. It's also possible to create a plugin for this but because the use of it is mostly tied to the display of a particular theme that's not necessary.
2. Call the function in one of your template files where you want the pagination to appear. See Examples Uses below for specifics.

### Example Uses

**Query Setup**
The examples assume that you have a custom query named $the_query. This query should be set up to make use of the paged query_var otherwise the query won't work properly. 
````
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$the_query = new WP_Query( array( 'category_name' => 'my-category', 'posts_per_page' => 3, 'paged' => $paged ));
````            
When you're using paging, WordPress will add **/page/x/** to the URL for the page where **X** is replaced with the current page number. It doesn't do this on the first page. So $paged is set by checking to see if there is a page set in the URL, uses that number if it is set and if it is not set uses 1.

The next line runs the WP_QUERY function. This example shows one that pulls posts from the my-category. It also uses the 'posts_per_page' => 3 setting. This is used when you might want to display a numbe of page (like thumbnails of posts to the user) and want to only display a certain amount at a time. You can change 3 to the number of posts you want to display at a time. These are optional but often used when doing paging.

**Basic Example**

The most basic use is to call the function and pass in your query variable (for all of our examples we use $the_query )
````
echo get_paginated_numbers( [ 'query' => $the_query ]);
````
You can also put the argument into a separate variable like this:
````
<?php
  $args = [
    'query' => $the_query
  ];
  echo get_paginated_numbers( $args );
?>
````

That allows you to add more parameters later.

**Full Example**

This example shows how to use the function to output a paginated list that Bootstrap expects. Here it wraps the list in a ul element and puts each link in an li elment. 

````
  <?php
    $args = [
      'query' => $the_query,
      'output_wrap' => 'ul',
      'output_class' => 'pagination',
      'current_wrap' => 'li',
      'current_class' => 'active',
      'page_wrap' => 'li',
      'page_class' => '',
      'link_class' => '',
      'show_page_count' => false    
    ];
    echo get_paginated_numbers( $args )
?>
````
