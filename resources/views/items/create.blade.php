@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">@lang('messages.new_item')</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">@lang('messages.add_new_item')</h3>
        </div>
        <form action="{{ route('items.store') }}" method="POST">
          @csrf
          <div class="card-body">
            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <div class="form-group">
              <label>@lang('messages.item_name')</label>
              <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>
            <div class="form-group">
              <label>@lang('messages.item_type')</label>
              <select name="type" class="form-control" required>
                <option value="" disabled selected>-- @lang('messages.select_item_type') --</option>
                <option value="product" {{ old('type')=='product'?'selected':'' }}>@lang('messages.product')</option>
                <option value="service" {{ old('type')=='service'?'selected':'' }}>@lang('messages.service')</option>
              </select>
            </div>
            <div class="form-group">
              <label>@lang('messages.unit_price')</label>
              <input type="number" name="unit_price" value="{{ old('unit_price') }}" step="0.01" class="form-control" required>
            </div>
            <div class="form-group">
              <label>@lang('messages.item_description')</label>
              <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-success">@lang('messages.save')</button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection 