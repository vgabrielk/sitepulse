@extends('dashboard.layout')

@section('title', 'Widgets - SitePulse')
@section('page-title', 'Widgets')

@section('content')
<div class="space-y-6">
    <div class="overflow-hidden h-[calc(100vh-180px)]">
        <div id="wg-carousel" class="flex gap-4 overflow-x-auto pb-2 h-full" style="scroll-snap-type:x mandatory;">
        <x-ui.card class="h-full w-[80%] snap-start flex-shrink-0 flex flex-col">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center font-semibold">R</div>
                <div class="flex-1">
                    <h3 class="font-semibold">Reviews</h3>
                    <p class="text-sm text-muted-foreground">Exiba depoimentos e colete novas avaliações.</p>
                </div>
            </div>
            <div class="mt-4 border border-border rounded-lg overflow-hidden flex-1">
                <iframe src="{{ route('widget.reviews', $sites->first()->widget_id ?? 'demo') }}" style="width:100%;height:100%;border:0;background:transparent"></iframe>
            </div>
            <div class="mt-4 flex justify-end">
                <x-ui.button type="button" data-open-modal="reviews">Ver detalhes</x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card class="h-full w-[80%] snap-start flex-shrink-0 flex flex-col">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-500 text-white flex items-center justify-center font-semibold">F</div>
                <div class="flex-1">
                    <h3 class="font-semibold">FAQ Inteligente</h3>
                    <p class="text-sm text-muted-foreground">Perguntas frequentes com UI moderna e IA (premium).</p>
                </div>
            </div>
            <div class="mt-4 border border-border rounded-lg overflow-hidden flex-1">
                <iframe src="{{ url('/widget/demo/faq') }}" style="width:100%;height:100%;border:0;background:transparent"></iframe>
            </div>
            <div class="mt-4 flex justify-end">
                <x-ui.button type="button" data-open-modal="faq">Ver detalhes</x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card class="h-full w-[80%] snap-start flex-shrink-0 flex flex-col">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-slate-500 to-slate-700 text-white flex items-center justify-center font-semibold">A/D</div>
                <div class="flex-1">
                    <h3 class="font-semibold">Antes & Depois</h3>
                    <p class="text-sm text-muted-foreground">Mostre transformações com um slider elegante.</p>
                </div>
            </div>
            <div class="mt-4 border border-border rounded-lg overflow-hidden flex-1">
                <iframe src="{{ url('/widget/demo/before-after') }}" style="width:100%;height:100%;border:0;background:transparent"></iframe>
            </div>
            <div class="mt-4 flex justify-end">
                <x-ui.button type="button" data-open-modal="before-after">Ver detalhes</x-ui.button>
            </div>
        </x-ui.card>
        </div>
    </div>
</div>

{{-- Modal base --}}
<div id="widgetGalleryModal" class="fixed inset-0 bg-white/30 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
    <div class="bg-card rounded-xl shadow-2xl border border-border max-w-2xl w-full overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-border">
            <h3 id="wg-title" class="font-semibold">Widget</h3>
            <button id="wg-close" class="px-2 py-1 rounded hover:bg-muted">✕</button>
        </div>
        <div id="wg-body" class="p-5 space-y-4 text-sm text-muted-foreground"></div>
        <div id="wg-footer" class="px-5 py-4 border-t border-border flex items-center justify-end gap-2"></div>
    </div>
    <template id="wg-step-info">
        <div>
            <p class="mb-3" data-for="reviews">Exiba avaliações reais dos seus clientes, aumente prova social e conversão. Personalize cores, tipografia e muito mais.</p>
            <p class="mb-3" data-for="faq">Reduza tickets com um FAQ bonito e responsivo. Versão premium usa IA treinada no seu conteúdo.</p>
            <p class="mb-3" data-for="before-after">Destaque transformações com um slider de Antes & Depois.</p>
        </div>
    </template>
    <template id="wg-step-site">
        <div>
            <div class="text-sm text-muted-foreground mb-3">Escolha um site para usar este widget:</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="wg-site-list">
                @forelse($sites as $s)
                    <button class="px-3 py-2 border border-border rounded-lg hover:bg-muted text-left" data-site-id="{{ $s->id }}">
                        <div class="font-medium">{{ $s->name }}</div>
                        <div class="text-xs text-muted-foreground break-all">{{ $s->domain }}</div>
                    </button>
                @empty
                    <div class="text-muted-foreground text-sm">Nenhum site. <a class="underline" href="{{ route('sites.create') }}">Crie um site</a> para continuar.</div>
                @endforelse
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
(function(){
    const modal = document.getElementById('widgetGalleryModal');
    const title = document.getElementById('wg-title');
    const body = document.getElementById('wg-body');
    const footer = document.getElementById('wg-footer');
    const stepInfoTpl = document.getElementById('wg-step-info');
    const stepSiteTpl = document.getElementById('wg-step-site');
    let currentWidget = null;
    function open(){ modal.classList.remove('hidden'); modal.classList.add('flex'); }
    function close(){ modal.classList.add('hidden'); modal.classList.remove('flex'); currentWidget=null; body.innerHTML=''; footer.innerHTML=''; }
    function setStepInfo(){
        body.innerHTML = stepInfoTpl.innerHTML;
        Array.from(body.querySelectorAll('[data-for]')).forEach(function(p){ p.style.display = (p.getAttribute('data-for')===currentWidget)?'block':'none'; });
        footer.innerHTML = '';
        const cancel = document.createElement('button'); cancel.className='px-3 py-2 rounded-lg border border-border hover:bg-muted'; cancel.textContent='Cancelar'; cancel.onclick=close;
        const use = document.createElement('button'); use.className='px-3 py-2 rounded-lg bg-primary text-primary-foreground hover:opacity-90'; use.textContent='Usar'; use.onclick=setStepSite;
        footer.appendChild(cancel); footer.appendChild(use);
    }
    function setStepSite(){
        body.innerHTML = stepSiteTpl.innerHTML;
        footer.innerHTML = '';
        const back = document.createElement('button'); back.className='px-3 py-2 rounded-lg border border-border hover:bg-muted'; back.textContent='Voltar'; back.onclick=setStepInfo;
        footer.appendChild(back);
        document.getElementById('wg-site-list')?.addEventListener('click', function(e){
            const btn = e.target.closest('button[data-site-id]');
            if(!btn) return;
            const siteId = btn.getAttribute('data-site-id');
            const url = buildUrl(currentWidget, siteId);
            window.location.href = url;
        });
        // Demo preview above list
        const preview = document.createElement('div');
        preview.className = 'mb-4 border border-border rounded-lg overflow-hidden';
        const iframe = document.createElement('iframe');
        iframe.style.cssText = 'width:100%;height:260px;border:0;background:transparent';
        iframe.src = currentWidget==='reviews' ? `{{ url('/widget/demo/reviews') }}` : (currentWidget==='faq' ? `{{ url('/widget/demo/faq') }}` : `{{ url('/widget/demo/before-after') }}`);
        preview.appendChild(iframe);
        body.prepend(preview);
    }
    function buildUrl(widget, siteId){
        switch(widget){
            case 'reviews': return `{{ url('/reviews/sites') }}/${siteId}`;
            case 'faq': return `{{ url('/sites') }}/${siteId}/faq`;
            case 'before-after': return `{{ url('/widget/demo/before-after') }}?site_id=${siteId}`;
        }
        return '{{ url('/dashboard') }}';
    }
    document.querySelectorAll('[data-open-modal]')?.forEach(function(btn){
        btn.addEventListener('click', function(){ currentWidget = this.getAttribute('data-open-modal'); title.textContent = currentWidget==='reviews'?'Reviews':(currentWidget==='faq'?'FAQ Inteligente':'Antes & Depois'); open(); setStepInfo(); });
    });
    document.getElementById('wg-close')?.addEventListener('click', close);
    modal.addEventListener('click', function(e){ if(e.target===modal) close(); });
})();
</script>
@endpush
@endsection


