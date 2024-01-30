@extends('Index/app')

   
    @push('head-script')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/@form-validation/umd/styles/index.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    @endpush
   

    @section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">{{ $title }} /</span> {{ $subtitle }}
        </h4>

        <!-- Customer Widget -->
        <div class="card mb-4">
            <div class="card-widget-separator-wrapper">
                <div class="card-body card-widget-separator">
                    <div class="row gy-4 gy-sm-1">
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                                <div>
                                    <h3 class="mb-2">{{ $menuCount }} Menus</h3>
                                    <!-- Button to open the modal with the plus icon -->
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddMenus">
                                        <i class="bx bx-plus me-1"></i> Add Menu
                                    </button>
                                </div>
                                <div class="avatar me-sm-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="bx bx-menu-alt-left bx-sm"></i>
                                    </span>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none me-4" />
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                                <div>
                                  <h3 class="mb-2">{{ $menusubCount }} Submenus</h3>
                                  <!-- Button to open the modal with the plus icon -->
                                  <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalAddSubmenus">
                                    <i class="bx bx-plus me-1"></i> Add Submenu
                                </button>
                                </div>
                                <div class="avatar me-lg-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="bx bx-building bx-sm"></i>
                                    </span>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none" />
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                                <div>
                                    <h3 class="mb-2">{{ $menuchildCount }} Child</h3>
                                    <!-- Button to open the modal with the plus icon -->
                                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddChildSubmenus">
                                      <i class="bx bx-plus me-1"></i> Add Child
                                  </button>
                                  </div>
                                <div class="avatar me-sm-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class='bx bx-buildings'></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-2">Inactive Custumer</h6>
                                    <h2 class="mb-2" style="color: red;">{{ $inActiveCustumer }}</h2>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class='bx bxs-error'></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 order-0 order-md-1">
            <!-- User Pills -->
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="/menu"><i class="bx bx-menu-alt-left me-1"></i>Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/menu/submenu"><i class="bx bx-menu-alt-left me-1"></i>Submenu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/menu/child"><i class="bx bx-menu-alt-left me-1"></i>Childs Submenu</a>
                </li>
            </ul>

            <div class="card">
                <div class="card-datatable table-responsive">
                    <table class="datatables-submmenus table border-top">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Submenu</th>
                                <th>Menu</th>
                                <th>url</th>
                                <th>icon</th>
                                <th>Dropdown</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddMenus" tabindex="-1" aria-labelledby="modalAddMenusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddMenusLabel">Add Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addMenuForm" action="{{ route('add.menu') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="menuName" class="form-label">Menu Name</label>
                            <input type="text" class="form-control" id="menuName" name="menu_name" required>
                        </div>
                        <div class="mb-3" style="display:none;">
                            <label for="order" class="form-label">Order</label>
                            <input type="number" class="form-control" id="order" name="order" value="1" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" value="1" checked>
                            <label class="form-check-label" for="status">Activate Menu</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Menu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modalAddSubmenus" tabindex="-1" aria-labelledby="modalAddSubmenusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddSubmenusLabel">Add Submenu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSubmenuForm" action="{{ route('add.submenu') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="submenuName" class="form-label">Submenu Name</label>
                            <input type="text" class="form-control" id="submenuName" name="submenu_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="menuId" class="form-label">Select Menu</label>
                            <select class="select2 form-select" id="menuId" name="menu_id" data-allow-clear="true" required>
                                <option value="">Select a menu</option>
                                @foreach($menu as $menuItem)
                                    <option value="{{ $menuItem->id }}">{{ ucfirst($menuItem->menu_name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" style="display:none;">
                            <label for="order" class="form-label">Order</label>
                            <input type="number" class="form-control" id="order" name="order" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label">URL</label>
                            <input type="text" class="form-control" id="url" name="url" required>
                        </div>
                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <input type="text" class="form-control" id="icon" name="icon" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="itemSub" name="itemsub" value="1">
                            <label class="form-check-label" for="itemSub">Enable Dropdown</label>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="submenuStatus" name="status" value="1" checked>
                            <label class="form-check-label" for="submenuStatus">Activate Submenu</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Submenu</button>
                    </form>
                </div>            
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddChildSubmenus" tabindex="-1" aria-labelledby="modalAddChildSubmenusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddChildSubmenusLabel">Add Child Submenu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addChildSubmenuForm" action="{{ route('add.ChildSubmenu') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="childsubmenuName" class="form-label">Child Submenu Name</label>
                            <input type="text" class="form-control" id="childsubmenuName" name="childsubmenu_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="submenuId" class="form-label">Select Submenu</label>
                            <select class="select2 form-select" id="submenuId" name="submenu_id" data-allow-clear="true" required>
                                <option value="">Select a submenu</option>
                                @foreach($menusub as $sub)
                                    <option value="{{ $sub->id }}">{{ ucfirst($sub->title) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" style="display:none;">
                            <label for="order" class="form-label">Order</label>
                            <input type="number" class="form-control" id="order" name="order" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label">URL</label>
                            <input type="text" class="form-control" id="url" name="url" required>
                        </div>                    
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="childSubmenuStatus" name="childSubmenuStatus" value="1" checked>
                            <label class="form-check-label" for="childSubmenuStatus">Activate Child Submenu</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Child Submenu</button>
                    </form>
                </div>            
            </div>
        </div>
    </div>

@endsection

@push('footer-script')  
  <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/cleavejs/cleave.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/cleavejs/cleave-phone.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
@endpush


@push('footer-Sec-script')
<script src="{{ asset('assets') }}/js/app-menu-all.js"></script>

<script>
  @if(session('response'))
      var response = @json(session('response'));
      showSweetAlert(response);
  @endif
</script>  
@endpush
