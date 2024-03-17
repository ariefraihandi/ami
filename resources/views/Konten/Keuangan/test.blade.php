@extends('Index/app')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">DataTables /</span> Advanced</h4>
    <!-- Advanced Search -->
    <div class="card">     
      <!--Search Form -->
      <div class="card-body">
        <form class="dt_adv_search" method="POST">
          <div class="row">
            <div class="col-12">
              <div class="row g-3">
                <div class="col-12 col-sm-6 col-lg-4">
                    <label class="form-label">REFERENCE:</label>
                    <input
                        type="text"
                        class="form-control dt-input reference-number"
                        data-column="1"
                        placeholder="No Invoice/Reference"
                        data-column-index="0" />
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <label class="form-label">Date:</label>
                    <div class="mb-0">
                        <input
                            type="text"
                            class="form-control dt-date flatpickr-range dt-input"
                            data-column="6"
                            placeholder="StartDate to EndDate"
                            data-column-index="6"
                            name="dt_date" />
                        <input
                            type="hidden"
                            class="form-control dt-date start_date dt-input"
                            data-column="6"
                            data-column-index="6"
                            name="value_from_start_date" />
                        <input
                            type="hidden"
                            class="form-control dt-date end_date dt-input"
                            name="value_from_end_date"
                            data-column="6"
                            data-column-index="6" />
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <label class="form-label">Status:</label>
                    <select class="form-select dt-input dt-status-filter" data-column="4" data-column-index="2">
                        <option value="">All</option> <!-- Opsi untuk menampilkan semua status -->
                        <option value="Invoice">Invoice</option>
                        <option value="Operational">Operational</option>
                        <option value="Ambilan">Ambilan</option>
                        <option value="Setoran Kas">Setoran Kas</option>
                        <option value="Top Up">Top Up</option>
                        <option value="Bonus">Bonus</option>
                        <option value="Gaji">Gaji</option>
                        <!-- Tambahkan opsi lainnya sesuai dengan status yang ada -->
                    </select>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <hr class="mt-0" />        
      <div class="card-datatable table-responsive">
        <table class="dt-advanced-search table border-top">
          <thead>
            <tr>
                <th style="text-align: center; width: 10%;">No.</th>
                <th style="text-align: center; width: 20%;">#REFERENCE</th>
                <th style="text-align: center; width: 15%;">AMOUNT</th>
                <th style="text-align: center; width: 15%;">STATUS</th>
                <th style="text-align: center; width: 25%;">DESKRIPSI</th>
                <th style="text-align: center; width: 15%;">DATE</th>                
                <th style="text-align: center; width: 15%;">Action</th>                
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <!--/ Advanced Search -->
  </div>
<!-- Modal -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-transaction">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3>Edit Transaction</h3>
          <p>Transaction ID: <span id="transactionId"></span> | Reference Number: <span id="referenceNumber"></span></p>
        </div>
          <form id="addNewTransactionForm" class="row g-3" action="{{ route('editTransaction') }}" method="POST">
            @csrf
            <div class="col-12">
              <label class="form-label" for="transactionDate">Transaction Date</label>
              <input type="date" class="form-control" id="transactionDate" name="transactionDate">
            </div>
            <div class="col-12">
              <label class="form-label" for="amount">Jumlah</label>
              <input type="text" class="form-control" id="amount" name="amount">
            </div>
            <input type="hidden" class="form-control" id="invoice_number" name="invoice_number">
            <input type="hidden" class="form-control" id="id" name="id">                         
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary me-sm-3 me-1 mt-3">Submit</button>
              <button type="reset" class="btn btn-label-secondary btn-reset mt-3" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>


@endsection

@push('footer-script')  
  <!-- Vendors JS -->
  <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

  <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
  <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script>         
        @if(session('response'))
            var response = @json(session('response'));
            showSweetAlert(response);
        @endif
    </script>
@endpush
