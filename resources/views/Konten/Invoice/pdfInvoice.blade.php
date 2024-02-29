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
            opacity: 1;
        }
        </style>
    </head>
    <body style="margin: 0;">  
            <img src="{{ $kopSuratImage }}" alt="Kop Surat" class="kop-surat-image" style="position: absolute; top: -45px; left: -45px; width: 115%; z-index: -1;">
        <img src="{{ $bgImage }}" alt="Background" class="bg-image" style="position: fixed; top: 0;  width: 114%; left: -45px; top: -45px;">
        <div style="position: relative;">       
        </div>
        <br>
        <br>  
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <h3>INVOICE</h3>
        <div class="table-responsive">
            <table id="dataTable" style="border: none; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 30%;"></td> <!-- Kolom kosong -->
                        <td style="text-align: left; width: 20%;"></td>
                        <td style="text-align: left; width: 50%;"><strong>Invoice #{{$invoice->invoice_number}}<br>Tanggal : {{$formattedDate}}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="width: 100%;"></td> <!-- Kolom kosong untuk memberikan ruang -->
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%; vertical-align: top;"> <!-- Bagian kiri untuk Invoice To -->
                            <strong>Invoice To:</strong><br>
                            {{$customer->name}}<br>
                            {{$customer->phone}}<br>
                            {{$customer->email}}<br>
                            {{$customer->address}}
                        </td>
                        <td colspan="2" style="width: 50%; vertical-align: top;"> <!-- Bagian kanan untuk Tagihan -->
                            <strong>Tagihan:</strong><br>
                            Total Tagihan: {{$subtotal}}<br>
                            Bank: Bank Syariah Indonesia (BSI)<br>
                            A.N: Dedy Maulana<br>
                            No Rek: 7222377848
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <br>    
        <div>
            <table id="dataTable" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
                <thead style="background-color: #BA0000; color: #E8B014;">
                    <tr>
                        <th style="border: 1px solid black; text-align: center; font-size: 12px; width: 5%;">NO</th>
                        <th style="border: 1px solid black; text-align: center; font-size: 12px; width: 30%;">DESKRIPSI</th>
                        <th style="border: 1px solid black; text-align: center; font-size: 12px; width: 15%;">SATUAN</th>
                        <th style="border: 1px solid black; text-align: center; font-size: 12px; width: 15%;">UKURAN</th>
                        <th style="border: 1px solid black; text-align: center; font-size: 12px; width: 10%;">JUMLAH</th>
                        <th style="border: 1px solid black; text-align: center; font-size: 12px; width: 25%;">SUBTOTAL</th>
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
                <tfoot>
                    <tr>
                        <td colspan="5" style="border: 1px solid black; text-align: right; font-size: 15px;">TOTAL:</td>
                        <td colspan="1" style="border: 1px solid black; background-color: #BA0000; color: #ffffff; font-size: 15px;"><strong>{{$subtotal}}</strong></td>
                    </tr>
                    @if ($discount != 'Rp. 0,-')
                        <tr>
                            <td colspan="5" style="border: 1px solid black; text-align: right; font-size: 14px;">DISCOUNT:</td>
                            <td colspan="1" style="border: 1px solid black; background-color: #BA0000; color: #ffffff; font-size: 15px;"><strong>{{$discount}}</strong></td>
                        </tr>
                    @endif
                    @if ($tax != '0%')
                        <tr>
                            <td colspan="5" style="border: 1px solid black; text-align: right; font-size: 14px;">PAJAK:</td>
                            <td colspan="1" style="border: 1px solid black; background-color: #BA0000; color: #ffffff; font-size: 15px;"><strong>{{$tax}}</strong></td>
                        </tr>
                    @endif                       
                    @if ($panjar_amount != 'Rp. 0,-')
                        <tr>
                            <td colspan="5" style="border: 1px solid black; text-align: right; font-size: 14px;">SUDAH BAYAR:</td>
                            <td colspan="1" style="border: 1px solid black; background-color: #BA0000; color: #ffffff; font-size: 15px;"><strong>{{$panjar_amount}}</strong></td>
                        </tr>
                    @endif                                       
                    <tr>
                        <td colspan="5" style="border: 1px solid black; text-align: right; font-size: 16px;">SISA:</td>
                        <td colspan="1" style="border: 1px solid black; background-color: #BA0000; color: #ffffff; font-size: 16px;"><strong>{{$total}}</strong></td>
                    </tr>
                </tfoot>
                <tfoot>
                    <!-- Footer tabel di sini -->
                    <tr>
                        <td colspan="6" style="font-size: 12px; text-align: center;">
                            *Mohon diperhatikan, kami tidak menyediakan layanan tukar atau pengembalian. Kami berharap produk ini memenuhi harapan Anda. Selamat menikmati pembelian Anda!
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br>
        <div class="table-responsive">
            <table id="dataTable" style="border: none; width: 100%;">
                <thead>
                    <tr>
                        
                        <td style="width: 33.33%;"></td>
                        <td style="width: 33.33%;"></td>                       
                        <td style="width: 33.33%; font-size: 14px; text-align: center;">
                            Lhokseumawe<br><strong>Aceh Mediatama Indonesia</strong><br><br><br><br>
                            <span style="text-decoration: underline;">Nishra ilkhalissia</span><br>Kasir
                        </td> 
                    </tr>
                </thead>                         
            </table>
        </div>
    </body>
</html>