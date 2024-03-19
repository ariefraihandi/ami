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
        <h3>A. Laporan Keuangan {{$tanggal}}</h3>
        <table style="border-collapse: collapse; border: 1px solid black;">
            <thead style="background-color: #BA0000; color: #E8B014; font-size: 13px; text-align: center;">
                <tr>
                    <th style="text-align: center; border: 1px solid black;">No</th>
                    <th style="text-align: center; border: 1px solid black;">Bulan</th>
                    <th style="text-align: center; border: 1px solid black;">Pendapatan</th>
                    <th style="text-align: center; border: 1px solid black;">Pengeluaran</th>
                    <th style="text-align: center; border: 1px solid black;">Margin</th>
                    <th style="text-align: center; border: 1px solid black;">%</th>
                </tr>
            </thead>
            <tbody style="font-size: 15px;">
                @foreach($dataTahunan as $index => $data)
                <tr>
                    <td style="text-align: center; border: 1px solid black;">{{ $index + 1 }}</td>
                    <td style="text-align: center; border: 1px solid black;">
                        <a href="{{ url('/report/?startDate=' . \Carbon\Carbon::parse($data->tanggal_awal)->format('Y-m-d') . '&endDate=' . \Carbon\Carbon::parse($data->tanggal_akhir)->format('Y-m-d')) }}">
                            {{ $data->bulan }}
                        </a>
                    </td>
                    
                    
                    <td style="border: 1px solid black;">Rp.{{ number_format($data->pendapatan, 0, ',', '.') }},-</td>
                    <td style="border: 1px solid black;">Rp.{{ number_format($data->pengeluaran, 0, ',', '.') }},-</td>
                    <td style="border: 1px solid black; color: {{ $data->margin < 0 ? 'red' : 'green' }}">
                        Rp.{{ number_format($data->margin, 0, ',', '.') }},-
                    </td>
                    <td style="text-align: center; border: 1px solid black; color: {{ $data->persentase < 0 ? 'red' : 'green' }}">
                        {{ $data->persentase }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="font-size: 15px; background-color: #BA0000; color: #E8B014;">
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold; border: 1px solid black;">Summarize:</td>
                    <td style="font-weight: bold; border: 1px solid black;">Rp.{{ number_format($totalIncome, 0, ',', '.') }},-</td>
                    <td style="font-weight: bold; border: 1px solid black;">Rp.{{ number_format($totalOutcome, 0, ',', '.') }},-</td>
                    <td style="font-weight: bold; border: 1px solid black;">Rp.{{ number_format($totalMargin, 0, ',', '.') }},-</td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid black;">{{ $totalPesen }}%</td>
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