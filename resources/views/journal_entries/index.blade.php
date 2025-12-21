@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/journal_entries.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="journal-entries-container">
    <!-- Header -->
    <div class="journal-entries-header">
        <h1><i class="fas fa-book"></i> @lang('messages.journal_entries')</h1>
        <p class="mb-0 mt-2" style="opacity: 0.9;">إدارة وعرض جميع القيود المحاسبية</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="fas fa-list"></i></div>
            <div class="stat-value">{{ number_format($stats['total_entries']) }}</div>
            <div class="stat-label">إجمالي القيود</div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-value amount-debit">{{ number_format($stats['total_debit'], 2) }}</div>
            <div class="stat-label">إجمالي المدين</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-value amount-credit">{{ number_format($stats['total_credit'], 2) }}</div>
            <div class="stat-label">إجمالي الدائن</div>
        </div>
        <div class="stat-card info">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value">{{ number_format($stats['active_entries']) }}</div>
            <div class="stat-label">قيود نشطة</div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="filters-card">
        <form method="GET" id="filterForm">
            <div class="filter-row">
                <div class="form-group">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">الحساب</label>
                    <select name="account_id" class="form-control select2-account" style="width: 100%;">
                        <option value="">جميع الحسابات</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->code }} - {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">العملة</label>
                    <select name="currency" class="form-control">
                        <option value="">جميع العملات</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->code }}" {{ request('currency') == $currency->code ? 'selected' : '' }}>
                                {{ $currency->code }} - {{ $currency->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="filter-row">
                <div class="form-group">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-control">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">نوع القيد</label>
                    <select name="entry_type" class="form-control">
                        <option value="">جميع الأنواع</option>
                        <option value="manual" {{ request('entry_type') == 'manual' ? 'selected' : '' }}>يدوي</option>
                        <option value="automatic" {{ request('entry_type') == 'automatic' ? 'selected' : '' }}>تلقائي</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">المستخدم</label>
                    <select name="user_id" class="form-control">
                        <option value="">جميع المستخدمين</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-filter w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="{{ route('journal-entries.index') }}" class="btn btn-reset">
                        <i class="fas fa-redo"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Actions Bar -->
    <div class="actions-bar">
        <div>
            <h5 class="mb-0" style="color: #4a5568;">عرض {{ $entries->total() }} قيد</h5>
        </div>
        <div class="btn-group">
            @if(auth()->user()->can('add_journal_entry'))
            <a href="{{ route('journal-entries.single-currency.create') }}" class="btn btn-primary">
                <i class="fas fa-coins"></i> قيد بعملة واحدة
            </a>
            <a href="{{ route('journal-entries.multi-currency.create') }}" class="btn btn-info">
                <i class="fas fa-globe"></i> قيد متعدد العملات
            </a>
            @endif
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        @if($entries->count() > 0)
        <table class="table table-hover mb-0" id="journalEntriesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>التاريخ</th>
                    <th>الوصف</th>
                    <th>المستخدم</th>
                    <th>العملة</th>
                    <th class="text-end">مدين</th>
                    <th class="text-end">دائن</th>
                    <th>الحالة</th>
                    <th>النوع</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                <tr>
                    <td><strong>#{{ $entry->id }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($entry->date)->format('Y-m-d') }}</td>
                    <td>
                        <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $entry->description }}">
                            {{ $entry->description }}
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $entry->user->name ?? '-' }}</span>
                    </td>
                    <td>
                        @if($entry->currency == 'MIX' || $entry->is_multi_currency)
                            <span class="badge badge-warning">متعدد العملات</span>
                        @else
                            <span class="currency-badge">{{ $entry->currency }}</span>
                        @endif
                    </td>
                    <td class="text-end amount-cell amount-debit">
                        @if($entry->currency == 'MIX' || $entry->is_multi_currency)
                            <span class="text-info">-</span>
                        @else
                            {{ number_format($entry->total_debit, 2) }}
                        @endif
                    </td>
                    <td class="text-end amount-cell amount-credit">
                        @if($entry->currency == 'MIX' || $entry->is_multi_currency)
                            <span class="text-info">-</span>
                        @else
                            {{ number_format($entry->total_credit, 2) }}
                        @endif
                    </td>
                    <td>
                        @if($entry->status == 'active')
                            <span class="badge badge-success">نشط</span>
                        @else
                            <span class="badge badge-danger">ملغي</span>
                        @endif
                    </td>
                    <td>
                        @if($entry->source_type == null || $entry->source_type == 'manual')
                            <span class="badge badge-info">يدوي</span>
                        @else
                            <span class="badge badge-warning">تلقائي</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ Route::localizedRoute('journal-entries.show', ['journal_entry' => $entry]) }}" 
                               class="btn btn-info" title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($entry->status == 'active' && ((!$entry->source_type || $entry->source_type == 'manual') && !($entry->source_type == 'manual' && $entry->source_id && Str::contains($entry->description, 'قيد عكسي'))))
                                <form action="{{ Route::localizedRoute('journal-entries.cancel', ['journalEntry' => $entry->id]) }}" 
                                      method="POST" style="display:inline-block;" 
                                      onsubmit="return confirm('هل أنت متأكد من إلغاء هذا القيد؟');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" title="إلغاء القيد">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-inbox"></i></div>
            <div class="empty-title">لا توجد قيود</div>
            <div class="empty-description">لم يتم العثور على أي قيود محاسبية تطابق معايير البحث</div>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($entries->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $entries->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    @if($entries->count() > 0)
    $('#journalEntriesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/{{ app()->getLocale() == "ar" ? "ar" : "en-GB" }}.json'
        },
        order: [[1, 'desc']], // ترتيب حسب التاريخ من الأحدث للأقدم
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "الكل"]],
        searching: true,
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        columnDefs: [
            { orderable: false, targets: [9] } // تعطيل الترتيب على عمود الإجراءات
        ]
    });
    @endif
    
    $('.select2-account').select2({
        width: '100%',
        dir: '{{ app()->getLocale() == "ar" ? "rtl" : "ltr" }}',
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            }
        },
        placeholder: 'اختر الحساب',
        allowClear: true
    });
});
</script>
@endpush
