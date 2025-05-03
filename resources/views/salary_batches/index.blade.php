@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">كشوف الرواتب الشهرية</h1>
            <a href="{{ route('salary-batches.create') }}" class="btn btn-primary">توليد كشف جديد</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الشهر</th>
                                <th>الحالة</th>
                                <th>عدد الموظفين</th>
                                <th>تاريخ الإنشاء</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batches as $batch)
                                <tr>
                                    <td>{{ $batch->id }}</td>
                                    <td>{{ $batch->month }}</td>
                                    <td>
                                        @if($batch->status=='pending')<span class="badge badge-warning">معلق</span>@endif
                                        @if($batch->status=='approved')<span class="badge badge-success">معتمد</span>@endif
                                        @if($batch->status=='closed')<span class="badge badge-secondary">مغلق</span>@endif
                                    </td>
                                    <td>{{ $batch->salaryPayments()->count() }}</td>
                                    <td>{{ $batch->created_at }}</td>
                                    <td>
                                        <a href="{{ route('salary-batches.show', $batch) }}" class="btn btn-sm btn-info">عرض</a>
                                        @if($batch->status=='pending')
                                        <form action="{{ route('salary-batches.approve', $batch) }}" method="POST" style="display:inline-block" onsubmit="return confirm('هل تريد اعتماد هذا الكشف؟');">
                                            @csrf
                                            <button class="btn btn-sm btn-success">اعتماد</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($batches->count() == 0)
                                <tr><td colspan="6" class="text-center">لا توجد كشوفات بعد.</td></tr>
                            @endif
                        </tbody>
                    </table>
                    {{ $batches->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 