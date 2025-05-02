@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">قائمة الحسابات الفعلية</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center mb-0">
                        <thead>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>رمز الحساب</th>
                                <th>اسم الحساب</th>
                                <th>الفئة الرئيسية</th>
                                <th>طبيعة الحساب</th>
                                <th>صندوق نقدي</th>
                                <th style="width:120px;">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $i => $account)
                                <tr>
                                    <td>{{ $accounts->firstItem() + $i }}</td>
                                    <td>{{ $account->code }}</td>
                                    <td class="text-left">{{ $account->name }}</td>
                                    <td>{{ $account->parent->name ?? '-' }}</td>
                                    <td>
                                        @if($account->nature == 'debit')
                                            <span class="badge badge-info">مدين</span>
                                        @elseif($account->nature == 'credit')
                                            <span class="badge badge-warning">دائن</span>
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($account->is_cash_box)
                                            <span class="badge badge-success">نعم</span>
                                        @else
                                            <span class="badge badge-secondary">لا</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('accounts.show', $account) }}" class="btn btn-outline-info" title="تفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-primary" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4">لا توجد حسابات لعرضها.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                <div>إجمالي الحسابات: <strong>{{ $accounts->total() }}</strong></div>
                <div>{{ $accounts->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
