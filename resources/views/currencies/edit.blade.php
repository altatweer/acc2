@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">@lang('messages.edit_currency')</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-warning">
        <div class="card-header">
          <h3 class="card-title">@lang('messages.currency_data')</h3>
        </div>

        <form action="{{ route('currencies.update', $currency) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="card-body">
            <div class="form-group">
              <label>@lang('messages.currency_name')</label>
              <input type="text" name="name" value="{{ old('name', $currency->name) }}" class="form-control" required>
            </div>

            <div class="form-group">
              <label>@lang('messages.currency_code')</label>
              <input type="text" name="code" value="{{ old('code', $currency->code) }}" class="form-control" required>
            </div>

            <div class="form-group">
              <label>@lang('messages.currency_symbol')</label>
              <input type="text" name="symbol" value="{{ old('symbol', $currency->symbol) }}" class="form-control">
            </div>

            <div class="form-group">
              <label>@lang('messages.exchange_rate')</label>
              <input type="number" step="0.000001" name="exchange_rate" value="{{ old('exchange_rate', $currency->exchange_rate) }}" class="form-control" required>
            </div>

            <div class="form-check">
              <input type="hidden" name="is_default" value="0">
              <input type="checkbox" name="is_default" value="1" class="form-check-input" id="defaultCheck" {{ old('is_default', $currency->is_default) ? 'checked' : '' }}>
              <label class="form-check-label" for="defaultCheck">@lang('messages.is_default_currency')</label>
            </div>
            
            @if($errors->any())
              <div class="alert alert-danger mt-2">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>

          <div class="card-footer">
            <button type="submit" class="btn btn-warning">@lang('messages.update')</button>
            <a href="{{ route('currencies.index') }}" class="btn btn-default">@lang('messages.cancel')</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection 