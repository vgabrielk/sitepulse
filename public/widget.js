(function() {
    'use strict';
    
    // Find all SitePulse review widgets on the page
    const widgets = document.querySelectorAll('[data-widget-id]');
    
    widgets.forEach(function(widget) {
        const widgetId = widget.getAttribute('data-widget-id');
        const apiUrl = widget.getAttribute('data-api-url');
        
        if (!widgetId || !apiUrl) return;
        
        // Create loading spinner
        const loadingDiv = document.createElement('div');
        loadingDiv.style.cssText = `
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 12px;
            margin: 10px 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        `;
        
        loadingDiv.innerHTML = `
            <div style="
                width: 40px;
                height: 40px;
                border: 4px solid #e3e3e3;
                border-top: 4px solid #007bff;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin-bottom: 16px;
            "></div>
            <div style="color: #666; font-size: 14px;">Loading reviews...</div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;
        
        // Show loading initially
        widget.innerHTML = '';
        widget.appendChild(loadingDiv);
        
        // Create iframe to load reviews
        const iframe = document.createElement('iframe');
        iframe.src = apiUrl + '/widget/' + widgetId + '/reviews';
        iframe.style.cssText = 'width: 100%; border: none; background: transparent; display: none;';
        
        iframe.onload = function() {
            // Hide loading and show iframe
            loadingDiv.style.display = 'none';
            iframe.style.display = 'block';
            
            // Adjust iframe height based on content
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                iframe.style.height = iframeDoc.body.scrollHeight + 'px';
            } catch (e) {
                // Cross-origin restrictions, use default height
                iframe.style.height = '100vh';
            }
        };
        
        iframe.onerror = function() {
            // Show error message if iframe fails to load
            loadingDiv.innerHTML = `
                <div style="color: #dc3545; font-size: 14px; text-align: center;">
                    <div style="margin-bottom: 8px;">⚠️</div>
                    <div>Unable to load reviews</div>
                </div>
            `;
        };
        
        // Add iframe to widget
        widget.appendChild(iframe);
    });
})();