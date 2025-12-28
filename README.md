Smart Content Fetcher

Smart Content Fetcher is a WordPress plugin designed to streamline content acquisition by combining editor-native integration with automated web content extraction. The plugin allows editors to fetch, summarise, and embed content from external URLs directly within WordPress using a headless crawler backend.
It supports both the WordPress Block Editor (Gutenberg) and the Classic Editor, ensuring compatibility across modern and legacy workflows.

Features
Editor Integration
Gutenberg Block
A custom React-based block that provides a native interface for fetching and inserting external content.
Classic Editor Support
A dedicated Meta Box compatible with the Classic Editor and popular page builders such as Elementor and Divi.
Headless Crawling
Integrates with an external Node.js service powered by Playwright.
Offloads JavaScript rendering and content extraction to a remote crawler, keeping the WordPress site lightweight and performant.
Content Processing
Automatically extracts metadata and primary content.
Generates concise summaries for editorial review.
Stores fetched content as static HTML within the post for optimal SEO and zero runtime dependencies.
Performance and Reliability
Uses asynchronous requests and polling to handle long-running crawl jobs.
Designed to safely process JavaScript-heavy and single-page applications (SPAs).
Security
Implements WordPress nonce verification.
Enforces capability checks to restrict usage to authorised users.

Architecture Overview
Smart Content Fetcher follows a microservices-oriented architecture:
WordPress Plugin (PHP / React)
Frontend: React-based Gutenberg block.
Backend: PHP REST API endpoints acting as a secure proxy between WordPress and the crawler service.
Crawler Service (Node.js)
External Express application running Playwright.
Responsible for rendering pages, extracting readable content, and returning structured results.

Installation
Download the latest smart-content-fetcher.zip from the releases page.
Upload the ZIP file via Plugins → Add New → Upload Plugin in the WordPress admin dashboard.
Activate the plugin.
Navigate to Settings → Smart Content Fetcher.
Configure the Crawler API URL and, if required, an API token.

Usage
Gutenberg (Block Editor)
Open or create a post or page.
Insert the Smart Content Fetcher block.
Enter a valid URL and click Fetch.
Review the extracted summary and content before publishing.
Classic Editor and Page Builders
Open the editor interface.
Locate the Smart Content Fetcher meta box in the sidebar.
Enter a URL and click Fetch.
Insert the content into the editor or copy it to the clipboard, depending on the editor in use.


Why This Plugin Exists
Managing external content inside WordPress often requires manual copying, third-party tools, or unreliable scraping plugins that struggle with JavaScript-heavy websites. This process is time-consuming, error-prone, and difficult to scale for editorial teams.
Smart Content Fetcher was built to solve this problem by providing a reliable, editor-native solution for importing external content directly into WordPress. By separating content rendering and extraction into a dedicated headless crawler service, the plugin ensures accurate results while keeping WordPress performant and secure.
The goal of this project is to demonstrate a clean separation of concerns between WordPress, modern JavaScript tooling, and backend automation, while improving editorial productivity.

Use Cases
Smart Content Fetcher is suitable for a wide range of editorial and content workflows, including:
Editorial Research
 Quickly fetch and summarize articles from external sources for review, rewriting, or citation.


Content Aggregation
 Import structured content from multiple sources into WordPress while retaining full control over formatting and SEO.


JavaScript-Heavy Websites
 Reliably extract content from SPAs and dynamically rendered pages that traditional scrapers cannot handle.


Internal Knowledge Bases
 Archive external documentation or articles as static content within WordPress for long-term reference.


Headless and Microservice Demonstrations
 Showcase modern WordPress development practices by integrating PHP, React, Node.js, and Playwright in a real-world system.
Development
This project follows modern WordPress development best practices.
Requirements
Node.js and npm
Composer (optional, for PHP tooling)
Available Commands
# Install JavaScript dependencies
npm install

# Start development server with hot reload
npm start

# Build production assets
npm run build

# Package the plugin into a distributable ZIP
sh package.sh


Privacy and Permissions
URLs are sent to the configured external crawler service for processing.
No fetched content is permanently stored on the crawler.
Only users with the edit_posts capability can initiate content fetching.


Metadata
Version: 1.0.0
License: GPL v2 or later

