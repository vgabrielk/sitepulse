/**
 * SitePulse Widget
 * Version: 1.0.0
 * 
 * This script provides widget rendering and basic event tracking.
 * It automatically tracks page views, user interactions, and collects feedback.
 */

(function() {
    'use strict';

    // Configuration
    window.SitePulse = window.SitePulse || {};
    window.SitePulse.config = window.SitePulse.config || {};
    window.SitePulse.events = window.SitePulse.events || [];
    window.SitePulse.session = window.SitePulse.session || null;
    window.SitePulse.visit = window.SitePulse.visit || null;

    // Default configuration
    const defaultConfig = {
        apiUrl: '/api/widget',
        siteId: null,
        sessionToken: null,
        tracking: {
            pageviews: true,
            events: true,
            scroll: true,
            clicks: true,
            forms: true,
            sessions: true,
            anonymizeIps: true,
            respectDoNotTrack: true,
            cookieConsent: true
        },
        widget: {
            position: 'bottom-right',
            theme: 'light',
            colors: {
                primary: '#007bff',
                secondary: '#6c757d',
                background: '#ffffff',
                text: '#333333'
            },
            showCounter: true,
            showFeedback: true,
            showSurveys: true,
            animation: 'slide',
            size: 'medium'
        }
    };

    // Merge configuration
    window.SitePulse.config = Object.assign({}, defaultConfig, window.SitePulse.config);

    // Utility functions
    const utils = {
        generateId: function() {
            return 'sp_' + Math.random().toString(36).substr(2, 9);
        },

        getSelector: function(element) {
            if (element.id) return '#' + element.id;
            if (element.className) return '.' + element.className.split(' ')[0];
            return element.tagName.toLowerCase();
        },

        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        throttle: function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        getDeviceInfo: function() {
            const ua = navigator.userAgent;
            return {
                browser: this.getBrowser(ua),
                os: this.getOS(ua),
                deviceType: this.getDeviceType(ua),
                screen: {
                    width: screen.width,
                    height: screen.height
                },
                viewport: {
                    width: window.innerWidth,
                    height: window.innerHeight
                }
            };
        },

        getBrowser: function(ua) {
            if (ua.includes('Chrome')) return 'Chrome';
            if (ua.includes('Firefox')) return 'Firefox';
            if (ua.includes('Safari')) return 'Safari';
            if (ua.includes('Edge')) return 'Edge';
            return 'Unknown';
        },

        getOS: function(ua) {
            if (ua.includes('Windows')) return 'Windows';
            if (ua.includes('Mac')) return 'macOS';
            if (ua.includes('Linux')) return 'Linux';
            if (ua.includes('Android')) return 'Android';
            if (ua.includes('iOS')) return 'iOS';
            return 'Unknown';
        },

        getDeviceType: function(ua) {
            if (ua.includes('Mobile')) return 'mobile';
            if (ua.includes('Tablet')) return 'tablet';
            return 'desktop';
        }
    };

    // Event tracking
    const tracker = {
        track: function(eventType, data = {}) {
            const event = {
                type: eventType,
                data: Object.assign({}, data, {
                    timestamp: Date.now(),
                    url: window.location.href,
                    title: document.title,
                    referrer: document.referrer
                })
            };

            window.SitePulse.events.push(event);
            this.sendEvents();
        },

        sendEvents: function() {
            if (window.SitePulse.events.length === 0) return;

            const events = window.SitePulse.events.splice(0);
            
            fetch(window.SitePulse.config.apiUrl + '/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Session-Token': window.SitePulse.config.sessionToken
                },
                body: JSON.stringify({
                    site_id: window.SitePulse.config.siteId,
                    events: events
                })
            }).catch(error => {
                console.warn('SitePulse: Failed to send events', error);
                // Re-add events to queue for retry
                window.SitePulse.events.unshift(...events);
            });
        },

        startSession: function() {
            if (window.SitePulse.session) return;

            const sessionData = {
                ip_address: null, // Will be determined server-side
                user_agent: navigator.userAgent,
                referrer: document.referrer,
                device_info: utils.getDeviceInfo(),
                utm_source: this.getUrlParameter('utm_source'),
                utm_medium: this.getUrlParameter('utm_medium'),
                utm_campaign: this.getUrlParameter('utm_campaign')
            };

            fetch(window.SitePulse.config.apiUrl + '/session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    site_id: window.SitePulse.config.siteId,
                    session_data: sessionData
                })
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.SitePulse.session = data.data;
                    window.SitePulse.config.sessionToken = data.data.session_token;
                }
            }).catch(error => {
                console.warn('SitePulse: Failed to start session', error);
            });
        },

        startVisit: function() {
            if (window.SitePulse.visit) return;

            const visitData = {
                url: window.location.href,
                title: document.title,
                path: window.location.pathname,
                query: window.location.search,
                hash: window.location.hash
            };

            fetch(window.SitePulse.config.apiUrl + '/visit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Session-Token': window.SitePulse.config.sessionToken
                },
                body: JSON.stringify({
                    site_id: window.SitePulse.config.siteId,
                    visit_data: visitData
                })
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.SitePulse.visit = data.data;
                }
            }).catch(error => {
                console.warn('SitePulse: Failed to start visit', error);
            });
        },

        getUrlParameter: function(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }
    };

    // Page tracking
    const pageTracker = {
        init: function() {
            if (!window.SitePulse.config.tracking.pageviews) return;

            this.trackPageView();
            this.trackPageExit();
        },

        trackPageView: function() {
            tracker.track('pageview', {
                url: window.location.href,
                title: document.title,
                referrer: document.referrer,
                timestamp: Date.now()
            });
        },

        trackPageExit: function() {
            window.addEventListener('beforeunload', () => {
                tracker.track('page_exit', {
                    time_on_page: Date.now() - (window.SitePulse.visit?.start_time || Date.now())
                });
            });
        }
    };

    // Event tracking
    const eventTracker = {
        init: function() {
            if (!window.SitePulse.config.tracking.events) return;

            this.trackClicks();
            this.trackScroll();
            this.trackForms();
        },

        trackClicks: function() {
            if (!window.SitePulse.config.tracking.clicks) return;

            document.addEventListener('click', (e) => {
                tracker.track('click', {
                    element: e.target.tagName,
                    text: e.target.textContent?.substring(0, 100),
                    selector: utils.getSelector(e.target),
                    coordinates: { x: e.clientX, y: e.clientY },
                    href: e.target.href || null
                });
            });
        },

        trackScroll: function() {
            if (!window.SitePulse.config.tracking.scroll) return;

            let maxScroll = 0;
            const trackScroll = utils.throttle(() => {
                const scrollDepth = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
                maxScroll = Math.max(maxScroll, scrollDepth);
                
                tracker.track('scroll', {
                    depth: scrollDepth,
                    max_depth: maxScroll,
                    position: window.scrollY
                });
            }, 100);

            window.addEventListener('scroll', trackScroll);
        },

        trackForms: function() {
            if (!window.SitePulse.config.tracking.forms) return;

            // Form focus/blur
            document.addEventListener('focus', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    tracker.track('form_focus', {
                        element: e.target.name || e.target.id,
                        type: e.target.type
                    });
                }
            }, true);

            document.addEventListener('blur', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    tracker.track('form_blur', {
                        element: e.target.name || e.target.id,
                        type: e.target.type
                    });
                }
            }, true);

            // Form submit
            document.addEventListener('submit', (e) => {
                tracker.track('form_submit', {
                    form: e.target.id || e.target.className,
                    action: e.target.action
                });
            });
        }
    };

    // Widget UI
    const widget = {
        init: function() {
            this.createWidget();
            this.startSending();
        },

        createWidget: function() {
            const widgetEl = document.createElement('div');
            widgetEl.id = 'sitepulse-widget';
            widgetEl.className = 'sitepulse-widget';
            
            // Apply styles
            Object.assign(widgetEl.style, this.getWidgetStyles());
            
            // Add content
            if (window.SitePulse.config.widget.showCounter) {
                const counter = document.createElement('div');
                counter.className = 'sp-counter';
                counter.textContent = '0';
                widgetEl.appendChild(counter);
            }
            
            if (window.SitePulse.config.widget.showFeedback) {
                const feedbackBtn = document.createElement('div');
                feedbackBtn.className = 'sp-feedback-btn';
                feedbackBtn.textContent = '💬';
                feedbackBtn.onclick = this.showFeedback;
                widgetEl.appendChild(feedbackBtn);
            }
            
            document.body.appendChild(widgetEl);
        },

        getWidgetStyles: function() {
            const config = window.SitePulse.config.widget;
            const position = config.position || 'bottom-right';
            
            const styles = {
                position: 'fixed',
                zIndex: '9999',
                padding: '10px',
                borderRadius: '8px',
                boxShadow: '0 2px 10px rgba(0,0,0,0.1)',
                fontFamily: 'Arial, sans-serif',
                fontSize: '14px',
                cursor: 'pointer',
                transition: 'all 0.3s ease'
            };
            
            // Position
            if (position.includes('bottom')) styles.bottom = '20px';
            if (position.includes('top')) styles.top = '20px';
            if (position.includes('right')) styles.right = '20px';
            if (position.includes('left')) styles.left = '20px';
            
            // Colors
            if (config.colors) {
                styles.backgroundColor = config.colors.background || '#ffffff';
                styles.color = config.colors.text || '#333333';
                styles.border = `2px solid ${config.colors.primary || '#007bff'}`;
            }
            
            return styles;
        },

        showFeedback: function() {
            // Create feedback modal
            const modal = document.createElement('div');
            modal.className = 'sp-feedback-modal';
            modal.innerHTML = `
                <div class="sp-modal-content">
                    <div class="sp-modal-header">
                        <h3>Share Your Feedback</h3>
                        <button class="sp-close-btn">&times;</button>
                    </div>
                    <div class="sp-modal-body">
                        <div class="sp-rating">
                            <label>Rating:</label>
                            <div class="sp-stars">
                                ${[1,2,3,4,5].map(i => `<span class="sp-star" data-rating="${i}">★</span>`).join('')}
                            </div>
                        </div>
                        <div class="sp-comment">
                            <label>Comment (optional):</label>
                            <textarea placeholder="Tell us what you think..."></textarea>
                        </div>
                        <div class="sp-name">
                            <label>Name (optional):</label>
                            <input type="text" placeholder="Your name">
                        </div>
                    </div>
                    <div class="sp-modal-footer">
                        <button class="sp-submit-btn">Submit</button>
                        <button class="sp-cancel-btn">Cancel</button>
                    </div>
                </div>
            `;
            
            // Apply modal styles
            Object.assign(modal.style, {
                position: 'fixed',
                top: '0',
                left: '0',
                width: '100%',
                height: '100%',
                backgroundColor: 'rgba(0,0,0,0.5)',
                zIndex: '10000',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center'
            });
            
            document.body.appendChild(modal);
            
            // Handle interactions
            modal.querySelector('.sp-close-btn').onclick = () => modal.remove();
            modal.querySelector('.sp-cancel-btn').onclick = () => modal.remove();
            modal.onclick = (e) => {
                if (e.target === modal) modal.remove();
            };
            
            // Star rating
            modal.querySelectorAll('.sp-star').forEach((star, index) => {
                star.onclick = () => {
                    modal.querySelectorAll('.sp-star').forEach((s, i) => {
                        s.style.color = i <= index ? '#ffc107' : '#ddd';
                    });
                    modal.dataset.rating = index + 1;
                };
            });
            
            // Submit feedback
            modal.querySelector('.sp-submit-btn').onclick = () => {
                const rating = modal.dataset.rating;
                const comment = modal.querySelector('textarea').value;
                const name = modal.querySelector('input').value;
                
                if (!rating) {
                    alert('Please select a rating');
                    return;
                }
                
                fetch(window.SitePulse.config.apiUrl + '/review', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Session-Token': window.SitePulse.config.sessionToken
                    },
                    body: JSON.stringify({
                        site_id: window.SitePulse.config.siteId,
                        rating: parseInt(rating),
                        comment: comment,
                        visitor_name: name
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Thank you for your feedback!');
                        modal.remove();
                    } else {
                        alert('Failed to submit feedback');
                    }
                }).catch(error => {
                    console.error('SitePulse: Failed to submit feedback', error);
                    alert('Failed to submit feedback');
                });
            };
        },

        startSending: function() {
            // Send events every 5 seconds
            setInterval(() => {
                tracker.sendEvents();
            }, 5000);
            
            // Send events on page unload
            window.addEventListener('beforeunload', () => {
                tracker.sendEvents();
            });
        }
    };

    // Initialize SitePulse
    function init() {
        // Check if Do Not Track is enabled
        if (window.SitePulse.config.tracking.respectDoNotTrack && navigator.doNotTrack === '1') {
            return;
        }
        
        // Start session and visit tracking
        tracker.startSession();
        tracker.startVisit();
        
        // Initialize tracking
        pageTracker.init();
        eventTracker.init();
        
        // Initialize widget
        widget.init();
        
        console.log('SitePulse Widget initialized');
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose public API
    window.SitePulse.track = tracker.track.bind(tracker);
    window.SitePulse.showFeedback = widget.showFeedback.bind(widget);

})();
