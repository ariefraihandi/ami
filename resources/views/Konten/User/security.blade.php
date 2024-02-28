@extends('Index/app')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@php
    use Carbon\Carbon;
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">{{$title}} / {{$subtitle}} /</span> Security</h4>
  <div class="row">
    <!-- User Sidebar -->
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
      <!-- User Card -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="user-avatar-section">
            <div class="d-flex align-items-center flex-column">
              <img
                class="img-fluid rounded my-4"
                src="{{ asset('assets') }}/img/staff/{{ $user->image }}"
                height="110"
                width="110"
                alt="User avatar" />
              <div class="user-info text-center">
                <h4 class="mb-2">{{ $user->name }}</h4>
                <span class="badge bg-label-secondary">{{$role->role}}</span>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-around flex-wrap my-4 py-3">
            <div class="d-flex align-items-start me-4 mt-3 gap-3">
              <span class="badge bg-label-primary p-2 rounded"><i class="bx bx-timer bx-sm"></i></span>
              <div>
                <h5 class="mb-0">{{ Carbon::parse($user->created_at)->format('M y') }}</h5>
                <span>Join Since</span>
              </div>
            </div>
            
          </div>
          <h5 class="pb-2 border-bottom mb-4">Details</h5>
          <div class="info-container">
            <ul class="list-unstyled">
              <li class="mb-3">
                <span class="fw-medium me-2">Username:</span>
                <span>{{ $user->username }}</span>
              </li>
              <li class="mb-3">
                <span class="fw-medium me-2">Email:</span>
                <span>{{ $user->email }}</span>
              </li>
              <li class="mb-3">
                <span class="fw-medium me-2">Status:</span>
                <span class="badge bg-label-success">Active</span>
              </li>
              <li class="mb-3">
                <span class="fw-medium me-2">Role:</span>
                <span>{{$role->role}}</span>
              </li>
              <li class="mb-3">
                <span class="fw-medium me-2">Contact:</span>
                <span>{{$user->wa}}</span>
              </li>
            </ul>
            <div class="d-flex justify-content-center pt-3">
              <a
                href="javascript:;"
                class="btn btn-primary me-3"
                data-bs-target="#editUser"
                data-bs-toggle="modal"
                >Edit</a
              >
              <a href="javascript:;" class="btn btn-label-danger suspend-user">Suspended</a>
            </div>
          </div>
        </div>
      </div>
      <!-- /User Card -->
    </div>
    <!--/ User Sidebar -->

    <!-- User Content -->
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
      <!-- User Pills -->
      @php        
        $variabel1 = '<li class="nav-item">
                        <a class="nav-link" href="' . route('user.profile') . '"><i class="bx bx-user me-1"></i>Account</a>
                      </li>';
        $variabel2 = '<li class="nav-item">
                        <a class="nav-link" href="' . route('user.gaji') . '"><i class="bx bxs-wallet me-1"></i>Gaji</a>
                      </li>';
        $variabel3 = '<li class="nav-item">
                        <a class="nav-link" href="' . route('user.setting') . '"><i class="bx bx-cog me-1"></i>Setting</a>
                      </li>';
        $variabel4 = '<li class="nav-item">
                        <a class="nav-link active" href="' . route('user.security') . '"><i class="bx bx-lock-alt me-1"></i>Security</a>
                      </li>';
        $urls = $childSubMenus->pluck('url')->toArray();
      @endphp

      <ul class="nav nav-pills flex-column flex-md-row mb-3">
        @foreach($urls as $url)
          @if(in_array($url, ['user.profile', 'user.gaji', 'user.setting', 'user.security']))
            @php
              $routeName = array_search($url, ['user.profile', 'user.gaji', 'user.setting', 'user.security']);
              $variableName = 'variabel' . ($routeName + 1);
              echo $$variableName;
            @endphp
          @endif
        @endforeach
      </ul>
      <!--/ User Pills -->
    
    <div class="card mb-4">
        <h5 class="card-header">Ganti Password</h5>
        <div class="card-body">
            <form id="formChangePassword" method="POST" action="{{ route('change.password') }}">
                @csrf
                <div class="alert alert-warning" role="alert">
                    <h6 class="alert-heading mb-1">Password Minimal 6 Karakter</h6>
                    <span>Gunakan Kombinasi Symbol & Angka</span>
                </div>             
                <div class="mb-3 col-12 col-sm-12 form-password-toggle">
                    <label class="form-label" for="newPassword">Password Lama</label>
                    <div class="input-group input-group-merge">
                      <input
                        class="form-control"
                        type="password"
                        id="oldPassword"
                        name="oldPassword"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                      <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                  </div>
                <div class="mb-3 col-12 col-sm-12 form-password-toggle">
                    <label class="form-label" for="newPassword">Password Baru</label>
                    <div class="input-group input-group-merge">
                      <input
                        class="form-control"
                        type="password"
                        id="newPassword"
                        name="newPassword"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                      <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                </div>
                <div class="mb-3 col-12 col-sm-12 form-password-toggle">
                    <label class="form-label" for="confirmPassword">Konfirmasi Password Baru</label>
                    <div class="input-group input-group-merge">
                      <input
                        class="form-control"
                        type="password"
                        name="confirmPassword"
                        id="confirmPassword"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                      <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                  </div>                
                <button type="submit" class="btn btn-primary me-2">Change Password</button>
            </form>
        </div>
    </div>
    
    
    </div>
    <!--/ User Content -->
  </div>
</div>
<!-- / Content -->
  
@endsection


@push('footer-script')
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/cleavejs/cleave.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/cleavejs/cleave-phone.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
 @endpush

 @push('footer-Sec-script')
 <script src="{{ asset('assets') }}/js/modal-edit-user.js"></script>
 <script src="{{ asset('assets') }}/js/modal-enable-otp.js"></script>
 <script src="{{ asset('assets') }}/js/app-user-view.js"></script>
 <script src="{{ asset('assets') }}/js/app-user-view-security.js"></script>
 <script>
   @if(session('response'))
       var response = @json(session('response'));
       showSweetAlert(response);
   @endif
 </script>  
 @endpush