@extends('Index/app-auth')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />

<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/@form-validation/umd/styles/index.min.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/page-auth.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush


@section('content')
    <div class="authentication-wrapper authentication-cover">
      <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
          <div class="w-100 d-flex justify-content-center">
            <img
              src="{{ asset('assets') }}/img/illustrations/boy-with-rocket-light.png"
              class="img-fluid"
              alt="Login image"
              width="700"
              data-app-dark-img="illustrations/boy-with-rocket-dark.png"
              data-app-light-img="illustrations/boy-with-rocket-light.png" />
          </div>
        </div>
        <!-- /Left Text -->

        <!-- Login -->
        <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
          <div class="w-px-400 mx-auto">
            <!-- Logo -->
            <div class="app-brand mb-5">
              <a href="index.html" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    <img src="{{ asset('assets') }}/img/icons/brands/ami-logo.png" alt="AMI Fast Logo" width="35">
                </span>
                <span class="app-brand-text demo text-body fw-bold">Aceh Mediatama</span>
              </a>
            </div>
            <!-- /Logo -->
            <h4 class="mb-2">Selamat Datang! 👋</h4>
            <p class="mb-4">Sign-in to your account and start the adventure</p>

            <form id="formAuthentication" class="mb-3" action="{{ route('login.post') }}" method="POST">
              @csrf
              <div class="mb-3">
                <label for="username" class="form-label">Email or Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your email or username" autofocus />
              </div>
              <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                      <label class="form-label" for="password">Password</label>
                      <a href="{{ route('login.page') }}">
                          <small>Forgot Password?</small>
                      </a>
                  </div>
                  
                  <div class="input-group input-group-merge">
                      <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password"/>
                      <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
              </div>            
              
              <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
              </div>
          </form>

            <p class="text-center">
              <span>New on our platform?</span>
              <a href="{{ route('register') }}">
                <span>Create an account</span>
              </a>
            </p>
           
          </div>
        </div>
        <!-- /Login -->
      </div>
    </div>
@endsection

@push('footer-script')  
  <script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
<script src="{{ asset('assets') }}/js/pages-auth.js"></script>

<script>
  @if(session('response'))
      var response = @json(session('response'));
      showSweetAlert(response);
  @endif
</script>  
@endpush
