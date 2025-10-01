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
        
        // Apply customizations if available
        const customizationData = widget.getAttribute('data-customization');
        if (customizationData) {
            try {
                const customization = JSON.parse(customizationData);
                applyCustomizations(widget, customization);
            } catch (e) {
                console.warn('Failed to parse customization data:', e);
            }
        }
        
        // Create iframe to load reviews
        const iframe = document.createElement('iframe');
        iframe.src = apiUrl + '/widget/' + widgetId + '/reviews';
        iframe.style.cssText = 'width: 100%; border: none; background: transparent; display: none;';
        
        iframe.onload = function() {
            // Hide loading and show iframe
            loadingDiv.style.display = 'none';
            iframe.style.display = 'block';
            
            // Apply customizations to iframe content if possible
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                if (iframeDoc) {
                    // Apply customizations to iframe content
                    const customizationData = widget.getAttribute('data-customization');
                    if (customizationData) {
                        try {
                            const customization = JSON.parse(customizationData);
                            applyCustomizationsToIframe(iframeDoc, customization);
                        } catch (e) {
                            console.warn('Failed to apply customizations to iframe:', e);
                        }
                    }
                }
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
    
    // Function to apply customizations
    function applyCustomizations(widget, customization) {
        const colors = customization.colors || {};
        const typography = customization.typography || {};
        const layout = customization.layout || {};
        const effects = customization.effects || {};
        
        // Apply base styles to widget
        widget.style.cssText += `
            font-family: ${typography.font_family || 'inherit'};
            font-size: ${typography.font_size || '14px'};
            font-weight: ${typography.font_weight || 'normal'};
            max-width: ${layout.max_width || '600px'};
            margin: ${layout.margin || '10px 0'};
            animation: ${effects.animation || 'fadeIn 0.3s ease'};
        `;
        
        // Add CSS for child elements
        const style = document.createElement('style');
        style.textContent = `
            #sitepulse-reviews .reviews-container {
                background: ${colors.background || '#ffffff'};
                border-radius: ${layout.border_radius || '8px'};
                padding: ${layout.padding || '16px'};
                box-shadow: ${effects.box_shadow || '0 2px 8px rgba(0,0,0,0.1)'};
            }
            
            #sitepulse-reviews .review-item {
                background: ${colors.background || '#ffffff'};
                border-radius: ${layout.border_radius || '8px'};
                box-shadow: ${effects.box_shadow || '0 2px 8px rgba(0,0,0,0.1)'};
                border-left-color: ${colors.primary || '#007bff'};
            }
            
            #sitepulse-reviews .review-item:hover {
                box-shadow: ${effects.hover_shadow || '0 4px 12px rgba(0,0,0,0.15)'};
            }
            
            #sitepulse-reviews .review-form-toggle,
            #sitepulse-reviews .submit-btn {
                background: ${colors.primary || '#007bff'};
                color: white;
                border-radius: ${layout.border_radius || '8px'};
            }
            
            #sitepulse-reviews .star-label.active {
                color: ${colors.accent || '#ffc107'};
            }
            
            #sitepulse-reviews .reviews-title {
                color: ${colors.text || '#333333'};
            }
            
            #sitepulse-reviews .review-comment {
                color: ${colors.text || '#333333'};
            }
            
            #sitepulse-reviews .review-name {
                color: ${colors.text || '#333333'};
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        
        document.head.appendChild(style);
    }
    
    // Function to apply customizations to iframe content
    function applyCustomizationsToIframe(iframeDoc, customization) {
        const colors = customization.colors || {};
        const typography = customization.typography || {};
        const layout = customization.layout || {};
        const effects = customization.effects || {};
        
        // Create style element for iframe
        const style = iframeDoc.createElement('style');
        style.textContent = `
            .reviews-container {
                background: ${colors.background || '#ffffff'} !important;
                border-radius: ${layout.border_radius || '8px'} !important;
                padding: ${layout.padding || '16px'} !important;
                box-shadow: ${effects.box_shadow || '0 2px 8px rgba(0,0,0,0.1)'} !important;
            }
            
            .review-item {
                background: ${colors.background || '#ffffff'} !important;
                border-radius: ${layout.border_radius || '8px'} !important;
                box-shadow: ${effects.box_shadow || '0 2px 8px rgba(0,0,0,0.1)'} !important;
                border-left-color: ${colors.primary || '#007bff'} !important;
            }
            
            .review-item:hover {
                box-shadow: ${effects.hover_shadow || '0 4px 12px rgba(0,0,0,0.15)'} !important;
            }
            
            .review-form-toggle,
            .submit-btn {
                background: ${colors.primary || '#007bff'} !important;
                color: white !important;
                border-radius: ${layout.border_radius || '8px'} !important;
            }
            
            .star-label.active {
                color: ${colors.accent || '#ffc107'} !important;
            }
            
            .reviews-title {
                color: ${colors.text || '#333333'} !important;
            }
            
            .review-comment {
                color: ${colors.text || '#333333'} !important;
            }
            
            .review-name {
                color: ${colors.text || '#333333'} !important;
            }
            
            body {
                font-family: ${typography.font_family || 'inherit'} !important;
                font-size: ${typography.font_size || '14px'} !important;
                font-weight: ${typography.font_weight || 'normal'} !important;
            }
            
            .reviews-container {
                max-width: ${layout.max_width || '600px'} !important;
                margin: ${layout.margin || '10px 0'} !important;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes bounceIn {
                0% { opacity: 0; transform: scale(0.3); }
                50% { opacity: 1; transform: scale(1.05); }
                70% { transform: scale(0.9); }
                100% { opacity: 1; transform: scale(1); }
            }
        `;
        
        iframeDoc.head.appendChild(style);
    }
})();