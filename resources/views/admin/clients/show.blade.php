@extends('layouts.ui')

@section('title', 'Detalhes do Cliente - Admin')
@section('page-header', 'Detalhes do Cliente')

@section('content')
<div class="w-full">
    <!-- Client Header -->
    <div class="flex justify-between items-start mb-8">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center">
                <span class="text-primary-foreground text-2xl font-bold">{{ substr($client->name, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-foreground">{{ $client->name }}</h1>
                <p class="text-muted-foreground">{{ $client->email }}</p>
                <div class="flex items-center space-x-2 mt-2">
                    <x-ui.badge variant="{{ $client->is_active ? 'success' : 'destructive' }}">
                        {{ $client->is_active ? 'Ativo' : 'Inativo' }}
                    </x-ui.badge>
                    <x-ui.badge variant="{{ $client->plan === 'enterprise' ? 'default' : ($client->plan === 'premium' ? 'secondary' : ($client->plan === 'basic' ? 'outline' : 'destructive')) }}">
                        {{ ucfirst($client->plan) }}
                    </x-ui.badge>
                    @if($client->isOnTrial())
                        <x-ui.badge variant="warning">Trial</x-ui.badge>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.clients.edit', $client) }}" class="bg-primary text-primary-foreground px-4 py-2 rounded-md hover:bg-primary/90 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            
            <div class="relative">
                <button onclick="toggleActions()" class="bg-secondary text-secondary-foreground px-4 py-2 rounded-md hover:bg-secondary/90 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                    Ações
                </button>
                
                <div id="actions-menu" class="hidden absolute right-0 mt-2 w-48 bg-card border border-border rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <button onclick="toggleStatus()" class="block w-full text-left px-4 py-2 text-sm text-foreground hover:bg-muted">
                            {{ $client->is_active ? 'Desativar' : 'Ativar' }} Cliente
                        </button>
                        <button onclick="changePlan()" class="block w-full text-left px-4 py-2 text-sm text-foreground hover:bg-muted">
                            Alterar Plano
                        </button>
                        <button onclick="regenerateApiKey()" class="block w-full text-left px-4 py-2 text-sm text-foreground hover:bg-muted">
                            Regenerar API Key
                        </button>
                        <button onclick="resetPassword()" class="block w-full text-left px-4 py-2 text-sm text-foreground hover:bg-muted">
                            Redefinir Senha
                        </button>
                        <hr class="my-1">
                        <button onclick="deleteClient()" class="block w-full text-left px-4 py-2 text-sm text-destructive hover:bg-destructive/10">
                            Excluir Cliente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-ui.card class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted-foreground">Sites</p>
                    <p class="text-2xl font-bold text-foreground">{{ $stats['total_sites'] }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted-foreground">Sessões</p>
                    <p class="text-2xl font-bold text-foreground">{{ number_format($stats['total_sessions']) }}</p>
                </div>
            </div>
        </x-ui.card>


        <x-ui.card class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted-foreground">Eventos</p>
                    <p class="text-2xl font-bold text-foreground">{{ number_format($stats['total_events']) }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Client Information -->
        <div class="lg:col-span-2">
            <x-ui.card class="p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações do Cliente</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Nome</label>
                        <p class="text-foreground">{{ $client->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Email</label>
                        <p class="text-foreground">{{ $client->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Plano</label>
                        <p class="text-foreground">{{ ucfirst($client->plan) }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Status</label>
                        <p class="text-foreground">{{ $client->is_active ? 'Ativo' : 'Inativo' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Criado em</label>
                        <p class="text-foreground">{{ $client->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Última atividade</label>
                        <p class="text-foreground">{{ $stats['last_activity'] ? \Carbon\Carbon::parse($stats['last_activity'])->format('d/m/Y H:i') : 'Nunca' }}</p>
                    </div>
                </div>

                @if($client->settings)
                    <div class="mt-6 pt-6 border-t border-border">
                        <h4 class="text-md font-semibold text-foreground mb-3">Informações Adicionais</h4>
                        
                        @if(isset($client->settings['company']))
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-muted-foreground mb-1">Empresa</label>
                                <p class="text-foreground">{{ $client->settings['company'] }}</p>
                            </div>
                        @endif
                        
                        @if(isset($client->settings['phone']))
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-muted-foreground mb-1">Telefone</label>
                                <p class="text-foreground">{{ $client->settings['phone'] }}</p>
                            </div>
                        @endif
                        
                        @if(isset($client->settings['notes']))
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-muted-foreground mb-1">Observações</label>
                                <p class="text-foreground">{{ $client->settings['notes'] }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </x-ui.card>

            <!-- Usage Statistics -->
            <x-ui.card class="p-6 mt-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Uso do Plano</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-foreground">Sessões (este mês)</span>
                            <span class="text-sm text-muted-foreground">{{ $usageStats['usage']['sessions'] }} / {{ $usageStats['limits']['monthly_sessions'] === -1 ? '∞' : number_format($usageStats['limits']['monthly_sessions']) }}</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full" style="width: {{ $usageStats['percentage']['sessions'] }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-foreground">Eventos (este mês)</span>
                            <span class="text-sm text-muted-foreground">{{ $usageStats['usage']['events'] }} / {{ $usageStats['limits']['monthly_events'] === -1 ? '∞' : number_format($usageStats['limits']['monthly_events']) }}</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2">
                            <div class="bg-success h-2 rounded-full" style="width: {{ $usageStats['percentage']['events'] }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-foreground">Sites</span>
                            <span class="text-sm text-muted-foreground">{{ $usageStats['usage']['sites'] }} / {{ $usageStats['limits']['sites'] === -1 ? '∞' : $usageStats['limits']['sites'] }}</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2">
                            <div class="bg-warning h-2 rounded-full" style="width: {{ $usageStats['percentage']['sites'] }}%"></div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Recent Activity -->
            <x-ui.card class="p-6 mt-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Atividade Recente</h3>
                
                @if($recentActivity->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentActivity as $activity)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-{{ $activity['type'] === 'site_created' ? 'primary' : 'success' }} rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-{{ $activity['type'] === 'site_created' ? 'primary-foreground' : 'success-foreground' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($activity['type'] === 'site_created')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            @endif
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-foreground">{{ $activity['message'] }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $activity['date']->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted-foreground">Nenhuma atividade recente.</p>
                @endif
            </x-ui.card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- API Information -->
            <x-ui.card class="p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">API Key</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Chave API</label>
                        <div class="flex items-center space-x-2">
                            <input type="password" value="{{ $client->api_key }}" readonly class="flex-1 px-3 py-2 border border-border rounded-md bg-muted text-sm font-mono" id="api-key">
                            <button onclick="toggleApiKey()" class="text-muted-foreground hover:text-foreground">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button onclick="copyApiKey()" class="text-muted-foreground hover:text-foreground">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <button onclick="regenerateApiKey()" class="w-full bg-warning text-warning-foreground px-4 py-2 rounded-md hover:bg-warning/90 transition-colors text-sm">
                        Regenerar API Key
                    </button>
                </div>
            </x-ui.card>

            <!-- Subscription Information -->
            <x-ui.card class="p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Assinatura</h3>
                
                <div class="space-y-3">
                    @if($client->trial_ends_at)
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Trial até</label>
                            <p class="text-foreground">{{ $client->trial_ends_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                    
                    @if($client->subscription_ends_at)
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Assinatura até</label>
                            <p class="text-foreground">{{ $client->subscription_ends_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Status da Assinatura</label>
                        @if($client->hasActiveSubscription())
                            <x-ui.badge variant="success">Ativa</x-ui.badge>
                        @elseif($client->isOnTrial())
                            <x-ui.badge variant="warning">Trial</x-ui.badge>
                        @else
                            <x-ui.badge variant="destructive">Expirada</x-ui.badge>
                        @endif
                    </div>
                </div>
            </x-ui.card>

            <!-- Quick Actions -->
            <x-ui.card class="p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ações Rápidas</h3>
                
                <div class="space-y-2">
                    <button onclick="toggleStatus()" class="w-full bg-{{ $client->is_active ? 'destructive' : 'success' }} text-{{ $client->is_active ? 'destructive-foreground' : 'success-foreground' }} px-4 py-2 rounded-md hover:bg-{{ $client->is_active ? 'destructive' : 'success' }}/90 transition-colors text-sm">
                        {{ $client->is_active ? 'Desativar' : 'Ativar' }} Cliente
                    </button>
                    
                    <button onclick="changePlan()" class="w-full bg-secondary text-secondary-foreground px-4 py-2 rounded-md hover:bg-secondary/90 transition-colors text-sm">
                        Alterar Plano
                    </button>
                    
                    <button onclick="resetPassword()" class="w-full bg-warning text-warning-foreground px-4 py-2 rounded-md hover:bg-warning/90 transition-colors text-sm">
                        Redefinir Senha
                    </button>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Change Plan Modal -->
<div id="change-plan-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-card border border-border rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-foreground mb-4">Alterar Plano</h3>
        
        <form method="POST" action="{{ route('admin.clients.update-plan', $client) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Novo Plano</label>
                    <select name="plan" class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="free" {{ $client->plan === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="basic" {{ $client->plan === 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="premium" {{ $client->plan === 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="enterprise" {{ $client->plan === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Dias de Assinatura (opcional)</label>
                    <input type="number" name="subscription_days" placeholder="Ex: 30" class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
            
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="closeChangePlanModal()" class="px-4 py-2 text-muted-foreground hover:text-foreground">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90">Alterar</button>
            </div>
        </form>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="reset-password-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-card border border-border rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-foreground mb-4">Redefinir Senha</h3>
        
        <form method="POST" action="{{ route('admin.clients.reset-password', $client) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Nova Senha</label>
                    <input type="password" name="new_password" required class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Confirmar Senha</label>
                    <input type="password" name="new_password_confirmation" required class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
            
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 text-muted-foreground hover:text-foreground">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90">Redefinir</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleActions() {
    const menu = document.getElementById('actions-menu');
    menu.classList.toggle('hidden');
}

function toggleApiKey() {
    const input = document.getElementById('api-key');
    input.type = input.type === 'password' ? 'text' : 'password';
}

function copyApiKey() {
    const input = document.getElementById('api-key');
    input.select();
    document.execCommand('copy');
    alert('API Key copiada para a área de transferência!');
}

function toggleStatus() {
    if (confirm('Tem certeza que deseja alterar o status deste cliente?')) {
        fetch(`{{ route('admin.clients.toggle-status', $client) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao alterar status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao alterar status do cliente');
        });
    }
}

function changePlan() {
    document.getElementById('change-plan-modal').classList.remove('hidden');
}

function closeChangePlanModal() {
    document.getElementById('change-plan-modal').classList.add('hidden');
}

function resetPassword() {
    document.getElementById('reset-password-modal').classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('reset-password-modal').classList.add('hidden');
}

function regenerateApiKey() {
    if (confirm('Tem certeza que deseja regenerar a API Key? A chave atual será invalidada.')) {
        fetch(`{{ route('admin.clients.regenerate-api-key', $client) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao regenerar API Key: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao regenerar API Key');
        });
    }
}

function deleteClient() {
    if (confirm('ATENÇÃO: Esta ação é irreversível! Todos os dados do cliente serão excluídos permanentemente. Tem certeza?')) {
        if (confirm('Última confirmação: Excluir cliente {{ $client->name }}?')) {
            fetch(`{{ route('admin.clients.destroy', $client) }}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route('admin.clients.index') }}';
                } else {
                    alert('Erro ao excluir cliente: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao excluir cliente');
            });
        }
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('bg-black')) {
        document.getElementById('change-plan-modal').classList.add('hidden');
        document.getElementById('reset-password-modal').classList.add('hidden');
    }
});
</script>
@endsection
