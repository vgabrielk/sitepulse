@extends('layouts.ui')

@section('title', 'Criar Cliente - Admin')
@section('page-header', 'Criar Novo Cliente')

@section('content')
<div class="w-full max-w-4xl mx-auto">
    <x-ui.card class="p-6">
        <form method="POST" action="{{ route('admin.clients.store') }}" class="space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-4">Informações Básicas</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-foreground mb-2">Nome *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-destructive @enderror">
                        @error('name')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-foreground mb-2">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
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
                                <option value="{{ $plan }}" {{ old('plan') === $plan ? 'selected' : '' }}>
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
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
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
                        <label for="trial_days" class="block text-sm font-medium text-foreground mb-2">Dias de Trial (opcional)</label>
                        <input type="number" id="trial_days" name="trial_days" value="{{ old('trial_days') }}" min="1" max="365"
                               placeholder="Ex: 14"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('trial_days') border-destructive @enderror">
                        <p class="text-xs text-muted-foreground mt-1">Deixe vazio para não definir trial</p>
                        @error('trial_days')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="subscription_days" class="block text-sm font-medium text-foreground mb-2">Dias de Assinatura (opcional)</label>
                        <input type="number" id="subscription_days" name="subscription_days" value="{{ old('subscription_days') }}" min="1" max="3650"
                               placeholder="Ex: 30"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('subscription_days') border-destructive @enderror">
                        <p class="text-xs text-muted-foreground mt-1">Deixe vazio para não definir assinatura</p>
                        @error('subscription_days')
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
                        <input type="text" id="company" name="company" value="{{ old('company') }}"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('company') border-destructive @enderror">
                        @error('company')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-foreground mb-2">Telefone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('phone') border-destructive @enderror">
                        @error('phone')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-foreground mb-2">Observações</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent @error('notes') border-destructive @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Plan Limits Preview -->
            <div id="plan-limits-preview" class="hidden">
                <h3 class="text-lg font-semibold text-foreground mb-4">Limites do Plano</h3>
                
                <div class="bg-muted p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Visitas/mês</label>
                            <p class="text-foreground font-semibold" id="visits-limit">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Eventos/mês</label>
                            <p class="text-foreground font-semibold" id="events-limit">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Sites</label>
                            <p class="text-foreground font-semibold" id="sites-limit">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground mb-1">Reviews</label>
                            <p class="text-foreground font-semibold" id="reviews-limit">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-border">
                <a href="{{ route('admin.clients.index') }}" class="px-6 py-2 text-muted-foreground hover:text-foreground transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors">
                    Criar Cliente
                </button>
            </div>
        </form>
    </x-ui.card>
</div>

<script>
// Plan limits data
const planLimits = {
    free: {
        monthly_visits: 1000,
        monthly_events: 5000,
        sites: 1,
        reviews: 50,
        exports: 0
    },
    basic: {
        monthly_visits: 10000,
        monthly_events: 50000,
        sites: 3,
        reviews: 500,
        exports: 10
    },
    premium: {
        monthly_visits: 100000,
        monthly_events: 500000,
        sites: 10,
        reviews: 5000,
        exports: 100
    },
    enterprise: {
        monthly_visits: -1,
        monthly_events: -1,
        sites: -1,
        reviews: -1,
        exports: -1
    }
};

// Update plan limits preview
document.getElementById('plan').addEventListener('change', function() {
    const selectedPlan = this.value;
    const preview = document.getElementById('plan-limits-preview');
    
    if (selectedPlan && planLimits[selectedPlan]) {
        const limits = planLimits[selectedPlan];
        
        document.getElementById('visits-limit').textContent = limits.monthly_visits === -1 ? 'Ilimitado' : limits.monthly_visits.toLocaleString();
        document.getElementById('events-limit').textContent = limits.monthly_events === -1 ? 'Ilimitado' : limits.monthly_events.toLocaleString();
        document.getElementById('sites-limit').textContent = limits.sites === -1 ? 'Ilimitado' : limits.sites;
        document.getElementById('reviews-limit').textContent = limits.reviews === -1 ? 'Ilimitado' : limits.reviews;
        
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
});

// Initialize preview if plan is already selected
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan');
    if (planSelect.value) {
        planSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
