@extends('Index/app-print')

@section('content')
    <div class="invoice-print p-5">
      <div class="d-flex justify-content-between flex-row">
        <div class="mb-4">
          <div class="d-flex svg-illustration mb-3 gap-2">
            <span class="app-brand-logo demo">
                <span class="app-brand-logo demo">
                    <img src="{{ asset('assets') }}/img/icons/brands/ami-logo.png" alt="Atjeh Mediatama Logo" width="35">
                </span>
                
            </span>
            <span class="app-brand-text demo text-body fw-bold">Aceh Mediatama Indonesia</span>
          </div>
          <p class="mb-1">Jl. Medan-B.Aceh, Mns. Mee Kandang, Kec. Muara Dua</p>
          <p class="mb-1">Lhokseumawe, Aceh, 24351, Indonesia</p>
          <p class="mb-0">+62 (811) 6856 6605</p>
        </div>
        <div>
          <h4>Invoice #{{$invoice->invoice_number}}</h4>
          <div class="mb-2">
            <span>Tanggal:</span>
            <span class="fw-medium">{{$formattedDate}}</span>
          </div>          
        </div>
      </div>

      <hr />

      <div class="row d-flex justify-content-between mb-4">
        <div class="col-sm-6 w-50">
          <h6>Invoice To:</h6>
          <p class="mb-1">{{$customer->name}}</p>
          <p class="mb-1">{{$customer->phone}}</p>
          <p class="mb-1">{{$customer->email}}</p>
          <p class="mb-1">{{$customer->address}}</p>
          
        </div>
        <div class="col-sm-6 w-50">
          <h6>Tagihan:</h6>
          <table>
            <tbody>
              <tr>
                <td class="pe-3">Total Tagihan:</td>
                <td class="fw-medium"><strong>Rp. {{number_format($invoice->total_amount)}},-</strong></td>
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

      <div class="table-responsive">
        <table class="table border-top m-0">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Satuan</th>
                    <th>Ukuran</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->deskripsi }}</td>
                        <td>Rp. {{ number_format($item->harga_satuan) }}</td>
                        <td>{{ $item->ukuran }} m<sup>2</sup></td>
                        <td>{{ $item->qty }}</td>
                        <td>Rp. {{ number_format($item->harga_satuan * $item->qty * $item->ukuran) }}</td>
                    </tr>
                @endforeach
            <tr>
              <td colspan="3" class="align-top px-4 py-3">
                {{-- <p class="mb-2">
                  <span class="me-1 fw-medium">Salesperson:</span>
                  <span>Alfie Solomons</span>
                </p> --}}
                <span>Terimakasih Sudah Berbelanja.!</span>
              </td>
              <td class="text-end px-4 py-3">
                <p class="mb-2">Subtotal:</p>
                <p class="mb-2">Discount:</p>
                <p class="mb-2">Pajak:</p>
                <p class="mb-2">Panjar:</p>
                <h4 class="mb-0">Sisa:</h4>
              </td>
              <td class="px-4 py-3">
                <p class="fw-medium mb-2">{{$subtotal}}</p>
                <p class="fw-medium mb-2">{{$discount}}</p>
                <p class="fw-medium mb-2">{{$tax}}</p>
                <p class="fw-medium mb-2">{{$panjar_amount}}</p>
                <h4 class="fw-medium mb-0">{{$total}}</h4>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="row">
        <div class="col-12">
          <span class="fw-medium">Note:</span>
          <span
            >{{$invoice->additional_notes}}</span
          >
        </div>
      </div>
    </div>
@endsection
