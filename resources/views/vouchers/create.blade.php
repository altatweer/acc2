@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>@lang('messages.create_new_financial_voucher')</h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ Route::localizedRoute('vouchers.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> @lang('messages.return_to_vouchers')
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">@lang('messages.voucher_information')</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <form action="{{ Route::localizedRoute('vouchers.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>@lang('messages.validation_errors')</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="voucher_type">@lang('messages.voucher_type')</label>
                            <select name="type" id="voucher_type" class="form-control select2" required>
                                <option value="" disabled {{ old('type')? '':'selected' }}>@lang('messages.choose_type')</option>
                                <option value="receipt" {{ old('type')=='receipt'?'selected':'' }}>@lang('messages.receipt_voucher')</option>
                                <option value="payment" {{ old('type')=='payment'?'selected':'' }}>@lang('messages.payment_voucher')</option>
                                <option value="deposit" {{ old('type')=='deposit'?'selected':'' }}>@lang('messages.voucher_deposit')</option>
                                <option value="withdraw" {{ old('type')=='withdraw'?'selected':'' }}>@lang('messages.voucher_withdraw')</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="voucher_date">@lang('messages.voucher_date')</label>
                            <input type="datetime-local" name="date" id="voucher_date" class="form-control" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="recipient_name">@lang('messages.recipient_payer_name')</label>
                            <input type="text" name="recipient_name" id="recipient_name" class="form-control" value="{{ old('recipient_name') }}" placeholder="@lang('messages.recipient_payer_placeholder_name')" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="voucher_description">@lang('messages.general_voucher_description')</label>
                        <textarea name="description" id="voucher_description" rows="2" class="form-control" placeholder="@lang('messages.optional')">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="voucher_currency">@lang('messages.currency')</label>
                            <select name="currency" id="voucher_currency" class="form-control select2" required>
                                @foreach($currencies as $cur)
                                    <option value="{{ $cur->code }}" {{ old('currency', $defaultCurrency->code) == $cur->code ? 'selected' : '' }}>{{ $cur->code }} - {{ $cur->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">@lang('messages.financial_transactions_related')</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="transactions_table">
                            <thead class="thead-light">
                                <tr>
                                    <th>@lang('messages.cash_account')</th>
                                    <th>@lang('messages.target_account')</th>
                                    <th>@lang('messages.amount')</th>
                                    <th>@lang('messages.transaction_description')</th>
                                    <th style="width:100px;">@lang('messages.action')</th>
                                </tr>
                            </thead>
                            <tbody id="transactions_body">
                                <tr>
                                    <td>
                                        <select name="transactions[0][account_id]" class="form-control select2" required>
                                            <option value="">@lang('messages.choose_cash_account')</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="transactions[0][target_account_id]" class="form-control select2">
                                            <option value="">@lang('messages.choose_account')</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="transactions[0][amount]" value="{{ old('transactions.0.amount') }}" step="0.01" class="form-control" required></td>
                                    <td><input type="text" name="transactions[0][description]" value="{{ old('transactions.0.description') }}" class="form-control"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-transaction"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <button type="button" id="add_transaction" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> @lang('messages.add_transaction')</button>
                    <button type="submit" class="btn btn-success float-left"><i class="fas fa-save"></i> @lang('messages.save_voucher')</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    const loadAccounts = (currency) => {
        if(!currency) return;
        $.getJSON('/accounts/by-currency/' + currency, function(data){
            // populate each row's selects
            $('#transactions_body tr').each(function(){
                const $row = $(this);
                const $cashSel = $row.find('select[name$="[account_id]"]');
                const $tgtSel  = $row.find('select[name$="[target_account_id]"]');

                $cashSel.empty().append($('<option>').val('').text('@lang('messages.choose_cash_account')'));
                data.cashAccounts.forEach(acc => {
                    $cashSel.append($('<option>').val(acc.id).text(acc.code + ' - ' + acc.name));
                });

                $tgtSel.empty().append($('<option>').val('').text('@lang('messages.choose_account')'));
                data.targetAccounts.forEach(acc => {
                    $tgtSel.append($('<option>').val(acc.id).text(acc.code + ' - ' + acc.name));
                });

                $row.find('select.select2').select2({ theme: 'bootstrap4' });
            });
        });
    };

    // initialize Select2 on currency
    $('#voucher_currency').select2({ theme: 'bootstrap4' })
        // bind change handler before triggering initial load
        .on('change', function(){
            loadAccounts($(this).val());
        })
        // set default value and trigger change to load accounts
        .val('{{ old('currency', $defaultCurrency->code) }}')
        .trigger('change');

    // Template row for adding
    let idx = 1;
    const $template = $('#transactions_body tr:first').clone();

    $('#add_transaction').click(function(){
        const $new = $template.clone();
        $new.find('select, input').each(function(){
            const name = $(this).attr('name');
            if(name) {
                const newName = name.replace(/transactions\[\d+\]/, 'transactions['+ idx +']');
                $(this).attr('name', newName);
            }
        });
        $('#transactions_body').append($new);
        loadAccounts($('#voucher_currency').val());
        idx++;
    });

    $(document).on('click', '.remove-transaction', function(){
        $(this).closest('tr').remove();
    });
});
</script>
@endpush