@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.salary_batch_details') {{ $salaryBatch->month }}</h1>
            <div class="action-buttons mb-3">
                <a href="{{ route('salary-batches.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> @lang('messages.back_to_batches')
                </a>
                
                @if($salaryBatch->status=='pending')
                <form action="{{ Route::localizedRoute('salary-batches.approve', ['salaryBatch' => $salaryBatch->id]) }}" method="POST" style="display:inline-block" onsubmit="return confirm('@lang('messages.approve_batch_confirm')');">
                    @csrf
                    <button class="btn btn-success">
                        <i class="fas fa-check"></i> @lang('messages.approve')
                    </button>
                </form>
                <form action="{{ Route::localizedRoute('salary-batches.destroy', ['salary_batch' => $salaryBatch, ]) }}" method="POST" style="display:inline-block" onsubmit="return confirm('@lang('messages.delete_confirm_text')');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">
                        <i class="fas fa-trash"></i> @lang('messages.delete')
                    </button>
                </form>
                @endif
                
                <!-- Print Button - Professional Design (Simplified) -->
                <a href="{{ route('salary-batches.print', $salaryBatch) }}" 
                   class="btn btn-primary btn-lg print-btn" 
                   target="_blank"
                   onclick="return handlePrint(this);">
                    <i class="fas fa-print"></i> 
                    <span class="btn-text">طباعة كشف الرواتب</span>
                    <small class="d-block">تصميم احترافي</small>
                </a>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <!-- ملخص العملات المتعددة -->
            @if(count($paymentsByCurrency) > 1)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5><i class="fas fa-coins"></i> ملخص العملات المتعددة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($paymentsByCurrency as $currency => $payments)
                                    <div class="col-md-4 mb-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-{{ $currency == 'USD' ? 'success' : ($currency == 'IQD' ? 'primary' : 'warning') }}">
                                                <i class="fas fa-{{ $currency == 'USD' ? 'dollar-sign' : ($currency == 'IQD' ? 'coins' : 'euro-sign') }}"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ $currency }}</span>
                                                <span class="info-box-number">{{ count($payments) }} موظف</span>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: {{ (count($payments) / $salaryBatch->salaryPayments->count()) * 100 }}%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    صافي الرواتب: {{ number_format($totalsByCurrency[$currency]['net'], 2) }} {{ $currency }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- أزرار الدفع السريع -->
            @if($salaryBatch->status == 'approved')
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-money-bill-wave"></i> دفع الرواتب (نظام العملات المتعددة)</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">يمكنك الآن دفع الرواتب بأي عملة متاحة مع التحويل التلقائي:</p>
                            <div class="row">
                                @foreach($paymentsByCurrency as $currency => $payments)
                                    @php
                                        $pendingCount = $payments->where('status', 'pending')->count();
                                    @endphp
                                    @if($pendingCount > 0)
                                    <div class="col-md-4 mb-2">
                                        <a href="{{ route('salary-payments.create') }}?salary_batch_id={{ $salaryBatch->id }}" 
                                           class="btn btn-outline-{{ $currency == 'USD' ? 'success' : ($currency == 'IQD' ? 'primary' : 'warning') }} btn-block">
                                            <i class="fas fa-{{ $currency == 'USD' ? 'dollar-sign' : ($currency == 'IQD' ? 'coins' : 'euro-sign') }}"></i>
                                            دفع رواتب {{ $currency }} ({{ $pendingCount }} معلق)
                                        </a>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            <small class="text-info">
                                <i class="fas fa-info-circle"></i> 
                                يمكنك دفع راتب موظف بالدولار مثلاً حتى لو كان راتبه بالدينار، وسيتم التحويل تلقائياً
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('messages.employee')</th>
                                <th>@lang('messages.currency')</th>
                                <th>@lang('messages.basic_salary')</th>
                                <th>@lang('messages.allowances')</th>
                                <th>@lang('messages.deductions')</th>
                                <th>@lang('messages.net_salary')</th>
                                <th>@lang('messages.status')</th>
                                @if($salaryBatch->status=='pending')
                                <th>@lang('messages.edit')</th>
                                @elseif($salaryBatch->status=='approved')
                                <th>الإجراءات</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentsByCurrency as $currency => $payments)
                                <!-- Currency header -->
                                <tr style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-left: 4px solid {{ $currency == 'USD' ? '#28a745' : ($currency == 'IQD' ? '#007bff' : '#ffc107') }};">
                                    <td colspan="{{ $salaryBatch->status=='pending' ? 9 : ($salaryBatch->status=='approved' ? 9 : 8) }}">
                                        <strong>
                                            <i class="fas fa-{{ $currency == 'USD' ? 'dollar-sign' : ($currency == 'IQD' ? 'coins' : 'euro-sign') }}"></i>
                                            {{ $currency }} 
                                            <small class="text-muted">({{ count($payments) }} موظف)</small>
                                        </strong>
                                    </td>
                                </tr>
                                
                                <!-- Employee rows for this currency -->
                                @foreach($payments as $i => $pay)
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>
                                            <strong>{{ $pay->employee->name ?? '-' }}</strong>
                                            @if($pay->employee->employee_number)
                                                <br><small class="text-muted">#{{ $pay->employee->employee_number }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $currency == 'USD' ? 'success' : ($currency == 'IQD' ? 'primary' : 'warning') }}">
                                                {{ $pay->employee->currency }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($pay->gross_salary, 2) }}</td>
                                        <td class="text-success">{{ number_format($pay->total_allowances, 2) }}</td>
                                        <td class="text-danger">{{ number_format($pay->total_deductions, 2) }}</td>
                                        <td><strong>{{ number_format($pay->net_salary, 2) }}</strong></td>
                                        <td>
                                            @if($pay->status=='pending')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> @lang('messages.status_pending')
                                                </span>
                                            @endif
                                            @if($pay->status=='paid')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> @lang('messages.status_paid')
                                                </span>
                                            @endif
                                            @if($pay->status=='cancelled')
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times"></i> @lang('messages.status_cancelled')
                                                </span>
                                            @endif
                                        </td>
                                        @if($salaryBatch->status=='pending')
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal" data-id="{{ $pay->id }}" data-allowances="{{ $pay->total_allowances }}" data-deductions="{{ $pay->total_deductions }}">@lang('messages.edit')</button>
                                        </td>
                                        @elseif($salaryBatch->status=='approved')
                                        <td>
                                            @if($pay->status == 'pending')
                                                <a href="{{ route('salary-payments.create') }}?salary_batch_id={{ $salaryBatch->id }}&employee_id={{ $pay->employee_id }}" 
                                                   class="btn btn-sm btn-success" title="دفع بنظام العملات المتعددة">
                                                    <i class="fas fa-coins"></i> دفع
                                                </a>
                                            @elseif($pay->voucher_id)
                                                <a href="{{ route('vouchers.show', $pay->voucher_id) }}" 
                                                   class="btn btn-sm btn-info" title="عرض سند الدفع">
                                                    <i class="fas fa-receipt"></i> السند
                                                </a>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                                
                                <!-- Subtotal for this currency -->
                                <tr style="font-weight:bold; background:#e9ecef;">
                                    <td colspan="3">{{ __('messages.subtotal') }} ({{ $currency }})</td>
                                    <td>{{ number_format($totalsByCurrency[$currency]['gross'], 2) }}</td>
                                    <td>{{ number_format($totalsByCurrency[$currency]['allowances'], 2) }}</td>
                                    <td>{{ number_format($totalsByCurrency[$currency]['deductions'], 2) }}</td>
                                    <td>{{ number_format($totalsByCurrency[$currency]['net'], 2) }}</td>
                                    <td colspan="{{ $salaryBatch->status=='pending' ? 2 : ($salaryBatch->status=='approved' ? 2 : 1) }}"></td>
                                </tr>
                            @endforeach
                                
                            @if($salaryBatch->salaryPayments->count() == 0)
                                <tr><td colspan="{{ $salaryBatch->status=='pending' ? 9 : ($salaryBatch->status=='approved' ? 9 : 8) }}" class="text-center">@lang('messages.no_payments_yet')</td></tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <!-- Grand totals for all currencies -->
                            <tr>
                                <td colspan="{{ $salaryBatch->status=='pending' ? 9 : ($salaryBatch->status=='approved' ? 9 : 8) }}" class="text-center bg-secondary text-white">
                                    <strong>@lang('messages.grand_total') @lang('messages.in_all_currencies')</strong>
                                </td>
                            </tr>
                            
                            @if(isset($grandTotalAllCurrencies))
                                @foreach($grandTotalAllCurrencies as $currCode => $totals)
                                    @if($totals['net'] > 0)
                                    <tr style="font-weight:bold; {{ $currCode == $defaultCurrency ? 'background:#d4edda; color:#155724;' : 'background:#f9f9f9;' }}">
                                        <td colspan="3">
                                            <i class="fas fa-{{ $currCode == 'USD' ? 'dollar-sign' : ($currCode == 'IQD' ? 'coins' : 'euro-sign') }}"></i>
                                            @lang('messages.grand_total') ({{ $currCode }})
                                            @if($currCode == $defaultCurrency)
                                                <i class="fas fa-star text-warning ml-1" title="العملة الافتراضية"></i>
                                            @endif
                                        </td>
                                        <td>{{ number_format($totals['gross'], 2) }}</td>
                                        <td>{{ number_format($totals['allowances'], 2) }}</td>
                                        <td>{{ number_format($totals['deductions'], 2) }}</td>
                                        <td>{{ number_format($totals['net'], 2) }}</td>
                                        <td colspan="{{ $salaryBatch->status=='pending' ? 2 : ($salaryBatch->status=='approved' ? 2 : 1) }}"></td>
                                    </tr>
                                    @endif
                                @endforeach
                            @endif
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

// Enhanced Print Function
function handlePrint(element) {
    // Add loading state
    const originalHtml = element.innerHTML;
    element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحضير...';
    
    // Restore original state after 2 seconds
    setTimeout(() => {
        element.innerHTML = originalHtml;
    }, 2000);
    
    return true; // Allow normal link behavior
}
</script>

<style>
.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}

.print-btn {
    position: relative;
    min-width: 200px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    text-decoration: none;
    color: white;
}

.print-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    text-decoration: none;
    color: white;
}

.print-btn i {
    font-size: 18px;
    margin-right: 8px;
}

.print-btn .btn-text {
    font-weight: 600;
    font-size: 14px;
}

.print-btn small {
    font-size: 11px;
    opacity: 0.8;
    margin-top: 2px;
}

@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
        align-items: stretch;
    }
    
    .print-btn {
        min-width: auto;
        width: 100%;
        text-align: center;
    }
}
</style>
@endpush 