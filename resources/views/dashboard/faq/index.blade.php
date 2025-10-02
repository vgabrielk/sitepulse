@extends('dashboard.layout')

@section('title', 'FAQ Inteligente - ' . $site->name)
@section('page-title', 'FAQ Inteligente - ' . $site->name)

@section('page-actions')
@endsection

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
        @push('scripts')
        <script>document.addEventListener('DOMContentLoaded',()=>{ try{ showToast && showToast('success', @json(session('success'))); }catch(e){} })</script>
        @endpush
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.card class="lg:col-span-2">
            <h3 class="font-semibold mb-4">FAQs do site</h3>
            @if($faqs->count())
                <div class="space-y-3">
                    @foreach($faqs as $faq)
                        <div class="border border-border rounded-lg p-3">
                            <div class="flex items-start justify-between gap-3">
                                <form method="POST" action="{{ route('sites.faq.update', [$site, $faq]) }}" class="flex-1" data-faq-form="{{ $faq->id }}">
                                    @csrf
                                    @method('PUT')
                                    <label class="text-xs text-muted-foreground">Pergunta</label>
                                    <input name="question" value="{{ $faq->question }}" class="w-full px-3 py-2 border border-input rounded-lg mb-3" required disabled>
                                    <label class="text-xs text-muted-foreground">Resposta</label>
                                    <textarea name="answer" rows="3" class="w-full px-3 py-2 border border-input rounded-lg" disabled>{{ $faq->answer }}</textarea>
                                    <div class="mt-3 flex items-center justify-end">
                                        <div class="flex items-center gap-2">
                                            <x-ui.button type="button" size="sm" data-faq-edit="{{ $faq->id }}">Editar</x-ui.button>
                                            <x-ui.button type="submit" size="sm" variant="success" data-faq-save="{{ $faq->id }}" class="hidden">Salvar</x-ui.button>
                                            <form method="POST" action="{{ route('sites.faq.destroy', [$site, $faq]) }}" onsubmit="return confirm('Remover FAQ?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" variant="destructive" size="sm">Remover</x-ui.button>
                                            </form>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted-foreground">Nenhuma FAQ cadastrada ainda.</p>
            @endif
        </x-ui.card>

        <x-ui.card>
            <h3 class="font-semibold mb-4">Adicionar nova FAQ</h3>
            <form method="POST" action="{{ route('sites.faq.store', $site) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs text-muted-foreground">Pergunta</label>
                    <input type="text" name="question" placeholder="Ex.: Qual o prazo de entrega?" class="w-full px-3 py-2 border border-input rounded-lg" required>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Resposta</label>
                    <textarea name="answer" rows="4" placeholder="Descreva a resposta de forma clara." class="w-full px-3 py-2 border border-input rounded-lg"></textarea>
                </div>
                <div class="flex items-center justify-end">
                    <div class="flex gap-2">
                        <x-ui.button type="reset" variant="outline">Limpar</x-ui.button>
                        <x-ui.button type="submit">Adicionar</x-ui.button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </div>

    <x-ui.card>
        <h3 class="font-semibold mb-4">Personalização do FAQ</h3>
        <form method="POST" action="{{ route('sites.faq.customize', $site) }}" class="space-y-4" id="faqCustomizationForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs text-muted-foreground">Cor do card (fundo)</label>
                    <input type="color" name="colors[card_bg]" value="{{ $faqCustomization['colors']['card_bg'] ?? '#ffffff' }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Borda do card</label>
                    <input type="color" name="colors[card_border]" value="{{ $faqCustomization['colors']['card_border'] ?? '#eef2f7' }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Título</label>
                    <input type="color" name="colors[title]" value="{{ $faqCustomization['colors']['title'] ?? '#0f172a' }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Pergunta</label>
                    <input type="color" name="colors[question]" value="{{ $faqCustomization['colors']['question'] ?? '#0f172a' }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Resposta</label>
                    <input type="color" name="colors[answer]" value="{{ $faqCustomization['colors']['answer'] ?? '#334155' }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Divisores</label>
                    <input type="color" name="colors[divider]" value="{{ $faqCustomization['colors']['divider'] ?? '#e5e7eb' }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Ícone</label>
                    <input type="color" name="colors[icon]" value="{{ $faqCustomization['colors']['icon'] ?? '#94a3b8' }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Nota (fundo)</label>
                    <input type="color" name="colors[note_bg]" value="{{ $faqCustomization['colors']['note_bg'] ?? '#eff6ff' }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Nota (borda)</label>
                    <input type="color" name="colors[note_border]" value="{{ $faqCustomization['colors']['note_border'] ?? '#bfdbfe' }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Nota (texto)</label>
                    <input type="color" name="colors[note_text]" value="{{ $faqCustomization['colors']['note_text'] ?? '#334155' }}" class="w-full h-10 border rounded"/>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs text-muted-foreground">Raio da borda</label>
                    <select name="layout[border_radius]" class="w-full border rounded px-2 py-2">
                        @foreach(['8px','12px','16px','20px','24px'] as $r)
                            <option value="{{ $r }}" {{ ($faqCustomization['layout']['border_radius'] ?? '16px') === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Padding</label>
                    <select name="layout[padding]" class="w-full border rounded px-2 py-2">
                        @foreach(['16px','20px','24px','28px'] as $p)
                            <option value="{{ $p }}" {{ ($faqCustomization['layout']['padding'] ?? '24px') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Sombra</label>
                    <select name="effects[box_shadow]" class="w-full border rounded px-2 py-2">
                        @foreach(['none','0 4px 12px rgba(0,0,0,0.08)','0 10px 30px rgba(15,23,42,0.06)'] as $s)
                            <option value="{{ $s }}" {{ ($faqCustomization['effects']['box_shadow'] ?? '0 10px 30px rgba(15,23,42,0.06)') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs text-muted-foreground">Fonte</label>
                    <select name="typography[font_family]" class="w-full border rounded px-2 py-2">
                        @foreach(['inherit','Arial, sans-serif','Helvetica, sans-serif','Georgia, serif'] as $f)
                            <option value="{{ $f }}" {{ ($faqCustomization['typography']['font_family'] ?? 'inherit') === $f ? 'selected' : '' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Tamanho</label>
                    <select name="typography[font_size]" class="w-full border rounded px-2 py-2">
                        @foreach(['12px','14px','16px','18px'] as $fs)
                            <option value="{{ $fs }}" {{ ($faqCustomization['typography']['font_size'] ?? '14px') === $fs ? 'selected' : '' }}>{{ $fs }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Peso</label>
                    <select name="typography[font_weight]" class="w-full border rounded px-2 py-2">
                        @foreach(['500','600','700'] as $fw)
                            <option value="{{ $fw }}" {{ ($faqCustomization['typography']['font_weight'] ?? '600') === $fw ? 'selected' : '' }}>{{ $fw }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2">
                <x-ui.button type="button" id="openFaqPreview">Salvar personalização</x-ui.button>
            </div>
        </form>
    </x-ui.card>


    <x-ui.card>
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-semibold">Embed</h3>
            <x-ui.button type="button" variant="outline" size="sm" id="copyFaqEmbed">Copiar</x-ui.button>
        </div>
        <p class="text-sm text-muted-foreground mb-3">Copie e cole em seu site:</p>
        <div class="relative">
            <pre class="p-3 bg-muted rounded overflow-x-auto" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; font-size: 13px; max-width: 100%;"><code id="faqEmbedSnippet">&lt;script async src="{{ url('/faq-widget.js') }}"&gt;&lt;/script&gt;
&lt;div data-sitepulse-faq="true" data-api-url="{{ url('') }}" data-widget-id="{{ $site->widget_id }}" data-site-id="{{ $site->id }}"&gt;&lt;/div&gt;</code></pre>
        </div>
    </x-ui.card>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('[data-faq-edit]').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = this.getAttribute('data-faq-edit');
            var form = document.querySelector('[data-faq-form="'+id+'"]');
            if(!form) return;
            var inputs = form.querySelectorAll('input[name="question"], textarea[name="answer"]');
            inputs.forEach(function(i){ i.disabled = false; });
            form.querySelector('[data-faq-save]')?.classList.remove('hidden');
            this.classList.add('hidden');
        });
    });
    // Live preview wiring
    const form = document.getElementById('faqCustomizationForm');
    const pvCard = document.getElementById('pv-card');
    const pvTitle = document.getElementById('pv-title');
    const pvItems = document.querySelectorAll('#pv-list .pv-item');
    const pvBtns = document.querySelectorAll('#pv-list .pv-btn');
    const pvPanels = document.querySelectorAll('#pv-list .pv-panel');
    const pvIcons = document.querySelectorAll('#pv-list .pv-icon');
    const pvNote = document.getElementById('pv-note');
    const snippet = document.getElementById('faqEmbedSnippet');

    function val(name, fallback){
        const input = form.querySelector('[name="'+name+'"]');
        return input ? input.value : fallback;
    }

    function updatePreview(){
        // Colors
        const cardBg = val('colors[card_bg]','#ffffff');
        const cardBorder = val('colors[card_border]','#eef2f7');
        const title = val('colors[title]','#0f172a');
        const question = val('colors[question]','#0f172a');
        const answer = val('colors[answer]','#334155');
        const divider = val('colors[divider]','#e5e7eb');
        const icon = val('colors[icon]','#94a3b8');
        const noteBg = val('colors[note_bg]','#eff6ff');
        const noteBorder = val('colors[note_border]','#bfdbfe');
        const noteText = val('colors[note_text]','#334155');

        // Layout/typography/effects
        const radius = val('layout[border_radius]','16px');
        const padding = val('layout[padding]','24px');
        const boxShadow = val('effects[box_shadow]','0 10px 30px rgba(15,23,42,0.06)');
        const fontFamily = val('typography[font_family]','inherit');
        const fontSize = val('typography[font_size]','14px');
        const fontWeight = val('typography[font_weight]','600');

        // Apply card styles
        pvCard.style.background = cardBg;
        pvCard.style.borderColor = cardBorder;
        pvCard.style.borderRadius = radius;
        pvCard.style.padding = padding;
        pvCard.style.boxShadow = boxShadow;

        // Title
        pvTitle.style.color = title;

        // Items
        pvItems.forEach(el => el.style.borderColor = divider);
        pvBtns.forEach(btn => { btn.style.color = question; btn.style.fontFamily = fontFamily; btn.style.fontSize = fontSize; btn.style.fontWeight = fontWeight; });
        pvPanels.forEach(p => { p.style.color = answer; p.style.fontFamily = fontFamily; p.style.fontSize = fontSize; });
        pvIcons.forEach(i => i.style.color = icon);

        // Note
        pvNote.style.background = noteBg;
        pvNote.style.borderColor = noteBorder;
        pvNote.style.color = noteText;

        // Update embed snippet with inline data-* reflecting customization (optional for granular styling)
        const base = `&lt;script async src="{{ url('/faq-widget.js') }}"&gt;&lt;/script&gt;\n` +
`&lt;div data-sitepulse-faq="true" data-api-url="{{ url('') }}" data-widget-id="{{ $site->widget_id }}" data-site-id="{{ $site->id }}"` +
` data-card-bg="${cardBg}" data-card-border="${cardBorder}" data-border-radius="${radius}" data-padding="${padding}" data-box-shadow="${boxShadow}"` +
` data-color-question="${question}" data-color-answer="${answer}" data-color-divider="${divider}" data-color-icon="${icon}" data-note-bg="${noteBg}" data-note-border="${noteBorder}" data-note-text="${noteText}"` +
` data-font-family="${fontFamily}" data-font-size="${fontSize}" data-font-weight="${fontWeight}"&gt;&lt;/div&gt;`;
        snippet.textContent = base;
    }

    form.addEventListener('input', updatePreview);
    form.addEventListener('change', updatePreview);
    updatePreview();
});
</script>
@endpush
@endsection

@push('scripts')
<script>
// ===== Modal de confirmação (FAQ) =====
document.addEventListener('DOMContentLoaded', function(){
    var form = document.getElementById('faqCustomizationForm');
    var openBtn = document.getElementById('openFaqPreview');
    if (!form || !openBtn) return;

    function val(name, fallback){
        var input = form.querySelector('[name="'+name+'"]');
        return input ? input.value : fallback;
    }

    function buildEmbedDiv(){
        var api = '{{ url('') }}';
        var widgetId = '{{ $site->widget_id }}';
        var siteId = '{{ $site->id }}';
        var attrs = [
            'data-sitepulse-faq="true"',
            'data-api-url="'+api+'"',
            'data-widget-id="'+widgetId+'"',
            'data-site-id="'+siteId+'"',
            'data-card-bg="'+val('colors[card_bg]','#fff')+'"',
            'data-card-border="'+val('colors[card_border]','#eef2f7')+'"',
            'data-border-radius="'+val('layout[border_radius]','16px')+'"',
            'data-padding="'+val('layout[padding]','24px')+'"',
            'data-box-shadow="'+val('effects[box_shadow]','0 10px 30px rgba(15,23,42,0.06)')+'"',
            'data-color-question="'+val('colors[question]','#0f172a')+'"',
            'data-color-answer="'+val('colors[answer]','#334155')+'"',
            'data-color-divider="'+val('colors[divider]','#e5e7eb')+'"',
            'data-color-icon="'+val('colors[icon]','#94a3b8')+'"',
            'data-note-bg="'+val('colors[note_bg]','#eff6ff')+'"',
            'data-note-border="'+val('colors[note_border]','#bfdbfe')+'"',
            'data-note-text="'+val('colors[note_text]','#334155')+'"',
            'data-font-family="'+val('typography[font_family]','inherit')+'"',
            'data-font-size="'+val('typography[font_size]','14px')+'"',
            'data-font-weight="'+val('typography[font_weight]','600')+'"'
        ].join(' ');
        return '<div '+attrs+'></div>';
    }

    function mountFaqWidget(){
        var script = document.createElement('script');
        script.async = true;
        script.src = '{{ url('/faq-widget.js') }}';
        document.body.appendChild(script);
    }

    function openModal(){
        var overlay = document.createElement('div');
        overlay.id = 'faq-preview-modal';
        overlay.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,0.45);backdrop-filter:blur(4px);z-index:100000;display:flex;align-items:center;justify-content:center;padding:20px;';
        var modal = document.createElement('div');
        modal.style.cssText = 'background:#ffffff;border-radius:14px;max-width:900px;width:100%;max-height:90vh;overflow:auto;box-shadow:0 20px 60px rgba(0,0,0,0.25)';
        modal.innerHTML = '\
<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #eee">\
  <div style="font-weight:700;color:#0f172a">Pré-visualização do FAQ</div>\
  <button id="faqCloseModal" style="background:#eef2f7;border:0;border-radius:8px;padding:6px 10px;cursor:pointer">Fechar</button>\
</div>\
<div style="padding:18px">'+ buildEmbedDiv() +'</div>\
<div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 18px;border-top:1px solid #eee">\
  <button id="faqCancel" style="background:#e5e7eb;border:0;border-radius:8px;padding:8px 14px;cursor:pointer">Cancelar</button>\
  <button id="faqConfirm" style="background:#2563eb;color:#fff;border:0;border-radius:8px;padding:8px 14px;cursor:pointer">Confirmar e salvar</button>\
</div>';
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        mountFaqWidget();

        function close(){ document.body.removeChild(overlay); }
        overlay.querySelector('#faqCloseModal').onclick = close;
        overlay.querySelector('#faqCancel').onclick = close;
        overlay.querySelector('#faqConfirm').onclick = function(){
            close();
            form.submit();
        };
    }

    openBtn.addEventListener('click', function(e){ e.preventDefault(); openModal(); });
    // copy embed
    var copyBtn = document.getElementById('copyFaqEmbed');
    var code = document.getElementById('faqEmbedSnippet');
    if (copyBtn && code) {
        copyBtn.addEventListener('click', function(){
            const txt = code.textContent;
            navigator.clipboard.writeText(txt).then(function(){
                try { showToast && showToast('success', 'Embed copiado!'); } catch(e) {}
            });
        });
    }
});
</script>
@endpush



