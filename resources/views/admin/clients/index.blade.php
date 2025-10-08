@extends('layouts.ui')

@section('title', 'Gerenciamento de Clientes - Admin')
@section('page-header', 'Gerenciamento de Clientes')

@section('content')
<div class="w-full">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <x-ui.card class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-5.523-4.477-10-10-10S-3 12.477-3 18v2m20 0H3m16 0v-2a3 3 0 00-3-3H7a3 3 0 00-3 3v2m16 0v-2a3 3 0 00-3-3H7a3 3 0 00-3 3v2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted-foreground">Total de Clientes</p>
                    <p class="text-2xl font-bold text-foreground">{{ $stats['total_clients'] }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted-foreground">Clientes Ativos</p>
                    <p class="text-2xl font-bold text-foreground">{{ $stats['active_clients'] }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted-foreground">Em Trial</p>
                    <p class="text-2xl font-bold text-foreground">{{ $stats['trial_clients'] }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted-foreground">Planos</p>
                    <p class="text-2xl font-bold text-foreground">{{ count($stats['plan_distribution']) }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Filters and Search -->
    <x-ui.card class="p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nome, email ou API key..."
                       class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Plano</label>
                <select name="plan" class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Todos os planos</option>
                    <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Free</option>
                    <option value="basic" {{ request('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                    <option value="premium" {{ request('plan') === 'premium' ? 'selected' : '' }}>Premium</option>
                    <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Todos os status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirado</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary text-primary-foreground px-4 py-2 rounded-md hover:bg-primary/90 transition-colors">
                    Filtrar
                </button>
            </div>
        </form>
    </x-ui.card>

    <!-- Actions Bar -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.clients.create') }}" class="bg-primary text-primary-foreground px-4 py-2 rounded-md hover:bg-primary/90 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Novo Cliente
            </a>

            <button onclick="toggleBulkActions()" class="bg-secondary text-secondary-foreground px-4 py-2 rounded-md hover:bg-secondary/90 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Ações em Lote
            </button>
        </div>

        <div class="flex items-center space-x-2">
            <span class="text-sm text-muted-foreground">{{ $clients->total() }} cliente(s) encontrado(s)</span>
        </div>
    </div>

    <!-- Bulk Actions Panel -->
    <div id="bulk-actions" class="hidden bg-muted p-4 rounded-lg mb-6">
        <form method="POST" action="{{ route('admin.clients.bulk-action') }}" id="bulk-form">
            @csrf
            <div class="flex items-center space-x-4">
                <select name="action" class="px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Selecione uma ação</option>
                    <option value="activate">Ativar</option>
                    <option value="deactivate">Desativar</option>
                    <option value="change_plan">Alterar Plano</option>
                    <option value="delete">Excluir</option>
                </select>

                <select name="plan" class="px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent" style="display: none;">
                    <option value="free">Free</option>
                    <option value="basic">Basic</option>
                    <option value="premium">Premium</option>
                    <option value="enterprise">Enterprise</option>
                </select>

                <button type="submit" class="bg-destructive text-destructive-foreground px-4 py-2 rounded-md hover:bg-destructive/90 transition-colors">
                    Executar
                </button>

                <button type="button" onclick="toggleBulkActions()" class="bg-secondary text-secondary-foreground px-4 py-2 rounded-md hover:bg-secondary/90 transition-colors">
                    Cancelar
                </button>
            </div>
        </form>
    </div>

    <!-- Clients Table -->
    <x-ui.card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-muted">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll()" class="rounded border-border">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_by') === 'name' && request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-foreground">
                                Cliente
                                @if(request('sort_by') === 'name')
                                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'plan', 'sort_order' => request('sort_by') === 'plan' && request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-foreground">
                                Plano
                                @if(request('sort_by') === 'plan')
                                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Sites</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Reviews</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'is_active', 'sort_order' => request('sort_by') === 'is_active' && request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-foreground">
                                Status
                                @if(request('sort_by') === 'is_active')
                                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_by') === 'created_at' && request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-foreground">
                                Criado em
                                @if(request('sort_by') === 'created_at')
                                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($clients as $client)
                        <tr class="hover:bg-muted/50">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="client_ids[]" value="{{ $client->id }}" class="client-checkbox rounded border-border">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center mr-3">
                                        <span class="text-primary-foreground text-sm font-semibold">{{ substr($client->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-foreground">{{ $client->name }}</div>
                                        <div class="text-sm text-muted-foreground">{{ $client->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-ui.badge variant="{{ $client->plan === 'enterprise' ? 'default' : ($client->plan === 'premium' ? 'secondary' : ($client->plan === 'basic' ? 'outline' : 'destructive')) }}">
                                    {{ ucfirst($client->plan) }}
                                </x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-sm text-muted-foreground">
                                {{ $client->sites_count }}
                            </td>
                            <td class="px-6 py-4 text-sm text-muted-foreground">
                                {{ $client->reviews_count }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <x-ui.badge variant="{{ $client->is_active ? 'success' : 'destructive' }}">
                                        {{ $client->is_active ? 'Ativo' : 'Inativo' }}
                                    </x-ui.badge>
                                    @if($client->isOnTrial())
                                        <x-ui.badge variant="warning">Trial</x-ui.badge>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-muted-foreground">
                                {{ $client->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.clients.show', $client) }}" class="text-primary hover:text-primary/80">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.clients.edit', $client) }}" class="text-secondary hover:text-secondary/80">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button onclick="toggleStatus({{ $client->id }})" class="text-{{ $client->is_active ? 'destructive' : 'success' }} hover:text-{{ $client->is_active ? 'destructive' : 'success' }}/80">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $client->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-muted-foreground">
                                <svg class="w-12 h-12 mx-auto mb-4 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-5.523-4.477-10-10-10S-3 12.477-3 18v2m20 0H3m16 0v-2a3 3 0 00-3-3H7a3 3 0 00-3 3v2m16 0v-2a3 3 0 00-3-3H7a3 3 0 00-3 3v2"/>
                                </svg>
                                <p class="text-lg font-medium">Nenhum cliente encontrado</p>
                                <p class="text-sm">Tente ajustar os filtros ou criar um novo cliente.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($clients->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $clients->links() }}
            </div>
        @endif
    </x-ui.card>
</div>

<script>
function toggleBulkActions() {
    const panel = document.getElementById('bulk-actions');
    panel.classList.toggle('hidden');
}

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.client-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function toggleStatus(clientId) {
    if (confirm('Tem certeza que deseja alterar o status deste cliente?')) {
        fetch(`/admin/clients/${clientId}/toggle-status`, {
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

// Show/hide plan selector based on action
document.querySelector('select[name="action"]').addEventListener('change', function() {
    const planSelector = document.querySelector('select[name="plan"]');
    if (this.value === 'change_plan') {
        planSelector.style.display = 'block';
    } else {
        planSelector.style.display = 'none';
    }
});
</script>
@endsection
