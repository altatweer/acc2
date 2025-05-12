@extends('layouts.install')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2>Database Configuration</h2>
                    <p class="mb-0">Please enter your database connection details.</p>
                </div>
                <div class="card-body">
                    @if($errors->has('db_error'))
                        <div class="alert alert-danger text-center">{{ $errors->first('db_error') }}</div>
                    @endif
                    <form method="POST" action="{{ route('install.saveDatabase') }}">
                        @csrf
                        <div class="form-group">
                            <label for="db_host">Database Host (DB_HOST)</label>
                            <input type="text" class="form-control @error('db_host') is-invalid @enderror" id="db_host" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" required>
                            @error('db_host')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="db_database">Database Name (DB_DATABASE)</label>
                            <input type="text" class="form-control @error('db_database') is-invalid @enderror" id="db_database" name="db_database" value="{{ old('db_database') }}" required>
                            @error('db_database')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="db_username">Database Username (DB_USERNAME)</label>
                            <input type="text" class="form-control @error('db_username') is-invalid @enderror" id="db_username" name="db_username" value="{{ old('db_username') }}" required>
                            @error('db_username')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="db_password">Database Password (DB_PASSWORD)</label>
                            <input type="password" class="form-control @error('db_password') is-invalid @enderror" id="db_password" name="db_password" value="{{ old('db_password') }}">
                            @error('db_password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <button type="submit" class="btn btn-success btn-lg btn-block">Save & Continue <i class="fas fa-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 