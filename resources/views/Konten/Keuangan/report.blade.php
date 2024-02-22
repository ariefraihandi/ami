<!DOCTYPE html>
<html xmlns:v-on="http://www.w3.org/1999/xhtml"
      xmlns:v-bind="http://www.w3.org/1999/xhtml"
      xmlns:v-pre="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  table 
  {
    width: 100%;
    border-collapse: collapse;
  }
  th, td 
  {
    padding: 8px;
    border-bottom: 1px solid #ddd;
    text-align: center;
  }
  th
  {
    background-color: #f2f2f2;
  }
  tr:hover 
  {
    background-color: #f5f5f5;
  }
  .page-break {
    page-break-after: always;
  }
  .image-container {
    margin-top: 45px;
    margin-bottom: 10px;
  }
  body {
    position: relative;
  }
  h3 
  {
    margin-top: 50px; 
    margin-bottom: 5px;
    text-align: left;
  }
  
  .bg-image {
    position: absolute;
    top: 0;
    left: 0;
    /* width: 100%;
    height: 100%; */
    z-index: -1;
    opacity: 0.3;
  }
</style>
</head>
<body style="margin: 0;">  
  <img src="{{ $bgImage }}" alt="Background" class="bg-image" style="position: fixed; top: 0;  width: 115%;  left: -45px;">
  <div style="position: relative;">
    <img src="{{ $logoPath }}" alt="Header Image" style="position: fixed; top: 0; left: -45px; width: 115%; padding: 0;  top: -45px;">
    <div class="image-container" style="position: absolute; top: 45px;">
      <img src="{{ $imagePath }}" alt="Logo" height="70" style="position: fixed; top: 60;" class="logo-img">
    </div>
  </div>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
    <h3>Invoice:</h3>
    <div class="table-responsive">
        <table id="dataTable">
            <thead>
                <tr>                
                    <th>Jumlah Invoice</th>
                    <th>Belum Bayar</th>
                    <th>Panjar</th>                
                    <th>Lunas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$totalInvoices}}</td>
                    <td>{{$totalInvoicesBB}}</td>
                    <td>{{$totalInvoicesPJ}}</td>
                    <td>{{$invoicesLN}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="table-responsive">
        <table id="dataTable">
            <caption>Invoice Belum Bayar</caption>
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
    <br>
    <div class="table-responsive">
        <table id="dataTable">
            <caption>Invoice Sudah Panjar</caption>
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
    <br>
    <div class="table-responsive">
        <table id="dataTable">
            <caption>Invoice Lunas</caption>
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
    <div class="page-break"></div>
    <h3 style="margin-bottom: 5px; text-align: left;">Transaksi</h3>
    <div class="table-responsive">
        <table id="dataTable">
            <thead>
                <tr>                  
                    <th>Pemasukan</th>
                    <th>Pengeluaran</th>
                    <th>Margin</th>
                    <th>Tagihan</th>
                    <th>Kas</th>
                    <th>Sisa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $totalincome }}<br>Rp. {{ number_format($incomeTotal), 0 }},-</td>
                    <td>{{ $totaloutcome }}<br>Rp. {{ number_format($outcomeTotal), 0 }},-</td>
                    <td>Rp. {{ number_format($incomeTotal - $outcomeTotal), 0 }},-</td>
                    <td>Rp. {{ number_format($totalTagih), 0 }},-</td>
                    <td>Rp. {{ number_format($saldoKas - $topup), 0 }},-</td>
                    <td>Rp. {{ number_format($incomeTotal - $outcomeTotal + $topup - $saldoKas), 0 }},-</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="table-responsive">
        <table id="dataTable">
            <caption>Data Pemasukan</caption>
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
    <br>
    <div class="table-responsive">
        <table id="dataTable">
            <caption>Data Pengeluaran</caption>
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
    <br>
    <div class="table-responsive">
        <table id="dataTable">
            <caption>Data Tagihan</caption>
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
                  @foreach ($outagihantcome as $item)
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
    <br>
    <div class="table-responsive">
        <table id="dataTable">
            <caption>Data Setoran Kas</caption>
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
    <br>
    <div class="table-responsive">
        <table id="dataTable">
            <caption>Data Top Up</caption>
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
</body>
</html>