<?php
/**
 * Plugin Name: MyPoolDesigner Gallery
 * Plugin URI: https://mypooldesigner.ai
 * Description: Display your MyPoolDesigner pool designs in a responsive gallery with lightbox functionality. Supports collections, multi-image navigation, and video presentations.
 * Version: 1.2.0
 * Author: MyPoolDesigner.ai
 * Author URI: https://mypooldesigner.ai
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mypooldesigner-gallery
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MPD_PLUGIN_VERSION', '1.2.0');
define('MPD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MPD_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MPD_API_URL', 'https://mypooldesigner.ai/api');

/**
 * Main MyPoolDesigner Gallery Plugin Class
 */
class MyPoolDesignerGallery {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Admin functionality
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_init', array($this, 'register_settings'));
        }
        
        // Frontend functionality
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('mypooldesigner-gallery', array($this, 'gallery_shortcode'));
        add_shortcode('mypooldesigner-collection', array($this, 'collection_shortcode'));
        add_shortcode('mypooldesigner-design', array($this, 'design_shortcode'));
        add_shortcode('mypooldesigner-videos', array($this, 'videos_shortcode'));
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Bootstrap CSS
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
        
        // Bootstrap JS
        wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
        
        // Plugin styles
        wp_enqueue_style('mpd-gallery-style', MPD_PLUGIN_URL . 'assets/css/gallery.css', array(), MPD_PLUGIN_VERSION);
        
        // Plugin scripts
        wp_enqueue_script('mpd-gallery-script', MPD_PLUGIN_URL . 'assets/js/gallery.js', array('jquery', 'bootstrap'), MPD_PLUGIN_VERSION, true);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'MyPoolDesigner Gallery',
            'MyPoolDesigner',
            'manage_options',
            'mypooldesigner-gallery',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('mpd_settings', 'mpd_api_key');
    }
    
    /**
     * Get MPD icon SVG inline
     */
    private function get_mpd_icon_svg_inline() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 100 100" style="fill: #3b82f6; margin-right: 8px;">
    <circle cx="50" cy="25" r="20" opacity="0.8"/>
    <ellipse cx="50" cy="60" rx="35" ry="15" opacity="0.6"/>
    <path d="M20 45 Q50 35 80 45 Q65 55 50 50 Q35 55 20 45" opacity="0.7"/>
</svg>';
    }
    
    /**
     * Validate API key
     */
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
        
        $response_code = wp_remote_retrieve_response_code($response);
        return $response_code === 200;
    }
    
    /**
     * Admin page content
     */
    public function admin_page() {
        $api_key = get_option('mpd_api_key');
        $is_connected = $this->validate_api_key($api_key);
        ?>
        <div class="wrap mpd-admin-wrapper">
            <div class="mpd-admin-header">
                <?php echo $this->get_mpd_icon_svg_inline(); ?>
                <h1>MyPoolDesigner Gallery Settings</h1>
            </div>
            
            <form method="post" action="options.php">
                <?php settings_fields('mpd_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">API Key</th>
                        <td>
                            <input type="text" name="mpd_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" placeholder="Enter your MyPoolDesigner API key" />
                            <p class="description">
                                Get your API key from <a href="https://mypooldesigner.ai/wordpress-integration" target="_blank">MyPoolDesigner WordPress Integration</a>
                            </p>
                            <?php if ($api_key): ?>
                                <p class="api-status <?php echo $is_connected ? 'connected' : 'error'; ?>">
                                    <?php if ($is_connected): ?>
                                        <span style="color: #46b450;">✓ Connected</span>
                                    <?php else: ?>
                                        <span style="color: #dc3232;">✗ API not connected</span>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <?php if (!$is_connected): ?>
            <div class="card" style="margin: 40px 0 20px; background: #f0f8ff; border-left: 4px solid #3b82f6;">
                <div style="padding: 20px;">
                    <h3 style="margin-top: 0;">🚀 Get Started with MyPoolDesigner.ai</h3>
                    <p>To use this plugin, you need a MyPoolDesigner.ai account and API key.</p>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
                        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e1e5e9;">
                            <h4 style="color: #3b82f6; margin-top: 0;">Free Plan</h4>
                            <div style="font-size: 24px; font-weight: bold; color: #333; margin: 10px 0;">$0<span style="font-size: 14px; font-weight: normal;">/month</span></div>
                            <ul style="list-style: none; padding: 0; margin: 15px 0;">
                                <li style="padding: 5px 0;"><strong>3 images per month</strong></li>
                                <li style="padding: 5px 0;">Basic pool designs</li>
                                <li style="padding: 5px 0;">Standard resolution</li>
                                <li style="padding: 5px 0;">Community support</li>
                            </ul>
                            <a href="https://mypooldesigner.ai/register" target="_blank" style="display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;">Get Started Free</a>
                        </div>
                        
                        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e1e5e9;">
                            <h4 style="color: #3b82f6; margin-top: 0;">Pro</h4>
                            <div style="font-size: 24px; font-weight: bold; color: #333; margin: 10px 0;">$19.99<span style="font-size: 14px; font-weight: normal;">/month</span></div>
                            <ul style="list-style: none; padding: 0; margin: 15px 0;">
                                <li style="padding: 5px 0;"><strong>50 images per month</strong></li>
                                <li style="padding: 5px 0;"><strong>3 videos per month</strong></li>
                                <li style="padding: 5px 0;">High-resolution downloads</li>
                                <li style="padding: 5px 0;">Upload backyard generator</li>
                                <li style="padding: 5px 0;">Presentation Maker</li>
                                <li style="padding: 5px 0;">Custom prompt design generator</li>
                                <li style="padding: 5px 0;">Image to video generator</li>
                                <li style="padding: 5px 0;">Email support</li>
                                <li style="padding: 5px 0;">Commercial usage rights</li>
                            </ul>
                            <a href="https://mypooldesigner.ai/subscribe" target="_blank" style="display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;">Upgrade to Pro</a>
                        </div>
                        
                        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e1e5e9;">
                            <h4 style="color: #3b82f6; margin-top: 0;">Premium</h4>
                            <div style="font-size: 24px; font-weight: bold; color: #333; margin: 10px 0;">$89.99<span style="font-size: 14px; font-weight: normal;">/month</span></div>
                            <ul style="list-style: none; padding: 0; margin: 15px 0;">
                                <li style="padding: 5px 0;"><strong>500 images per month</strong></li>
                                <li style="padding: 5px 0;"><strong>10 videos per month</strong></li>
                                <li style="padding: 5px 0;">Presentation maker</li>
                                <li style="padding: 5px 0;">Multiple viewpoints & aspects</li>
                                <li style="padding: 5px 0;">Choose AI modeling styles</li>
                                <li style="padding: 5px 0;">Custom prompt generator</li>
                                <li style="padding: 5px 0;">Upload backyard generator</li>
                                <li style="padding: 5px 0;">Priority email support</li>
                                <li style="padding: 5px 0;">Commercial usage rights</li>
                            </ul>
                            <a href="https://mypooldesigner.ai/subscribe" target="_blank" style="display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;">Upgrade to Premium</a>
                        </div>
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
            
            <div class="card" style="padding: 20px; margin: 20px 0; background: #fff;">
                <h3>Usage Examples</h3>
                <p><strong>Basic Gallery:</strong></p>
                <code>[mypooldesigner-gallery]</code>
                
                <p><strong>Gallery with Custom Pagination:</strong></p>
                <code>[mypooldesigner-gallery pagination="20"]</code>
                
                <p><strong>Dark Theme Gallery:</strong></p>
                <code>[mypooldesigner-gallery theme="dark"]</code>
                
                <p><strong>Display Specific Collection:</strong></p>
                <code>[mypooldesigner-collection 1]</code>
                
                <p><strong>Collection with Custom Settings:</strong></p>
                <code>[mypooldesigner-collection 1 pagination="15" theme="light"]</code>
            </div>
        </div>
        
        <style>
        .mpd-admin-wrapper .mpd-admin-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .mpd-admin-wrapper .api-status {
            font-weight: bold;
            margin-top: 5px;
        }
        .mpd-admin-wrapper .card {
            background: white;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
        .mpd-admin-wrapper code {
            background: #f6f7f7;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: Monaco, Consolas, monospace;
        }
        </style>
        <?php
    }
    
    /**
     * Gallery shortcode handler
     */
    public function gallery_shortcode($atts) {
        // Parse attributes
        $theme = 'light';
        $pagination = 12; // Default items per page
        $page = isset($_GET['mpd_page']) ? max(1, intval($_GET['mpd_page'])) : 1;
        
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
            return '<div class="mpd-error">Please configure your API key in WordPress admin → MyPoolDesigner settings.</div>';
        }
        
        // Check if API key is valid
        if (!$this->validate_api_key($api_key)) {
            return '<div class="mpd-error">API not connected. Please check your API key in the MyPoolDesigner settings.</div>';
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
    
    /**
     * Collection shortcode handler
     */
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
            return '<div class="mpd-error">Please configure your API key in WordPress admin → MyPoolDesigner settings.</div>';
        }
        
        // Check if API key is valid
        if (!$this->validate_api_key($api_key)) {
            return '<div class="mpd-error">API not connected. Please check your API key in the MyPoolDesigner settings.</div>';
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
    
    /**
     * Design shortcode handler (placeholder for future single design display)
     */
    public function design_shortcode($atts) {
        return '<div class="mpd-error">Single design display coming soon!</div>';
    }
    
    /**
     * Videos shortcode handler (placeholder for future video gallery)
     */
    public function videos_shortcode($atts) {
        return '<div class="mpd-error">Video gallery coming soon!</div>';
    }
    
    /**
     * Fetch designs from API
     */
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
                'message' => 'Failed to connect to MyPoolDesigner API'
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
    
    /**
     * Fetch collection from API
     */
    private function fetch_collection($api_key, $collection_number) {
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
                'message' => 'Failed to connect to MyPoolDesigner API'
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
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
    
    /**
     * Render gallery HTML
     */
    private function render_gallery($designs, $theme = 'light', $current_page = 1, $total_items = 0, $items_per_page = 12) {
        if (empty($designs)) {
            return '<div class="mpd-error">No designs found. Make sure you have public designs in your MyPoolDesigner account.</div>';
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
                $output .= '<div class="mpd-multi-image-item" data-images="' . $images_string . '" data-title="' . esc_attr($title) . '">';
                $output .= '<img src="' . esc_attr($image) . '" alt="' . esc_attr($title) . '" class="mpd-image">';
                $output .= '<div class="mpd-multi-indicator"><span class="dashicons dashicons-images-alt2"></span> ' . count($image_urls) . ' Images</div>';
                $output .= '</div>';
            } else {
                // Single image item
                $output .= '<div class="mpd-single-image-item" data-image-url="' . esc_attr($image) . '" data-title="' . esc_attr($title) . '">';
                $output .= '<img src="' . esc_attr($image) . '" alt="' . esc_attr($title) . '" class="mpd-image">';
                $output .= '</div>';
            }
            
            $output .= '<div class="mpd-item-title">' . esc_html($title) . '</div>';
            $output .= '</div>';
            $output .= '</div>';
        }
        
        $output .= '</div>'; // End row
        $output .= '</div>'; // End container-fluid
        
        // Add pagination if needed
        if ($total_pages > 1) {
            $output .= $this->render_pagination($current_page, $total_pages);
        }
        
        $output .= '</div>'; // End mpd-gallery
        $output .= $this->render_lightbox_modal();
        $output .= '</div>'; // End mpd-gallery-wrapper
        
        return $output;
    }
    
    /**
     * Render pagination
     */
    private function render_pagination($current_page, $total_pages) {
        $output = '<div class="mpd-pagination">';
        $output .= '<nav aria-label="Pool designs pagination">';
        $output .= '<ul class="pagination justify-content-center">';
        
        // Previous page
        if ($current_page > 1) {
            $prev_url = add_query_arg('mpd_page', $current_page - 1);
            $output .= '<li class="page-item"><a class="page-link" href="' . esc_url($prev_url) . '">Previous</a></li>';
        } else {
            $output .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }
        
        // Page numbers
        $start = max(1, $current_page - 2);
        $end = min($total_pages, $current_page + 2);
        
        if ($start > 1) {
            $output .= '<li class="page-item"><a class="page-link" href="' . esc_url(add_query_arg('mpd_page', 1)) . '">1</a></li>';
            if ($start > 2) {
                $output .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $current_page) {
                $output .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $output .= '<li class="page-item"><a class="page-link" href="' . esc_url(add_query_arg('mpd_page', $i)) . '">' . $i . '</a></li>';
            }
        }
        
        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                $output .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $output .= '<li class="page-item"><a class="page-link" href="' . esc_url(add_query_arg('mpd_page', $total_pages)) . '">' . $total_pages . '</a></li>';
        }
        
        // Next page
        if ($current_page < $total_pages) {
            $next_url = add_query_arg('mpd_page', $current_page + 1);
            $output .= '<li class="page-item"><a class="page-link" href="' . esc_url($next_url) . '">Next</a></li>';
        } else {
            $output .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }
        
        $output .= '</ul>';
        $output .= '</nav>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render lightbox modal
     */
    private function render_lightbox_modal() {
        return '
        <!-- MyPoolDesigner Lightbox Modal -->
        <div class="modal fade" id="mpdLightbox" tabindex="-1" aria-labelledby="mpdLightboxLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mpdLightboxLabel">Pool Design</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="mpdLightboxContent">
                            <!-- Content will be loaded here -->
                        </div>
                        <div id="mpdNavigationControls" style="display: none;">
                            <button type="button" class="btn btn-outline-primary" id="mpdPrevImage">← Previous</button>
                            <span id="mpdImageCounter"></span>
                            <button type="button" class="btn btn-outline-primary" id="mpdNextImage">Next →</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
}

// Initialize the plugin
new MyPoolDesignerGallery();

// Activation hook
register_activation_hook(__FILE__, function() {
    // Plugin activation tasks if needed
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Plugin deactivation tasks if needed
});