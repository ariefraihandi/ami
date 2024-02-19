@extends('Index/app')

@push('head-script')
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/typeahead-js/typeahead.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css" />
  <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row invoice-add">
      <div class="col-lg-9 col-12 mb-lg-0 mb-4">
        <div class="card invoice-preview-card">
          <div class="card-body">
            <div class="row p-sm-3 p-0">
              <div class="col-md-6 mb-md-0 mb-4">
                <div class="d-flex svg-illustration mb-4 gap-2">
                  <span class="app-brand-logo demo">
                    <img src="/assets/img/icons/brands/ami-logo.png" alt="AMI Fast Logo" width="40">
                </span>
                  <span class="app-brand-text demo text-body fw-bold">Atjeh Mediatama Indonesia</span>
                </div>
                <p class="mb-1">Jl. Medan-B.Aceh, Mns. Mee Kandang, Kec. Muara Dua</p>
                <p class="mb-1">Lhokseumawe, Aceh, 24351, Indonesia</p>
                <p class="mb-0">+62 (811) 6856 6605</p>
              </div>
              <div class="col-md-6">
                <dl class="row mb-2">
                    <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                        <span class="h4 text-capitalize mb-0 text-nowrap">Laporan</span>
                    </dt>
                    <dd class="col-sm-6 d-flex justify-content-md-end">
                      <div class="w-px-150">
                          <input type="text" class="form-control" disabled placeholder="{{$jenis}}" value="{{$jenis}}" id="jenis" />
                      </div>
                    </dd>
                  <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                    <span class="fw-normal">Dari:</span>
                  </dt>
                  <dd class="col-sm-6 d-flex justify-content-md-end">
                    <div class="w-px-150">
                        <input type="text" class="form-control" disabled placeholder="{{$startDate}}" value="{{$startDate}}" name="startDate" id="startDate" />
                    </div>
                  </dd>
                  
                  <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                    <span class="fw-normal">Sampai:</span>
                  </dt>
                  
                  <dd class="col-sm-6 d-flex justify-content-md-end">
                    <div class="w-px-150">
                      <input type="text" class="form-control" disabled id="endDate" name="endDate" value="{{$endDate}}" value="{{$endDate}}" />
                    </div>
                  </dd>                  
                </dl>
              </div>
            </div>     
            <hr class="my-4 mx-n4" />
            <h6 class="pb-2">Invoice:</h6>
            <div class="card mb-4">
              <div class="card-widget-separator-wrapper">
                  <div class="card-body card-widget-separator">
                      <div class="row gy-4 gy-sm-1">
                          <div class="col-sm-6 col-lg-3">
                              <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                                  <div>
                                      <h6 class="mb-2">Jumlah Invoices</h6>
                                      <h4 class="mb-2">{{ $totalInvoices }}</h4>                                      
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
                              <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                                  <div>
                                      <h6 class="mb-2">Belum Bayar</h6>
                                      <h4 class="mb-2">{{ $totalInvoicesBB }}</h4>
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
                                      <h6 class="mb-2">Panjar</h6>
                                      <h4 class="mb-2">{{ $totalInvoicesPJ }}</h4>                                     
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
                                      <h6 class="mb-2">Lunas</h6>
                                      <h4 class="mb-2">{{ $invoicesLN }}</h4>                                                                    
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
            <div class="row">
              <div class="col-xl-12">                  
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-bb" aria-controls="navs-top-bb" aria-selected="true">
                                Belum Bayar
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false">
                                Panjar
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-messages" aria-controls="navs-top-messages" aria-selected="false">
                                Lunas
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                      <div class="tab-pane fade show active" id="navs-top-bb" role="tabpanel">
                        <div class="card">                                  
                          <div class="table-responsive text-nowrap">
                            <table class="table">
                              <thead>
                                <tr>
                                  <th>No</th>
                                  <th>Invoice</th>
                                  <th>Custumer</th>                                              
                                  <th>Status</th>                              
                                  <th>Tagihan</th>
                                  <th>Sisa</th>
                                  <th>Aksi</th>
                                </tr>
                              </thead>
                              <tbody>
                                @if($invoicesBB->isEmpty())
                                  <tr>
                                    <td colspan="7" class="text-center">No Data Found</td>
                                  </tr>
                                @else
                                  @foreach ($invoicesBB as $invoice)
                                    <tr>
                                      <td>{{ $loop->iteration }}</td>
                                      <td>{{ $invoice->invoice_number }}<br>{{ $invoice->invoice_name }}</td>
                                      <td>{{ $invoice->customer_uuid }}</td>
                                      <td>
                                        @if ($invoice->panjar_amount == 0.00)
                                            <span class="badge bg-warning">Belum Bayar</span>
                                        @elseif ($invoice->panjar_amount >= $invoice->total_amount)
                                            <span class="badge bg-success">Lunas</span>
                                        @elseif (strtotime($invoice->due_date) < strtotime('today'))
                                            <span class="badge bg-danger">Jatuh Tempo</span>
                                        @else
                                            <span class="badge bg-info">Panjar</span>
                                        @endif
                                      </td>
                                      <td>Rp. {{ number_format($invoice->total_amount) }},-</td>
                                      <td>Rp. {{ number_format($invoice->total_amount - $invoice->panjar_amount) }},-</td>
                                      <td>
                                        <a href="{{ url('/invoice/add?invoiceNumber=' . $invoice->invoice_number . '&customerUuid=' . $invoice->customer_uuid) }}" target="_blank">
                                            <i class="bx bx-show-alt"></i>
                                        </a>
                                      </td>
                                    </tr>
                                  @endforeach
                                @endif
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
                        <div class="card">                             
                          <div class="table-responsive text-nowrap">
                              <table class="table">
                                <thead>
                                  <tr>
                                    <th>No</th>
                                    <th>Invoice</th>
                                    <th>Custumer</th>                                              
                                    <th>Status</th>                              
                                    <th>Tagihan</th>
                                    <th>Sisa</th>
                                    <th>Aksi</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @if($invoicesPJ->isEmpty())
                                    <tr>
                                      <td colspan="7" class="text-center">No Data Found</td>
                                    </tr>
                                  @else
                                    @foreach ($invoicesPJ as $invoice)
                                      <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $invoice->invoice_number }}<br>{{ $invoice->invoice_name }}</td>
                                        <td>{{ $invoice->customer_uuid }}</td>
                                        <td>
                                          @if ($invoice->panjar_amount == 0.00)
                                              <span class="badge bg-warning">Belum Bayar</span>
                                          @elseif ($invoice->panjar_amount >= $invoice->total_amount)
                                              <span class="badge bg-success">Lunas</span>
                                          @elseif (strtotime($invoice->due_date) < strtotime('today'))
                                              <span class="badge bg-danger">Jatuh Tempo</span>
                                          @else
                                              <span class="badge bg-info">Panjar</span>
                                          @endif
                                        </td>
                                        <td>Rp. {{ number_format($invoice->total_amount) }},-</td>
                                        <td>Rp. {{ number_format($invoice->total_amount - $invoice->panjar_amount) }},-</td>
                                        <td>
                                          <a href="{{ url('/invoice/add?invoiceNumber=' . $invoice->invoice_number . '&customerUuid=' . $invoice->customer_uuid) }}" target="_blank">
                                              <i class="bx bx-show-alt"></i>
                                          </a>
                                        </td>
                                      </tr>
                                    @endforeach
                                  @endif
                                </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="navs-top-messages" role="tabpanel">
                        <div class="card">                              
                          <div class="table-responsive text-nowrap">
                            <table class="table">
                              <thead>
                                <tr>
                                  <th>No</th>
                                  <th>Invoice</th>
                                  <th>Custumer</th>                                              
                                  <th>Status</th>                              
                                  <th>Tagihan</th>
                                  <th>Sisa</th>
                                  <th>Aksi</th>
                                </tr>
                              </thead>
                              <tbody>
                                @if($invoicesLUN->isEmpty())
                                  <tr>
                                    <td colspan="7" class="text-center">No Data Found</td>
                                  </tr>
                                @else
                                  @foreach ($invoicesLUN as $invoice)
                                    <tr>
                                      <td>{{ $loop->iteration }}</td>
                                      <td>{{ $invoice->invoice_number }}<br>{{ $invoice->invoice_name }}</td>
                                      <td>{{ $invoice->customer_uuid }}</td>
                                      <td>
                                        @if ($invoice->panjar_amount == 0.00)
                                            <span class="badge bg-warning">Belum Bayar</span>
                                        @elseif ($invoice->panjar_amount >= $invoice->total_amount)
                                            <span class="badge bg-success">Lunas</span>
                                        @elseif (strtotime($invoice->due_date) < strtotime('today'))
                                            <span class="badge bg-danger">Jatuh Tempo</span>
                                        @else
                                            <span class="badge bg-info">Panjar</span>
                                        @endif
                                      </td>
                                      <td>Rp. {{ number_format($invoice->total_amount) }},-</td>
                                      <td>Rp. {{ number_format($invoice->total_amount - $invoice->panjar_amount) }},-</td>
                                      <td>
                                        <a href="{{ url('/invoice/add?invoiceNumber=' . $invoice->invoice_number . '&customerUuid=' . $invoice->customer_uuid) }}" target="_blank">
                                            <i class="bx bx-show-alt"></i>
                                        </a>
                                      </td>
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
            <hr class="my-4 mx-n4" />
            <h6 class="pb-2">Keuangan:</h6>
            <div class="card mb-4">
              <div class="card-widget-separator-wrapper">
                  <div class="card-body card-widget-separator">
                      <div class="row gy-4 gy-sm-1">
                          <div class="col-sm-6 col-lg-3">
                              <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                                  <div>
                                      <h6 class="mb-2">Pemasukan</h6>
                                      <h4 class="mb-2">{{ $totalincome }}</h4>       
                                      <p class="mb-0"><span class="text-muted me-2">Rp. {{ number_format($incomeTotal), 0 }},-</span></p>                               
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
                                      <h6 class="mb-2">Pengeluaran</h6>
                                      <h4 class="mb-2">{{ $totaloutcome }}</h4>
                                      <p class="mb-0"><span class="text-muted me-2">Rp. {{ number_format($outcomeTotal), 0 }},-</span></p>                               
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
                                      <h6 class="mb-2">Margin</h6>
                                      <h4 class="mb-2">{{ number_format($incomeTotal - $outcomeTotal), 0 }},-</h4>  
                                      <p class="mb-0"><span class="text-muted me-2">Tagihan {{ number_format($totalTagih), 0 }},-</span></p>                                                                
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
                                      <h6 class="mb-2">Kas</h6>
                                      <h4 class="mb-2">{{ number_format($saldoKas - $topup), 0 }},-</h4>      
                                      <p class="mb-0"><span class="text-muted me-2">Sisa {{ number_format($incomeTotal - $outcomeTotal + $topup - $saldoKas), 0 }},-</span></p>                                                                
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
            <div class="row">
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
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-tagihan" aria-controls="navs-top-tagihan" aria-selected="false">
                                Tagihan
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
                                  <th>Tanggal</th>                                              
                                  <th>Jumlah</th>                              
                                  <th>Metode</th>
                                </tr>
                              </thead>
                              <tbody>
                                @if($income->isEmpty())
                                  <tr>
                                    <td colspan="7" class="text-center">No Data Found</td>
                                  </tr>
                                @else
                                  @foreach ($income as $item)
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
                                @if($outcome->isEmpty())
                                  <tr>
                                    <td colspan="7" class="text-center">No Data Found</td>
                                  </tr>
                                @else
                                  @foreach ($outcome as $item)
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
                      <div class="tab-pane fade" id="navs-top-tagihan" role="tabpanel">
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
                                @if($tagihan->isEmpty())
                                  <tr>
                                    <td colspan="7" class="text-center">No Data Found</td>
                                  </tr>
                                @else
                                  @foreach ($tagihan as $item)
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
                                @if($setorKas->isEmpty())
                                  <tr>
                                    <td colspan="7" class="text-center">No Data Found</td>
                                  </tr>
                                @else
                                  @foreach ($setorKas as $item)
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
                                @if($top->isEmpty())
                                  <tr>
                                    <td colspan="7" class="text-center">No Data Found</td>
                                  </tr>
                                @else
                                  @foreach ($top as $item)
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
          </div>
        </div>
      </div>
      <!-- /Invoice Add-->
      <!-- Transaksi -->
        <div class="col-lg-3 col-12 invoice-actions">
          <div class="card mb-4">
            <div class="card-body">
              <a href="https://apps.atjehmediatama.co.id/report?startDate={{ $startDate }}&endDate={{ $endDate }}" class="btn btn-primary d-grid w-100 mb-3" target="_blank" download>
                <span class="d-flex align-items-center justify-content-center text-nowrap">
                  <i class="bx bx-download bx-xs me-1"></i>
                  Download
                </span>
              </a>                              
              <a href="{{ route('invoice.list') }}" class="btn btn-warning d-grid w-100 mb-3">
                <span class="d-flex align-items-center justify-content-center text-nowrap">
                  <i class="bx bxs-left-arrow-circle bx-xs me-1"></i>
                  Back
                </span>
              </a>
              
            </div>
          </div>
          <div class="card">
            <h5 class="card-header">Riwayat Pembayaran</h5>
            <div class="card-body">
              <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="text-align: center;">Rp</th>
                      <th style="text-align: center;">Aksi</th>
                      <th style="text-align: center;">Status</th>
                    </tr>
                  </thead>
                  {{-- <tbody>
                      @forelse ($transactions as $transaction)
                          <tr>
                              <td>{{ number_format($transaction->transaction_amount) }}</td>
                              <td>
                                <a href="#" class="text-body" data-bs-toggle="modal" data-bs-target="#editTransactionModal{{$transaction->id}}">
                                  <i class="bx bxs-message-square-edit"></i>
                                </a>                            
                                <a href="#" class="text-body" onclick="confirmDelete('/delete/trans?id={{ $transaction->id }}', 'barang')">
                                  <i class="bx bx-trash"></i>
                                </a>                                           
                              </td>
                              <td>
                                <span class="badge bg-label-{{ ($transaction->status == 1) ? 'primary' : (($transaction->status == 2) ? 'info' : 'success') }} me-1">
                                    {{ ($transaction->status == 1) ? 'Panjar' : (($transaction->status == 2) ? 'Partial' : 'Lunas') }}
                                </span>
                              </td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="3" class="text-center">Belum Ada Transaksi</td>
                          </tr>
                      @endforelse
                  </tbody>
                </table>             --}}
              </div>
            </div>
          </div>
        </div>
      <!-- /Transaksi -->
    </div>
  </div>
   
  
 <!-- Modal metodebayar -->
{{-- @foreach($invoices as $data)
  <div class="modal fade" id="metodebayar{{ $data->invoice_number  }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Pilih Jenis Pembayaran</h3>
            <p class="text-muted">
              Pilih Metode Pembayaran Untuk Invoice No #{{ $data->invoice_number  }} 
            </p>
          </div>
          <div class="row">
            <div class="col-12 mb-3">
              <div class="form-check custom-option custom-option-basic">
                <label class="form-check-label custom-option-content ps-3" for="customRadioTemp1" data-bs-target="#cash{{ $data->invoice_number  }}" data-bs-toggle="modal">
                  <input name="customRadioTemp" class="form-check-input d-none" type="radio" value="" id="customRadioTemp1" />
                  <span class="d-flex align-items-start"><i class='bx bx-money bx-md me-3'></i>
                    <span>
                      <span class="custom-option-header">
                        <span class="h4 mb-2">Cash</span>
                      </span>
                      <span class="custom-option-body">
                        <span class="mb-0">
                          Metode pembayaran Cash
                        </span>
                      </span>
                    </span>
                  </span>
                </label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check custom-option custom-option-basic">
                <label class="form-check-label custom-option-content ps-3" for="customRadioTemp2" data-bs-target="#transfer{{ $data->invoice_number  }}" data-bs-toggle="modal">
                  <input name="customRadioTemp" class="form-check-input d-none" type="radio" value="" id="customRadioTemp2" />
                  <span class="d-flex align-items-start"> <i class='bx bx-credit-card bx-md me-3'></i>
                    <span>
                      <span class="custom-option-header">
                        <span class="h4 mb-2">Transfer</span>
                      </span>
                      <span class="custom-option-body">
                        <span class="mb-0">
                          Metode Pembayaran transfer
                          </span>
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
@endforeach --}}
<!--/ Modal Metodebayar -->

<!-- Modal Cash -->
{{-- @foreach($invoices as $data)
  <div class="modal fade" id="cash{{ $data->invoice_number }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-2">
          <h3 class="mb-0">Masukkan Nominal Pembayaran</h3>
        </div>
        <form id="cashForm{{ $data->invoice_number }}" action="{{ route('bayarInvoice') }}" method="POST">
          @csrf          
          <div class="mb-4">
            <label for="total_amount_display">Total Tagihan Invoice {{ $data->invoice_name }}:</label>
            @if ($data->invoice_panjar_amount != 0)
            <div class="input-group input-group-merge">
              <span class="input-group-text">Rp.</span>
              <input type="text" class="form-control" name="total_amount_display" id="total_amount_display{{ $data->invoice_number }}" value="{{ number_format($data->total_amount, 0, ',', '.') }}" readonly />
              <span class="input-group-text">.00</span>
            </div>                  
            @else
            <div class="input-group input-group-merge">
              <span class="input-group-text">Rp.</span>
              <input type="text" class="form-control" name="total_amount_display" id="total_amount_display{{ $data->invoice_number }}" value="{{ number_format($data->total_amount - $data->panjar_amount, 0, ',', '.') }}" readonly />
              <span class="input-group-text">.00</span>
            </div>
            @endif
          </div>
          <div class="mb-4">
            <label for="total_amount_input_cash{{ $data->invoice_number }}">Total Bayar:</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text">Rp.</span>
                <input type="text" class="form-control total-amount-input" oninput="formatCurrency(this, 'total_amount_input_cash{{ $data->invoice_number }}', {{ $data->total_amount }}, {{ $data->panjar_amount }}, 'sisa_cash_cash{{ $data->invoice_number }}'); updateSisa('total_amount_input_cash{{ $data->invoice_number }}', {{ $data->total_amount }}, {{ $data->panjar_amount }}, 'sisa_cash_cash{{ $data->invoice_number }}');" placeholder="100" name="total_amount_input" id="total_amount_input_cash{{ $data->invoice_number }}" value="" />
                <span class="input-group-text">.00</span>
            </div>
        </div>
        
        <div class="mb-4">
            <label for="sisa_cash_cash{{ $data->invoice_number }}">Sisa:</label>
            <input type="text" class="form-control" name="sisa" id="sisa_cash_cash{{ $data->invoice_number }}" value="Rp. 0" readonly />
        </div>
          <input type="hidden" class="form-control" name="methode" value="1" />
          <input type="hidden" class="form-control" name="invoice_number" value="{{ $data->invoice_number }}" />
          <input type="hidden" name="uuid" id="uuid" value="{{ $customerUuid }}">

          <div class="col-12 text-end">
            <button type="button" class="btn btn-label-secondary me-sm-3 me-2 px-3 px-sm-4" data-bs-toggle="modal" data-bs-target="#metodebayar{{ $data->invoice_number }}">
              <i class="bx bx-left-arrow-alt bx-xs me-1 scaleX-n1-rtl"></i>
              <span class="align-middle">Back</span>
            </button>
            <button type="submit" class="btn btn-success px-3 px-sm-4">
              <span class="align-middle">Bayar</span><i class="bx bx-money-withdraw bx-xs ms-1 scaleX-n1-rtl"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  </div>
@endforeach --}}
<!--/ Modal Cash -->

<!-- Modal Transfer -->
{{-- @foreach($invoices as $data)
  <div class="modal fade" id="transfer{{ $data->invoice_number }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-2">
          <h3 class="mb-0">Masukkan Nominal Transfer</h3>
        </div>
        <form id="transferForm{{ $data->invoice_number }}" action="{{ route('bayarInvoice') }}" method="POST">
          @csrf
          <div class="mb-4">
            <label for="total_amount_display">Total Tagihan Invoice {{ $data->invoice_name }}:</label>
            @if ($data->invoice_panjar_amount != 0)
            <div class="input-group input-group-merge">
              <span class="input-group-text">Rp.</span>
              <input type="text" class="form-control" name="total_amount_display" id="total_amount_display{{ $data->invoice_number }}" value="{{ number_format($data->total_amount, 0, ',', '.') }}" readonly />
              <span class="input-group-text">.00</span>
            </div>                  
            @else
            <div class="input-group input-group-merge">
              <span class="input-group-text">Rp.</span>
              <input type="text" class="form-control" name="total_amount_display" id="total_amount_display{{ $data->invoice_number }}" value="{{ number_format($data->total_amount - $data->panjar_amount, 0, ',', '.') }}" readonly />
              <span class="input-group-text">.00</span>
            </div>
            @endif
          </div>
          <div class="mb-4">
            <label for="total_amount_input_transfer{{ $data->invoice_number }}">Total Bayar:</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text">Rp.</span>
                <input type="text" class="form-control total-amount-input" oninput="formatCurrency(this, 'total_amount_input_transfer{{ $data->invoice_number }}', {{ $data->total_amount }}, {{ $data->panjar_amount }}, 'sisa_cash_transfer{{ $data->invoice_number }}'); updateSisa('total_amount_input_transfer{{ $data->invoice_number }}', {{ $data->total_amount }}, {{ $data->panjar_amount }}, 'sisa_cash_transfer{{ $data->invoice_number }}');" placeholder="100" name="total_amount_input" id="total_amount_input_transfer{{ $data->invoice_number }}" value="" />
                <span class="input-group-text">.00</span>
            </div>
        </div>
        
        <div class="mb-4">
            <label for="sisa_cash_transfer{{ $data->invoice_number }}">Sisa:</label>
            <input type="text" class="form-control" name="sisa" id="sisa_cash_transfer{{ $data->invoice_number }}" value="Rp. 0" readonly />
        </div>
          <input type="hidden" class="form-control" name="methode" value="2" />
          <input type="hidden" class="form-control" name="invoice_number" value="{{ $data->invoice_number }}" />
          <input type="hidden" name="uuid" id="uuid" value="{{ $customerUuid }}">

          <div class="col-12 text-end">
            <button type="button" class="btn btn-label-secondary me-sm-3 me-2 px-3 px-sm-4" data-bs-toggle="modal" data-bs-target="#metodebayar{{ $data->invoice_number }}">
              <i class="bx bx-left-arrow-alt bx-xs me-1 scaleX-n1-rtl"></i>
              <span class="align-middle">Back</span>
            </button>
            <button type="submit" class="btn btn-success px-3 px-sm-4">
              <span class="align-middle">Bayar</span><i class="bx bx-money-withdraw bx-xs ms-1 scaleX-n1-rtl"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  </div>
@endforeach --}}
<!--/ Modal Transfer -->

{{-- @foreach($transdetil as $item)
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
@endforeach --}}

<!-- Modal Edit Item -->
  {{-- <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Your form goes here -->
          <form class="source-item py-sm-3" id="editForm" action="{{ route('updateItem') }}" method="post">
            @csrf
            <div class="mb-3">
              <div class="d-flex border rounded position-relative pe-0">
                <div class="row w-100 m-0 p-3">
                  <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                    <p class="mb-2">Item</p>                   
                    <div class="mb-3"></div>
                    <input type="text" class="form-control item-name mb-2" name="kode_barang" id="kode_barang" placeholder="Kode Barang / Jasa">
                    <input type="text" class="form-control item-name mb-2" name="barang" id="barang" placeholder="Barang / Jasa">
                    <textarea class="form-control mb-2" name="deskripsi" id="deskripsi" rows="2" placeholder="Deskripsi"></textarea>
                    <div class="col-12">
                      <span>Ukuran:</span>
                      <div class="d-flex align-items-center">
                        <div class="input-group input-group-merge me-2">
                          <input type="text" class="form-control item-code" name="ukurana" id="ukurana" value="0" oninput="calculateTotal()">
                          <span class="input-group-text">cm</span>
                        </div>
                        <span class="me-2">x</span>
                        <div class="input-group input-group-merge">
                          <input type="text" class="form-control item-code" name="ukuranb" id="ukuranb" value="0" oninput="calculateTotal()">
                          <span class="input-group-text">cm</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 col-12 mb-md-0 mb-3">
                    <p class="mb-2">Cost</p>
                    <div class="input-group input-group-merge">
                      <span class="input-group-text">Rp</span>
                      <input type="text" class="form-control" name="harga_satuan" id="harga_satuan" placeholder="100" />
                      <span class="input-group-text">.00</span>
                    </div>
                    <div>
                      <span>Discount:</span>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" name="discount" id="discount">
                        <span class="input-group-text">.00</span>
                      </div>
                      <span>Pajak:</span>
                      <div class="input-group input-group-merge">
                        <input type="text" class="form-control" name="tax" id="tax" value="0" oninput="calculateTotal()">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3 col-12 mb-md-0 mb-3">
                    <p class="mb-2">Qty</p>
                    <input type="number" class="form-control item-code mb-2" name="qty" id="qty" placeholder="1" oninput="calculateTotal()" />
                  </div>  
                                
                  <input type="hidden" class="form-control item-name mb-2" name="id" id="id" value="">
                  <input type="hidden" name="invoice_id" id="invoice_id" value="{{$invoiceNumber}}">
                  <input type="hidden" name="uuid" id="uuid" value="{{$customerUuid }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <button type="submit" class="btn btn-success btn-save">Update Item</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div> --}}
<!-- //Modal Edit Item -->
  

@endsection

@push('footer-script')  
<script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
    <script src="{{ asset('assets') }}/js/laporan.js"></script>
    <script>
         var startDate = "{{ $startDate }}";
        var endDate = "{{ $endDate }}";
        @if(session('response'))
            var response = @json(session('response'));
            showSweetAlert(response);
        @endif
    </script>
@endpush
