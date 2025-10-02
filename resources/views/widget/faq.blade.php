<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - {{ $site->name }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial, "Apple Color Emoji", "Segoe UI Emoji"; margin:0; }
        .container { max-width: 880px; margin: 0 auto; padding: 24px; }
        .card { background: {{ $site->faq_customization['colors']['card_bg'] ?? '#fff' }}; border:1px solid {{ $site->faq_customization['colors']['card_border'] ?? '#eef2f7' }}; border-radius: {{ $site->faq_customization['layout']['border_radius'] ?? '16px' }}; padding: {{ $site->faq_customization['layout']['padding'] ?? '24px' }}; box-shadow: {{ $site->faq_customization['effects']['box_shadow'] ?? '0 10px 30px rgba(15,23,42,0.06)' }}; }
        .faq { border: 1px solid {{ $site->faq_customization['colors']['divider'] ?? '#e5e7eb' }}; border-radius: 12px; overflow:hidden; }
        .q { cursor: pointer; display:flex; justify-content:space-between; align-items:center; font-weight: {{ $site->faq_customization['typography']['font_weight'] ?? '600' }}; color: {{ $site->faq_customization['colors']['question'] ?? '#0f172a' }}; padding:14px 18px; font-family: {{ $site->faq_customization['typography']['font_family'] ?? 'inherit' }}; font-size: {{ $site->faq_customization['typography']['font_size'] ?? '14px' }}; }
        .a { color: {{ $site->faq_customization['colors']['answer'] ?? '#334155' }}; line-height:1.6; display:none; padding:0 18px 14px 18px; }
        .open .a { display:block; }
        .muted { color:#6b7280; font-size: 12px; margin-bottom: 8px; }
        .chev { width:20px; height:20px; transform: rotate(0deg); transition: transform .2s ease; color: {{ $site->faq_customization['colors']['icon'] ?? '#94a3b8' }}; }
        .open .chev { transform: rotate(180deg); }
        .spacer { height:12px; }
        .note { margin-top:16px; padding:12px 14px; background: {{ $site->faq_customization['colors']['note_bg'] ?? '#eff6ff' }}; border:1px solid {{ $site->faq_customization['colors']['note_border'] ?? '#bfdbfe' }}; border-radius:12px; color: {{ $site->faq_customization['colors']['note_text'] ?? '#334155' }}; font-size:14px; }
        .note b { color:#2563eb; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.faq .q').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var p = this.parentElement;
                    p.classList.toggle('open');
                });
            });
        });
    </script>
    </head>
<body>
    <div class="container">
        <div class="card">
            <h1 style="margin:0 0 12px 0; font-size:18px; font-weight:700; color: {{ $site->faq_customization['colors']['title'] ?? '#0f172a' }};">Perguntas frequentes</h1>
            @forelse($faqs as $idx => $item)
                <div class="faq">
                    <div class="q">
                        <span>{{ $item->question }}</span>
                        <svg class="chev" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
                    </div>
                    @if($item->answer)
                        <div class="a">{{ $item->answer }}</div>
                    @endif
                </div>
                @if(!$loop->last)
                    <div class="spacer"></div>
                @endif
            @empty
                <div class="muted">Nenhuma FAQ disponível.</div>
            @endforelse
            <div class="note"><span><b>Premium:</b> IA treinada no seu conteúdo responde automaticamente</span></div>
        </div>
    </div>
</body>
</html>


