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
        <h3>A. Invoice</h3>
            <table border="1">
                <thead style="background-color: #BA0000; color: #E8B014; font-size: 12px;">
                    <tr>
                        <th>No</th>
                        <th>No. Invoice</th>
                        <th>Customer</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Panjar</th>
                        <th>Sisa</th>
                      
                    </tr>
                </thead>                
                <tbody style="font-size: 13px;">
                    @foreach($invoiceData as $index => $data)
                    <tr>
                        <td style="width: 5%;">{{ $index + 1 }}</td>
                        <td style="width: 25%;">
                            {{ $data->invoice_name }}<br>{{ $data->invoice_number }}
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
                        <td style="width: 10%;">
                            {{ number_format($data->total_amount, 0) }},-
                        </td>
                        <td style="width: 10%;">
                            @if($data->panjar_amount == 0.00)
                                -
                            @else
                                {{ number_format($data->panjar_amount, 0) }},-
                            @endif
                        </td>
                        <td style="width: 10%;">{{ number_format($data->total_amount-$data->panjar_amount, 0) }}</td>              
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="font-size: 13px; background-color: #BA0000; color: #E8B014;">
                    <tr>
                        <td colspan="5"></td>
                        <td>sa,-</td>
                        <td>{{ number_format($invoicePan, 0) }},-</td>
                        <td>sa,-</td>
                    </tr>
                </tfoot>
                
            </table>
        <h3>B. Keuangan</h3>
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