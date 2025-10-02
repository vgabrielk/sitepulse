(function(){
    'use strict';

    function getScriptOrigin() {
        var scripts = document.getElementsByTagName('script');
        for (var i = scripts.length - 1; i >= 0; i--) {
            var s = scripts[i];
            try {
                if (s.src && s.src.indexOf('/faq-widget.js') !== -1) {
                    return new URL(s.src).origin;
                }
            } catch (e) {}
        }
        return window.location.origin;
    }

    function sanitize(text) {
        return String(text || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function ensureStyles(opts) {
        if (document.getElementById('sp-faq-style')) return;
        var style = document.createElement('style');
        style.id = 'sp-faq-style';
        var css = (
            '.sp-faq-widget{background:'+ (opts.card_bg||'#ffffff') +';border:1px solid '+(opts.card_border||'#eef2f7')+';border-radius:'+(opts.border_radius||'16px')+';padding:'+(opts.padding||'24px')+';box-shadow:'+(opts.box_shadow||'0 10px 30px rgba(15,23,42,0.06)')+';font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Ubuntu,Cantarell,\'Helvetica Neue\',Arial,sans-serif}' +
            '.sp-faq-item{border:1px solid '+(opts.divider||'#e5e7eb')+';border-radius:12px;overflow:hidden}' +
            '.sp-faq-btn{width:100%;padding:14px 18px;background:'+ (opts.card_bg||'#ffffff') +';display:flex;align-items:center;justify-content:space-between;cursor:pointer;border:0;text-align:left}' +
            '.sp-faq-q{font-weight:'+ (opts.font_weight||'600') +';color:'+ (opts.question||'#0f172a') +';font-size:'+ (opts.font_size||'14px') +';font-family:'+ (opts.font_family||'inherit') +'}' +
            '.sp-faq-icon{width:20px;height:20px;color:'+ (opts.icon||'#94a3b8') +';transition:transform .2s ease}' +
            '.sp-faq-panel{overflow:hidden;max-height:0;transition:max-height .28s ease}' +
            '.sp-faq-panel.open{max-height:240px}' +
            '.sp-faq-a{padding:0 18px 14px 18px;color:'+ (opts.answer||'#334155') +';line-height:1.6}' +
            '.sp-faq-premium{margin-top:16px;padding:12px 14px;background:'+ (opts.note_bg||'#eff6ff') +';border:1px solid '+ (opts.note_border||'#bfdbfe') +';border-radius:12px;color:'+ (opts.note_text||'#334155') +';font-size:14px}' +
            '.sp-faq-premium b{color:#2563eb}'
        );
        style.textContent = css;
        document.head.appendChild(style);
    }

    function renderList(node, list, styleOpts) {
        ensureStyles(styleOpts || {});
        var container = document.createElement('div');
        container.className = 'sp-faq-widget';
        if (!Array.isArray(list) || !list.length) {
            container.innerHTML = '<div style="color:#6b7280;font-size:13px">Nenhuma FAQ disponível.</div>';
            node.innerHTML = '';
            node.appendChild(container);
            return;
        }

        list.forEach(function(item, idx){
            var q = sanitize(item.question);
            var a = sanitize(item.answer);

            var itemEl = document.createElement('div');
            itemEl.className = 'sp-faq-item';

            var btn = document.createElement('button');
            btn.className = 'sp-faq-btn';
            btn.setAttribute('type', 'button');
            btn.innerHTML = '<span class="sp-faq-q">'+ q +'</span>'+
                            '<svg class="sp-faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';

            var panel = document.createElement('div');
            panel.className = 'sp-faq-panel';
            panel.innerHTML = '<div class="sp-faq-a">'+ a +'</div>';

            btn.addEventListener('click', function(){
                var isOpen = panel.classList.contains('open');
                document.querySelectorAll('.sp-faq-panel.open').forEach(function(p){ p.classList.remove('open'); });
                document.querySelectorAll('.sp-faq-btn.rot').forEach(function(b){ b.classList.remove('rot'); });
                if (!isOpen) {
                    panel.classList.add('open');
                    btn.classList.add('rot');
                }
            });

            itemEl.appendChild(btn);
            itemEl.appendChild(panel);
            container.appendChild(itemEl);
            if (idx < list.length - 1) {
                var spacer = document.createElement('div');
                spacer.style.height = '12px';
                container.appendChild(spacer);
            }
        });

        var note = document.createElement('div');
        note.className = 'sp-faq-premium';
        note.innerHTML = '<span><b>Premium:</b> IA treinada no seu conteúdo responde automaticamente</span>';
        container.appendChild(note);

        node.innerHTML = '';
        node.appendChild(container);
    }

    function mount() {
        var nodes = document.querySelectorAll('[data-sitepulse-faq]');
        if (!nodes.length) return;

        var defaultApi = getScriptOrigin();

        nodes.forEach(function(node){
            var api = node.getAttribute('data-api-url') || defaultApi;
            var widgetId = node.getAttribute('data-widget-id');
            var siteId = node.getAttribute('data-site-id');

            function fallback() {
                if (widgetId) {
                    node.innerHTML = '<iframe src="' + api + '/widget/' + widgetId + '/faq" style="width:100%;border:0;height:380px"></iframe>';
                } else {
                    node.innerHTML = '<div style="color:#6b7280;font-size:13px">FAQ indisponível.</div>';
                }
            }

            var url = widgetId ? (api + '/widget/' + widgetId + '/faqs')
                                : (siteId ? (api + '/widget/faqs?site_id=' + encodeURIComponent(siteId)) : null);
            if (!url) { fallback(); return; }

            fetch(url, { credentials: 'omit' })
                .then(function(r){ return r.ok ? r.json() : []; })
                .catch(function(){ return []; })
                .then(function(list){
                    if (Array.isArray(list) && list.length) {
                        var styleOpts = {
                            card_bg: node.getAttribute('data-card-bg'),
                            card_border: node.getAttribute('data-card-border'),
                            border_radius: node.getAttribute('data-border-radius'),
                            padding: node.getAttribute('data-padding'),
                            box_shadow: node.getAttribute('data-box-shadow'),
                            question: node.getAttribute('data-color-question'),
                            answer: node.getAttribute('data-color-answer'),
                            divider: node.getAttribute('data-color-divider'),
                            icon: node.getAttribute('data-color-icon'),
                            note_bg: node.getAttribute('data-note-bg'),
                            note_border: node.getAttribute('data-note-border'),
                            note_text: node.getAttribute('data-note-text'),
                            font_family: node.getAttribute('data-font-family'),
                            font_size: node.getAttribute('data-font-size'),
                            font_weight: node.getAttribute('data-font-weight'),
                        };
                        renderList(node, list, styleOpts);
                    } else if (siteId && widgetId) {
                        // try alternate by site if started with widget (or vice-versa)
                        var altUrl = api + '/widget/faqs?site_id=' + encodeURIComponent(siteId);
                        fetch(altUrl, { credentials:'omit' })
                            .then(function(r){ return r.ok ? r.json() : []; })
                            .catch(function(){ return []; })
                            .then(function(list2){
                                if (Array.isArray(list2) && list2.length) {
                                    renderList(node, list2, {});
                                } else {
                                    fallback();
                                }
                            });
                    } else {
                        fallback();
                    }
                });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mount);
    } else {
        mount();
    }
})();


