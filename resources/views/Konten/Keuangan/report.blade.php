<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Table</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .actions button {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                {{-- <img src="https://apps.atjehmediatama.co.id/assets/img/icons/brands/ami-logo.png" alt="AMI Logo"> --}}
            </td>
            <td style="text-align: center;">
                <h1 style="margin-bottom: 5px;">CV Atjeh Mediatama Indonesia</h1>
                <p style="margin-top: 5px;">Mns Mee, Muara Dua, Lhokseumawe, Aceh</p>
            </td>            
        </tr>
    </table>
    <h2 style="margin-bottom: 5px; text-align: center;">Laporan Harian</h2>
    <br>
    <h3 style="margin-bottom: 5px; text-align: left;">Invoice</h3>
    <div class="table-responsive">
        <table id="dataTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Jumlah Invoice</th>
                    <th>Belum Bayar</th>
                    <th>Panjar</th>
                    <th>Jatuh Tempo</th>
                    <th>Lunas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>john@example.com</td>
                    <td>Admin</td>
                    <td>john@example.com</td>
                    <td>Admin</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <h3 style="margin-bottom: 5px; text-align: left;">Transaksi</h3>
    <div class="table-responsive">
        <table id="dataTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Total Income</th>
                    <th>Total OutCome</th>
                    <th>Panjar</th>
                    <th>Jatuh Tempo</th>
                    <th>Lunas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>john@example.com</td>
                    <td>Admin</td>
                    <td>john@example.com</td>
                    <td>Admin</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
