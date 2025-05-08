@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">@lang('messages.roles_permissions_management')</h1>
      </div>
      <div class="col-sm-6 text-left">
        @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
        @if($isSuperAdmin || auth()->user()->can('create roles'))
        <a href="{{ route('admin.roles.create') }}" class="btn btn-success">@lang('messages.add_new_role')</a>
        @endif
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">@lang('messages.roles_list')</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>@lang('messages.role_name')</th>
              <th>@lang('messages.permissions')</th>
              <th>@lang('messages.actions')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($roles as $role)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $role->name }}</td>
                <td>
                  @foreach($role->permissions as $perm)
                    <span class="badge badge-info">{{ $perm->name }}</span>
                  @endforeach
                </td>
                <td>
                  @if($isSuperAdmin || auth()->user()->can('edit roles'))
                  <a href="{{ Route::localizedRoute('admin.roles.edit', ['role' => $role->id, ]) }}" class="btn btn-sm btn-primary">@lang('messages.edit')</a>
                  @endif
                  @if($isSuperAdmin || auth()->user()->can('delete roles'))
                  <form action="{{ Route::localizedRoute('admin.roles.destroy', ['role' => $role->id, ]) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('@lang('messages.delete_role_confirm')')">@lang('messages.delete')</button>
                  </form>
                  @endif
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