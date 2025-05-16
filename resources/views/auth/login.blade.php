@extends('layouts.app')

@section('content')
<style>
    body.login-bg {
        background: linear-gradient(135deg, #0a2540 0%, #1e3a8a 100%) !important;
        min-height: 100vh;
        direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-box {
        width: 100%;
        max-width: 410px;
        margin: 0 auto;
    }
    .login-card-body {
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(10,37,64,0.10), 0 1.5px 8px rgba(0,0,0,0.08);
        background: #fff;
        padding: 2.2rem 2rem 1.5rem 2rem;
    }
    .login-logo {
        margin-bottom: 1.2rem;
        text-align: center;
    }
    .login-logo img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(30,58,138,0.10);
        background: #fff;
        padding: 8px;
        margin-bottom: 0.5rem;
    }
    .login-logo div {
        color: #0a2540;
        font-weight: bold;
        font-size: 1.3rem;
        letter-spacing: 1px;
    }
    .login-box-msg {
        font-size: 1.15rem;
        color: #1e3a8a;
        font-weight: 700;
        margin-bottom: 1.5rem;
        letter-spacing: 0.5px;
    }
    .form-control {
        font-size: 1.08rem;
        background: #f8fafc;
        border: 1.5px solid #e0e7ef;
        border-radius: 8px;
        padding: 0.85rem 1.1rem;
        direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        color: #222;
    }
    .form-control:focus {
        border-color: #1e3a8a;
        box-shadow: 0 0 0 2px #1e3a8a22;
    }
    .input-group-text {
        background: #e3eafc;
        border-radius: 0 8px 8px 0;
        border: none;
        color: #1e3a8a;
    }
    .btn-primary {
        background: linear-gradient(90deg, #1e3a8a 0%, #0a2540 100%);
        border: none;
        border-radius: 8px;
        font-size: 1.08rem;
        font-weight: 700;
        padding: 0.7rem 0;
        box-shadow: 0 2px 8px rgba(10,37,64,0.10);
        transition: background 0.2s;
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #0a2540 0%, #1e3a8a 100%);
    }
    .icheck-primary label {
        font-size: 1rem;
        color: #555;
        font-weight: 500;
    }
    .login-footer {
        text-align: center;
        color: #888;
        font-size: 0.98rem;
        margin-top: 1.5rem;
    }
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        padding: 0.7rem 1rem;
        margin-bottom: 1rem;
        font-size: 1rem;
    }
    @media (max-width: 500px) {
        .login-box { max-width: 98vw; }
        .login-card-body { padding: 1.2rem 0.5rem; }
    }
</style>
<body class="login-bg">
<div class="login-box">
  <div class="login-logo">
    <img src="/assets/dist/img/logo.png" alt="AurSuite">
    <div><b>AurSuite</b></div>
  </div>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">@lang('auth.login_welcome')</p>
      @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="@lang('auth.email')" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <i class="fas fa-envelope"></i>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="@lang('auth.password')" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <i class="fas fa-lock"></i>
            </div>
          </div>
        </div>
        <div class="row align-items-center">
          <div class="col-7">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember">
              <label for="remember">@lang('auth.remember_me')</label>
            </div>
          </div>
          <div class="col-5 text-left">
            <button type="submit" class="btn btn-primary btn-block">@lang('auth.login')</button>
          </div>
        </div>
      </form>
      <p class="mb-1 mt-3 text-center">
        <a href="{{ route('password.request') }}">@lang('auth.forgot_password')</a>
      </p>
    </div>
  </div>
  <div class="login-footer mt-3">
    AurSuite &copy; 2025
  </div>
</div>
</body>
@endsection
