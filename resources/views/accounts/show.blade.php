@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>تفاصيل الحساب: {{ $account->name }}</h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('accounts.real') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
    </div>
</section>

@php
    $typeLabels = [
        'receipt' => 'سند قبض',
        'payment' => 'سند صرف',
        'transfer' => 'سند تحويل',
        'deposit' => 'إيداع نقدي',
        'withdraw' => 'سحب نقدي',
    ];
@endphp

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Account Info Card -->
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">معلومات الحساب</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-4">
                            <dt class="col-sm-3">كود الحساب</dt>
                            <dd class="col-sm-9">{{ $account->code }}</dd>

                            <dt class="col-sm-3">نوع الحساب</dt>
                            <dd class="col-sm-9">{{ ucfirst($account->type) }}</dd>

                            <dt class="col-sm-3">طبيعة الحساب</dt>
                            <dd class="col-sm-9">{{ $account->nature ?? 'غير محددة' }}</dd>

                            <dt class="col-sm-3">الرصيد الإجمالي ({{ $defaultCurrency->symbol }} {{ $defaultCurrency->code }})</dt>
                            <dd class="col-sm-9 font-weight-bold">{{ $totalInDefault >= 0 ? '+' : '-' }}{{ number_format(abs($totalInDefault), 2) }}</dd>
                        </dl>
                    </div>
                </div>
                <!-- Balances Card -->
                <div class="card card-secondary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">الأرصدة حسب العملة</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>العملة</th>
                                        <th>الرصيد</th>
                                        <th>ما يعادله ({{ $defaultCurrency->code }})</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($balances as $currencyCode => $item)
                                        <tr>
                                            <td>{{ $item['currency']->symbol }} {{ $item['currency']->code }}</td>
                                            <td>{{ number_format($item['balance'], 2) }}</td>
                                            <td>{{ number_format($item['balance'] * $item['exchange_rate'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Transactions Tabs Card -->
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">الحركات حسب العملة</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-tabs" id="txTab" role="tablist">
                            @foreach($linesByCurrency as $currencyCode => $lines)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $currencyCode }}-tab" data-toggle="tab" href="#tab-{{ $currencyCode }}" role="tab">
                                        {{ $currencyCode }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content p-3" id="txTabContent">
                            @foreach($linesByCurrency as $currencyCode => $lines)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $currencyCode }}" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover text-center mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width:50px;">#</th>
                                                    <th>التاريخ</th>
                                                    <th>رقم القيد</th>
                                                    <th>الوصف</th>
                                                    <th>مدين</th>
                                                    <th>دائن</th>
                                                    <th>الرصيد التراكمي</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $runningBalance = 0; @endphp
                                                @foreach($lines as $line)
                                                    @php
                                                        $runningBalance += $line->debit - $line->credit;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $line->journalEntry->date ?? '-' }}</td>
                                                        <td>{{ $line->journalEntry->id ?? '-' }}</td>
                                                        <td>{{ $line->description ?? '-' }}</td>
                                                        <td>{{ number_format($line->debit, 2) }}</td>
                                                        <td>{{ number_format($line->credit, 2) }}</td>
                                                        <td>{{ number_format($runningBalance, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 