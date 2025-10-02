@extends('dashboard.layout')

@section('title', 'Settings - SitePulse Widgets')
@section('page-title', 'Settings')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">General Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    
                    <!-- Webhook Settings -->
                    <h6 class="mb-3">Webhook Configuration</h6>
                    <div class="mb-3">
                        <label for="webhook_url" class="form-label">Webhook URL</label>
                        <input type="url" class="form-control @error('webhook_url') is-invalid @enderror" 
                               id="webhook_url" name="webhook_url" 
                               value="{{ old('webhook_url', $client->webhook_url) }}"
                               placeholder="https://your-domain.com/webhook">
                        <div class="form-text">Receive real-time notifications about widget events and reviews.</div>
                        @error('webhook_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="webhook_secret" class="form-label">Webhook Secret</label>
                        <input type="text" class="form-control @error('webhook_secret') is-invalid @enderror" 
                               id="webhook_secret" name="webhook_secret" 
                               value="{{ old('webhook_secret', $client->webhook_secret) }}"
                               placeholder="Enter a secret key for webhook verification">
                        <div class="form-text">Used to verify webhook authenticity.</div>
                        @error('webhook_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Notification Settings -->
                    <h6 class="mb-3">Notification Preferences</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="email_notifications" 
                                       name="settings[email_notifications]" value="1" 
                                       {{ old('settings.email_notifications', $client->settings['email_notifications'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">
                                    Email Notifications
                                </label>
                                <div class="form-text">Receive email alerts for important events</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="weekly_reports" 
                                       name="settings[weekly_reports]" value="1" 
                                       {{ old('settings.weekly_reports', $client->settings['weekly_reports'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="weekly_reports">
                                    Weekly Reports
                                </label>
                                <div class="form-text">Receive weekly summaries</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="monthly_reports" 
                                       name="settings[monthly_reports]" value="1" 
                                       {{ old('settings.monthly_reports', $client->settings['monthly_reports'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="monthly_reports">
                                    Monthly Reports
                                </label>
                                <div class="form-text">Receive monthly summaries</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_retention_days" class="form-label">Data Retention (Days)</label>
                                <input type="number" class="form-control @error('settings.data_retention_days') is-invalid @enderror" 
                                       id="data_retention_days" name="settings[data_retention_days]" 
                                       value="{{ old('settings.data_retention_days', $client->settings['data_retention_days'] ?? 365) }}"
                                       min="30" max="365">
                                <div class="form-text">How long to keep data (30-365 days)</div>
                                @error('settings.data_retention_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Account Information -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Plan:</strong> {{ ucfirst($client->plan ?? 'Free') }}
                    <br>
                    <small class="text-muted">Current subscription plan</small>
                </div>
                
                <div class="mb-3">
                    <strong>API Key:</strong>
                    <br>
                    <code class="small">{{ Str::limit($client->api_key ?? 'Not generated', 20) }}</code>
                    <br>
                    <small class="text-muted">Used for API authentication</small>
                </div>
                
                <div class="mb-3">
                    <strong>Member Since:</strong>
                    <br>
                    <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                </div>
                
                <div class="mb-3">
                    <strong>Last Updated:</strong>
                    <br>
                    <small class="text-muted">{{ $client->updated_at->format('M d, Y H:i') }}</small>
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('profile') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user me-1"></i>
                        Edit Profile
                    </a>
                    <a href="{{ route('billing') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-credit-card me-1"></i>
                        Billing & Plans
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Data Export -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Data Management</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Export your reviews and manage data retention settings.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('exports.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-download me-1"></i>
                        Export Data
                    </a>
                    <button class="btn btn-outline-warning" onclick="confirmDataReset()">
                        <i class="fas fa-trash me-1"></i>
                        Reset All Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDataReset() {
    if (confirm('Are you sure you want to reset all data? This action cannot be undone.')) {
        if (confirm('This will permanently delete all your data. Are you absolutely sure?')) {
            // Here you would implement the data reset functionality
            alert('Data reset functionality would be implemented here.');
        }
    }
}
</script>
@endpush
