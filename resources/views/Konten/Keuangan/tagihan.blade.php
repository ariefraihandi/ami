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
      {{-- <div class="card mb-4">
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
                    <h4 class="mb-2">Rp.  {{ number_format($sisaTidakStor) }}</h4>
                    <span class="text-muted me-2">Sisa Rp. {{ number_format($totalkas) }}</span>
                    
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
      </div> --}}

      <!-- Tagihan List Table -->
      <div class="card">
        <div class="card-datatable table-responsive">
          <table id="dataTable" class="table border-top">
            <thead>
              <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">Nama Karyawan</th>
                  <th class="text-center">Jabatan</th>
                  <th class="text-center">Hari kerja</th>
                  <th class="text-center">Gaji Pokok</th>
                  <th class="text-center">Bonus</th>
                  <th class="text-center">Ambilan</th>
                  <th class="text-center">Gaji Bersih</th>
                  <th class="text-center">Periode</th>
                  <th class="text-center cell-fit">Actions</th>
              </tr>
          </thead>          
          </table>
        </div>
      </div>      
    </div>       
  </div>
</div>

<!-- Modal Bayar Gaji -->
<div class="modal fade" id="bayarGaji" tabindex="-1" aria-labelledby="bayarGajiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-transaction">
      <div class="modal-content p-3 p-md-5">
          <div class="modal-body">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              <div class="text-center mb-4">
                <h3>Bayar Gaji</h3>
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">  
                        <input class="form-check-input" type="checkbox" id="select-all">
                      </th>
                      <th scope="col">Nama Tagihan</th>
                      <th scope="col">Jumlah Gaji</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Loop untuk setiap tagihan -->
                    @foreach($tagihan as $item)
                    <tr>
                      <td>
                        <input class="form-check-input tagihan-checkbox" type="checkbox" id="tagihan_{{ $item->id }}" name="tagihan[]" value="{{ $item->id }}" data-id-tagih="{{ $item->id_tagih }}">
                      </td>
                      <td>{{ $item->nama_tagihan }}</td>
                      <td>
                        @if($item->masa_kerja != 0)
                            Rp. {{ number_format($item->jumlah_tagihan * $item->masa_kerja, 0) }},-
                        @else
                            Rp. {{ number_format($item->jumlah_tagihan, 0) }},-
                        @endif
                    </td>                    
                    </tr>
                    @endforeach
                  </tbody>
                </table>
            </div>
            <h3>Total:</h3>
            <p id="total-tagihan">Rp. 0,-</p>
            
              <form id="bayarGajiForm" class="row g-3" action="" method="POST">
                  @csrf
                  <!-- Tambahkan input atau elemen form lainnya sesuai kebutuhan Anda -->
                  <div class="col-12 text-center">
                      <button type="submit" class="btn btn-primary me-sm-3 me-1 mt-3">Submit</button>
                      <button type="reset" class="btn btn-label-secondary btn-reset mt-3" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>
<!-- /Modal Bayar Gaji -->

<!--/ Add New Transaction -->

<!-- Edit Masa Kerja -->
@foreach($tagihan as $item)
  <div class="modal fade" id="editmasakerja{{$item->id_tagih}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-transaction">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3>Edit Masa Kerja</h3>
            <p>{{$item->nama_tagihan}}</p>
          </div>
          <form id="addNewTransactionForm" class="row g-3" action="{{ route('editMasaKerja') }}" method="POST">
            @csrf
            <div class="col-12">
              <label class="form-label" for="transactionDate">Masa Kerja</label>
              <input type="text" id="masa_kerja" name="masa_kerja" class="form-control" value="{{$item->masa_kerja}}" />
            </div>
            
            <input type="hidden" class="form-control" id="id" name="id" value="{{$item->id}}" />
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
<!--/ Edit  Masa Kerja -->

<!-- Tambahkan ini pada bagian HTML Anda -->
{{-- <div class="modal fade" id="sendReportModal" tabindex="-1" aria-labelledby="sendReportModalLabel" aria-hidden="true">
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
</div> --}}

@endsection


@push('footer-script')
<script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
<script src="{{ asset('assets') }}/js/tagihan.js"></script>
<script>
  @if(session('response'))
      var response = @json(session('response'));
      showSweetAlert(response);
  @endif
</script>  
@endpush