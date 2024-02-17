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
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">{{$title}} /</span> {{$subtitle}}</h4>
    <!-- Custumer Widget -->
    <div class="card mb-4">
      <div class="card-widget-separator-wrapper">
        <div class="card-body card-widget-separator">
          <div class="row gy-4 gy-sm-1">
            <div class="col-sm-6 col-lg-3">
              <div
                class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                <div>
                  <h6 class="mb-2">Individual</h6>
                  <h4 class="mb-2">{{ $individualCount }}</h4>
                  <p class="mb-0">
                    <span class="text-muted me-2">Hari Ini {{$individualToday}} Custumer</span>
                    @if ($individualPercn < 0)
                      <span class="badge bg-label-danger">{{ number_format($individualPercn, 0) }}%</span>
                    @elseif ($individualPercn > 0)
                      <span class="badge bg-label-success">+{{ $individualPercn }}%</span>
                    @else
                      <span class="badge bg-label-secondary">{{ $individualPercn }}%</span>
                    @endif
                  </p>
                </div>
                <div class="avatar me-sm-4">
                  <span class="avatar-initial rounded bg-label-secondary">
                    <i class="bx bx-user bx-sm"></i>
                  </span>
                </div>
              </div>
              <hr class="d-none d-sm-block d-lg-none me-4" />
            </div>
            <div class="col-sm-6 col-lg-3">
              <div
                class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                <div>
                  <h6 class="mb-2">Biro</h6>
                  <h4 class="mb-2">{{ $biroCustomerCount }}</h4>
                  <p class="mb-0">
                    <span class="text-muted me-2">Hari Ini {{$biroToday}} Custumer</span>
                    @if ($biroPercn < 0)
                      <span class="badge bg-label-danger">{{ $biroPercn }}%</span>
                    @elseif ($biroPercn > 0)
                      <span class="badge bg-label-success">+{{ $biroPercn }}%</span>
                    @else
                      <span class="badge bg-label-secondary">{{ $biroPercn }}%</span>
                    @endif
                  </p>
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
                  <h6 class="mb-2">Instansi</h6>
                  <h4 class="mb-2">{{ $instansiCount }}</h4>
                  <p class="mb-0">
                    <span class="text-muted me-2">Hari Ini {{$instToday}} Custumer</span>
                    @if ($instPercn < 0)
                      <span class="badge bg-label-danger">{{ $instPercn }}%</span>
                    @elseif ($instPercn > 0)
                      <span class="badge bg-label-success">+{{ $instPercn }}%</span>
                    @else
                      <span class="badge bg-label-secondary">{{ $instPercn }}%</span>
                    @endif
                  </p>
                </div>
                <div class="avatar me-sm-4">
                  <span class="avatar-initial rounded bg-label-secondary">
                    <i class='bx bx-buildings' ></i>
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
    <!-- /Custumer Widget -->
    <!-- customers List Table -->
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="datatables-customers table border-top">
          <thead>
            <tr>
              <th>No</th>
              <th>Customer</th>
              <th>Status</th>
              <th>UUID</th>
              <th class="text-nowrap">Customer Id</th>
              <th>Order</th>
              <th>Total Spent</th>
              <th class="text-nowrap">Member Since</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Add New Custumer Modal -->
    <div class="modal fade" id="modalEcommerceCustomerAdd" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
        <div class="modal-content p-3 p-md-5">
          <div class="modal-body">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="text-center mb-4">
              <h3 class="address-title">Add New Customer</h3>
            </div>
            <form id="addNewAddressForm" class="row g-3" action="{{ route('add.customer') }}" method="post">
              @csrf 
              <div class="col-12">
                  <div class="row">
                      <div class="col-md mb-md-0 mb-3">
                          <div class="form-check custom-option custom-option-icon">
                              <label class="form-check-label custom-option-content" for="customRadioHome">
                                  <span class="custom-option-body">
                                      <i class="bx bx-user-circle"></i>
                                      <span class="custom-option-title">Individuals</span>
                                  </span>
                                  <input name="customer_type" class="form-check-input" type="radio" value="1" id="customRadioHome" checked />
                              </label>
                          </div>
                      </div>
                      <div class="col-md mb-md-0 mb-3">
                          <div class="form-check custom-option custom-option-icon">
                              <label class="form-check-label custom-option-content" for="customRadioOffice">
                                  <span class="custom-option-body">
                                      <i class="bx bxs-buildings"></i>
                                      <span class="custom-option-title">Biro</span>
                                  </span>
                                  <input name="customer_type" class="form-check-input" type="radio" value="2" id="customRadioOffice" />
                              </label>
                          </div>
                      </div>
                      <div class="col-md mb-md-0 mb-3">
                          <div class="form-check custom-option custom-option-icon">
                              <label class="form-check-label custom-option-content" for="customRadioOther">
                                  <span class="custom-option-body">
                                    <i class='bx bxs-business'></i>
                                      <span class="custom-option-title">Instansi</span>
                                  </span>
                                  <input name="customer_type" class="form-check-input" type="radio" value="3" id="customRadioOther" />
                              </label>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-12">
                  <label class="form-label" for="name">Nama Lengkap</label>
                  <input type="text" id="name" name="name" class="form-control" placeholder="Nama Lengkap" />
              </div>
              <div class="col-12">
                <label class="form-label" for="phone">Whatsapp</label>
                <input type="number" id="phone" name="phone" class="form-control" placeholder="08xx-xxx-xxxx" />
              </div>
              <div class="col-12">
                  <label class="form-label" for="email">e-Mail</label>
                  <input type="text" id="email" name="email" class="form-control" placeholder="example@mail.com" />
              </div>
              <div class="col-12">
                  <label class="form-label" for="address">Alamat</label>
                  <input type="text" id="address" name="address" class="form-control" value="Lhokseumawe" />
              </div>
              <div class="col-12 text-center">
                  <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                  <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">
                      Batal
                  </button>
              </div>
              <input type="hidden" id="country" name="country" class="form-control" value="Indonesia">
              <input type="hidden" id="country_code" name="country_code" class="form-control" value="id">
          </form>
          </div>
        </div>
      </div>
    </div>
  <!--/ Add New Custumer Modal -->

  <!-- Jenis Invoice Modal -->
  <div class="modal fade" id="jenisinvoice" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Pilih Jenis Invoice Untuk <div id="usernamePlaceholder"></div></h3>           
          </div>
          <div class="row">
            <div class="col-12 mb-3">
              <div class="form-check custom-option custom-option-basic">
                <label class="form-check-label custom-option-content ps-3" for="customRadioTemp1" data-bs-target="#sales" data-bs-toggle="modal">
                
                  <input name="customRadioTemp" class="form-check-input d-none" type="radio" value="" id="customRadioTemp1" />
                  <span class="d-flex align-items-start">
                  
                    <i class='bx bx-purchase-tag bx-md me-3'></i>
                    <span>
                      <span class="custom-option-header">
                        <span class="h4 mb-2">Sales</span>
                      </span>
                      <span class="custom-option-body">
                        <span class="mb-0"
                          >Tambah Invoice Sales Untuk Custumer Ini.</span>
                      </span>
                    </span>
                  </span>
                </label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check custom-option custom-option-basic">
                <label
                  class="form-check-label custom-option-content ps-3"
                  for="customRadioTemp2"
                  data-bs-target="#project"
                  data-bs-toggle="modal">
                  <input
                    name="customRadioTemp"
                    class="form-check-input d-none"
                    type="radio"
                    value=""
                    id="customRadioTemp2" />
                  <span class="d-flex align-items-start">
                    <i class='bx bx-basket bx-md me-3'></i>
                    <span>
                      <span class="custom-option-header">
                        <span class="h4 mb-2">Project</span>
                      </span>
                      <span class="custom-option-body">
                        <span class="mb-0"
                          >Tambah Invoice Project Untuk Custumer Ini.</span>
                      </span>
                    </span>
                  </span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Jenis Invoice Modal -->

  <!-- Modal Sales -->
  <div class="modal fade" id="sales" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <h5 class="mb-2 pt-1">Masukkan Nama Invoice Sales</h5>
          <p class="mb-4">Mulai membuat invoice dengan memberikan nama untuk Sales.</p>
          <form id="salesForm" action="{{ route('add.invoice') }}" method="POST">
            @csrf
            <input type="hidden" class="form-control" name="type" value="Sales" />
            <input type="hidden" class="form-control" name="customer_uuid" id="customerUUIDSales" value="" />
            <div class="mb-4">
                <label for="invoiceName">Invoice Name:</label>
                <input type="text" class="form-control" name="invoiceName" id="invoiceName" placeholder="Spanduk Burger 4x3" required>
            </div>            
            <div class="col-12 text-end">
              <button type="button" class="btn btn-label-secondary me-sm-3 me-2 px-3 px-sm-4" data-bs-toggle="modal" data-bs-target="#jenisinvoice">
                <i class="bx bx-left-arrow-alt bx-xs me-1 scaleX-n1-rtl"></i><span class="align-middle">Back</span>
              </button>
                <button type="submit" class="btn btn-primary px-3 px-sm-4">
                    <span class="align-middle">Continue</span><i class="bx bx-right-arrow-alt bx-xs ms-1 scaleX-n1-rtl"></i>
                </button>
            </div>
        </form>
        
        </form>
        
        </div>
      </div>
    </div>
  </div>
  <!--/ Modal Sales -->

  <!-- Modal Project -->
  <div class="modal fade" id="project" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <h5 class="mb-2 pt-1">Masukkan Nama Invoice Project</h5>
          <p class="mb-4">Mulai membuat invoice dengan memberikan nama untuk Project.</p>
          <form id="projectForm" action="{{ route('add.invoice') }}" method="POST">
            @csrf
            <input type="hidden" class="form-control" name="type" value="Project" />
            <div class="mb-4">
              <label for="invoiceNameProject">Invoice Name:</label>
              <input type="text" class="form-control" name="invoiceName" id="invoiceNameProject" placeholder="Project Tata Ruang Bank Aceh" required>
            </div>
            <input type="hidden" class="form-control" name="customer_uuid" id="customerUUIDProject" placeholder="Customer UUID" readonly />
            <div class="col-12 text-end">
              <button type="button" class="btn btn-label-secondary me-sm-3 me-2 px-3 px-sm-4" data-bs-toggle="modal" data-bs-target="#jenisinvoice">
                <i class="bx bx-left-arrow-alt bx-xs me-1 scaleX-n1-rtl"></i><span class="align-middle">Back</span>
              </button>
              <button type="submit" class="btn btn-primary px-3 px-sm-4">
                <span class="align-middle">Continue</span><i class="bx bx-right-arrow-alt bx-xs ms-1 scaleX-n1-rtl"></i>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!--/ Modal Project -->

  <!-- Edit Customer Modal -->
  <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data <span id="editCustomerName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm" action="{{ route('edit.customer') }}" method="post">
                    @csrf
                    <!-- Other form fields -->
                    <div class="mb-3">
                      <label for="name" class="form-label">Nama Custumer</label>
                      <input type="text" class="form-control" id="nameedit" name="name" value="">
                  </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Whatsapp</label>
                        <input type="text" class="form-control" id="phoneedit" name="phone" value="">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">e-Mail</label>
                        <input type="text" class="form-control" id="emailedit" name="email" value="">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="addressedit" name="address" value="">
                    </div>
                    <input type="hidden" class="form-control" id="uuidedit" name="uuid" value="">

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
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
  <script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/cleavejs/cleave.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/cleavejs/cleave-phone.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
<script src="{{ asset('assets') }}/js/app-ecommerce-customer-all.js"></script>
<script>
  @if(session('response'))
      var response = @json(session('response'));
      showSweetAlert(response);
  @endif
</script>  
@endpush
