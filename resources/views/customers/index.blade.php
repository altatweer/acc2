@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">قائمة العملاء</h3>
                <div class="card-tools">
                    <a href="{{ route('customers.create') }}" class="btn btn-sm btn-success">عميل جديد</a>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="card-body p-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center mb-0">
                        <thead>
                            <tr>
                                <th>#</th><th>الاسم</th><th>البريد الإلكتروني</th><th>الهاتف</th><th>حساب الذمم</th><th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $i => $cust)
                            <tr>
                                <td>{{ $customers->firstItem() + $i }}</td>
                                <td class="text-left">{{ $cust->name }}</td>
                                <td>{{ $cust->email }}</td>
                                <td>{{ $cust->phone ?? '-' }}</td>
                                <td>{{ $cust->account->name }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('customers.show', $cust) }}" class="btn btn-outline-info" title="عرض"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('customers.edit', $cust) }}" class="btn btn-outline-primary" title="تعديل"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('customers.destroy', $cust) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger" title="حذف"><i class="fas fa-trash"></i></button></form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="py-4">لا توجد عملاء لعرضها.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                <div>إجمالي العملاء: <strong>{{ $customers->total() }}</strong></div>
                <div>{{ $customers->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection 