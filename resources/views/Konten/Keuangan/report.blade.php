<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title}}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .page-break {
          page-break-after: always;
        }
        .logo-img {
          float: left; /* Mengatur gambar agar rata kiri */
          margin-right: 10px; /* Memberi jarak kanan antara gambar dan teks */
          /* box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.4); Menambahkan efek bayangan */
        }

      .header-table {
        width: 100%;
        margin-bottom: 10px; /* Tambahkan margin bawah untuk memberikan ruang di antara elemen */
      }
      .image-container {
        margin-bottom: 10px; 
      }
      h3 {
        margin-top: 50px; 
        margin-bottom: 5px;
        text-align: left;
      }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
              <img src="{{ $logoPath }}" alt="Logo" width="80" height="80">
            </td>
            <td style="text-align: center;">
                <h1 style="margin-bottom: 5px;">CV Atjeh Mediatama Indonesia</h1>
                <p style="margin-top: 5px;">Mns Mee, Muara Dua, Lhokseumawe, Aceh</p>
            </td>            
        </tr>imagePath
    </table>
    <div class="image-container">
      <img src="{{ $imagePath }}" alt="Logo" height="70" class="logo-img">
    </div>
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
    </div>
</body>
</html>
