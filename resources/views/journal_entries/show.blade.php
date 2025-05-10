@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Debug: الحالة الحالية --}}
    <div style="direction:ltr;color:red">Status: {{ $journalEntry->status }}</div>
    <h1 class="mb-4">@lang('messages.entry_details') #{{ $journalEntry->id }}</h1>
    <div class="card mb-3">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-2">@lang('messages.date')</dt>
                <dd class="col-sm-4">{{ $journalEntry->date }}</dd>
                <dt class="col-sm-2">@lang('messages.description')</dt>
                <dd class="col-sm-4">{{ $journalEntry->description }}</dd>
                <dt class="col-sm-2">@lang('messages.user')</dt>
                <dd class="col-sm-4">{{ $journalEntry->user->name ?? '-' }}</dd>
                <dt class="col-sm-2">@lang('messages.currency')</dt>
                <dd class="col-sm-4">{{ $journalEntry->currency }}</dd>
                <dt class="col-sm-2">@lang('messages.debit')</dt>
                <dd class="col-sm-4">{{ number_format($journalEntry->total_debit,2) }}</dd>
                <dt class="col-sm-2">@lang('messages.credit')</dt>
                <dd class="col-sm-4">{{ number_format($journalEntry->total_credit,2) }}</dd>
                <dt class="col-sm-2">@lang('messages.status')</dt>
                <dd class="col-sm-4">
                    @if($journalEntry->status == 'active')
                        <span class="badge badge-success">@lang('messages.status_active')</span>
                    @else
                        <span class="badge badge-danger">@lang('messages.status_cancelled')</span>
                    @endif
                </dd>
            </dl>
            @if($journalEntry->status == 'canceled')
                <div class="alert alert-danger font-weight-bold text-center">
                    @lang('messages.entry_cancelled_note')
                </div>
            @endif
            @php use Illuminate\Support\Str; @endphp
            @if($journalEntry->status == 'active' && ((!$journalEntry->source_type || $journalEntry->source_type == 'manual') && !($journalEntry->source_type == 'manual' && $journalEntry->source_id && Str::contains($journalEntry->description, 'قيد عكسي'))))
                <form action="{{ Route::localizedRoute('journal-entries.cancel', ['journalEntry' => $journalEntry->id]) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('messages.cancel_entry_confirm')')">@lang('messages.cancel') @lang('messages.journal_entry')</button>
                </form>
            @endif
            @if($journalEntry->source_type && $journalEntry->source_id)
                <hr>
                <strong>@lang('messages.related_to'):</strong>
                <span class="badge badge-info">{{ $journalEntry->source_type }}</span>
                <span>#{{ $journalEntry->source_id }}</span>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header"><strong>@lang('messages.lines')</strong></div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('messages.account')</th>
                        <th>@lang('messages.description')</th>
                        <th>@lang('messages.debit')</th>
                        <th>@lang('messages.credit')</th>
                        <th>@lang('messages.currency')</th>
                        <th>@lang('messages.exchange_rate')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntry->lines as $i=>$line)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $line->account->name ?? '-' }}</td>
                        <td>{{ $line->description }}</td>
                        <td>{{ number_format($line->debit,2) }}</td>
                        <td>{{ number_format($line->credit,2) }}</td>
                        <td>{{ $line->currency }}</td>
                        <td>{{ $line->exchange_rate }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">@lang('messages.back')</a>
        <a href="{{ Route::localizedRoute('journal-entries.print', ['id' => $journalEntry->id, ]) }}" class="btn btn-primary" target="_blank"><i class="fa fa-print"></i> @lang('messages.print')</a>
    </div>
</div>
@endsection 