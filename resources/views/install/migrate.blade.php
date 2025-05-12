@extends('layouts.install')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h2>Database Initialization</h2>
                </div>
                <div class="card-body">
                    @if(session('migrate_error'))
                        <div class="alert alert-danger text-center">{{ session('migrate_error') }}</div>
                    @else
                        <div class="alert alert-success text-center mb-4">
                            Database connection details saved successfully!<br>
                            Next step: Create database tables.
                        </div>
                    @endif
                    <form method="POST" action="{{ route('install.migrate') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Run Migrations & Create Tables <i class="fas fa-database"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 