@extends('layouts.app')
@section('title', __('messages.payroll_report'))
@section('content')
@if(isset($export) && $export)
    <style>
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
            direction: rtl;
        }
    </style>
@endif
<div class="container">
    <h2 class="mb-4">{{ __('messages.payroll_report') }}</h2>
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ Route::localizedRoute('reports.payroll.excel', request()->only(['month','employee'])) }}" class="btn btn-outline-success me-2"><i class="fas fa-file-excel"></i> {{ __('messages.export_excel') }}</a>
        <a href="{{ Route::localizedRoute('reports.payroll.pdf', request()->only(['month','employee'])) }}" class="btn btn-outline-danger me-2"><i class="fas fa-file-pdf"></i> {{ __('messages.export_pdf') }}</a>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> {{ __('messages.print') }}</button>
    </div>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="month" class="form-label">{{ __('messages.month') }}</label>
            <input type="month" name="month" id="month" class="form-control" value="{{ request('month') }}">
        </div>
        <div class="col-md-3">
            <label for="employee" class="form-label">{{ __('messages.employee') }}</label>
            <input type="text" name="employee" id="employee" class="form-control" placeholder="{{ __('messages.search_employee') }}" value="{{ request('employee') }}">
        </div>
        <div class="col-md-2">
            <label for="currency" class="form-label">{{ __('messages.currency') }}</label>
            <select name="currency" id="currency" class="form-select">
                <option value="">{{ __('messages.all_currencies') }}</option>
                @foreach($currencies as $curr)
                    <option value="{{ $curr->code }}" {{ request('currency') == $curr->code ? 'selected' : '' }}>
                        {{ $curr->code }} - {{ $curr->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="display_currency" class="form-label">{{ __('messages.display_in_currency') ?? 'عرض بعملة' }}</label>
            <select name="display_currency" id="display_currency" class="form-select">
                <option value="">{{ __('messages.original_currency') ?? 'العملة الأصلية' }}</option>
                @foreach($currencies as $curr)
                    <option value="{{ $curr->code }}" {{ request('display_currency') == $curr->code ? 'selected' : '' }}>
                        {{ $curr->code }} - {{ $curr->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100">{{ __('messages.show_report') }}</button>
        </div>
    </form>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.employee_name') }}</th>
                            <th>{{ __('messages.month') }}</th>
                            <th>{{ __('messages.currency') }}</th>
                            <th>{{ __('messages.basic_salary') }}</th>
                            <th>{{ __('messages.allowances') }}</th>
                            <th>{{ __('messages.deductions') }}</th>
                            <th>{{ __('messages.net_salary') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // تجميع الصفوف حسب العملة
                            $rowsByCurrency = collect($rows)->groupBy(function($row) {
                                return $row->employee ? $row->employee->currency : 'Unknown';
                            });
                            
                            // حساب المجاميع حسب كل عملة
                            $totals = [];
                            foreach($rowsByCurrency as $currency => $currencyRows) {
                                $totals[$currency] = [
                                    'gross' => $currencyRows->sum('gross_salary'),
                                    'allowances' => $currencyRows->sum('total_allowances'),
                                    'deductions' => $currencyRows->sum('total_deductions'),
                                    'net' => $currencyRows->sum('net_salary'),
                                ];
                            }
                            
                            // عملة العرض المختارة
                            $displayCurrency = request('display_currency');
                        @endphp
                        
                        @if($displayCurrency)
                            <!-- عرض جميع البيانات بعملة واحدة -->
                            @foreach($rows as $row)
                                @php
                                    $originalCurrency = $row->employee ? $row->employee->currency : 'Unknown';
                                    // تحويل القيم إلى العملة المختارة
                                    $convertedGross = ($originalCurrency != $displayCurrency) ? 
                                        \App\Helpers\CurrencyHelper::convert($row->gross_salary, $originalCurrency, $displayCurrency) : 
                                        $row->gross_salary;
                                    
                                    $convertedAllowances = ($originalCurrency != $displayCurrency) ? 
                                        \App\Helpers\CurrencyHelper::convert($row->total_allowances, $originalCurrency, $displayCurrency) : 
                                        $row->total_allowances;
                                    
                                    $convertedDeductions = ($originalCurrency != $displayCurrency) ? 
                                        \App\Helpers\CurrencyHelper::convert($row->total_deductions, $originalCurrency, $displayCurrency) : 
                                        $row->total_deductions;
                                    
                                    $convertedNet = ($originalCurrency != $displayCurrency) ? 
                                        \App\Helpers\CurrencyHelper::convert($row->net_salary, $originalCurrency, $displayCurrency) : 
                                        $row->net_salary;
                                @endphp
                                <tr>
                                    <td>{{ $row->employee->name ?? '-' }}</td>
                                    <td>{{ $row->salary_month }}</td>
                                    <td>{{ $originalCurrency }} <i class="fas fa-arrow-right text-muted"></i> {{ $displayCurrency }}</td>
                                    <td>{{ number_format($convertedGross, 2) }}</td>
                                    <td>{{ number_format($convertedAllowances, 2) }}</td>
                                    <td>{{ number_format($convertedDeductions, 2) }}</td>
                                    <td>{{ number_format($convertedNet, 2) }}</td>
                                </tr>
                            @endforeach
                            
                            <!-- المجموع الكلي بالعملة المختارة -->
                            @php
                                $totalConvertedGross = 0;
                                $totalConvertedAllowances = 0;
                                $totalConvertedDeductions = 0;
                                $totalConvertedNet = 0;
                                
                                // حساب المجموع الكلي بعد تحويل جميع العملات
                                foreach($totals as $currency => $total) {
                                    if ($currency != $displayCurrency) {
                                        $totalConvertedGross += \App\Helpers\CurrencyHelper::convert(
                                            $total['gross'], $currency, $displayCurrency
                                        );
                                        $totalConvertedAllowances += \App\Helpers\CurrencyHelper::convert(
                                            $total['allowances'], $currency, $displayCurrency
                                        );
                                        $totalConvertedDeductions += \App\Helpers\CurrencyHelper::convert(
                                            $total['deductions'], $currency, $displayCurrency
                                        );
                                        $totalConvertedNet += \App\Helpers\CurrencyHelper::convert(
                                            $total['net'], $currency, $displayCurrency
                                        );
                                    } else {
                                        $totalConvertedGross += $total['gross'];
                                        $totalConvertedAllowances += $total['allowances'];
                                        $totalConvertedDeductions += $total['deductions'];
                                        $totalConvertedNet += $total['net'];
                                    }
                                }
                            @endphp
                            
                            <!-- عرض المجموع الكلي بالعملة المختارة -->
                            <tr class="table-dark text-white">
                                <td colspan="3" class="fw-bold">{{ __('messages.grand_total') }} ({{ $displayCurrency }})</td>
                                <td class="fw-bold">{{ number_format($totalConvertedGross, 2) }}</td>
                                <td class="fw-bold">{{ number_format($totalConvertedAllowances, 2) }}</td>
                                <td class="fw-bold">{{ number_format($totalConvertedDeductions, 2) }}</td>
                                <td class="fw-bold">{{ number_format($totalConvertedNet, 2) }}</td>
                            </tr>
                        @else
                            <!-- العرض التقليدي مصنف حسب العملة -->
                            @forelse($rowsByCurrency as $currency => $currencyRows)
                                <!-- عنوان العملة -->
                                <tr class="bg-light">
                                    <td colspan="7" class="fw-bold">{{ $currency }}</td>
                                </tr>
                                
                                <!-- صفوف كل عملة -->
                                @foreach($currencyRows as $row)
                                <tr>
                                    <td>{{ $row->employee->name ?? '-' }}</td>
                                    <td>{{ $row->salary_month }}</td>
                                    <td>{{ $row->employee->currency ?? 'Unknown' }}</td>
                                    <td>{{ number_format($row->gross_salary, 2) }}</td>
                                    <td>{{ number_format($row->total_allowances, 2) }}</td>
                                    <td>{{ number_format($row->total_deductions, 2) }}</td>
                                    <td>{{ number_format($row->net_salary, 2) }}</td>
                                </tr>
                                @endforeach
                                
                                <!-- المجموع الفرعي لكل عملة -->
                                <tr class="table-secondary">
                                    <td colspan="3" class="fw-bold">{{ __('messages.subtotal') }} ({{ $currency }})</td>
                                    <td class="fw-bold">{{ number_format($totals[$currency]['gross'], 2) }}</td>
                                    <td class="fw-bold">{{ number_format($totals[$currency]['allowances'], 2) }}</td>
                                    <td class="fw-bold">{{ number_format($totals[$currency]['deductions'], 2) }}</td>
                                    <td class="fw-bold">{{ number_format($totals[$currency]['net'], 2) }}</td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('messages.no_data') }}</td>
                            </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @if(!$displayCurrency)
        <!-- المجموع الكلي بكل العملات المتاحة - يظهر فقط في حالة عدم اختيار عملة عرض واحدة -->
    @endif
</div>
@endsection 