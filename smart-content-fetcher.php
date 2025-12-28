<?php
/**
 * Plugin Name:       Smart Content Fetcher
 * Description:       Example block scaffolded with Create Block tool.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Yusuf Abubakar Saka
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-content-fetcher
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function create_block_smart_content_fetcher_block_init() {
	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata.
	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 */
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
	 * Added to WordPress 6.7 to improve the performance of block type registration.
	 *
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}
	/**
	 * Registers the block type(s) in the `blocks-manifest.php` file.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', 'create_block_smart_content_fetcher_block_init' );
class Smart_Content_Fetcher {

    /**
     * Initialize the plugin hooks.
     */
    public function init(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
        add_action('admin_menu', [$this, 'add_plugin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        
        // Meta Box Hooks (Legacy Editor Support)
        add_action('add_meta_boxes', [$this, 'register_meta_box']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    /**
     * Add the top-level menu item.
     */
    public function add_plugin_menu(): void {
        add_options_page(
            'Smart Content Fetcher Settings',
            'Smart Content Fetcher',
            'manage_options',
            'smart-content-fetcher',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Register the settings (crawler URL and API Token).
     */
    public function register_settings(): void {
        register_setting('scf_options_group', 'scf_crawler_url', [
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => 'https://smart-crawler-ii.onrender.com/crawl' // Live Production URL
        ]);

        register_setting('scf_options_group', 'scf_api_token', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
    }

    /**
     * Render the Settings Page HTML.
     */
    public function render_settings_page(): void {
        ?>
        <div class="wrap">
            <h1>Smart Content Fetcher Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('scf_options_group'); ?>
                <?php do_settings_sections('scf_options_group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Crawler API URL</th>
                        <td>
                            <input type="text" name="scf_crawler_url" value="<?php echo esc_attr(get_option('scf_crawler_url')); ?>" class="regular-text" />
                            <p class="description">The endpoint of your python/node crawler (e.g., https://api.mysite.com/crawl)</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">API Token (Optional)</th>
                        <td>
                            <input type="password" name="scf_api_token" value="<?php echo esc_attr(get_option('scf_api_token')); ?>" class="regular-text" />
                            <p class="description">If your crawler requires an Authorization header.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register the custom REST API route.
     * Endpoint: /wp-json/scf/v1/fetch
     */
    public function register_routes(): void {
        register_rest_route('scf/v1', '/fetch', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_fetch_request'],
            'permission_callback' => [$this, 'check_permissions'],
            'args'                => [
                'url' => [
                    'required' => true,
                    // We sanitize before validating. 
                    // Note: WP REST API doesn't have a native 'sanitize_callback' that runs BEFORE validation 
                    // in the way we want for this specific check, so we often handle it in the callback 
                    // or relax validation. 
                    
                    // For this interview portfolio, let's relax validation slightly 
                    // and handle the protocol in the main logic function.
                    'validate_callback' => function($param) {
                        return is_string($param) && !empty($param); 
                    }
                ]
            ]
        ]);
    }

    /**
     * Check if user is allowed to access.
     */
    public function check_permissions(): bool {
        // Only allow logged-in users to fetch
        return current_user_can('edit_posts');
    }

    public function handle_fetch_request(WP_REST_Request $request): WP_REST_Response {
        try {
            $target_url = $request->get_param('url');

            // ROBUSTNESS: Auto-prepend https if missing
            if (!preg_match("~^(?:f|ht)tps?://~i", $target_url)) {
                $target_url = "https://" . $target_url;
            }

            // Validate URL
            if (!filter_var($target_url, FILTER_VALIDATE_URL)) {
                return new WP_REST_Response(['message' => 'Invalid URL format'], 400);
            }

            // 1. Get Settings (with default fallback)
            $default_crawler_url = 'https://smart-crawler-ii.onrender.com/crawl';
            $crawler_api_url = get_option('scf_crawler_url', $default_crawler_url);
            
            // Fallback: If it's somehow still empty (e.g. user saved empty string explicitly), revert to default
            if (empty($crawler_api_url)) {
                $crawler_api_url = $default_crawler_url;
            }
            
            $api_token = get_option('scf_api_token');

            // 2. Prepare Request
            $args = [
                'body'    => json_encode(['url' => $target_url]),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 45, // Crawling can take time
            ];

            if (!empty($api_token)) {
                $args['headers']['Authorization'] = 'Bearer ' . $api_token;
            }

            // 3. Make the Remote Request
            $response = wp_remote_post($crawler_api_url, $args);

            // 4. Handle Connection Errors (e.g., DNS, Timeout)
            if (is_wp_error($response)) {
                return new WP_REST_Response([
                    'message' => 'Connection Error: ' . $response->get_error_message()
                ], 500);
            }

            // 5. Handle HTTP Errors (e.g., 404 from Crawler, 500 from Crawler)
            $status_code = wp_remote_retrieve_response_code($response);
            $body        = wp_remote_retrieve_body($response);
            $data        = json_decode($body, true);

            // SAFEGUARD: Ensure data is an array before accessing
            if (!is_array($data)) {
                return new WP_REST_Response([
                    'message' => "Crawler returned invalid data ($status_code)."
                ], 500);
            }

            // ASYNC HANDLING: If we got a 'jobId', we need to poll for the result.
            if (isset($data['jobId'])) {
                $job_id = $data['jobId'];
                $attempts = 0;
                $max_attempts = 15; // 15 * 2s = 30 seconds max wait
                $complete = false;

                // Base URL for jobs. IMPORTANT: Analyzed from user repo: /status/{id}
                // We strip the /crawl endpoint to get the base
                $base_url = str_replace('/crawl', '', $crawler_api_url); 
                // Fallback if user just entered the root URL
                $base_url = rtrim($base_url, '/');
                $job_status_url = "$base_url/status/$job_id";

                // ARGUMENT FIX: Create fresh args for GET request. 
                // We cannot reuse the POST args because they contain a JSON body string,
                // and wp_remote_get might try to process it incorrectly or the server might reject it.
                $job_args = [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'timeout' => 45,
                ];

                if (!empty($api_token)) {
                    $job_args['headers']['Authorization'] = 'Bearer ' . $api_token;
                }

                while ($attempts < $max_attempts && !$complete) {
                    sleep(2); // Wait 2 seconds
                    $attempts++;

                    $job_response = wp_remote_get($job_status_url, $job_args); // Use clean args
                    
                    if (is_wp_error($job_response)) {
                        continue; 
                    }

                    $job_body = wp_remote_retrieve_body($job_response);
                    $job_data = json_decode($job_body, true);

                    // Analyzed Repo Logic:
                    // Response: { status: 'completed', data: { ... } }
                    // OR: { status: 'failed', error: '...' }
                    
                    if (isset($job_data['status']) && $job_data['status'] === 'completed') {
                        // Success!
                        // The actual content is inside 'data'
                        if (isset($job_data['data'])) {
                             $data = $job_data['data'];
                        }
                        $complete = true;
                    } 
                    
                    if (isset($job_data['status']) && $job_data['status'] === 'failed') {
                        $fail_msg = $job_data['error'] ?? 'Unknown crawler error';
                        return new WP_REST_Response(['message' => "Crawler Job Failed: $fail_msg"], 500);
                    }
                }

                if (!$complete) {
                    return new WP_REST_Response(['message' => 'Timeout waiting for crawler job.'], 504);
                }
            }

            if ($status_code !== 200 && !isset($data['jobId'])) {
                $error_msg = $data['error'] ?? 'Unknown Error from Crawler';
                return new WP_REST_Response([
                    'message' => "Crawler Error ($status_code): $error_msg"
                ], $status_code);
            }

            // 6. Return Successful Data
            // Support 'returnvalue' wrapper if BullMQ style
            if (isset($data['returnvalue'])) {
                $data = $data['returnvalue'];
            }

            // FALLBACK SUMMARY LOGIC
            // Smart-Crawler-II returns: { title, metaDescription, items: [ { text, link, image }, ... ] }
            $summary = $data['summary'] ?? null;
            
            if (empty($summary)) {
                // 1. Try 'metaDescription'
                if (!empty($data['metaDescription'])) {
                    $summary = $data['metaDescription'];
                } 
                // 2. Try to build from 'items'
                elseif (!empty($data['items']) && is_array($data['items'])) {
                    $collected_text = [];
                    foreach ($data['items'] as $item) {
                        if (!empty($item['text'])) {
                            $collected_text[] = $item['text'];
                        }
                        if (count($collected_text) >= 5) break; // Limit to first 5 items
                    }
                    if (!empty($collected_text)) {
                        $summary = implode(" ", $collected_text);
                        // Truncate if too long
                        if (mb_strlen($summary) > 300) {
                            $summary = mb_substr($summary, 0, 300) . '...';
                        }
                    }
                }
            }

            // Final fallback
            if (empty($summary)) {
                $summary = 'No summary available.';
            }

            $result = [
                'url'       => $target_url,
                'title'     => $data['title'] ?? 'Title Not Found',
                'summary'   => $summary,
                'timestamp' => time()
            ];

            return new WP_REST_Response($result, 200);

        } catch (\Throwable $e) {
            // CATCH-ALL: This will trap Fatal Errors (PHP 7+) and Exceptions
            return new WP_REST_Response([
                'message' => 'CRITICAL PLUGIN ERROR: ' . $e->getMessage() . ' in line ' . $e->getLine()
            ], 500);
        }
    }
    /**
     * Enqueue Admin Scripts for Meta Box
     */
    public function enqueue_admin_scripts($hook) {
        global $post;

        // Only load on post edit screens
        if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
            wp_enqueue_script(
                'scf-admin-js',
                plugins_url('admin/scf-admin.js', __FILE__),
                ['jquery'],
                '1.0.0',
                true
            );

            wp_enqueue_style(
                'scf-admin-css',
                plugins_url('admin/scf-admin.css', __FILE__),
                [],
                '1.0.0'
            );

            // Pass PHP variables to JS
            wp_localize_script('scf-admin-js', 'scf_vars', [
                'api_url' => get_rest_url(null, 'scf/v1/fetch'),
                'nonce'   => wp_create_nonce('wp_rest')
            ]);
        }
    }

    /**
     * Register Meta Box
     */
    public function register_meta_box() {
        $screens = ['post', 'page']; // Add more post types if needed
        foreach ($screens as $screen) {
            add_meta_box(
                'scf_meta_box',           // ID
                'Smart Content Fetcher',  // Title
                [$this, 'render_meta_box'],    // Callback
                $screen,                  // Screen
                'side',                   // Context (side is good for tools)
                'high'                    // Priority
            );
        }
    }

    /**
     * Render Meta Box HTML
     */
    public function render_meta_box($post) {
        ?>
        <div id="scf-meta-box-container">
            <p class="howto">Enter a URL to fetch content:</p>
            
            <div id="scf-input-group">
                <input type="url" id="scf-admin-url" placeholder="https://example.com/article" style="width:100%;">
            </div>
            <div style="margin-bottom: 10px; text-align: right;">
                 <button type="button" id="scf-admin-fetch-btn" class="button button-primary">Fetch Content</button>
            </div>
    
            <div class="scf-admin-loader">
                <span class="scf-spinner"></span> Fetching... please wait.
            </div>
            
            <div id="scf-admin-error"></div>
    
            <div id="scf-admin-result">
                <img id="scf-preview-image" src="" alt="Preview" style="display:none;">
                <h4 id="scf-preview-title"></h4>
                <div id="scf-preview-summary"></div>
                
                <div id="scf-actions">
                    <button type="button" id="scf-insert-btn" class="button button-secondary">Insert into Post</button>
                    <button type="button" id="scf-copy-btn" class="button button-secondary">Copy to Clipboard</button>
                </div>
            </div>
        </div>
        <?php
    }

}

// Instantiate and run.
$scf_plugin = new Smart_Content_Fetcher();
$scf_plugin->init();