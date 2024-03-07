@extends('Index/app')

@push('head-script')
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
            <div class="col-12">
              <label class="form-label" for="amount">Jumlah</label>
              <input type="text" id="amount" name="amount" class="form-control" value="{{number_format($item->transaction_amount),0}}" />
            </div>
          
            <input type="hidden" class="form-control" id="id" name="id" value="{{$item->id}}" />
            <input type="hidden" class="form-control" id="invoice_number" name="invoice_number" value="{{$item->reference_number}}" />
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

<!-- Tambahkan ini pada bagian HTML Anda -->
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
            <option value="weekly">Mingguan</option>
            <option value="monthly">Bulanan</option>
            <option value="yearly">Tahunan</option>
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

@endsection


@push('footer-script')
<script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
<script src="{{ asset('assets') }}/js/keuangan.js"></script>
<script>
  @if(session('response'))
      var response = @json(session('response'));
      showSweetAlert(response);
  @endif
</script>  
@endpush