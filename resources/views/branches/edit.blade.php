@extends('layouts.app')

@section('content')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">تعديل بيانات الفرع</h1>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <div class="card card-warning">
        <div class="card-header">
          <h3 class="card-title">بيانات الفرع</h3>
        </div>

        <form action="{{ route('branches.update', $branch->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="card-body">
            <div class="form-group">
              <label>اسم الفرع</label>
              <input type="text" name="name" class="form-control" value="{{ $branch->name }}" required>
            </div>

            <div class="form-group">
              <label>العنوان</label>
              <input type="text" name="address" class="form-control" value="{{ $branch->address }}">
            </div>

            <div class="form-group">
              <label>الهاتف</label>
              <input type="text" name="phone" class="form-control" value="{{ $branch->phone }}">
            </div>
          </div>

          <div class="card-footer">
            <button type="submit" class="btn btn-warning">تحديث</button>
          </div>
        </form>

      </div>

    </div>
  </section>

</div>

@endsection
