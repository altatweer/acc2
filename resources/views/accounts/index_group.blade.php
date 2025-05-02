@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>إدارة الفئات الرئيسية</h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('accounts.createGroup') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle"></i> إضافة فئة جديدة
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">قائمة الفئات الرئيسية</h3>
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
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width:60px;">#</th>
                                        <th>رمز الفئة</th>
                                        <th>اسم الفئة</th>
                                        <th>نوع الحساب</th>
                                        <th>الفئة الرئيسية</th>
                                        <th style="width:140px;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $index => $category)
                                        <tr>
                                            <td>{{ $categories->firstItem() + $index }}</td>
                                            <td>{{ $category->code }}</td>
                                            <td class="text-left">{{ $category->name }}</td>
                                            <td>
                                                @php
                                                    $types = ['asset'=>'أصول','liability'=>'خصوم','revenue'=>'إيرادات','expense'=>'مصروفات','equity'=>'حقوق ملكية'];
                                                @endphp
                                                <span class="badge badge-info">{{ $types[$category->type] ?? '-' }}</span>
                                            </td>
                                            <td>{{ $category->parent->name ?? '-' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('accounts.edit', $category) }}" class="btn btn-outline-primary" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('accounts.destroy', $category) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');" style="display:inline-block;">
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
                                            <td colspan="6" class="py-4">لا توجد فئات لعرضها.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                        <div>إجمالي الفئات: <strong>{{ $categories->total() }}</strong></div>
                        <div>{{ $categories->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
