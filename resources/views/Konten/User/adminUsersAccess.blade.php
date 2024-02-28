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
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">{{$title}} / </span> {{$subtitle}}</h4>

        <!-- Role cards -->
        <div class="row g-4">
            @php
                $users = \App\Models\User::all();
            @endphp

            @foreach($allRole as $role)
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            @php                          
                                $userCount = $users->where('role', $role->id)->count();
                            @endphp
                            <h6 class="fw-normal">Total {{ $userCount }} users</h6>
                            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                @php
                                    $data = $users->where('role', $role->id);
                                @endphp
                                @if($data->isNotEmpty())
                                    @foreach($data as $user)
                                        <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                            title="{{ $user->name }}" class="avatar avatar-sm pull-up">
                                            <img class="rounded-circle"
                                                src="{{ asset('assets/img/staff/' . $user->image) }}"
                                                alt="Avatar" />
                                        </li>
                                    @endforeach
                                @endif
                            </ul>                        
                        </div>
                        <div class="d-flex justify-content-between align-items-end">
                            <div class="role-heading">
                                <h4 class="mb-1">{{ ucfirst($role->role) }}</h4>
                                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal{{ $role->id }}"
                                    class="role-edit-modal"><small>Edit Role</small></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card h-100">
            <div class="row h-100">
                <div class="col-sm-5">
                <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-3">
                    <img
                    src="{{ asset('assets') }}/img/illustrations/sitting-girl-with-laptop-light.png"
                    class="img-fluid"
                    alt="Image"
                    width="120"
                    data-app-light-img="illustrations/sitting-girl-with-laptop-light.png"
                    data-app-dark-img="illustrations/sitting-girl-with-laptop-dark.png" />
                </div>
                </div>
                <div class="col-sm-7">
                <div class="card-body text-sm-end text-center ps-sm-0">
                    <button
                    data-bs-target="#addRoleModal"
                    data-bs-toggle="modal"
                    class="btn btn-primary mb-3 text-nowrap add-new-role">
                    Add New Role
                    </button>
                    <p class="mb-0">Add role, if it does not exist</p>
                </div>
                </div>
            </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Table Role Access Menus</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Role</th>
                                @foreach($allMenu as $menu)
                                    <th>{{ ucfirst($menu->menu_name) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allRole->whereNotIn('role', ['administrator']) as $role)
                            <tr>
                                <td>{{ ucfirst($role->role) }}</td>
                                @foreach($allMenu as $menu)
                                    <td>
                                        <div class="form-check mt-3">
                                            @php
                                                $AccessMenu = \App\Models\AccessMenu::all();
                                                $hasAccess = $AccessMenu->where('user_id', $role->id)->where('menu_id', $menu->id)->isNotEmpty();
                                            @endphp
                                           <input class="form-check-input" data-role-id="{{ $role->id }}" data-type="menu" type="checkbox" value="{{ $menu->id }}" id="menu_{{ $menu->id }}" @if($hasAccess) checked @endif />
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>            
        </div>
        <div class="divider">
            <div class="divider-text">
                <i class="bx bx-star"></i>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Table Role Access Submenus</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Submenu</th>
                                @foreach($allRole->whereNotIn('role', ['administrator']) as $role)
                                    <th>{{ ucfirst($role->role) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allMenuSub as $submenu)
                                <tr>
                                    <td>{{ ucfirst($submenu->title) }}</td>
                                    @foreach($allRole->whereNotIn('role', ['administrator']) as $role)
                                        <td>
                                            <div class="form-check mt-3">
                                                @php
                                                    $AccessSubmenu = \App\Models\AccessSub::all();
                                                    $hasAccessSub  = $AccessSubmenu->where('role_id', $role->id)->where('submenu_id', $submenu->id)->isNotEmpty();
                                                @endphp                                           
                                                <input class="form-check-input" data-role-id="{{ $role->id }}" data-type="submenu" type="checkbox" value="{{ $submenu->id }}" id="menu_{{ $submenu->id }}" @if($hasAccessSub) checked @endif />
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>                    
                </div>
            </div>            
        </div>
        <div class="divider">
            <div class="divider-text">
                <i class="bx bx-star"></i>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Table Role Access ChildSubmenu</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Child Submenu</th>
                                @foreach($allRole->whereNotIn('role', ['administrator']) as $role)
                                    <th>{{ ucfirst($role->role) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allChildSub as $childSubmenu)
                                <tr>
                                    <td>{{ ucfirst($childSubmenu->title) }}</td>
                                    @foreach($allRole->whereNotIn('role', ['administrator']) as $role)
                                        <td>
                                            <div class="form-check mt-3">
                                                @php
                                                    $AccessChildSubmenu     = \App\Models\AccessSubChild::all();
                                                    $hasAccessChildSub = $AccessChildSubmenu->where('role_id', $role->id)->where('childsubmenu_id', $childSubmenu->id)->isNotEmpty();
                                                @endphp                                           
                                                <input class="form-check-input" data-role-id="{{ $role->id }}" data-type="childsubmenu" type="checkbox" value="{{ $childSubmenu->id }}" id="menu_{{ $childSubmenu->id }}" @if($hasAccessChildSub) checked @endif />
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>                    
                </div>
            </div>            
        </div>
    </div>
</div>
@endsection

@push('footer-script')
  <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
    <script src="{{ asset('assets') }}/js/admin-user.js"></script>
    <script>
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var roleId      = checkbox.getAttribute('data-role-id');
                var type        = checkbox.getAttribute('data-type');
                var menuId      = checkbox.getAttribute('value');

                // Kirim data ke controller
                fetch('/change-access', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        roleId: roleId,
                        menuId: menuId,
                        type: type
                    })
                })
                .then(response => {
                    if (response.ok) {
                        return response.json(); // Mengembalikan respons JSON
                    } else {
                        throw new Error('Gagal mengirim data ke controller');
                    }
                })
                .then(data => {
                    // Handle respons dari server
                    var title = 'Berhasil';
                    var message = '';
                    if (data.response === 'delete') {
                        message = 'Penghapusan Access menu ' + data.menuName + ' untuk role ' + data.roleName + ' berhasil';
                    } else if (data.response === 'adding') {
                        message = 'Penambahan Access menu ' + data.menuName + ' untuk role ' + data.roleName + ' berhasil';
                    }
                    showSweetAlert(title, message);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });

        function showSweetAlert(title, message) {
            // Lakukan logika untuk menampilkan SweetAlert di sini
            // Misalnya, Anda dapat menggunakan library SweetAlert
            // Contoh:
            Swal.fire({
                title: title,
                text: message,
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }
    </script>    
@endpush