@extends('layouts.install')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2>Welcome to the Accounting System Installer</h2>
                    <p class="mb-0">A step-by-step wizard to install your accounting system easily and professionally.</p>
                </div>
                <div class="card-body">
                    <h4 class="mb-3">System Requirements Check</h4>
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Required PHP Version: <strong>{{ $requirements['php']['required'] }}+</strong></span>
                            <span class="badge badge-{{ $requirements['php']['ok'] ? 'success' : 'danger' }}">{{ $requirements['php']['current'] }}</span>
                        </li>
                        @foreach($requirements['extensions'] as $ext => $ok)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>PHP Extension: <strong>{{ strtoupper($ext) }}</strong></span>
                            <span class="badge badge-{{ $ok ? 'success' : 'danger' }}">{{ $ok ? 'Available' : 'Missing' }}</span>
                        </li>
                        @endforeach
                        @foreach($requirements['permissions'] as $perm => $ok)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Write Permission: <strong>{{ $perm == 'storage' ? 'storage folder' : '.env file' }}</strong></span>
                            <span class="badge badge-{{ $ok ? 'success' : 'danger' }}">{{ $ok ? 'Writable' : 'Not Writable' }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @if($requirements['php']['ok'] && !in_array(false, $requirements['extensions']) && !in_array(false, $requirements['permissions']))
                        <form method="POST" action="{{ route('install.process') }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg btn-block">Continue Installation <i class="fas fa-arrow-right"></i></button>
                        </form>
                    @else
                        <div class="alert alert-danger text-center">
                            Please make sure all requirements above are met before continuing.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 