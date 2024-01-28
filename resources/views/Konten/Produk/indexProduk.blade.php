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
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">eCommerce /</span> Product List</h4>
    <!-- Product List Widget -->
    <div class="card mb-4">
      <div class="card-widget-separator-wrapper">
        <div class="card-body card-widget-separator">
          <div class="row gy-4 gy-sm-1">
            <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                    <div>
                        <h6 class="mb-2">Jumlah Product</h6>
                        <h4 class="mb-2">{{$productCount}}</h4>                  
                    </div>
                    <div class="avatar me-sm-4">
                        <span class="avatar-initial rounded bg-label-secondary">
                            <i class="bx bx-package bx-sm"></i> <!-- bx-package for product -->
                        </span>
                    </div>
                </div>
                <hr class="d-none d-sm-block d-lg-none me-4" />
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                    <div>
                        <h6 class="mb-2">Low Stock</h6>
                        <h4 class="mb-2">{{$productLow}}</h4>                  
                    </div>
                    <div class="avatar me-lg-4">
                        <span class="avatar-initial rounded bg-label-secondary">
                            <i class="bx bxs-x-circle bx-sm"></i> <!-- bx-warning for low stock -->
                        </span>
                    </div>
                </div>
                <hr class="d-none d-sm-block d-lg-none" />
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                    <div>
                        <h6 class="mb-2">Categories</h6>
                        <h4 class="mb-2">{{$cateCount}}</h4>
                    </div>
                    <div class="avatar me-sm-4">
                        <span class="avatar-initial rounded bg-label-secondary">
                            <i class="bx bxs-category bx-sm"></i> <!-- bx-category for categories -->
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-2">Inactive Product</h6>
                        <h4 class="mb-2">{{$inProductCount}}</h4>                  
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-secondary">
                            <i class="bx bx-infinite bx-sm"></i> <!-- bx-infinite for inactive product -->
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        </div>
      </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
          <table id="dataTable" class="table border-top">
            <thead>            
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Product</th>
                <th class="text-center">Kode</th>
                <th class="text-center">category</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Status</th>
                <th class="text-center">Tanggal</th>
             
                <th class="text-center cell-fit">Actions</th>
            </tr>

          </thead>          
          </table>
        </div>
      </div>        
    </div>
  </div>
<!-- Add New Product Modal -->
  <div class="modal fade" id="addProduct" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-product">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3>Add New Product</h3>
            <p>Add new product to your inventory</p>
          </div>
          <form action="{{ route('addProduct') }}" method="post">
            @csrf
            <div class="col-12">
                <label class="form-label" for="productName">Product Name</label>
                <input type="text" name="productName" id="productName" class="form-control" placeholder="Enter product name" />
            </div>
            <div class="row">
                <div class="col-6">
                    <label class="form-label" for="productCode">Kode Barang</label>
                    <input type="text" name="productCode" id="productCode" class="form-control" placeholder="Enter product code" />
                </div>
                <div class="col-6">
                    <label class="form-label" for="productStock">Stock</label>
                    <input type="number" name="productStock" id="productStock" class="form-control" placeholder="Enter stock quantity" />
                </div>
            </div>
            <div class="row">
              <div class="col-6">
                <label class="form-label" for="productStatus">Status</label>
                <select name="productStatus" id="productStatus" class="form-select">
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
              <div class="col-6">
                <label class="form-label" for="productCategory">Category</label>
                <select name="productCategory" id="productCategory" class="form-select">
                  @foreach($categories as $category)
                      <option value="{{ $category->nama }}">{{ $category->nama }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12">
                <label class="form-label" for="productDescription">Product Description</label>
                <textarea name="productDescription" id="productDescription" class="form-control" placeholder="Enter product description" rows="3"></textarea>
            </div>
            <!-- Add other fields as needed -->
        
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary me-sm-3 me-1 mt-3">Submit</button>
                <button type="reset" class="btn btn-label-secondary btn-reset mt-3" data-bs-dismiss="modal" aria-label="Close">
                    Cancel
                </button>
            </div>
        </form>
        
        </div>
      </div>
    </div>
  </div>
<!--/ Add New Product Modal -->

<!-- Add New Category Modal -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-simple modal-add-new-category">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3>Add New Category</h3>
                    <p>Add a new category to your inventory</p>
                </div>
            <form action="{{ route('addCategory') }}" method="post">
                @csrf
                <div class="col-12">
                <label class="form-label" for="categoryName">Category Name</label>
                <input type="text" name="categoryName" id="categoryName" class="form-control" placeholder="Enter category name" />
                </div>
    
                <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary me-sm-3 me-1 mt-3">Add Category</button>
                <button type="reset" class="btn btn-label-secondary btn-reset mt-3" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
            </form>
            </div>
        </div>
    </div>
  </div>
<!--/ Add New Category Modal -->

<!-- Edit Product Modal -->
  @foreach($productData as $product)
    <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-edit-product">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3>Edit Product</h3>
                        <p>Edit product information</p>
                    </div>
                    <form action="{{ route('updateProduct') }}" method="post" enctype="multipart/form-data">
                      @csrf
                      <input type="hidden" name="editProductId" value="{{ $product->id }}">
                  
                      <div class="col-12 text-center">
                          @if($product->gambar)
                          <img src="{{ asset('assets/img/ecommerce-images/' . $product->gambar) }}" alt="Product Image" class="mt-2" style="max-width: 200px;">
                          @endif
                      </div>
                      <div class="col-12">
                          <label class="form-label" for="editProductName">Product Name</label>
                          <input type="text" name="editProductName" class="form-control" placeholder="Enter product name" value="{{ $product->name }}" />
                      </div>
                          <div class="row">
                            <div class="col-6">
                                <label class="form-label" for="editProductCode">Kode Barang</label>
                                <input type="text" name="editProductCode" class="form-control" placeholder="Enter product code" value="{{ $product->kode }}" />
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="editProductStock">Stock</label>
                                <input type="number" name="editProductStock" class="form-control" placeholder="Enter stock quantity" value="{{ $product->stock }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label" for="editProductStatus">Status</label>
                                <select name="editProductStatus" class="form-select">
                                    <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="editProductCategory">Category</label>
                                <select name="editProductCategory" class="form-select">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->nama }}" {{ $product->category == $category->nama ? 'selected' : '' }}>{{ $category->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="editProductDescription">Product Description</label>
                            <textarea name="editProductDescription" class="form-control" placeholder="{{ $product->deskripsi }}" value="{{ $product->deskripsi }}" rows="3">{{ $product->deskripsi }}</textarea>
                        </div>
                        <div class="col-12">
                          <label class="form-label" for="editProductImage">Product Image:</label>
                          <input type="file" name="editProductImage" class="form-control">
                      </div>
                  
                      <div class="col-12 text-center">
                          <button type="submit" class="btn btn-primary me-sm-3 me-1 mt-3">Update</button>
                          <button type="button" class="btn btn-label-secondary btn-reset mt-3" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                      </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
  @endforeach
<!--/ Edit Product Modal -->

@endsection

@push('footer-script')
<script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
  <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
  <script>
    function dataTableCategoryFilter(value) {
      return function (settings, data, dataIndex) {
        var selectedCategory = value.toLowerCase();
        var rowDataCategory = data[3].toLowerCase(); // Sesuaikan indeks jika diperlukan

        if (selectedCategory === '' || rowDataCategory === selectedCategory) {
          return true;
        }

        return false;
      };
    }

    $(document).ready(function () {
      var dt_keuangan_table = $('#dataTable').DataTable({
          ajax: "{{ url()->current() }}",
          success: function (data) {
              console.log("Ajax Response:", data);

              // Add additional console logs if needed
              console.log("Reference Number:", data[0].reference_number);
              console.log("Source Receiver:", data[0].source_receiver);
              console.log("Status:", data[0].status); // Add this line to log the 'status'
          },
          error: function (xhr, status, error) {
              console.error("Ajax Error:", status, error);
          },
          columns: [
              {
                data: null,
                targets: 0,
                render: function (data, type, full, meta) {
                  return meta.row + 1;
                }
              },
              {
                data: 'product',
                targets: 1,
                render: function (data, type, full, meta) {
                    var name = full.name;
                    var description = full.deskripsi;
                    var image = full.gambar;

                    if (image) {
                        // For Product image
                        var output = '<img src="' + assetsPath + 'img/ecommerce-images/' + image + '" alt="' + name + '" class="rounded-2">';
                    } else {
                        // For Product badge
                        var stateNum = Math.floor(Math.random() * 6);
                        var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                        var state = states[stateNum];
                        var initials = name.match(/\b\w/g) || [];
                        initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
                        output = '<span class="avatar-initial rounded-2 bg-label-' + state + '">' + initials + '</span>';
                    }

                    // Creates full output for row
                    var rowOutput =
                        '<div class="d-flex justify-content-start align-items-center product-name">' +
                        '<div class="avatar-wrapper">' +
                        '<div class="avatar avatar me-2 rounded-2 bg-label-secondary">' +
                        output +
                        '</div>' +
                        '</div>' +
                        '<div class="d-flex flex-column">' +
                        '<h6 class="text-body text-nowrap mb-0">' +
                        name +
                        '</h6>' +
                        '<small class="text-muted text-truncate d-none d-sm-block">' +
                        description +
                        '</small>' +
                        '</div>' +
                        '</div>';
                    return rowOutput;
                }
              },
              {
                data: 'kode',
                targets: 2,
                render: function (data, type, full, meta) {
                    var kode = full.kode;
                    
                    // Creates full output for row
                    return '<div class="text-center">' + kode +'</div>';
                }
              },  
              {
                data: 'category',
                targets: 3,
                render: function (data, type, full, meta) {
                    var category = full.category;
                    
                    // Creates full output for row
                    return '<div class="text-center">'+ category +'</div>';
                }
              },    
              {
                data: 'stock',
                targets: 4,
                render: function (data, type, full, meta) {
                    var stock = full.stock;
                    
                    // Creates full output for row
                    return '<div class="text-center">' + stock +'</div>';
                }
              },    
              {
                data: 'status',
                targets: 5,
                render: function (data, type, full, meta) {
                    var status = full.status;
                    var badgeClass = (status === 'active') ? 'bg-label-success' : 'bg-label-warning';

                    // Ensure the correct HTML structure
                    return '<div class="text-center"><span class="badge ' + badgeClass + '">' + status + '</span></div>';
                }
              },
              {
                data: 'created_at',
                targets: 6,
                render: function (data, type, full, meta) {
                    var created_at = full.created_at;
                    
                    // Creates full output for row
                    return '<div class="text-center">'+ created_at +'</div>';
                }
              },                 
              {
                targets: -1,
                title: 'Actions',
                searchable: false,
                orderable: false,
                render: function (data, type, full, meta) {
                    var id = full.id;

                    return (
                        '<div class="d-flex align-items-center">' +
                          '<a href="#" data-bs-toggle="modal" class="text-body" data-bs-target="#editProductModal'+ id + '" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"> ' + ' <i class="bx bxs-message-square-edit mx-1"></i></a>' +
                        '<a href="#"  data-bs-toggle="tooltip" class="text-body" data-bs-placement="top" title="Hapus" onclick="return confirm(\'Are you sure?\')"><i class="bx bx-trash mx-1"></i></a>' +                  
                        '</div>'
                    );
                }
              },       
          ],
          order: [[0, 'asc']],       
        dom:
        '<"row mx-1"' +
        '<"col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start gap-3"l<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start mt-md-0 mt-3"B>>' +
        '<"col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row pe-3 gap-md-3"f<"category_filter mb-3 mb-md-0"><"transaction_status">>' + '>t' + '<"row mx-2"' + '<"col-sm-12 col-md-6"i>' + '<"col-sm-12 col-md-6"p>' + '>',

        language: {
          sLengthMenu: '_MENU_',
          search: '',
          searchPlaceholder: 'Search Product'
        },
        buttons: [
          {
              text: '<i class="bx bx-plus me-md-1 "></i><span class="d-md-inline-block d-none">Add Product</span>',
              className: 'btn btn-success',
              action: function (e, dt, button, config) {
                  // Tampilkan Modal Produk
                  $('#addProduct').modal('show');
              }
          },
          {
              text: '<i class="bx bx-plus me-md-1"></i><span class="d-md-inline-block d-none">Add Category</span>',
              className: 'btn btn-primary',
              action: function (e, dt, button, config) {
                  // Tampilkan Modal Kategori
                  $('#addCategoryModal').modal('show');
              }
          }
        ],

            
        initComplete: function () {
            var table = this.api();

            table.columns(3).every(function () {
                  var column = this;
                  var select = $('<select class="form-select"><option value="">All Categories</option></select>')
                      .appendTo('.category_filter')
                      .on('change', function () {
                          var val = $.fn.dataTable.util.escapeRegex($(this).val());

                          console.log('Category filter changed to:', val);

                          // Clear existing filters
                          $.fn.dataTable.ext.search = [];

                          // Apply the custom filter function
                          if (val) {
                              $.fn.dataTable.ext.search.push(dataTableCategoryFilter(val));
                          }

                          // Redraw the table
                          table.draw();
                      });

                  // Retrieve unique category values from the column
                  var categor = Array.from(new Set(column.data().toArray())).sort();

                  // Add options for each category
                  categor.forEach(function (category) {
                      select.append('<option value="' + category + '">' + category + '</option>');
                  });
              });



            table.columns(5).every(function () {
                    var column = this;
                    var select = $(
                        '<select id="StatusFilter" class="form-select"><option value="">Status</option></select>'
                    )
                        .appendTo('.transaction_status')
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });

                    var statusOptions = ['active', 'inactive'];

                    statusOptions.forEach(function (d) {
                        select.append('<option value="' + d.toLowerCase() + '" class="text-capitalize">' + d + '</option>');
                    });
                });

            console.log('Init Complete Finished');
        }
      });
    
      // Delete Record
      $('.invoice-list-table tbody').on('click', '.delete-record', function () {
        dt_invoice.row($(this).parents('tr')).remove().draw();
      });

      setTimeout(() => {
        $('.dataTables_filter .form-control').removeClass('form-control-sm');
        $('.dataTables_length .form-select').removeClass('form-select-sm');
      }, 300);
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ambil semua tautan dengan data-bs-toggle="tooltip"
        var editLinks = document.querySelectorAll('[data-bs-toggle="tooltip"]');

        // Tambahkan event handler untuk setiap tautan
        editLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault(); // Mencegah tautan melakukan navigasi standar
                var targetModalId = link.getAttribute('data-bs-target');
                
                // Tampilkan modal dengan menggunakan Bootstrap
                var modal = new bootstrap.Modal(document.querySelector(targetModalId));
                modal.show();
            });
        });
    });
  </script>

  <script>
    var sweetAlertData = @json(session('response'));

    if (sweetAlertData) {
      if (sweetAlertData.success) {
          Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              text: sweetAlertData.message,
          }).then(function() {
              // Kode tambahan setelah pengguna menutup SweetAlert
          });
      } else {
          Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: 'Terjadi kesalahan. ' + sweetAlertData.message, // Tampilkan pesan kesalahan dari server
              customClass: {
                  confirmButton: 'btn btn-primary'
              },
              buttonsStyling: false
          });
      }
    }
  </script>

@endpush