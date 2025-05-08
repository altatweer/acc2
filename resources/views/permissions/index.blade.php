@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">@lang('messages.permissions_management')</h1>
      </div>
      <div class="col-sm-6 text-left">
        <!-- No add button -->
      </div>
    </div>
    <div class="alert alert-info mt-3" role="alert">
      @lang('messages.permissions_system_notice')
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">@lang('messages.permissions_list')</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>@lang('messages.permission_name')</th>
              <th>@lang('messages.description')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($permissions as $perm)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $perm->name }}</td>
                <td>{{ $perm->description }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
@endsection 