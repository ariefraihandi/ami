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
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">{{$title}} /</span> {{$subtitle}}</h4>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Category List</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                Add Category
            </button>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th class="text-center">No.</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center cell-fit">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $category->nama }}</td>
                        <td class="text-center">{{ $category->created_at->format('D M Y') }}</td>
                        <td class="text-center cell-fit">
                           <!-- Tombol Edit -->
                            <a href="#" class="text-body edit-category-btn" data-category-id="{{ $category->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                <i class="bx bx-edit mx-1"></i>
                            </a>

                            
                    
                            <!-- Tombol Hapus -->
                            <a href="{{ route('deleteCategory', ['id' => $category->id]) }}" class="text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                <i class="fas fa-trash mx-1"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>        
    </div>
  </div>
  
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
  
<!-- Edit Item Modal -->
    @foreach($categories as $item)
        <div class="modal fade" id="editCategoryModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-edit-user">
            <div class="modal-content p-3 p-md-6">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3>Edit Category</h3>
                    <p>Edit the category information</p>
                </div>
                <form action="{{ route('updateCategory') }}" method="post">
                    @csrf
                    <div class="col-12">
                        <label class="form-label" for="editCategoryName">Category Name</label>
                        <input type="text" name="editCategoryName" id="editCategoryName" class="form-control" placeholder="Enter category name" value="{{ $item->nama }}" />
                    </div>
                    <input type="hidden" name="editCategoryId" value="{{ $item->id }}">
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
<!-- Edit Item Modal -->
@endsection



@push('footer-script')
<script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>

@endpush

@push('footer-Sec-script')
<script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil semua elemen tombol edit
        var editButtons = document.querySelectorAll('.edit-category-btn');

        // Tambahkan event listener untuk setiap tombol
        editButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                // Ambil ID kategori dari atribut data
                var categoryId = event.currentTarget.getAttribute('data-category-id');

                // Buat ID modal dari ID kategori
                var modalId = '#editCategoryModal' + categoryId;

                // Aktifkan modal secara manual
                var modal = new bootstrap.Modal(document.querySelector(modalId));
                modal.show();
            });
        });
    });
</script>


<script>
  var sweetAlertData = @json(session('response'));

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
</script>

@endpush