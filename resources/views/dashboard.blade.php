@extends('layouts.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">لوحة التحكم</h1>
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
              <p>عدد الحسابات</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('accounts.index') }}" class="small-box-footer">عرض المزيد <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>{{ $usersCount }}</h3>
              <p>عدد المستخدمين</p>
            </div>
            <div class="icon">
              <i class="fas fa-user"></i>
            </div>
            <a href="#" class="small-box-footer">عرض المزيد <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>{{ $transactionsCount }}</h3>
              <p>الحركات المالية</p>
            </div>
            <div class="icon">
              <i class="fas fa-wallet"></i>
            </div>
            <a href="{{ route('transactions.index') }}" class="small-box-footer">عرض المزيد <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>{{ $vouchersCount }}</h3>
              <p>عدد السندات</p>
            </div>
            <div class="icon">
              <i class="fas fa-file-invoice"></i>
            </div>
            <a href="{{ route('vouchers.index') }}" class="small-box-footer">عرض المزيد <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-12">
          <h4 class="mt-4">تفاصيل السندات حسب النوع:</h4>
        </div>
        <div class="col-lg-2 col-4">
          <div class="small-box bg-light">
            <div class="inner">
              <h4>{{ $receiptCount }}</h4>
              <p>سندات القبض</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-4">
          <div class="small-box bg-secondary">
            <div class="inner">
              <h4>{{ $paymentCount }}</h4>
              <p>سندات الصرف</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-4">
          <div class="small-box bg-primary">
            <div class="inner">
              <h4>{{ $transferCount }}</h4>
              <p>سندات التحويل</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-4">
          <div class="small-box bg-info">
            <div class="inner">
              <h4>{{ $depositCount }}</h4>
              <p>الإيداعات</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-4">
          <div class="small-box bg-dark">
            <div class="inner">
              <h4>{{ $withdrawCount }}</h4>
              <p>السحوبات</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
