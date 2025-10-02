<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SitePulse Widgets')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: 'hsl(210 40% 98%)',
                        foreground: 'hsl(222.2 47.4% 11.2%)',
                        card: 'hsl(0 0% 100%)',
                        'card-foreground': 'hsl(222.2 47.4% 11.2%)',
                        primary: 'hsl(203 91% 53%)',
                        'primary-foreground': 'hsl(0 0% 100%)',
                        secondary: 'hsl(215 20% 65%)',
                        'secondary-foreground': 'hsl(0 0% 100%)',
                        muted: 'hsl(210 40% 96.1%)',
                        'muted-foreground': 'hsl(215 16% 47%)',
                        accent: 'hsl(270 60% 60%)',
                        'accent-foreground': 'hsl(0 0% 100%)',
                        success: 'hsl(142 76% 36%)',
                        'success-foreground': 'hsl(0 0% 100%)',
                        warning: 'hsl(38 92% 50%)',
                        'warning-foreground': 'hsl(0 0% 100%)',
                        destructive: 'hsl(0 84% 60%)',
                        'destructive-foreground': 'hsl(0 0% 100%)',
                        border: 'hsl(214 32% 91%)',
                        input: 'hsl(214 32% 91%)',
                        ring: 'hsl(203 91% 53%)',
                    },
                    borderRadius: {
                        DEFAULT: '0.5rem',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: hsl(210 40% 98%); color: hsl(222.2 47.4% 11.2%); }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .toast-enter { animation: slideIn 0.3s ease-out; }
        .modal-backdrop {
            background-color: rgba(255,255,255,0.35);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.5;} }
        .skeleton-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 1s linear infinite; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">
    <nav class="bg-card border-b border-border sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-primary">SitePulse</a>
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('dashboard') }}" class="text-foreground hover:text-primary transition">Dashboard</a>
                        <a href="{{ route('sites.index') }}" class="text-foreground hover:text-primary transition">Sites</a>
                        <a href="{{ route('widgets.gallery') }}" class="text-foreground hover:text-primary transition">Widgets</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition">Login</a>
                    @endauth
                    <button onclick="toggleSidebar()" class="md:hidden p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div id="sidebar" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden">
        <div class="fixed right-0 top-0 bottom-0 w-64 bg-card shadow-xl transform transition-transform">
            <div class="p-4 border-b border-border flex justify-between items-center">
                <h2 class="font-semibold">Menu</h2>
                <button onclick="toggleSidebar()" class="p-2">✕</button>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-muted transition">Dashboard</a>
                <a href="{{ route('sites.index') }}" class="block px-4 py-2 rounded-lg hover:bg-muted transition">Sites</a>
                <div>
                    <div class="px-4 py-2 text-sm font-semibold text-muted-foreground">Widgets</div>
                    <a href="#" data-widget="reviews" class="widget-link block px-6 py-2 rounded-lg hover:bg-muted transition">Reviews</a>
                    <a href="#" data-widget="faq" class="widget-link block px-6 py-2 rounded-lg hover:bg-muted transition">FAQ Inteligente</a>
                    <a href="#" data-widget="before-after" class="widget-link block px-6 py-2 rounded-lg hover:bg-muted transition">Antes & Depois</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-success p-4 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-success mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <div><h4 class="font-semibold text-success">Sucesso</h4><p class="text-sm text-foreground">{{ session('success') }}</p></div>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-destructive p-4 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-destructive mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <div><h4 class="font-semibold text-destructive">Erro</h4><p class="text-sm text-foreground">{{ session('error') }}</p></div>
                </div>
            </div>
        @endif

        @yield('page-header')
        @yield('content')
    </div>

    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop">
        <div class="bg-card rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">@yield('modal-title', 'Título do Modal')</h3>
                <button onclick="closeModal()" class="text-muted-foreground hover:text-foreground">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="text-muted-foreground mb-6">@yield('modal-content')</div>
            <div class="flex justify-end gap-3">
                <button onclick="closeModal()" class="px-4 py-2 border border-border rounded-lg hover:bg-muted transition">Cancelar</button>
                <button onclick="closeModal()" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition">Confirmar</button>
            </div>
        </div>
    </div>

    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <div id="widget-site-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop">
        <div class="bg-card rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Escolha o site para gerenciar</h3>
                <button onclick="closeWidgetSiteModal()" class="text-muted-foreground hover:text-foreground">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @php($userSites = auth()->user()?->client?->sites()->select('id','name','domain')->get() ?? collect())
            @if($userSites->count())
                <div class="space-y-2" id="widget-site-list">
                    @foreach($userSites as $s)
                        <button class="w-full text-left px-4 py-3 border border-border rounded-lg hover:bg-muted transition flex items-center justify-between" data-site-id="{{ $s->id }}">
                            <div>
                                <div class="font-medium">{{ $s->name }}</div>
                                <div class="text-xs text-muted-foreground">{{ $s->domain }}</div>
                            </div>
                            <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endforeach
                </div>
            @else
                <x-ui.alert variant="warning" title="Nenhum site cadastrado">
                    Crie um site primeiro para vincular seus widgets.
                </x-ui.alert>
                <div class="mt-4"><a class="inline-block" href="{{ route('sites.create') }}"><x-ui.button>Novo Site</x-ui.button></a></div>
            @endif
        </div>
    </div>

    <footer class="bg-card border-t border-border mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center text-muted-foreground">
                <p>© {{ date('Y') }} SitePulse. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleSidebar(){ const s=document.getElementById('sidebar'); s.classList.toggle('hidden'); }
        function openModal(){ const m=document.getElementById('modal'); m.classList.remove('hidden'); m.classList.add('flex'); }
        function closeModal(){ const m=document.getElementById('modal'); m.classList.add('hidden'); m.classList.remove('flex'); }
        function showToast(type,msg){
            const c=document.getElementById('toast-container'); const t=document.createElement('div');
            let bg='bg-primary text-primary-foreground', icon='ℹ';
            if(type==='success'){ bg='bg-success text-success-foreground'; icon='✓'; }
            else if(type==='error'){ bg='bg-destructive text-destructive-foreground'; icon='✕'; }
            t.className = `${bg} px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 toast-enter`;
            t.innerHTML = `<span class="text-xl font-bold">${icon}</span><span>${msg||''}</span>`;
            c.appendChild(t);
            setTimeout(()=>{ t.style.opacity='0'; t.style.transform='translateX(100%)'; t.style.transition='all 0.3s ease-out'; setTimeout(()=>t.remove(),300); },3000);
        }
        document.getElementById('modal')?.addEventListener('click', function(e){ if(e.target===this){ closeModal(); } });

        // Widget site selection modal logic
        let selectedWidget = null;
        function openWidgetSiteModal(){ const m=document.getElementById('widget-site-modal'); m.classList.remove('hidden'); m.classList.add('flex'); }
        function closeWidgetSiteModal(){ const m=document.getElementById('widget-site-modal'); m.classList.add('hidden'); m.classList.remove('flex'); selectedWidget=null; }
        document.getElementById('widget-site-modal')?.addEventListener('click', function(e){ if(e.target===this){ closeWidgetSiteModal(); } });

        // legacy dropdown removed; widgets now have a dedicated gallery page
    </script>
    @stack('scripts')
</body>
</html>


