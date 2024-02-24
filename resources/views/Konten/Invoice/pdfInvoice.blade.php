<!DOCTYPE html>
<html xmlns:v-on="http://www.w3.org/1999/xhtml"
      xmlns:v-bind="http://www.w3.org/1999/xhtml"
      xmlns:v-pre="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    text-align: center;
    font-family: Arial, sans-serif;
    font-size: 18px; /* Misalnya, ukuran font 18 piksel */
}
  
  .bg-image {
    position: absolute;
    top: 0;
    left: 0;
    /* width: 100%;
    height: 100%; */
    z-index: -1;
    opacity: 0.8;
  }
</style>
</head>
<body style="margin: 0;">  
    <img src="{{ $bgImage }}" alt="Background" class="bg-image" style="position: fixed; top: 0;  width: 110%;  left: -45px;">
    <div style="position: relative;">
    <img src="{{ $logoPath }}" alt="Header Image" style="position: fixed; top: 0; left: -45px; width: 115%; padding: 0;  top: -45px;">
    {{-- <div class="image-container" style="position: absolute; top: 45px;">
        <img src="{{ $imagePath }}" alt="Logo" height="70" style="position: fixed; top: 60;" class="logo-img">
    </div> --}}
    </div>
    <br>
    <br>  
    <br>
    <br>
    <h3>INVOICE</h3>
    <div class="table-responsive">
        <table id="dataTable" style="border: none; width: 100%;">
            <thead>
                <tr>                
                    <td rowspan="2" style="vertical-align: middle; text-align: center;">
                        <img src="{{ $logo }}" style="width: 50px; height: 50px;">                      
                    </td>                
                    <td rowspan="2" style="text-align: left;">
                        <span style="font-size: 20px; width: 100%;"><strong>Aceh Mediatama Indonesia</strong></span>
                    </td>
                    <td></td> <!-- Kolom kosong -->
                    <th style="text-align: left;">Invoice #{{$invoice->invoice_number}}</th>                
                </tr>
                <tr>
                    <td></td> <!-- Kolom kosong -->
                    <th style="text-align: left;">Tanggal : {{$formattedDate}}</th>                    
                </tr>
                <tr>
                    <td colspan="4">Jl. Medan-B.Aceh, Mns. Mee Kandang, Kec. Muara Dua<br>Lhokseumawe, Aceh, 24351, Indonesia<br>+62 (811) 6856 6605</td>
                <tr>
                <tr>
                    <td colspan="2" style="width: 50%; vertical-align: top;"> <!-- Bagian kiri untuk Invoice To -->
                        <strong>Invoice To:</strong><br>
                        ILHAM FIRDAUS SE<br>
                        0808080808<br>
                        ilham@gmail.com<br>
                        Lhokseumawe
                    </td>
                    <td colspan="2" style="width: 50%; vertical-align: top;"> <!-- Bagian kanan untuk Tagihan -->
                        <strong>Tagihan:</strong><br>
                        Total Tagihan: {{$subtotal}}<br>
                        Bank: Bank Syariah Indonesia (BSI)<br>
                        A.N: Dedy Maulana<br>
                        No Rek: 7222377848
                    </td>
                </tr>
            </thead>
        </table>        
    </div>
    <br>    
    <div>
        <table id="dataTable" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
            <thead style="background-color: #BA0000; color: #E8B014;">
                <tr>
                    <th style="border: 1px solid black; text-align: center; font-size: 14px; width: 5%;">NO</th>
                    <th style="border: 1px solid black; text-align: center; font-size: 14px; width: 30%;">DESKRIPSI</th>
                    <th style="border: 1px solid black; text-align: center; font-size: 14px; width: 15%;">SATUAN</th>
                    <th style="border: 1px solid black; text-align: center; font-size: 14px; width: 15%;">UKURAN</th>
                    <th style="border: 1px solid black; text-align: center; font-size: 14px; width: 10%;">JUMLAH</th>
                    <th style="border: 1px solid black; text-align: center; font-size: 14px; width: 25%;">SUBTOTAL</th>
                </tr>
            </thead>             
                </tr>
            </thead>            
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach($items as $item)
                    <tr>
                        <td style="border: 1px solid black; text-align: center; font-size: 12px;">{{ $no++ }}</td>
                        <td style="border: 1px solid black; font-size: 12px;">{{ $item->deskripsi }}</td>
                        <td style="border: 1px solid black; font-size: 12px;">Rp. {{ number_format($item->harga_satuan) }}</td>
                        <td style="border: 1px solid black; text-align: center; font-size: 12px;">{{ $item->ukuran }} m<sup>2</sup></td>
                        <td style="border: 1px solid black; text-align: center; font-size: 12px;">{{ $item->qty }}</td>
                        <td style="border: 1px solid black; font-size: 12px;">Rp. {{ number_format($item->harga_satuan * $item->qty * $item->ukuran) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot> <!-- Menghapus border pada footer -->
                <tr>
                    <td colspan="5" style="border: 1px solid black; text-align: right; font-size: 15px;">TOTAL:</td>
                    <td colspan="1" style="border: 1px solid black; background-color: #BA0000; color: #E8B014; font-size: 15px;">{{$subtotal}}</td>
                </tr>
                @if ($discount != 'Rp. 0,-')
                    <tr>
                        <td colspan="5" style="border: 1px solid black; text-align: right; font-size: 14px;">DISCOUNT:</td>
                        <td colspan="1" style="border: 1px solid black; background-color: #BA0000; color: #E8B014; font-size: 15px;">{{$discount}}</td>
                    </tr>
                @endif
                @if ($tax != '0%')
                    <tr>
                        <td colspan="5" style="border: 1px solid black; text-align: right; font-size: 14px;">PAJAK:</td>
                        <td colspan="1" style="border: 1px solid black; background-color: #BA0000; color: #E8B014; font-size: 15px;">{{$tax}}</td>
                    </tr>
                @endif                       
                @if ($panjar_amount != 'Rp. 0,-')
                    <tr>
                        <td colspan="5" style="border: 1px solid black; text-align: right; font-size: 14px;">SUDAH BAYAR:</td>
                        <td colspan="1" style="border: 1px solid black; background-color: #BA0000; color: #E8B014; font-size: 15px;">{{$panjar_amount}}</td>
                    </tr>
                @endif                                       
                <tr>
                    <td colspan="5" style="border: 1px solid black; text-align: right; font-size: 16px;">SISA:</td>
                    <td colspan="1" style="border: 1px solid black; background-color: #BA0000; color: #E8B014; font-size: 16px;">{{$total}}</td>
                </tr>
            </tfoot>

        </table>
    </div>
    
    
</body>
</html>