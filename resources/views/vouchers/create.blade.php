@extends('layouts.app')

@section('content')
<div class="content-wrapper">
   <div class="content-header">
       <div class="container-fluid">
           <h1 class="m-0">إنشاء سند جديد</h1>
       </div>
   </div>

   <section class="content">
       <div class="container-fluid">

           <div class="card card-primary">
               <div class="card-body">
                   <form method="POST" action="{{ route('vouchers.store') }}">
                       @csrf

                       <div class="row">
                           <div class="col-md-4">
                               <div class="form-group">
                                   <label>نوع السند</label>
                                   <select name="type" id="voucher_type" class="form-control select2">
                                       <option value="receipt">قبض</option>
                                       <option value="payment">صرف</option>
                                       <option value="transfer">تحويل</option>
                                   </select>
                               </div>
                           </div>

                           <div class="col-md-4">
                               <div class="form-group">
                                   <label>التاريخ</label>
                                   <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                               </div>
                           </div>

                           <div class="col-md-4">
                               <div class="form-group">
                                   <label>اسم المستلم / الدافع</label>
                                   <input type="text" name="recipient_name" class="form-control">
                               </div>
                           </div>

                           <div class="col-12">
                               <div class="form-group">
                                   <label>وصف عام للسند</label>
                                   <textarea name="description" class="form-control" rows="2"></textarea>
                               </div>
                           </div>
                       </div>

                       <hr>

                       <h5 class="mb-3">الحركات المالية المرتبطة بالسند:</h5>

                       <div class="table-responsive">
                           <table class="table table-bordered" id="transactions_table">
                               <thead class="thead-light">
                                   <tr>
                                       <th>الحساب الرئيسي (الصندوق)</th>
                                       <th>الحساب المستهدف</th>
                                       <th>المبلغ</th>
                                       <th>العملة</th>
                                       <th>سعر الصرف</th>
                                       <th>وصف الحركة</th>
                                       <th>إجراء</th>
                                   </tr>
                               </thead>
                               <tbody id="transactions_body">
                                   <tr>
                                       <td>
                                           <select name="transactions[0][account_id]" class="form-control account-select select2">
                                               @foreach($cashAccounts as $account)
                                                   <option value="{{ $account->id }}">{{ $account->name }}</option>
                                               @endforeach
                                           </select>
                                       </td>
                                       <td>
                                           <select name="transactions[0][target_account_id]" class="form-control target-account-select select2">
                                               <option value="">-- اختر الحساب --</option>
                                               @foreach($normalAccounts as $account)
                                                   <option value="{{ $account->id }}">{{ $account->name }}</option>
                                               @endforeach
                                           </select>
                                       </td>
                                       <td><input type="number" step="0.01" name="transactions[0][amount]" class="form-control"></td>
                                       <td>
                                           <select name="transactions[0][currency]" class="form-control select2">
                                               @foreach($currencies as $currency)
                                                   <option value="{{ $currency->code }}">{{ $currency->code }}</option>
                                               @endforeach
                                           </select>
                                       </td>
                                       <td><input type="number" step="0.0001" name="transactions[0][exchange_rate]" value="1" class="form-control"></td>
                                       <td><input type="text" name="transactions[0][description]" class="form-control"></td>
                                       <td class="text-center">
                                           <button type="button" class="btn btn-danger btn-sm remove-transaction">حذف</button>
                                       </td>
                                   </tr>
                               </tbody>
                           </table>
                       </div>

                       <button type="button" id="add_transaction" class="btn btn-secondary btn-block">إضافة حركة مالية أخرى</button>

                       <div class="form-group mt-4">
                           <button type="submit" class="btn btn-success btn-lg btn-block">حفظ السند</button>
                       </div>
                   </form>
               </div>
           </div>

       </div>
   </section>
</div>
@endsection

@push('scripts')
<!-- تحميل مكتبة Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
   $('.select2').select2();

   let transactionIndex = 1;

   $('#add_transaction').click(function() {
       var html = `
           <tr>
               <td>
                   <select name="transactions[${transactionIndex}][account_id]" class="form-control select2">
                       @foreach($cashAccounts as $account)
                           <option value="{{ $account->id }}">{{ $account->name }}</option>
                       @endforeach
                   </select>
               </td>
               <td>
                   <select name="transactions[${transactionIndex}][target_account_id]" class="form-control select2">
                       <option value="">-- اختر الحساب --</option>
                       @foreach($normalAccounts as $account)
                           <option value="{{ $account->id }}">{{ $account->name }}</option>
                       @endforeach
                   </select>
               </td>
               <td><input type="number" step="0.01" name="transactions[${transactionIndex}][amount]" class="form-control"></td>
               <td>
                   <select name="transactions[${transactionIndex}][currency]" class="form-control select2">
                       @foreach($currencies as $currency)
                           <option value="{{ $currency->code }}">{{ $currency->code }}</option>
                       @endforeach
                   </select>
               </td>
               <td><input type="number" step="0.0001" name="transactions[${transactionIndex}][exchange_rate]" value="1" class="form-control"></td>
               <td><input type="text" name="transactions[${transactionIndex}][description]" class="form-control"></td>
               <td class="text-center">
                   <button type="button" class="btn btn-danger btn-sm remove-transaction">حذف</button>
               </td>
           </tr>
       `;

       $('#transactions_body').append(html);
       $('.select2').select2();
       transactionIndex++;
   });

   $(document).on('click', '.remove-transaction', function() {
       $(this).closest('tr').remove();
   });

});
</script>
@endpush