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
      <div class="card mb-4">
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
                      {{-- <span class="text-muted me-2"> vs. yesterday</span> --}}
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
                    <span class="text-muted me-2">Outcome Rp. {{ number_format($totalOutcome) }}</span>
                    <p class="mb-0">
                      @if ($percentageTotal < 0)
                      <span class="badge bg-label-success">{{ number_format($percentageTotal, 1) }}% </span>
                    @elseif ($percentageTotal > 0)
                      <span class="badge bg-label-danger">+ {{ number_format($percentageTotal, 1) }}% </span>
                    @else
                      <span class="badge bg-label-secondary">{{ number_format($percentageTotal, 1) }}% </span>
                    @endif                    
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
          <table id="dataTable" class="table border-top">
            <thead>
              <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">#Reference</th>
                  <th class="text-center">Amount</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Tanggal</th>
                  <th class="text-center">Deskripsi</th>
                  <th class="text-center cell-fit">Actions</th>
              </tr>
          </thead>          
          </table>
        </div>
      </div>      
    </div>       
  </div>
</div>

<!-- Add New Transaction -->
<div class="modal fade" id="addNewTransactionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-transaction">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3>Add New Transaction</h3>
          <p>Add new transaction details</p>
        </div>
        <form id="addNewTransactionForm" class="row g-3" action="{{ route('addNewTransaction') }}" method="POST">
          @csrf
          <div class="col-12">
            <label class="form-label" for="transactionAmount">Transaction Amount</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text">Rp.</span>
                <input type="text" class="form-control" id="transactionAmount" name="transactionAmount" placeholder="Transaction Amount" oninput="formatCurrency(this)" />
                <span class="input-group-text">.00</span>
            </div>
        </div>
             
          <div class="col-12">
              <label class="form-label" for="description">Description</label>
              <textarea id="description" name="description" class="form-control" placeholder="Description"></textarea>
          </div>
          <div class="col-12">
              <label class="form-label" for="paymentMethod">Payment Method</label>
              <select id="paymentMethod" name="paymentMethod" class="form-select">
                  <option value="Cash">Cash</option>
                  <option value="Transfer">Transfer</option>
              </select>
          </div>
          <div class="col-12">
              <label class="form-label" for="status">Status</label>
              <select id="status" name="status" class="form-select">
                  <option value="4">Operational</option>
                  <option value="5">Ambilan</option>
                  <option value="6">Setoran Kas</option>
                  <option value="7">Top Up</option>
              </select>
          </div>
          <div class="col-12">
              <label class="form-label" for="transactionDate">Transaction Date</label>
              <input type="date" id="transactionDate" name="transactionDate" class="form-control" value="<?= date('Y-m-d') ?>" />
          </div>
          <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary me-sm-3 me-1 mt-3" onclick="submitTransaction()">Submit</button>
              <button type="reset" class="btn btn-label-secondary btn-reset mt-3" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
      </form>
      
      </div>
    </div>
  </div>
</div>
<!--/ Add New Transaction -->

<!-- Edit Transaction -->
@foreach($transaction as $item)
  <div class="modal fade" id="editTransactionModal{{$item->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-transaction">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3>Edit Transaction</h3>
            <p>Add new transaction details</p>
          </div>
          <form id="addNewTransactionForm" class="row g-3" action="{{ route('editTransaction') }}" method="POST">
            @csrf
            <div class="col-12">
              <label class="form-label" for="transactionDate">Transaction Date</label>
              <input type="date" id="transactionDate" name="transactionDate" class="form-control" value="{{ \Carbon\Carbon::parse($item->transaction_date)->format('Y-m-d') }}" />
            </div>
          
            <input type="hidden" class="form-control" id="id" name="id" value="{{$item->id	}}" />
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary me-sm-3 me-1 mt-3">Submit</button>
                <button type="reset" class="btn btn-label-secondary btn-reset mt-3" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endforeach

<!--/ Edit Transaction -->
@endsection


@push('footer-script')
<script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>


<script>
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
                data: 'reference_number',
                targets: 1,
                render: function (data, type, full, meta) {
                    var sourceReceiver = full.source_receiver;
                    var referenceNumber = full.reference_number;                
                    // Creates full output for row
                    return '<div class="text-center">' + sourceReceiver + '<br>' + '#' + referenceNumber + '</div>';
                }
            },         
            {
              data: 'transaction_amount',
              targets: 2,
              render: function (data, type, full, meta) {
                  var transaction_amount = full.transaction_amount;
                  
                  // Creates full output for row
                  return '<div class="text-center">'+ ' Rp. ' + transaction_amount +'</div>';
              }
            },    
            {
              data: 'status',
              targets: 3,
              render: function (data, type, full, meta) {
                  var status = full.status;
                  var badgeClass = '';

                  switch (String(status)) {
                      case '1':
                      case '2':
                      case '3':
                          badgeClass = 'bg-label-primary';
                          status = 'Invoice';
                          break;
                      case '4':
                          badgeClass = 'bg-label-danger';
                          status = 'Operational';
                          break;
                      case '5':
                          badgeClass = 'bg-label-warning';
                          status = 'Ambilan';
                          break;
                      case '6':
                          badgeClass = 'bg-label-secondary';
                          status = 'Setoran Kas';
                          break;
                      case '7':
                          badgeClass = 'bg-label-success';
                          status = 'Top Up';
                          break;
                      default:
                          badgeClass = 'bg-label-secondary';
                          status = status ? status : 'Unknown';
                  }

                  // Ensure the correct HTML structure
                  return '<div class="text-center"><span class="badge ' + badgeClass + '">' + status + '</span></div>';
              }
            },
            {
              data: 'created_at',
              targets: 5,
              render: function (data, type, full, meta) {
                  var created_at = full.created_at;
                  
                  // Creates full output for row
                  return '<div class="text-center">'+ created_at +'</div>';
              }
            },    
            {
              data: 'description',
              targets: 6,
              render: function (data, type, full, meta) {
                  var description = full.description;
                  
                  // Creates full output for row
                  return '<div class="text-center">'+ description +'</div>';
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
                        '<a href="#" class="text-body" data-bs-toggle="modal" data-bs-target="#editTransactionModal' + id + '">' +
                            '<i class="bx bxs-message-square-edit mx-1"></i>' +
                        '</a>' +
                        '<a href="#" data-bs-toggle="tooltip" class="text-body" data-bs-placement="top" title="Hapus" onclick="return confirm(\'Are you sure?\')">' +
                            '<i class="bx bx-trash mx-1"></i>' +
                        '</a>' +
                    '</div>'
                  );
              }
            },       
        ],
        order: [[0, 'asc']],       
      dom:
      '<"row mx-1"' +
      '<"col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start gap-3"l<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start mt-md-0 mt-3"B>>' +
      '<"col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row pe-3 gap-md-3"f<"transaction_date_filter mb-3 mb-md-0"><"transaction_status">>' + '>t' + '<"row mx-2"' + '<"col-sm-12 col-md-6"i>' + '<"col-sm-12 col-md-6"p>' + '>',

      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Invoice'
      },
      buttons: [
          {
            text: '<i class="bx bx-plus me-md-1"></i><span class="d-md-inline-block d-none">Add Transaction</span>',
            className: 'btn btn-primary',
            action: function (e, dt, button, config) {
                // Tampilkan Modal
                $('#addNewTransactionModal').modal('show');
            }
          }
        ],
            
        initComplete: function () {
      var table = this.api();

      table.columns(5).every(function () {
          var column = this;
          var select = $('<select id="TransactionDateFilter" class="form-select"><option value="">All Dates</option></select>')
              .appendTo('.transaction_date_filter')
              .on('change', function () {
                  var val = $.fn.dataTable.util.escapeRegex($(this).val());

                  // Clear existing filters
                  $.fn.dataTable.ext.search = [];

                  // Apply the custom filter function
                  if (val) {
                      $.fn.dataTable.ext.search.push(dataTableTransactionDateFilter);
                  }

                  // Redraw the table
                  table.draw();
              });

          var dateFilterOptions = [
              { value: 'today', label: 'Today' },
              { value: 'this_week', label: 'This Week' },
              { value: 'this_month', label: 'This Month' },
              // Add more options if needed
          ];

          dateFilterOptions.forEach(function (option) {
              select.append('<option value="' + option.value + '">' + option.label + '</option>');
          });
      });

      function dataTableTransactionDateFilter(settings, data, dataIndex) {
        var currentDate = new Date();
        var dateParts = data[4].split(' '); // Gunakan indeks 4 karena kolom tanggal berada di indeks 4
        var day = parseInt(dateParts[0], 10);
        var month = getMonthIndex(dateParts[1]);
        var year = 2000 + parseInt(dateParts[2], 10);
        var transactionDate = new Date(year, month, day);

        var filterType = $('#TransactionDateFilter').val();

        switch (filterType) {
            case 'today':
                return transactionDate.toDateString() === currentDate.toDateString();
            case 'this_week':
                var oneDay = 24 * 60 * 60 * 1000;
                var diffDays = Math.round(Math.abs((currentDate - transactionDate) / oneDay));
                return diffDays <= 7;
            case 'this_month':
                return transactionDate.getMonth() === currentDate.getMonth() && transactionDate.getFullYear() === currentDate.getFullYear();
            default:
                return true; // No filter
        }
      }

      function getMonthIndex(monthAbbreviation) {
          var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
          return months.indexOf(monthAbbreviation);
      }

      table.columns(3).every(function () {
              var column = this;
              var select = $(
                  '<select id="StatusFilter" class="form-select"><option value="">Status</option></select>'
              )
                  .appendTo('.transaction_status')
                  .on('change', function () {
                      var val = $.fn.dataTable.util.escapeRegex($(this).val());
                      column.search(val ? '^' + val + '$' : '', true, false).draw();
                  });

              var statusOptions = ['Invoice', 'Operational', 'Ambilan', 'Setoran Kas', 'Top Up'];

              statusOptions.forEach(function (d) {
                  select.append('<option value="' + d.toLowerCase() + '" class="text-capitalize">' + d + '</option>');
              });
          });

      console.log('Init Complete Finished');
    }

    });

    dt_invoice_table.on('draw.dt', function () {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
          boundary: document.body
        });
      });
    });

    // Delete Record
    $('.invoice-list-table tbody').on('click', '.delete-record', function () {
      dt_invoice.row($(this).parents('tr')).remove().draw();
    });

    // Filter form control to default size
    // ? setTimeout used for multilingual table initialization
    setTimeout(() => {
      $('.dataTables_filter .form-control').removeClass('form-control-sm');
      $('.dataTables_length .form-select').removeClass('form-select-sm');
    }, 300);
  });
</script>

   
<script>
  // Function to format currency
  function formatCurrency(input) {
      const value = input.value.replace(/[^\d]/g, '');

      // Format the number with currency symbol
      const formattedValue = new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
      }).format(value);

      // Set the formatted value to the input field
      input.value = formattedValue.replace('Rp', '');
  }
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
