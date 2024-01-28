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
                    <span class="h4 text-capitalize mb-0 text-nowrap">Invoice #</span>
                  </dt>
                  <dd class="col-sm-6 d-flex justify-content-md-end">
                      <div class="w-px-150">
                          <input type="text" class="form-control" disabled placeholder="{{$invoiceNumber}}" value="{{$invoiceNumber}}" id="invoiceId" data-invoice-number="{{$invoiceNumber}}" />
                      </div>
                  </dd>
                  <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                    <span class="fw-normal">Date:</span>
                  </dt>
                  <dd class="col-sm-6 d-flex justify-content-md-end">
                    <div class="w-px-150">
                      <input type="date" class="form-control" id="created_at" name="created_at" value="{{$created_at}}" placeholder="{{$created_at}}" />
                    </div>
                  </dd>
                  
                  <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                    <span class="fw-normal">Due Date:</span>
                  </dt>
                  
                  <dd class="col-sm-6 d-flex justify-content-md-end">
                    <div class="w-px-150">
                      <input type="date" class="form-control" id="due_date" name="due_date" value="{{$dueDate}}" placeholder="{{$dueDate}}" />
                    </div>
                  </dd>                  
                </dl>
              </div>
            </div>     
            <hr class="my-4 mx-n4" />
            <div class="row p-sm-3 p-0">
              <div class="col-md-6 col-sm-5 col-12 mb-sm-0 mb-4">
                <h6 class="pb-2">Invoice To:</h6>
                <p class="mb-1">
                  <?php
                  if ($customerData->customer_type == 'individual') {
                  } else {                     
                      echo ucfirst($customerData->customer_type) . ':';
                  }
                  ?>
                </p>
                <p class="mb-1">{{ $customerData->name }}</p>
                <p class="mb-0">{{ $customerData->email}}</p>
                <p class="mb-1">{{ $customerData->phone }}</p>
                <p class="mb-1">{{ $customerData->address}} - {{ $customerData->country}}</p>
              </div>
              <div class="col-md-6 col-sm-7">
                <h6 class="pb-2">Bill To:</h6>
                <table>
                  <tbody>
                    <tr>
                      <td class="pe-3">Tagihan:</td>
                      <td>{{$total_amount}}</td>
                    </tr>
                    <tr>
                      <td class="pe-3">Bank:</td>
                      <td>Bank Syariah Indonesia (BSI)</td>
                    </tr>
                    <tr>
                      <td class="pe-3">A.N:</td>
                      <td>Dedy Maulana</td>
                    </tr>
                    <tr>
                      <td class="pe-3">No Rek:</td>
                      <td>7222377848</td>
                    </tr>
                    
                  </tbody>
                </table>
              </div>
            </div>
            <hr class="mx-n4" />
            @if($itemInvoice->isNotEmpty())
            <div class="card">
              <div class="card-datatable table-responsive">
                  <table id="datatables-items" class="datatables-items table border-top">
                      <thead>
                          <tr>
                              <th>No</th>
                              <th>Kode</th>
                              <th>Nama</th>
                              <th>Deskripsi</th>                              
                              <th>Action</th>
                              <th>Ukuran</th>
                              <th>Jumlah</th>
                              <th>Harga Satuan</th>
                              <th>Discount</th>
                              <th>Pajak</th>
                          </tr>
                      </thead>
                  </table>
              </div>
            </div>
          
              <hr class="my-4 mx-n4" />
            @endif
            <form class="source-item py-sm-3" id="myForm" action="{{ route('addItemInvoice') }}" method="post">
              @csrf
              <div class="mb-3">
                <div class="d-flex border rounded position-relative pe-0">
                  <div class="row w-100 m-0 p-3">
                    <div class="col-md-5 col-12 mb-md-0 mb-3 ps-md-0">
                      <p class="mb-2">Item</p>
                      <select id="select2Product" class="select2 form-select" data-allow-clear="true">                        
                    </select>       
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
                    <div class="col-md-3 col-12 mb-md-0 mb-3">
                      <p class="mb-2">Cost</p>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" name="harga_satuan" id="harga_satuan" placeholder="100" oninput="formatCurrency(this, 'harga_satuan'); calculateTotal()" />
                        <span class="input-group-text">.00</span>
                      </div>
                      <div>
                        <span>Discount:</span>
                        <div class="input-group input-group-merge">
                          <span class="input-group-text">Rp</span>
                          <input type="text" class="form-control" name="discount" id="discount" value="0" oninput="formatCurrency(this, 'discount'); calculateTotal()">
                          <span class="input-group-text">.00</span>
                        </div>
                        <span>Pajak:</span>
                        <div class="input-group input-group-merge">
                          <input type="text" class="form-control" name="tax" id="tax" value="0" oninput="calculateTotal()">
                          <span class="input-group-text">%</span>
                        </div>
                      </div>
                    </div>
                    <input type="hidden" name="invoice_id" id="invoice_id" value="{{$invoiceNumber}}">
                    <input type="hidden" name="uuid" id="uuid" value="{{$customerUuid }}">
                    <div class="col-md-2 col-12 mb-md-0 mb-3">
                      <p class="mb-2">Qty</p>
                      <input type="number" class="form-control item-code mb-2" name="qty" id="qty" placeholder="1" oninput="calculateTotal()" />
                    </div>
                    <div class="col-md-2 col-12 pe-0">
                      <p class="mb-2">Total</p>
                      <p class="mb-0"><span id="total">0</span></p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <button type="submit" class="btn btn-success btn-save">Tambah Item</button>
                </div>
              </div>
            </form>
            
            <hr class="my-4" />
            <div class="row py-sm-3">
              <div class="col-md-6 mb-md-0 mb-3">
                <input
                  type="text"
                  class="form-control"
                  id="invoiceMsg"
                  value="Terima kasih atas pembelian Anda! " />
              </div>
              <div class="col-md-6 d-flex justify-content-end">
                <div class="invoice-calculations">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="w-px-100">Subtotal:</span>
                    <span class="fw-medium">{{$subtotal}}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="w-px-100">Discount:</span>
                    <span class="fw-medium">{{$discount}}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="w-px-100">Pajak:</span>
                    <span class="fw-medium">{{$tax}}</span>
                  </div>
                  @if ($panjar != 'Rp. 0.00')
                    <div class="d-flex justify-content-between mb-2">
                        <span class="w-px-100">Panjar:</span>
                        <span class="fw-medium">{{ $panjar }}</span>
                    </div>
                    <hr />
                    <div class="d-flex justify-content-between">
                      <span class="w-px-100">Sisa Tagihan:</span>
                      <span class="fw-medium">{{$total_amount}}</span>
                    </div>
                  @else
                  <hr />
                  <div class="d-flex justify-content-between">
                    <span class="w-px-100">Total Tagihan:</span>
                    <span class="fw-medium">{{$total_amount}}</span>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="mb-3">
                  <label for="note" class="form-label fw-medium">Note:</label>
                  <textarea class="form-control" rows="2" id="note" value="{{$note}}">{{$note}}</textarea>
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
              <button class="btn btn-primary d-grid w-100 mb-3" data-bs-toggle="modal" data-bs-target="#metodebayar{{$invoiceNumber}}">
                <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="bx bx-money bx-xs me-1"></i>
                  Bayar
                </span>
              </button>            
              <a href="/test" class="btn btn-label-secondary d-grid w-100 mb-3">Download</a>
              <a href="{{ route("deleteInvoice") }}?invoiceNumber={{$invoiceNumber}}" class="btn btn-label-danger d-grid w-100 mb-3" onclick="return confirm('Are you sure?')">
                <span class="d-flex align-items-center justify-content-center text-nowrap">
                    <i class="bx bx-trash bx-xs me-1"></i>
                    Hapus
                </span>
            </a>
              <a href="/invoice" class="btn btn-warning d-grid w-100 mb-3">
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
                          <th>Nominal</th>
                          <th>Status</th>
                      </tr>
                  </thead>
                  <tbody>
                      @forelse ($transactions as $transaction)
                          <tr>
                              <td>{{ 'Rp. ' . number_format($transaction->transaction_amount) }}</td>
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
                </table>            
              </div>
            </div>
          </div>
        </div>
      <!-- /Transaksi -->
    </div>
  </div>
   
  
 

  <!-- Modal Edit Invoice -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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
                  <div class="col-md-5 col-12 mb-md-0 mb-3 ps-md-0">
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
                      <input type="text" class="form-control" name="harga_satuan" id="harga_satuan" placeholder="100" oninput="formatCurrency(this, 'harga_satuan'); calculateTotal()" />
                      <span class="input-group-text">.00</span>
                    </div>
                    <div>
                      <span>Discount:</span>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" name="discount" id="discount" value="0" oninput="formatCurrency(this, 'discount'); calculateTotal()">
                        <span class="input-group-text">.00</span>
                      </div>
                      <span>Pajak:</span>
                      <div class="input-group input-group-merge">
                        <input type="text" class="form-control" name="tax" id="tax" value="0" oninput="calculateTotal()">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                  </div>
                  <input type="hidden" name="invoice_id" id="invoice_id" value="{{$invoiceNumber}}">
                  <input type="hidden" name="uuid" id="uuid" value="{{$customerUuid }}">
                  <div class="col-md-3 col-12 mb-md-0 mb-3">
                    <p class="mb-2">Qty</p>
                    <input type="number" class="form-control item-code mb-2" name="qty" id="qty" placeholder="1" oninput="calculateTotal()" />
                  </div>                 
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
  </div>
  

@endsection

@push('footer-script')  
<script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('footer-Sec-script')
    <script src="{{ asset('assets') }}/js/app-invoiceitem-add.js"></script>
    <script>
        var invoiceNumber = '{{ $invoiceNumber }}';
        @if(session('response'))
            var response = @json(session('response'));
            showSweetAlert(response);
        @endif
    </script>
@endpush
