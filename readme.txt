=== Smart Content Fetcher ===
Contributors: yourname
Tags: block, crawler, ai, content
Requires at least: 6.7
Tested up to: 6.9
Stable Tag: 0.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A smart block that fetches and summarizes content from external URLs using your SmartPlaywright Crawler.

== Description ==

Smart Content Fetcher is a bridge between the WordPress Block Editor and your custom web crawler (e.g., SmartPlaywright Crawler). 

**Features:**
*   **Block Editor Integration:** Add the "Smart Content Fetcher" block to any post or page.
*   **Fetcher API:** Enter a URL and fetch a title and summary instantly.
*   **Persistent Storage:** The fetched content is saved directly into your post content, ensuring good SEO and no client-side API calls for your visitors.
*   **Configurable:** Set your Crawler API URL and Token in the settings page.

== Installation ==

1.  Upload the plugin files to the `/wp-content/plugins/smart-content-fetcher` directory, or install the plugin through the WordPress plugins screen directly.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Go to **Settings -> Smart Content Fetcher** and configure your Crawler API URL (and optional Token).
4.  Add the "Smart Content Fetcher" block to a post to start using it.

== Privacy Policy ==

This plugin relies on an external crawling service to function.
*   **Service Used:** Smart Crawler II (Self-hosted on Render by the plugin author).
*   **Data Sent:** When you click "Fetch" in the block editor, the **URL** you entered is sent to the crawler service.
*   **Data Received:** The service returns the page title, a summary, and a thumbnail image (if available).
*   **Data Retention:** The crawler processes the URL in real-time and does not store the content permanently.
*   **Terms of Use:** This service is provided "as is" for the convenience of Smart Content Fetcher users.

== Frequently Asked Questions ==

= Do I need a crawler backend? =

Yes. This plugin is a client. You must have a compatible crawler API (like the SmartPlaywright Crawler) running and accessible from your WordPress server.

== Changelog ==

= 1.0.0 =
*   Initial release.
