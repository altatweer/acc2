@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">إدارة العملات</h1>
        </div>
        <div class="col-sm-6 text-left">
          @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
          @if($isSuperAdmin || auth()->user()->can('إضافة عملة'))
          <a href="{{ route('currencies.create') }}" class="btn btn-primary">إضافة عملة جديدة</a>
          @endif
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">قائمة العملات</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>الرمز</th>
                <th>الرمز النصي</th>
                <th>سعر الصرف</th>
                <th>الافتراضية</th>
                <th>الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              @foreach($currencies as $currency)
                <tr>
                  <td>{{ $currency->id }}</td>
                  <td>{{ $currency->name }}</td>
                  <td>{{ $currency->code }}</td>
                  <td>{{ $currency->symbol }}</td>
                  <td>{{ $currency->exchange_rate }}</td>
                  <td>
                    @if($currency->is_default)
                      <span class="badge badge-success">نعم</span>
                    @else
                      <span class="badge badge-secondary">لا</span>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm" role="group">
                      @if($isSuperAdmin || auth()->user()->can('عرض العملات'))
                      <a href="{{ route('currencies.show', $currency) }}" class="btn btn-outline-info" title="عرض"><i class="fas fa-eye"></i></a>
                      @endif
                      @if($isSuperAdmin || auth()->user()->can('تعديل عملة'))
                      <a href="{{ route('currencies.edit', $currency) }}" class="btn btn-outline-primary" title="تعديل"><i class="fas fa-edit"></i></a>
                      @endif
                      @if($isSuperAdmin || auth()->user()->can('حذف عملة'))
                      <form action="{{ route('currencies.destroy', $currency) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger" title="حذف"><i class="fas fa-trash"></i></button></form>
                      @endif
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection 