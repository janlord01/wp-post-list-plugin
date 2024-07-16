# Post Title List Plugin with Pagination

**Plugin Name:** Post Title List Plugin  
**Description:** A simple WordPress plugin to display a paginated list of all post titles as clickable links using a shortcode.  
**Version:** 1.2  
**Author:** Janlord Luga  

## Description

The Post Title List Plugin with Pagination allows you to easily display a paginated list of all post titles on your WordPress site using a shortcode. Each post title is clickable, linking to the respective post. This plugin is ideal for creating an index of your posts or for use in a custom archive page.

## Features

- Display all post titles in a list format.
- Each post title is a clickable link to the respective post.
- Pagination support to navigate through large lists of posts.
- Customizable number of posts per page.
- Simple shortcode to add the list to any post or page.

## Installation

1. Download the plugin files.
2. Upload the `post-title-list-plugin` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Use the shortcode `[post_title_list]` in any post or page to display the post titles list.

## Usage

To display the paginated list of post titles, simply add the shortcode `[post_title_list]` to any post or page. You can also customize the number of posts per page by using the `posts_per_page` attribute in the shortcode:

```sh
[post_title_list posts_per_page="10"]
```
By default, the plugin will display 20 posts per page.

## CSS Styling

The plugin includes default CSS for pagination. If you wish to customize the appearance, you can modify the style.css file located in the plugin folder or add your own CSS rules.

```sh
.pagination {
    text-align: center;
    margin: 20px 0;
}
.pagination ul {
    list-style: none;
    padding: 0;
}
.pagination ul li {
    display: inline-block;
    margin: 0 5px;
}
.pagination ul li a {
    padding: 5px 10px;
    text-decoration: none;
    border: 1px solid #ddd;
    color: #0073aa;
}
.pagination ul li a:hover {
    background-color: #0073aa;
    color: #fff;
}
.pagination ul li span {
    padding: 5px 10px;
    border: 1px solid #ddd;
    background-color: #0073aa;
    color: #fff;
}
```

## Code

Here is the complete code for the post-title-list-plugin.php file:

```sh
<?php
/*
Plugin Name: Post Title List Plugin with Pagination
Description: A simple plugin to display a paginated list of all post titles using a shortcode.
Version: 1.2
Author: Janlord Luga
*/

// Function to fetch and display post titles with pagination
function display_post_titles($atts) {
    // Attributes
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 20, // Default posts per page
        ),
        $atts,
        'post_title_list'
    );

    // Get the current page number
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // Query to fetch posts
    $args = array(
        'posts_per_page' => $atts['posts_per_page'],
        'post_type' => 'post',
        'post_status' => 'publish',
        'paged' => $paged
    );
    $query = new WP_Query($args);

    // Check if there are posts
    if ($query->have_posts()) {
        $output = '<ul>';
        // Loop through the posts
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        $output .= '</ul>';

        // Pagination
        $big = 999999999; // Need an unlikely integer
        $pagination = paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $query->max_num_pages,
            'type' => 'list',
        ));

        if ($pagination) {
            $output .= '<div class="pagination">' . $pagination . '</div>';
        }

        // Restore original Post Data
        wp_reset_postdata();
    } else {
        $output = '<p>No posts found.</p>';
    }

    return $output;
}

// Register the shortcode
function register_post_title_list_shortcode() {
    add_shortcode('post_title_list', 'display_post_titles');
}

// Hook into the 'init' action
add_action('init', 'register_post_title_list_shortcode');

// Ensure the pagination works correctly
function add_pagination_rewrite_rules() {
    global $wp_rewrite;
    $wp_rewrite->pagination_base = 'page';
    $wp_rewrite->flush_rules();
}
add_action('init', 'add_pagination_rewrite_rules');

// Enqueue plugin styles
function post_title_list_enqueue_styles() {
    wp_enqueue_style('post-title-list-styles', plugins_url('style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'post_title_list_enqueue_styles');
```

## License

This plugin is open-source software licensed under the MIT License.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request with any improvements.

## Support

If you have any questions or need support, feel free to open an issue on the GitHub repository.

Feel free to adjust the content as needed. This `README.md` provides a comprehensive overview of your plugin, including installation instructions, usage examples, and the complete code.
