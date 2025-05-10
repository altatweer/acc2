@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">@lang('messages.journal_entries')</h1>
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="@lang('messages.from_date')">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="@lang('messages.to_date')">
            </div>
            <div class="col-md-3">
                <select name="account_id" class="form-control">
                    <option value="">@lang('messages.all_accounts')</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="user_id" class="form-control" value="{{ request('user_id') }}" placeholder="@lang('messages.user_id')">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-block">@lang('messages.search')</button>
            </div>
        </div>
    </form>
    <div class="mb-3 text-right">
        <a href="{{ route('journal-entries.single-currency.create') }}" class="btn btn-primary mr-2">
            <i class="fas fa-coins"></i> {{ __('messages.add_single_currency_entry') }}
        </a>
        <a href="{{ route('journal-entries.multi-currency.create') }}" class="btn btn-info mr-2">
            <i class="fas fa-globe"></i> {{ __('messages.add_multi_currency_entry') }}
        </a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0" id="journalEntriesTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('messages.description')</th>
                        <th>@lang('messages.user')</th>
                        <th>@lang('messages.currency')</th>
                        <th>@lang('messages.debit')</th>
                        <th>@lang('messages.credit')</th>
                        <th>@lang('messages.status')</th>
                        <th>@lang('messages.entry_type')</th>
                        <th>@lang('messages.actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                    <tr>
                        <td>{{ $entry->id }}</td>
                        <td>{{ $entry->date }}</td>
                        <td>{{ $entry->description }}</td>
                        <td>{{ $entry->user->name ?? '-' }}</td>
                        <td>{{ $entry->currency }}</td>
                        <td>{{ number_format($entry->total_debit,2) }}</td>
                        <td>{{ number_format($entry->total_credit,2) }}</td>
                        <td>
                            @if($entry->status == 'active')
                                <span class="badge badge-success">@lang('messages.active')</span>
                            @else
                                <span class="badge badge-danger">@lang('messages.cancelled')</span>
                            @endif
                        </td>
                        <td>
                            @if($entry->source_type == null || $entry->source_type == 'manual')
                                {{ __('messages.manual_entry') }}
                            @else
                                {{ __('messages.automatic_entry') }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ Route::localizedRoute('journal-entries.show', ['journal_entry' => $entry, ]) }}" class="btn btn-sm btn-info">@lang('messages.view')</a>
                            @if($entry->status == 'active' && ((!$entry->source_type || $entry->source_type == 'manual') && !($entry->source_type == 'manual' && $entry->source_id && Str::contains($entry->description, 'قيد عكسي'))))
                                <form action="{{ Route::localizedRoute('journal-entries.cancel', ['journalEntry' => $entry->id]) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('@lang('messages.cancel_entry_confirm')')">@lang('messages.cancel')</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $entries->links() }}
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    $('#journalEntriesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/{{ app()->getLocale() == "ar" ? "ar" : "en-GB" }}.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        searching: true,
        responsive: true
    });
    $('select[name="account_id"]').select2({
        width: '100%',
        dir: '{{ app()->getLocale() == "ar" ? "rtl" : "ltr" }}',
        language: '{{ app()->getLocale() }}',
        placeholder: '@lang("messages.all_accounts")',
        allowClear: true
    });
});
</script>
@endpush 