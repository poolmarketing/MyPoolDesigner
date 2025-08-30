<?php
/**
 * Plugin Name: MyPoolDesigner Gallery
 * Plugin URI: https://mypooldesigner.ai/
 * Description: Display your AI-generated pool designs with responsive Bootstrap galleries, lightbox viewing, multi-image navigation, and video support
 * Version: 2.1.0
 * Author: MyPoolDesigner Team
 * License: GPL v2 or later
 * Text Domain: mypooldesigner-gallery
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MPD_VERSION', '2.1.0');
define('MPD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MPD_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MPD_API_URL', 'https://mypooldesigner.ai/api');

class MyPoolDesigner_Gallery {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        register_activation_hook(__FILE__, array($this, 'activate'));
    }
    
    public function init() {
        // Register shortcodes
        add_shortcode('mypooldesigner_gallery', array($this, 'gallery_shortcode'));
        add_shortcode('mypooldesigner_collection', array($this, 'collection_shortcode'));
    }
    
    public function activate() {
        // Set default options on activation
        add_option('mpd_api_key', '');
        add_option('mpd_activation_notice', true);
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('MyPoolDesigner', 'mypooldesigner-gallery'),
            __('MyPoolDesigner', 'mypooldesigner-gallery'),
            'manage_options',
            'mypooldesigner',
            array($this, 'admin_page'),
            'data:image/svg+xml;base64,' . base64_encode($this->get_mpd_icon_svg()),
            30
        );
    }
    
    private function get_mpd_icon_svg() {
        // Your actual MPD wand icon - colors will be handled by WordPress admin CSS
        return '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 177 173">
<path fill="currentColor" d="M32.749962,114.750038 C51.018864,96.480080 69.037216,78.459618 87.056824,60.440407 C97.246758,50.250710 107.372360,39.995098 117.680962,29.926872 C119.986298,27.675280 122.701691,25.492676 125.643669,24.323765 C130.381332,22.441389 134.215164,25.639980 137.626038,28.295614 C141.015915,30.934898 144.217178,34.028564 146.793701,37.454960 C151.724152,44.011715 151.082260,49.988804 144.202744,56.903801 C135.214859,65.938065 126.092262,74.838242 117.037033,83.805611 C104.029709,96.686722 91.014687,109.560127 78.033905,122.467949 C68.803413,131.646561 59.807625,141.070694 50.353363,150.011215 C44.617085,155.435776 39.307659,155.793289 33.194759,150.879562 C28.805349,147.351242 25.259037,142.671219 21.744461,138.185410 C17.985342,133.387497 20.252455,127.502296 23.686008,123.849884 C26.536495,120.817696 29.555555,117.943970 32.749962,114.750038 M63.999989,118.499992 C77.499672,105.074615 90.999344,91.649239 104.507881,78.215050 C101.584488,75.088181 99.257713,72.599457 97.448074,70.663872 C76.462006,91.643845 55.482365,112.617386 34.029018,134.064499 C34.861271,134.762970 36.358768,135.642944 37.344177,136.918518 C40.133354,140.528946 42.366268,140.615768 45.593975,137.060059 C51.286346,130.789230 57.502899,124.994217 63.999989,118.499992 M118.999931,47.500103 C114.644241,51.757206 110.288544,56.014309 106.900452,59.325710 C109.590691,62.492592 111.919823,65.234383 113.818497,67.469452 C121.107750,60.189171 128.360245,52.945591 135.590515,45.724216 C133.318924,43.496613 130.763718,40.990902 127.940338,38.222202 C125.255859,41.014004 122.377846,44.007076 118.999931,47.500103"/>
<path fill="currentColor" d="M36.829105,73.093536 C35.230080,73.015457 34.010723,73.223198 32.909851,72.951279 C29.218056,72.039383 25.952742,70.522789 26.018126,65.933189 C26.082911,61.385586 29.326164,59.855713 33.044903,59.055431 C34.449020,58.753258 35.972641,59.006390 37.674061,59.006390 C38.479542,54.262459 36.470791,47.830185 44.412952,48.017536 C49.429127,48.135857 50.538170,49.879955 51.327293,58.746628 C56.555241,58.808819 61.644707,59.969105 62.755466,66.050148 C63.504509,70.150909 59.787041,72.322670 50.994083,73.513176 C50.994083,74.899101 51.104023,76.363869 50.973347,77.806854 C50.590454,82.034927 48.006638,84.239563 43.656620,83.979935 C39.591831,83.737335 37.926716,81.355186 37.958878,77.410454 C37.970413,75.995483 37.508755,74.576645 36.829105,73.093536"/>
<path fill="currentColor" d="M139.286682,118.845718 C136.743454,118.955452 134.612015,118.955452 132.568130,118.955452 C131.210663,128.028366 129.346924,131.065872 125.397942,130.996201 C121.379776,130.925293 119.406548,127.571907 118.681602,119.291824 C113.648720,118.920052 107.529839,119.525398 107.095528,112.084129 C106.871895,108.252380 110.920082,106.206581 118.928696,105.806984 C118.524269,100.239677 119.159035,93.849419 126.337830,94.320885 C132.152359,94.702751 132.574280,100.568428 131.846420,105.940720 C137.193802,105.589798 142.381546,105.784874 143.643082,111.240349 C144.124786,113.323448 141.116348,116.213615 139.286682,118.845718"/>
<path fill="currentColor" d="M74.652641,24.032814 C78.097054,26.953981 81.205963,29.802540 84.183128,32.782719 C84.960945,33.561325 85.962776,34.903961 85.763832,35.719860 C85.210991,37.987183 84.680504,41.166683 83.060860,42.089268 C78.961716,44.424229 77.212914,52.429260 70.647209,48.643215 C66.919136,46.493458 64.151161,42.569717 61.238628,39.196392 C60.566856,38.418339 60.855236,36.583042 61.084835,35.302982 C61.548409,32.718433 71.460800,24.374384 74.652641,24.032814"/>
</svg>';
    }
    
    
    public function register_settings() {
        register_setting('mpd_settings', 'mpd_api_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ));
    }
    
    public function enqueue_frontend_assets() {
        // Enqueue dashicons for frontend (needed for video indicators)
        wp_enqueue_style('dashicons');
        
        // Plugin styles (replaces external dependencies)
        wp_enqueue_style('mpd-gallery-style', MPD_PLUGIN_URL . 'assets/css/gallery.css', array(), MPD_VERSION);
        
        // Add custom CSS inline
        wp_add_inline_style('mpd-gallery-style', $this->get_custom_css());
        
        // jQuery (WordPress includes it)
        wp_enqueue_script('jquery');
        
        // Plugin scripts (replaces external dependencies)
        wp_enqueue_script('mpd-gallery-script', MPD_PLUGIN_URL . 'assets/js/gallery.js', array('jquery'), MPD_VERSION, true);
        
        // Custom JS for multi-image navigation and video support
        wp_add_inline_script('mpd-gallery-script', $this->get_frontend_js());
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'mypooldesigner') === false) {
            return;
        }
        
        // Enqueue external admin CSS file instead of inline styles
        wp_enqueue_style('mpd-admin-style', MPD_PLUGIN_URL . 'assets/css/admin.css', array(), MPD_VERSION);
    }
    
    private function get_admin_css() {
        return '
        .mpd-admin-wrapper {
            max-width: 1400px;
            margin: 20px auto;
        }
        
        .mpd-admin-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .mpd-icon-large {
            width: 48px;
            height: 48px;
        }
        
        .mpd-shortcode-section {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .mpd-shortcode-card {
            background: white;
            border: 1px solid #ccd0d4;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .mpd-shortcode-card h3 {
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1d2327;
        }
        
        .mpd-cta-section {
            margin-top: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            color: white;
        }
        
        .mpd-cta-section h2 {
            color: white;
            font-size: 32px;
            margin-bottom: 20px;
        }
        
        .mpd-cta-section p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.95;
        }
        
        .mpd-cta-button {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .mpd-cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            color: #764ba2;
        }
        
        .mpd-pricing-section {
            margin-top: 50px;
        }
        
        .mpd-pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .mpd-pricing-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 30px;
            padding-bottom: 80px;
            text-align: center;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            min-height: 480px;
        }
        
        .mpd-pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .mpd-pricing-card.featured {
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }
        
        .mpd-pricing-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: #667eea;
            color: white;
            padding: 4px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .mpd-pricing-title {
            font-size: 24px;
            margin-bottom: 10px;
            color: #1d2327;
        }
        
        .mpd-pricing-price {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin: 20px 0;
        }
        
        .mpd-pricing-price span {
            font-size: 16px;
            color: #6b7280;
            font-weight: normal;
        }
        
        .mpd-pricing-features {
            list-style: none;
            padding: 0;
            margin: 20px 0 30px;
        }
        
        .mpd-pricing-features li {
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
            color: #4b5563;
        }
        
        .mpd-pricing-features li:last-child {
            border-bottom: none;
        }
        
        .mpd-pricing-button {
            display: inline-block;
            position: absolute;
            bottom: 20px;
            left: 30px;
            right: 30px;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        
        .mpd-pricing-button:hover {
            background: #5a67d8;
            color: white;
        }
        
        .mpd-pricing-button.secondary {
            background: #e5e7eb;
            color: #1d2327;
        }
        
        .mpd-pricing-button.secondary:hover {
            background: #d1d5db;
            color: #1d2327;
        }
        
        .mpd-connection-status {
            margin-top: 10px;
        }
        
        .mpd-status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
        }
        
        .mpd-status-connected {
            color: #008a20;
            background: #e6f4ea;
        }
        
        .mpd-status-connected .dashicons {
            color: #008a20;
        }
        
        .mpd-status-disconnected {
            color: #d93025;
            background: #fce8e6;
        }
        
        .mpd-status-disconnected .dashicons {
            color: #d93025;
        }
        ';
    }
    
    private function get_custom_css() {
        return '
        /* MyPoolDesigner Gallery Styles */
        .mpd-gallery {
            padding: 20px 0;
        }
        
        .mpd-gallery.light {
            background: #ffffff;
            color: #333333;
        }
        
        .mpd-gallery.dark {
            background: #1a1a1a;
            color: #ffffff;
            padding: 30px;
            border-radius: 8px;
        }
        
        .mpd-gallery .mpd-item {
            margin-bottom: 30px;
            cursor: pointer;
            transition: transform 0.3s ease;
            position: relative;
        }
        
        .mpd-gallery .mpd-item:hover {
            transform: translateY(-5px);
        }
        
        .mpd-gallery .mpd-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .mpd-gallery.dark .mpd-image {
            border: 1px solid #333;
        }
        
        .mpd-gallery .mpd-title {
            margin-top: 10px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .mpd-gallery.dark .mpd-title {
            color: #e0e0e0;
        }
        
        /* Multi-image indicators */
        .mpd-multi-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        /* Video/Presentation indicator */
        .mpd-video-indicator {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(217, 48, 37, 0.9);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        /* Video modal */
        .mpd-video-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
        }
        
        .mpd-video-container {
            position: relative;
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .mpd-video-container video {
            width: 100%;
            height: auto;
        }
        
        .mpd-video-close {
            position: absolute;
            top: -40px;
            right: 0;
            color: #fff;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
            background: rgba(0,0,0,0.5);
            padding: 0 15px;
            border-radius: 4px;
        }
        
        .mpd-video-close:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .mpd-video-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 10px;
            text-align: center;
        }
        
        /* Pagination */
        .mpd-pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 10px;
        }
        
        .mpd-pagination button {
            padding: 8px 16px;
            background: #007cba;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .mpd-pagination button:hover:not(:disabled) {
            background: #005a87;
        }
        
        .mpd-pagination button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .mpd-pagination .page-info {
            display: flex;
            align-items: center;
            padding: 8px 16px;
            background: #f0f0f0;
            border-radius: 4px;
        }
        
        .mpd-gallery.dark .mpd-pagination .page-info {
            background: #333;
            color: #fff;
        }
        
        .mpd-collection-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
        }
        
        .mpd-collection-header h2 {
            margin: 0;
            color: white;
        }
        
        .mpd-error {
            padding: 15px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            color: #721c24;
            margin: 20px 0;
        }
        
        @media (max-width: 768px) {
            .mpd-gallery .mpd-image {
                height: 200px;
            }
        }
        ';
    }
    
    private function get_frontend_js() {
        return '
        jQuery(document).ready(function($) {
            // Multi-image gallery navigation
            var currentImageIndexes = {};
            
            // Handle multi-image items
            $(document).on("click", ".mpd-multi-item", function(e) {
                e.preventDefault();
                var $item = $(this);
                var itemId = $item.data("item-id");
                var images = $item.data("images").split(",");
                
                if (!currentImageIndexes[itemId]) {
                    currentImageIndexes[itemId] = 0;
                }
                
                // Cycle through images
                currentImageIndexes[itemId] = (currentImageIndexes[itemId] + 1) % images.length;
                var nextImage = images[currentImageIndexes[itemId]];
                
                // Update image
                $item.find(".mpd-image").attr("src", nextImage);
                $item.attr("href", nextImage);
                
                // Update indicator
                var $indicator = $item.closest(".mpd-item").find(".mpd-multi-indicator");
                $indicator.html(
                    "<span class=\"dashicons dashicons-images-alt2\"></span> " + 
                    (currentImageIndexes[itemId] + 1) + "/" + images.length
                );
                
                return false;
            });
            
            // Video modal handling
            $(document).on("click", ".mpd-video-item", function(e) {
                e.preventDefault();
                var videoUrl = $(this).data("video-url");
                var title = $(this).data("title");
                
                // Create modal if it doesn\'t exist
                if ($("#mpd-video-modal").length === 0) {
                    var modalHtml = \'<div id="mpd-video-modal" class="mpd-video-modal">\' +
                        \'<div class="mpd-video-container">\' +
                        \'<span class="mpd-video-close">&times;</span>\' +
                        \'<video id="mpd-video-player" controls autoplay>\' +
                        \'<source src="" type="video/mp4">\' +
                        \'Your browser does not support the video tag.\' +
                        \'</video>\' +
                        \'<div class="mpd-video-title"></div>\' +
                        \'</div>\' +
                        \'</div>\';
                    $("body").append(modalHtml);
                }
                
                // Set video source and title
                $("#mpd-video-player source").attr("src", videoUrl);
                $("#mpd-video-player")[0].load();
                $(".mpd-video-title").text(title);
                
                // Show modal
                $("#mpd-video-modal").fadeIn();
                
                return false;
            });
            
            // Close video modal
            $(document).on("click", ".mpd-video-close, #mpd-video-modal", function(e) {
                if (e.target === this) {
                    $("#mpd-video-modal").fadeOut();
                    $("#mpd-video-player")[0].pause();
                }
            });
            
            // Pagination handling
            $(document).on("click", ".mpd-prev-page", function() {
                var $gallery = $(this).closest(".mpd-gallery-wrapper");
                var currentPage = parseInt($gallery.data("current-page")) || 1;
                if (currentPage > 1) {
                    mpdLoadPage($gallery, currentPage - 1);
                }
            });
            
            $(document).on("click", ".mpd-next-page", function() {
                var $gallery = $(this).closest(".mpd-gallery-wrapper");
                var currentPage = parseInt($gallery.data("current-page")) || 1;
                var totalPages = parseInt($gallery.data("total-pages")) || 1;
                if (currentPage < totalPages) {
                    mpdLoadPage($gallery, currentPage + 1);
                }
            });
            
            function mpdLoadPage($gallery, page) {
                // This would be implemented with AJAX in a full solution
                // For now, pagination is handled by shortcode attributes
                console.log("Loading page", page);
            }
        });
        ';
    }
    
    
    private function validate_api_key($api_key) {
        if (empty($api_key)) {
            return false;
        }
        
        // Make a test API call to validate the key
        $response = wp_remote_get(
            'https://mypooldesigner.ai/api/wordpress/validate-key',
            array(
                'headers' => array(
                    'X-API-Key' => $api_key
                ),
                'timeout' => 10
            )
        );
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        return $status_code === 200;
    }
    
    private function get_mpd_icon_svg_inline() {
        // MPD wand icon for the admin header
        return '<svg class="mpd-icon-large" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 177 173">
<path fill="#3B74F9" d="M32.749962,114.750038 C51.018864,96.480080 69.037216,78.459618 87.056824,60.440407 C97.246758,50.250710 107.372360,39.995098 117.680962,29.926872 C119.986298,27.675280 122.701691,25.492676 125.643669,24.323765 C130.381332,22.441389 134.215164,25.639980 137.626038,28.295614 C141.015915,30.934898 144.217178,34.028564 146.793701,37.454960 C151.724152,44.011715 151.082260,49.988804 144.202744,56.903801 C135.214859,65.938065 126.092262,74.838242 117.037033,83.805611 C104.029709,96.686722 91.014687,109.560127 78.033905,122.467949 C68.803413,131.646561 59.807625,141.070694 50.353363,150.011215 C44.617085,155.435776 39.307659,155.793289 33.194759,150.879562 C28.805349,147.351242 25.259037,142.671219 21.744461,138.185410 C17.985342,133.387497 20.252455,127.502296 23.686008,123.849884 C26.536495,120.817696 29.555555,117.943970 32.749962,114.750038 M63.999989,118.499992 C77.499672,105.074615 90.999344,91.649239 104.507881,78.215050 C101.584488,75.088181 99.257713,72.599457 97.448074,70.663872 C76.462006,91.643845 55.482365,112.617386 34.029018,134.064499 C34.861271,134.762970 36.358768,135.642944 37.344177,136.918518 C40.133354,140.528946 42.366268,140.615768 45.593975,137.060059 C51.286346,130.789230 57.502899,124.994217 63.999989,118.499992 M118.999931,47.500103 C114.644241,51.757206 110.288544,56.014309 106.900452,59.325710 C109.590691,62.492592 111.919823,65.234383 113.818497,67.469452 C121.107750,60.189171 128.360245,52.945591 135.590515,45.724216 C133.318924,43.496613 130.763718,40.990902 127.940338,38.222202 C125.255859,41.014004 122.377846,44.007076 118.999931,47.500103"/>
<path fill="#3B74F9" d="M36.829105,73.093536 C35.230080,73.015457 34.010723,73.223198 32.909851,72.951279 C29.218056,72.039383 25.952742,70.522789 26.018126,65.933189 C26.082911,61.385586 29.326164,59.855713 33.044903,59.055431 C34.449020,58.753258 35.972641,59.006390 37.674061,59.006390 C38.479542,54.262459 36.470791,47.830185 44.412952,48.017536 C49.429127,48.135857 50.538170,49.879955 51.327293,58.746628 C56.555241,58.808819 61.644707,59.969105 62.755466,66.050148 C63.504509,70.150909 59.787041,72.322670 50.994083,73.513176 C50.994083,74.899101 51.104023,76.363869 50.973347,77.806854 C50.590454,82.034927 48.006638,84.239563 43.656620,83.979935 C39.591831,83.737335 37.926716,81.355186 37.958878,77.410454 C37.970413,75.995483 37.508755,74.576645 36.829105,73.093536"/>
<path fill="#3B74F9" d="M139.286682,118.845718 C136.743454,118.955452 134.612015,118.955452 132.568130,118.955452 C131.210663,128.028366 129.346924,131.065872 125.397942,130.996201 C121.379776,130.925293 119.406548,127.571907 118.681602,119.291824 C113.648720,118.920052 107.529839,119.525398 107.095528,112.084129 C106.871895,108.252380 110.920082,106.206581 118.928696,105.806984 C118.524269,100.239677 119.159035,93.849419 126.337830,94.320885 C132.152359,94.702751 132.574280,100.568428 131.846420,105.940720 C137.193802,105.589798 142.381546,105.784874 143.643082,111.240349 C144.124786,113.323448 141.116348,116.213615 139.286682,118.845718"/>
<path fill="#3B74F9" d="M74.652641,24.032814 C78.097054,26.953981 81.205963,29.802540 84.183128,32.782719 C84.960945,33.561325 85.962776,34.903961 85.763832,35.719860 C85.210991,37.987183 84.680504,41.166683 83.060860,42.089268 C78.961716,44.424229 77.212914,52.429260 70.647209,48.643215 C66.919136,46.493458 64.151161,42.569717 61.238628,39.196392 C60.566856,38.418339 60.855236,36.583042 61.084835,35.302982 C61.548409,32.718433 71.460800,24.374384 74.652641,24.032814"/>
</svg>';
    }
    
    public function admin_page() {
        $api_key = get_option('mpd_api_key');
        $is_connected = $this->validate_api_key($api_key);
        ?>
        <div class="wrap mpd-admin-wrapper">
            <div class="mpd-admin-header">
                <?php echo wp_kses($this->get_mpd_icon_svg_inline(), array(
                    'svg' => array('class' => array(), 'version' => array(), 'xmlns' => array(), 'viewBox' => array()),
                    'path' => array('fill' => array(), 'd' => array())
                )); ?>
                <h1><?php echo esc_html__('MyPoolDesigner Gallery Settings', 'mypooldesigner-gallery'); ?></h1>
            </div>
            
            <form method="post" action="options.php">
                <?php settings_fields('mpd_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="mpd_api_key"><?php echo esc_html__('API Key', 'mypooldesigner-gallery'); ?></label></th>
                        <td>
                            <input type="password" id="mpd_api_key" name="mpd_api_key" 
                                   value="<?php echo esc_attr($api_key); ?>" 
                                   class="regular-text" />
                            <p class="description"><?php
                                printf(
                                    // translators: %s is replaced with a link to the MyPoolDesigner WordPress Integration page
                                    esc_html__('Get your API key from %s', 'mypooldesigner-gallery'),
                                    '<a href="' . esc_url('https://mypooldesigner.ai/advanced') . '" target="_blank" rel="noopener noreferrer">' . 
                                    // translators: Link text for the MyPoolDesigner WordPress Integration page
                                    esc_html__('MyPoolDesigner WordPress Integration', 'mypooldesigner-gallery') . '</a>'
                                ); ?>
                            </p>
                            <?php if (!empty($api_key)): ?>
                                <div class="mpd-connection-status">
                                    <?php if ($is_connected): ?>
                                        <span class="mpd-status-indicator mpd-status-connected">
                                            <span class="dashicons dashicons-yes-alt"></span> <?php echo esc_html__('Connected', 'mypooldesigner-gallery'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="mpd-status-indicator mpd-status-disconnected">
                                            <span class="dashicons dashicons-dismiss"></span> <?php echo esc_html__('API not connected', 'mypooldesigner-gallery'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2>Shortcode Documentation</h2>
            
            <div class="mpd-shortcode-section">
                <div class="mpd-shortcode-card">
                    <h3>Gallery Shortcode</h3>
                    <p>Display your public pool designs in a responsive grid:</p>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Shortcode</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>[mypooldesigner_gallery]</code></td>
                                <td>Display gallery with default settings (light theme, 12 items per page)</td>
                            </tr>
                            <tr>
                                <td><code>[mypooldesigner_gallery pagination=20]</code></td>
                                <td>Display 20 items per page</td>
                            </tr>
                            <tr>
                                <td><code>[mypooldesigner_gallery dark pagination=8]</code></td>
                                <td>Dark theme with 8 items per page</td>
                            </tr>
                            <tr>
                                <td><code>[mypooldesigner_gallery light pagination=16]</code></td>
                                <td>Light theme with 16 items per page</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mpd-shortcode-card">
                    <h3>Collection Shortcode</h3>
                    <p>Display specific numbered collections:</p>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Shortcode</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>[mypooldesigner_collection 1]</code></td>
                                <td>Display oldest collection with default settings</td>
                            </tr>
                            <tr>
                                <td><code>[mypooldesigner_collection 2 pagination=8]</code></td>
                                <td>Second collection with 8 items per page</td>
                            </tr>
                            <tr>
                                <td><code>[mypooldesigner_collection dark 3 pagination=12]</code></td>
                                <td>Third collection with dark theme and 12 items per page</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php if (!$is_connected): ?>
            <div class="mpd-cta-section">
                <h2>Transform Your Pool Business with AI</h2>
                <p>Join thousands of pool professionals using MyPoolDesigner.ai to create stunning visualizations in seconds</p>
                <a href="https://mypooldesigner.ai/ai-pool-design" target="_blank" rel="noopener noreferrer" class="mpd-cta-button"><?php echo esc_html__('Start Your Free Trial', 'mypooldesigner-gallery'); ?></a>
            </div>
            
            <div class="mpd-pricing-section">
                <h2 style="text-align: center; font-size: 28px; margin-bottom: 10px;">Choose Your Plan</h2>
                <p style="text-align: center; color: #6b7280;">Start free, upgrade when you need more power</p>
                
                <div class="mpd-pricing-grid">
                    <div class="mpd-pricing-card">
                        <div class="mpd-pricing-title">Free</div>
                        <div class="mpd-pricing-price">$0<span>/month</span></div>
                        <ul class="mpd-pricing-features">
                            <li>10 designs during 7-day trial</li>
                            <li>Access to all features during trial</li>
                            <li>Standard resolution downloads</li>
                            <li>Video design generation</li>
                            <li>Presentation creator</li>
                            <li>Add designs to collections</li>
                            <li>Share your designs</li>
                        </ul>
                        <a href="https://mypooldesigner.ai/ai-pool-design" target="_blank" class="mpd-pricing-button secondary">Start Free Trial</a>
                    </div>
                    
                    <div class="mpd-pricing-card featured">
                        <div class="mpd-pricing-badge">Most Popular</div>
                        <div class="mpd-pricing-title">Pro</div>
                        <div class="mpd-pricing-price">$29.99<span>/month</span></div>
                        <ul class="mpd-pricing-features">
                            <li><strong>100 images per month</strong></li>
                            <li><strong>3 videos per month</strong></li>
                            <li>High-resolution downloads</li>
                            <li>Upload backyard generator</li>
                            <li>Presentation Maker</li>
                            <li>Custom prompt design generator</li>
                            <li>Image to video generator</li>
                            <li>Email support</li>
                            <li>Commercial usage rights</li>
                        </ul>
                        <a href="https://mypooldesigner.ai/subscribe" target="_blank" class="mpd-pricing-button">Upgrade to Pro</a>
                    </div>
                    
                    <div class="mpd-pricing-card">
                        <div class="mpd-pricing-title">Premium</div>
                        <div class="mpd-pricing-price">$89.99<span>/month</span></div>
                        <ul class="mpd-pricing-features">
                            <li><strong>500 images per month</strong></li>
                            <li><strong>10 videos per month</strong></li>
                            <li>Presentation maker</li>
                            <li>Multiple viewpoints & aspects</li>
                            <li>Choose AI modeling styles</li>
                            <li>Custom prompt generator</li>
                            <li>Upload backyard generator</li>
                            <li>Priority email support</li>
                            <li>Commercial usage rights</li>
                        </ul>
                        <a href="https://mypooldesigner.ai/subscribe" target="_blank" class="mpd-pricing-button">Upgrade to Premium</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="card" style="padding: 20px; margin: 40px 0 20px; background: #f0f8ff;">
                <h3>Plugin Features</h3>
                <ul>
                    <li>✓ Responsive Bootstrap grid (4 columns on desktop)</li>
                    <li>✓ Lightbox modal for full-size image viewing</li>
                    <li>✓ Multi-image navigation with arrow indicators</li>
                    <li>✓ Video/presentation playback support</li>
                    <li>✓ Pagination controls with customizable items per page</li>
                    <li>✓ Light and dark theme support</li>
                    <li>✓ Shows only your public designs</li>
                    <li>✓ Clean display with image and title</li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    
    
    public function gallery_shortcode($atts) {
        // Parse attributes
        $theme = 'light';
        $pagination = 12; // Default items per page
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- This is for pagination, nonce not required for GET parameter
        $page = isset($_GET['mpd_page']) ? max(1, intval(sanitize_text_field(wp_unslash($_GET['mpd_page'])))) : 1;
        
        // Handle both positional and named attributes
        if (is_array($atts)) {
            foreach ($atts as $key => $value) {
                if (is_numeric($key)) {
                    // Positional parameter
                    if (in_array($value, array('light', 'dark'))) {
                        $theme = $value;
                    }
                } else {
                    // Named parameter
                    if ($key === 'pagination') {
                        $pagination = max(1, min(100, intval($value))); // Limit between 1-100
                    } elseif ($key === 'theme' && in_array($value, array('light', 'dark'))) {
                        $theme = $value;
                    }
                }
            }
        }
        
        $api_key = get_option('mpd_api_key');
        if (empty($api_key)) {
            // translators: → is a navigation arrow indicating the path to settings
            return '<div class="mpd-error">' . esc_html__('Please configure your API key in WordPress admin → MyPoolDesigner settings.', 'mypooldesigner-gallery') . '</div>';
        }
        
        // Check if API key is valid
        if (!$this->validate_api_key($api_key)) {
            return '<div class="mpd-error">' . esc_html__('API not connected. Please check your API key in the MyPoolDesigner settings.', 'mypooldesigner-gallery') . '</div>';
        }
        
        // Calculate offset for pagination
        $offset = ($page - 1) * $pagination;
        
        // Fetch designs from API with pagination
        $response = $this->fetch_designs($api_key, $pagination, $offset);
        
        if ($response['error']) {
            return '<div class="mpd-error">' . esc_html($response['message']) . '</div>';
        }
        
        return $this->render_gallery($response['designs'], $theme, $page, $response['total'], $pagination);
    }
    
    public function collection_shortcode($atts) {
        // Parse attributes
        $theme = 'light';
        $pagination = 12;
        $collection_number = null;
        
        // Handle both positional and named attributes
        if (is_array($atts)) {
            foreach ($atts as $key => $value) {
                if (is_numeric($key)) {
                    // Positional parameter
                    if (in_array($value, array('light', 'dark'))) {
                        $theme = $value;
                    } elseif (is_numeric($value)) {
                        $collection_number = intval($value);
                    }
                } else {
                    // Named parameter
                    if ($key === 'pagination') {
                        $pagination = max(1, min(100, intval($value)));
                    } elseif ($key === 'theme' && in_array($value, array('light', 'dark'))) {
                        $theme = $value;
                    }
                }
            }
        }
        
        if (!$collection_number) {
            return '<div class="mpd-error">Please specify a collection number (e.g., [mypooldesigner-collection 1])</div>';
        }
        
        $api_key = get_option('mpd_api_key');
        if (empty($api_key)) {
            // translators: → is a navigation arrow indicating the path to settings
            return '<div class="mpd-error">' . esc_html__('Please configure your API key in WordPress admin → MyPoolDesigner settings.', 'mypooldesigner-gallery') . '</div>';
        }
        
        // Check if API key is valid
        if (!$this->validate_api_key($api_key)) {
            return '<div class="mpd-error">' . esc_html__('API not connected. Please check your API key in the MyPoolDesigner settings.', 'mypooldesigner-gallery') . '</div>';
        }
        
        // Fetch collection from API
        $response = $this->fetch_collection($api_key, $collection_number);
        
        if ($response['error']) {
            return '<div class="mpd-error">' . esc_html($response['message']) . '</div>';
        }
        
        // Add collection header if we have collection info
        $output = '';
        if (!empty($response['collection'])) {
            $output .= '<div class="mpd-collection-header">';
            $output .= '<h2>' . esc_html($response['collection']['name']) . '</h2>';
            if (!empty($response['collection']['description'])) {
                $output .= '<p>' . esc_html($response['collection']['description']) . '</p>';
            }
            $output .= '</div>';
        }
        
        $output .= $this->render_gallery($response['designs'], $theme, 1, count($response['designs']), $pagination);
        
        return $output;
    }
    
    private function fetch_designs($api_key, $limit = 12, $offset = 0) {
        $response = wp_remote_post(MPD_API_URL . '/wordpress/designs', array(
            'method' => 'POST',
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode(array(
                'api_key' => $api_key,
                'limit' => $limit,
                'offset' => $offset
            )),
            'timeout' => 15
        ));
        
        if (is_wp_error($response)) {
            return array(
                'error' => true,
                'message' => __('Failed to connect to MyPoolDesigner API', 'mypooldesigner-gallery')
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (!isset($data['success']) || !$data['success']) {
            return array(
                'error' => true,
                'message' => isset($data['message']) ? $data['message'] : 'Failed to fetch designs'
            );
        }
        
        return array(
            'error' => false,
            'designs' => $data['designs'],
            'total' => isset($data['total']) ? $data['total'] : count($data['designs'])
        );
    }
    
    private function fetch_collection($api_key, $collection_number) {
        // Debug: Attempting to fetch collection (production-ready)
        
        $response = wp_remote_post(MPD_API_URL . '/wordpress/collections', array(
            'method' => 'POST',
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode(array(
                'api_key' => $api_key,
                'collection_number' => $collection_number
            )),
            'timeout' => 15
        ));
        
        if (is_wp_error($response)) {
            return array(
                'error' => true,
                'message' => __('Failed to connect to MyPoolDesigner API', 'mypooldesigner-gallery')
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        // Collection API response processed (production-ready)
        
        if (!isset($data['success']) || !$data['success']) {
            return array(
                'error' => true,
                'message' => isset($data['message']) ? $data['message'] : 'Failed to fetch collection'
            );
        }
        
        return array(
            'error' => false,
            'collection' => isset($data['collection']) ? $data['collection'] : null,
            'designs' => $data['designs']
        );
    }
    
    private function render_gallery($designs, $theme = 'light', $current_page = 1, $total_items = 0, $items_per_page = 12) {
        if (empty($designs)) {
            return '<div class="mpd-error">' . esc_html__('No designs found. Make sure you have public designs in your MyPoolDesigner account.', 'mypooldesigner-gallery') . '</div>';
        }
        
        $total_pages = $items_per_page > 0 ? ceil($total_items / $items_per_page) : 1;
        
        $output = '<div class="mpd-gallery-wrapper" data-current-page="' . $current_page . '" data-total-pages="' . $total_pages . '">';
        $output .= '<div class="mpd-gallery ' . esc_attr($theme) . '">';
        $output .= '<div class="container-fluid">';
        $output .= '<div class="row">';
        
        foreach ($designs as $design) {
            $title = !empty($design['title']) ? $design['title'] : 'Pool Design';
            $image = !empty($design['imageUrl']) ? $design['imageUrl'] : '';
            $video_url = !empty($design['videoUrl']) ? $design['videoUrl'] : '';
            $image_urls = !empty($design['imageUrls']) ? $design['imageUrls'] : array();
            
            // Skip if no image
            if (empty($image)) continue;
            
            $output .= '<div class="col-lg-3 col-md-4 col-sm-6">';
            $output .= '<div class="mpd-item">';
            
            // Check if this is a multi-image design (more than 1 image in imageUrls)
            $is_multi_image = is_array($image_urls) && count($image_urls) > 1;
            
            if ($video_url) {
                // Video/presentation item
                $output .= '<div class="mpd-video-item" data-video-url="' . esc_attr($video_url) . '" data-title="' . esc_attr($title) . '">';
                $output .= '<img src="' . esc_attr($image) . '" alt="' . esc_attr($title) . '" class="mpd-image">';
                $output .= '<div class="mpd-video-indicator"><span class="dashicons dashicons-video-alt3"></span> Video</div>';
                $output .= '</div>';
            } elseif ($is_multi_image) {
                // Multi-image item - only show if more than 1 image
                $images_string = implode(',', array_map('esc_attr', $image_urls));
                $item_id = 'mpd-' . md5($images_string);
                $output .= '<a href="' . esc_attr($image_urls[0]) . '" data-lightbox="gallery-' . $item_id . '" 
                               class="mpd-multi-item" data-item-id="' . $item_id . '" 
                               data-images="' . $images_string . '">';
                $output .= '<img src="' . esc_attr($image_urls[0]) . '" alt="' . esc_attr($title) . '" class="mpd-image">';
                $output .= '<div class="mpd-multi-indicator"><span class="dashicons dashicons-images-alt2"></span> 1/' . count($image_urls) . '</div>';
                $output .= '</a>';
                
                // Add hidden links for lightbox gallery
                for ($i = 1; $i < count($image_urls); $i++) {
                    $output .= '<a href="' . esc_attr($image_urls[$i]) . '" data-lightbox="gallery-' . $item_id . '" style="display:none;"></a>';
                }
            } else {
                // Single image item
                $output .= '<a href="' . esc_attr($image) . '" data-lightbox="mpd-gallery">';
                $output .= '<img src="' . esc_attr($image) . '" alt="' . esc_attr($title) . '" class="mpd-image">';
                $output .= '</a>';
            }
            
            $output .= '<div class="mpd-title">' . esc_html($title) . '</div>';
            $output .= '</div>';
            $output .= '</div>';
        }
        
        $output .= '</div>'; // row
        $output .= '</div>'; // container
        
        // Add pagination if needed
        if ($total_pages > 1) {
            $output .= '<div class="mpd-pagination">';
            $output .= '<button class="mpd-prev-page" ' . ($current_page <= 1 ? 'disabled' : '') . '>Previous</button>';
            $output .= '<div class="page-info">Page ' . $current_page . ' of ' . $total_pages . '</div>';
            $output .= '<button class="mpd-next-page" ' . ($current_page >= $total_pages ? 'disabled' : '') . '>Next</button>';
            $output .= '</div>';
        }
        
        $output .= '</div>'; // mpd-gallery
        $output .= '</div>'; // wrapper
        
        return $output;
    }
}

// Initialize the plugin
MyPoolDesigner_Gallery::get_instance();