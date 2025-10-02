@extends('dashboard.layout')

@section('title', 'Customize Widget - ' . $site->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Customize Widget</h1>
                    <p class="text-muted mb-0">{{ $site->name }} - {{ $site->domain }}</p>
                </div>
                <div>
                    <a href="{{ route('sites.show', $site) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Site
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Customization Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Widget Customization</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <x-ui.alert variant="success" title="Sucesso">
                            {{ session('success') }}
                        </x-ui.alert>
                    @endif

                    <form method="POST" action="{{ route('sites.save-customization', $site) }}" id="customizationForm">
                        @csrf
                        
                        <!-- Theme Presets -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Theme Presets</label>
                            <div class="row g-3">
                                @foreach($themePresets as $key => $preset)
                                    <div class="col-md-3">
                                        <div class="preset-card" data-preset="{{ $key }}">
                                            <div class="preset-preview" style="
                                                background: {{ $preset['colors']['background'] }};
                                                border: 2px solid {{ $preset['colors']['primary'] }};
                                                border-radius: {{ $preset['layout']['border_radius'] }};
                                                padding: 10px;
                                                text-align: center;
                                                cursor: pointer;
                                            ">
                                                <div style="
                                                    color: {{ $preset['colors']['text'] }};
                                                    font-size: 12px;
                                                    font-weight: {{ $preset['typography']['font_weight'] }};
                                                ">{{ $preset['name'] }}</div>
                                                <div style="
                                                    color: {{ $preset['colors']['primary'] }};
                                                    font-size: 10px;
                                                    margin-top: 5px;
                                                ">★★★★★</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Colors Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Colors</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="colors_primary" class="form-label">Primary Color</label>
                                    <input type="color" class="form-control form-control-color" id="colors_primary" 
                                           name="colors[primary]" value="{{ $customization['colors']['primary'] }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="colors_secondary" class="form-label">Secondary Color</label>
                                    <input type="color" class="form-control form-control-color" id="colors_secondary" 
                                           name="colors[secondary]" value="{{ $customization['colors']['secondary'] }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="colors_background" class="form-label">Background Color</label>
                                    <input type="color" class="form-control form-control-color" id="colors_background" 
                                           name="colors[background]" value="{{ $customization['colors']['background'] }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="colors_text" class="form-label">Text Color</label>
                                    <input type="color" class="form-control form-control-color" id="colors_text" 
                                           name="colors[text]" value="{{ $customization['colors']['text'] }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="colors_accent" class="form-label">Accent Color</label>
                                    <input type="color" class="form-control form-control-color" id="colors_accent" 
                                           name="colors[accent]" value="{{ $customization['colors']['accent'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Typography Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Typography</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="typography_font_family" class="form-label">Font Family</label>
                                    <select class="form-select" id="typography_font_family" name="typography[font_family]">
                                        <option value="inherit" {{ $customization['typography']['font_family'] == 'inherit' ? 'selected' : '' }}>Inherit</option>
                                        <option value="Arial, sans-serif" {{ $customization['typography']['font_family'] == 'Arial, sans-serif' ? 'selected' : '' }}>Arial</option>
                                        <option value="Helvetica, sans-serif" {{ $customization['typography']['font_family'] == 'Helvetica, sans-serif' ? 'selected' : '' }}>Helvetica</option>
                                        <option value="Georgia, serif" {{ $customization['typography']['font_family'] == 'Georgia, serif' ? 'selected' : '' }}>Georgia</option>
                                        <option value="Times New Roman, serif" {{ $customization['typography']['font_family'] == 'Times New Roman, serif' ? 'selected' : '' }}>Times New Roman</option>
                                        <option value="Courier New, monospace" {{ $customization['typography']['font_family'] == 'Courier New, monospace' ? 'selected' : '' }}>Courier New</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="typography_font_size" class="form-label">Font Size</label>
                                    <select class="form-select" id="typography_font_size" name="typography[font_size]">
                                        <option value="12px" {{ $customization['typography']['font_size'] == '12px' ? 'selected' : '' }}>12px</option>
                                        <option value="14px" {{ $customization['typography']['font_size'] == '14px' ? 'selected' : '' }}>14px</option>
                                        <option value="16px" {{ $customization['typography']['font_size'] == '16px' ? 'selected' : '' }}>16px</option>
                                        <option value="18px" {{ $customization['typography']['font_size'] == '18px' ? 'selected' : '' }}>18px</option>
                                        <option value="20px" {{ $customization['typography']['font_size'] == '20px' ? 'selected' : '' }}>20px</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="typography_font_weight" class="form-label">Font Weight</label>
                                    <select class="form-select" id="typography_font_weight" name="typography[font_weight]">
                                        <option value="normal" {{ $customization['typography']['font_weight'] == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="bold" {{ $customization['typography']['font_weight'] == 'bold' ? 'selected' : '' }}>Bold</option>
                                        <option value="500" {{ $customization['typography']['font_weight'] == '500' ? 'selected' : '' }}>Medium</option>
                                        <option value="600" {{ $customization['typography']['font_weight'] == '600' ? 'selected' : '' }}>Semi Bold</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Layout Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Layout</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="layout_border_radius" class="form-label">Border Radius</label>
                                    <select class="form-select" id="layout_border_radius" name="layout[border_radius]">
                                        <option value="0px" {{ $customization['layout']['border_radius'] == '0px' ? 'selected' : '' }}>None (0px)</option>
                                        <option value="4px" {{ $customization['layout']['border_radius'] == '4px' ? 'selected' : '' }}>Small (4px)</option>
                                        <option value="8px" {{ $customization['layout']['border_radius'] == '8px' ? 'selected' : '' }}>Medium (8px)</option>
                                        <option value="12px" {{ $customization['layout']['border_radius'] == '12px' ? 'selected' : '' }}>Large (12px)</option>
                                        <option value="20px" {{ $customization['layout']['border_radius'] == '20px' ? 'selected' : '' }}>Extra Large (20px)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="layout_padding" class="form-label">Padding</label>
                                    <select class="form-select" id="layout_padding" name="layout[padding]">
                                        <option value="10px" {{ $customization['layout']['padding'] == '10px' ? 'selected' : '' }}>Small (10px)</option>
                                        <option value="16px" {{ $customization['layout']['padding'] == '16px' ? 'selected' : '' }}>Medium (16px)</option>
                                        <option value="20px" {{ $customization['layout']['padding'] == '20px' ? 'selected' : '' }}>Large (20px)</option>
                                        <option value="24px" {{ $customization['layout']['padding'] == '24px' ? 'selected' : '' }}>Extra Large (24px)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="layout_margin" class="form-label">Margin</label>
                                    <select class="form-select" id="layout_margin" name="layout[margin]">
                                        <option value="5px 0" {{ $customization['layout']['margin'] == '5px 0' ? 'selected' : '' }}>Small (5px)</option>
                                        <option value="10px 0" {{ $customization['layout']['margin'] == '10px 0' ? 'selected' : '' }}>Medium (10px)</option>
                                        <option value="15px 0" {{ $customization['layout']['margin'] == '15px 0' ? 'selected' : '' }}>Large (15px)</option>
                                        <option value="20px 0" {{ $customization['layout']['margin'] == '20px 0' ? 'selected' : '' }}>Extra Large (20px)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="layout_max_width" class="form-label">Max Width</label>
                                    <select class="form-select" id="layout_max_width" name="layout[max_width]">
                                        <option value="400px" {{ $customization['layout']['max_width'] == '400px' ? 'selected' : '' }}>Small (400px)</option>
                                        <option value="600px" {{ $customization['layout']['max_width'] == '600px' ? 'selected' : '' }}>Medium (600px)</option>
                                        <option value="800px" {{ $customization['layout']['max_width'] == '800px' ? 'selected' : '' }}>Large (800px)</option>
                                        <option value="1000px" {{ $customization['layout']['max_width'] == '1000px' ? 'selected' : '' }}>Extra Large (1000px)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Effects Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Effects</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="effects_box_shadow" class="form-label">Box Shadow</label>
                                    <select class="form-select" id="effects_box_shadow" name="effects[box_shadow]">
                                        <option value="none" {{ $customization['effects']['box_shadow'] == 'none' ? 'selected' : '' }}>None</option>
                                        <option value="0 2px 4px rgba(0,0,0,0.1)" {{ $customization['effects']['box_shadow'] == '0 2px 4px rgba(0,0,0,0.1)' ? 'selected' : '' }}>Light</option>
                                        <option value="0 4px 12px rgba(0,0,0,0.1)" {{ $customization['effects']['box_shadow'] == '0 4px 12px rgba(0,0,0,0.1)' ? 'selected' : '' }}>Medium</option>
                                        <option value="0 8px 24px rgba(0,0,0,0.15)" {{ $customization['effects']['box_shadow'] == '0 8px 24px rgba(0,0,0,0.15)' ? 'selected' : '' }}>Strong</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="effects_hover_shadow" class="form-label">Hover Shadow</label>
                                    <select class="form-select" id="effects_hover_shadow" name="effects[hover_shadow]">
                                        <option value="none" {{ $customization['effects']['hover_shadow'] == 'none' ? 'selected' : '' }}>None</option>
                                        <option value="0 4px 8px rgba(0,0,0,0.15)" {{ $customization['effects']['hover_shadow'] == '0 4px 8px rgba(0,0,0,0.15)' ? 'selected' : '' }}>Light</option>
                                        <option value="0 6px 20px rgba(0,0,0,0.15)" {{ $customization['effects']['hover_shadow'] == '0 6px 20px rgba(0,0,0,0.15)' ? 'selected' : '' }}>Medium</option>
                                        <option value="0 12px 32px rgba(0,0,0,0.2)" {{ $customization['effects']['hover_shadow'] == '0 12px 32px rgba(0,0,0,0.2)' ? 'selected' : '' }}>Strong</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="effects_animation" class="form-label">Animation</label>
                                    <select class="form-select" id="effects_animation" name="effects[animation]">
                                        <option value="none" {{ $customization['effects']['animation'] == 'none' ? 'selected' : '' }}>None</option>
                                        <option value="fadeIn 0.3s ease" {{ $customization['effects']['animation'] == 'fadeIn 0.3s ease' ? 'selected' : '' }}>Fade In</option>
                                        <option value="slideIn 0.5s ease" {{ $customization['effects']['animation'] == 'slideIn 0.5s ease' ? 'selected' : '' }}>Slide In</option>
                                        <option value="bounceIn 0.6s ease" {{ $customization['effects']['animation'] == 'bounceIn 0.6s ease' ? 'selected' : '' }}>Bounce In</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetToDefault()">
                                <i class="fas fa-undo"></i> Reset to Default
                            </button>
                            <button type="button" id="openReviewsPreview" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Customization
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Live Preview -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Live Preview</h5>
                </div>
                <div class="card-body">
                    <div id="livePreview" style="
                        background: {{ $customization['colors']['background'] }};
                        border-radius: {{ $customization['layout']['border_radius'] }};
                        padding: {{ $customization['layout']['padding'] }};
                        box-shadow: {{ $customization['effects']['box_shadow'] }};
                        font-family: {{ $customization['typography']['font_family'] }};
                        font-size: {{ $customization['typography']['font_size'] }};
                        font-weight: {{ $customization['typography']['font_weight'] }};
                        color: {{ $customization['colors']['text'] }};
                        max-width: 100%;
                    ">
                        <h6 style="color: {{ $customization['colors']['text'] }}; margin-bottom: 15px;">Customer Reviews</h6>
                        
                        <div class="review-preview" style="
                            background: {{ $customization['colors']['background'] }};
                            border-left: 4px solid {{ $customization['colors']['primary'] }};
                            border-radius: {{ $customization['layout']['border_radius'] }};
                            padding: 15px;
                            margin-bottom: 10px;
                            box-shadow: {{ $customization['effects']['box_shadow'] }};
                        ">
                            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                <strong style="color: {{ $customization['colors']['text'] }}; margin-right: 10px;">John Doe</strong>
                                <div style="color: {{ $customization['colors']['accent'] }};">★★★★★</div>
                            </div>
                            <p style="color: {{ $customization['colors']['text'] }}; margin: 0; font-size: 0.9em;">Great service! Highly recommended.</p>
                        </div>
                        
                        <button style="
                            background: {{ $customization['colors']['primary'] }};
                            color: white;
                            border: none;
                            border-radius: {{ $customization['layout']['border_radius'] }};
                            padding: 10px 20px;
                            cursor: pointer;
                            font-size: {{ $customization['typography']['font_size'] }};
                        ">Write a Review</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.preset-card {
    cursor: pointer;
    transition: transform 0.2s;
}

.preset-card:hover {
    transform: scale(1.05);
}

.preset-card.selected {
    border: 2px solid #007bff;
    border-radius: 8px;
}

.review-preview:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('customizationForm');
    const preview = document.getElementById('livePreview');
    const presetCards = document.querySelectorAll('.preset-card');
    
    // Theme presets data
    const presets = @json($themePresets);
    
    // Update preview on form change
    form.addEventListener('input', updatePreview);
    form.addEventListener('change', updatePreview);
    
    // Preset selection
    presetCards.forEach(card => {
        card.addEventListener('click', function() {
            const presetKey = this.dataset.preset;
            const preset = presets[presetKey];
            
            // Remove selected class from all cards
            presetCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            
            // Apply preset values
            applyPreset(preset);
            updatePreview();
        });
    });
    
    function applyPreset(preset) {
        // Colors
        document.getElementById('colors_primary').value = preset.colors.primary;
        document.getElementById('colors_secondary').value = preset.colors.secondary;
        document.getElementById('colors_background').value = preset.colors.background;
        document.getElementById('colors_text').value = preset.colors.text;
        document.getElementById('colors_accent').value = preset.colors.accent;
        
        // Typography
        document.getElementById('typography_font_family').value = preset.typography.font_family;
        document.getElementById('typography_font_size').value = preset.typography.font_size;
        document.getElementById('typography_font_weight').value = preset.typography.font_weight;
        
        // Layout
        document.getElementById('layout_border_radius').value = preset.layout.border_radius;
        document.getElementById('layout_padding').value = preset.layout.padding;
        document.getElementById('layout_margin').value = preset.layout.margin;
        document.getElementById('layout_max_width').value = preset.layout.max_width;
        
        // Effects
        document.getElementById('effects_box_shadow').value = preset.effects.box_shadow;
        document.getElementById('effects_hover_shadow').value = preset.effects.hover_shadow;
        document.getElementById('effects_animation').value = preset.effects.animation;
    }
    
    function updatePreview() {
        const colors = {
            primary: document.getElementById('colors_primary').value,
            secondary: document.getElementById('colors_secondary').value,
            background: document.getElementById('colors_background').value,
            text: document.getElementById('colors_text').value,
            accent: document.getElementById('colors_accent').value
        };
        
        const typography = {
            font_family: document.getElementById('typography_font_family').value,
            font_size: document.getElementById('typography_font_size').value,
            font_weight: document.getElementById('typography_font_weight').value
        };
        
        const layout = {
            border_radius: document.getElementById('layout_border_radius').value,
            padding: document.getElementById('layout_padding').value,
            margin: document.getElementById('layout_margin').value,
            max_width: document.getElementById('layout_max_width').value
        };
        
        const effects = {
            box_shadow: document.getElementById('effects_box_shadow').value,
            hover_shadow: document.getElementById('effects_hover_shadow').value,
            animation: document.getElementById('effects_animation').value
        };
        
        // Update preview styles
        preview.style.background = colors.background;
        preview.style.borderRadius = layout.border_radius;
        preview.style.padding = layout.padding;
        preview.style.boxShadow = effects.box_shadow;
        preview.style.fontFamily = typography.font_family;
        preview.style.fontSize = typography.font_size;
        preview.style.fontWeight = typography.font_weight;
        preview.style.color = colors.text;
        
        // Update review preview
        const reviewPreview = preview.querySelector('.review-preview');
        reviewPreview.style.background = colors.background;
        reviewPreview.style.borderLeftColor = colors.primary;
        reviewPreview.style.borderRadius = layout.border_radius;
        reviewPreview.style.boxShadow = effects.box_shadow;
        
        // Update text colors
        preview.querySelector('h6').style.color = colors.text;
        preview.querySelector('strong').style.color = colors.text;
        preview.querySelector('p').style.color = colors.text;
        preview.querySelector('.star-rating').style.color = colors.accent;
        
        // Update button
        const button = preview.querySelector('button');
        button.style.background = colors.primary;
        button.style.borderRadius = layout.border_radius;
        button.style.fontSize = typography.font_size;
    }
    
    function resetToDefault() {
        const defaultPreset = presets.default;
        applyPreset(defaultPreset);
        updatePreview();
        
        // Remove selected class from all cards
        presetCards.forEach(c => c.classList.remove('selected'));
    }
    
    // Initial preview update
    updatePreview();
});
</script>

<script>
// Modal de confirmação para Reviews
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('customizationForm');
    const openBtn = document.getElementById('openReviewsPreview');
    if (!form || !openBtn) return;

    function getVal(id){ const el = document.getElementById(id); return el ? el.value : ''; }

    function buildCustomizationJson(){
        const customization = {
            colors: {
                primary: getVal('colors_primary'),
                secondary: getVal('colors_secondary'),
                background: getVal('colors_background'),
                text: getVal('colors_text'),
                accent: getVal('colors_accent')
            },
            typography: {
                font_family: document.getElementById('typography_font_family')?.value || 'inherit',
                font_size: document.getElementById('typography_font_size')?.value || '14px',
                font_weight: document.getElementById('typography_font_weight')?.value || 'normal'
            },
            layout: {
                border_radius: document.getElementById('layout_border_radius')?.value || '8px',
                padding: document.getElementById('layout_padding')?.value || '16px',
                margin: document.getElementById('layout_margin')?.value || '10px 0',
                max_width: document.getElementById('layout_max_width')?.value || '600px'
            },
            effects: {
                box_shadow: document.getElementById('effects_box_shadow')?.value || '0 2px 8px rgba(0,0,0,0.1)',
                hover_shadow: document.getElementById('effects_hover_shadow')?.value || '0 4px 12px rgba(0,0,0,0.15)',
                animation: document.getElementById('effects_animation')?.value || 'fadeIn 0.3s ease'
            }
        };
        return JSON.stringify(customization);
    }

    function mountReviewsWidget(container){
        const apiUrl = '{{ url('') }}';
        const widgetId = '{{ $site->widget_id }}';
        const div = document.createElement('div');
        div.setAttribute('data-widget-id', widgetId);
        div.setAttribute('data-api-url', apiUrl);
        div.setAttribute('data-customization', buildCustomizationJson());
        container.appendChild(div);
        const script = document.createElement('script');
        script.async = true;
        script.src = '{{ url('/widget.js') }}';
        document.body.appendChild(script);
    }

    function openModal(){
        const overlay = document.createElement('div');
        overlay.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,0.45);backdrop-filter:blur(4px);z-index:100000;display:flex;align-items:center;justify-content:center;padding:20px;';
        const modal = document.createElement('div');
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
        mountReviewsWidget(modal.querySelector('#rvMount'));

        function close(){ document.body.removeChild(overlay); }
        modal.querySelector('#rvCloseModal').onclick = close;
        modal.querySelector('#rvCancel').onclick = close;
        modal.querySelector('#rvConfirm').onclick = function(){ close(); form.submit(); };
    }

    openBtn.addEventListener('click', function(e){ e.preventDefault(); openModal(); });
});
</script>
@endsection
