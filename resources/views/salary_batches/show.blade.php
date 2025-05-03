@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">كشف رواتب شهر {{ $salaryBatch->month }}</h1>
            <a href="{{ route('salary-batches.index') }}" class="btn btn-secondary">عودة للقائمة</a>
            @if($salaryBatch->status=='pending')
            <form action="{{ route('salary-batches.approve', $salaryBatch) }}" method="POST" style="display:inline-block" onsubmit="return confirm('هل تريد اعتماد هذا الكشف؟');">
                @csrf
                <button class="btn btn-success">اعتماد الكشف</button>
            </form>
            <form action="{{ route('salary-batches.destroy', $salaryBatch) }}" method="POST" style="display:inline-block" onsubmit="return confirm('هل تريد حذف هذا الكشف؟');">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">حذف الكشف</button>
            </form>
            @endif
            <button class="btn btn-info" onclick="window.print()">طباعة الكشف</button>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الموظف</th>
                                <th>الراتب الأساسي</th>
                                <th>البدلات</th>
                                <th>الخصومات</th>
                                <th>الصافي</th>
                                <th>الحالة</th>
                                @if($salaryBatch->status=='pending')
                                <th>تعديل</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sum_gross = 0;
                                $sum_allow = 0;
                                $sum_deduct = 0;
                                $sum_net = 0;
                            @endphp
                            @foreach($salaryBatch->salaryPayments as $i => $pay)
                                @php
                                    $sum_gross += $pay->gross_salary;
                                    $sum_allow += $pay->total_allowances;
                                    $sum_deduct += $pay->total_deductions;
                                    $sum_net += $pay->net_salary;
                                @endphp
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $pay->employee->name ?? '-' }}</td>
                                    <td>{{ number_format($pay->gross_salary, 2) }}</td>
                                    <td>{{ number_format($pay->total_allowances, 2) }}</td>
                                    <td>{{ number_format($pay->total_deductions, 2) }}</td>
                                    <td>{{ number_format($pay->net_salary, 2) }}</td>
                                    <td>
                                        @if($pay->status=='pending')<span class="badge badge-warning">معلق</span>@endif
                                        @if($pay->status=='paid')<span class="badge badge-success">مدفوع</span>@endif
                                        @if($pay->status=='cancelled')<span class="badge badge-danger">ملغي</span>@endif
                                    </td>
                                    @if($salaryBatch->status=='pending')
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal" data-id="{{ $pay->id }}" data-allowances="{{ $pay->total_allowances }}" data-deductions="{{ $pay->total_deductions }}">تعديل خصم/بدلات</button>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                            @if($salaryBatch->salaryPayments->count() == 0)
                                <tr><td colspan="{{ $salaryBatch->status=='pending' ? 8 : 7 }}" class="text-center">لا يوجد موظفون في هذا الكشف.</td></tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr style="font-weight:bold; background:#f9f9f9;">
                                <td colspan="2">المجموع</td>
                                <td>{{ number_format($sum_gross, 2) }}</td>
                                <td>{{ number_format($sum_allow, 2) }}</td>
                                <td>{{ number_format($sum_deduct, 2) }}</td>
                                <td>{{ number_format($sum_net, 2) }}</td>
                                <td colspan="{{ $salaryBatch->status=='pending' ? 2 : 1 }}"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="editForm" method="POST" action="{{ url('salary-payments/update-allowances-deductions') }}">
        @csrf
        <input type="hidden" name="salary_payment_id" id="modal_salary_payment_id">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">تعديل البدلات والخصومات</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>البدلات</label>
            <input type="number" step="0.01" class="form-control" name="total_allowances" id="modal_total_allowances">
          </div>
          <div class="form-group">
            <label>الخصومات</label>
            <input type="number" step="0.01" class="form-control" name="total_deductions" id="modal_total_deductions">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
$('#editModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var id = button.data('id');
  var allowances = button.data('allowances');
  var deductions = button.data('deductions');
  var modal = $(this);
  modal.find('#modal_salary_payment_id').val(id);
  modal.find('#modal_total_allowances').val(allowances);
  modal.find('#modal_total_deductions').val(deductions);
});
</script>
@endpush 