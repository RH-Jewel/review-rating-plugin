<?php
if (! defined('ABSPATH')) {
    exit;
}

class Review_Rating_Shortcode
{
    public function __construct()
    {
        add_shortcode('review_rating', [$this, 'render_review_section']);
        add_action('init', [$this, 'handle_review_submission']);
    }

    public function render_review_section()
    {
        if (!is_singular()) {
            return '';
        }

        ob_start();

        global $post;

        // Approved reviews for current post
        $reviews = get_posts([
            'post_type'      => 'review-rating',
            'post_status'    => 'publish',
            'numberposts'    => -1,
            'meta_query'     => [
                [
                    'key'   => '_review_post_id',
                    'value' => $post->ID,
                ],
            ],
        ]);

        $total_reviews = count($reviews);
        $avg_rating    = 0;
        $all_ratings   = [];

        // criteria sums
        $criteria_sums = [
            'overall'     => 0,
            'transport'   => 0,
            'food'        => 0,
            'hospitality' => 0,
            'destination' => 0
        ];

        if ($total_reviews > 0) {
            foreach ($reviews as $review) {
                $overall       = get_post_meta($review->ID, '_rating_overall', true);
                $all_ratings[] = intval($overall);

                foreach ($criteria_sums as $key => $sum) {
                    $criteria_sums[$key] += intval(get_post_meta($review->ID, '_rating_' . $key, true));
                }
            }
            $avg_rating = round(array_sum($all_ratings) / $total_reviews, 1);
        }

        // Dynamic criteria labels from plugin settings
        $criteria_labels = get_option('review_criteria_labels', [
            'overall'      => 'Overall',
            'transport'    => 'Transport',
            'food'         => 'Food',
            'hospitality'  => 'Hospitality',
            'destination'  => 'Destination',
        ]);

        // criteria averages
        $criteria_avgs   = [];
        foreach ($criteria_sums as $key => $sum) {
            $criteria_avgs[$key] = $total_reviews ? round($sum / $total_reviews, 1) : 0;
        }
?>
        <div class="customer-rating-area">
            <h4><?php echo __('Customer Review & Rating', 'review-rating') ?></h4>
            <div class="rating-wrapper">
                <div class="rating-area">
                    <span><?php echo ($avg_rating >= 4) ? 'Excellent!' : 'Good'; ?></span>
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
                    <p>
                        <strong><?php echo esc_html($avg_rating); ?></strong>
                        <?php echo __('based on ', 'review-rating') . esc_html($total_reviews) . __(' reviews', 'review-rating') ?>
                    </p>
                    <button class="primary-btn1 two" data-bs-toggle="modal" data-bs-target="#ratingModal">
                        <span><?php echo __('Write a Review', 'review-rating') ?></span>
                        <span><?php echo __('Write a Review', 'review-rating') ?></span>
                    </button>
                </div>

                <!-- Progress Bar Section -->
                <div class="progress-list">
                    <?php foreach ($criteria_labels as $key => $label):
                        $avg     = $criteria_avgs[$key] ?? 0;
                        $percent = $avg * 20;
                    ?>
                        <div class="progress-item">
                            <span><?php echo esc_html($label); ?></span>
                            <span class="progress-score"><?php echo number_format($avg, 1); ?></span>
                            <div class="progress">
                                <div class="progress-bar" style="width:<?php echo esc_attr($percent); ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="comment-area">
                <ul class="comment">
                    <?php if ($reviews): ?>
                        <?php foreach ($reviews as $review): ?>
                            <li>
                                <div class="single-comment-area">
                                    <div class="author-img">
                                        <img src="<?php echo esc_url(get_avatar_url(0)); ?>" alt="">
                                    </div>
                                    <div class="comment-content">
                                        <div class="author-name-deg">
                                            <h6><?php echo esc_html(get_the_title($review)); ?></h6>
                                            <span><?php echo get_the_date('', $review->ID); ?></span>
                                        </div>
                                        <p><?php echo esc_html($review->post_content); ?></p>

                                        <!-- Criteria Ratings -->
                                        <ul class="review-item-list">
                                            <?php foreach ($criteria_labels as $key => $label):
                                                $rating = intval(get_post_meta($review->ID, '_rating_' . $key, true));
                                                if ($rating): ?>
                                                    <li>
                                                        <span><?php echo esc_html($label); ?></span>
                                                        <ul class="star-list">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <li><?php echo ($i <= $rating) ? '&#9733;' : '&#9734;'; ?></li>
                                                            <?php endfor; ?>
                                                        </ul>
                                                    </li>
                                            <?php endif;
                                            endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><?php echo __('No reviews yet. Be the first to write one!', 'review-rating') ?></p>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal rating-modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">X</button>
                    <div class="modal-body">
                        <h4 class="modal-title"><?php echo __('Give Your Review', 'review-rating') ?></h4>
                        <form method="post" class="review-form-wrapper">
                            <ul class="star-rating-list">
                                <?php foreach ($criteria_labels as $key => $label): ?>
                                    <li>
                                        <span><?php echo esc_html($label); ?></span>
                                        <div class="rating-container" data-key="<?php echo esc_attr($key); ?>" data-rating="0">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star star-icon" data-value="<?php echo $i; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" name="rating_<?php echo esc_attr($key); ?>" value="0" required>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="form-inner mb-25">
                                <label><?php echo __('Your Feedback', 'review-rating') ?></label>
                                <textarea name="review_content" placeholder="Write a your tour feedback" required></textarea>
                            </div>
                            <div class="form-inner">
                                <label><?php echo __('Your Name', 'review-rating') ?></label>
                                <input type="text" placeholder="Your Name" name="review_name" required>
                            </div>
                            <?php wp_nonce_field('submit_review_nonce', 'review_nonce'); ?>
                            <input type="hidden" name="review_post_id" value="<?php echo esc_attr($post->ID); ?>">
                            <button type="submit" name="submit_review" class="primary-btn1 black-bg mt-30">
                                <span><?php echo __('Submit Rating', 'review-rating') ?></span>
                                <span><?php echo __('Submit Rating', 'review-rating') ?></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<?php
        return ob_get_clean();
    }

    public function handle_review_submission()
    {
        if (isset($_POST['submit_review']) && wp_verify_nonce($_POST['review_nonce'], 'submit_review_nonce')) {
            $name    = sanitize_text_field($_POST['review_name']);
            $content = sanitize_textarea_field($_POST['review_content']);
            $post_id = intval($_POST['review_post_id']);

            // Dynamic criteria
            $criteria_labels = get_option('review_criteria_labels', [
                'overall'      => 'Overall',
                'transport'    => 'Transport',
                'food'         => 'Food',
                'hospitality'  => 'Hospitality',
                'destination'  => 'Destination',
            ]);

            $criteria = [];
            foreach ($criteria_labels as $key => $label) {
                $criteria[$key] = isset($_POST['rating_' . $key]) ? intval($_POST['rating_' . $key]) : 0;
            }

            // Calculate overall average
            $overall = round(array_sum($criteria) / count($criteria));

            // Insert review
            $review_id = wp_insert_post([
                'post_title'   => $name,
                'post_content' => $content,
                'post_type'    => 'review-rating',
                'post_status'  => 'pending',
            ]);

            if ($review_id) {
                foreach ($criteria as $key => $value) {
                    update_post_meta($review_id, '_rating_' . $key, $value);
                }
                update_post_meta($review_id, '_rating_overall', $overall);
                update_post_meta($review_id, '_review_post_id', $post_id);
            }

            wp_safe_redirect(add_query_arg('review_status', 'success', wp_get_referer()));
            exit;
        }
    }
}
