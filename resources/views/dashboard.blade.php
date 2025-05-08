@extends('layouts.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">@lang('messages.dashboard_title')</h1>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row justify-content-center">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3>{{ $accountsCount }}</h3>
              <p>@lang('messages.accounts_count')</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('accounts.index') }}" class="small-box-footer">@lang('messages.view_more') <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>{{ $usersCount }}</h3>
              <p>@lang('messages.users_count')</p>
            </div>
            <div class="icon">
              <i class="fas fa-user"></i>
            </div>
            <a href="#" class="small-box-footer">@lang('messages.view_more') <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>{{ $transactionsCount }}</h3>
              <p>@lang('messages.transactions_count')</p>
            </div>
            <div class="icon">
              <i class="fas fa-wallet"></i>
            </div>
            <a href="{{ route('transactions.index') }}" class="small-box-footer">@lang('messages.view_more') <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>{{ $vouchersCount }}</h3>
              <p>@lang('messages.vouchers_count')</p>
            </div>
            <div class="icon">
              <i class="fas fa-file-invoice"></i>
            </div>
            <a href="{{ Route::localizedRoute('vouchers.index') }}" class="small-box-footer">@lang('messages.view_more') <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-12">
          <h4 class="mt-4">@lang('messages.vouchers_by_type')</h4>
        </div>
        <div class="col-lg-2 col-4">
          <div class="small-box bg-light">
            <div class="inner">
              <h4>{{ $receiptCount }}</h4>
              <p>@lang('messages.receipt_vouchers_count')</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-4">
          <div class="small-box bg-secondary">
            <div class="inner">
              <h4>{{ $paymentCount }}</h4>
              <p>@lang('messages.payment_vouchers_count')</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-4">
          <div class="small-box bg-primary">
            <div class="inner">
              <h4>{{ $transferCount }}</h4>
              <p>@lang('messages.transfer_vouchers_count')</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-4">
          <div class="small-box bg-info">
            <div class="inner">
              <h4>{{ $depositCount }}</h4>
              <p>@lang('messages.deposits_count')</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-4">
          <div class="small-box bg-dark">
            <div class="inner">
              <h4>{{ $withdrawCount }}</h4>
              <p>@lang('messages.withdrawals_count')</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
