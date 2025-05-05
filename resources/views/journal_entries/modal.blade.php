<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-12">
            <h5>تفاصيل القيد المحاسبي رقم: <span class="text-primary">#{{ $entry->id }}</span></h5>
            <p class="mb-1"><strong>التاريخ:</strong> {{ $entry->date }}<br>
            <strong>الوصف:</strong> {{ $entry->description ?? '-' }}<br>
            <strong>المستخدم:</strong> {{ $entry->user->name ?? '-' }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-sm text-center mb-0">
                    <thead class="thead-light">
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
                        @foreach($entry->lines as $line)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $line->account->name ?? '-' }}</td>
                            <td>{{ $line->description ?? '-' }}</td>
                            <td>{{ number_format($line->debit, 2) }}</td>
                            <td>{{ number_format($line->credit, 2) }}</td>
                            <td>{{ $line->currency }}</td>
                            <td>{{ number_format($line->exchange_rate, 4) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-6 text-success"><strong>إجمالي المدين:</strong> {{ number_format($entry->total_debit, 2) }}</div>
        <div class="col-6 text-danger"><strong>إجمالي الدائن:</strong> {{ number_format($entry->total_credit, 2) }}</div>
    </div>
</div> 