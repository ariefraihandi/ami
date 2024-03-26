@extends('Index/app')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">   
    <div class="card bg-transparent shadow-none border-0 my-4">
      <div class="card-body row p-0 pb-3">
        <div class="col-12 col-md-12">
          <h3>Laporan Harian 📃</h3>
          <div class="col-12 col-lg-7">
            <h4>Keuangan 💰</h4>
          </div>
          <div class="card mb-4">
            <div class="card-widget-separator-wrapper">
                <div class="card-body card-widget-separator">
                    <div class="row gy-4 gy-sm-1">
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                                <div>
                                    <h6 class="mb-2">Pemasukan</h6>
                                    <h5 class="mb-2">{{ number_format($incomeTotal), 0 }},-</h5>  
                                    <hr>     
                                    <h6 class="mb-2">Saldo Lampau</h6>
                                    <h5 class="mb-0">{{ number_format($sisaBefore), 0 }},-</h5>           
                                </div>
                                <div class="avatar me-sm-4">
                                    <span class="avatar-initial rounded bg-label-secondary">                                          
                                      <i class='bx bx-wallet-alt bx-tada' ></i>
                                    </span>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none me-4" />
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                                <div>
                                  <h6 class="mb-2">Top Up:</h6>
                                  <h5 class="mb-2">{{ number_format($topup), 0 }},-</h5>  
                                  <hr>
                                  <h6 class="mb-2">Operational:</h6>
                                  <h5 class="mb-2">{{ number_format($outcomeTotal-$topup), 0 }},-</h5>  
                                </div>
                                <div class="avatar me-lg-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                      <i class='bx bx-money-withdraw bx-tada' ></i>
                                    </span>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none" />
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                                <div>
                                  <h6 class="mb-2">Pengeluaran:</h6>
                                  <h5 class="mb-2">{{ number_format($outcomeTotal), 0 }},-</h5> 
                                  <hr>
                                  <h6 class="mb-2">Bon Konsumen</h6>
                                    <h5 class="mb-0">{{ number_format($invoiceBon), 0 }},-</h5>                                                                                                                                  
                                </div>
                                <div class="avatar me-sm-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                      <i class='bx bx-money bx-tada' ></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                  <h6 class="mb-2">Setoran Kas</h6>
                                  <h5 class="mb-0">{{ number_format($saldoKas), 0 }},-</h5>
                                  <hr>
                                    <h6 class="mb-2">Sisa Kas</h6>
                                    <h4 class="mb-0">{{ number_format($sisaBefore+$incomeTotal+$topup-$outcomeTotal-$saldoKas), 0 }},-</h4>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                      <i class='bx bxs-bank bx-tada' ></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>   
        </div>
      </div>
    </div>

    <div class="card bg-transparent shadow-none border-0 my-4">
      <div class="card-body row p-0 pb-3">
        <div class="col-12 col-md-8 card-separator">
          <h4>Invoice 📑</h4>          
          <div class="card mb-4">
            <div class="card-widget-separator-wrapper">
                <div class="card-body card-widget-separator">
                    <div class="row gy-4 gy-sm-1">
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                                <div>
                                    <h6 class="mb-2">Hari Ini</h6>
                                    <h5 class="mb-2">{{ $countInvoice }}</h5>           
                                </div>
                                <div class="avatar me-sm-4">
                                    <span class="avatar-initial rounded bg-label-secondary">                                          
                                      <i class='bx bx-book-content' ></i>
                                    </span>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none me-4" />
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                                <div>
                                  <h6 class="mb-2">Inv Lunas:</h6>
                                  <h5 class="mb-2">{{ $countLuns }}</h5>  
                                </div>
                                <div class="avatar me-lg-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                      <i class='bx bxs-circle-half' ></i>
                                    </span>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none" />
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                                <div>
                                  <h6 class="mb-2">Inv Panjar:</h6>
                                  <h5 class="mb-2">{{ $countPanjar }}</h5>                                                                                                   
                                </div>
                                <div class="avatar me-sm-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                      <i class='bx bx-time-five' ></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                  <h6 class="mb-2">Inv Bon:</h6>
                                  <h5 class="mb-2">{{ $countInvBB }}</h5>                        
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                      <i class='bx bx-error-alt' ></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4 ps-md-3 ps-lg-5 pt-3 pt-md-0">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div>
                <h5 class="mb-2">Invoice Chart</h5>              
              </div>
              <div class="time-spending-chart">
                <h3 class="mb-2">{{$countInvoice}} <span class="text-muted">Inv</span></h3>
                <span class="badge bg-label-success">Harian</span>
              </div>
            </div>
            <div id="leadsReportChart"></div>
          </div>
        </div>
      </div>
    </div>
   

    <!-- Topic and Instructors -->
    <div class="row mb-4 g-4">
      <div class="col-12 col-xl-8">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Chart Keuangan Harian</h5>          
          </div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <div id="horizontalBarChart"></div>
            </div>
            <div class="col-md-6 d-flex justify-content-around align-items-center">
              <div>
                <div class="d-flex align-items-baseline">
                  <span class="text-primary me-2"><i class="bx bxs-circle"></i></span>
                  <div>
                    <p class="mb-2">Saldo Lampau</p>
                    <h5>{{ number_format($sisaBefore), 0 }},-</h5>
                  </div>
                </div>
                <div class="d-flex align-items-baseline my-3">
                  <span class="text-info me-2"><i class="bx bxs-circle"></i></span>
                  <div>
                    <p class="mb-2">Top Up Harian</p>
                    <h5>{{ number_format($topup), 0 }},-</h5>
                  </div>
                </div>
                <div class="d-flex align-items-baseline">
                  <span class="text-danger me-2"><i class="bx bxs-circle"></i></span>
                  <div>
                    <p class="mb-2">Pengeluaran</p>
                    <h5>{{ number_format($outcomeTotal), 0 }},-</h5>
                  </div>
                </div>
              </div>

              <div>
                <div class="d-flex align-items-baseline">
                  <span class="text-success me-2"><i class="bx bxs-circle"></i></span>
                  <div>
                    <p class="mb-2">Pemasukan</p>
                    <h5>{{ number_format($incomeTotal), 0 }},-</h5>
                  </div>
                </div>
                <div class="d-flex align-items-baseline my-3">
                  <span class="text-secondary me-2"><i class="bx bxs-circle"></i></span>
                  <div>
                    <p class="mb-2">Setor Kas</p>
                    <h5>{{ number_format($saldoKas), 0 }},-</h5>
                  </div>
                </div>
                <div class="d-flex align-items-baseline">
                  <span class="text-warning me-2"><i class="bx bxs-circle"></i></span>
                  <div>
                    <p class="mb-2">Sisa Kas</p>
                    <h5>{{ number_format($sisaBefore+$incomeTotal+$topup-$outcomeTotal-$saldoKas), 0 }},-</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-4 col-md-6">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">Kelola Laporan</h5>
            </div>            
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="dateSelect" class="form-label">Pilih Tanggal:</label>
              <input type="date" class="form-control" placeholder="{{ date('Y-m-d', strtotime($startDate)) }}" value="{{ date('Y-m-d', strtotime($startDate)) }}" name="startDate" id="startDate" />
            </div>            
            <a href="{{ url('/report') }}/?startDate={{ date('Y-m-d', strtotime($startDate)) }}&endDate={{ date('Y-m-d', strtotime($startDate)) }}" target="_blank" class="btn btn-label-secondary d-grid w-100 mb-3">
              <span class="d-flex align-items-center justify-content-center text-nowrap">
                <i class="bx bx-download bx-fade-down bx-xs me-2"></i>
                Download Laporan
              </span>
            </a>                      
            <a href="{{ url('/laporan/bulanan') }}/?startDate={{ date('Y-m-d', strtotime($startMonth)) }}&endDate={{ date('Y-m-d', strtotime($endMonth)) }}" target="_blank" class="btn btn-label-success d-grid w-100 mb-3">
              <span class="d-flex align-items-center justify-content-center text-nowrap">
                <i class="bx bxs-calendar bx-xs me-2"></i>
                Lihat Laporan Bulanan
              </span>
            </a>                                  
            <a href="{{ url('/laporan/tahunan') }}/?startDate={{ date('Y-m-d', strtotime($startYear)) }}&endDate={{ date('Y-m-d', strtotime($endYear)) }}" target="_blank" class="btn btn-label-primary d-grid w-100 mb-3">
              <span class="d-flex align-items-center justify-content-center text-nowrap">
                <i class="bx bx-calendar bx-xs me-2"></i>
                Lihat Laporan Tahunan
              </span>
            </a>        
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-12">
      <div class="row">
          <div class="col-6">
            <h4>Keungan Data 💸</h4>  
              <div class="col-xl-12">
                  <div class="nav-align-top mb-4">
                      <ul class="nav nav-tabs" role="tablist">
                          <li class="nav-item">
                              <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-income" aria-controls="navs-top-income" aria-selected="true">
                                  Pemasukan
                              </button>
                          </li>
                          <li class="nav-item">
                              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-outcome" aria-controls="navs-top-outcome" aria-selected="false">
                                  Pengeluaran
                              </button>
                          </li>
                          <li class="nav-item">
                              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-setoran" aria-controls="navs-top-setoran" aria-selected="false">
                                  Setoran Kas
                              </button>
                          </li>
                          <li class="nav-item">
                              <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-topup" aria-controls="navs-top-topup" aria-selected="false">
                                  Top Up
                              </button>
                          </li>
                      </ul>
                      <div class="tab-content">
                          <div class="tab-pane fade show active" id="navs-top-income" role="tabpanel">
                              <div class="card">                                  
                                  <div class="table-responsive text-nowrap">
                                    <table class="table">
                                      <thead>
                                        <tr>
                                          <th>No</th>
                                          <th>Transaksi</th>
                                          <th>Deskripsi</th>                                              
                                          <th>Invoice</th>                                              
                                          <th>Tanggal</th>                                              
                                          <th>Jumlah</th>                              
                                          <th>Metode</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        @if($incomeData->isEmpty())
                                          <tr>
                                            <td colspan="7" class="text-center">No Data Found</td>
                                          </tr>
                                        @else
                                          @foreach ($incomeData as $item)
                                            <tr>
                                              <td>{{ $loop->iteration }}</td>
                                              <td>{{ $item->source_receiver }}<br>
                                                @php
                                                    $invoice = \App\Models\Invoice::where('invoice_number', $item->reference_number)->first();
                                                    if ($invoice) {
                                                        $customerUuid = $invoice->customer_uuid;
                                                        $invoiceNumber = $invoice->invoice_number;
                                                        $url = url('/invoice/add') . "?invoiceNumber=$invoiceNumber&customerUuid=$customerUuid";
                                                    } else {
                                                        $url = '';
                                                    }
                                                @endphp
                                                <a href="{{ $url }}" target="_blank">{{ $item->reference_number }}</a>
                                              </td>
                                            
                                              <td>{{ $item->description }}</td>
                                              <td>
                                                @php
                                                  $invoice = \App\Models\Invoice::where('invoice_number', $item->reference_number)->first();
                                                  $invoiceName = $invoice ? $invoice->invoice_name : 'Invoice not found';
                                                @endphp
                                                {{ $invoiceName }}
                                              </td>
                                              <td>{{ $item->transaction_date }}</td>
                                              <td>Rp. {{ number_format($item->transaction_amount), 0 }},-</td>
                                              <td>{{ $item->payment_method }}</td>                                      
                                            </tr>
                                          @endforeach
                                        @endif
                                      </tbody>
                                    </table>
                                  </div>
                              </div>
                          </div>
                          <div class="tab-pane fade" id="navs-top-outcome" role="tabpanel">
                              <div class="card">                             
                                  <div class="table-responsive text-nowrap">
                                    <table class="table">
                                      <thead>
                                        <tr>
                                          <th>No</th>
                                          <th>Transaksi</th>
                                          <th>Deskripsi</th>  
                                          <th>Tanggal</th>                                                  
                                          <th>Jumlah</th>                              
                                          <th>Metode</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        @if($outcomeData->isEmpty())
                                          <tr>
                                            <td colspan="7" class="text-center">No Data Found</td>
                                          </tr>
                                        @else
                                          @foreach ($outcomeData as $item)
                                            <tr>
                                              <td>{{ $loop->iteration }}</td>
                                              <td>{{ $item->source_receiver }}<br>{{ $item->reference_number }}</td>
                                              <td>{{ $item->description }}</td>
                                              <td>{{ $item->transaction_date }}</td>
                                              <td>Rp. {{ number_format($item->transaction_amount), 0 }},-</td>
                                              <td>{{ $item->payment_method }}</td>                                      
                                            </tr>
                                          @endforeach
                                        @endif
                                      </tbody>
                                    </table>
                                  </div>
                              </div>
                          </div>
                          <div class="tab-pane fade" id="navs-top-setoran" role="tabpanel">
                              <div class="card">                              
                                  <div class="table-responsive text-nowrap">
                                      <table class="table">
                                        <thead>
                                          <tr>
                                            <th>No</th>
                                            <th>Transaksi</th>
                                            <th>Deskripsi</th>   
                                            <th>Tanggal</th>                                                 
                                            <th>Jumlah</th>                              
                                            <th>Metode</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          @if($setorData->isEmpty())
                                            <tr>
                                              <td colspan="7" class="text-center">No Data Found</td>
                                            </tr>
                                          @else
                                            @foreach ($setorData as $item)
                                              <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->source_receiver }}<br>{{ $item->reference_number }}</td>
                                                <td>{{ $item->description }}</td>
                                                <td>{{ $item->transaction_date }}</td>
                                                <td>Rp. {{ number_format($item->transaction_amount), 0 }},-</td>
                                                <td>{{ $item->payment_method }}</td>                                      
                                              </tr>
                                            @endforeach
                                          @endif
                                        </tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                          <div class="tab-pane fade" id="navs-top-topup" role="tabpanel">
                              <div class="card">                              
                                  <div class="table-responsive text-nowrap">
                                      <table class="table">
                                        <thead>
                                          <tr>
                                            <th>No</th>
                                            <th>Transaksi</th>
                                            <th>Deskripsi</th>  
                                            <th>Tanggal</th>                                                  
                                            <th>Jumlah</th>                              
                                            <th>Metode</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          @if($topupData->isEmpty())
                                            <tr>
                                              <td colspan="7" class="text-center">No Data Found</td>
                                            </tr>
                                          @else
                                            @foreach ($topupData as $item)
                                              <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->source_receiver }}<br>{{ $item->reference_number }}</td>
                                                <td>{{ $item->description }}</td>
                                                <td>{{ $item->transaction_date }}</td>
                                                <td>Rp. {{ number_format($item->transaction_amount), 0 }},-</td>
                                                <td>{{ $item->payment_method }}</td>                                      
                                              </tr>
                                            @endforeach
                                          @endif
                                        </tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col-6">
            <h4>Invoice Data 📋</h4>  
              <div class="col-xl-12">
                  <div class="nav-align-top mb-4">
                      <ul class="nav nav-tabs" role="tablist">
                          <li class="nav-item">
                              <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-invoice" aria-controls="navs-top-invoice" aria-selected="true">
                                  Invoice Hari Ini
                              </button>
                          </li>                          
                      </ul>
                      <div class="tab-content">
                          <div class="tab-pane fade show active" id="navs-top-invoice" role="tabpanel">
                              <div class="card">                                  
                                  <div class="table-responsive text-nowrap">
                                      <table class="table">
                                        <thead>
                                          <tr>
                                            <th>Invoice</th>
                                            <th>Constumer</th>
                                            <th>Pembayaran</th>
                                            <th>Order Status</th>
                                            <th>Actions</th>
                                          </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                          @foreach($allInvoiceData as $invoice)
                                              <tr>
                                                <td>
                                                  <div class="d-flex align-items-center">
                                                      @php
                                                          $imagePath = '';
                                                          if (Str::startsWith($invoice->invoice_number, 'S')) {
                                                              $imagePath = 'sales.png';
                                                          } elseif (Str::startsWith($invoice->invoice_number, 'P')) {
                                                              $imagePath = 'project.png';
                                                          }
                                                      @endphp
                                                      <img src="{{ asset('assets/img/icons/unicons/' . $imagePath) }}" alt="{{ $invoice->invoice_number }}" height="32" width="32" class="me-2" />
                                                      <div class="d-flex flex-column">
                                                          <span class="fw-medium lh-1">{{ $invoice->invoice_number }}</span>
                                                          <small class="text-muted">{{ $invoice->invoice_name }}</small>
                                                      </div>
                                                  </div>
                                                </td>
                                                <td>
                                                  <div class="d-flex align-items-center">
                                                      @php
                                                          $imagePath = '';
                                                          $customer = \App\Models\Customer::where('uuid', $invoice->customer_uuid)->first();
                                                          
                                                          if ($customer) {
                                                              if ($customer->customer_type == 'individual') {
                                                                  $imagePath = 'user.png';
                                                              } elseif ($customer->customer_type == 'biro') {
                                                                  $imagePath = 'biro.png';
                                                              } elseif ($customer->customer_type == 'instansi') {
                                                                  $imagePath = 'instansi.png';
                                                              }
                                                          }
                                                      @endphp
                                                      <img src="{{ asset('assets/img/icons/unicons/' . $imagePath) }}" alt="{{ $invoice->invoice_number }}" height="32" width="32" class="me-2" />
                                                      <div class="d-flex flex-column">
                                                          <span class="fw-medium lh-1">{{ $customer->name }}</span>
                                                          <small class="text-muted">{{ ucfirst($customer->customer_type) }}</small>
                                                      </div>
                                                  </div>
                                                </td>  
                                                <td>
                                                  <div class="text-muted lh-1">
                                                    <span class="text-primary fw-medium">Rp. {{ number_format($invoice->total_amount, 0) }}</span>/{{ number_format($invoice->panjar_amount, 0) }}
                                                  </div>
                                                  <small class="text-muted">
                                                    @if($invoice->status == 0)
                                                        Belum Bayar
                                                    @elseif($invoice->status == 1)
                                                        Panjar
                                                    @endif
                                                  </small>   
                                                </td>
                                                <td>
                                                  @if($invoice->status == 0)
                                                      <div class="progress">
                                                          <div class="progress-bar bg-danger" role="progressbar" style="width: 10%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                                      </div>
                                                  @elseif($invoice->status == 1)
                                                      @php
                                                          $percentage = ($invoice->panjar_amount / $invoice->total_amount) * 100;
                                                      @endphp
                                                      <div class="progress">
                                                          <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                              {{ number_format($percentage, 2) }}%
                                                          </div>
                                                      </div>
                                                  @elseif($invoice->status == 2)
                                                      <span class="badge badge-success">Lunas</span>
                                                      @php
                                                          $percentage = ($invoice->panjar_amount / $invoice->total_amount) * 100;
                                                      @endphp
                                                      <div class="progress">
                                                          <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                              Lunas
                                                          </div>
                                                      </div>
                                                  @endif
                                                </td>
                                              
                                                <td>
                                                  <div class="dropdown">
                                                      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                          <i class="bx bx-dots-vertical-rounded"></i>
                                                      </button>
                                                      <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ url('/invoice/add') }}?invoiceNumber={{ $invoice->invoice_number }}&customerUuid={{ $invoice->customer_uuid }}" target="_blank"><i class="bx bx-edit-alt me-1"></i> Lihat Invoice</a>
                                                      </div>
                                                  </div>
                                                </td>
                                              </tr>
                                          @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>                          
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
  
    

    <!-- Course datatable End -->
  </div>
  <!-- / Content -->
@endsection

@push('footer-script')  
<script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>  
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
<script>
    var sisaBefore      = {{$sisaBefore}};
    var pemasukan       = {{$incomeTotal}};
    var outcomeTotal    = {{$outcomeTotal}};
    var topup           = {{$topup}};
    var saldoKas        = {{$saldoKas}};
    var sisaKas         = {{$sisaBefore+$incomeTotal+$topup-$outcomeTotal-$saldoKas}};
    
    var countInvoice    = {{$countInvoice}};
    var countLuns       = {{$countLuns}};
    var countPanjar     = {{$countPanjar}};
    var countInvBB      = {{$countInvBB}};
</script>
    <script src="{{ asset('assets') }}/js/app-academy-dashboard.js"></script>
    <script>         
        @if(session('response'))
            var response = @json(session('response'));
            showSweetAlert(response);
        @endif
    </script>
@endpush
