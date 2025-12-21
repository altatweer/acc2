@extends('layouts.app')
@section('title', __('messages.cash_flow'))
@section('content')
<link href="{{ asset('css/reports.css') }}?v={{ time() }}" rel="stylesheet">
@if(isset($export) && $export)
    <style>
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
    </style>
@endif
<div class="reports-container">
    <!-- Header -->
    <div class="report-card mb-4">
        <div class="report-card-header">
            <h2 class="report-title mb-0">
                <i class="fas fa-money-bill-wave mr-2"></i>@lang('messages.cash_flow')
            </h2>
        </div>
        <div class="report-card-body">
            <form method="GET" class="report-filters">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="from" class="form-label">
                            <i class="fas fa-calendar-alt mr-1"></i>@lang('messages.from_date')
                        </label>
                        <input type="date" name="from" id="from" class="form-control" value="{{ $from ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="to" class="form-label">
                            <i class="fas fa-calendar-check mr-1"></i>@lang('messages.to_date')
                        </label>
                        <input type="date" name="to" id="to" class="form-control" value="{{ $to ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label for="currency" class="form-label">
                            <i class="fas fa-coins mr-1"></i>@lang('messages.filter_by_currency')
                        </label>
                        <select name="currency" id="currency" class="form-select">
                            <option value="">@lang('messages.all_currencies')</option>
                            @foreach($currencies ?? [] as $curr)
                                <option value="{{ $curr->code }}" {{ isset($selectedCurrency) && $selectedCurrency == $curr->code ? 'selected' : '' }}>
                                    {{ $curr->code }} - {{ $curr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="display_currency" class="form-label">
                            <i class="fas fa-eye mr-1"></i>@lang('messages.display_currency')
                        </label>
                        <select name="display_currency" id="display_currency" class="form-select">
                            <option value="">@lang('messages.original_currencies')</option>
                            @foreach($currencies ?? [] as $curr)
                                <option value="{{ $curr->code }}" {{ isset($displayCurrency) && $displayCurrency == $curr->code ? 'selected' : '' }}>
                                    {{ $curr->code }} - {{ $curr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button type="submit" class="report-btn report-btn-primary w-100">
                            <i class="fas fa-search mr-2"></i>@lang('messages.show_report')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    @if(isset($cashFlowData) && count($cashFlowData) > 0)
        @foreach($cashFlowData as $currency => $data)
            <div class="report-card mb-4">
                <div class="report-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-coins mr-2"></i>العملة: 
                        <span class="currency-badge currency-badge-primary">{{ $currency }}</span>
                    </h5>
                </div>
                <div class="report-card-body p-0">
                    <div class="report-table">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>الوصف</th>
                                    <th class="text-end">المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['inflows']) && count($data['inflows']) > 0)
                                    <tr class="table-success">
                                        <td colspan="2" class="fw-bold">
                                            <i class="fas fa-arrow-down mr-2"></i>التدفقات الداخلة
                                        </td>
                                    </tr>
                                    @foreach($data['inflows'] as $inflow)
                                    <tr>
                                        <td>{{ $inflow['description'] ?? '-' }}</td>
                                        <td class="text-end text-success fw-bold">{{ number_format($inflow['amount'] ?? 0, 2) }} {{ $currency }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="table-light">
                                        <th>إجمالي التدفقات الداخلة</th>
                                        <th class="text-end text-success">{{ number_format($data['total_inflows'] ?? 0, 2) }} {{ $currency }}</th>
                                    </tr>
                                @endif
                                
                                @if(isset($data['outflows']) && count($data['outflows']) > 0)
                                    <tr class="table-danger">
                                        <td colspan="2" class="fw-bold">
                                            <i class="fas fa-arrow-up mr-2"></i>التدفقات الخارجة
                                        </td>
                                    </tr>
                                    @foreach($data['outflows'] as $outflow)
                                    <tr>
                                        <td>{{ $outflow['description'] ?? '-' }}</td>
                                        <td class="text-end text-danger fw-bold">{{ number_format($outflow['amount'] ?? 0, 2) }} {{ $currency }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="table-light">
                                        <th>إجمالي التدفقات الخارجة</th>
                                        <th class="text-end text-danger">{{ number_format($data['total_outflows'] ?? 0, 2) }} {{ $currency }}</th>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                @php
                                    $netCashFlow = ($data['total_inflows'] ?? 0) - ($data['total_outflows'] ?? 0);
                                @endphp
                                <tr class="{{ $netCashFlow >= 0 ? 'table-success' : 'table-danger' }}">
                                    <th>
                                        <i class="fas fa-{{ $netCashFlow >= 0 ? 'check-circle' : 'exclamation-triangle' }} mr-2"></i>
                                        صافي التدفق النقدي ({{ $currency }})
                                    </th>
                                    <th class="text-end fw-bold">{{ number_format(abs($netCashFlow), 2) }} {{ $currency }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="report-card">
            <div class="report-card-body text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted">لا توجد بيانات لعرضها</p>
            </div>
        </div>
    @endif
</div>
@endsection
