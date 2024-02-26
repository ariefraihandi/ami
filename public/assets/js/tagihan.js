'use strict';

$(document).ready(function () {
    var dt_customer_table = $('#dataTable');

    if (dt_customer_table.length) {
        var dt_customer = dt_customer_table.DataTable({
            ajax: {
                url: '/get-tagih',
                dataSrc: 'data'
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, full, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'Karyawan',
                    targets: 1,
                    responsivePriority: 1,
                    render: function (data, type, full, meta) {
                        var $name = full['nama_tagihan'],
                            $status = full['status'],
                            $image = full['image'];
                
                        var $output;
                
                        if ($image) {
                            $output = '<img src="' + assetsPath + 'img/staff/' + $image + '" alt="Avatar" class="rounded-circle">';
                        } else {
                            $output = '<span class="avatar-initial rounded-circle bg-primary">' + $name.charAt(0).toUpperCase() + '</span>';
                        }
                
                        var statusText;

                        if ($status === '1') {
                            statusText = 'Karyawan Tetap';
                        } else if ($status === '2') {
                            statusText = 'Karyawan Harian';
                        } else {
                            statusText = 'Tidak Aktif';
                        }
                
                        var $row_output =
                            '<div class="d-flex justify-content-start align-items-center customer-name">' +
                            '<div class="avatar-wrapper">' +
                            '<div class="avatar me-2">' +
                            $output +
                            '</div>' +
                            '</div>' +
                            '<div class="d-flex flex-column">' +
                            '<span class="fw-medium">' + $name + '</span>' +
                            '<span class="text-muted">' + statusText + '</span>' +
                            '</div>' +
                            '</div>';
                        return $row_output;
                    }
                },
                {
                    data: 'jabatan',
                    render: function (data, type, full, meta) {
                        return data.toLowerCase().replace(/\b\w/g, function (char) {
                            return char.toUpperCase();
                        });
                    }
                },                
                {
                    data: 'masa_kerja',
                    render: function (data, type, full, meta) {
                        var masaKerja = full['masa_kerja'];
                        var status = full['status'];
                        
                        if (status === '1' && masaKerja === '0') {
                            return 'Bulanan';
                        } else if (status === '2') {
                            return masaKerja + ' Hari';
                        } else {
                            return ''; // Atau teks lain jika diperlukan
                        }
                    }
                },    
                {
                    data: 'salary',
                    render: function (data, type, full, meta) {
                        // Mengonversi string angka menjadi number
                        var salaryNumber = parseFloat(data);
                
                        // Mengonversi number menjadi format mata uang Rp. X,-
                        var formattedSalary = 'Rp. ' + salaryNumber.toLocaleString('id-ID') + ',-';
                
                        return formattedSalary;
                    }
                },
                {
                    data: 'bonus',
                    render: function (data, type, full, meta) {
                        // Mengonversi string angka menjadi number
                        var bonusAmount = parseFloat(data);
                
                        // Jika bonus adalah 0, tampilkan 'Rp. 0,-'
                        if (bonusAmount === 0) {
                            return 'Rp. 0,-';
                        } else {
                            // Jika bonus tidak 0, tampilkan format mata uang dengan menggunakan toLocaleString()
                            return 'Rp. ' + bonusAmount.toLocaleString('id-ID') + ',-';
                        }
                    }
                },
                {
                    data: 'ambilan',
                    render: function (data, type, full, meta) {
                        // Mengonversi string angka menjadi number
                        var ambilanAmount = parseFloat(data);
                
                        // Jika bonus adalah 0, tampilkan 'Rp. 0,-'
                        if (ambilanAmount === 0) {
                            return 'Rp. 0,-';
                        } else {
                            // Jika bonus tidak 0, tampilkan format mata uang dengan menggunakan toLocaleString()
                            return 'Rp. ' + ambilanAmount.toLocaleString('id-ID') + ',-';
                        }
                    }
                },
                {
                    data: null,
                    targets: 7, // Sesuaikan dengan indeks kolom yang sesuai
                    render: function (data, type, full, meta) {
                        var salaryAmount = parseFloat(full['salary']);
                        var bonusAmount = parseFloat(full['bonus']);
                        var ambilanAmount = parseFloat(full['ambilan']);
                        var status = full['status'];
                        var masaKerja = parseFloat(full['masa_kerja']);
                
                        // Hitung gaji bersih
                        var netSalary = salaryAmount + bonusAmount - ambilanAmount;
                
                        // Jika status adalah 2, kalikan netSalary dengan masa_kerja
                        if (status === '2') {
                            netSalary *= masaKerja;
                        }
                
                        // Tampilkan gaji bersih dalam format mata uang Rupiah
                        return 'Rp. ' + netSalary.toLocaleString('id-ID') + ',-';
                    }
                },                
                {
                    data: null,
                    render: function (data, type, full, meta) {
                        var id = full.id;
                        return (
                            '<div class="d-flex align-items-center">' +
                            '<a href="#" class="text-body" data-bs-toggle="modal" data-bs-target="#editmasakerja' + id + '">' +
                            '<i class="bx bxs-message-square-edit mx-1"></i>' +
                            '</a>' +                           
                            '</a>' +
                            '</div>'
                        );
                    }
                }
            ],
            order: [[0, 'asc']],
            dom:
                '<"row mx-1"' +
                '<"col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start gap-3"l<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start mt-md-0 mt-3"B>>' +
                '<"col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row pe-3 gap-md-3"f>' + '>t' + '<"row mx-2"' + '<"col-sm-12 col-md-6"i>' + '<"col-sm-12 col-md-6"p>' + '>',

            language: {
                sLengthMenu: '_MENU_',
                search: '',
                searchPlaceholder: 'Search Users'
            },
            buttons: [
                {
                    text: '<i class="bx bx-money-withdraw bx-fade-up me-md-1"></i><span class="d-md-inline-block d-none">Bayar</span>',
                    className: 'btn btn-primary',
                    action: function (e, dt, button, config) {
                        $('#bayarGaji').modal('show');
                    }
                }
            ]
        });
    }
});

$(document).ready(function() {
    // Saat checkbox-tagihan diubah
    $('.tagihan-checkbox').change(function() {
        // Menghitung total jumlah gaji dari tagihan yang dipilih
        var totalGaji = 0;
        $('.tagihan-checkbox:checked').each(function() {
            var jumlahGajiText = $(this).closest('tr').find('td:nth-child(3)').text().trim(); // Ambil teks dari kolom Jumlah Gaji
            var jumlahGaji = parseInt(jumlahGajiText.replace(/\D/g, ''), 10); // Hapus karakter non-angka dan ubah ke tipe integer
            if (!isNaN(jumlahGaji)) {
                totalGaji += jumlahGaji;
            }
        });

        // Tampilkan total jumlah gaji
        $('#total-tagihan').text('Rp. ' + totalGaji.toLocaleString('id-ID') + ',-');
    });

    // Saat checkbox "Select All" diubah
    $('#select-all').change(function() {
        // Jika checkbox "Select All" dicentang
        if(this.checked) {
            // Tandai semua checkbox-tagihan
            $('.tagihan-checkbox').prop('checked', true);
        } else {
            // Hilangkan tanda centang dari semua checkbox-tagihan
            $('.tagihan-checkbox').prop('checked', false);
        }
        // Trigger event change pada checkbox-tagihan untuk menghitung kembali total jumlah gaji
        $('.tagihan-checkbox').change();
    });

    
  
    $('#bayarGajiForm').submit(function(event) {
        event.preventDefault(); // Mencegah pengiriman formulir default

        var selectedTagihanIds = []; // Array untuk menyimpan ID tagihan yang dipilih

        // Iterasi melalui checkbox-tagihan yang dicentang
        $('.tagihan-checkbox:checked').each(function() {
            var idTagih = $(this).data('id-tagih'); // Ambil nilai ID tagihan dari atribut data
            selectedTagihanIds.push(idTagih); // Tambahkan ID tagihan ke dalam array selectedTagihanIds
        });

        // Kirim data tagihan yang dipilih ke URL /get-tagih menggunakan AJAX
        // Kirim data tagihan yang dipilih ke URL /get-tagih menggunakan AJAX
        $.ajax({
            url: '/get-tagih',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                // Tangani respons dari server
                console.log(response.data); // Tampilkan data tagihan dari server dalam konsol

                // Iterasi melalui data tagihan dari respons
                $.each(response.data, function(index, tagihan) {
                    // Periksa apakah ID tagihan terdapat dalam daftar ID tagihan yang dipilih
                    if (selectedTagihanIds.includes(tagihan.id_tagih)) {
                        console.log("ID Tagihan:", tagihan.id_tagih);
                        console.log("Salary:", tagihan.salary);
                        console.log("Bonus:", tagihan.bonus);
                        console.log("Ambilan:", tagihan.ambilan);
                
                        // Konversi string menjadi tipe data numerik
                        var salary = parseFloat(tagihan.salary);
                        var bonus = parseFloat(tagihan.bonus);
                        var ambilan = parseFloat(tagihan.ambilan);
                
                        // Hitung total berdasarkan status
                        var total = 0;
                        if (tagihan.status === '1') {
                            total = salary + bonus - ambilan;
                            console.log("Total:", total);
                        } else if (tagihan.status === '2') {
                            total = (salary + bonus - ambilan) * tagihan.masa_kerja;
                            console.log("Total:", total);
                        }

                        var csrfToken = $('meta[name="csrf-token"]').attr('content');

                        // Kirim data id_tagih dan total ke controller menggunakan AJAX POST
                        $.ajax({
                            url: '/bayar-gaji',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken // Sertakan token CSRF dalam header
                            },
                            data: {
                                id_tagih: tagihan.id_tagih,
                                bonus: tagihan.bonus,
                                ambilan: tagihan.ambilan,
                                total: total
                            },
                            
                        });
                    }
                });
                
            },
            error: function(xhr, status, error) {
                // Tangani kesalahan jika terjadi
                console.error(error); // Tampilkan pesan kesalahan dalam konsol
            }
        });

    });
});


