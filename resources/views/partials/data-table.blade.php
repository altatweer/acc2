<div class="table-responsive">
    <table class="table table-bordered table-striped" id="mainDataTable">
        <thead>
            <tr>
                @foreach($columns as $col)
                    <th>{{ $col }}</th>
                @endforeach
                @isset($actions)
                    <th>إجراءات</th>
                @endisset
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($fields as $field)
                        <td>{{ data_get($row, $field) }}</td>
                    @endforeach
                    @isset($actions)
                        <td>{!! $actions($row) !!}</td>
                    @endisset
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-2">
    <button onclick="window.print()" class="btn btn-outline-secondary no-print"><i class="fa fa-print"></i> طباعة</button>
    <!-- يمكن إضافة أزرار تصدير لاحقًا -->
</div>
@push('scripts')
<script>
// بحث فوري داخل الجدول
$(document).ready(function(){
    $('#mainDataTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json'
        }
    });
});
</script>
@endpush 