<?php
if (! defined('ABSPATH')) {
    exit;
}

class Review_Rating_Settings
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_settings_submenu']);
        add_action('admin_init', [$this, 'register_settings']);
    }


    public function add_settings_submenu()
    {
        add_submenu_page('edit.php?post_type=review-rating', __('Review Settings', 'review-rating'), __('Review Settings', 'review-rating'), 'manage_options', 'review-rating-settings', [$this, 'render_settings_page']);
    }

    // Option register + fields
    public function register_settings()
    {
        register_setting('review_rating_settings_group', 'review_criteria_labels');

        add_settings_section('review_rating_settings_section', '', null, 'review-rating-settings');

        $defaults = ['overall', 'transport', 'food', 'hospitality', 'destination'];
        $num = 1;

        foreach ($defaults as $key) {
            add_settings_field(
                'review_criteria_' . $key,
                'Criteria label ' . $num,
                function () use ($key) {
                    $options = get_option('review_criteria_labels');
                    $value   = isset($options[$key]) ? esc_attr($options[$key]) : ucfirst($key);
                    echo "<input type='text' name='review_criteria_labels[$key]' value='{$value}' class='regular-text'>";
                },
                'review-rating-settings',
                'review_rating_settings_section'
            );
            $num++;
        }
    }

    // Settings page render
    public function render_settings_page()
    {
?>
        <div class="wrap">
            <h1><?php _e('Review Criteria Settings', 'review-rating'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('review_rating_settings_group');
                do_settings_sections('review-rating-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }
}
