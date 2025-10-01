(function() {
    'use strict';
    
    // SitePulse Analytics Widget
    var SitePulse = {
        siteId: {{ $site->id }},
        widgetId: '{{ $site->widget_id }}',
        apiUrl: '{{ url("/api/widget") }}',
        sessionToken: null,
        events: [],
        isTracking: true,
        
        init: function() {
            this.checkSessionExpiration();
            this.generateSessionToken();
            this.trackPageView();
            this.setupEventListeners();
            this.startEventBatching();
        },
        
        checkSessionExpiration: function() {
            var expires = localStorage.getItem('sitepulse_session_expires_' + this.siteId);
            if (expires && Date.now() > parseInt(expires)) {
                // Session expired, clear localStorage
                localStorage.removeItem('sitepulse_session_' + this.siteId);
                localStorage.removeItem('sitepulse_session_expires_' + this.siteId);
                localStorage.removeItem('sitepulse_last_pageview_' + this.siteId);
                console.log('SitePulse: Session expired, starting new session');
            }
        },
        
        generateSessionToken: function() {
            // Try to get existing session token from localStorage
            var existingToken = localStorage.getItem('sitepulse_session_' + this.siteId);
            if (existingToken) {
                this.sessionToken = existingToken;
            } else {
                // Generate new session token
                this.sessionToken = 'sp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                // Store in localStorage for 30 minutes
                localStorage.setItem('sitepulse_session_' + this.siteId, this.sessionToken);
                // Set expiration
                localStorage.setItem('sitepulse_session_expires_' + this.siteId, Date.now() + (30 * 60 * 1000));
            }
        },
        
        trackPageView: function() {
            // Check if we already tracked this pageview recently
            var lastPageview = localStorage.getItem('sitepulse_last_pageview_' + this.siteId);
            var currentUrl = window.location.href;
            var now = Date.now();
            
            // If we have a recent pageview for the same URL (within 5 minutes), skip
            if (lastPageview) {
                var lastData = JSON.parse(lastPageview);
                if (lastData.url === currentUrl && (now - lastData.timestamp) < (5 * 60 * 1000)) {
                    console.log('SitePulse: Skipping duplicate pageview for', currentUrl);
                    return;
                }
            }
            
            var event = {
                type: 'pageview',
                data: {
                    url: currentUrl,
                    title: document.title,
                    referrer: document.referrer,
                    timestamp: now
                },
                timestamp: now
            };
            
            // Store this pageview info
            localStorage.setItem('sitepulse_last_pageview_' + this.siteId, JSON.stringify({
                url: currentUrl,
                timestamp: now
            }));
            
            this.events.push(event);
        },
        
        setupEventListeners: function() {
            var self = this;
            
            // Track clicks
            document.addEventListener('click', function(e) {
                if (!self.isTracking) return;
                
                var event = {
                    type: 'click',
                    data: {
                        selector: self.getElementSelector(e.target),
                        text: e.target.textContent?.trim().substring(0, 100) || '',
                        element: e.target.tagName.toLowerCase(),
                        coordinates: {
                            x: e.clientX,
                            y: e.clientY
                        },
                        url: window.location.href,
                        timestamp: Date.now()
                    },
                    timestamp: Date.now()
                };
                
                self.events.push(event);
            });
            
            // Track scroll events
            var scrollTimeout;
            window.addEventListener('scroll', function() {
                if (!self.isTracking) return;
                
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    var event = {
                        type: 'scroll',
                        data: {
                            scrollY: window.scrollY,
                            scrollX: window.scrollX,
                            scrollPercent: Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100),
                            url: window.location.href,
                            timestamp: Date.now()
                        },
                        timestamp: Date.now()
                    };
                    
                    self.events.push(event);
                }, 100);
            });
            
            // Track form submissions
            document.addEventListener('submit', function(e) {
                if (!self.isTracking) return;
                
                var event = {
                    type: 'form_submit',
                    data: {
                        formId: e.target.id || null,
                        formClass: e.target.className || null,
                        action: e.target.action || null,
                        method: e.target.method || 'get',
                        url: window.location.href,
                        timestamp: Date.now()
                    },
                    timestamp: Date.now()
                };
                
                self.events.push(event);
            });
            
            // Track page visibility changes
            document.addEventListener('visibilitychange', function() {
                if (!self.isTracking) return;
                
                var event = {
                    type: 'visibility_change',
                    data: {
                        hidden: document.hidden,
                        url: window.location.href,
                        timestamp: Date.now()
                    },
                    timestamp: Date.now()
                };
                
                self.events.push(event);
            });
        },
        
        getElementSelector: function(element) {
            if (element.id) {
                return '#' + element.id;
            }
            
            if (element.className) {
                return '.' + element.className.split(' ')[0];
            }
            
            var path = [];
            while (element && element.nodeType === Node.ELEMENT_NODE) {
                var selector = element.nodeName.toLowerCase();
                if (element.id) {
                    selector += '#' + element.id;
                    path.unshift(selector);
                    break;
                } else {
                    var sibling = element;
                    var nth = 1;
                    while (sibling = sibling.previousElementSibling) {
                        if (sibling.nodeName.toLowerCase() === selector) {
                            nth++;
                        }
                    }
                    if (nth !== 1) {
                        selector += ':nth-of-type(' + nth + ')';
                    }
                }
                path.unshift(selector);
                element = element.parentElement;
            }
            
            return path.join(' > ');
        },
        
        startEventBatching: function() {
            var self = this;
            
            // Send events every 5 seconds or when 10 events are queued
            setInterval(function() {
                if (self.events.length > 0) {
                    self.sendEvents();
                }
            }, 5000);
            
            // Send events when page is about to unload
            window.addEventListener('beforeunload', function() {
                if (self.events.length > 0) {
                    self.sendEvents(true); // Send synchronously
                }
            });
        },
        
        sendEvents: function(sync) {
            if (this.events.length === 0) return;
            
            var eventsToSend = this.events.splice(0, 10); // Send max 10 events at a time
            var self = this;
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', this.apiUrl + '/events', !sync);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-Session-Token', this.sessionToken);
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log('SitePulse: Events sent successfully');
                    } else {
                        console.error('SitePulse: Failed to send events', xhr.status);
                        // Re-add events to queue if failed
                        self.events.unshift.apply(self.events, eventsToSend);
                    }
                }
            };
            
            var payload = {
                site_id: this.siteId,
                events: eventsToSend
            };
            
            xhr.send(JSON.stringify(payload));
        }
    };
    
    // Reviews functionality
    SitePulse.reviews = {
        show: function() {
            var iframe = document.createElement('iframe');
            iframe.src = this.apiUrl.replace('/api', '') + '/widget/{{ $site->widget_id }}/reviews';
            iframe.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;border:none;z-index:999999;background:rgba(0,0,0,0.5);';
            iframe.onload = function() {
                iframe.style.background = 'transparent';
            };
            
            var closeBtn = document.createElement('div');
            closeBtn.innerHTML = '×';
            closeBtn.style.cssText = 'position:fixed;top:20px;right:20px;width:40px;height:40px;background:#e74c3c;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:1000000;font-size:20px;font-weight:bold;';
            closeBtn.onclick = function() {
                document.body.removeChild(iframe);
                document.body.removeChild(closeBtn);
            };
            
            document.body.appendChild(iframe);
            document.body.appendChild(closeBtn);
        }
    };
    
    // Add reviews button to page
    SitePulse.addReviewsButton = function() {
        if (document.getElementById('sitepulse-reviews-btn')) return;
        
        var button = document.createElement('div');
        button.id = 'sitepulse-reviews-btn';
        button.innerHTML = '⭐ Reviews';
        button.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#3498db;color:white;padding:12px 16px;border-radius:25px;cursor:pointer;z-index:999998;font-size:14px;font-weight:500;box-shadow:0 4px 12px rgba(52,152,219,0.3);transition:all 0.3s ease;';
        
        button.onmouseover = function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 6px 16px rgba(52,152,219,0.4)';
        };
        
        button.onmouseout = function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 12px rgba(52,152,219,0.3)';
        };
        
        button.onclick = function() {
            SitePulse.reviews.show();
        };
        
        document.body.appendChild(button);
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            SitePulse.init();
            SitePulse.addReviewsButton();
        });
    } else {
        SitePulse.init();
        SitePulse.addReviewsButton();
    }
    
    // Make SitePulse available globally for debugging
    window.SitePulse = SitePulse;
    
})();
