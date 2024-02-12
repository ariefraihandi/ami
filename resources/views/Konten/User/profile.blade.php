@extends('Index/app')

@push('head-script')
<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/@form-validation/umd/styles/index.min.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/page-user-view.css" />
@endpush

@php
    use Carbon\Carbon;
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">{{$title}} / {{$subtitle}} /</span> Account</h4>
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
            <div class="d-flex align-items-start mt-3 gap-3">
              <span class="badge bg-label-primary p-2 rounded"><i class="bx bx-customize bx-sm"></i></span>
              <div>
                <h5 class="mb-0">568</h5>
                <span>Projects Done</span>
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
      <ul class="nav nav-pills flex-column flex-md-row mb-3">
        <li class="nav-item">
          <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i>Account</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="app-user-view-security.html"
            ><i class="bx bx-lock-alt me-1"></i>Security</a
          >
        </li>      
      </ul>
      <!--/ User Pills -->
      <!-- Activity Timeline -->
      <div class="card mb-4">
        <h5 class="card-header">User Activity Timeline</h5>
        <div class="card-body">
            <ul class="timeline">
                @php
                    $latestLogin = null;
                @endphp
                @foreach($userActivities as $activity)
                    @if ($activity->activity === 'Logged in' && !$latestLogin)
                        @php
                            $latestLogin = $activity;
                        @endphp
                    @endif
                @endforeach
                @if ($latestLogin)
                    <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point-wrapper">
                            <span class="timeline-point @if($latestLogin->activity === 'Logged in') timeline-point-success @else timeline-point-primary @endif"></span>
                        </span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-1">
                                <h6 class="mb-0">{{ $latestLogin->activity }}</h6>
                                <small class="text-muted">{{ $latestLogin->created_at }}</small>
                            </div>
                            <p class="mb-2">{{ $latestLogin->ip_address }}</p>
                            @php
                                $deviceInfo = explode(' ', $latestLogin->device_info);
                            @endphp
                            @if ($deviceInfo[1] == 'Windows')
                                <i class="bx bx-desktop"></i> <!-- Icon komputer untuk Windows -->
                            @elseif (in_array($deviceInfo[1], ['iOS', 'Android']))
                                <i class="bx bx-mobile"></i> <!-- Icon hp untuk iOS dan Android -->
                            @endif
                        </div>
                    </li>
                @endif
                <li class="timeline-end-indicator">
                    <i class="bx bx-check-circle"></i>
                </li>
            </ul>
        </div>
    </div>
    
    
    
    
      <!-- /Activity Timeline -->      
    </div>
    <!--/ User Content -->
  </div>

  <!-- Modal -->
  <!-- Edit User Modal -->
  <div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3>Edit User Information</h3>
            <p>Updating user details will receive a privacy audit.</p>
          </div>
          <form id="editUserForm" class="row g-3" onsubmit="return false">
            <div class="col-12 col-md-6">
              <label class="form-label" for="modalEditUserFirstName">First Name</label>
              <input
                type="text"
                id="modalEditUserFirstName"
                name="modalEditUserFirstName"
                class="form-control"
                placeholder="John" />
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label" for="modalEditUserLastName">Last Name</label>
              <input
                type="text"
                id="modalEditUserLastName"
                name="modalEditUserLastName"
                class="form-control"
                placeholder="Doe" />
            </div>
            <div class="col-12">
              <label class="form-label" for="modalEditUserName">Username</label>
              <input
                type="text"
                id="modalEditUserName"
                name="modalEditUserName"
                class="form-control"
                placeholder="john.doe.007" />
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label" for="modalEditUserEmail">Email</label>
              <input
                type="text"
                id="modalEditUserEmail"
                name="modalEditUserEmail"
                class="form-control"
                placeholder="example@domain.com" />
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label" for="modalEditUserStatus">Status</label>
              <select
                id="modalEditUserStatus"
                name="modalEditUserStatus"
                class="form-select"
                aria-label="Default select example">
                <option selected>Status</option>
                <option value="1">Active</option>
                <option value="2">Inactive</option>
                <option value="3">Suspended</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label" for="modalEditTaxID">Tax ID</label>
              <input
                type="text"
                id="modalEditTaxID"
                name="modalEditTaxID"
                class="form-control modal-edit-tax-id"
                placeholder="123 456 7890" />
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label" for="modalEditUserPhone">Phone Number</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text">+1</span>
                <input
                  type="text"
                  id="modalEditUserPhone"
                  name="modalEditUserPhone"
                  class="form-control phone-number-mask"
                  placeholder="202 555 0111" />
              </div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label" for="modalEditUserLanguage">Language</label>
              <select
                id="modalEditUserLanguage"
                name="modalEditUserLanguage"
                class="select2 form-select"
                multiple>
                <option value="">Select</option>
                <option value="english" selected>English</option>
                <option value="spanish">Spanish</option>
                <option value="french">French</option>
                <option value="german">German</option>
                <option value="dutch">Dutch</option>
                <option value="hebrew">Hebrew</option>
                <option value="sanskrit">Sanskrit</option>
                <option value="hindi">Hindi</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label" for="modalEditUserCountry">Country</label>
              <select
                id="modalEditUserCountry"
                name="modalEditUserCountry"
                class="select2 form-select"
                data-allow-clear="true">
                <option value="">Select</option>
                <option value="Australia">Australia</option>
                <option value="Bangladesh">Bangladesh</option>
                <option value="Belarus">Belarus</option>
                <option value="Brazil">Brazil</option>
                <option value="Canada">Canada</option>
                <option value="China">China</option>
                <option value="France">France</option>
                <option value="Germany">Germany</option>
                <option value="India">India</option>
                <option value="Indonesia">Indonesia</option>
                <option value="Israel">Israel</option>
                <option value="Italy">Italy</option>
                <option value="Japan">Japan</option>
                <option value="Korea">Korea, Republic of</option>
                <option value="Mexico">Mexico</option>
                <option value="Philippines">Philippines</option>
                <option value="Russia">Russian Federation</option>
                <option value="South Africa">South Africa</option>
                <option value="Thailand">Thailand</option>
                <option value="Turkey">Turkey</option>
                <option value="Ukraine">Ukraine</option>
                <option value="United Arab Emirates">United Arab Emirates</option>
                <option value="United Kingdom">United Kingdom</option>
                <option value="United States">United States</option>
              </select>
            </div>
            <div class="col-12">
              <label class="switch">
                <input type="checkbox" class="switch-input" />
                <span class="switch-toggle-slider">
                  <span class="switch-on"></span>
                  <span class="switch-off"></span>
                </span>
                <span class="switch-label">Use as a billing address?</span>
              </label>
            </div>
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
              <button
                type="reset"
                class="btn btn-label-secondary"
                data-bs-dismiss="modal"
                aria-label="Close">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!--/ Edit User Modal -->

  <!-- Add New Credit Card Modal -->
  <div class="modal fade" id="upgradePlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-simple modal-upgrade-plan">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3>Upgrade Plan</h3>
            <p>Choose the best plan for user.</p>
          </div>
          <form id="upgradePlanForm" class="row g-3" onsubmit="return false">
            <div class="col-sm-9">
              <label class="form-label" for="choosePlan">Choose Plan</label>
              <select id="choosePlan" name="choosePlan" class="form-select" aria-label="Choose Plan">
                <option selected>Choose Plan</option>
                <option value="standard">Standard - $99/month</option>
                <option value="exclusive">Exclusive - $249/month</option>
                <option value="Enterprise">Enterprise - $499/month</option>
              </select>
            </div>
            <div class="col-sm-3 d-flex align-items-end">
              <button type="submit" class="btn btn-primary">Upgrade</button>
            </div>
          </form>
        </div>
        <hr class="mx-md-n5 mx-n3" />
        <div class="modal-body">
          <h6 class="mb-0">User current plan is standard plan</h6>
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex justify-content-center me-2 mt-3">
              <sup class="h5 pricing-currency pt-1 mt-3 mb-0 me-1 text-primary">$</sup>
              <h1 class="display-3 mb-0 text-primary">99</h1>
              <sub class="h5 pricing-duration mt-auto mb-2">/month</sub>
            </div>
            <button class="btn btn-label-danger cancel-subscription mt-3">Cancel Subscription</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Add New Credit Card Modal -->

  <!-- /Modal -->
</div>
<!-- / Content -->
  
@endsection


@push('footer-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
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
<script src="{{ asset('assets') }}/js/app-user-view.js"></script>
<script>
  @if(session('response'))
      var response = @json(session('response'));
      showSweetAlert(response);
  @endif
</script>  
@endpush
