@extends('layouts.install')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h2>Installation Complete</h2>
                </div>
                <div class="card-body text-center">
                    <div class="alert alert-success mb-4">
                        <h4 class="mb-2"><i class="fas fa-check-circle"></i> The system has been installed successfully!</h4>
                        <p>You can now log in and start using the accounting system.</p>
                    </div>
                    <a href="{{ url('/login') }}" class="btn btn-primary btn-lg">Go to Login <i class="fas fa-sign-in-alt"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 