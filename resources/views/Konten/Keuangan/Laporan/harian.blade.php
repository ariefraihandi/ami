<!DOCTYPE html>
<html xmlns:v-on="http://www.w3.org/1999/xhtml"
      xmlns:v-bind="http://www.w3.org/1999/xhtml"
      xmlns:v-pre="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{$title}} {{$subtitle}}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('assets') }}/img/favicon/fav-icon.png" />
        <style>
            body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            /* border: 1px solid #ddd; */
            padding: 8px;
            text-align: left;
        }
        th {
            /* background-color: #f2f2f2; */
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
        h3 {
            /* text-align: center; */
            font-family: Arial, sans-serif;
            font-size: 18px;
        }
        ul {
            margin-top: -30px; /* Atur margin atas sesuai kebutuhan */
            margin-bottom: -5px; /* Atur margin bawah sesuai kebutuhan */
        }
        
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            /* width: 100%;
            height: 100%; */
            z-index: -1;
            opacity: 1;
        }
        </style>
    </head>
    <body style="margin: 0;">  
        <img src="{{ $coverLaporan }}" alt="Kop Surat" class="kop-surat-image" style="position: absolute; top: 0;  width: 114%; left: -45px; top: -45px;">                 
        <div style="position: absolute; top: 271; left: 240; transform: translateY(-50%); font-size: 70px; color: #ffd304; font-weight: bold; font-family: Helvetica; z-index: 999;">{{$jenis}}</div>
        <div style="position: absolute; top: 320; left: 43; transform: translateY(-50%); font-size: 45px; color: #ffd304; font-weight: bold; font-family: Helvetica; z-index: 999;">{{$dayName}} {{$tanggal}}</div>
        <div class="page-break"></div>
        <img src="{{ $kopSuratImage }}" alt="Kop Surat" class="kop-surat-image" style="position: absolute; top: -45px; left: -45px; width: 115%; z-index: -1;">
        <img src="{{ $bgImage }}" alt="Background" class="bg-image" style="position: fixed; top: 0;  width: 114%; left: -45px; top: -45px;">
        <br>
        <br>  
        <br>
        <br>  
        <br>
        <br>  
        <br>
        <br>  
        <h3>A. Invoice {{$dayName}} {{$tanggal}}</h3>
            <table border="1">
                <thead style="background-color: #BA0000; color: #E8B014; font-size: 11px; text-align: center;">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Invoice</th>
                        <th style="text-align: center;">Customer</th>
                        <th style="text-align: center;">Tanggal</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Total</th>
                        <th style="text-align: center;">Panjar</th>
                        <th style="text-align: center;">Sisa</th>
                    </tr>
                </thead>                
                <tbody style="font-size: 11px;">
                    @foreach($invoiceData as $index => $data)
                    <tr>
                        <td style="width: 5%; text-align: center;">{{ $index + 1 }}</td>
                        <td style="width: 25%;">
                            {{ $data->invoice_name }}<br>
                            <a href="{{ url('/print/' . $data->invoice_number) }}" target="_blank">{{ $data->invoice_number }}</a>
                        </td>
                        
                        @php                           
                            $customer = \App\Models\Customer::where('uuid', $data->customer_uuid)->first();
                        @endphp
                        <td style="width: 20%;">{{ ucwords($customer->name) }}<br>{{ ucwords($customer->customer_type) }}</td>
                        <td style="width: 10%;">{{ $data->created_at->format('d-m-y') }}</td>
                        <td style="width: 10%;">
                            @if($data->status == 0)
                                Belum Bayar
                            @elseif($data->status == 1)
                                Panjar
                            @elseif($data->status == 2)
                                Lunas
                            @endif
                        </td>
                        <td style="width: 10%; text-align: center;">
                            {{ number_format($data->total_amount, 0) }},-
                        </td>
                        <td style="width: 10%; text-align: center;">
                            @if($data->panjar_amount == 0.00)
                                -
                            @else
                                {{ number_format($data->panjar_amount, 0) }},-
                            @endif
                        </td>
                        <td style="width: 10%; text-align: center;">{{ number_format($data->total_amount-$data->panjar_amount, 0) }},-</td>              
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="font-size: 13px; background-color: #BA0000; color: #E8B014;">
                    <tr>
                        <td style="text-align: right;" colspan="6"><strong>Sisa Tagihan:</strong></td>                        
                        <td style="text-align: center;" colspan="2"><strong>{{ number_format($invoiceBon, 0) }},-</strong></td>                        
                    </tr>
                </tfoot>
            </table>
        <h3>B. Keuangan</h3>
        <ul>
            <li><h4>Pemasukan {{$dayName}} {{$tanggal}}</h4></li>
            <table border="1">
                <thead style="background-color: #BA0000; color: #E8B014; font-size: 11px; text-align: center;">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Transaksi</th>
                        <th style="text-align: center;">Customer</th>
                        <th style="text-align: center;">Deskripsi</th>
                        <th style="text-align: center;">Tagihan</th>
                        <th style="text-align: center;">Bayar</th>
                        <th style="text-align: center;">Sisa</th>
                        <th style="text-align: center;">Metode</th>
                    </tr>
                </thead> 
                <tbody style="font-size: 11px;">
                    @foreach($incomeData as $induk => $item)
                        <tr>
                            <td style="width: 5%; text-align: center;">{{ $induk + 1 }}</td>
                            <td style="width: 15%;">
                                {{ $item->invoice->invoice_name }}<br>
                                <a href="{{ url('/print/' . $item->invoice->invoice_number) }}" target="_blank">#{{ $item->invoice->invoice_number }}</a>
                            </td>
                            @php                           
                                $customer = \App\Models\Customer::where('uuid', $item->customer_uuid)->first();
                            @endphp
                            <td style="width: 20%;">{{ ucwords($customer->name) }}<br>{{ ucwords($customer->customer_type) }}</td>
                            <td style="width: 20%;">{{ ucwords($item->description) }}</td>
                            <td style="width: 10%; text-align: center;">{{ number_format($item->total_amount, 0) }},-</td>
                            <td style="width: 10%; text-align: center;">{{ number_format($item->transaction_amount, 0) }},-</td>
                            <td style="width: 10%; text-align: center;">{{ number_format($item->total_amount-$item->panjar_amount, 0) }},-</td>
                            <td style="width: 10%; text-align: center;">{{ $item->payment_method }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot style="font-size: 13px; background-color: #BA0000; color: #E8B014;">
                    <tr>
                        <td style="text-align: right;" colspan="5"><strong>Total Pemasukan:</strong></td>
                        <td style="text-align: center;" colspan="3"><strong>{{ number_format($sumIncome, 0) }},-</strong></td>                                               
                    </tr>
                </tfoot>
            </table>
            <li><h4>Pengeluaran {{$dayName}} {{$tanggal}}</h4></li>
            <table border="1">
                <thead style="background-color: #BA0000; color: #E8B014; font-size: 11px; text-align: center;">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Transaksi</th>
                        <th style="text-align: center;">Deskripsi</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th style="text-align: center;">Metode</th>
                    </tr>
                </thead> 
                <tbody style="font-size: 11px;">
                    @foreach($outcomeData as $m => $n)
                    <tr>
                        <td style="width: 5%; text-align: center;">{{ $m + 1 }}</td>
                        <td style="width: 20%; text-align: center;">{{ $n->source_receiver }}<br>#{{ $n->reference_number }}</td>
                        <td style="width: 25%; text-align: left;">{{ $n->description }}</td>
                        <td style="width: 20%; text-align: center;">{{ number_format($n->transaction_amount, 0) }},-</td>
                        <td style="width: 20%; text-align: center;">{{ $n->payment_method }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="font-size: 13px; background-color: #BA0000; color: #E8B014;">
                    <tr>
                        <td style="text-align: right;" colspan="3"><strong>Total Pengeluaran:</strong></td>
                        <td style="text-align: center;" colspan="2"><strong>{{ number_format($sumOutcome, 0) }},-</strong></td>                                               
                    </tr>
                </tfoot>
            </table>
            
            <li><h4>Top Up {{$dayName}} {{$tanggal}}</h4></li>
            <table border="1">
                <thead style="background-color: #BA0000; color: #E8B014; font-size: 11px; text-align: center;">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Transaksi</th>
                        <th style="text-align: center;">Deskripsi</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th style="text-align: center;">Metode</th>
                    </tr>
                </thead> 
                <tbody style="font-size: 11px;">
                    @foreach($topupData as $q => $o)
                    <tr>
                        <td style="width: 5%; text-align: center;">{{ $q + 1 }}</td>
                        <td style="width: 20%; text-align: center;">{{ $o->source_receiver }}<br>#{{ $o->reference_number }}</td>
                        <td style="width: 25%; text-align: left;">{{ $o->description }}</td>
                        <td style="width: 20%; text-align: center;">{{ number_format($o->transaction_amount, 0) }},-</td>
                        <td style="width: 20%; text-align: center;">{{ $o->payment_method }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="font-size: 13px; background-color: #BA0000; color: #E8B014;">
                    <tr>
                        <td style="text-align: right;" colspan="3"><strong>Total Top Up:</strong></td>
                        <td style="text-align: center;" colspan="2"><strong>{{ number_format($sumTopup, 0) }},-</strong></td>                                               
                    </tr>
                </tfoot>
            </table>
            <li><h4>Setor Kas {{$dayName}} {{$tanggal}}</h4></li>
            <table border="1">
                <thead style="background-color: #BA0000; color: #E8B014; font-size: 11px; text-align: center;">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Transaksi</th>
                        <th style="text-align: center;">Deskripsi</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th style="text-align: center;">Metode</th>
                    </tr>
                </thead> 
                <tbody style="font-size: 11px;">
                    @foreach($setorData as $q => $o)
                    <tr>
                        <td style="width: 5%; text-align: center;">{{ $q + 1 }}</td>
                        <td style="width: 20%; text-align: center;">{{ $o->source_receiver }}<br>#{{ $o->reference_number }}</td>
                        <td style="width: 25%; text-align: left;">{{ $o->description }}</td>
                        <td style="width: 20%; text-align: center;">{{ number_format($o->transaction_amount, 0) }},-</td>
                        <td style="width: 20%; text-align: center;">{{ $o->payment_method }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="font-size: 13px; background-color: #BA0000; color: #E8B014;">
                    <tr>
                        <td style="text-align: right;" colspan="3"><strong>Total Top Up:</strong></td>
                        <td style="text-align: center;" colspan="2"><strong>{{ number_format($sumSetor, 0) }},-</strong></td>                                               
                    </tr>
                </tfoot>
            </table>
        </ul>
        <h3>C. Summarize</h3>
        <table border="1">
            <thead style="background-color: #BA0000; color: #E8B014; font-size: 15px; text-align: center;">
                <tr>
                    <th style="text-align: center;" rowspan="2">Saldo Lampau</th>
                    <th style="text-align: center;" rowspan="2">Pemasukan</th>
                    <th style="text-align: center;" rowspan="2">Bon Invoice</th>
                    <th style="text-align: center;" colspan="2">Pengeluaran</th>
                    <th style="text-align: center;" rowspan="2">Setoran Kas</th>
                    <th style="text-align: center;" rowspan="2">Sisa Kas</th>
                </tr>
                <tr>
                    <th style="text-align: center;">Operational</th>
                    <th style="text-align: center;">Top Up</th>
                </tr>
            </thead>
                         
            <tbody style="font-size: 15px;">  
                <tr>
                    <td style="text-align: center;">{{ number_format($sisaBefore, 0) }},-</td>              
                    <td style="text-align: center;">{{ number_format($sumIncome, 0) }},-</td>              
                    <td style="text-align: center;">{{ number_format($invoiceBon, 0) }},-</td>              
                    <td style="text-align: center;">{{ number_format($sumOutcome-$sumTopup, 0) }},-</td>              
                    <td style="text-align: center;">{{ number_format($sumTopup, 0) }},-</td>              
                    <td style="text-align: center;">{{ number_format($sumSetor, 0) }},-</td>              
                    <td style="text-align: center;">{{ number_format($sisaBefore+$sumIncome+$sumTopup-$sumOutcome-$sumSetor, 0) }},-</td>                                                                            
                </tr>
            </tbody>
            {{-- <tfoot style="font-size: 13px; background-color: #BA0000; color: #E8B014;">
                <tr>
                    <td style="text-align: right;" colspan="6"><strong>Sisa Tagihan:</strong></td>                        
                    <td style="text-align: center;" colspan="2"><strong>{{ number_format($invoiceBon, 0) }},-</strong></td>                        
                </tr>
            </tfoot> --}}
        </table>
        <div style="position: absolute; top: 500px; left: 50px; font-size: 24px; color: #000; font-family: Helvetica; z-index: 999;">
       
          
        </div>
     
        <br>
        <br>  
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
       
        
        <br>    
       
    </body>
</html>