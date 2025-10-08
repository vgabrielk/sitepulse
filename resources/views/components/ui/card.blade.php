<div {{ $attributes->merge(['class' => 'bg-card border border-border rounded-lg']) }}>
    @isset($title)
        <div class="p-4 border-b border-border flex items-center justify-between">
            <h3 class="text-base font-semibold">{{ $title }}</h3>
            @if(isset($actions))
                {!! $actions !!}
            @endif
        </div>
    @endisset
    <div class="p-4">
        {{ $slot }}
    </div>
</div>



