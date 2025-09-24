<?php
/**
 * Plugin Name: Review & Rating
 * Description: Smart plugin for multi-criteria reviews and ratings to boost engagement.
 * Version: 1.0.0
 * Author: RH Jewel
 * Author URI: https://rh-jewel.com/
 * Text Domain: review-rating
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define constants
define('REVIEW_RATING_PATH', plugin_dir_path(__FILE__));
define('REVIEW_RATING_URL', plugin_dir_url(__FILE__));

// Include class
require_once REVIEW_RATING_PATH . 'includes/class-review-rating-cpt.php';
require_once REVIEW_RATING_PATH . 'includes/class-review-rating-shortcode.php';
require_once REVIEW_RATING_PATH . 'includes/class-review-rating-card-shortcode.php';
require_once REVIEW_RATING_PATH . 'includes/class-review-rating-settings.php';



// Initialize
add_action('plugins_loaded', function () {
    new Review_Rating_CPT();
    new Review_Rating_Shortcode();
    new Post_Rating_Shortcode();
    new Review_Rating_Settings();
});
