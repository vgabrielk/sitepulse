@extends('layouts.blank')

@section('content')
<div class="p-4 w-full h-full flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-slate-100 transform transition-all duration-500 w-full max-w-md">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-xl">A</div>
            <div class="flex-1">
                <h3 class="font-bold text-lg text-slate-900">Ana Costa</h3>
                <p class="text-sm text-slate-600">Beauty Lab</p>
                <div class="flex gap-1 mt-2">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 fill-amber-400 text-amber-400 transition-transform hover:scale-125" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    @endfor
                </div>
            </div>
        </div>
        <p class="text-slate-700 text-lg leading-relaxed mb-6">"O slider Antes & Depois é show. Clientes adoram!"</p>
        <div class="flex justify-between">
            <button class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/>
                </svg>
            </button>
            <div class="flex gap-2">
                <div class="w-2 h-2 rounded-full transition-all bg-slate-300"></div>
                <div class="w-2 h-2 rounded-full transition-all bg-slate-300"></div>
                <div class="w-2 h-2 rounded-full transition-all bg-blue-600 w-6"></div>
            </div>
            <button class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/>
                </svg>
            </button>
        </div>
    </div>
</div>
@endsection
