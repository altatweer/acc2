@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">القيود المحاسبية</h1>
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="من تاريخ">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="إلى تاريخ">
            </div>
            <div class="col-md-3">
                <select name="account_id" class="form-control">
                    <option value="">كل الحسابات</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="user_id" class="form-control" value="{{ request('user_id') }}" placeholder="معرّف المستخدم">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-block">بحث</button>
            </div>
        </div>
    </form>
    <div class="mb-3 text-right">
        <a href="{{ route('journal-entries.create') }}" class="btn btn-success">إضافة قيد يدوي</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>الوصف</th>
                        <th>المستخدم</th>
                        <th>العملة</th>
                        <th>مدين</th>
                        <th>دائن</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                    <tr>
                        <td>{{ $entry->id }}</td>
                        <td>{{ $entry->date }}</td>
                        <td>{{ $entry->description }}</td>
                        <td>{{ $entry->user->name ?? '-' }}</td>
                        <td>{{ $entry->currency }}</td>
                        <td>{{ number_format($entry->total_debit,2) }}</td>
                        <td>{{ number_format($entry->total_credit,2) }}</td>
                        <td>
                            @if($entry->status == 'active')
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-danger">ملغي</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('journal-entries.show', $entry) }}" class="btn btn-sm btn-info">عرض</a>
                            @if((!$entry->source_type || $entry->source_type == 'manual') && $entry->status == 'active')
                                <form action="{{ route('journal-entries.cancel', $entry) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من إلغاء القيد؟ سيتم توليد قيد عكسي ولن يمكن التراجع.')">إلغاء</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $entries->links() }}
    </div>
</div>
@endsection 