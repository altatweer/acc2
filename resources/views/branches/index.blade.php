@extends('layouts.app')

@section('content')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">إدارة الفروع</h1>
        </div>
        <div class="col-sm-6 text-left">
          <a href="{{ route('branches.create') }}" class="btn btn-primary">إضافة فرع جديد</a>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">قائمة الفروع</h3>
        </div>

        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>اسم الفرع</th>
                <th>العنوان</th>
                <th>الهاتف</th>
                <th>الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              @foreach($branches as $branch)
              <tr>
                <td>{{ $branch->id }}</td>
                <td>{{ $branch->name }}</td>
                <td>{{ $branch->address }}</td>
                <td>{{ $branch->phone }}</td>
                <td>
                  <a href="{{ route('branches.edit'branch$branch->id) }}" class="btn btn-sm btn-info">تعديل</a>

                  <form action="{{ route('branches.destroy'branch$branch->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                  </form>

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
