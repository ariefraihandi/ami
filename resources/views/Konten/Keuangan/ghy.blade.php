<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <!-- CSS styles for the PDF -->
    <style>
        /* Define your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .page {
            width: 210mm;
            height: 297mm;
            padding: 0;
            margin: 0 auto;
            background-color: white;
        }
        .header {
            background-image: url('assets/img/report/kop.png');
            background-size: cover;
            height: 100px; /* Sesuaikan tinggi gambar kop */
            text-align: center;
            color: white;
        }
        .content {
            padding: 20mm;
            /* Add your content styles here */
        }

        @media print {
            /* CSS for printing */
            .header {
                position: fixed; /* Menetapkan posisi header */
                top: 0;
                left: 0;
                right: 0;
                z-index: -1; /* Memastikan header muncul di belakang konten */
            }
            .content {
                padding-top: 120px; /* Menyesuaikan padding agar konten tidak tumpang tindih dengan header */
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <!-- You can add any additional content or styling for the header here -->
            <!-- This is where the background image will be displayed -->
        </div>
        <div class="content">
            <!-- Your content goes here -->
            <h1>Laporan Keuangan</h1>
            <!-- Add more content here as needed -->
        </div>
    </div>
</body>
</html>
