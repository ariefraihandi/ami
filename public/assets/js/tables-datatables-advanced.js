'use strict';

$(function () {
    var dt_adv_filter_table = $('.dt-advanced-search');
    var startDateEle = $('.start_date');
    var endDateEle = $('.end_date');
    var referenceInput = $('.reference-number');

    // Datepicker for advanced filter
    var rangePickr = $('.flatpickr-range');
    var dateFormat = 'YYYY/MM/DD';

    if (rangePickr.length) {
        rangePickr.flatpickr({
            mode: 'range',
            dateFormat: 'Y-m-d',
            onClose: function (selectedDates) {
                var startDate = '';
                var endDate = '';
                if (selectedDates.length > 1) {
                    startDate = moment(selectedDates[0]).format('YYYY/MM/DD');
                    endDate = moment(selectedDates[1]).format('YYYY/MM/DD');
                }
                startDateEle.val(startDate);
                endDateEle.val(endDate);
                filterTableByDate();
            }
        });
    }

    // Function to filter table by date range
    function filterTableByDate() {
        var startDate = startDateEle.val();
        var endDate = endDateEle.val();
        dt_adv_filter_table.DataTable().draw();
    }

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var startDate = moment(startDateEle.val(), 'YYYY/MM/DD');
        var endDate = moment(endDateEle.val(), 'YYYY/MM/DD');
        var currentDate = moment(data[5], 'YYYY/MM/DD')
        if (startDate.isValid() && endDate.isValid()) {
            return currentDate.isBetween(startDate, endDate, null, '[]');
        }
        return true;
    });


    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var referenceValue = referenceInput.val().trim().toUpperCase();

        // Lakukan pencarian di semua kolom
        for (var i = 0; i < data.length; i++) {
            var columnData = data[i].toUpperCase();
            if (columnData.includes(referenceValue)) {
                return true; // Jika nilai cocok ditemukan, kembalikan true
            }
        }

        // Jika tidak ada nilai cocok ditemukan di seluruh kolom, kembalikan false
        return false;
    });

    // DataTable initialization for advanced filter
    var dt_adv_filter = dt_adv_filter_table.DataTable({
        dom: "<'row'<'col-sm-6'<'btn-add-container'>><'col-sm-6'>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-6'i><'col-sm-6'p>>",
        ajax: {
            url: '/getDataKeuangan',
            dataSrc: 'data'
        },
        columns: [
            { data: null, title: 'No.', className: 'text-center', render: function (data, type, row, meta) { return meta.row + 1; } },
            { data: 'reference_number', title: '#REFERENCE', render: function (data, type, full, meta) { return renderReferenceNumber(full); } },
            { data: 'amount', title: 'AMOUNT', render: function (data, type, full, meta) { return renderAmount(full); } },
            { data: 'status', title: 'STATUS', render: function (data, type, full, meta) { return renderStatus(full); } },
            { data: 'description', title: 'DESKRIPSI' },
            { data: 'start_date', title: 'Date', className: 'text-center' },
            { 
                data: null,
                title: 'Action',
                render: function (data, type, full, meta) {
                    var id = full.id;
                    return (
                        '<div class="d-flex align-items-center">' +
                        '<a href="#" class="text-body edit-transaction-btn" data-transaction-id="' + id + '" data-bs-toggle="modal" data-bs-target="#editTransactionModal">' +
                        '<i class="bx bxs-message-square-edit mx-1"></i>' +
                        '</a>' +
                        '<a href="#" class="text-body" onclick="return confirmDelete(\'/delete/trans?id=' + id + '\')">' +
                        '<i class="bx bx-trash mx-1"></i>' +
                        '</a>' +
                        '</div>'
                    );
                }
            }
        ],
        orderCellsTop: true,
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) { return 'Details of ' + row.data()['source_receiver']; }
                }),
                type: 'column',
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col, i) {
                        return col.title !== '' ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '"><td>' + col.title + ':</td> <td>' + col.data + '</td></tr>' : '';
                    }).join('');
                    return data ? $('<table class="table"/><tbody />').append(data) : false;
                }
            }
        },
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pageLength: 10,
        initComplete: function () {
            $('.btn-add-container').html('<button type="button" class="btn btn-primary mx-4 mb-3" id="addNewTransactionBtn"><i class="bx bx-plus me-md-1"></i><span class="d-md-inline-block d-none">Tambah</span></button><button type="button" class="btn btn-success mx-4 mb-3" id="sendReportBtn"><i class="bx bxs-paper-plane bx-fade-right me-md-1"></i><span class="d-md-inline-block d-none">Kirim</span></button><button type="button" class="btn btn-info mx-4 mb-3" id="lihatLaporan"><i class="bx bx-show bx-tada me-md-1"></i><span class="d-md-inline-block d-none">Lihat Laporan</span></button>');
            
            $('#addNewTransactionBtn').on('click', function () {
                $('#addNewTransactionModal').modal('show');
            });
        
            $('#sendReportBtn').on('click', function () {
                $('#sendReportModal').modal('show');
            });
           
            $('#lihatLaporan').on('click', function () {
                $('#lihatLaporanModal').modal('show');
            });          
            
        }
                
    });

    referenceInput.on('input', function () {
        filterTableByReference();
    });

    // Function to trigger table redraw on reference input change
    function filterTableByReference() {
        dt_adv_filter_table.DataTable().draw();
    }

    // Event listener for status filter
    $('select.dt-status-filter').on('change', function () {
        var value = $(this).val();
        dt_adv_filter_table.DataTable().column(3).search(value).draw();
    });

    // Function to render reference number with invoice link if available
    function renderReferenceNumber(full) {
        var sourceReceiver = full.source_receiver;
        var referenceNumber = full.reference_number;
        var customer = full.customerUuid;
        if (customer) {
            var link = '/invoice/add?invoiceNumber=' + referenceNumber + '&customerUuid=' + customer;
            return '<div class="text-center">' + sourceReceiver + '<br>' + '<a href="' + link + '" class="invoice-link" target="_blank"><span class="fw-medium">#' + referenceNumber + '</span></a></div>';
        } else {
            return '<div class="text-center">' + sourceReceiver + '<br>' + '#' + referenceNumber + '</div>';
        }
    }

    // Function to render amount with currency formatting
    function renderAmount(full) {
        var amount = parseFloat(full.amount);
        var formattedAmount = amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
        return '<div class="text-center">' + formattedAmount + '</div>';
    }

    // Function to render status with badge
    function renderStatus(full) {
        var status = full.status;

        if (status == 1 || status == 2 || status == 3) {
            return '<div class="text-center"><span class="badge bg-label-primary">Invoice</span></div>';
        } else if (status == 4) {
            return '<div class="text-center"><span class="badge bg-label-danger">Operational</span></div>';
        } else if (status == 5) {
            return '<div class="text-center"><span class="badge bg-label-warning">Ambilan</span></div>';
        } else if (status == 6) {
            return '<div class="text-center"><span class="badge bg-label-secondary">Setoran Kas</span></div>';
        } else if (status == 7) {
            return '<div class="text-center"><span class="badge bg-label-success">Top Up</span></div>';
        } else if (status == 8) {
            return '<div class="text-center"><span class="badge bg-label-info">Bonus</span></div>';
        } else if (status == 9) {
            return '<div class="text-center"><span class="badge bg-label-warning">Gaji</span></div>';
        } else {
            return '<div class="text-center"><span class="badge bg-label-secondary">' + (status ? status : 'Unknown') + '</span></div>';
        }
    }

});

function confirmDelete(deleteUrl) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // If user confirms, proceed with the delete action
            window.location.href = deleteUrl;
        }
    });
    return false; 
}

$(document).on('click', '.edit-transaction-btn', function() {
    var transactionId = $(this).data('transaction-id');
    
    // Mengambil data transaksi berdasarkan ID menggunakan AJAX
    $.ajax({
        url: '/getDataKeuanganById/' + transactionId,
        method: 'GET',
        success: function(response) {
            if (response) {
                $('#tanggalTransaksi').val(response.start_date);
                
                // Mengambil nilai amount dari response
                var amountValue = response.amount;

                // Mengkonversi nilai amount menjadi format uang dengan mata uang IDR
                var formattedAmount = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(amountValue);

                // Menghapus simbol "Rp." dan desimal "00" di belakang
                formattedAmount = formattedAmount.replace('Rp', '').replace(',00', '');

                // Memasukkan nilai yang diformat ke dalam input
                $('#amount').val(formattedAmount);

                $('#id').val(response.id);
                $('#invoice_number').val(response.reference_number);
                
                // Menampilkan modal
                $('#editTransactionModal').modal('show');

                // Mengatur teks untuk ID dan nomor referensi
                $('#transactionId').text(response.id);
                $('#referenceNumber').text(response.reference_number);
            } else {
                console.error("Data transaksi tidak ditemukan dalam respons.");
                alert('Terjadi kesalahan saat memuat data transaksi.');
            }
        },
        error: function(xhr, status, error) {
            // Menangani kesalahan jika terjadi
            console.error(error);
            alert('Terjadi kesalahan saat memuat data transaksi.');
        }
    });
});

// Menambahkan event listener untuk input amount
$('#amount').on('input', function() {
    formatCurrency(this); // Memformat nilai amount setiap kali input berubah
});

function showKaryawanSelect() {
  var statusSelect = document.getElementById("status");
  var karyawanSelectDiv = document.getElementById("karyawanSelectDiv");

  if (statusSelect.value === "5" || statusSelect.value === "8") {
      karyawanSelectDiv.style.display = "block";
  } else {
      karyawanSelectDiv.style.display = "none";
  }
}

function formatCurrency(input) {
    var numericValue = $(input).val().replace(/[^\d]/g, '');
    var trimmedValue = numericValue.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,').replace(/^0+/, '');

    if (trimmedValue === '') {
        $(input).val('');
        return;
    }
    $(input).val(trimmedValue);
}

function showSweetAlert(response) {
    Swal.fire({
        icon: response.success ? 'success' : 'error',
        title: response.title,   
        text: response.message,
    });
}