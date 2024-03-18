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
        td[rowspan] {
    page-break-inside: avoid;
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
        <div style="position: absolute; top: 320; left: 43; transform: translateY(-50%); font-size: 45px; color: #ffd304; font-weight: bold; font-family: Helvetica; z-index: 999;">{{$tanggal}}</div>
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
        <h3>A. Invoice {{$tanggal}}</h3>
        <table border="1">
            <thead style="background-color: #BA0000; color: #E8B014; font-size: 11px; text-align: center;">
                <tr>
                    <th style="text-align: center; width: 15%;">Tanggal</th>
                    <th style="text-align: center; width: 25%;">Invoice</th>
                    <th style="text-align: center; width: 20%;">Customer</th>
                    <th style="text-align: center; width: 10%;">Status</th>
                    <th style="text-align: center; width: 10%;">Total</th>
                    <th style="text-align: center; width: 10%;">Panjar</th>
                    <th style="text-align: center; width: 10%;">Sisa</th>
                </tr>
            </thead>                
            <tbody style="font-size: 11px;">
                @foreach($dates as $date)
                    @php
                        $invoicesOnDate = $date['invoiceData']; // Mengambil data invoice untuk tanggal saat ini
                        $rowCount = count($invoicesOnDate);
                    @endphp
                    <tr>
                        @if($rowCount > 0)
                            @foreach($invoicesOnDate as $index => $data)
                                @if($index == 0)
                                    <td rowspan="{{ $rowCount }}" style="width: 15%; text-align: center;">
                                        <a href="{{ url('/report/?startDate=' . $date['start']->format('Y-m-d') . '&endDate=' . $date['end']->format('Y-m-d')) }}">
                                            {{ $date['start']->format('d-m-Y') }}
                                        </a>
                                    </td>
                                @endif
                                <td style="width: 25%;">
                                    {{ $data->invoice_name }}<br>
                                    <a href="{{ url('/print/' . $data->invoice_number ) }}" target="_blank">#{{ $data->invoice_number  }}</a>
                                </td>
                                <td style="width: 20%;">
                                    @php                           
                                        $customer = \App\Models\Customer::where('uuid', $data->customer_uuid)->first();
                                    @endphp
                                    {{ ucwords($customer->name) }}<br>{{ ucwords($customer->customer_type) }}
                                </td>
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
                                <td style="width: 10%; text-align: center;">
                                    {{ number_format($data->total_amount - $data->panjar_amount, 0) }},-
                                </td>              
                            </tr>
                            @if($index < $rowCount - 1)
                                <tr>
                            @endif
                            @endforeach
                        @else
                            <td rowspan="1" style="width: 15%; text-align: center;">
                                <a href="{{ url('/report/?startDate=' . $date['start']->format('Y-m-d') . '&endDate=' . $date['end']->format('Y-m-d')) }}">
                                    {{ $date['start']->format('d-m-Y') }}
                                </a>
                            </td>
                            <td colspan="6" style="text-align: center; background-color: #E8B014; color: #000000;">Tidak ada invoice/Libur</td>
                        @endif
                @endforeach
            </tbody>
            
            <tfoot style="font-size: 13px; background-color: #BA0000; color: #E8B014;">
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>Sisa Tagihan:</strong></td>                        
                    <td colspan="2" style="text-align: center;"><strong>{{ number_format($invoiceBon, 0) }},-</strong></td>                        
                </tr>
            </tfoot>
        </table>
        
        <br>
        <h3>B. Keuangan {{$tanggal}}</h3>
        <table id="dataTable" style="border-collapse: collapse; width: 100%; border: 1px solid black;">          
            <thead style="background-color: #BA0000; color: #E8B014; font-size: 11px; text-align: center;">
                <tr>
                    <th style="border: 1px solid black; width: 16%;">Tanggal</th>
                    <th style="border: 1px solid black; width: 14%;">Saldo Lampau</th>
                    <th style="border: 1px solid black; width: 14%;">Income</th>
                    <th style="border: 1px solid black; width: 14%;">Top Up</th>
                    <th style="border: 1px solid black; width: 14%;">Outcome</th>
                    <th style="border: 1px solid black; width: 14%;">Setor Kas</th>
                    <th style="border: 1px solid black; width: 14%;">Sisa Kas</th>
                </tr>
            </thead>
            <tbody style="font-size: 11px;">
                @foreach($keuangan as $m)
                <tr>
                    <td style="border: 1px solid black; text-align: center;">
                        <a href="{{ url('/report/?startDate=' . $m['start']->format('Y-m-d') . '&endDate=' . $m['end']->format('Y-m-d')) }}">
                            {{ $m['start']->format('d-m-Y') }}
                        </a>
                    </td>    
                    @if ($m['income'] == 0 && $m['topup'] == 0 && $m['outcome'] == 0 && $m['setorKas'] == 0)
                        <td colspan="6" style="text-align: center; border: 1px solid black; background-color: #E8B014; color: #000000;">Tidak Ada Data / Libur</td>
                    @else
                        <td style="border: 1px solid black; text-align: center;">{{ number_format($m['saldoLampau'], 0) }},-</td>
                        <td style="border: 1px solid black; text-align: center;">{{ number_format($m['income'], 0) }},-</td>
                        <td style="border: 1px solid black; text-align: center;">{{ number_format($m['topup'], 0) }},-</td>
                        <td style="border: 1px solid black; text-align: center;">{{ number_format($m['outcome'], 0) }},-</td>
                        <td style="border: 1px solid black; text-align: center;">{{ number_format($m['setorKas'], 0) }},-</td>
                        <td style="border: 1px solid black; text-align: center;">{{ number_format($m['sisakas'], 0) }},-</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-size: 13px; background-color: #BA0000; color: #E8B014;">
                <tr>
                    <td rowspan="2" style="border: 2px solid black; text-align: right;"><strong>Summarize:</strong></td>    
                    <td style="border: 2px solid black; width: 14%; text-align: center;">Sisa Kas {{$lasrMonth}}</td>
                    <td style="border: 2px solid black; width: 14%; text-align: center;">Income</td>
                    <td style="border: 2px solid black; width: 14%; text-align: center;">Top Up</td>
                    <td style="border: 2px solid black; width: 14%; text-align: center;">Outcome</td>
                    <td style="border: 2px solid black; width: 14%; text-align: center;">Setor Kas</td>
                    <td style="border: 2px solid black; width: 14%; text-align: center;">Sisa Kas</td>
                </tr>
                <tr>                    
                    <td style="border: 2px solid black; text-align: center;"><strong>{{ number_format($sisaBefore, 0) }},-</strong></td>                        
                    <td style="border: 2px solid black; text-align: center;"><strong>{{ number_format($sumIncome, 0) }},-</strong></td>                        
                    <td style="border: 2px solid black; text-align: center;"><strong>{{ number_format($sumTopup, 0) }},-</strong></td>                                                                
                    <td style="border: 2px solid black; text-align: center;"><strong>{{ number_format($sumOutcome, 0) }},-</strong></td>                                                                
                    <td style="border: 2px solid black; text-align: center;"><strong>{{ number_format($sumSetor, 0) }},-</strong></td>                                                                
                    <td style="border: 2px solid black; text-align: center;"><strong>{{ number_format($sisaBefore+$sumIncome+$sumTopup-$sumOutcome-$sumSetor, 0) }},-</strong></td>                                                                
                </tr>
            </tfoot>
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