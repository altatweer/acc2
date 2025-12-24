@extends('layouts.app')

@section('styles')
<style>
    .voucher-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
    }
    
    .voucher-header {
        background: linear-gradient(135deg, #3a4a66 0%, #2c3e50 100%);
        color: white;
        padding: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .voucher-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(-30deg);
        pointer-events: none;
    }
    
    .voucher-number {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
    }
    
    .voucher-type {
        display: inline-block;
        padding: 6px 15px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        margin-top: 10px;
        position: relative;
        z-index: 1;
    }
    
    .type-receipt {
        background-color: #28a745;
    }
    
    .type-payment {
        background-color: #dc3545;
    }
    
    .type-transfer {
        background-color: #007bff;
    }
    
    .voucher-status {
        position: absolute;
        top: 25px;
        right: 30px;
        z-index: 1;
    }
    
    .status-active {
        color: #28a745;
    }
    
    .status-canceled {
        color: #dc3545;
    }
    
    .status-badge {
        padding: 5px 15px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .badge-active {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
    }
    
    .badge-canceled {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }
    
    .voucher-body {
        padding: 30px;
    }
    
    .info-section {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-gap: 30px;
        margin-bottom: 40px;
    }
    
    .info-item {
        margin-bottom: 20px;
    }
    
    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 8px;
        display: block;
    }
    
    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #343a40;
    }
    
    .info-description {
        grid-column: span 2;
    }
    
    .transactions-section h4 {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
        font-weight: 600;
    }
    
    .transactions-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .transactions-table th {
        background-color: #f8f9fa;
        padding: 15px;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        text-align: right;
    }
    
    .transactions-table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    
    .transactions-table tr:last-child td {
        border-bottom: none;
    }
    
    .text-debit {
        color: #28a745;
        font-weight: 600;
    }
    
    .text-credit {
        color: #dc3545;
        font-weight: 600;
    }
    
    .action-bar {
        display: flex;
        gap: 15px;
        margin-top: 40px;
        flex-wrap: wrap;
    }
    
    .btn-print {
        display: inline-flex;
        align-items: center;
        background: #2c3e50;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-print:hover {
        background: #1a2530;
        color: white;
        text-decoration: none;
    }
    
    .btn-cancel {
        display: inline-flex;
        align-items: center;
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid #dc3545;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-cancel:hover {
        background: #dc3545;
        color: white;
        text-decoration: none;
    }
    
    .icon-print, .icon-cancel {
        margin-right: 8px;
    }
    
    .alert-canceled {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: none;
        border-radius: 5px;
        padding: 15px 20px;
        font-weight: 600;
        margin-bottom: 30px;
    }
    
    .empty-transactions {
        padding: 30px;
        text-align: center;
        color: #6c757d;
        font-style: italic;
    }
    
    @media (max-width: 768px) {
        .info-section {
            grid-template-columns: 1fr;
        }
        
        .info-description {
            grid-column: span 1;
        }
        
        .transactions-section {
            overflow-x: auto;
        }
        
        .voucher-status {
            position: static;
            margin-top: 15px;
            text-align: left;
        }
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-receipt mr-2 text-primary"></i>@lang('messages.voucher_details_title')
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> @lang('messages.dashboard_title')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('vouchers.index') }}">@lang('messages.vouchers')</a></li>
                        <li class="breadcrumb-item active">@lang('messages.voucher_details_title')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($voucher->status == 'canceled')
                <div class="alert alert-danger text-center font-weight-bold">
                    <i class="fas fa-ban mr-1"></i> @lang('messages.voucher_canceled_alert')
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('messages.voucher_information')</h3>
                    <div class="card-tools">
                        <span class="badge {{ $voucher->status == 'canceled' ? 'badge-danger' : 'badge-success' }}">
                            {{ $voucher->status == 'canceled' ? __('messages.canceled') : __('messages.active') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted">@lang('messages.voucher_number')</span>
                                    <span class="info-box-number text-bold">{{ $voucher->voucher_number }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted">@lang('messages.voucher_type')</span>
                                    <span class="info-box-number text-bold">
                                        @if($voucher->type == 'receipt')
                                            <i class="fas fa-arrow-down text-success mr-1"></i> @lang('messages.receipt')
                                        @elseif($voucher->type == 'payment')
                                            <i class="fas fa-arrow-up text-danger mr-1"></i> @lang('messages.payment')
                                        @else
                                            <i class="fas fa-exchange-alt text-info mr-1"></i> @lang('messages.transfer')
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">@lang('messages.voucher_date')</th>
                                <td>
                                    <i class="far fa-calendar-alt text-muted mr-1"></i>
                                    {{ $voucher->date ? $voucher->date->format('Y-m-d H:i:s') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>@lang('messages.accountant')</th>
                                <td>
                                    <i class="far fa-user text-muted mr-1"></i>
                                    {{ $voucher->user->name ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>@lang('messages.recipient_payer')</th>
                                <td>
                                    <i class="far fa-handshake text-muted mr-1"></i>
                                    {{ $voucher->recipient_name ?: '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>@lang('messages.description')</th>
                                <td>
                                    <i class="far fa-comment-alt text-muted mr-1"></i>
                                    {{ $voucher->description ?: '-' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exchange-alt mr-1"></i> 
                        @lang('messages.related_financial_transactions')
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $voucherStatus = $voucher->status;
                        if (is_null($voucherStatus)) $voucherStatus = 'active';
                    @endphp
                    
                    <div class="mb-3">
                        @if(trim((string)$voucherStatus) === 'active')
                            <a href="{{ route('vouchers.print', ['voucher' => $voucher->id, ]) }}" class="btn btn-primary" target="_blank">
                                <i class="fas fa-print mr-1"></i> @lang('messages.print_voucher')
                            </a>
                            @can('cancel_vouchers')
                            <form action="{{ route('vouchers.cancel', ['voucher' => $voucher, ]) }}" method="POST" style="display:inline-block;" class="ml-2">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('messages.cancel_voucher_confirm')')">
                                    <i class="fas fa-ban mr-1"></i> @lang('messages.cancel_voucher')
                                </button>
                            </form>
                            @endcan
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i> <strong>@lang('messages.note'):</strong> @lang('messages.voucher_edit_note')
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('messages.account')</th>
                                    <th>@lang('messages.description')</th>
                                    <th class="text-right">@lang('messages.debit')</th>
                                    <th class="text-right">@lang('messages.credit')</th>
                                    <th class="text-center">@lang('messages.currency')</th>
                                    <th class="text-center">سعر الصرف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($voucher->journalEntry && $voucher->journalEntry->lines && $voucher->journalEntry->lines->count())
                                    @php
                                        $totalDebit = 0;
                                        $totalCredit = 0;
                                    @endphp
                                    @php
                                        $linesArray = $voucher->journalEntry->lines->sortBy('id')->values()->all();
                                    @endphp
                                    @foreach($linesArray as $index => $line)
                                        @php
                                            $totalDebit += $line->debit;
                                            $totalCredit += $line->credit;
                                            
                                            // إظهار سعر الصرف فقط في السطر الأول (USD أو IQD حسب الاتجاه)
                                            $showExchangeRate = false;
                                            
                                            // إذا كان هذا السطر الأول
                                            if ($index === 0) {
                                                // السطر الأول: نعرض سعر الصرف إذا كان USD أو IQD
                                                // التحقق من أن هناك سطر ثاني بعملة مختلفة
                                                $hasSecondLine = isset($linesArray[1]);
                                                $secondLineCurrency = $hasSecondLine ? $linesArray[1]->currency : null;
                                                
                                                // إذا كان السطر الأول USD أو IQD والسطر الثاني بعملة مختلفة (USD/IQD)
                                                if (($line->currency === 'USD' || $line->currency === 'IQD') && 
                                                    $hasSecondLine && 
                                                    ($secondLineCurrency === 'USD' || $secondLineCurrency === 'IQD') &&
                                                    $secondLineCurrency !== $line->currency) {
                                                    // البحث عن سعر الصرف في أي من السطرين
                                                    $exchangeRate = $line->exchange_rate;
                                                    if (!$exchangeRate || $exchangeRate == 1.0) {
                                                        $exchangeRate = $linesArray[1]->exchange_rate ?? null;
                                                    }
                                                    
                                                    if ($exchangeRate && $exchangeRate != 1.0) {
                                                        $showExchangeRate = true;
                                                    }
                                                }
                                            } else {
                                                // السطر الثاني: لا نعرض سعر الصرف
                                                $showExchangeRate = false;
                                            }
                                            
                                            // للعملات الأخرى غير USD/IQD، نعرض إذا كان السعر موجوداً
                                            if (!$showExchangeRate && $line->currency !== 'USD' && $line->currency !== 'IQD' && 
                                                $line->exchange_rate && $line->exchange_rate != 1.0) {
                                                $showExchangeRate = true;
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $line->account->name ?? '-' }}
                                                @if($line->account && $line->account->code)
                                                    <small class="d-block text-muted">{{ $line->account->code }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $line->description ?: '-' }}</td>
                                            <td class="text-right text-success">
                                                @if($line->debit > 0)
                                                    {{ number_format($line->debit, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-right text-danger">
                                                @if($line->credit > 0)
                                                    {{ number_format($line->credit, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-light">{{ $line->currency }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($showExchangeRate)
                                                    @php
                                                        // استخدام سعر الصرف من السطر الأول أو الثاني
                                                        $displayRate = $line->exchange_rate;
                                                        if (!$displayRate || $displayRate == 1.0) {
                                                            $displayRate = $linesArray[1]->exchange_rate ?? $line->exchange_rate;
                                                        }
                                                    @endphp
                                                    <span class="badge badge-info" title="سعر الصرف المستخدم في التحويل">
                                                        {{ number_format($displayRate, 4) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    @if($voucher->type == 'transfer')
                                    <!-- عرض الإجماليات لسند التحويل: إظهار كل مبلغ على حدة -->
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="6" class="text-center border-bottom">@lang('messages.totals')</td>
                                    </tr>
                                    @foreach($voucher->journalEntry->lines->groupBy('currency') as $currency => $lines)
                                        @php
                                            $currDebit = $lines->sum('debit');
                                            $currCredit = $lines->sum('credit');
                                        @endphp
                                        <tr class="bg-light">
                                            <td colspan="2" class="text-right">
                                                <strong>{{ $currency }} @lang('messages.total')</strong>
                                            </td>
                                            <td class="text-right text-success">
                                                <strong>{{ number_format($currDebit, 2) }}</strong>
                                            </td>
                                            <td class="text-right text-danger">
                                                <strong>{{ number_format($currCredit, 2) }}</strong>
                                            </td>
                                            <td class="text-center">{{ $currency }}</td>
                                            <td class="text-center">-</td>
                                        </tr>
                                    @endforeach
                                    @else
                                    <!-- عرض الإجماليات لسندات القبض والصرف: مبلغ واحد فقط -->
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="2" class="text-right">@lang('messages.total')</td>
                                        <td colspan="2" class="text-center">
                                            <span class="badge badge-pill badge-secondary px-3 py-2">
                                                {{ number_format($totalDebit, 2) }} {{ $voucher->journalEntry->lines->first()->currency ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $voucher->journalEntry->lines->first()->currency ?? '-' }}</td>
                                        <td class="text-center">-</td>
                                    </tr>
                                    @endif
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            <i class="fas fa-info-circle mr-1"></i> @lang('messages.no_transactions')
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 