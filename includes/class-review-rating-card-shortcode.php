<?php
if (! defined('ABSPATH')) {
    exit;
}

class Post_Rating_Shortcode
{
    public function __construct()
    {
        add_shortcode('post_rating', [$this, 'render_post_rating']);
    }

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
        <div class="post-rating-card">
            <div class="rating-stars">
                <ul>
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= floor($avg_rating)) {
                            echo '<li><i class="bi bi-star-fill"></i></li>';
                        } elseif ($i - $avg_rating <= 0.5) {
                            echo '<li><i class="bi bi-star-half"></i></li>';
                        } else {
                            echo '<li><i class="bi bi-star"></i></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="rating-text">
                <strong><?php echo esc_html($avg_rating); ?></strong>
                <span>(<?php echo esc_html($total_reviews); ?> reviews)</span>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
}
