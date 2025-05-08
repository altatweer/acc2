@extends('layouts.app')
@section('title', __('messages.expenses_revenues'))
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
    <h2 class="mb-4">@lang('messages.expenses_revenues')</h2>
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ Route::localizedRoute('reports.expenses-revenues.excel', request()->only(['from','to','type','parent_id'])) }}" class="btn btn-outline-success me-2"><i class="fas fa-file-excel"></i> @lang('messages.export_excel')</a>
        <a href="{{ Route::localizedRoute('reports.expenses-revenues.pdf', request()->only(['from','to','type','parent_id'])) }}" class="btn btn-outline-danger me-2"><i class="fas fa-file-pdf"></i> @lang('messages.export_pdf')</a>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> @lang('messages.print')</button>
    </div>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="from" class="form-label">@lang('messages.from_date')</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-4">
            <label for="to" class="form-label">@lang('messages.to_date')</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary w-100">@lang('messages.show_report')</button>
        </div>
    </form>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>@lang('messages.item')</th>
                            <th>@lang('messages.revenues')</th>
                            <th>@lang('messages.expenses')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                        <tr>
                            <td>{{ $row['account']->name }}</td>
                            <td>
                                @if(in_array($row['type'], ['إيراد', 'revenue']))
                                    {{ number_format(abs($row['balance']), 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(in_array($row['type'], ['مصروف', 'expense']))
                                    {{ number_format(abs($row['balance']), 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">@lang('messages.no_data')</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>@lang('messages.total')</th>
                            <th>{{ number_format($totalRevenue, 2) }}</th>
                            <th>{{ number_format($totalExpense, 2) }}</th>
                        </tr>
                        <tr>
                            @php $net = abs($totalRevenue) - abs($totalExpense); @endphp
                            @if($net >= 0)
                                <th colspan="2">@lang('messages.net_profit')</th>
                            @else
                                <th colspan="2">@lang('messages.net_loss')</th>
                            @endif
                            <th>{{ number_format(abs($net), 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 