'use strict';

$(function () {
  // Variable declaration for table
  var dt_item_table = $('#datatables-items').DataTable({
    processing: true,
    // serverSide: true,
    ajax: {
      url: '/get-items/' + invoiceNumber,
      type: 'GET',
    },
    columns: [
      { 
        data: null,
        targets: 0,
        render: function (data, type, full, meta) {
          return meta.row + 1;
        }
      },
      { 
        data: 'kode_barang', 
        name: 'kode_barang',
        targets: 1,
        render: function (data, type, full, meta) {
          return '#' + data; // Menambahkan '#' sebelum kode_barang
        }
      },
      { 
        data: 'barang', 
        name: 'barang',
        targets: 2
      },
      { 
        data: 'deskripsi', 
        name: 'deskripsi',
        targets: 3
      },
      {
        data: null,
        targets: 4,
        title: 'Actions',
        searchable: false,
        orderable: false,
        render: function (data, type, full, meta) {
            var itemId = full['id']; // Ganti 'id' dengan kunci yang sesuai pada data item
            return (
                '<div class="d-flex align-items-center">' +
                '<a href="javascript:;" class="btn-open-edit-modal text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Item" data-item-id="' + itemId + '"><i class="bx bx-edit mx-1"></i></a>' + 
                '<a href="javascript:;" class="btn-open-delete-confirmation text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Item" data-uuid="' + itemId + '" onclick="return confirmDelete(\'/delete-item?itemId=' + itemId + '\')"><i class="bx bx-trash mx-1"></i></a>' +
                '</div>'
            );
        }
      },
      { 
        data: null,
        name: 'ukuran',
        targets: 5,
        render: function (data, type, full, meta) {
          return full['ukuran'] + ' m2';
        }
      },
      { 
        data: 'qty', 
        name: 'qty',
        targets: 6,
      },
      { 
        data: 'harga_satuan', 
        name: 'harga_satuan',
        targets: 7,
        render: function (data, type, full, meta) {
          return simplifyNumber(data);
        }
      },
      { 
        data: 'discount', 
        name: 'discount',
        targets: 8,
        render: function (data, type, full, meta) {
          return simplifyNumber(data);
        }
      },
      { 
        data: 'tax', 
        name: 'tax',
        targets: 9
      },
    ],
    order: [[0, 'asc']],
    dom:
      '<"row mx-1"' +
      '<"col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start gap-3"l<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start mt-md-0 mt-3"B>>' +
      '<"col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row pe-3 gap-md-3"f<"invoice_type_filter mb-3 mb-md-0">>' +
      '>t' +
      '<"row mx-2"' +
      '<"col-sm-12 col-md-6"i>' +
      '<"col-sm-12 col-md-6"p>' +
      '>',
    language: {
      sLengthMenu: '_MENU_',
      search: '',
      searchPlaceholder: 'Search Item'
    },
    buttons: [
      
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
            return col.title !== ''
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
  });

  // On each datatable draw, initialize tooltip
  dt_item_table.on('draw.dt', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            boundary: document.body
        });
    });
  });

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

$(document).ready(function () {
  // Handle change in the select element
  $('#select2Product').on('change', function () {
      // Get the selected product ID
      var selectedProductId = $(this).val();

      // Perform AJAX request to get product data based on ID
      $.ajax({
          url: '/get-product/' + selectedProductId,
          type: 'GET',
          success: function (data) {
              // Fill the form with product data
              $('#kode_barang').val(data.kode || '');
              $('#barang').val(data.name || '');
              $('#deskripsi').val(data.deskripsi || '');

              // Recalculate total
              calculateTotal();
          },
          error: function () {
              alert('Failed to fetch product data.');
          }
      });
  });

  $.ajax({
      url: '/get-products', // Replace with the correct URL to fetch products
      type: 'GET',
      success: function (products) {
          // Iterate through the products and append options to the select element
          $('#select2Product').append('<option value="">Select</option>');
          products.forEach(function (product) {
              $('#select2Product').append('<option value="' + product.id + '">' + product.name + '</option>');
          });

          // Initialize Select2
          $('#select2Product').select2();
      },
      error: function () {
          alert('Failed to fetch product data.');
      }
  });
});

function bulatkanUkuran(ukuran) {
  // Jika ukuran kurang dari atau sama dengan 100 cm, bulatkan ke 100 cm
  if (ukuran <= 100) {
      return 100;
  } else {
      // Gunakan ceil untuk mendekatkan ke angka di atasnya dalam kelipatan 50
      return Math.ceil((ukuran - 5) / 50) * 50;
  }
}

function calculateTotal() {
  var hargaSatuan = parseFloat($('#harga_satuan').val().replace(/[^\d]/g, '')) || 0;
  var qty = parseInt($('#qty').val()) || 0;
  var discount = parseFloat($('#discount').val().replace(/[^\d]/g, '')) || 0;
  var tax = parseFloat($('#tax').val()) || 0;

  // Ambil nilai ukuran a dan b dari input
  var ukurana = parseFloat($('#ukurana').val()) || 0;
  var ukuranb = parseFloat($('#ukuranb').val()) || 0;

  // Bulatkan ukuran a dan b
  var bulatUkurana = bulatkanUkuran(ukurana) / 100;
  var bulatUkuranb = bulatkanUkuran(ukuranb) / 100;

  // Perhitungan total berdasarkan ukuran yang sudah dibulatkan
  var volume = bulatUkurana * bulatUkuranb;
  var total = (hargaSatuan * qty * volume) - discount + ((hargaSatuan * qty * volume - discount) * (tax / 100));

  // Format dan tampilkan total
  var formattedTotal = 'Rp. ' + total.toLocaleString('id-ID', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
  });

  $('#total').text(formattedTotal);
}

function updateSisa(id, totalAmount, panjarAmount, sisaId) {
  const formattedValue = $('#' + id).val().replace(/[^\d]/g, '');
  const amount = parseFloat(formattedValue) || 0;

  const sisa = totalAmount - panjarAmount - amount;

  // Format the Sisa value with currency symbol
  const formattedSisa = new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
  }).format(sisa);

  // Set the formatted Sisa value to the specified input field
  $('#' + sisaId).val(formattedSisa.replace('Rp', ''));
}

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

$(document).on('click', '.btn-open-edit-modal', function () {
  // Get the item ID from the button's data attribute
  var itemId = $(this).data('item-id');

  // Make an AJAX request to get item data
  $.ajax({
    url: '/edit-items/' + itemId,
    type: 'GET',
    success: function (response) {
        // Handle the successful response
        var itemData = response.data;
      $('#editModal #kode_barang').val(itemData.kode_barang);
      $('#editModal #barang').val(itemData.barang);
      $('#editModal #deskripsi').val(itemData.deskripsi);
      $('#editModal #ukurana').val(itemData.ukurana);
      $('#editModal #ukuranb').val(itemData.ukuranb);
      $('#editModal #harga_satuan').val(itemData.harga_satuan);
      $('#editModal #discount').val(itemData.discount);
      $('#editModal #tax').val(itemData.tax);
      $('#editModal #qty').val(itemData.qty);
      $('#editModal #total').text(itemData.total);

      // Assuming you're using Bootstrap, show the modal
      $('#editModal').modal('show');
    },
    error: function () {
      alert('Failed to fetch item details.');
    }
  });
});


function confirmDelete(deleteUrl, barang) {
  Swal.fire({
      title: 'Are you sure?',
      text: `Yakin Ingin Mengahpus Ini?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
      if (result.isConfirmed) {
          // If the user confirms, proceed to the delete URL
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