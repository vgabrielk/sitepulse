@extends('admin.layout')

@section('title', 'Admin Dashboard - SitePulse')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- System Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Clients -->
        <x-ui.card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Clientes</p>
                    <p class="text-3xl font-bold text-foreground">{{ $stats['total_clients'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-5.523-4.477-10-10-10S-3 12.477-3 18v2m20 0H3m16 0v-2a3 3 0 00-3-3H7a3 3 0 00-3 3v2m16 0v-2a3 3 0 00-3-3H7a3 3 0 00-3 3v2"/>
                    </svg>
                </div>
            </div>
        </x-ui.card>

        <!-- Total Sites -->
        <x-ui.card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total de Sites</p>
                    <p class="text-3xl font-bold text-foreground">{{ $stats['total_sites'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                    </svg>
                </div>
            </div>
        </x-ui.card>

        <!-- Total Sessions -->
        <x-ui.card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total de Sessões</p>
                    <p class="text-3xl font-bold text-foreground">{{ number_format($stats['total_sessions'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </x-ui.card>

        <!-- Total Events -->
        <x-ui.card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total de Eventos</p>
                    <p class="text-3xl font-bold text-foreground">{{ number_format($stats['total_events'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Clients -->
        <div class="lg:col-span-2">
            <x-ui.card title="Clientes Recentes" :actions="'<a href=\''.route('admin.clients.index').'\' class=\'text-sm text-primary hover:text-primary/80 font-medium\'>Ver todos</a>'">
                @if(isset($recentClients) && count($recentClients) > 0)
                    <div class="space-y-4">
                        @foreach($recentClients as $client)
                            <div class="flex items-center justify-between p-4 border border-border rounded-lg hover:bg-muted/50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-semibold text-primary">{{ substr($client['name'], 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $client['name'] }}</p>
                                        <p class="text-sm text-muted-foreground">{{ $client['email'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <x-ui.badge variant="{{ $client['plan'] === 'enterprise' ? 'primary' : ($client['plan'] === 'premium' ? 'success' : ($client['plan'] === 'basic' ? 'warning' : 'muted')) }}">
                                        {{ ucfirst($client['plan']) }}
                                    </x-ui.badge>
                                    <x-ui.badge variant="{{ $client['is_active'] ? 'success' : 'destructive' }}">
                                        {{ $client['is_active'] ? 'Ativo' : 'Inativo' }}
                                    </x-ui.badge>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-muted-foreground mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-5.523-4.477-10-10-10S-3 12.477-3 18v2m20 0H3m16 0v-2a3 3 0 00-3-3H7a3 3 0 00-3 3v2m16 0v-2a3 3 0 00-3-3H7a3 3 0 00-3 3v2"/>
                        </svg>
                        <p class="text-muted-foreground">Nenhum cliente encontrado</p>
                    </div>
                @endif
            </x-ui.card>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-6">
            <!-- Quick Actions Card -->
            <x-ui.card title="Ações Rápidas">
                <div class="space-y-3">
                    <x-ui.button href="{{ route('admin.clients.create') }}" variant="primary" class="w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Novo Cliente
                    </x-ui.button>
                    
                    <x-ui.button href="{{ route('admin.settings') }}" variant="outline" class="w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Configurações
                    </x-ui.button>
                </div>
            </x-ui.card>

            <!-- System Status -->
            @if(isset($systemStatus))
                <x-ui.card title="Status do Sistema">
                    <div class="space-y-3">
                        @foreach($systemStatus as $service => $status)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium capitalize">{{ ucfirst($service) }}</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 rounded-full {{ $status ? 'bg-success' : 'bg-destructive' }}"></div>
                                    <span class="text-xs {{ $status ? 'text-success' : 'text-destructive' }}">
                                        {{ $status ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>

    <!-- Charts Section -->
    @if(isset($chartData) && isset($planData))
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Activity Chart -->
            <x-ui.card title="Atividade dos Últimos 30 Dias">
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-muted-foreground mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="text-muted-foreground">Gráfico de atividade será implementado em breve</p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Plan Distribution -->
            <x-ui.card title="Distribuição de Planos">
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-muted-foreground mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                        <p class="text-muted-foreground">Gráfico de distribuição será implementado em breve</p>
                    </div>
                </div>
            </x-ui.card>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Dashboard specific scripts can be added here
    console.log('Admin Dashboard loaded');
</script>
@endpush