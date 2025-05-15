@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">{{ __('messages.users_management') }}</h1>
      </div>
      <div class="col-sm-6 text-left">
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">{{ __('messages.add_new_user') }}</a>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">{{ __('messages.users_list') }}</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ __('messages.name') }}</th>
              <th>{{ __('messages.email') }}</th>
              <th>{{ __('messages.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">{{ __('messages.edit') }}</a>
                  <a href="{{ route('admin.users.cash_boxes.edit', $user->id) }}" class="btn btn-sm btn-info">{{ __('messages.manage_cash_boxes') }}</a>
                  <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.delete_user_confirm') }}')">{{ __('messages.delete') }}</button>
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
@endsection 