@extends('layouts.ui')

@section('title', 'Editar Cliente - Admin')
@section('page-header', 'Editar Cliente')

@section('content')
<div class="w-full">
    <x-ui.card class="p-6">
        <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações Básicas</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-foreground mb-2">Nome *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $client->name) }}" required
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-destructive @enderror">
                        @error('name')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-foreground mb-2">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $client->email) }}" required
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('email') border-destructive @enderror">
                        @error('email')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Plan and Status -->
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-4">Plano e Status</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="plan" class="block text-sm font-medium text-foreground mb-2">Plano *</label>
                        <select id="plan" name="plan" required
                                class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('plan') border-destructive @enderror">
                            <option value="">Selecione um plano</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan }}" {{ old('plan', $client->plan) === $plan ? 'selected' : '' }}>
                                    {{ ucfirst($plan) }}
                                </option>
                            @endforeach
                        </select>
                        @error('plan')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }}
                                   class="rounded border-border text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-foreground">Cliente ativo</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Trial and Subscription -->
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-4">Trial e Assinatura</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="trial_ends_at" class="block text-sm font-medium text-foreground mb-2">Trial até</label>
                        <input type="datetime-local" id="trial_ends_at" name="trial_ends_at" 
                               value="{{ old('trial_ends_at', $client->trial_ends_at ? $client->trial_ends_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('trial_ends_at') border-destructive @enderror">
                        <p class="text-xs text-muted-foreground mt-1">Deixe vazio para remover trial</p>
                        @error('trial_ends_at')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="subscription_ends_at" class="block text-sm font-medium text-foreground mb-2">Assinatura até</label>
                        <input type="datetime-local" id="subscription_ends_at" name="subscription_ends_at"
                               value="{{ old('subscription_ends_at', $client->subscription_ends_at ? $client->subscription_ends_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('subscription_ends_at') border-destructive @enderror">
                        <p class="text-xs text-muted-foreground mt-1">Deixe vazio para remover assinatura</p>
                        @error('subscription_ends_at')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações Adicionais</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="company" class="block text-sm font-medium text-foreground mb-2">Empresa</label>
                        <input type="text" id="company" name="company" value="{{ old('company', $client->settings['company'] ?? '') }}"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('company') border-destructive @enderror">
                        @error('company')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-foreground mb-2">Telefone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $client->settings['phone'] ?? '') }}"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('phone') border-destructive @enderror">
                        @error('phone')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-foreground mb-2">Observações</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('notes') border-destructive @enderror">{{ old('notes', $client->settings['notes'] ?? '') }}</textarea>
                    @error('notes')
                        <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Plan Limits Customization -->
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-4">Limites Personalizados</h3>
                <p class="text-sm text-muted-foreground mb-4">Personalize os limites do plano (deixe vazio para usar os padrões)</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="plan_limits_sessions" class="block text-sm font-medium text-foreground mb-2">Sessões mensais</label>
                        <input type="number" id="plan_limits_sessions" name="plan_limits[monthly_sessions]"
                               value="{{ old('plan_limits.monthly_sessions', $client->plan_limits['monthly_sessions'] ?? '') }}"
                               min="-1" placeholder="Ilimitado: -1"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label for="plan_limits_events" class="block text-sm font-medium text-foreground mb-2">Eventos mensais</label>
                        <input type="number" id="plan_limits_events" name="plan_limits[monthly_events]"
                               value="{{ old('plan_limits.monthly_events', $client->plan_limits['monthly_events'] ?? '') }}"
                               min="-1" placeholder="Ilimitado: -1"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label for="plan_limits_sites" class="block text-sm font-medium text-foreground mb-2">Sites</label>
                        <input type="number" id="plan_limits_sites" name="plan_limits[sites]"
                               value="{{ old('plan_limits.sites', $client->plan_limits['sites'] ?? '') }}"
                               min="-1" placeholder="Ilimitado: -1"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Current Usage -->
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-4">Uso Atual</h3>
                
                <div class="bg-muted p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Sites</label>
                            <p class="text-foreground font-semibold">{{ $client->sites()->count() }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Sessões (mês)</label>
                            <p class="text-foreground font-semibold">{{ $client->sites()->withCount(['sessions' => function($query) { $query->where('started_at', '>=', now()->startOfMonth()); }])->get()->sum('sessions_count') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Eventos (mês)</label>
                            <p class="text-foreground font-semibold">
                                {{ \Illuminate\Support\Facades\DB::table('events')
                                    ->join('visits', 'events.visit_id', '=', 'visits.id')
                                    ->join('analytics_sessions', 'visits.session_id', '=', 'analytics_sessions.id')
                                    ->whereIn('analytics_sessions.site_id', $client->sites()->pluck('id'))
                                    ->where('events.occurred_at', '>=', now()->startOfMonth())
                                    ->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-border">
                <a href="{{ route('admin.clients.show', $client) }}" class="px-6 py-2 text-muted-foreground hover:text-foreground transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
