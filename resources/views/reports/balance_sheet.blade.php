@extends('layouts.app')
@section('title', __('messages.balance_sheet'))
@section('content')
@if(isset($export) && $export)
    <style>
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
    </style>
@endif
<div class="container">
    <h2 class="mb-4">@lang('messages.balance_sheet')</h2>
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ Route::localizedRoute('reports.balance-sheet.excel', request()->only(['from','to'])) }}" class="btn btn-outline-success me-2"><i class="fas fa-file-excel"></i> @lang('messages.export_excel')</a>
        <a href="{{ Route::localizedRoute('reports.balance-sheet.pdf', request()->only(['from','to'])) }}" class="btn btn-outline-danger me-2"><i class="fas fa-file-pdf"></i> @lang('messages.export_pdf')</a>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> @lang('messages.print')</button>
    </div>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="from" class="form-label">@lang('messages.from_date')</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-4">
            <label for="to" class="form-label">@lang('messages.to_date')</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary w-100">@lang('messages.show_report')</button>
        </div>
    </form>
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-light"><strong>@lang('messages.assets')</strong></div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm mb-0">
                        <tbody>
                        @foreach($sections['أصل']['rows'] as $row)
                            <tr>
                                <td>{{ $row['account']->name }}</td>
                                <td>{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>@lang('messages.total')</th>
                                <th>{{ number_format($sections['أصل']['total'], 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-light"><strong>@lang('messages.liabilities')</strong></div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm mb-0">
                        <tbody>
                        @foreach($sections['خصم']['rows'] as $row)
                            <tr>
                                <td>{{ $row['account']->name }}</td>
                                <td>{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>@lang('messages.total')</th>
                                <th>{{ number_format($sections['خصم']['total'], 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-light"><strong>@lang('messages.equity')</strong></div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm mb-0">
                        <tbody>
                        @foreach($sections['حقوق ملكية']['rows'] as $row)
                            <tr>
                                <td>{{ $row['account']->name }}</td>
                                <td>{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>@lang('messages.total')</th>
                                <th>{{ number_format($sections['حقوق ملكية']['total'], 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 