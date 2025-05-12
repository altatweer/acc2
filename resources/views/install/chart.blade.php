@extends('layouts.install')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-info text-white text-center">
                    <h2>Import Chart of Accounts</h2>
                    <p class="mb-0">You can import a ready-made chart of accounts in Arabic or English for <b>each selected currency</b>.<br>
                        <span class="text-danger">This step is optional. You can skip it and add accounts later.</span>
                    </p>
                </div>
                <div class="card-body">
                    @if(session('chart_error'))
                        <div class="alert alert-danger text-center">{{ session('chart_error') }}</div>
                    @endif
                    <div class="mb-3">
                        <strong>Currencies selected:</strong>
                        <ul>
                            @php $currencies = session('install_currencies', ['USD']); @endphp
                            @foreach($currencies as $cur)
                                <li>{{ $cur }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <form method="POST" action="{{ route('install.importChart') }}">
                        @csrf
                        <div class="form-group">
                            <label>Choose Chart Language:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="chart_type" id="chart_ar" value="ar" checked>
                                <label class="form-check-label" for="chart_ar">Arabic شجرة الحسابات بالعربية</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="chart_type" id="chart_en" value="en">
                                <label class="form-check-label" for="chart_en">English Chart of Accounts</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Import Chart for All Currencies <i class="fas fa-arrow-right"></i></button>
                    </form>
                    <form method="POST" action="{{ route('install.skipChart') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-block">Skip This Step</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 