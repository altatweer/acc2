@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">@lang('messages.system_settings')</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('settings.system.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>@lang('messages.system_name')</label>
                            <input type="text" name="system_name" class="form-control" value="{{ old('system_name', $settings['system_name']) }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.company_name')</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name']) }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.company_logo')</label><br>
                            @if($settings['company_logo'])
                                <img src="{{ asset('storage/'.$settings['company_logo']) }}" alt="@lang('messages.current_logo')" style="max-height:80px;max-width:200px;" class="mb-2 d-block">
                            @endif
                            <input type="file" name="company_logo" class="form-control-file">
                            <small class="form-text text-muted">@lang('messages.logo_requirements')</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.default_language')</label>
                            <select name="default_language" class="form-control">
                                <option value="ar" {{ $settings['default_language'] == 'ar' ? 'selected' : '' }}>العربية</option>
                                <option value="en" {{ $settings['default_language'] == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                            <small class="form-text text-muted">@lang('messages.default_language_hint')</small>
                            <div class="alert alert-info mt-2" style="font-size:0.97em;">
                                <i class="fas fa-info-circle"></i> @lang('messages.language_setting_admin_only')
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">@lang('messages.save_settings')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 