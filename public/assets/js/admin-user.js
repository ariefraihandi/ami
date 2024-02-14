'use strict';

$(function () {
  var dt_customer_table = $('#dataTable');

  if (dt_customer_table.length) {
    var dt_customer = dt_customer_table.DataTable({
      ajax: {
        url: '/get-all-user',
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
              var $name = full['name'],
                  $email = full['email'],
                  $image = full['image'];

              var $output;

              if ($image) {
                  $output = '<img src="' + assetsPath + 'img/staff/' + $image + '" alt="Avatar" class="rounded-circle">';
              } else {
                  $output = '<span class="avatar-initial rounded-circle bg-primary">' + $name.charAt(0).toUpperCase() + '</span>';
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
                  '<span class="text-muted">' + $email + '</span>' +
                  '</div>' +
                  '</div>';
              return $row_output;
          }
        },
        { data: 'username' },
        { data: 'wa' }, 
        { data: 'status' },
        { data: 'address' },
        {
          data: 'created_at',
          render: function (data, type, full, meta) {
            return moment(data).format('YYYY-MM-DD');
          }
        },
        {
          data: null,
          render: function (data, type, full, meta) {
            return '<button class="btn btn-sm btn-primary">Edit</button>';
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
            text: '<i class="bx bx-plus me-md-1"></i><span class="d-md-inline-block d-none">Add New Users</span>',
            className: 'btn btn-primary',
            action: function (e, dt, button, config) {
                $('#addUsers').modal('show');
            }
        }
      ],    
    });
  }
});


function formatCurrency(input, id) {
  const value = input.value.replace(/[^\d]/g, '');

  // Format the number with currency symbol
  const formattedValue = new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
  }).format(value);

  // Set the formatted value to the specified input field
  $('#' + id).val(formattedValue.replace('Rp', ''));
}

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