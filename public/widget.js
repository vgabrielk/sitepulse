(function() {
    'use strict';
    
    // Find all SitePulse review widgets on the page
    const widgets = document.querySelectorAll('[data-widget-id]');
    
    widgets.forEach(function(widget) {
        const widgetId = widget.getAttribute('data-widget-id');
        const apiUrl = widget.getAttribute('data-api-url');
        
        if (!widgetId || !apiUrl) return;
        
        // Create iframe to load reviews
        const iframe = document.createElement('iframe');
        iframe.src = apiUrl + '/widget/' + widgetId + '/reviews';
        iframe.style.cssText = 'width: 100%; border: none; background: transparent;';
        iframe.onload = function() {
            // Adjust iframe height based on content
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                iframe.style.height = iframeDoc.body.scrollHeight + 'px';
            } catch (e) {
                // Cross-origin restrictions, use default height
                iframe.style.height = '400px';
            }
        };
        
        // Replace widget content with iframe
        widget.innerHTML = '';
        widget.appendChild(iframe);
    });
})();
