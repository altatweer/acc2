@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">تفاصيل القيد المحاسبي #{{ $journalEntry->id }}</h1>
    <div class="card mb-3">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-2">التاريخ</dt>
                <dd class="col-sm-4">{{ $journalEntry->date }}</dd>
                <dt class="col-sm-2">الوصف</dt>
                <dd class="col-sm-4">{{ $journalEntry->description }}</dd>
                <dt class="col-sm-2">المستخدم</dt>
                <dd class="col-sm-4">{{ $journalEntry->user->name ?? '-' }}</dd>
                <dt class="col-sm-2">العملة</dt>
                <dd class="col-sm-4">{{ $journalEntry->currency }}</dd>
                <dt class="col-sm-2">مدين</dt>
                <dd class="col-sm-4">{{ number_format($journalEntry->total_debit,2) }}</dd>
                <dt class="col-sm-2">دائن</dt>
                <dd class="col-sm-4">{{ number_format($journalEntry->total_credit,2) }}</dd>
                <dt class="col-sm-2">الحالة</dt>
                <dd class="col-sm-4">
                    @if($journalEntry->status == 'active')
                        <span class="badge badge-success">نشط</span>
                    @else
                        <span class="badge badge-danger">ملغي</span>
                    @endif
                </dd>
            </dl>
            @if($journalEntry->status == 'canceled')
                <div class="alert alert-danger font-weight-bold text-center">
                    هذا القيد ملغي (تم توليد قيد عكسي تلقائيًا لإبطاله).<br>
                    لا يمكن تعديله أو حذفه بعد ذلك.
                </div>
            @endif
            @if((!$journalEntry->source_type || $journalEntry->source_type == 'manual') && $journalEntry->status == 'active')
                <form action="{{ route('journal-entries.cancel', $journalEntry) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من إلغاء القيد؟ سيتم توليد قيد عكسي ولن يمكن التراجع.')">إلغاء القيد</button>
                </form>
            @endif
            @if($journalEntry->source_type && $journalEntry->source_id)
                <hr>
                <strong>المصدر:</strong>
                <span class="badge badge-info">{{ $journalEntry->source_type }}</span>
                <span>#{{ $journalEntry->source_id }}</span>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header"><strong>تفاصيل السطور</strong></div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الحساب</th>
                        <th>الوصف</th>
                        <th>مدين</th>
                        <th>دائن</th>
                        <th>العملة</th>
                        <th>سعر الصرف</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntry->lines as $i=>$line)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $line->account->name ?? '-' }}</td>
                        <td>{{ $line->description }}</td>
                        <td>{{ number_format($line->debit,2) }}</td>
                        <td>{{ number_format($line->credit,2) }}</td>
                        <td>{{ $line->currency }}</td>
                        <td>{{ $line->exchange_rate }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">العودة للقائمة</a>
    </div>
</div>
@endsection 