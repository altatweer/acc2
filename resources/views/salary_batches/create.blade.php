@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">توليد كشف رواتب جديد</h1>
            <a href="{{ route('salary-batches.index') }}" class="btn btn-secondary">عودة للقائمة</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('salary-batches.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>الشهر</label>
                            <input type="month" name="month" class="form-control" required value="{{ old('month', date('Y-m')) }}">
                        </div>
                        <button type="submit" class="btn btn-success">توليد كشف</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 