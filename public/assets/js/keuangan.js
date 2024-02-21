/**
 * App Invoice List (jquery)
 */

'use strict';

$(function () {
  // Variable declaration for table
  var dt_invoice_table = $('#dataTable');

  // Invoice datatable
  if (dt_invoice_table.length) {
    var dt_invoice = dt_invoice_table.DataTable({
      ajax: {
        url: '/get-keua',
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
          data: 'reference_number',
          render: function (data, type, full, meta) {
            var sourceReceiver = full.source_receiver;
            var referenceNumber = full.reference_number;
            var customer = full.customer;
        
            // Jika ada data pelanggan, tampilkan link invoice
            if (customer) {
              var link = '/invoice/add?invoiceNumber=' + referenceNumber + '&customerUuid=' + customer;
              return '<div class="text-center">' + sourceReceiver + '<br>' + '<a href="' + link + '" class="invoice-link" target="_blank"><span class="fw-medium">#' + referenceNumber + '</span></a></div>';
            } else {
              // Jika tidak ada data pelanggan, tampilkan hanya nomor referensi
              return '<div class="text-center">' + sourceReceiver + '<br>' + '#' + referenceNumber + '</div>';
            }
          }
        },        
        {
          data: 'transaction_amount',
          render: function (data, type, full, meta) {
            var transaction_amount = full.transaction_amount;
            return '<div class="text-center">' + ' Rp. ' + transaction_amount + '</div>';
          }
        },
        {
          data: 'status',
          render: function (data, type, full, meta) {
            var status = full.status;
            var badgeClass = '';

            switch (String(status)) {
              case '1':
              case '2':
              case '3':
                badgeClass = 'bg-label-primary';
                status = 'Invoice';
                break;
              case '4':
                badgeClass = 'bg-label-danger';
                status = 'Operational';
                break;
              case '5':
                badgeClass = 'bg-label-warning';
                status = 'Ambilan';
                break;
              case '6':
                badgeClass = 'bg-label-secondary';
                status = 'Setoran Kas';
                break;
              case '7':
                badgeClass = 'bg-label-success';
                status = 'Top Up';
                break;
              case '8':
                badgeClass = 'bg-label-info';
                status = 'Bonus';
                break;
              default:
                badgeClass = 'bg-label-secondary';
                status = status ? status : 'Unknown';
            }

            return '<div class="text-center"><span class="badge ' + badgeClass + '">' + status + '</span></div>';
          }
        },
        {
          data: 'created_at',
          render: function (data, type, full, meta) {
            var created_at = full.created_at;
            return '<div class="text-center">' + created_at + '</div>';
          }
        },
        {
          data: 'description',
          render: function (data, type, full, meta) {
            var description = full.description;
            return '<div class="text-center">' + description + '</div>';
          }
        },
        {
            data: null,
            render: function (data, type, full, meta) {
              var id = full.id;
              return (
                '<div class="d-flex align-items-center">' +
                '<a href="#" class="text-body" data-bs-toggle="modal" data-bs-target="#editTransactionModal' + id + '">' +
                '<i class="bx bxs-message-square-edit mx-1"></i>' +
                '</a>' +
                '<a href="#" class="text-body" onclick="return confirmDelete(\'/delete/trans?id=' + id+ '\')">' +
                '<i class="bx bx-trash mx-1"></i>' +
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
      '<"col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row pe-3 gap-md-3"f<"transaction_date_filter mb-3 mb-md-0"><"transaction_status">>' + '>t' + '<"row mx-2"' + '<"col-sm-12 col-md-6"i>' + '<"col-sm-12 col-md-6"p>' + '>',

      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Invoice'
      },
      buttons: [
        {
            text: '<i class="bx bx-plus me-md-1"></i><span class="d-md-inline-block d-none">Tambah</span>',
            className: 'btn btn-primary',
            action: function (e, dt, button, config) {
                // Tampilkan Modal
                $('#addNewTransactionModal').modal('show');
            }
        },
        {
            text: '<i class="bx bx-send"></i> Kirim',
            className: 'btn btn-success',
            action: function (e, dt, button, config) {
                // Tampilkan Modal Report
                $('#sendReportModal').modal('show');
            }
        }
    ],    
            
    initComplete: function () {
      var table = this.api();

      table.columns(5).every(function () {
          var column = this;
          var select = $('<select id="TransactionDateFilter" class="form-select"><option value="">All Dates</option></select>')
              .appendTo('.transaction_date_filter')
              .on('change', function () {
                  var val = $.fn.dataTable.util.escapeRegex($(this).val());

                  // Clear existing filters
                  $.fn.dataTable.ext.search = [];

                  // Apply the custom filter function
                  if (val) {
                      $.fn.dataTable.ext.search.push(dataTableTransactionDateFilter);
                  }

                  // Redraw the table
                  table.draw();
              });

          var dateFilterOptions = [
              { value: 'today', label: 'Today' },
              { value: 'this_week', label: 'This Week' },
              { value: 'this_month', label: 'This Month' },
              // Add more options if needed
          ];

          dateFilterOptions.forEach(function (option) {
              select.append('<option value="' + option.value + '">' + option.label + '</option>');
          });
      });

      function dataTableTransactionDateFilter(settings, data, dataIndex) {
        var currentDate = new Date();
        var dateParts = data[4].split(' '); // Gunakan indeks 4 karena kolom tanggal berada di indeks 4
        var day = parseInt(dateParts[0], 10);
        var month = getMonthIndex(dateParts[1]);
        var year = 2000 + parseInt(dateParts[2], 10);
        var transactionDate = new Date(year, month, day);

        var filterType = $('#TransactionDateFilter').val();

        switch (filterType) {
            case 'today':
                return transactionDate.toDateString() === currentDate.toDateString();
            case 'this_week':
                var oneDay = 24 * 60 * 60 * 1000;
                var diffDays = Math.round(Math.abs((currentDate - transactionDate) / oneDay));
                return diffDays <= 7;
            case 'this_month':
                return transactionDate.getMonth() === currentDate.getMonth() && transactionDate.getFullYear() === currentDate.getFullYear();
            default:
                return true; // No filter
        }
      }

      function getMonthIndex(monthAbbreviation) {
          var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
          return months.indexOf(monthAbbreviation);
      }

      table.columns(3).every(function () {
              var column = this;
              var select = $(
                  '<select id="StatusFilter" class="form-select"><option value="">Status</option></select>'
              )
                  .appendTo('.transaction_status')
                  .on('change', function () {
                      var val = $.fn.dataTable.util.escapeRegex($(this).val());
                      column.search(val ? '^' + val + '$' : '', true, false).draw();
                  });

              var statusOptions = ['Invoice', 'Operational', 'Ambilan', 'Setoran Kas', 'Top Up'];

              statusOptions.forEach(function (d) {
                  select.append('<option value="' + d.toLowerCase() + '" class="text-capitalize">' + d + '</option>');
              });
          });

      console.log('Init Complete Finished');
    }

    });
  }

  dt_invoice_table.on('draw.dt', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl, {
        boundary: document.body
      });
    });
  });

  // Delete Record
  $('.invoice-list-table tbody').on('click', '.delete-record', function () {
    dt_invoice.row($(this).parents('tr')).remove().draw();
  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
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
  const value = input.value.replace(/[^\d]/g, '');

  // Format the number with currency symbol
  const formattedValue = new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
  }).format(value);

  // Set the formatted value to the specified input field
  $(input).val(formattedValue.replace('Rp', ''));
}

function showSweetAlert(response) {
  Swal.fire({
      icon: response.success ? 'success' : 'error',
      title: response.title,   
      text: response.message,
  });
}