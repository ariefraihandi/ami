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
                    <h6 class="mb-2">Pendapatan Hari Ini + Sisa</h6>                    
                    <h4 class="mb-2">Rp. {{ number_format($incomeToday) }} + {{number_format($sisaBefore)}}</h4>                       
                    <span class="text-muted me-2">Pendapatan Kemarin</span>                                  
                    <p class="mb-0">
                      <span class="text-muted me-2">Rp. {{ number_format($incomeYesterday) }}</span>
                      @php
                        if ($incomeYesterday > 0) {
                            $percentageIncome = ($incomeToday - $incomeYesterday) / $incomeYesterday * 100;
                        } else {
                            $percentageIncome = ($incomeToday > 0) ? 100 : 0;
                        }
                      @endphp                  
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
                    <h6 class="mb-2">Pengeluaran Hari Ini</h6>
                    <h4 class="mb-2">Rp. {{ number_format($outcomeToday) }}</h4>
                    <span class="text-muted me-2">Pengeluaran Kemarin</span> 
                    <p class="mb-0">
                      <span class="text-muted me-2">Rp. {{ number_format($outcomeYesterday) }}</span>
                      @if ($outcomeYesterday > 0)
                          @php
                              $percentageOutcome = ($outcomeToday - $outcomeYesterday) / $outcomeYesterday * 100;
                          @endphp
                          @if ($percentageOutcome < 0)
                              <span class="badge bg-label-success">{{ number_format($percentageOutcome, 1) }}%</span>
                          @elseif ($percentageOutcome > 0)
                              <span class="badge bg-label-danger">+{{ number_format($percentageOutcome, 1) }}%</span>
                          @else
                              <span class="badge bg-label-secondary">{{ number_format($percentageOutcome, 1) }}%</span>
                          @endif
                      @else
                          <span class="badge bg-label-secondary">0%</span>
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
                    <h6 class="mb-2">Top Up Hari Ini</h6>
                    <h4 class="mb-2">Rp. {{ number_format($topupToday) }}</h4>
                    <span class="text-muted me-2">Setor Kas Hari Ini</span> <br>
                    <span class="text-muted me-2">Rp. {{ number_format($setorToday) }}</span>                    
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
                    <h6 class="mb-2">Sisa Kas Hari Ini</h6>
                    <h4 class="mb-2">Rp.
                      {{ number_format($incomeToday+$sisaBefore+$topupToday-$outcomeToday-$setorToday) }}
                    </h4>
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
                <textarea id="description" required name="description" class="form-control" placeholder="Description"></textarea>
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
              <select id="status" name="status" class="form-select" onchange="showKaryawanSelect()">
                  <option value="4">Operational</option>
                  <option value="5">Ambilan</option>
                  <option value="8">Bonus</option>
                  <option value="6">Setoran Kas</option>
                  <option value="7">Top Up</option>
              </select>
            </div>
            
            <div id="karyawanSelectDiv" style="display: none;">
              <label class="form-label" for="karyawan">Karyawan</label>
              <select id="karyawan" name="karyawan" class="form-select">
                  <?php foreach ($usersData as $item): ?>
                      <option value="<?= $item->id ?>"><?= $item->name ?></option>
                  <?php endforeach; ?>
              </select>
            </div>
        
            <div class="col-12">
                <label class="form-label" for="transactionDate">Transaction Date</label>
                <input type="date" id="transactionDate" name="transactionDate" class="form-control" value="<?= date('Y-m-d') ?>" />
            </div>
            <div class="col-12">            
              <input type="checkbox" id="lunas" name="lunas" checked>
              <label for="lunas"> Lunas</label>
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

  <!-- Send Report -->
  <div class="modal fade" id="sendReportModal" tabindex="-1" aria-labelledby="sendReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="sendReportModalLabel">Send Report</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('send.report') }}" method="POST">
          @csrf <!-- Tambahkan CSRF token untuk keamanan -->
          <div class="modal-body">
            <p>Select report type:</p>
            <select id="reportType" name="reportType" class="form-select mb-3">
              <option value="daily">Harian</option>
              <option value="monthly">Bulanan</option>
              <option value="yearly">Tahunan</option>
              {{-- <option value="weekly">Mingguan</option> --}}
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Send</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!--/ Send Report -->
  
<!-- Modal Lihat Laporan -->
<div class="modal fade" id="lihatLaporanModal" tabindex="-1" aria-labelledby="lihatLaporanModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="lihatLaporanModalLabel">Lihat Laporan</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('send.report') }}" method="POST">
              @csrf <!-- Tambahkan CSRF token untuk keamanan -->
              <div class="modal-body">
                  <p>Select report type:</p>
                  <select id="reportType" name="reportType" class="form-select mb-3" required onchange="showDatePicker()">
                      <option value="">Pilih jenis laporan</option>
                      <option value="daily">Harian</option>
                      <option value="monthly">Bulanan</option>
                      <option value="yearly">Tahunan</option>
                  </select>

                  <div id="datePickerContainer" class="mb-3" style="display: none;">
                      <label for="datePicker" class="form-label">Pilih tanggal:</label>
                      <input type="date" id="datePicker" name="datePicker" class="form-control">
                  </div>

                  <div id="monthPickerContainer" class="mb-3 d-none">
                      <label for="monthPicker" class="form-label">Pilih bulan:</label>
                      <select id="monthPicker" name="monthPicker" class="form-select">
                          <option value="1">Januari</option>
                          <option value="2">Februari</option>
                          <option value="3">Maret</option>
                          <option value="4">April</option>
                          <option value="5">Mei</option>
                          <option value="6">Juni</option>
                          <option value="7">Juli</option>
                          <option value="8">Agustus</option>
                          <option value="9">September</option>
                          <option value="10">Oktober</option>
                          <option value="11">November</option>
                          <option value="12">Desember</option>
                      </select>
                  </div>

                  <div id="yearPickerContainer" class="mb-3 d-none">
                      <label for="yearPicker" class="form-label">Pilih tahun:</label>
                      <select id="yearPicker" name="yearPicker" class="form-select">
                          <!-- Year options will be populated dynamically using JavaScript -->
                      </select>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Send</button>
              </div>
          </form>
      </div>
  </div>
</div>
<!--/ Modal Lihat Laporan -->

  <!--Edit Transaction -->
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
                <label class="form-label" for="tanggalTransaksi">Transaction Date</label>
                <input type="date" class="form-control" id="tanggalTransaksi" name="tanggalTransaksi">
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
  <!--/ Edit Transaction -->

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
