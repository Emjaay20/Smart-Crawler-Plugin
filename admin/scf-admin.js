jQuery(document).ready(function ($) {
    const $container = $('#scf-meta-box-container');
    const $fetchBtn = $('#scf-admin-fetch-btn');
    const $urlInput = $('#scf-admin-url');
    const $resultArea = $('#scf-admin-result');
    const $errorArea = $('#scf-admin-error');
    const $loader = $('.scf-admin-loader');

    // Actions
    const $insertBtn = $('#scf-insert-btn');
    const $copyBtn = $('#scf-copy-btn');

    // Hidden storage for content
    let currentFetchedContent = '';

    // Handle Enter Key
    $urlInput.on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $fetchBtn.click();
        }
    });

    // Handle Fetch
    $fetchBtn.on('click', function (e) {
        e.preventDefault();

        const url = $urlInput.val().trim();
        if (!url) {
            showError('Please enter a valid URL.');
            return;
        }

        // Reset UI
        $errorArea.hide().text('');
        $resultArea.hide();
        $loader.show();
        $fetchBtn.prop('disabled', true);

        // Make the call
        $.ajax({
            url: scf_vars.api_url,
            method: 'POST',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', scf_vars.nonce);
            },
            data: {
                url: url
            },
            success: function (response) {
                $loader.hide();
                $fetchBtn.prop('disabled', false);

                if (response.title) {
                    renderResult(response);
                } else {
                    showError('No data found for this URL.');
                }
            },
            error: function (xhr, status, error) {
                $loader.hide();
                $fetchBtn.prop('disabled', false);

                let msg = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showError(msg);
            }
        });
    });

    // Helper: Show Error
    function showError(msg) {
        $errorArea.text(msg).show();
    }

    // Helper: Render Result
    function renderResult(data) {
        // Build the HTML to display AND insert
        // Note: This needs to match the block's output structure roughly if possible, 
        // or be a clean HTML snippet.

        const title = data.title || 'No Title';
        const summary = data.summary || '';
        const image = data.image || '';

        // Update Preview DOM
        $('#scf-preview-title').text(title);
        $('#scf-preview-summary').text(summary);
        if (image) {
            $('#scf-preview-image').attr('src', image).show();
        } else {
            $('#scf-preview-image').hide();
        }

        // Generate HTML for Insertion
        // Using a standard implementation that looks good in Classic/Elementor
        let html = '<div class="smart-content-fetcher-card" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin: 20px 0;">';
        if (image) {
            html += '<img src="' + image + '" alt="' + title + '" style="max-width: 100%; height: auto; margin-bottom: 15px; border-radius: 4px;">';
        }
        html += '<h3 style="margin-top: 0;">' + title + '</h3>';
        html += '<p>' + summary + '</p>';
        html += '<a href="' + $urlInput.val() + '" target="_blank" rel="noopener noreferrer">Read Original</a>';
        html += '</div>';

        currentFetchedContent = html;

        $resultArea.fadeIn();
    }

    // Handle Insert (Classic Editor)
    $insertBtn.on('click', function () {
        if (!currentFetchedContent) return;

        if (typeof send_to_editor === 'function') {
            send_to_editor(currentFetchedContent);
        } else {
            // Fallback for weird environments
            showError('Could not insert automatically. Please Copy instead.');
        }
    });

    // Handle Copy (Elementor / General)
    $copyBtn.on('click', function () {
        if (!currentFetchedContent) return;

        navigator.clipboard.writeText(currentFetchedContent).then(function () {
            const originalText = $copyBtn.text();
            $copyBtn.text('Copied!');
            setTimeout(() => {
                $copyBtn.text(originalText);
            }, 2000);
        }, function (err) {
            showError('Could not copy text.');
        });
    });

});
