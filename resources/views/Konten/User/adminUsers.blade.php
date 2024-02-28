@extends('Index/app')

@push('head-script')
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row invoice-add">
      <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">{{$title}} /</span> {{$subtitle}}</h4>
        <!-- Invoice List Widget -->
        {{-- <div class="card mb-4">
          <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
              <div class="row gy-4 gy-sm-1">
                <div class="col-sm-6 col-lg-3">
                  <div
                    class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                    <div>
                      <h6 class="mb-2">Daily Icome</h6>
                      <h4 class="mb-2">Rp. {{ number_format($totalToday) }}</h4>
                      <p class="mb-0">
                        <span class="text-muted me-2">Rp. {{ number_format($totalYesterday) }}</span>
                        @if ($percentageIncome < 0)
                          <span class="badge bg-label-danger">{{ number_format($percentageIncome, 1) }}%</span>
                        @elseif ($percentageIncome > 0)
                          <span class="badge bg-label-success">+{{ number_format($percentageIncome, 1) }}%</span>
                        @else
                          <span class="badge bg-label-secondary">{{ number_format($percentageIncome, 1) }}%</span>
                        @endif
                      </p>
                    </div>
                    <div class="avatar me-sm-4">
                      <span class="avatar-initial rounded bg-label-secondary">
                        <i class="bx bx-receipt bx-sm"></i>
                      </span>
                    </div>
                  </div>
                  <hr class="d-none d-sm-block d-lg-none me-4" />
                </div>
                <div class="col-sm-6 col-lg-3">
                  <div
                    class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                    <div>
                      <h6 class="mb-2">Daily Outcome</h6>
                      <h4 class="mb-2">Rp. {{ number_format($totalOutcomeToday) }}</h4>
                      <p class="mb-0">
                        <span class="text-muted me-2">Rp. {{ number_format($totalOutcomeYesterday) }}</span>
                        @if ($percentageOutcome < 0)
                          <span class="badge bg-label-success">{{ number_format($percentageOutcome, 1) }}%</span>
                        @elseif ($percentageOutcome > 0)
                          <span class="badge bg-label-danger">+{{ number_format($percentageOutcome, 1) }}%</span>
                        @else
                          <span class="badge bg-label-secondary">{{ number_format($percentageOutcome, 1) }}%</span>
                        @endif
                      </p>
                    </div>
                    <div class="avatar me-lg-4">
                      <span class="avatar-initial rounded bg-label-secondary">
                        <i class="bx bxs-error bx-sm"></i>
                      </span>
                    </div>
                  </div>
                  <hr class="d-none d-sm-block d-lg-none" />
                </div>
                <div class="col-sm-6 col-lg-3">
                  <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                    <div>
                      <h6 class="mb-2">Daily Margin</h6>
                      <h4 class="mb-2">Rp.  {{ number_format($marginToday) }}</h4>
                      <span class="text-muted me-2">Rp. {{ number_format($marginYesterday) }}</span>
                      @if ($percentageMargin > 0)
                          <span class="badge bg-label-success">+ {{ number_format($percentageMargin, 1) }}% </span>
                        @elseif ($percentageMargin < 0)
                          <span class="badge bg-label-danger">{{ number_format($percentageMargin, 1) }}% </span>
                        @else
                          <span class="badge bg-label-secondary">{{ number_format($percentageMargin, 1) }}% </span>
                        @endif
                      
                    </div>
                    <div class="avatar me-sm-4">
                      <span class="avatar-initial rounded bg-label-secondary">
                        <i class="bx bx-file bx-sm"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h6 class="mb-2">Kas</h6>
                      <h4 class="mb-2">Rp.  {{ number_format($totalkas) }}</h4>
                      <span class="text-muted me-2">Sisa Rp. {{ number_format($sisaTidakStor) }}</span>
                      
                    </div>
                    <div class="avatar">
                      <span class="avatar-initial rounded bg-label-secondary">
                        <i class="bx bx-wallet bx-sm"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> --}}

        <!-- Invoice List Table -->
        <div class="card">
          <div class="card-datatable table-responsive">
            <table id="dataTable" class="table border-top">
              <thead>
                <tr>
                    <th class="text-center">No.</th>
                    <th class="text-center">Karyawan</th>
                    <th class="text-center">Username</th>
                    <th class="text-center">Phone</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Alamat</th>
                    <th class="text-center">Since</th>
                    <th class="text-center cell-fit">Actions</th>
                </tr>
            </thead>          
            </table>
          </div>
        </div>      
      </div>       
    </div>
  </div>

  <!-- Add New Users -->
  <div class="modal fade" id="addUsers" tabindex="-1" aria-labelledby="addUsersLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="addUsersLabel">Tambah Karyawan</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form id="addUserForm" action="{{ route('add.users') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="modal-body">
                      <div class="mb-3">
                          <label for="name" class="form-label">Nama Lengkap</label>
                          <input type="text" class="form-control" id="name" name="name" required>
                      </div>
                      <div class="mb-3">
                          <label for="email" class="form-label">Email</label>
                          <input type="email" class="form-control" id="email" name="email" required>
                      </div>
                      <div class="mb-3">
                          <label for="username" class="form-label">Username</label>
                          <input type="text" class="form-control" id="username" name="username" required>
                      </div>
                      <div class="mb-3">
                          <label for="wa" class="form-label">WhatsApp</label>
                          <div class="input-group">
                              <span class="input-group-text">+62</span>
                              <input type="text" class="form-control" id="wa" name="wa">
                          </div>
                      </div>
                      <div class="mb-3">
                          <label for="role" class="form-label">Role</label>
                          <select class="form-select" id="role" name="role" required>
                              <option value="">Select Role</option>
                              @foreach(App\Models\UserRole::all() as $userRole)
                                  <option value="{{ $userRole->id }}">{{ ucwords($userRole->role) }}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="mb-3">
                          <label for="jabatan" class="form-label">Jabatan</label>
                          <select class="form-select" id="jabatan" name="jabatan" required>
                              <option value="">Select Jabatan</option>
                              <option value="direktur">Direktur</option>
                              <option value="supervisor percetakan">SPV Percetakan</option>
                              <option value="supervisor production">SPV Production</option>
                              <option value="designer">Design Grafis</option>
                              <option value="kasir">Admin / Kasir</option>
                              <option value="welder">Welder</option>
                              <option value="driver">Driver</option>                            
                              <option value="office support">Office Support</option>
                              <option value="stickerman">Stickerman</option>
                              <option value="welder">Welder</option>
                          </select>
                      </div>          
                      <div class="mb-3">
                          <label for="status" class="form-label">Status</label>
                          <select class="form-select" id="status" name="status" required>
                              <option value="">Select Status</option>
                              <option value="1">Karyawan Tetap</option>
                              <option value="2">Karyawan Harian</option>                            
                          </select>
                      </div>                                                               
                      <div class="mb-3">
                          <label for="address" class="form-label">Address</label>
                          <input type="text" class="form-control" id="address" name="address" Value="Lhokseumawe">
                      </div>
                      <div class="mb-3">
                          <label for="salary" class="form-label">Gaji</label>
                          <div class="input-group">
                              <span class="input-group-text">Rp.</span>
                              <input type="text" class="form-control" id="salary" name="salary" oninput="formatCurrency(this, 'salary')" required>
                              <span class="input-group-text">.00</span>
                          </div>
                      </div>
                      <div class="mb-3">
                          <label for="date_of_birth" class="form-label">Date of Birth</label>
                          <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                      </div>                
                      <div class="mb-3">
                          <label for="image" class="form-label">Image</label>
                          <input type="file" class="form-control" id="image" name="image" accept="image/*">
                      </div>
                  </div>
                  <input type="hidden" class="form-control" id="password" name="password"  value="123456" required>
                  <input type="hidden" class="form-control" id="token" name="token"  value="A6XRWxKqrVQWIf58ogY5j7sVf2M48jHiHyfUpSVED9v7F4JkYo" required>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Add User</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
  <!--/ Add New Users -->

  <!-- Edit user -->
    @foreach($users as $user)
      <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                          <label for="email" class="form-label">Email</label>
                          <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                      </div>
                      <div class="mb-3">
                          <label for="username" class="form-label">Username</label>
                          <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
                      </div>
                      <div class="mb-3">
                          <label for="wa" class="form-label">WhatsApp</label>
                          <div class="input-group">
                              <span class="input-group-text">+62</span>
                              <input type="text" class="form-control" id="wa" name="wa" value="{{ $user->wa }}" required>
                          </div>
                      </div>
                      <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            @foreach(App\Models\UserRole::all() as $userRole)
                                <option value="{{ $userRole->id }}" @if($userRole->id == $user->role) selected @endif>{{ ucwords($userRole->role) }}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <select class="form-select" id="jabatan" name="jabatan" required>
                            <option value="">Select Jabatan</option>
                            <option value="direktur" @if($user->jabatan == 'direktur') selected @endif>Direktur</option>
                            <option value="supervisor percetakan" @if($user->jabatan == 'supervisor percetakan') selected @endif>SPV Percetakan</option>
                            <option value="supervisor production" @if($user->jabatan == 'supervisor production') selected @endif>SPV Production</option>
                            <option value="designer" @if($user->jabatan == 'designer') selected @endif>Design Grafis</option>
                            <option value="kasir" @if($user->jabatan == 'kasir') selected @endif>Admin / Kasir</option>
                            <option value="welder" @if($user->jabatan == 'welder') selected @endif>Welder</option>
                            <option value="driver" @if($user->jabatan == 'driver') selected @endif>Driver</option>
                            <option value="office support" @if($user->jabatan == 'office support') selected @endif>Office Support</option>
                            <option value="stickerman" @if($user->jabatan == 'stickerman') selected @endif>Stickerman</option>
                            <option value="admin" @if($user->jabatan == 'admin') selected @endif>Admin</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="1" @if($user->status == 1) selected @endif>Karyawan Tetap</option>
                            <option value="2" @if($user->status == 2) selected @endif>Karyawan Harian</option>
                            <option value="0" @if($user->status == 0) selected @endif>Inactive</option>
                        </select>
                      </div>                   
                      <div class="mb-3">
                        <label for="gaji" class="form-label">Gaji</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control" id="gaji" name="gaji" oninput="formatCurrency(this, 'gaji')" value="{{ number_format($user->salary, 0, ',', '.') }}" required>
                            <span class="input-group-text">.00</span>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{$user->date_of_birth}}">
                      </div>      
                    </div>
                    <input type="hidden" class="form-control" id="id" name="id"  value="{{$user->id}}" required>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
    @endforeach
  <!--/ Edit user -->

@endsection

@push('footer-script')
  <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
  <script src="{{ asset('assets') }}/js/admin-user.js"></script>
  <script>
    @if(session('response'))
        var response = @json(session('response'));
        showSweetAlert(response);
    @endif
  </script>  
@endpush