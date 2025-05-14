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
                    @if(session('install_notice'))
                        <div class="alert alert-warning text-center">{{ session('install_notice') }}</div>
                    @endif
                    <h4 class="mb-3">System Requirements Check</h4>
                    @if(!empty($requirements['installer_dirs']))
                        <div class="alert alert-danger">
                            <b>بعض المجلدات لم يتم إنشاؤها أو لم يتم ضبط الصلاحيات تلقائيًا:</b>
                            <ul style="margin-top:10px;">
                                @foreach($requirements['installer_dirs'] as $err)
                                    <li>{!! $err !!}</li>
                                @endforeach
                            </ul>
                            <div class="mt-2">يرجى إنشاء هذه المجلدات وضبط الصلاحيات يدويًا عبر لوحة تحكم الاستضافة (File Manager).</div>
                        </div>
                    @endif
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
                        <form method="POST" action="{{ route('install.process') }}" id="purchase-form">
                            @csrf
                            <div class="form-group">
                                <label for="purchase_code">Envato Purchase Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="purchase_code" name="purchase_code" value="{{ old('purchase_code') }}" required>
                                <small class="form-text text-muted">You must enter a valid purchase code to continue installation.</small>
                                <div id="purchase-status" class="mt-2"></div>
                                @if(session('purchase_error'))
                                    <div class="alert alert-danger mt-2">{{ session('purchase_error') }}</div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-success btn-lg btn-block" id="continue-btn" disabled>Continue Installation <i class="fas fa-arrow-right"></i></button>
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
<script>
document.getElementById('purchase_code').addEventListener('blur', function() {
    var code = this.value.trim();
    var status = document.getElementById('purchase-status');
    var btn = document.getElementById('continue-btn');
    if (!code) { status.innerHTML = ''; btn.disabled = true; return; }
    status.innerHTML = '<span class="text-info">Checking purchase code...</span>';
    btn.disabled = true;
    fetch('https://envatocode.aursuite.com/envato-verify.php?purchase_code=' + encodeURIComponent(code))
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                status.innerHTML = '<span class="text-success">Valid purchase code for <b>' + data.item + '</b> (buyer: ' + data.buyer + ')</span>';
                btn.disabled = false;
            } else {
                status.innerHTML = '<span class="text-danger">' + data.message + '</span>';
                btn.disabled = true;
            }
        })
        .catch(() => { status.innerHTML = '<span class="text-danger">Could not verify code. Check your internet connection.</span>'; btn.disabled = true; });
});
</script>
@endsection 