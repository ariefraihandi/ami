@extends('Index/app')

@push('head-script')
<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css" />
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row invoice-add">
    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="py-3 mb-4"><span class="text-muted fw-light">{{$title}} /</span> {{$subtitle}}</h4>
      <!-- Invoice List Widget -->
      <div class="card mb-4">
        <div class="card-widget-separator-wrapper">
          <div class="card-body card-widget-separator">
            <div class="row gy-4 gy-sm-1">
              <div class="col-sm-6 col-lg-3">
                <div
                  class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                  <div>
                    <h6 class="mb-2">Jumlah Invoices</h6>
                    <h4 class="mb-2">{{ $totalInvoices }}</h4>
                    <p class="mb-0">
                      <span class="text-muted me-2">Today {{$totalInvoicesToday}} Invoice</span>
                      @if ($percentageIncrease < 0)
                        <span class="badge bg-label-danger">{{ number_format($percentageIncrease, 1) }}%</span>
                      @elseif ($percentageIncrease > 0)
                        <span class="badge bg-label-success">+{{ number_format($percentageIncrease, 1) }}%</span>
                      @else
                        <span class="badge bg-label-secondary">{{ number_format($percentageIncrease, 1) }}%</span>
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
                    <h6 class="mb-2">Jatuh Tempo</h6>
                    <h4 class="mb-2">{{ $totalInvDueToday }}</h4>
                    <p class="mb-0">
                      <span class="text-muted me-2">Rp. {{ number_format($DueToday, 2) }}</span>
                      @if ($percentageDue < 0)
                        <span class="badge bg-label-success">{{ number_format($percentageDue, 1) }}%</span>
                      @elseif ($percentageDue > 0)
                        <span class="badge bg-label-danger">+{{ number_format($percentageDue, 1) }}%</span>
                      @else
                        <span class="badge bg-label-secondary">{{ number_format($percentageDue, 1) }}%</span>
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
                    <h6 class="mb-2">Total Invoices</h6>
                    <h4 class="mb-2">Rp.  {{ number_format($totalAmount, 2) }}</h4>
                    @if ($totalPercentage > 0)
                        <span class="badge bg-label-success">+ {{ number_format($totalPercentage, 1) }}% </span>
                      @elseif ($totalPercentage < 0)
                        <span class="badge bg-label-danger">{{ number_format($totalPercentage, 1) }}% </span>
                      @else
                        <span class="badge bg-label-secondary">{{ number_format($totalPercentage, 1) }}% </span>
                      @endif
                      <span class="text-muted me-2"> vs. yesterday</span>
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
                    <h6 class="mb-2">Unpaid Invoices</h6>
                    <h4 class="mb-2">Rp.  {{ number_format($totalUnAmount, 2) }}</h4>
                    <p class="mb-0">
                      @if ($unTotalPercentage < 0)
                      <span class="badge bg-label-success">{{ number_format($unTotalPercentage, 1) }}% </span>
                    @elseif ($unTotalPercentage > 0)
                      <span class="badge bg-label-danger">+ {{ number_format($unTotalPercentage, 1) }}% </span>
                    @else
                      <span class="badge bg-label-secondary">{{ number_format($unTotalPercentage, 1) }}% </span>
                    @endif
                    <span class="text-muted me-2"> vs. yesterday</span>
                    </p>
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
      </div>
      <!-- Invoice List Table -->
      <div class="card">
        <div class="card-datatable table-responsive">
          <table class="invoice-list-table table border-top">
            <thead>
              <tr>
                <th>No.</th>
                <th>#ID</th>
                <th><i class="bx bx-trending-up"></i></th>
                <th>Client</th>
                <th>Total</th>
                <th class="text-truncate">Tanggal Inv</th>
                <th>Invoice Status</th>
                <th class="cell-fit">Actions</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      
    </div>       
  </div>
</div>

<div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-labelledby="createInvoiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createInvoiceModalLabel">Create Invoice</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Form untuk membuat invoice -->
        <form id="createInvoiceForm" action="{{ route('add.invoice') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="customer_uuid" class="form-label">Customer Name</label>
            <select id="customer_uuid" name="customer_uuid" class="select2 form-select" data-allow-clear="true">              
              @foreach($customers as $customer)
                <option value="{{ $customer->uuid }}">{{ $customer->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="invoiceName" class="form-label">Invoice Name</label>
            <input type="text" class="form-control" id="invoiceName" name="invoiceName" placeholder="Enter Invoice Name" required>
          </div>
          <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select class="form-select" id="type" name="type" required>
              <option value="Sales">Sales</option>
              <option value="Project">Project</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Create</button>
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
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
<script src="{{ asset('assets') }}/js/app-invoice-list.js"></script>
<script>
  @if(session('response'))
      var response = @json(session('response'));
      showSweetAlert(response);
  @endif
</script>  

@endpush
