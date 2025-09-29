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

// /**
//  * The main plugin class
//  */
// final class Review_Main
// {

//     /**
//      * Plugin version
//      *
//      * @var string
//      */
//     const version = '1.0.0';

//     /**
//      * Class construcotr
//      */
//     private function __construct()
//     {
//         $this->define_constants();
//         $this->require_files();


//         add_action('plugins_loaded', [$this, 'plugin_classes_init']);
//     }

//     /**
//      * Initializes a singleton instance
//      *
//      * @return \Review_Main
//      */
//     public static function init()
//     {
//         static $instance = false;

//         if (!$instance) {
//             $instance = new self();
//         }

//         return $instance;
//     }

//     /**
//      * Define the required plugin constants
//      *
//      * @return void
//      */
//     public function define_constants()
//     {
//         define('REVIEW_RATING_PATH', plugin_dir_path(__FILE__));
//         define('REVIEW_RATING_URL', plugin_dir_url(__FILE__));
//     }

//     /**
//      * Include all require files
//      *
//      * @return void
//      */
//     public function require_files()
//     {
//         require_once REVIEW_RATING_PATH . 'includes/class-review-rating-cpt.php';
//         require_once REVIEW_RATING_PATH . 'includes/class-review-rating-shortcode.php';
//         require_once REVIEW_RATING_PATH . 'includes/class-review-rating-card-shortcode.php';
//         require_once REVIEW_RATING_PATH . 'includes/class-review-rating-settings.php';
//     }

//     /**
//      * Initialize the plugin
//      *
//      * @return void
//      */
//     public function plugin_classes_init()
//     {
//         new Review_Rating_CPT();
//         new Review_Rating_Shortcode();
//         new Post_Rating_Shortcode();
//         new Review_Rating_Settings();
//     }




//     // end main class 
// }

// /**
//  * Initializes the main plugin
//  *
//  * @return \Review_Main
//  */
// function review_core()
// {
//     return Review_Main::init();
// }

// // start doing amazing things
// review_core();



// Define constants
define('REVIEW_RATING_PATH', plugin_dir_path(__FILE__));
define('REVIEW_RATING_URL', plugin_dir_url(__FILE__));

// Include class
require_once REVIEW_RATING_PATH . 'includes/class-review-rating-cpt.php';
require_once REVIEW_RATING_PATH . 'includes/class-review-rating-shortcode.php';
require_once REVIEW_RATING_PATH . 'includes/class-review-rating-card-shortcode.php';
require_once REVIEW_RATING_PATH . 'includes/class-review-rating-settings.php';

// Enqueue plugin assets
add_action('wp_enqueue_scripts', function () {
    // CSS
    wp_enqueue_style('review-rating-style', REVIEW_RATING_URL . 'assets/css/review-rating.css', [], '1.0.0');
    // JS
    wp_enqueue_script('review-rating-script', REVIEW_RATING_URL . 'assets/js/review-rating.js', ['jquery'], '1.0.0', true);
});


// Initialize
add_action('plugins_loaded', function () {
    new Review_Rating_CPT();
    new Review_Rating_Shortcode();
    new Post_Rating_Shortcode();
    new Review_Rating_Settings();
});
