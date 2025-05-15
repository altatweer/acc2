@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">{{ __('messages.add_new_user') }}</h1>
      </div>
      <div class="col-sm-6 text-left">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">{{ __('messages.user_data') }}</h3>
      </div>
      <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="form-group">
            <label>{{ __('messages.name') }}</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
          </div>
          <div class="form-group">
            <label>{{ __('messages.email') }}</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
          </div>
          <div class="form-group">
            <label>{{ __('messages.password') }}</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="form-group">
            <label>{{ __('messages.password_confirmation') }}</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>
          <div class="form-group">
            <label>{{ __('messages.roles') }}</label>
            <div class="row">
              @foreach($roles as $role)
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}">
                    <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="card-footer text-right">
          <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection 