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

    // Custom filter for date range
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var startDate = moment(startDateEle.val(), 'YYYY/MM/DD');
        var endDate = moment(endDateEle.val(), 'YYYY/MM/DD');
        var currentDate = moment(data[5], 'YYYY/MM/DD'); // Assuming date column index is 5
        if (startDate.isValid() && endDate.isValid()) {
            return currentDate.isBetween(startDate, endDate, null, '[]'); // '[]' includes both start and end dates
        }
        return true;
    });

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      var referenceValue = referenceInput.val().trim().toUpperCase(); // Trim and convert to uppercase for case-insensitive search
      var referenceData = data[1].toUpperCase(); // Assuming reference column index is 0

      if (referenceValue !== '') {
          return referenceData.includes(referenceValue);
      }
      return true;
  });

    // DataTable initialization for advanced filter
    var dt_adv_filter = dt_adv_filter_table.DataTable({
        dom: "<'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 dataTables_pager'p>>",
        ajax: {
            url: '/getDataKeuangan',
            dataSrc: 'data'
        },
        columns: [
            { data: null, title: 'No.', className: 'text-center', render: function (data, type, row, meta) { return meta.row + 1; } },
            { data: 'reference_number', title: '#REFERENCE', render: function (data, type, full, meta) { return renderReferenceNumber(full); } },
            { data: 'amount', title: 'AMOUNT', render: function (data, type, full, meta) { return renderAmount(full); } },
            {
              data: 'status',
              title: 'STATUS',
              render: function (data, type, full, meta) {
                  try {
                      var status = full.status;
                      var statusText = '';
                      var badgeClass = '';
          
                      if (status === 1 || status === 2 || status === 3) {
                          statusText = 'Invoice';
                          badgeClass = 'bg-label-primary';
                      } else if (status === 4) {
                          statusText = 'Operational';
                          badgeClass = 'bg-label-danger';
                      } else if (status === 5) {
                          statusText = 'Ambilan';
                          badgeClass = 'bg-label-warning';
                      } else if (status === 6) {
                          statusText = 'Setoran Kas';
                          badgeClass = 'bg-label-secondary';
                      } else if (status === 7) {
                          statusText = 'Top Up';
                          badgeClass = 'bg-label-success';
                      } else if (status === 8) {
                          statusText = 'Bonus';
                          badgeClass = 'bg-label-info';
                      } else if (status === 9) {
                          statusText = 'Gaji';
                          badgeClass = 'bg-label-warning';
                      } else {
                          statusText = status ? status : 'Unknown';
                          badgeClass = 'bg-label-secondary';
                      }
          
                      return '<div class="text-center"><span class="badge ' + badgeClass + '">' + statusText + '</span></div>';
                  } catch (error) {
                      console.error('Error rendering status:', error);
                      return '<div class="text-center"><span class="badge bg-label-danger">Error</span></div>';
                  }
              }
          },                   
            { data: 'description', title: 'DESKRIPSI' },
            { data: 'start_date', title: 'Date', className: 'text-center' }
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
        pageLength: 10
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
            var link = '/invoice/add?invoiceNumber=' + referenceNumber + '&customerUuid=' + customer.customerUuid;
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

    

});
