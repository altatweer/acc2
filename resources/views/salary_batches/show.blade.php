@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.salary_batch_details') {{ $salaryBatch->month }}</h1>
            <a href="{{ route('salary-batches.index') }}" class="btn btn-secondary">@lang('messages.back_to_batches')</a>
            @if($salaryBatch->status=='pending')
            <form action="{{ Route::localizedRoute('salary-batches.approve', ['salary_batch' => $salaryBatch, ]) }}" method="POST" style="display:inline-block" onsubmit="return confirm('@lang('messages.approve_batch_confirm')');">
                @csrf
                <button class="btn btn-success">@lang('messages.approve')</button>
            </form>
            <form action="{{ Route::localizedRoute('salary-batches.destroy', ['salary_batch' => $salaryBatch, ]) }}" method="POST" style="display:inline-block" onsubmit="return confirm('@lang('messages.delete_confirm_text')');">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">@lang('messages.delete')</button>
            </form>
            @endif
            <button class="btn btn-info" onclick="window.print()">@lang('messages.print')</button>
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
                                <th>@lang('messages.employee')</th>
                                <th>@lang('messages.basic_salary')</th>
                                <th>@lang('messages.allowances')</th>
                                <th>@lang('messages.deductions')</th>
                                <th>@lang('messages.net_salary')</th>
                                <th>@lang('messages.status')</th>
                                @if($salaryBatch->status=='pending')
                                <th>@lang('messages.edit')</th>
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
                                        @if($pay->status=='pending')<span class="badge badge-warning">@lang('messages.status_pending')</span>@endif
                                        @if($pay->status=='paid')<span class="badge badge-success">@lang('messages.status_paid')</span>@endif
                                        @if($pay->status=='cancelled')<span class="badge badge-danger">@lang('messages.status_cancelled')</span>@endif
                                    </td>
                                    @if($salaryBatch->status=='pending')
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal" data-id="{{ $pay->id }}" data-allowances="{{ $pay->total_allowances }}" data-deductions="{{ $pay->total_deductions }}">@lang('messages.edit')</button>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                            @if($salaryBatch->salaryPayments->count() == 0)
                                <tr><td colspan="{{ $salaryBatch->status=='pending' ? 8 : 7 }}" class="text-center">@lang('messages.no_payments_yet')</td></tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr style="font-weight:bold; background:#f9f9f9;">
                                <td colspan="2">@lang('messages.total')</td>
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
      <form id="editForm" method="POST" action="{{ url('salary-payments/update-allowances-deductions', ['lang' => app()->getLocale()]) }}">
        @csrf
        <input type="hidden" name="salary_payment_id" id="modal_salary_payment_id">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">@lang('messages.edit') @lang('messages.allowances') & @lang('messages.deductions')</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="@lang('messages.close')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>@lang('messages.allowances')</label>
            <input type="number" step="0.01" class="form-control" name="total_allowances" id="modal_total_allowances">
          </div>
          <div class="form-group">
            <label>@lang('messages.deductions')</label>
            <input type="number" step="0.01" class="form-control" name="total_deductions" id="modal_total_deductions">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.cancel')</button>
          <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
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