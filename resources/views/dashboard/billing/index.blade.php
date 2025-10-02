@extends('dashboard.layout')

@section('title', 'Billing & Plans - SitePulse Widgets')
@section('page-title', 'Billing & Plans')

@section('content')
<div class="row">
    <!-- Current Plan -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Plan</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">{{ ucfirst($client->plan ?? 'Free') }} Plan</h4>
                        <p class="text-muted mb-0">
                            @if($client->isOnTrial())
                                Trial ends {{ $client->trial_ends_at->format('M d, Y') }}
                            @elseif($client->hasActiveSubscription())
                                Next billing: {{ $client->subscription_ends_at->format('M d, Y') }}
                            @else
                                No active subscription
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        @if($client->plan === 'free')
                            <span class="badge bg-secondary fs-6">Free</span>
                        @else
                            <span class="badge bg-success fs-6">Active</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Usage Statistics -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Usage</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h4 text-primary">{{ $currentUsage['sites'] }}</div>
                            <div class="text-muted small">Sites</div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar" style="width: {{ min(($currentUsage['sites'] / $planLimits['sites']) * 100, 100) }}%"></div>
                            </div>
                            <small class="text-muted">{{ $currentUsage['sites'] }} / {{ $planLimits['sites'] === -1 ? '∞' : $planLimits['sites'] }}</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h4 text-success">{{ number_format($currentUsage['monthly_visits']) }}</div>
                            <div class="text-muted small">Monthly Visits</div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ min(($currentUsage['monthly_visits'] / $planLimits['monthly_visits']) * 100, 100) }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($currentUsage['monthly_visits']) }} / {{ $planLimits['monthly_visits'] === -1 ? '∞' : number_format($planLimits['monthly_visits']) }}</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h4 text-info">{{ number_format($currentUsage['monthly_events']) }}</div>
                            <div class="text-muted small">Monthly Events</div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-info" style="width: {{ min(($currentUsage['monthly_events'] / $planLimits['monthly_events']) * 100, 100) }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($currentUsage['monthly_events']) }} / {{ $planLimits['monthly_events'] === -1 ? '∞' : number_format($planLimits['monthly_events']) }}</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h4 text-warning">{{ $currentUsage['exports'] }}</div>
                            <div class="text-muted small">Exports</div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: {{ min(($currentUsage['exports'] / $planLimits['exports']) * 100, 100) }}%"></div>
                            </div>
                            <small class="text-muted">{{ $currentUsage['exports'] }} / {{ $planLimits['exports'] === -1 ? '∞' : $planLimits['exports'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Available Plans -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Available Plans</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Free Plan -->
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 {{ $client->plan === 'free' ? 'border-primary' : '' }}">
                            <div class="card-header text-center">
                                <h5 class="card-title">Free</h5>
                                <div class="h2 mb-0">$0<small class="text-muted">/month</small></div>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>1 Site</li>
                                    <li><i class="fas fa-check text-success me-2"></i>1,000 Monthly Visits</li>
                                    <li><i class="fas fa-check text-success me-2"></i>5,000 Monthly Events</li>
                                    <li><i class="fas fa-check text-success me-2"></i>50 Reviews</li>
                                    <li><i class="fas fa-times text-muted me-2"></i>No Exports</li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                @if($client->plan === 'free')
                                    <button class="btn btn-outline-primary w-100" disabled>Current Plan</button>
                                @else
                                    <form method="POST" action="{{ route('billing.upgrade') }}" class="d-inline w-100">
                                        @csrf
                                        <input type="hidden" name="plan" value="free">
                                        <button type="submit" class="btn btn-outline-primary w-100">Downgrade</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Basic Plan -->
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 {{ $client->plan === 'basic' ? 'border-primary' : '' }}">
                            <div class="card-header text-center">
                                <h5 class="card-title">Basic</h5>
                                <div class="h2 mb-0">$29<small class="text-muted">/month</small></div>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>3 Sites</li>
                                    <li><i class="fas fa-check text-success me-2"></i>10,000 Monthly Visits</li>
                                    <li><i class="fas fa-check text-success me-2"></i>50,000 Monthly Events</li>
                                    <li><i class="fas fa-check text-success me-2"></i>500 Reviews</li>
                                    <li><i class="fas fa-check text-success me-2"></i>10 Exports</li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                @if($client->plan === 'basic')
                                    <button class="btn btn-outline-primary w-100" disabled>Current Plan</button>
                                @else
                                    <form method="POST" action="{{ route('billing.upgrade') }}" class="d-inline w-100">
                                        @csrf
                                        <input type="hidden" name="plan" value="basic">
                                        <button type="submit" class="btn btn-primary w-100">Upgrade</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Premium Plan -->
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 {{ $client->plan === 'premium' ? 'border-primary' : '' }}">
                            <div class="card-header text-center">
                                <h5 class="card-title">Premium</h5>
                                <div class="h2 mb-0">$99<small class="text-muted">/month</small></div>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>10 Sites</li>
                                    <li><i class="fas fa-check text-success me-2"></i>100,000 Monthly Visits</li>
                                    <li><i class="fas fa-check text-success me-2"></i>500,000 Monthly Events</li>
                                    <li><i class="fas fa-check text-success me-2"></i>5,000 Reviews</li>
                                    <li><i class="fas fa-check text-success me-2"></i>100 Exports</li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                @if($client->plan === 'premium')
                                    <button class="btn btn-outline-primary w-100" disabled>Current Plan</button>
                                @else
                                    <form method="POST" action="{{ route('billing.upgrade') }}" class="d-inline w-100">
                                        @csrf
                                        <input type="hidden" name="plan" value="premium">
                                        <button type="submit" class="btn btn-primary w-100">Upgrade</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Enterprise Plan -->
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 {{ $client->plan === 'enterprise' ? 'border-primary' : '' }}">
                            <div class="card-header text-center">
                                <h5 class="card-title">Enterprise</h5>
                                <div class="h2 mb-0">$299<small class="text-muted">/month</small></div>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Unlimited Sites</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Unlimited Visits</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Unlimited Events</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Unlimited Reviews</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Unlimited Exports</li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                @if($client->plan === 'enterprise')
                                    <button class="btn btn-outline-primary w-100" disabled>Current Plan</button>
                                @else
                                    <form method="POST" action="{{ route('billing.upgrade') }}" class="d-inline w-100">
                                        @csrf
                                        <input type="hidden" name="plan" value="enterprise">
                                        <button type="submit" class="btn btn-primary w-100">Upgrade</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection