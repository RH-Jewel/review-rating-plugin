<?php
if (! defined('ABSPATH')) {
    exit;
}

class Review_Rating_CPT
{

    public function __construct()
    {
        add_action('init', [$this, 'register_review_rating_cpt']);
    }

    public function register_review_rating_cpt()
    {
        $labels = array(
            'name'               => esc_html_x('All Review & Rating', 'Post Type General Name', 'review-rating'),
            'singular_name'      => esc_html_x('Review & Rating', 'Post Type Singular Name', 'review-rating'),
            'menu_name'          => esc_html__('Review & Ratings', 'review-rating'),
            'all_items'          => esc_html__('Review & Rating', 'review-rating'),
            'view_item'          => esc_html__('View Review & Rating', 'review-rating'),
            'add_new_item'       => esc_html__('Add New Review & Rating', 'review-rating'),
            'add_new'            => esc_html__('Add New Review & Rating', 'review-rating'),
            'edit_item'          => esc_html__('Edit Review & Rating', 'review-rating'),
            'update_item'        => esc_html__('Update Review & Rating', 'review-rating'),
            'search_items'       => esc_html__('Search Review & Rating', 'review-rating'),
            'not_found'          => esc_html__('Not Found', 'review-rating'),
            'not_found_in_trash' => esc_html__('Not found in Trash', 'review-rating'),
        );

        $capabilities = array(
            'create_posts'       => false,
            'edit_post'          => 'manage_options',
            'read_post'          => 'manage_options',
            'delete_post'        => 'manage_options',
            'edit_posts'         => 'manage_options',
            'edit_others_posts'  => 'manage_options',
            'publish_posts'      => 'manage_options',
            'read_private_posts' => 'manage_options',
        );

        $args = array(
            'label'               => esc_html__('Review & Rating', 'review-rating'),
            'description'         => esc_html__('Review & Rating', 'review-rating'),
            'labels'              => $labels,
            'capabilities'        => $capabilities,
            'supports'            => array('title'),
            'hierarchical'        => true,
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'rewrite'             => array('slug' => 'review-rating', 'with_front' => false),
            'exclude_from_search' => true,
            'can_export'          => true,
            'capability_type'     => 'post',
            'query_var'           => true,
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-star-filled',
        );

        register_post_type('review-rating', $args);
    }
}
