@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>تعديل سند مالي</h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('vouchers.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> العودة إلى السندات
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
                <h3 class="card-title">بيانات السند</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <form action="{{ route('vouchers.update', $voucher) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="voucher_type">نوع السند</label>
                            <select name="type" id="voucher_type" class="form-control select2" required>
                                <option value="" disabled>-- اختر النوع --</option>
                                <option value="receipt" {{ old('type', $voucher->type)=='receipt'?'selected':'' }}>سند قبض</option>
                                <option value="payment" {{ old('type', $voucher->type)=='payment'?'selected':'' }}>سند صرف</option>
                                <option value="transfer" {{ old('type', $voucher->type)=='transfer'?'selected':'' }}>سند تحويل</option>
                                <option value="deposit" {{ old('type', $voucher->type)=='deposit'?'selected':'' }}>إيداع نقدي</option>
                                <option value="withdraw" {{ old('type', $voucher->type)=='withdraw'?'selected':'' }}>سحب نقدي</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="voucher_date">التاريخ</label>
                            <input type="date" name="date" id="voucher_date" class="form-control" value="{{ old('date', $voucher->date) }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="recipient_name">مستلم/دافع</label>
                            <input type="text" name="recipient_name" id="recipient_name" class="form-control" value="{{ old('recipient_name', $voucher->recipient_name) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="voucher_description">وصف عام للسند</label>
                        <textarea name="description" id="voucher_description" rows="2" class="form-control">{{ old('description', $voucher->description) }}</textarea>
                    </div>

                    <hr>
                    <h5 class="mb-3">الحركات المالية المرتبطة بالسند</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="transactions_table">
                            <thead class="thead-light">
                                <tr>
                                    <th>حساب الصندوق</th>
                                    <th>الحساب المستهدف</th>
                                    <th>المبلغ</th>
                                    <th>وصف الحركة</th>
                                    <th style="width:100px;">إجراء</th>
                                </tr>
                            </thead>
                            <tbody id="transactions_body">
                                @foreach($voucher->transactions as $i => $tx)
                                    <tr>
                                        <td>
                                            <select name="transactions[{{ $i }}][account_id]" class="form-control select2" required>
                                                @foreach($cashAccounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ old('transactions.'.$i.'.account_id', $tx->account_id)==$acc->id?'selected':'' }}>{{ $acc->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="transactions[{{ $i }}][target_account_id]" class="form-control select2">
                                                <option value="">-- اختر حساب --</option>
                                                @foreach($targetAccounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ old('transactions.'.$i.'.target_account_id', $tx->target_account_id)==$acc->id?'selected':'' }}>{{ $acc->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="transactions[{{ $i }}][amount]" value="{{ old('transactions.'.$i.'.amount', $tx->amount) }}" step="0.01" class="form-control" required></td>
                                        <td><input type="text" name="transactions[{{ $i }}][description]" value="{{ old('transactions.'.$i.'.description', $tx->description) }}" class="form-control"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm remove-transaction"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <button type="button" id="add_transaction" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> إضافة حركة</button>
                    <button type="submit" class="btn btn-success float-left"><i class="fas fa-save"></i> حفظ التعديلات</button>
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
    $('.select2').select2({ theme: 'bootstrap4' });
    let idx = {{ $voucher->transactions->count() }};
    $('#add_transaction').click(function(){
        let row = `
            <tr>
                <td>
                    <select name="transactions[${idx}][account_id]" class="form-control select2" required>
                        @foreach($cashAccounts as $acc)
                            <option value="${acc->id}">${acc->name}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="transactions[${idx}][target_account_id]" class="form-control select2">
                        <option value="">-- اختر حساب --</option>
                        @foreach($targetAccounts as $acc)
                            <option value="${acc->id}">${acc->name}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="transactions[${idx}][amount]" step="0.01" class="form-control" required></td>
                <td><input type="text" name="transactions[${idx}][description]" class="form-control"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-transaction"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
        $('#transactions_body').append(row);
        $('.select2').select2({ theme: 'bootstrap4' });
        idx++;
    });
    $(document).on('click','.remove-transaction',function(){
        $(this).closest('tr').remove();
    });
});
</script>
@endpush 