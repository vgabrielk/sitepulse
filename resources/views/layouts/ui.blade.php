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
        
        /* Sidebar styles */
        .sidebar-collapsed { width: 5rem !important; }
        .sidebar-expanded { width: 16rem !important; }
        .sidebar-item { 
            transition: all 0.2s ease; 
            position: relative; 
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: inherit;
        }
        .sidebar-item:hover { background-color: hsl(210 40% 94%); }
        .sidebar-item.active { background-color: hsl(203 91% 53%); color: white; }
        .sidebar-item.active:hover { background-color: hsl(203 91% 48%); }
        .sidebar-text { 
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
        }
        
        /* Tooltip styles */
        .tooltip {
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 12px;
            background: hsl(222.2 47.4% 11.2%);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            z-index: 1000;
            pointer-events: none;
        }
        
        .tooltip::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-right-color: hsl(222.2 47.4% 11.2%);
        }
        
        .sidebar-collapsed .sidebar-item:hover .tooltip {
            opacity: 1;
            visibility: visible;
        }
        
        @media (max-width: 768px) {
            #sidebar { 
                width: 16rem !important; 
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            #sidebar:not(.sidebar-mobile) { 
                transform: translateX(0); 
            }
            .sidebar-text { display: block !important; opacity: 1 !important; width: auto !important; }
            .sidebar-item { justify-content: flex-start !important; }
            #main-content { margin-left: 0 !important; }
        }
        
        @media (min-width: 768px) {
            .sidebar-collapsed .sidebar-text { 
                opacity: 0; 
                width: 0; 
                overflow: hidden;
                margin: 0;
            }
            .sidebar-expanded .sidebar-text { 
                opacity: 1; 
                width: auto;
            }
            .sidebar-collapsed .sidebar-item { 
                justify-content: center;
                gap: 0;
                padding: 8px;
            }
            .sidebar-expanded .sidebar-item { 
                justify-content: flex-start;
                gap: 12px;
                padding: 8px 12px;
            }
        }
    </style>
    @stack('styles')
    @auth
    <script>
        // Set initial sidebar state before page renders to prevent flash
        (function() {
            const savedState = localStorage.getItem('sidebarExpanded');
            const isExpanded = savedState !== null ? savedState === 'true' : true;
            
            // Set global variable immediately
            window.sidebarExpanded = isExpanded;
            
            if (window.innerWidth >= 768) {
                const style = document.createElement('style');
                if (isExpanded) {
                    style.textContent = `
                        #sidebar { width: 16rem !important; transition: none !important; }
                        #main-content { margin-left: 16rem !important; transition: none !important; }
                        .sidebar-text { display: block !important; opacity: 1 !important; width: auto !important; transition: none !important; }
                        .sidebar-item { justify-content: flex-start !important; gap: 12px !important; padding: 8px 12px !important; transition: none !important; }
                    `;
                } else {
                    style.textContent = `
                        #sidebar { width: 5rem !important; transition: none !important; }
                        #main-content { margin-left: 5rem !important; transition: none !important; }
                        .sidebar-text { display: none !important; opacity: 0 !important; width: 0 !important; overflow: hidden !important; transition: none !important; }
                        .sidebar-item { justify-content: center !important; gap: 0 !important; padding: 8px !important; transition: none !important; }
                    `;
                }
                document.head.appendChild(style);
            }
        })();
    </script>
    @endauth
</head>
<body class="min-h-screen bg-background">
    @auth
    <!-- Sidebar -->
    <div id="sidebar" class="fixed left-0 top-0 h-full bg-card border-r border-border z-50 transition-all duration-300 sidebar-mobile sidebar-expanded">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 border-b border-border">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="font-bold text-lg text-primary sidebar-text">SitePulse</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
                </svg>
                <span class="sidebar-text">Dashboard</span>
                <div class="tooltip">Dashboard</div>
            </a>

            <a href="{{ route('sites.index') }}" class="sidebar-item {{ request()->routeIs('sites.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                </svg>
                <span class="sidebar-text">Sites</span>
                <div class="tooltip">Sites</div>
            </a>

            <a href="{{ route('widgets.gallery') }}" class="sidebar-item {{ request()->routeIs('widgets.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="sidebar-text">Widgets</span>
                <div class="tooltip">Widgets</div>
            </a>

            <a href="{{ route('reviews.index') }}" class="sidebar-item {{ request()->routeIs('reviews.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <span class="sidebar-text">Reviews</span>
                <div class="tooltip">Reviews</div>
            </a>
        </nav>

        <!-- Toggle Button (Desktop) -->
        <button id="sidebar-toggle-btn" class="hidden md:flex absolute -right-3 top-20 w-6 h-6 bg-card border border-border rounded-full items-center justify-center hover:bg-muted transition-colors z-50 cursor-pointer">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <!-- User Section (Bottom) -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-border">
            @auth
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                        <span class="text-primary-foreground text-sm font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="sidebar-text flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-muted-foreground truncate">{{ auth()->user()->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="sidebar-text">@csrf
                        <button class="p-1 hover:bg-muted rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>

    <!-- Mobile Header -->
    <div class="md:hidden bg-card border-b border-border sticky top-0 z-40">
        <div class="flex items-center justify-between px-4 h-16">
            <button onclick="toggleSidebarMobile()" class="p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-bold text-lg text-primary">SitePulse</span>
            <div class="w-10"></div>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeSidebarMobile()"></div>
    @endauth

    <!-- Main Content -->
    <div id="main-content" class="transition-all duration-300 @auth md:ml-64 @endauth min-h-screen">
        <div class="p-6">
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
    </div>

    <!-- Modals and other components remain the same -->
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

    <script>
        // Sidebar functionality
        let sidebarExpanded = window.sidebarExpanded || true;
        
        function toggleSidebarDesktop() {
            console.log('🔄 Toggling sidebar from:', sidebarExpanded, 'to:', !sidebarExpanded);
            
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = sidebar.querySelector('button svg');
            const sidebarTexts = sidebar.querySelectorAll('.sidebar-text');
            const sidebarItems = sidebar.querySelectorAll('.sidebar-item');
            
            if (!sidebar || !mainContent) {
                console.error('❌ Sidebar elements not found!');
                return;
            }
            
            // Toggle state
            sidebarExpanded = !sidebarExpanded;
            
            // Save preference to localStorage
            localStorage.setItem('sidebarExpanded', sidebarExpanded.toString());
            
            // Apply the new state immediately with FORCE
            applySidebarState(sidebar, mainContent, sidebarTexts, sidebarItems, toggleBtn, sidebarExpanded);
            
            console.log('✅ Toggle completed! New state:', sidebarExpanded);
        }
        
        function toggleSidebarMobile() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            if (sidebar.classList.contains('sidebar-mobile')) {
                sidebar.classList.remove('sidebar-mobile');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('sidebar-mobile');
                overlay.classList.add('hidden');
            }
        }
        
        function closeSidebarMobile() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            sidebar.classList.add('sidebar-mobile');
            overlay.classList.add('hidden');
        }
        
        // Initialize sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            // Only initialize if sidebar exists (user is logged in)
            if (!sidebar) return;
            
            const sidebarTexts = sidebar.querySelectorAll('.sidebar-text');
            const sidebarItems = sidebar.querySelectorAll('.sidebar-item');
            const toggleBtn = sidebar.querySelector('button svg');
            
            // Check localStorage for saved preference and sync with global variable
            const savedState = localStorage.getItem('sidebarExpanded');
            const currentState = savedState !== null ? savedState === 'true' : true;
            sidebarExpanded = currentState; // Sync with current state
            
            // Force apply the current state immediately
            if (window.innerWidth >= 768) {
                applySidebarState(sidebar, mainContent, sidebarTexts, sidebarItems, toggleBtn, sidebarExpanded);
            }
            
            // Add event listener to toggle button
            const toggleButton = document.getElementById('sidebar-toggle-btn');
            if (toggleButton) {
                // Test if button is clickable
                toggleButton.addEventListener('mouseenter', function() {
                    console.log('Button hover detected - button is accessible');
                });
                
                toggleButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('🔄 Toggle button clicked!');
                    toggleSidebarDesktop();
                });
                console.log('Event listener added to toggle button');
            } else {
                console.error('Toggle button not found!');
            }
            
            // Re-enable transitions after initial load to prevent flash
            setTimeout(function() {
                if (sidebar) {
                    sidebar.style.transition = 'all 0.3s ease';
                }
                if (mainContent) {
                    mainContent.style.transition = 'all 0.3s ease';
                }
                sidebarItems.forEach(item => {
                    item.style.transition = 'all 0.2s ease';
                });
                sidebarTexts.forEach(text => {
                    text.style.transition = 'all 0.2s ease';
                });
            }, 100);
        });
        
        // Helper function to apply sidebar state
        function applySidebarState(sidebar, mainContent, sidebarTexts, sidebarItems, toggleBtn, isExpanded) {
            console.log('Applying sidebar state:', isExpanded);
            
            // FORÇA aplicação com !important via style inline
            if (isExpanded) {
                // Expandir sidebar - FORÇAR com !important
                sidebar.style.setProperty('width', '16rem', 'important');
                mainContent.style.setProperty('margin-left', '16rem', 'important');
                if (toggleBtn) toggleBtn.style.transform = 'rotate(180deg)';
                
                // Mostrar textos - FORÇAR
                sidebarTexts.forEach(text => {
                    text.style.setProperty('display', 'block', 'important');
                    text.style.setProperty('opacity', '1', 'important');
                    text.style.setProperty('width', 'auto', 'important');
                    text.style.setProperty('overflow', 'visible', 'important');
                });
                
                // Ajustar items - FORÇAR
                sidebarItems.forEach(item => {
                    item.style.setProperty('justify-content', 'flex-start', 'important');
                    item.style.setProperty('gap', '12px', 'important');
                    item.style.setProperty('padding', '8px 12px', 'important');
                });
                
                // Aplicar classes
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                mainContent.classList.remove('md:ml-20');
                mainContent.classList.add('md:ml-64');
                
                console.log('✅ Sidebar EXPANDIDA - Width:', sidebar.style.width);
            } else {
                // Colapsar sidebar - FORÇAR com !important
                sidebar.style.setProperty('width', '5rem', 'important');
                mainContent.style.setProperty('margin-left', '5rem', 'important');
                if (toggleBtn) toggleBtn.style.transform = 'rotate(0deg)';
                
                // Esconder textos - FORÇAR
                sidebarTexts.forEach(text => {
                    text.style.setProperty('display', 'none', 'important');
                    text.style.setProperty('opacity', '0', 'important');
                    text.style.setProperty('width', '0', 'important');
                    text.style.setProperty('overflow', 'hidden', 'important');
                });
                
                // Ajustar items - FORÇAR
                sidebarItems.forEach(item => {
                    item.style.setProperty('justify-content', 'center', 'important');
                    item.style.setProperty('gap', '0', 'important');
                    item.style.setProperty('padding', '8px', 'important');
                });
                
                // Aplicar classes
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.remove('md:ml-64');
                mainContent.classList.add('md:ml-20');
                
                console.log('✅ Sidebar COLAPSADA - Width:', sidebar.style.width);
            }
            
            // Força repaint do browser
            sidebar.offsetHeight;
            mainContent.offsetHeight;
        }
        
        // Modal functions
        function openModal(){ const m=document.getElementById('modal'); m.classList.remove('hidden'); m.classList.add('flex'); }
        function closeModal(){ const m=document.getElementById('modal'); m.classList.add('hidden'); m.classList.remove('flex'); }
        
        // Toast function
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
        
        // Modal click outside to close
        document.getElementById('modal')?.addEventListener('click', function(e){ if(e.target===this){ closeModal(); } });

        // Widget site selection modal logic
        let selectedWidget = null;
        function openWidgetSiteModal(){ const m=document.getElementById('widget-site-modal'); m.classList.remove('hidden'); m.classList.add('flex'); }
        function closeWidgetSiteModal(){ const m=document.getElementById('widget-site-modal'); m.classList.add('hidden'); m.classList.remove('flex'); selectedWidget=null; }
        document.getElementById('widget-site-modal')?.addEventListener('click', function(e){ if(e.target===this){ closeWidgetSiteModal(); } });
    </script>
    @stack('scripts')
</body>
</html>


