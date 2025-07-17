@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3 class="m-0">@lang('messages.edit_real_account')</h3>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-warning">
                <div class="card-header">
                    <h5 class="card-title">@lang('messages.real_account_data')</h5>
                </div>
                <form action="{{ route('accounts.update', ['account' => $account]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <input type="hidden" name="is_group" value="0">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">@lang('messages.account_name')</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $account->name) }}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('messages.account_code')</label>
                                <input type="text" id="code" class="form-control" value="{{ $account->code }}" disabled>
                                <input type="hidden" id="codeInput" name="code" value="{{ $account->code }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="parent_id">@lang('messages.select_parent_category')</label>
                                <select id="parent_id" name="parent_id" class="form-control" required>
                                    <option value="">-- @lang('messages.select_category') --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('parent_id', $account->parent_id) == $cat->id ? 'selected' : '' }}
                                                data-currency="{{ $cat->currency ?? '' }}"
                                                style="color: {{ ($cat->currency ?? '') == 'IQD' ? '#1976d2' : (($cat->currency ?? '') == 'USD' ? '#388e3c' : '#5e35b1') }};">
                                            {{ $cat->name }}
                                            @if($cat->currency)
                                                <span style="font-weight: bold; background: {{ ($cat->currency ?? '') == 'IQD' ? '#e3f2fd' : (($cat->currency ?? '') == 'USD' ? '#e8f5e8' : '#f3e5f5') }}; padding: 2px 6px; border-radius: 3px; font-size: 0.85em;">
                                                    {{ strtoupper($cat->currency) }}
                                                </span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="nature">@lang('messages.account_nature')</label>
                                <select id="nature" name="nature" class="form-control" required>
                                    <option value="">-- @lang('messages.select_option') --</option>
                                    <option value="debit" {{ old('nature', $account->nature) == 'debit' ? 'selected' : '' }}>@lang('messages.debit_nature')</option>
                                    <option value="credit" {{ old('nature', $account->nature) == 'credit' ? 'selected' : '' }}>@lang('messages.credit_nature')</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group form-check">
                            <input type="hidden" name="is_cash_box" value="0">
                            <input type="checkbox" id="isCashBox" name="is_cash_box" value="1" class="form-check-input" {{ old('is_cash_box', $account->is_cash_box) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isCashBox">@lang('messages.is_cash_box')</label>
                        </div>

                        <!-- معلومات العملات المدعومة -->
                        <div class="alert alert-success border-left-success shadow h-100 py-2">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        <i class="fas fa-coins"></i> العملات المدعومة
                                    </div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        يدعم هذا الحساب جميع العملات النشطة
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-star text-warning"></i> 
                                        العملة الافتراضية: <strong>{{ $account->default_currency ?? 'IQD' }}</strong>
                                        <br>
                                        <i class="fas fa-info-circle text-info"></i> 
                                        يمكن إجراء معاملات بأي عملة نشطة على هذا الحساب
                                    </small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>

                        @php
                            // فحص وجود حركات مالية في الحساب
                            $hasTransactions = \App\Models\JournalEntryLine::where('account_id', $account->id)->exists();
                            $canEditOpeningBalance = !$hasTransactions;
                        @endphp

                        <!-- قسم الرصيد الافتتاحي -->
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-balance-scale"></i> الرصيد الافتتاحي
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($account->has_opening_balance)
                                    <!-- عرض الرصيد الافتتاحي الحالي -->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>المبلغ:</strong><br>
                                            <span class="h5 text-primary">
                                                {{ number_format($account->opening_balance, 2) }}
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>العملة:</strong><br>
                                            <span class="badge badge-info badge-lg">
                                                {{ $account->opening_balance_currency ?? 'IQD' }}
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>النوع:</strong><br>
                                            <span class="badge badge-{{ $account->opening_balance_type == 'debit' ? 'warning' : 'success' }} badge-lg">
                                                {{ $account->opening_balance_type == 'debit' ? 'مدين' : 'دائن' }}
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>التاريخ:</strong><br>
                                            {{ \Carbon\Carbon::parse($account->opening_balance_date)->format('Y-m-d') }}
                                        </div>
                                    </div>

                                    @if($canEditOpeningBalance)
                                        <div class="mt-3">
                                            <div class="form-group form-check">
                                                <input type="checkbox" name="edit_opening_balance" value="1" class="form-check-input" 
                                                       id="editOpeningBalanceCheck" onchange="toggleEditOpeningBalance()">
                                                <label class="form-check-label" for="editOpeningBalanceCheck">
                                                    <strong>تعديل الرصيد الافتتاحي</strong>
                                                </label>
                                            </div>

                                            <!-- تفاصيل تعديل الرصيد الافتتاحي -->
                                            <div id="editOpeningBalanceSection" style="display: none;" class="border p-3 bg-light rounded">
                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label for="opening_balance_amount">مبلغ الرصيد الافتتاحي</label>
                                                        <input type="number" step="0.01" id="opening_balance_amount" name="opening_balance_amount" 
                                                               value="{{ $account->opening_balance }}" class="form-control">
                                                    </div>
                                                    
                                                    <div class="form-group col-md-4">
                                                        <label for="opening_balance_currency">عملة الرصيد الافتتاحي</label>
                                                        <select id="opening_balance_currency" name="opening_balance_currency" class="form-control">
                                                            @foreach($currencies as $currency)
                                                                <option value="{{ $currency->code }}" 
                                                                        {{ ($account->opening_balance_currency ?? 'IQD') == $currency->code ? 'selected' : '' }}
                                                                        style="color: {{ $currency->code == 'IQD' ? '#1976d2' : ($currency->code == 'USD' ? '#388e3c' : '#5e35b1') }};">
                                                                    {{ $currency->code }} - {{ $currency->name }}
                                                                    @if($currency->is_default)
                                                                        <span style="font-weight: bold;">(افتراضي)</span>
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="form-group col-md-4">
                                                        <label for="opening_balance_type">نوع الرصيد</label>
                                                        <select id="opening_balance_type" name="opening_balance_type" class="form-control">
                                                            <option value="debit" {{ $account->opening_balance_type == 'debit' ? 'selected' : '' }}>مدين</option>
                                                            <option value="credit" {{ $account->opening_balance_type == 'credit' ? 'selected' : '' }}>دائن</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="opening_balance_date">تاريخ الرصيد الافتتاحي</label>
                                                    <input type="date" id="opening_balance_date" name="opening_balance_date" 
                                                           value="{{ $account->opening_balance_date }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>لا يمكن تعديل الرصيد الافتتاحي:</strong> يحتوي هذا الحساب على حركات مالية.
                                        </div>
                                    @endif

                                @else
                                    <!-- إضافة رصيد افتتاحي جديد -->
                                    @if($canEditOpeningBalance)
                                        <div class="form-group form-check">
                                            <input type="checkbox" name="has_opening_balance" value="1" class="form-check-input" 
                                                   id="openingBalanceCheck" onchange="toggleOpeningBalance()">
                                            <label class="form-check-label" for="openingBalanceCheck">
                                                <strong>إضافة رصيد افتتاحي</strong>
                                            </label>
                                        </div>

                                        <!-- تفاصيل الرصيد الافتتاحي -->
                                        <div id="openingBalanceSection" style="display: none;" class="border p-3 bg-light rounded">
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="new_opening_balance_amount">مبلغ الرصيد الافتتاحي</label>
                                                    <input type="number" step="0.01" id="new_opening_balance_amount" name="opening_balance_amount" 
                                                           class="form-control" placeholder="0.00">
                                                </div>
                                                
                                                <div class="form-group col-md-4">
                                                    <label for="new_opening_balance_currency">عملة الرصيد الافتتاحي</label>
                                                    <select id="new_opening_balance_currency" name="opening_balance_currency" class="form-control">
                                                        <option value="">-- اختر العملة --</option>
                                                        @foreach($currencies as $currency)
                                                            <option value="{{ $currency->code }}" 
                                                                    {{ $currency->code == 'IQD' ? 'selected' : '' }}
                                                                    style="color: {{ $currency->code == 'IQD' ? '#1976d2' : ($currency->code == 'USD' ? '#388e3c' : '#5e35b1') }};">
                                                                {{ $currency->code }} - {{ $currency->name }}
                                                                @if($currency->is_default)
                                                                    <span style="font-weight: bold;">(افتراضي)</span>
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group col-md-4">
                                                    <label for="new_opening_balance_type">نوع الرصيد</label>
                                                    <select id="new_opening_balance_type" name="opening_balance_type" class="form-control">
                                                        <option value="">-- اختر نوع الرصيد --</option>
                                                        <option value="debit">مدين</option>
                                                        <option value="credit">دائن</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="new_opening_balance_date">تاريخ الرصيد الافتتاحي</label>
                                                <input type="date" id="new_opening_balance_date" name="opening_balance_date" 
                                                       value="{{ date('Y-m-d') }}" class="form-control">
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>لا يمكن إضافة رصيد افتتاحي:</strong> يحتوي هذا الحساب على حركات مالية.
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> @lang('messages.update')
                        </button>
                        <a href="{{ route('accounts.real') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> @lang('messages.cancel')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    function refreshAccountCode(){
        let isGroup = 0;
        let parent_id = $('#parent_id').val() || '';
        $.getJSON("{{ route('accounts.nextCode') }}", { is_group: isGroup, parent_id: parent_id }, function(data){
            $('#code').val(data.nextCode);
            $('#codeInput').val(data.nextCode);
        });
    }
    $('#parent_id').on('change', refreshAccountCode);
});

// دوال إظهار/إخفاء أقسام الرصيد الافتتاحي
function toggleOpeningBalance() {
    const checkbox = document.getElementById('openingBalanceCheck');
    const section = document.getElementById('openingBalanceSection');
    
    if (checkbox && section) {
        if (checkbox.checked) {
            section.style.display = 'block';
            document.getElementById('new_opening_balance_amount').required = true;
            document.getElementById('new_opening_balance_currency').required = true;
            document.getElementById('new_opening_balance_type').required = true;
            document.getElementById('new_opening_balance_date').required = true;
        } else {
            section.style.display = 'none';
            document.getElementById('new_opening_balance_amount').required = false;
            document.getElementById('new_opening_balance_currency').required = false;
            document.getElementById('new_opening_balance_type').required = false;
            document.getElementById('new_opening_balance_date').required = false;
        }
    }
}

function toggleEditOpeningBalance() {
    const checkbox = document.getElementById('editOpeningBalanceCheck');
    const section = document.getElementById('editOpeningBalanceSection');
    
    if (checkbox && section) {
        if (checkbox.checked) {
            section.style.display = 'block';
            document.getElementById('opening_balance_amount').required = true;
            document.getElementById('opening_balance_currency').required = true;
            document.getElementById('opening_balance_type').required = true;
            document.getElementById('opening_balance_date').required = true;
        } else {
            section.style.display = 'none';
            document.getElementById('opening_balance_amount').required = false;
            document.getElementById('opening_balance_currency').required = false;
            document.getElementById('opening_balance_type').required = false;
            document.getElementById('opening_balance_date').required = false;
        }
    }
}
</script>
@endpush
