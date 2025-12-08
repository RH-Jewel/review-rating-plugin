<?php
if (! defined('ABSPATH')) {
    exit;
}

class Post_Rating_Shortcode
{
    public function __construct()
    {
        add_shortcode('post_rating', [$this, 'render_post_rating']);
        add_shortcode('post_rating_count', [$this, 'render_post_rating_count']);
        add_shortcode('total_post_rating_count', [$this, 'render_total_post_count']);
    }

    /**
     * Render rating avarage, total count with start icon
     * @var string
     */
    public function render_post_rating()
    {
        if (!is_singular()) {
            return '';
        }

        global $post;
        $post_id = $post->ID;

        // Approved reviews only for this post
        $reviews = get_posts([
            'post_type'      => 'review-rating',
            'post_status'    => 'publish',
            'numberposts'    => -1,
            'meta_key'       => '_review_post_id',
            'meta_value'     => $post_id,
        ]);

        $total_reviews = count($reviews);
        $avg_rating    = 0;
        $all_ratings   = [];

        if ($total_reviews > 0) {
            foreach ($reviews as $review) {
                $overall = get_post_meta($review->ID, '_rating_overall', true);
                $all_ratings[] = intval($overall);
            }
            $avg_rating = round(array_sum($all_ratings) / $total_reviews, 1);
        }

        ob_start();
?>
        <div class="rating-area">
            <ul class="star">
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= floor($avg_rating)) {
                        echo '<li>&#9733;</i></li>';
                    } elseif ($i - $avg_rating <= 0.5) {
                        echo '<li class="half">&#11242;</li>';
                    } else {
                        echo '<li>&#9734;</li>';
                    }
                }
                ?>
            </ul>
            <span><?php echo esc_html($total_reviews) . ' ' . esc_html__('Review', 'review-rating') ?> ( <?php echo esc_html__('based on', 'review-rating') . ' ' . esc_html($avg_rating) . ' ' . esc_html__('reviews', 'review-rating') ?> )</span>
        </div>
    <?php
        return ob_get_clean();
    }


    /**
     * Render rating total count
     * @var string
     */
    public function render_post_rating_count()
    {

        global $post;

        if (empty($post) || !isset($post->ID)) {
            return '';
        }

        $post_id = $post->ID;

        // Approved reviews only for this post
        $reviews = get_posts([
            'post_type'      => 'review-rating',
            'post_status'    => 'publish',
            'numberposts'    => -1,
            'meta_key'       => '_review_post_id',
            'meta_value'     => $post_id,
        ]);

        $total_reviews = count($reviews);
        $avg_rating    = 0;
        $all_ratings   = [];

        if ($total_reviews > 0) {
            foreach ($reviews as $review) {
                $overall = get_post_meta($review->ID, '_rating_overall', true);
                $all_ratings[] = intval($overall);
            }
            $avg_rating = round(array_sum($all_ratings) / $total_reviews, 1);
        }

        ob_start();
    ?>
        <div class="rating-text">
            <div class="rating-stars">
                <ul>
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= floor($avg_rating)) {
                            echo '<li>&#9733;</i></li>';
                        } elseif ($i - $avg_rating <= 0.5) {
                            echo '<li class="half">&#11242;</li>';
                        } else {
                            echo '<li>&#9734;</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <span class="total"><?php echo esc_html($total_reviews) . ' ' . esc_html__('reviews', 'review-rating') ?></span>
        </div>
    <?php
        return ob_get_clean();
    }

    /**
     * Only Render all rating total count
     * @var string
     */
    public function render_total_post_count()
    {

        global $post;

        if (empty($post) || !isset($post->ID)) {
            return '';
        }

        $post_id = $post->ID;

        // Approved reviews only for this post
        $reviews = get_posts([
            'post_type'      => 'review-rating',
            'post_status'    => 'publish',
            'numberposts'    => -1,
            'meta_key'       => '_review_post_id',
            'meta_value'     => $post_id,
        ]);

        $total_reviews = count($reviews);
        $avg_rating    = 0;
        $all_ratings   = [];

        if ($total_reviews > 0) {
            foreach ($reviews as $review) {
                $overall = get_post_meta($review->ID, '_rating_overall', true);
                $all_ratings[] = intval($overall);
            }
            $avg_rating = round(array_sum($all_ratings) / $total_reviews, 1);
        }

        ob_start();
    ?>

        <?php echo esc_html($total_reviews) ?>
<?php
        return ob_get_clean();
    }
}
