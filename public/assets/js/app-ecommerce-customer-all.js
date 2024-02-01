/**
 * App eCommerce customer all
 */

'use strict';

// Datatable (jquery)
$(function () {
  let borderColor, bodyBg, headingColor;

  if (isDarkStyle) {
    borderColor = config.colors_dark.borderColor;
    bodyBg = config.colors_dark.bodyBg;
    headingColor = config.colors_dark.headingColor;
  } else {
    borderColor = config.colors.borderColor;
    bodyBg = config.colors.bodyBg;
    headingColor = config.colors.headingColor;
  }

  // Variable declaration for table
  var dt_customer_table = $('.datatables-customers'),
    select2 = $('.select2'),
    url = $('#url').val(),
    customerView = 'app-ecommerce-customer-details-overview.html';
  if (select2.length) {
    var $this = select2;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'United States ',
      dropdownParent: $this.parent()
    });
  }


  // customers datatable
  if (dt_customer_table.length) {
    var dt_customer = dt_customer_table.DataTable({
      // serverSide: true,
      ajax: '/get-all-customers',
    
      columnDefs: [
        {
          data: null,
          targets: 0,
          render: function (data, type, full, meta) {
              return meta.row + 1;
          }
        },
        {
            data: 'customer',
            targets: 1,
            responsivePriority: 1,
            render: function (data, type, full, meta) {
                var $name = full['name'],                    
                    $type = full['customer_type'];

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
                    '<span class="fw-medium">' + $type + '</span>' +
                    '</div>' +
                    '</div>';
                return $row_output;
            }
        },
        {
          data: 'active', // Ubah dari 'status' ke 'active'
          targets: 2,
          render: function (data, type, full, meta) {
              var $statusBadge = data == 'Active' ? '<span class="badge bg-label-success">Active</span>' : '<span class="badge bg-label-danger">Inactive</span>';
      
              return '<div class="status-badge">' + $statusBadge + '</div>';
          }
        },      
        {
          data: 'customer_id',
          targets: 3, 
          render: function(data, type, full, meta) {
            var id = full.uuid;
            return "<span class='fw-medium text-heading'>#" + id + '</span>';
          }
        },
        {
          data: null,
          targets: 4,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
            var uuid = full['uuid']; 
            var name = full['name'];
            return (
              '<div class="d-flex align-items-center">' +
              '<a href="javascript:;" class="btn-open-modal text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Buat Invoice" data-bs-target="#jenisinvoice" data-uuid="' + uuid + '"><i class="bx bxs-cart-add mx-1"></i></a>' +
              '<a href="javascript:;" data-bs-toggle="tooltip" class="text-body" data-bs-placement="top" title="Hapus Customer" onclick="return confirmDelete(\'/delete-customer?uuid=' + uuid + '&name=' + encodeURIComponent(name) + '\')"><i class="bx bx-trash mx-1"></i></a>' +   
              '<a href="javascript:;" class="btn-open-edit-modal text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Custumer" " data-uuid="' + uuid + '"><i class="bx bx-edit mx-1"></i></a>' +
              '</div>'
            );
          }
        },
        {
          data: 'total_orders',
          targets: 5,
          render: function (data, type, full, meta) {
              return "<span class='fw-medium text-heading'>#" + data + "</span>";
          }
        },
        {
          data: 'total_spent',
          targets: 6,
          render: function (data, type, full, meta) {
              var formattedAmount = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data);

              var simplifiedAmount = simplifyNumber(data);

              return "<span class='fw-medium text-heading'>" + formattedAmount + '</span>';
          }
        },
        {
          data: 'created_at',
          targets: 7,
          render: function (data, type, full, meta) {
            var createdAtDate = moment(data, 'YYYY-MM-DD').toDate();
            var currentDate = new Date();
            var daysDiff = Math.floor((currentDate - createdAtDate) / (1000 * 60 * 60 * 24));

            if (daysDiff === 0) {
              return 'Today';
            } else if (daysDiff === 1) {
              return 'Yesterday';
            } else if (daysDiff <= 7) {
              return daysDiff + ' days ago';
            } else if (daysDiff <= 14) {
              return 'a week ago';
            } else if (daysDiff <= 30) {
              var weeksDiff = Math.floor(daysDiff / 7);
              return weeksDiff + ' weeks ago';
            } else if (daysDiff <= 60) {
              return 'a month ago';
            } else {
              var monthsDiff = Math.floor(daysDiff / 30);
              return monthsDiff + ' months ago';
            }
          }
        }
      ],
      // order: [[2, 'desc']],
      dom:
        //
        '<"card-header d-flex flex-wrap py-3"' +
        '<"me-5 ms-n2"f>' +
        '<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end gap-3 gap-sm-2 flex-wrap flex-sm-nowrap"lB>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',

      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Cari Custumer'
      },
      // Buttons with Dropdown
      buttons: [
        
        {
          text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Customer</span>',
          className: 'add-new btn btn-primary',
          attr: {
            'data-bs-toggle': 'modal',
            'data-bs-target': '#modalEcommerceCustomerAdd'
          }
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['customer'];
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
      }
    });
    $('.dataTables_length').addClass('mt-0 mt-md-3 me-2');
    $('.dt-action-buttons').addClass('pt-0');
    // To remove default btn-secondary in export buttons
    $('.dt-buttons > .btn-group > button').removeClass('btn-secondary');
  }

  
  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
});

function simplifyNumber(value) {
  if (value === 0) {
      return 'Rp. 0';
  }
  return 'Rp. ' + value.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

var currentUuid;

$(document).on('click', '.btn-open-modal', function () {
  currentUuid = $(this).data('uuid');

  $.ajax({
    url: '/get-customer/' + currentUuid,
    type: 'GET',
    success: function (response) {
      var customerData = response.data;

      $('#usernamePlaceholder').text(customerData.name);
      
      // Set the UUID value in the sales and project forms
      $('#sales input[name="customer_uuid"]').val(currentUuid);
      $('#project input[name="customer_uuid"]').val(currentUuid);

      // Display the jenisinvoice modal
      $('#jenisinvoice').modal('show');
    },
    error: function (error) {
      console.error('Error fetching customer data:', error);
    }
  });
});

function continueToModal(modalId) {
  // Hide current modal
  $('#' + modalId).modal('hide');
}

// Handling radio button clicks
$(document).on('click', '#customRadioTemp2, #customRadioTemp1', function () {
  continueToModal('jenisinvoice');
});

// Handling "Continue" button clicks
$(document).on('click', '#continueToSales', function () {
  continueToModal('sales');
});

$(document).on('click', '#continueToProject', function () {
  continueToModal('project');
});


$(document).on('click', '.btn-open-edit-modal', function () {
  // Get the customer UUID from the button's data attribute
  var uuid = $(this).data('uuid');

  // Make an AJAX request to get customer data
  $.ajax({
      url: '/get-customer/' + uuid,
      type: 'GET',
      success: function (response) {
          // Handle the successful response
          var customerData = response.data;

          // Populate form fields with customer data
          $('#editCustomerName').text(customerData.name);
          $('#nameedit').val(customerData.name);
          $('#phoneedit').val(customerData.phone);
          $('#emailedit').val(customerData.email);
          $('#addressedit').val(customerData.address);
          $('#uuidedit').val(customerData.uuid);
          

          // Display the edit customer modal
          $('#editCustomerModal').modal('show');
      },
      error: function (error) {
          // Handle the error
          console.error('Error fetching customer data:', error);
      }
  });
});

function confirmDelete(deleteUrl, customerName) {
  Swal.fire({
      title: 'Are you sure?',
      text: `You are about to delete?. Aksi Ini Juga Memicu Penghapusan Data Invoice Dan Transaksi Custumer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
      if (result.isConfirmed) {
          // If the user confirms, proceed with the delete action
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