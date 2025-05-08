@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">@lang('messages.view_item')</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card mt-3">
        <div class="card-body">
          <table class="table table-bordered">
            <tr><th>#</th><td>{{ $item->id }}</td></tr>
            <tr><th>@lang('messages.item_name')</th><td>{{ $item->name }}</td></tr>
            <tr><th>@lang('messages.item_type')</th><td>{{ $item->type == 'product' ? __('messages.product') : __('messages.service') }}</td></tr>
            <tr><th>@lang('messages.unit_price')</th><td>{{ number_format($item->unit_price,2) }}</td></tr>
            <tr><th>@lang('messages.item_description')</th><td>{{ $item->description ?? '-' }}</td></tr>
          </table>
        </div>
        <div class="card-footer text-right">
          <a href="{{ Route::localizedRoute('items.edit', ['item' => $item, ]) }}" class="btn btn-sm btn-warning">@lang('messages.edit')</a>
          <a href="{{ route('items.index') }}" class="btn btn-sm btn-secondary">@lang('messages.back')</a>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection 