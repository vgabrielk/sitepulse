@extends('layouts.blank')

@section('title', 'Widget - Antes & Depois')

@section('content')
<section class="relative p-4 w-full h-full">
    <div class="relative max-w-5xl mx-auto h-full">
        <div class="bg-card rounded-2xl shadow-xl p-6 border border-border h-full">
            <div class="relative h-full rounded-xl overflow-hidden bg-muted">
                <img src="https://s2.glbimg.com/03-R_F2xgE1FwPTkmL4Tvle6HUE=/e.glbimg.com/og/ed/f/original/2019/11/06/jojo.png" alt="Antes" class="absolute inset-0 w-full h-full object-cover">
                <img id="afterImage" src="https://robertajungmann.com.br/wp-content/uploads/2025/02/Destaque-Site-2-5.jpg" alt="Depois" class="absolute inset-0 w-full h-full object-cover" style="clip-path: inset(0 50% 0 0)">

                <input id="baSlider" type="range" min="0" max="100" value="50" class="absolute inset-0 w-full h-full opacity-0 cursor-ew-resize z-10">

                <div id="baHandle" class="absolute top-0 bottom-0 w-px bg-white/90 shadow pointer-events-none" style="left: 50%">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center">
                        <div class="flex gap-0.5 text-muted-foreground">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var slider = document.getElementById('baSlider');
    var afterImg = document.getElementById('afterImage');
    var handle = document.getElementById('baHandle');
    if (!slider || !afterImg || !handle) return;

    function update(val){
        var pct = Math.max(0, Math.min(100, parseInt(val || 0, 10)));
        afterImg.style.clipPath = 'inset(0 ' + (100 - pct) + '% 0 0)';
        handle.style.left = pct + '%';
    }

    slider.addEventListener('input', function(){ update(this.value); });
    update(slider.value);
});
</script>
@endpush
@endsection


