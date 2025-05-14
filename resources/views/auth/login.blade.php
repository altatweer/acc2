@extends('layouts.app')

@section('content')
<style>
    body.login-bg {
        background: linear-gradient(135deg, #e0e7ef 0%, #cfd9e6 100%) !important;
        min-height: 100vh;
        direction: rtl;
    }
    .login-box {
        margin: 4% auto;
        max-width: 410px;
    }
    .login-card-body {
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(0,123,255,0.08), 0 1.5px 8px rgba(0,0,0,0.04);
        background: #fff;
        padding: 2.2rem 2rem 1.5rem 2rem;
    }
    .login-logo {
        margin-bottom: 1.2rem;
    }
    .login-logo img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0,123,255,0.10);
        background: #fff;
        padding: 8px;
        margin-bottom: 0.5rem;
    }
    .login-box-msg {
        font-size: 1.15rem;
        color: #007bff;
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
        direction: rtl;
    }
    .input-group-text {
        background: #e3eafc;
        border-radius: 0 8px 8px 0;
        border: none;
        color: #007bff;
    }
    .btn-primary {
        background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
        border: none;
        border-radius: 8px;
        font-size: 1.08rem;
        font-weight: 700;
        padding: 0.7rem 0;
        box-shadow: 0 2px 8px rgba(0,123,255,0.08);
        transition: background 0.2s;
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #0056b3 0%, #007bff 100%);
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
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="@lang('auth.email')" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="@lang('auth.password')" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
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
