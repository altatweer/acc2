@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>السندات المالية</h1>
            </div>
            <div class="col-sm-6 text-left">
                @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
                @if($isSuperAdmin || auth()->user()->can('إضافة سند'))
                <a href="{{ route('vouchers.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle"></i> إنشاء سند جديد
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">قائمة السندات</h3>
                <div class="card-tools">
                    @if($isSuperAdmin || auth()->user()->can('إضافة سند'))
                    <a href="{{ route('vouchers.create') }}" class="btn btn-sm btn-success">سند جديد</a>
                    @endif
                    @if(request('type') == 'transfer')
                    <a href="{{ route('vouchers.transfer.create') }}" class="btn btn-success mb-3">إضافة سند تحويل بين الصناديق</a>
                    @endif
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="{{ route('vouchers.index') }}" class="form-inline mb-3">
                    <div class="input-group input-group-sm mr-2">
                        <select name="type" class="form-control">
                            <option value="">-- النوع --</option>
                            <option value="receipt" {{ request('type')=='receipt'?'selected':'' }}>قبض</option>
                            <option value="payment" {{ request('type')=='payment'?'selected':'' }}>صرف</option>
                            <option value="transfer" {{ request('type')=='transfer'?'selected':'' }}>تحويل</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm mr-2">
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                    </div>
                    <div class="input-group input-group-sm mr-2">
                        <input type="text" name="recipient_name" value="{{ request('recipient_name') }}" class="form-control" placeholder="مستلم/دافع">
                    </div>
                    <button type="submit" class="btn btn-sm btn-info mr-2">بحث</button>
                    <a href="{{ route('vouchers.index') }}" class="btn btn-sm btn-secondary">مسح</a>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width:80px;">#</th>
                                <th>رقم السند</th>
                                <th>نوع السند</th>
                                <th>التاريخ</th>
                                <th>المحاسب</th>
                                <th>مستلم/دافع</th>
                                <th style="width:160px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $i => $voucher)
                                <tr>
                                    <td>{{ $vouchers->firstItem() + $i }}</td>
                                    <td>{{ $voucher->voucher_number }}</td>
                                    <td>
                                        @php
                                            $labels=['receipt'=>'سند قبض','payment'=>'سند صرف','transfer'=>'سند تحويل','deposit'=>'إيداع','withdraw'=>'سحب'];
                                        @endphp
                                        <span class="badge badge-info">{{ $labels[$voucher->type] ?? $voucher->type }}</span>
                                    </td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($voucher->date)->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $voucher->user->name ?? '-' }}</td>
                                    <td>{{ $voucher->recipient_name ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($isSuperAdmin || auth()->user()->can('عرض السندات'))
                                            <a href="{{ route('vouchers.show', $voucher) }}" class="btn btn-outline-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4">لا توجد سندات لعرضها.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                <div>إجمالي السندات: <strong>{{ $vouchers->total() }}</strong></div>
                <div>{{ $vouchers->links() }}</div>
            </div>
        </div>
    </div>
</section>
@endsection
