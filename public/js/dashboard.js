/**
 * SitePulse Dashboard JavaScript
 * Version: 1.0.0
 */

(function() {
    'use strict';

    // Dashboard namespace
    window.SitePulseDashboard = window.SitePulseDashboard || {};

    // Configuration
    const config = {
        apiUrl: '/api',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        refreshInterval: 30000, // 30 seconds
        chartColors: {
            primary: '#007bff',
            success: '#28a745',
            info: '#17a2b8',
            warning: '#ffc107',
            danger: '#dc3545',
            secondary: '#6c757d'
        }
    };

    // Utility functions
    const utils = {
        formatNumber: function(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toString();
        },

        formatDate: function(date) {
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        formatDateTime: function(date) {
            return new Date(date).toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
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
        }
    };

    // API client
    const api = {
        request: function(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            const mergedOptions = {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...options.headers
                }
            };

            return fetch(url, mergedOptions)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                });
        },

        get: function(url) {
            return this.request(url, { method: 'GET' });
        },

        post: function(url, data) {
            return this.request(url, {
                method: 'POST',
                body: JSON.stringify(data)
            });
        },

        put: function(url, data) {
            return this.request(url, {
                method: 'PUT',
                body: JSON.stringify(data)
            });
        },

        delete: function(url) {
            return this.request(url, { method: 'DELETE' });
        }
    };

    // Chart manager
    const charts = {
        instances: new Map(),

        create: function(canvasId, options) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return null;

            const ctx = canvas.getContext('2d');
            const chart = new Chart(ctx, options);
            this.instances.set(canvasId, chart);
            return chart;
        },

        update: function(canvasId, data) {
            const chart = this.instances.get(canvasId);
            if (chart) {
                chart.data = data;
                chart.update();
            }
        },

        destroy: function(canvasId) {
            const chart = this.instances.get(canvasId);
            if (chart) {
                chart.destroy();
                this.instances.delete(canvasId);
            }
        }
    };

    // Real-time updates
    const realtime = {
        interval: null,

        start: function() {
            this.interval = setInterval(() => {
                this.updateStats();
            }, config.refreshInterval);
        },

        stop: function() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        },

        updateStats: function() {
            // Update dashboard stats
            api.get('/api/auth/stats')
                .then(data => {
                    if (data.success) {
                        this.updateStatCards(data.data);
                    }
                })
                .catch(error => {
                    console.warn('Failed to update stats:', error);
                });
        },

        updateStatCards: function(stats) {
            // Update stat cards with new data
            const statElements = {
                'total-sites': stats.sites_count,
                'total-sessions': stats.total_sessions,
                'total-visits': stats.total_visits,
                'total-reviews': stats.total_reviews
            };

            Object.entries(statElements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = utils.formatNumber(value);
                }
            });
        }
    };

    // Form handlers
    const forms = {
        init: function() {
            // Auto-submit forms with data-auto-submit attribute
            document.querySelectorAll('form[data-auto-submit]').forEach(form => {
                form.addEventListener('change', utils.debounce(() => {
                    this.submitForm(form);
                }, 500));
            });

            // Handle form submissions
            document.querySelectorAll('form[data-ajax]').forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.submitForm(form);
                });
            });
        },

        submitForm: function(form) {
            const formData = new FormData(form);
            const url = form.action;
            const method = form.method || 'POST';

            const options = {
                method: method,
                body: formData
            };

            // Show loading state
            this.setLoadingState(form, true);

            fetch(url, options)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showMessage('success', data.message || 'Operation completed successfully');
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    } else {
                        this.showMessage('error', data.message || 'Operation failed');
                    }
                })
                .catch(error => {
                    this.showMessage('error', 'An error occurred: ' + error.message);
                })
                .finally(() => {
                    this.setLoadingState(form, false);
                });
        },

        setLoadingState: function(form, loading) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                if (loading) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalText || 'Submit';
                }
            }
        },

        showMessage: function(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            // Insert at the top of the main content
            const mainContent = document.querySelector('main');
            if (mainContent) {
                mainContent.insertAdjacentHTML('afterbegin', alertHtml);
            }
        }
    };

    // Table handlers
    const tables = {
        init: function() {
            // Initialize data tables
            document.querySelectorAll('table[data-sortable]').forEach(table => {
                this.makeSortable(table);
            });

            // Initialize bulk actions
            document.querySelectorAll('table[data-bulk-actions]').forEach(table => {
                this.initBulkActions(table);
            });
        },

        makeSortable: function(table) {
            const headers = table.querySelectorAll('th[data-sortable]');
            headers.forEach(header => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => {
                    this.sortTable(table, header);
                });
            });
        },

        sortTable: function(table, header) {
            const column = header.dataset.sortable;
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const isAscending = header.classList.contains('sort-asc');

            // Remove sort classes from all headers
            table.querySelectorAll('th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
            });

            // Add sort class to current header
            header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');

            // Sort rows
            rows.sort((a, b) => {
                const aValue = a.querySelector(`[data-sort="${column}"]`)?.textContent || '';
                const bValue = b.querySelector(`[data-sort="${column}"]`)?.textContent || '';
                
                if (isAscending) {
                    return bValue.localeCompare(aValue);
                } else {
                    return aValue.localeCompare(bValue);
                }
            });

            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        },

        initBulkActions: function(table) {
            const selectAllCheckbox = table.querySelector('input[data-select-all]');
            const itemCheckboxes = table.querySelectorAll('input[data-select-item]');
            const bulkActionsContainer = document.querySelector('[data-bulk-actions]');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', () => {
                    itemCheckboxes.forEach(checkbox => {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                    this.updateBulkActions();
                });
            }

            itemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    this.updateBulkActions();
                });
            });

            this.updateBulkActions();
        },

        updateBulkActions: function() {
            const selectedItems = document.querySelectorAll('input[data-select-item]:checked');
            const bulkActionsContainer = document.querySelector('[data-bulk-actions]');
            
            if (bulkActionsContainer) {
                if (selectedItems.length > 0) {
                    bulkActionsContainer.style.display = 'block';
                    bulkActionsContainer.querySelector('[data-selected-count]').textContent = selectedItems.length;
                } else {
                    bulkActionsContainer.style.display = 'none';
                }
            }
        }
    };

    // Widget preview
    const widgetPreview = {
        init: function() {
            const previewContainer = document.getElementById('widget-preview');
            if (!previewContainer) return;

            this.updatePreview();
            
            // Update preview when config changes
            document.querySelectorAll('input[data-widget-config], select[data-widget-config]').forEach(input => {
                input.addEventListener('change', () => {
                    this.updatePreview();
                });
            });
        },

        updatePreview: function() {
            const config = this.getConfig();
            const previewHtml = this.generatePreview(config);
            
            const previewContainer = document.getElementById('widget-preview');
            if (previewContainer) {
                previewContainer.innerHTML = previewHtml;
            }
        },

        getConfig: function() {
            const config = {};
            
            document.querySelectorAll('input[data-widget-config], select[data-widget-config]').forEach(input => {
                const key = input.dataset.widgetConfig;
                let value = input.value;
                
                if (input.type === 'checkbox') {
                    value = input.checked;
                } else if (input.type === 'number') {
                    value = parseInt(value) || 0;
                }
                
                config[key] = value;
            });
            
            return config;
        },

        generatePreview: function(config) {
            const position = config.position || 'bottom-right';
            const theme = config.theme || 'light';
            const size = config.size || 'medium';
            
            const styles = {
                position: 'fixed',
                zIndex: '9999',
                padding: size === 'small' ? '8px' : size === 'large' ? '16px' : '12px',
                borderRadius: '8px',
                boxShadow: '0 2px 10px rgba(0,0,0,0.1)',
                fontFamily: 'Arial, sans-serif',
                fontSize: size === 'small' ? '12px' : size === 'large' ? '16px' : '14px',
                cursor: 'pointer',
                backgroundColor: theme === 'dark' ? '#333' : '#fff',
                color: theme === 'dark' ? '#fff' : '#333',
                border: `2px solid ${config.primaryColor || '#007bff'}`
            };
            
            // Position styles
            if (position.includes('bottom')) styles.bottom = '20px';
            if (position.includes('top')) styles.top = '20px';
            if (position.includes('right')) styles.right = '20px';
            if (position.includes('left')) styles.left = '20px';
            
            const styleString = Object.entries(styles)
                .map(([key, value]) => `${key}: ${value}`)
                .join('; ');
            
            return `
                <div style="${styleString}">
                    ${config.showCounter ? '<div>👥 0</div>' : ''}
                    ${config.showFeedback ? '<div>💬</div>' : ''}
                </div>
            `;
        }
    };

    // Initialize dashboard
    function init() {
        forms.init();
        tables.init();
        widgetPreview.init();
        
        // Start real-time updates if on dashboard
        if (document.body.classList.contains('dashboard-page')) {
            realtime.start();
        }
        
        console.log('SitePulse Dashboard initialized');
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose public API
    window.SitePulseDashboard = {
        utils,
        api,
        charts,
        realtime,
        forms,
        tables,
        widgetPreview
    };

})();
