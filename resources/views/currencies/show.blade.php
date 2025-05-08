@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">@lang('messages.view_currency')</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card mt-3">
        <div class="card-body">
          <table class="table table-bordered">
            <tr>
              <th>#</th>
              <td>{{ $currency->id }}</td>
            </tr>
            <tr>
              <th>@lang('messages.currency_name')</th>
              <td>{{ $currency->name }}</td>
            </tr>
            <tr>
              <th>@lang('messages.currency_code')</th>
              <td>{{ $currency->code }}</td>
            </tr>
            <tr>
              <th>@lang('messages.currency_symbol')</th>
              <td>{{ $currency->symbol }}</td>
            </tr>
            <tr>
              <th>@lang('messages.exchange_rate')</th>
              <td>{{ $currency->exchange_rate }}</td>
            </tr>
            <tr>
              <th>@lang('messages.is_default_currency')</th>
              <td>
                @if($currency->is_default)
                  @lang('messages.default_yes')
                @else
                  @lang('messages.default_no')
                @endif
              </td>
            </tr>
          </table>
        </div>
        <div class="card-footer text-right">
          <a href="{{ route('currencies.edit', $currency) }}" class="btn btn-warning">@lang('messages.edit')</a>
          <a href="{{ route('currencies.index') }}" class="btn btn-secondary">@lang('messages.back')</a>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection 