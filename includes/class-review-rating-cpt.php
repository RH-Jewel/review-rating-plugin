<?php
if (! defined('ABSPATH')) {
    exit;
}

class Review_Rating_CPT
{

    public function __construct()
    {
        // Register CPT
        add_action('init', [$this, 'register_review_rating_cpt']);

        // Admin columns
        add_filter('manage_review-rating_posts_columns', [$this, 'add_custom_columns']);
        add_action('manage_review-rating_posts_custom_column', [$this, 'render_custom_columns'], 10, 2);

        // Approve / Unapprove action
        add_action('admin_post_toggle_review_status', [$this, 'toggle_review_status']);


        // Add filter dropdown for post types
        add_action('restrict_manage_posts', [$this, 'add_post_type_filter']);

        // Add filter review post types query
        add_action('pre_get_posts', [$this, 'filter_reviews_by_post_type']);
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
            'supports'            => array('title', 'editor'),
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

    /**
     * Add custom columns (Message + Status)
     */
    public function add_custom_columns($columns)
    {
        $columns['review_message'] = __('Review Message', 'review-rating');
        $columns['review_status']  = __('Status', 'review-rating');
        return $columns;
    }

    /**
     * Render custom column content
     */
    public function render_custom_columns($column, $post_id)
    {
        if ($column === 'review_message') {
            $content = get_post_field('post_content', $post_id);
            echo wp_trim_words(esc_html($content), 15, '...');
        }

        if ($column === 'review_status') {
            $status = get_post_status($post_id);
            $nonce  = wp_create_nonce('toggle_review_' . $post_id);
            $url    = admin_url('admin-post.php?action=toggle_review_status&post_id=' . $post_id . '&_wpnonce=' . $nonce);

            if ($status === 'publish') {
                echo '<a href="' . esc_url($url) . '" class="button">Unapprove</a>';
            } else {
                echo '<a href="' . esc_url($url) . '" class="button button-primary">Approve</a>';
            }
        }
    }

    /**
     * Handle approve/unapprove toggle
     */
    public function toggle_review_status()
    {
        if (! current_user_can('manage_options')) {
            wp_die('Not allowed.');
        }

        $post_id = intval($_GET['post_id'] ?? 0);
        if (! $post_id || get_post_type($post_id) !== 'review-rating') {
            wp_die('Invalid review.');
        }

        check_admin_referer('toggle_review_' . $post_id);

        $status = get_post_status($post_id);
        $new_status = ($status === 'publish') ? 'draft' : 'publish';

        wp_update_post([
            'ID'          => $post_id,
            'post_status' => $new_status,
        ]);

        wp_safe_redirect(admin_url('edit.php?post_type=review-rating'));
        exit;
    }

    /**
     * Get all public posts list
     */
    public function add_post_type_filter($post_type)
    {
        if ($post_type !== 'review-rating') {
            return;
        }

        // Get all public post types
        $post_types = get_post_types(['public' => true], 'objects');

        // Current filter
        $current_filter = $_GET['review_for_post_type'] ?? '';

        echo '<select name="review_for_post_type">';
        echo '<option value="">' . __('All Post Types', 'review-rating') . '</option>';
        foreach ($post_types as $ptype) {
            $selected = selected($current_filter, $ptype->name, false);
            echo '<option value="' . esc_attr($ptype->name) . '" ' . $selected . '>' . esc_html($ptype->labels->singular_name) . '</option>';
        }
        echo '</select>';
    }


    /**
     * Public posts filter query
     */
    public function filter_reviews_by_post_type($query)
    {
        global $pagenow;
        if (!is_admin() || $pagenow !== 'edit.php' || $query->get('post_type') !== 'review-rating') {
            return;
        }

        $selected_type = $_GET['review_for_post_type'] ?? '';
        if ($selected_type) {
            // Get all post IDs of that post type
            $posts = get_posts([
                'post_type'      => $selected_type,
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ]);

            $query->set('meta_query', [
                [
                    'key'     => '_review_post_id',
                    'value'   => $posts,
                    'compare' => 'IN'
                ]
            ]);
        }
    }
}
