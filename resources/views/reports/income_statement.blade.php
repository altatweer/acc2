@extends('layouts.app')
@section('title', __('messages.income_statement'))
@section('content')
@if(isset($export) && $export)
    <style>
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
    </style>
@endif
@php
if (!isset($totalDebit)) $totalDebit = 0;
if (!isset($totalCredit)) $totalCredit = 0;
if (!isset($totalBalance)) $totalBalance = 0;
@endphp
<div class="container">
    <h2 class="mb-4">@lang('messages.income_statement')</h2>
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ Route::localizedRoute('reports.income-statement.excel', request()->only(['from','to'])) }}" class="btn btn-outline-success me-2"><i class="fas fa-file-excel"></i> @lang('messages.export_excel')</a>
        <a href="{{ Route::localizedRoute('reports.income-statement.pdf', request()->only(['from','to'])) }}" class="btn btn-outline-danger me-2"><i class="fas fa-file-pdf"></i> @lang('messages.export_pdf')</a>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> @lang('messages.print')</button>
    </div>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="from" class="form-label">@lang('messages.from_date')</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-3">
            <label for="to" class="form-label">@lang('messages.to_date')</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-3">
            <label for="type" class="form-label">@lang('messages.account_type')</label>
            <select name="type" id="type" class="form-select">
                <option value="">@lang('messages.all')</option>
                <option value="إيراد" {{ $type == 'إيراد' ? 'selected' : '' }}>@lang('messages.revenues')</option>
                <option value="مصروف" {{ $type == 'مصروف' ? 'selected' : '' }}>@lang('messages.expenses')</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="parent_id" class="form-label">@lang('messages.parent_category')</label>
            <select name="parent_id" id="parent_id" class="form-select">
                <option value="">@lang('messages.all')</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ $parent_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 align-self-end">
            <button type="submit" class="btn btn-primary">@lang('messages.show_report')</button>
        </div>
    </form>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>@lang('messages.item')</th>
                            <th>@lang('messages.account_type')</th>
                            <th>@lang('messages.debit')</th>
                            <th>@lang('messages.credit')</th>
                            <th>@lang('messages.balance')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                        <tr>
                            <td>{{ $row['account']->name }}</td>
                            <td>{{ $row['type'] }}</td>
                            <td>{{ number_format(abs($row['debit']), 2) }}</td>
                            <td>{{ number_format(abs($row['credit']), 2) }}</td>
                            <td>{{ number_format(abs($row['balance']), 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2">@lang('messages.revenues')</th>
                            <th colspan="3">{{ number_format($totalRevenue, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="2">@lang('messages.expenses')</th>
                            <th colspan="3">{{ number_format($totalExpense, 2) }}</th>
                        </tr>
                        <tr>
                            @if($net >= 0)
                                <th colspan="2">@lang('messages.net_profit')</th>
                                <th colspan="3">{{ number_format($net, 2) }}</th>
                            @else
                                <th colspan="2">@lang('messages.net_loss')</th>
                                <th colspan="3">{{ number_format(abs($net), 2) }}</th>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 