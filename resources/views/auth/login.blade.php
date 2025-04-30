@extends('layouts.app')

@section('content')

<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>نظام</b> الحسابات</a>
  </div>

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">قم بتسجيل الدخول لبدء جلستك</p>

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="كلمة المرور" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember">
              <label for="remember">
                تذكرني
              </label>
            </div>
          </div>

          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">دخول</button>
          </div>
        </div>
      </form>

      <p class="mb-1 mt-2">
        <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
      </p>

    </div>
  </div>
</div>
</body>

@endsection
