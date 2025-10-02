@extends('dashboard.layout')

@section('title', $site->name . ' Reviews - SitePulse')
@section('page-title', $site->name . ' - Reviews')

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.card>
            <div class="text-xs font-semibold text-warning uppercase mb-1">Pending Reviews</div>
            <div class="text-2xl font-bold">{{ $reviews->where('status', 'pending')->count() }}</div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-xs font-semibold text-success uppercase mb-1">Approved Reviews</div>
            <div class="text-2xl font-bold">{{ $reviews->where('status', 'approved')->count() }}</div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-xs font-semibold text-primary uppercase mb-1">Average Rating</div>
            <div class="text-2xl font-bold">{{ number_format($reviews->avg('rating') ?? 0, 1) }}</div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-xs font-semibold text-primary uppercase mb-1">Total Reviews</div>
            <div class="text-2xl font-bold">{{ $reviews->count() }}</div>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-ui.card>
            <h5 class="text-base font-semibold mb-4">Recent Reviews</h5>
            @if($reviews->count() > 0)
                <div class="divide-y divide-border">
                    @foreach($reviews->take(5) as $review)
                        <div class="py-3 flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <strong>{{ $review['visitor_name'] ?? 'Anonymous' }}</strong>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= ($review['rating'] ?? 0) ? 'text-warning' : 'text-muted-foreground' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-foreground">{{ Str::limit($review['comment'] ?? '', 100) }}</p>
                                <small class="text-xs text-muted-foreground">{{ \Carbon\Carbon::parse($review['created_at'] ?? now())->format('M d, Y') }}</small>
                            </div>
                            <div>
                                <x-ui.badge variant="{{ $review['status'] === 'approved' ? 'success' : ($review['status'] === 'rejected' ? 'destructive' : 'warning') }}">
                                    {{ ucfirst($review['status'] ?? 'pending') }}
                                </x-ui.badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted-foreground">No reviews found for this site.</p>
            @endif
        </x-ui.card>

        <x-ui.card>
            <h5 class="text-base font-semibold mb-4">How ratings are trending</h5>
            <div class="text-sm text-muted-foreground">Charts disabled. Focused on widgets.</div>
        </x-ui.card>
    </div>

    <x-ui.card>
        <h5 class="text-base font-semibold mb-4">Personalização do widget de Reviews</h5>
        <form method="POST" action="{{ route('sites.save-customization', $site) }}" id="reviewsCustomizationForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs text-muted-foreground">Primária</label>
                    <input type="color" id="rv_colors_primary" name="colors[primary]" value="{{ $customization['colors']['primary'] }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Secundária</label>
                    <input type="color" id="rv_colors_secondary" name="colors[secondary]" value="{{ $customization['colors']['secondary'] }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Fundo</label>
                    <input type="color" id="rv_colors_background" name="colors[background]" value="{{ $customization['colors']['background'] }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Texto</label>
                    <input type="color" id="rv_colors_text" name="colors[text]" value="{{ $customization['colors']['text'] }}" class="w-full h-10 border rounded"/>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Acento</label>
                    <input type="color" id="rv_colors_accent" name="colors[accent]" value="{{ $customization['colors']['accent'] }}" class="w-full h-10 border rounded"/>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs text-muted-foreground">Fonte</label>
                    <select id="rv_typography_font_family" name="typography[font_family]" class="w-full border rounded px-2 py-2">
                        @foreach(['inherit','Arial, sans-serif','Helvetica, sans-serif','Georgia, serif'] as $f)
                            <option value="{{ $f }}" {{ ($customization['typography']['font_family'] ?? 'inherit') === $f ? 'selected' : '' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Tamanho</label>
                    <select id="rv_typography_font_size" name="typography[font_size]" class="w-full border rounded px-2 py-2">
                        @foreach(['12px','14px','16px','18px'] as $fs)
                            <option value="{{ $fs }}" {{ ($customization['typography']['font_size'] ?? '14px') === $fs ? 'selected' : '' }}>{{ $fs }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Peso</label>
                    <select id="rv_typography_font_weight" name="typography[font_weight]" class="w-full border rounded px-2 py-2">
                        @foreach(['normal','500','600','700'] as $fw)
                            <option value="{{ $fw }}" {{ ($customization['typography']['font_weight'] ?? 'normal') === $fw ? 'selected' : '' }}>{{ $fw }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs text-muted-foreground">Raio da borda</label>
                    <select id="rv_layout_border_radius" name="layout[border_radius]" class="w-full border rounded px-2 py-2">
                        @foreach(['4px','8px','12px','16px','20px'] as $r)
                            <option value="{{ $r }}" {{ ($customization['layout']['border_radius'] ?? '8px') === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Padding</label>
                    <select id="rv_layout_padding" name="layout[padding]" class="w-full border rounded px-2 py-2">
                        @foreach(['12px','16px','20px','24px'] as $p)
                            <option value="{{ $p }}" {{ ($customization['layout']['padding'] ?? '16px') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-muted-foreground">Box shadow</label>
                    <select id="rv_effects_box_shadow" name="effects[box_shadow]" class="w-full border rounded px-2 py-2">
                        @foreach(['none','0 2px 8px rgba(0,0,0,0.1)','0 4px 12px rgba(0,0,0,0.1)'] as $s)
                            <option value="{{ $s }}" {{ ($customization['effects']['box_shadow'] ?? '0 2px 8px rgba(0,0,0,0.1)') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2">
                <x-ui.button type="button" id="openReviewsPreview">Salvar personalização</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card>
        <div class="flex items-center justify-between mb-2">
            <h5 class="text-base font-semibold">Embed</h5>
            <x-ui.button type="button" variant="outline" size="sm" id="copyReviewsEmbed">Copiar</x-ui.button>
        </div>
        <div class="relative">
            <pre class="p-3 bg-muted rounded overflow-x-auto" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; font-size: 13px; max-width: 100%;"><code id="reviewsEmbedSnippet">&lt;div id="sitepulse-reviews" data-widget-id="{{ $site->widget_id }}" data-api-url="{{ url('') }}" data-customization='@json($customization)'&gt;&lt;/div&gt;
&lt;script async src="{{ url('/widget.js') }}"&gt;&lt;/script&gt;</code></pre>
        </div>
    </x-ui.card>

    <x-ui.card>
        <h5 class="text-base font-semibold mb-4">All Reviews</h5>
        @if($reviews->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted">
                        <tr>
                            <th class="px-4 py-2 text-left">Reviewer</th>
                            <th class="px-4 py-2 text-left">Rating</th>
                            <th class="px-4 py-2 text-left">Comment</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($reviews as $review)
                            <tr>
                                <td class="px-4 py-2">
                                    <div class="font-medium">{{ $review['visitor_name'] ?? 'Anonymous' }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $review['visitor_email'] ?? 'No email' }}</div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= ($review['rating'] ?? 0) ? 'text-warning' : 'text-muted-foreground' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        <span class="ml-2">{{ $review['rating'] ?? 0 }}/5</span>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="max-w-xs">{{ Str::limit($review['comment'] ?? '', 150) }}</div>
                                </td>
                                <td class="px-4 py-2">
                                    <x-ui.badge variant="{{ $review['status'] === 'approved' ? 'success' : ($review['status'] === 'rejected' ? 'destructive' : 'warning') }}">
                                        {{ ucfirst($review['status'] ?? 'pending') }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-4 py-2"><small class="text-muted-foreground">{{ \Carbon\Carbon::parse($review['created_at'] ?? now())->format('M d, Y') }}</small></td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        @if($review['status'] === 'pending')
                                            <form method="POST" action="{{ route('reviews.approve', ['review' => $review['id']]) }}" class="inline">
                                                @csrf
                                                <x-ui.button type="submit" variant="success" size="sm">Approve</x-ui.button>
                                            </form>
                                            <form method="POST" action="{{ route('reviews.reject', $review['id']) }}" class="inline">
                                                @csrf
                                                <x-ui.button type="submit" variant="destructive" size="sm">Reject</x-ui.button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('reviews.destroy', $review['id']) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="destructive" size="sm">Delete</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-12 h-12 mx-auto text-muted-foreground mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/></svg>
                <h3 class="text-lg font-semibold">No Reviews Found</h3>
                <p class="text-muted-foreground">Reviews for this site will appear here once users start submitting feedback.</p>
            </div>
        @endif
    </x-ui.card>
</div>
@endsection
@push('scripts')
<script>
// Modal de confirmação – Reviews
document.addEventListener('DOMContentLoaded', function(){
    var form = document.getElementById('reviewsCustomizationForm');
    var openBtn = document.getElementById('openReviewsPreview');
    if(!form || !openBtn) return;

    function buildCustomization(){
        return JSON.stringify({
            colors: {
                primary: document.getElementById('rv_colors_primary')?.value,
                secondary: document.getElementById('rv_colors_secondary')?.value,
                background: document.getElementById('rv_colors_background')?.value,
                text: document.getElementById('rv_colors_text')?.value,
                accent: document.getElementById('rv_colors_accent')?.value,
            },
            typography: {
                font_family: document.getElementById('rv_typography_font_family')?.value,
                font_size: document.getElementById('rv_typography_font_size')?.value,
                font_weight: document.getElementById('rv_typography_font_weight')?.value,
            },
            layout: {
                border_radius: document.getElementById('rv_layout_border_radius')?.value,
                padding: document.getElementById('rv_layout_padding')?.value,
                margin: '{{ $customization['layout']['margin'] }}',
                max_width: '{{ $customization['layout']['max_width'] }}',
            },
            effects: {
                box_shadow: document.getElementById('rv_effects_box_shadow')?.value,
                hover_shadow: '{{ $customization['effects']['hover_shadow'] }}',
                animation: '{{ $customization['effects']['animation'] }}',
            }
        });
    }

    function openModal(){
        var overlay = document.createElement('div');
        overlay.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,0.45);backdrop-filter:blur(4px);z-index:100000;display:flex;align-items:center;justify-content:center;padding:20px;';
        var modal = document.createElement('div');
        modal.style.cssText = 'background:#ffffff;border-radius:14px;max-width:1000px;width:100%;max-height:90vh;overflow:auto;box-shadow:0 20px 60px rgba(0,0,0,0.25)';
        modal.innerHTML = '\
<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #eee">\
  <div style="font-weight:700;color:#0f172a">Pré-visualização de Reviews</div>\
  <button id="rvCloseModal" style="background:#eef2f7;border:0;border-radius:8px;padding:6px 10px;cursor:pointer">Fechar</button>\
</div>\
<div id="rvMount" style="padding:18px"></div>\
<div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 18px;border-top:1px solid #eee">\
  <button id="rvCancel" style="background:#e5e7eb;border:0;border-radius:8px;padding:8px 14px;cursor:pointer">Cancelar</button>\
  <button id="rvConfirm" style="background:#2563eb;color:#fff;border:0;border-radius:8px;padding:8px 14px;cursor:pointer">Confirmar e salvar</button>\
</div>';
        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // mount widget
        var container = document.createElement('div');
        container.id = 'sitepulse-reviews';
        container.setAttribute('data-widget-id', '{{ $site->widget_id }}');
        container.setAttribute('data-api-url', '{{ url('') }}');
        container.setAttribute('data-customization', buildCustomization());
        modal.querySelector('#rvMount').appendChild(container);
        var script = document.createElement('script');
        script.async = true;
        script.src = '{{ url('/widget.js') }}';
        document.body.appendChild(script);

        function close(){ document.body.removeChild(overlay); }
        modal.querySelector('#rvCloseModal').onclick = close;
        modal.querySelector('#rvCancel').onclick = close;
        modal.querySelector('#rvConfirm').onclick = function(){ close(); form.submit(); };
    }

    openBtn.addEventListener('click', function(e){ e.preventDefault(); openModal(); });
    // copy embed
    var copyBtn = document.getElementById('copyReviewsEmbed');
    var code = document.getElementById('reviewsEmbedSnippet');
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
