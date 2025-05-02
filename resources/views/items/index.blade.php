@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">قائمة العناصر</h3>
                <div class="card-tools">
                    <a href="{{ route('items.create') }}" class="btn btn-sm btn-success">عنصر جديد</a>
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
                                <th>#</th>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>سعر الوحدة</th>
                                <th>الوصف</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>{{ $items->firstItem() + $loop->index }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->type == 'product' ? 'منتج' : 'خدمة' }}</td>
                                <td>{{ number_format($item->unit_price,2) }}</td>
                                <td>{{ $item->description }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('items.show', $item) }}" class="btn btn-outline-info" title="عرض"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn-outline-primary" title="تعديل"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="حذف"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                <div>إجمالي العناصر: <strong>{{ $items->total() }}</strong></div>
                <div>{{ $items->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection 