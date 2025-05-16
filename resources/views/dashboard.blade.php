@extends('layouts.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-3">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">
            <i class="fas fa-tachometer-alt mr-2 text-primary"></i>@lang('messages.dashboard_title')
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active"><i class="fas fa-home"></i> @lang('messages.dashboard_title')</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <!-- احصائيات عامة -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="info-box">
            <span class="info-box-icon bg-gradient-primary elevation-1">
              <i class="fas fa-book"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text">@lang('messages.accounts_count')</span>
              <span class="info-box-number">{{ $accountsCount }}</span>
              <div class="progress mt-1" style="height: 3px;">
                <div class="progress-bar bg-primary" style="width: 100%"></div>
              </div>
            </div>
            <a href="{{ route('accounts.index') }}" class="stretched-link"></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="info-box">
            <span class="info-box-icon bg-gradient-success elevation-1">
              <i class="fas fa-users"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text">@lang('messages.users_count')</span>
              <span class="info-box-number">{{ $usersCount }}</span>
              <div class="progress mt-1" style="height: 3px;">
                <div class="progress-bar bg-success" style="width: 100%"></div>
              </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="stretched-link"></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="info-box">
            <span class="info-box-icon bg-gradient-warning elevation-1">
              <i class="fas fa-exchange-alt"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text">@lang('messages.transactions_count')</span>
              <span class="info-box-number">{{ $transactionsCount }}</span>
              <div class="progress mt-1" style="height: 3px;">
                <div class="progress-bar bg-warning" style="width: 100%"></div>
              </div>
            </div>
            <a href="{{ route('transactions.index') }}" class="stretched-link"></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="info-box">
            <span class="info-box-icon bg-gradient-danger elevation-1">
              <i class="fas fa-file-invoice"></i>
            </span>
            <div class="info-box-content">
              <span class="info-box-text">@lang('messages.vouchers_count')</span>
              <span class="info-box-number">{{ $vouchersCount }}</span>
              <div class="progress mt-1" style="height: 3px;">
                <div class="progress-bar bg-danger" style="width: 100%"></div>
              </div>
            </div>
            <a href="{{ Route::localizedRoute('vouchers.index') }}" class="stretched-link"></a>
          </div>
        </div>
      </div>

      <!-- بطاقات السندات -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-chart-pie mr-1"></i>
                @lang('messages.vouchers_by_type')
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md col-6 mb-3">
                  <div class="small-box" style="box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 10px; background: #eef2ff;">
                    <div class="inner p-3 text-center">
                      <h3 style="font-size: 25px; color: #4f46e5;">{{ $receiptCount }}</h3>
                      <p style="color: #4338ca;">@lang('messages.receipt_vouchers_count')</p>
                      <i class="fas fa-receipt" style="font-size: 2rem; color: #6366f1; opacity: 0.2; position: absolute; bottom: 0; right: 10px;"></i>
                    </div>
                    <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'receipt']) }}" class="small-box-footer" style="padding: 8px; background: #4f46e5; color: white; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                      @lang('messages.view_more') <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                  </div>
                </div>
                
                <div class="col-md col-6 mb-3">
                  <div class="small-box" style="box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 10px; background: #dcfce7;">
                    <div class="inner p-3 text-center">
                      <h3 style="font-size: 25px; color: #16a34a;">{{ $paymentCount }}</h3>
                      <p style="color: #15803d;">@lang('messages.payment_vouchers_count')</p>
                      <i class="fas fa-money-bill" style="font-size: 2rem; color: #22c55e; opacity: 0.2; position: absolute; bottom: 0; right: 10px;"></i>
                    </div>
                    <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'payment']) }}" class="small-box-footer" style="padding: 8px; background: #16a34a; color: white; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                      @lang('messages.view_more') <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                  </div>
                </div>
                
                <div class="col-md col-6 mb-3">
                  <div class="small-box" style="box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 10px; background: #dbeafe;">
                    <div class="inner p-3 text-center">
                      <h3 style="font-size: 25px; color: #2563eb;">{{ $transferCount }}</h3>
                      <p style="color: #1d4ed8;">@lang('messages.transfer_vouchers_count')</p>
                      <i class="fas fa-exchange-alt" style="font-size: 2rem; color: #3b82f6; opacity: 0.2; position: absolute; bottom: 0; right: 10px;"></i>
                    </div>
                    <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'transfer']) }}" class="small-box-footer" style="padding: 8px; background: #2563eb; color: white; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                      @lang('messages.view_more') <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                  </div>
                </div>
                
                <div class="col-md col-6 mb-3">
                  <div class="small-box" style="box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 10px; background: #ffedd5;">
                    <div class="inner p-3 text-center">
                      <h3 style="font-size: 25px; color: #ea580c;">{{ $depositCount }}</h3>
                      <p style="color: #c2410c;">@lang('messages.deposits_count')</p>
                      <i class="fas fa-piggy-bank" style="font-size: 2rem; color: #f97316; opacity: 0.2; position: absolute; bottom: 0; right: 10px;"></i>
                    </div>
                    <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'deposit']) }}" class="small-box-footer" style="padding: 8px; background: #ea580c; color: white; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                      @lang('messages.view_more') <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                  </div>
                </div>
                
                <div class="col-md col-6 mb-3">
                  <div class="small-box" style="box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 10px; background: #fef2f2;">
                    <div class="inner p-3 text-center">
                      <h3 style="font-size: 25px; color: #dc2626;">{{ $withdrawCount }}</h3>
                      <p style="color: #b91c1c;">@lang('messages.withdrawals_count')</p>
                      <i class="fas fa-money-bill-wave" style="font-size: 2rem; color: #ef4444; opacity: 0.2; position: absolute; bottom: 0; right: 10px;"></i>
                    </div>
                    <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'withdraw']) }}" class="small-box-footer" style="padding: 8px; background: #dc2626; color: white; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                      @lang('messages.view_more') <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- صناديق الكاش -->
      @if($userCashBoxes && $userCashBoxes->count())
      <div class="row">
        <div class="col-12">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-cash-register mr-1"></i>
                @lang('messages.your_cash_boxes')
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                @foreach($userCashBoxes as $box)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                  <div class="card h-100" style="border-radius: 12px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
                    <div class="card-header text-white" style="background: linear-gradient(45deg, #2563eb, #1e40af); border-bottom: none;">
                      <h5 class="mb-0">{{ $box['name'] }}</h5>
                    </div>
                    <div class="card-body p-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">@lang('messages.balance'):</span>
                        <span class="font-weight-bold" style="font-size: 1.25rem;">{{ number_format($box['balance'], 2) }} <small>{{ $box['currency'] }}</small></span>
                      </div>
                      @if($box['balance'] > 0)
                      <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                      </div>
                      @else
                      <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-danger" style="width: 100%"></div>
                      </div>
                      @endif
                    </div>
                    <div class="card-footer bg-light p-0" style="border-top: none;">
                      <div class="btn-group w-100">
                        <a href="{{ Route::localizedRoute('vouchers.create') }}?account_id={{ $box['id'] }}&type=receipt" class="btn btn-sm btn-light py-2" style="border-radius: 0;">
                          <i class="fas fa-plus-circle text-success"></i> @lang('messages.receipt')
                        </a>
                        <a href="{{ Route::localizedRoute('vouchers.create') }}?account_id={{ $box['id'] }}&type=payment" class="btn btn-sm btn-light py-2" style="border-radius: 0;">
                          <i class="fas fa-minus-circle text-danger"></i> @lang('messages.payment')
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // Add some animations to dashboard elements
  $(document).ready(function() {
    // Animate info boxes
    $('.info-box').each(function(index) {
      $(this).css('opacity', '0');
      $(this).css('transform', 'translateY(20px)');
      
      setTimeout(() => {
        $(this).css('transition', 'all 0.5s ease');
        $(this).css('opacity', '1');
        $(this).css('transform', 'translateY(0)');
      }, 100 * index);
    });
    
    // Animate voucher cards
    $('.small-box').each(function(index) {
      $(this).css('opacity', '0');
      $(this).css('transform', 'translateY(20px)');
      
      setTimeout(() => {
        $(this).css('transition', 'all 0.5s ease');
        $(this).css('opacity', '1');
        $(this).css('transform', 'translateY(0)');
      }, 300 + (100 * index));
    });
  });
</script>
@endpush
