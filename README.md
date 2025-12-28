# Smart Content Fetcher

**Smart Content Fetcher** is a robust WordPress plugin engineered to bridge the gap between content management and automated web scraping. It integrates directly with the WordPress Block Editor (Gutenberg) and Classic Editor to fetch, summarize, and import content from external URLs using a headless crawler backend.

## 🚀 Features

*   **Seamless Editor Integration**:
    *   **Gutenberg Block**: A custom-built React block that provides a native "Fetch" experience.
    *   **Legacy Support**: A dedicated Meta Box for Classic Editor and Page Builders (Elementor, Divi, etc.).
*   **Headless Crawler Integration**: Connects to a remote Node.js/Playwright service to handle heavy-duty rendering and scraping, keeping your WordPress site fast.
*   **Intelligent Summarization**: Automatically parses metadata and content to generate concise summaries.
*   **Persistent Storage**: Fetched content is saved as static HTML within the post, ensuring perfect SEO and zero external dependencies at runtime.
*   **Asynchronous Processing**: Handles long-running crawl jobs via a robust polling mechanism.
*   **Security**: Includes nonce verification and capability checks to prevent unauthorized access.

## 🛠 Architecture

This plugin operates as a client-side interface for a microservices architecture:

1.  **WordPress Plugin (PHP/React)**:
    *   **Frontend**: React-based Gutenberg block.
    *   **Backend**: PHP REST API endpoints that act as a secure proxy.
2.  **Crawler Service (Node.js)**:
    *   An external Express application running Playwright.
    *   Handles the complexity of rendering JavaScript-heavy sites (SPA) to extract readable content.

## ⚙️ Installation

1.  Download the latest `smart-content-fetcher.zip` from the releases.
2.  Upload it to your WordPress site via **Plugins > Add New > Upload Plugin**.
3.  Activate the plugin.
4.  Navigate to **Settings > Smart Content Fetcher**.
5.  Configure your **Crawler API URL** (Default provided) and optional API Token.

## 📖 Usage

### Block Editor (Gutenberg)
1.  Open a Post or Page.
2.  Add the **Smart Content Fetcher** block.
3.  Enter a URL and click **Fetch**.
4.  Review the summary and basic details.

### Classic Editor / Elementor
1.  Open the editor.
2.  Locate the **Smart Content Fetcher** meta box in the sidebar.
3.  Enter a URL and click **Fetch**.
4.  Use the **Insert into Post** (Classic) or **Copy to Clipboard** (Elementor) buttons to use the content.

## 💻 Development

This project adheres to modern WordPress development standards.

### Prerequisites
*   Node.js & NPM
*   Composer (optional for PHP tooling)

### Build Commands

```bash
# Install dependencies
npm install

# Start development server (hot reload)
npm start

# Build production assets
npm run build

# Create distributable ZIP file
sh package.sh
```

## 🔒 Privacy & Permissions

*   The plugin sends URLs to the configured external crawler.
*   No content is permanently stored on the crawler; it is a pass-through service.
*   Only users with `edit_posts` capability can trigger the fetcher.

---

**Version**: 1.0.0
**License**: GPLv2 or later
