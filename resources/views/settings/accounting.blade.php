@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">إعدادات الحسابات الافتراضية</h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('accounting-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>العملة</th>
                                    <th>حساب المبيعات الافتراضي</th>
                                    <th>حساب المشتريات الافتراضي</th>
                                    <th>حساب العملاء الافتراضي</th>
                                    <th>حساب الموردين الافتراضي</th>
                                    <th>حساب مصروف الرواتب</th>
                                    <th>حساب الذمم المستحقة للموظفين</th>
                                    <th>حساب الخصومات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currencies as $currency)
                                    <tr>
                                        <td>{{ $currency->code }}</td>
                                        <td>
                                            <select name="sales_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">-- اختر --</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->sales_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="purchases_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">-- اختر --</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->purchases_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="receivables_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">-- اختر --</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->receivables_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="payables_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">-- اختر --</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->payables_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="expenses_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">-- اختر --</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->expenses_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="liabilities_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">-- اختر --</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->liabilities_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="deductions_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">-- اختر --</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->deductions_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-success">حفظ الإعدادات</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 