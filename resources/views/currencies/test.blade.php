@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">@lang('messages.currency_functions_test')</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">@lang('messages.currency_conversion_formatting_functions')</h3>
        </div>
        <div class="card-body">
          @php
          // الحصول على جميع العملات
          $currencies = \App\Models\Currency::all();
          $defaultCurrency = default_currency();
          $usdCurrency = \App\Models\Currency::where('code', 'USD')->first();
          $amount = 1000;
          @endphp

          <h4>@lang('messages.basic_currency_info')</h4>
          <table class="table table-bordered">
            <tr>
              <th>@lang('messages.default_currency')</th>
              <td>{{ $defaultCurrency->name }} ({{ $defaultCurrency->code }})</td>
            </tr>
            <tr>
              <th>@lang('messages.default_currency_code')</th>
              <td>{{ default_currency_code() }}</td>
            </tr>
          </table>

          <h4 class="mt-4">@lang('messages.currency_conversion_test')</h4>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>@lang('messages.source_currency')</th>
                <th>@lang('messages.amount')</th>
                <th>@lang('messages.target_currency')</th>
                <th>@lang('messages.converted_amount')</th>
              </tr>
            </thead>
            <tbody>
              @foreach($currencies as $fromCurrency)
                @foreach($currencies as $toCurrency)
                  @if($fromCurrency->id != $toCurrency->id)
                  <tr>
                    <td>{{ $fromCurrency->name }} ({{ $fromCurrency->code }})</td>
                    <td>{{ $amount }}</td>
                    <td>{{ $toCurrency->name }} ({{ $toCurrency->code }})</td>
                    <td>
                      {{ currency_convert($amount, $fromCurrency->code, $toCurrency->code) }}
                    </td>
                  </tr>
                  @endif
                @endforeach
              @endforeach
            </tbody>
          </table>

          <h4 class="mt-4">@lang('messages.currency_formatting_test')</h4>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>@lang('messages.currency')</th>
                <th>@lang('messages.amount')</th>
                <th>@lang('messages.formatted_amount')</th>
              </tr>
            </thead>
            <tbody>
              @foreach($currencies as $currency)
              <tr>
                <td>{{ $currency->name }} ({{ $currency->code }})</td>
                <td>{{ $amount }}</td>
                <td>{{ currency_format($amount, $currency->code) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>

          <h4 class="mt-4">@lang('messages.conversion_formatting_together_test')</h4>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>@lang('messages.source_currency')</th>
                <th>@lang('messages.amount')</th>
                <th>@lang('messages.converted_formatted_amount')</th>
              </tr>
            </thead>
            <tbody>
              @foreach($currencies as $currency)
              <tr>
                <td>{{ $currency->name }} ({{ $currency->code }})</td>
                <td>{{ $amount }}</td>
                <td>{{ money($amount, $currency->code) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>

          <h4 class="mt-4">@lang('messages.model_methods_test')</h4>
          <table class="table table-bordered">
            <tr>
              <th>@lang('messages.amount_in_iqd')</th>
              <td>{{ $amount }} IQD</td>
            </tr>
            <tr>
              <th>@lang('messages.iqd_to_usd_direct_conversion')</th>
              <td>
                @if($usdCurrency)
                {{ \App\Models\Currency::convert($amount, 'IQD', 'USD') }} USD
                @else
                @lang('messages.usd_currency_not_found')
                @endif
              </td>
            </tr>
            <tr>
              <th>@lang('messages.format_amount_in_iqd')</th>
              <td>{{ \App\Models\Currency::formatByCurrency($amount, 'IQD') }}</td>
            </tr>
          </table>
        </div>
        <div class="card-footer">
          <div class="row">
            <div class="col-md-6">
              <a href="{{ route('currencies.index') }}" class="btn btn-primary">@lang('messages.back_to_currencies')</a>
            </div>
            <div class="col-md-6 text-right">
              @if(app()->getLocale() == 'ar')
                <a href="{{ url('/language/en') }}" class="btn btn-outline-secondary font-weight-bold">Switch to English</a>
              @else
                <a href="{{ url('/language/ar') }}" class="btn btn-outline-secondary font-weight-bold">التحويل للعربية</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection 