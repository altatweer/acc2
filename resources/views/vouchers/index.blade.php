@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>@lang('messages.financial_vouchers')</h1>
            </div>
            <div class="col-sm-6 text-left">
                @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
                @if($isSuperAdmin || auth()->user()->can('add_voucher'))
                <a href="{{ Route::localizedRoute('vouchers.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle"></i> @lang('messages.create_new_voucher')
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">@lang('messages.vouchers_list')</h3>
                <div class="card-tools">
                    @if($isSuperAdmin || auth()->user()->can('add_voucher'))
                    <a href="{{ Route::localizedRoute('vouchers.create') }}" class="btn btn-sm btn-success">@lang('messages.new_voucher')</a>
                    @endif
                    @if(request('type') == 'transfer')
                    <a href="{{ Route::localizedRoute('vouchers.transfer.create') }}" class="btn btn-success mb-3">@lang('messages.add_transfer_voucher')</a>
                    @endif
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="{{ Route::localizedRoute('vouchers.index') }}" class="form-inline mb-3">
                    <!-- حفظ اللغة الحالية عند ارسال النموذج -->
                    <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                    <div class="input-group input-group-sm mr-2">
                        <select name="type" class="form-control">
                            <option value="">-- @lang('messages.type') --</option>
                            <option value="receipt" {{ request('type')=='receipt'?'selected':'' }}>@lang('messages.receipt_voucher')</option>
                            <option value="payment" {{ request('type')=='payment'?'selected':'' }}>@lang('messages.payment_voucher')</option>
                            <option value="transfer" {{ request('type')=='transfer'?'selected':'' }}>@lang('messages.transfer_voucher')</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm mr-2">
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                    </div>
                    <div class="input-group input-group-sm mr-2">
                        <input type="text" name="recipient_name" value="{{ request('recipient_name') }}" class="form-control" placeholder="@lang('messages.recipient_payer_placeholder')">
                    </div>
                    <button type="submit" class="btn btn-sm btn-info mr-2">@lang('messages.search_button')</button>
                    <a href="{{ Route::localizedRoute('vouchers.index') }}" class="btn btn-sm btn-secondary">@lang('messages.reset_button')</a>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center mb-0" id="vouchersTable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width:80px;">#</th>
                                <th>@lang('messages.voucher_number')</th>
                                <th>@lang('messages.voucher_type')</th>
                                <th>@lang('messages.date')</th>
                                <th>@lang('messages.accountant')</th>
                                <th>@lang('messages.recipient_payer')</th>
                                <th>@lang('messages.status')</th>
                                <th style="width:160px;">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $i => $voucher)
                                <tr>
                                    <td>{{ $vouchers->firstItem() + $i }}</td>
                                    <td>{{ $voucher->voucher_number }}</td>
                                    <td>
                                        @if($voucher->type == 'receipt')
                                            <span class="badge bg-success">@lang('messages.receipt_voucher')</span>
                                        @elseif($voucher->type == 'payment')
                                            <span class="badge bg-danger">@lang('messages.payment_voucher')</span>
                                        @elseif($voucher->type == 'transfer')
                                            <span class="badge bg-info">@lang('messages.transfer_voucher')</span>
                                        @endif
                                    </td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($voucher->date)->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $voucher->user->name ?? '-' }}</td>
                                    <td>{{ $voucher->recipient_name ?? '-' }}</td>
                                    <td>
                                        @if($voucher->status == 'active')
                                            <span class="badge badge-success">@lang('messages.active')</span>
                                        @else
                                            <span class="badge badge-danger">@lang('messages.cancelled')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($isSuperAdmin || auth()->user()->can('view_vouchers'))
                                            <a href="{{ Route::localizedRoute('vouchers.show', ['voucher' => $voucher->id]) }}" class="btn btn-outline-info" title="@lang('messages.view')">
                                                <i class="fas fa-eye"></i> @lang('messages.view')
                                            </a>
                                            <a href="{{ Route::localizedRoute('vouchers.print', ['voucher' => $voucher->id]) }}" class="btn btn-outline-primary" title="@lang('messages.print')" target="_blank">
                                                <i class="fas fa-print"></i> @lang('messages.print')
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4">@lang('messages.no_vouchers')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                <div>@lang('messages.total_vouchers_count') <strong>{{ $vouchers->total() }}</strong></div>
                <div>{{ $vouchers->appends(['lang' => app()->getLocale()])->links() }}</div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function(){
    $('#vouchersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/{{ app()->getLocale() == "ar" ? "ar" : "en" }}.json'
        },
        order: [[3, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        searching: true,
        responsive: true
    });
});
</script>
@endpush
