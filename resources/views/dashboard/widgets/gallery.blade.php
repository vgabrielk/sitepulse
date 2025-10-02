@extends('dashboard.layout')

@section('title', 'Widgets - SitePulse')
@section('page-title', 'Widgets')

@section('content')
<div class="space-y-6">
    <!-- Carousel Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Escolha seu Widget</h2>
            <p class="text-muted-foreground">Selecione um widget para começar</p>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="outline" size="sm" id="carousel-prev" class="p-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </x-ui.button>
            <x-ui.button variant="outline" size="sm" id="carousel-next" class="p-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </x-ui.button>
            <x-ui.button variant="outline" size="sm" id="carousel-auto" class="px-3 py-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1"/>
                </svg>
                Auto
            </x-ui.button>
        </div>
    </div>

    <!-- Carousel Container -->
    <div class="relative overflow-hidden h-[400px] md:h-[380px] rounded-xl bg-card/50">
        <div id="wg-carousel" class="flex gap-4 h-full transition-transform duration-500 ease-in-out" style="scroll-snap-type:x mandatory;">
        <x-ui.card class="h-full w-[95%] md:w-[60%] snap-start flex-shrink-0 flex flex-col">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center font-semibold">R</div>
                <div class="flex-1">
                    <h3 class="font-semibold">Reviews</h3>
                    <p class="text-sm text-muted-foreground">Exiba depoimentos e colete novas avaliações.</p>
                </div>
            </div>
            <div class="mt-4 border border-border rounded-lg overflow-hidden flex-1 bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center">
                <div class="text-center p-8">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Widget de Reviews</h3>
                    <p class="text-muted-foreground text-sm">Colete e exiba avaliações dos seus clientes</p>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <x-ui.button type="button" data-open-modal="reviews">Ver detalhes</x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card class="h-full w-[95%] md:w-[60%] snap-start flex-shrink-0 flex flex-col">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-500 text-white flex items-center justify-center font-semibold">F</div>
                <div class="flex-1">
                    <h3 class="font-semibold">FAQ Inteligente</h3>
                    <p class="text-sm text-muted-foreground">Perguntas frequentes com UI moderna e IA (premium).</p>
                </div>
            </div>
            <div class="mt-4 border border-border rounded-lg overflow-hidden flex-1 bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center">
                <div class="text-center p-8">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">FAQ Inteligente</h3>
                    <p class="text-muted-foreground text-sm">Perguntas frequentes com IA integrada</p>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <x-ui.button type="button" data-open-modal="faq">Ver detalhes</x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card class="h-full w-[95%] md:w-[60%] snap-start flex-shrink-0 flex flex-col">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-slate-500 to-slate-700 text-white flex items-center justify-center font-semibold">A/D</div>
                <div class="flex-1">
                    <h3 class="font-semibold">Antes & Depois</h3>
                    <p class="text-sm text-muted-foreground">Mostre transformações com um slider elegante.</p>
                </div>
            </div>
            <div class="mt-4 border border-border rounded-lg overflow-hidden flex-1 bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center">
                <div class="text-center p-8">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Antes & Depois</h3>
                    <p class="text-muted-foreground text-sm">Slider interativo para transformações</p>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <x-ui.button type="button" data-open-modal="before-after">Ver detalhes</x-ui.button>
            </div>
        </x-ui.card>
        </div>
        
        <!-- Carousel Indicators -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2" id="carousel-indicators">
            <button class="w-2 h-2 rounded-full bg-primary transition-all duration-300" data-slide="0"></button>
            <button class="w-2 h-2 rounded-full bg-muted-foreground/30 hover:bg-muted-foreground/50 transition-all duration-300" data-slide="1"></button>
            <button class="w-2 h-2 rounded-full bg-muted-foreground/30 hover:bg-muted-foreground/50 transition-all duration-300" data-slide="2"></button>
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
// Carousel Controller
class WidgetCarousel {
    constructor() {
        this.carousel = document.getElementById('wg-carousel');
        this.prevBtn = document.getElementById('carousel-prev');
        this.nextBtn = document.getElementById('carousel-next');
        this.autoBtn = document.getElementById('carousel-auto');
        this.indicators = document.querySelectorAll('#carousel-indicators button');
        
        this.currentSlide = 0;
        this.totalSlides = 3;
        this.isAutoPlay = false;
        this.autoPlayInterval = null;
        
        this.init();
    }
    
    init() {
        // Button event listeners
        this.prevBtn.addEventListener('click', () => this.prevSlide());
        this.nextBtn.addEventListener('click', () => this.nextSlide());
        this.autoBtn.addEventListener('click', () => this.toggleAutoPlay());
        
        // Indicator event listeners
        this.indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => this.goToSlide(index));
        });
        
        // Touch/swipe support
        this.addTouchSupport();
        
        // Keyboard support
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prevSlide();
            if (e.key === 'ArrowRight') this.nextSlide();
            if (e.key === ' ') { e.preventDefault(); this.toggleAutoPlay(); }
        });
        
        
        // Update initial state
        this.updateCarousel();
    }
    
    goToSlide(index) {
        this.currentSlide = index;
        this.updateCarousel();
    }
    
    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        this.updateCarousel();
    }
    
    prevSlide() {
        this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        this.updateCarousel();
    }
    
    updateCarousel() {
        // Calculate slide width (95% on mobile, 60% on desktop + gap)
        const isMobile = window.innerWidth < 768;
        const slideWidth = isMobile ? 95 : 60;
        const gap = isMobile ? 0.5 : 1; // gap in rem converted to percentage
        
        const translateX = -(this.currentSlide * (slideWidth + gap));
        this.carousel.style.transform = `translateX(${translateX}%)`;
        
        // Update indicators
        this.indicators.forEach((indicator, index) => {
            if (index === this.currentSlide) {
                indicator.className = 'w-2 h-2 rounded-full bg-primary transition-all duration-300';
            } else {
                indicator.className = 'w-2 h-2 rounded-full bg-muted-foreground/30 hover:bg-muted-foreground/50 transition-all duration-300';
            }
        });
        
        // Update button states
        this.prevBtn.disabled = false;
        this.nextBtn.disabled = false;
    }
    
    toggleAutoPlay() {
        this.isAutoPlay = !this.isAutoPlay;
        
        if (this.isAutoPlay) {
            this.autoBtn.innerHTML = `
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6"/>
                </svg>
                Pause
            `;
            this.autoBtn.classList.add('bg-primary', 'text-primary-foreground');
            this.autoBtn.classList.remove('border-border');
            
            this.autoPlayInterval = setInterval(() => {
                this.nextSlide();
            }, 4000);
        } else {
            this.autoBtn.innerHTML = `
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1"/>
                </svg>
                Auto
            `;
            this.autoBtn.classList.remove('bg-primary', 'text-primary-foreground');
            this.autoBtn.classList.add('border-border');
            
            clearInterval(this.autoPlayInterval);
        }
    }
    
    addTouchSupport() {
        let startX = 0;
        let currentX = 0;
        let isDragging = false;
        
        this.carousel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
        });
        
        this.carousel.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            currentX = e.touches[0].clientX;
        });
        
        this.carousel.addEventListener('touchend', () => {
            if (!isDragging) return;
            isDragging = false;
            
            const diffX = startX - currentX;
            const threshold = 50;
            
            if (Math.abs(diffX) > threshold) {
                if (diffX > 0) {
                    this.nextSlide();
                } else {
                    this.prevSlide();
                }
            }
        });
    }
    
}

// Initialize carousel when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new WidgetCarousel();
});

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
        preview.style.height = '400px'; // Altura fixa adequada
        const iframe = document.createElement('iframe');
        iframe.style.cssText = 'width:100%;height:100%;border:0;background:transparent';
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


