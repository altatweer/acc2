@extends('layouts.app')

@section('content')
@php
    use App\Models\Setting;
    $companyLogo = Setting::get('company_logo');
    $companyName = Setting::get('company_name', '');
@endphp
<div class="container print-container">
    <div class="print-header text-center mb-4">
        {{-- شعار الشركة تم حذفه بناءً على طلب العميل --}}
        <h2 class="mt-2">{{ $companyName }}</h2>
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
    html, body {
        width: 100vw !important;
        min-width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        min-height: 100vh !important;
        max-height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        box-sizing: border-box !important;
    }
    .print-container, .print-container * {
        visibility: visible;
    }
    .print-container,
    .print-body,
    .container,
    .row,
    [class^="col-"],
    .card {
        width: 100vw !important;
        max-width: 100vw !important;
        min-width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box !important;
    }
    .print-header, .print-footer { page-break-inside: avoid; }
    .no-print { display: none !important; }
}
.print-header img { max-height: 60px; }
.print-header h2 { margin: 0; }
</style>
@endpush 