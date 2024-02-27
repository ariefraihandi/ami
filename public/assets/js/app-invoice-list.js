/**
 * App Invoice List (jquery)
 */

'use strict';

$(function () {
  // Variable declaration for table
  var dt_invoice_table = $('.invoice-list-table');

  // Invoice datatable
  if (dt_invoice_table.length) {
    var dt_invoice = dt_invoice_table.DataTable({
      ajax: '/get-all-invoice',      
      columnDefs: [
        {
          data: null,
          targets: 0,
          render: function (data, type, full, meta) {
            return meta.row + 1;
          }
        },
        {
          data: 'invoice_number',
          targets: 1,
          render: function (data, type, full, meta) {
              var invoice_number = full.invoice_number;
              var uuid = full['customer_uuid'];
              var invoiceType = invoice_number.charAt(0).toLowerCase();
              var invoiceLabel = (invoiceType === 's') ? 'Sales' : 'Project';
      
              var link = '/invoice/add?invoiceNumber=' + invoice_number + '&customerUuid=' + uuid;
              var row_output = '<a href="' + link + '" class="invoice-link"><span class="fw-medium">#' + invoice_number + '</span></a>';
              
              return '<div class="text-center">' + invoiceLabel + '<br>' + row_output + '</div>';
          }
        },       
        {
            data: 'invoice_status',
            targets: 2,
            render: function (data, type, full, meta) {
                var $status = full.status,
                    $due_date = full.due_date,
                    $total_amount = parseFloat(full.total_amount),
                    $panjar_amount = parseFloat(full.panjar_amount);

                // Determine the status
                var statusDescription = '';
                var currentDate = new Date();

                if ($status === '0') {
                    // Status 0 handling
                    var dueDate = $due_date ? new Date($due_date) : null;

                    if ($total_amount === 0 && dueDate) {
                        if (dueDate < currentDate) {
                            statusDescription = 'Kadaluarsa';
                        } else {
                            statusDescription = 'Draft';
                        }
                    } else if ($total_amount > 0 && dueDate) {
                        if (dueDate < currentDate) {
                            statusDescription = 'Jatuh Tempo';
                        } else {
                            statusDescription = 'Belum Bayar';
                        }
                    } else {
                        statusDescription = 'Unknown';
                    }
                } else if ($status === '1') {
                    var dueDate = new Date($due_date);

                    if (dueDate <= currentDate) {
                        statusDescription = 'Jatuh Tempo';
                    } else {
                        statusDescription = 'Panjar';
                    }
                } else if ($status === '2') {
                    statusDescription = 'Lunas';
                } else if ($status === '3') {
                    statusDescription = 'Error';
                } else {
                    statusDescription = 'Unknown';
                }

                // Prepare the tooltip content
                var tooltipContent = '';
                if (statusDescription === 'Lunas') {
                    tooltipContent = '<span>' + statusDescription + '<br> <span class="fw-medium">Tagihan:</span> ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format($total_amount) + '</span>';
                } else if (statusDescription === 'Belum Bayar') {
                    tooltipContent = '<span>' + statusDescription + '<br> <span class="fw-medium">Tagihan:</span> ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format($total_amount) + '<br> <span class="fw-medium">Tempo:</span> ' + moment($due_date).format('DD MMM YYYY') + '</span>';
                } else if (statusDescription === 'Panjar') {
                    var remainingAmount = $total_amount - $panjar_amount;
                    tooltipContent = '<span>' + statusDescription + '<br> <span class="fw-medium">Sisa Tagihan:</span> ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(remainingAmount) + '<br> <span class="fw-medium">Tempo:</span> ' + moment($due_date).format('DD MMM YYYY') + '</span>';
                } else if (statusDescription === 'Jatuh Tempo') {
                    var remainingAmount = $total_amount - $panjar_amount;
                    tooltipContent = '<span>' + statusDescription + '<br> <span class="fw-medium">Sisa Tagihan:</span> ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(remainingAmount) + '<br> <span class="fw-medium">Tempo:</span> ' + moment($due_date).format('DD MMM YYYY') + '</span>';
                } else if (statusDescription === 'Kadaluarsa') {
                    tooltipContent = '<span>' + statusDescription + '</span>';
                } else {
                    tooltipContent = '<span>' + statusDescription + '<br> <span class="fw-medium">Tagihan:</span> ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format($total_amount) + '</span>';
                }

                // Now, based on the determined status, select the appropriate badge
                var roleBadgeObj = '';
                if (statusDescription === 'Draft') {
                    roleBadgeObj = '<span class="badge badge-center rounded-pill bg-label-info w-px-30 h-px-30"><i class="bx bxs-save bx-xs"></i></span>';
                } else if (statusDescription === 'Jatuh Tempo') {
                    roleBadgeObj = '<span class="badge badge-center rounded-pill bg-label-danger w-px-30 h-px-30"><i class="bx bx-info-circle bx-xs"></i></span>';
                } else if (statusDescription === 'Belum Bayar') {
                    roleBadgeObj = '<span class="badge badge-center rounded-pill bg-label-warning w-px-30 h-px-30"><i class="bx bx-hourglass bx-xs"></i></span>';
                } else if (statusDescription === 'Panjar') {
                    roleBadgeObj = '<span class="badge badge-center rounded-pill bg-label-primary w-px-30 h-px-30"><i class="bx bx-adjust bx-xs"></i></span>';
                } else if (statusDescription === 'Lunas') {
                    roleBadgeObj = '<span class="badge badge-center rounded-pill bg-label-success w-px-30 h-px-30"><i class="bx bx-badge-check bx-xs"></i></span>';
                } else if (statusDescription === 'Error') {
                    roleBadgeObj = '<span class="badge badge-center rounded-pill bg-label-danger w-px-30 h-px-30"><i class="bx bx-down-arrow-circle bx-xs"></i></span>';
                } else if (statusDescription === 'Kadaluarsa') {
                    roleBadgeObj = '<span class="badge badge-center rounded-pill bg-label-danger w-px-30 h-px-30"><i class="bx bx-message-alt-x bx-xs"></i></span>';
                } else {
                    roleBadgeObj = '<span class="badge badge-center rounded-pill bg-label-secondary w-px-30 h-px-30"><i class="bx bx-question-mark bx-xs"></i></span>';
                }

                return (
                    "<span data-bs-toggle='tooltip' data-bs-html='true' title='" + tooltipContent + "'>" +
                    roleBadgeObj +
                    '</span>'
                );
            }
        },
        {
          data: 'customer', 
          targets: 3,
          responsivePriority: 1,
          render: function (data, type, full, meta) {
              var $name = full.customer.name,
                  $type = full.customer.customer_type,
                  $invoiceName = full['invoice_name'];
                  

              var $output;

              switch ($type) {
                  case 'individual':
                      $output = '<img src="' + assetsPath + 'img/front-pages/icons/user-success.png" alt="Avatar" class="rounded-circle">';
                      break;
                  case 'biro':
                      $output = '<img src="' + assetsPath + 'img/front-pages/icons/user.png" alt="Avatar" class="rounded-circle">';
                      break;
                  case 'instansi':
                      $output = '<img src="' + assetsPath + 'img/front-pages/icons/diamond-info.png" alt="Avatar" class="rounded-circle">';
                      break;
                  default:
                      $output = ''; // Handle default case if needed
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
                  '<small class="text-muted">' + $invoiceName + '</small>' +
                  '</div>' +
                  '</div>';
              return $row_output;
          }
        },
        {
          data: 'total_amount',
          targets: 4,
          render: function (data, type, full, meta) {
            var $total_amount = full.total_amount;
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format($total_amount);
          }
        },
        {
          data: 'created_at',
          targets: 5,
          render: function (data, type, full, meta) {
            var $due_date = moment(full['created_at']);
            
            // Creates full output for row
            var $row_output =
              '<span class="d-none">' +
              $due_date.format('YYYYMMDD') +
              '</span>' +
              $due_date.format('DD MMM YYYY');
            
            return $row_output;
          }
        },
        {
            data: 'status',
            targets: 6, // Remove this line
            orderable: false,
            render: function (data, type, full, meta) {
                var total_amount = full.total_amount;
                var panjar_amount = full.panjar_amount;
                var due_date = moment(full.due_date);
                var current_date = moment();

                var $badge_class = 'bg-label-info';
                var status = 'Draft';
                var filterStatus = 'Draft';

                if (full.status == 0) {
                    if (panjar_amount > 0 && current_date.isBefore(due_date)) {
                        $badge_class = 'bg-label-primary';
                        status = 'Panjar';
                        // status = 'Panjar <br><br> Sisa: ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total_amount - panjar_amount);
                    } else if (total_amount > 0 && current_date.isBefore(due_date)) {
                        $badge_class = 'bg-label-warning';
                        status = 'Belum Bayar';
                    } else if (panjar_amount == 0.00 && current_date.isAfter(due_date) ) {
                        $badge_class = 'bg-label-danger';
                        status = 'Jatuh Tempo';
                        // status = 'Jatuh Tempo' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total_amount - panjar_amount);
                    } else if (current_date.isAfter(due_date)) {
                        $badge_class = 'bg-label-danger';
                        status = 'Kadaluarsa';
                    } else {
                        $badge_class = 'bg-label-info';
                        status = 'Draft';
                    }
                } else if (full.status == 1) {
                    if (panjar_amount > 0 && current_date.isBefore(due_date)) {
                        $badge_class = 'bg-label-primary';
                        status = 'Panjar';
                        // status = 'Panjar <br><br> Sisa: ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total_amount - panjar_amount);
                    } else if (panjar_amount == 0.00 && current_date.isBefore(due_date)) {
                        $badge_class = 'bg-label-warning';
                        status = 'Belum Bayar';
                    } else if (current_date.isAfter(due_date)) {
                        $badge_class = 'bg-label-danger';
                        status = 'Jatuh Tempo';
                        // status = 'Jatuh Tempo <br><br> Sisa: ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total_amount - panjar_amount);
                    }
                } else if (full.status == 2 && current_date.isBefore(due_date)) {
                    $badge_class = 'bg-label-success';
                    status = 'Lunas';
                } else if (current_date.isAfter(due_date)) {
                    $badge_class = 'bg-label-success';
                    status = 'Lunas';
                }


                return '<span class="badge ' + $badge_class + '">' + status + '</span>';
            }
        },
        {
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
              var invoiceNumber = full['invoice_number']; // Ubah ini sesuai dengan nama kolom pada objek data
              var uuid = full['customer_uuid']; // Ubah ini sesuai dengan nama kolom pada objek data

              return (
                  '<div class="d-flex align-items-center">' +
                  '<a href="' + '/invoice/add?invoiceNumber=' + invoiceNumber + '&customerUuid=' + uuid + '" data-bs-toggle="tooltip" class="text-body" data-bs-placement="top" title="Edit"><i class="bx bxs-message-square-edit mx-1"></i></a>' +
                  '<a href="/delete-invoice?invoiceNumber=' + invoiceNumber + '" data-bs-toggle="tooltip" class="text-body" data-bs-placement="top" title="Hapus" onclick="return confirmDelete(\'/delete-invoice?invoiceNumber=' + invoiceNumber + '\')"><i class="bx bx-trash mx-1"></i></a>' +                  
                  '<div class="dropdown">' +
                  '<a href="javascript:;" class="btn dropdown-toggle hide-arrow text-body p-0" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></a>' +
                  '<div class="dropdown-menu dropdown-menu-end">' +
                  '<a href="/print/' + invoiceNumber +'" class="dropdown-item" target="_blank">Download</a>' +
                  '<a href="' + '/send-invoice?invoiceNumber=' + invoiceNumber + '&customerUuid=' + uuid + '" class="dropdown-item">Kirim Invoice</a>' +
                  '<a href="javascript:;" class="dropdown-item">Duplicate</a>' +                                  
                  '</div>' +
                  '</div>' +
                  '</div>'
              );
          }
        }
      ],
      order: [[0, 'asc']],
      dom:
      '<"row mx-1"' +
      '<"col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start gap-3"l<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start mt-md-0 mt-3"B>>' +
      '<"col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row pe-3 gap-md-3"f<"invoice_type_filter mb-3 mb-md-0"><"invoice_status">>' + '>t' + '<"row mx-2"' + '<"col-sm-12 col-md-6"i>' + '<"col-sm-12 col-md-6"p>' + '>',

      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search Invoice'
      },
      buttons: [
        {
            text: '<i class="bx bx-plus me-md-1"></i><span class="d-md-inline-block d-none">Create Invoice</span>',
            className: 'btn btn-primary',
            action: function (e, dt, button, config) {
                // Tampilkan Modal
                $('#createInvoiceModal').modal('show');
            }
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['full_name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      },
      initComplete: function () {
        var table = this.api();

        table.columns(1).every(function () {
        var column = this;
        var select = $('<select id="InvoiceTypeFilter" class="form-select"><option value="">Type</option></select>')
          .appendTo('.invoice_type_filter')
          .on('change', function () {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            if (val === '') {
              column.search('').draw();
            } else {
              column
                .search('^' + val.toLowerCase(), true, false)
                .draw();
            }
          });

          var options = [
            { value: 'S', label: 'Sales' },
            { value: 'P', label: 'Project' }
          ];

          options.forEach(function (option) {
            select.append('<option value="' + option.value + '" class="text-capitalize">' + option.label + '</option>');
          });
        });

        table.columns(6).every(function () {
          var column = this;
          var select = $(
              '<select id="UserRole" class="form-select"><option value=""> Status </option></select>'
          )
                .appendTo('.invoice_status')
                .on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                });

            var statusOptions = ['Draft', 'Lunas', 'Belum Bayar', 'Panjar', 'Jatuh Tempo', 'Kadaluarsa'];

            statusOptions.forEach(function (d) {
                select.append('<option value="' + d.toLowerCase() + '" class="text-capitalize">' + d + '</option>');
            });
        });

          console.log('Init Complete Finished');
      }    
    });
  }

  // On each datatable draw, initialize tooltip
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
  return false; // Prevent the default link behavior
}

function showSweetAlert(response) {
  Swal.fire({
      icon: response.success ? 'success' : 'error',
      title: response.title,   
      text: response.message,
  });
}