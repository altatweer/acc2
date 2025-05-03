@extends('layouts.app')

@section('content')
<div class="container print-container">
    <div class="print-header text-center mb-4">
        <img src="{{ asset('assets/logo.png') }}" alt="Logo" style="height:60px;">
        <h2 class="mt-2">{{ config('app.name', 'نظام المحاسبة') }}</h2>
        <div class="text-muted">تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
        <hr>
    </div>
    <div class="print-body">
        @yield('print-content')
    </div>
    <div class="print-footer text-center mt-5">
        <hr>
        <small>تم توليد هذه الصفحة بواسطة النظام - جميع الحقوق محفوظة &copy; {{ date('Y') }}</small>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    .print-container, .print-container * { visibility: visible; }
    .print-container { position: absolute; left: 0; top: 0; width: 100%; background: #fff; }
    .print-header, .print-footer { page-break-inside: avoid; }
    .no-print { display: none !important; }
}
.print-header img { max-height: 60px; }
.print-header h2 { margin: 0; }
</style>
@endpush 