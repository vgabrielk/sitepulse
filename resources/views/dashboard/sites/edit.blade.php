@extends('dashboard.layout')

@section('title', 'Edit ' . $site->name . ' - SitePulse Widgets')
@section('page-title', 'Edit Site')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Site Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sites.update', $site) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Site Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $site->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="domain" class="form-label">Domain <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('domain') is-invalid @enderror" 
                               id="domain" name="domain" value="{{ old('domain', $site->domain) }}" 
                               placeholder="example.com" required>
                        <div class="form-text">Enter your domain without http:// or https://</div>
                        @error('domain')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="anonymize_ips" 
                                           name="anonymize_ips" value="1" 
                                           {{ old('anonymize_ips', $site->anonymize_ips) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="anonymize_ips">
                                        Anonymize IP Addresses
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="track_events" 
                                           name="track_events" value="1" 
                                           {{ old('track_events', $site->track_events) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="track_events">
                                        Track Events
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="collect_feedback" 
                                   name="collect_feedback" value="1" 
                                   {{ old('collect_feedback', $site->collect_feedback) ? 'checked' : '' }}>
                            <label class="form-check-label" for="collect_feedback">
                                Collect User Feedback
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('sites.show', $site) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Site
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Update Site
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
