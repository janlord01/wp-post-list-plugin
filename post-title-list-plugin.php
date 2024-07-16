<?php
/*
Plugin Name: Post Title List Plugin
Description: A simple plugin to display a paginated list of all post titles using a shortcode.
Version: 1.0
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
