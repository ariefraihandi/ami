'use strict';

$(document).ready(function () {
    // Variable declaration for tables
    var dt_menus_table = $('.datatables-menus');
    var dt_submenus_table = $('.datatables-submmenus');
    var dt_child_table = $('.datatables-childs');
    var dt_role = $('.datatables-roles');

    // Initialize DataTables for Menus
    if (dt_menus_table.length) {
        var dt_menus = dt_menus_table.DataTable({
            ajax: '/get-all-menus',
            columns: [
                { data: null, targets: 0, render: function (data, type, full, meta) { return meta.row + 1; } },
                { data: 'menu_name', targets: 1, render: function (data, type, full, meta) { return "<span class='fw-medium text-heading'>#" + data + "</span>"; } },
                { data: 'order', targets: 2, render: function (data, type, full, meta) { return "<span class='fw-medium text-heading'>#" + data + "</span>"; } },
                {
                    data: 'status',
                    targets: 3,
                    render: function (data, type, full, meta) {
                        return (data == 1) ? "<span class='badge bg-success'>Active</span>" : "<span class='badge bg-danger'>Inactive</span>";
                    }
                },
                {
                    data: null,
                    targets: 4,
                    title: 'Actions',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var id = full.id;
                        var menuName = full.menu_name;

                        return (
                            '<div class="d-flex align-items-center">' +
                            '<a href="javascript:;" data-bs-toggle="tooltip" class="text-body" data-bs-placement="top" title="Hapus" onclick="confirmDelete(\'' + id + '\', \'/delete-menu?id=' + id + '\', \'' + menuName + '\')"><i class="bx bx-trash mx-1"></i></a>' +
                            '<a href="javascript:;" class="btn-open-edit-modal text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Dummy Action 3" data-uuid="' + full.uuid + '"><i class="bx bx-edit mx-1"></i></a>' +
                            '</div>'
                        );
                    }
                }
            ],
            dom:
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
                searchPlaceholder: 'Cari Menu',
            },
            responsive: true,
        });
        $('.dt-buttons').remove();
        $('.dataTables_length').addClass('mt-0 mt-md-3 me-2');
        $('.dt-action-buttons').addClass('pt-0');
    }
    
    if (dt_submenus_table.length) {
        var dt_submenus_table = dt_submenus_table.DataTable({
            ajax: {
                url: '/get-all-submenus',
                dataSrc: 'data',
            },
            columns: [
                { data: null, targets: 0, render: function (data, type, full, meta) { return meta.row + 1; } },
                { data: 'title', targets: 1, render: function (data, type, full, meta) { return "<span class='fw-medium text-heading'>#" + data + "</span>"; } },
                { data: 'menu_name', targets: 2, render: function (data, type, full, meta) { return "<span class='fw-medium text-heading'>#" + data + "</span>"; } },
                { data: 'url', targets: 3, render: function (data, type, full, meta) { return "<span class='fw-medium text-heading'>#" + data + "</span>"; } },
                { data: 'icon', targets: 4, render: function (data, type, full, meta) { return "<span class='fw-medium text-heading'>" + data + "</span>"; } },                
                {
                    data: 'itemsub',
                    targets: 5,
                    render: function (data, type, full, meta) {
                        return (data == 1) ? "<span class='badge bg-primary'>Dropdown</span>" : "<span class='badge bg-warning'>Not Dropdown</span>";
                    }
                },
                {
                    data: 'is_active',
                    targets: 6,
                    render: function (data, type, full, meta) {
                        return (data == 1) ? "<span class='badge bg-success'>Active</span>" : "<span class='badge bg-danger'>Inactive</span>";
                    }
                },
                {
                    data: null,
                    targets: 7,
                    title: 'Actions',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var id = full.id;
                        var menuName = full.title;

                        return (
                            '<div class="d-flex align-items-center">' +
                            '<a href="javascript:;" data-bs-toggle="tooltip" class="text-body" data-bs-placement="top" title="Hapus" onclick="confirmDelete(\'' + id + '\', \'/delete-submenu?id=' + id + '\', \'' + menuName + '\')"><i class="bx bx-trash mx-1"></i></a>' +
                            '<a href="javascript:;" class="btn-open-edit-modal text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Dummy Action 3" data-uuid="' + full.uuid + '"><i class="bx bx-edit mx-1"></i></a>' +
                            '</div>'
                        );
                    }
                }
            ],
            dom:
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
                searchPlaceholder: 'Cari Submenu',
            },
            responsive: true,
        });
        $('.dt-buttons').remove();
        $('.dataTables_length').addClass('mt-0 mt-md-3 me-2');
        $('.dt-action-buttons').addClass('pt-0');
    }
    
    if (dt_child_table.length) {
        var dt_child_table = dt_child_table.DataTable({
            ajax: {
                url: '/get-all-child',
                dataSrc: 'data',
            },
            columns: [
                { data: null, targets: 0, render: function (data, type, full, meta) { return meta.row + 1; } },
                { data: 'title', targets: 1, render: function (data, type, full, meta) { return "<span class='fw-medium text-heading'>#" + data + "</span>"; } },
                { data: 'id_submenu', targets: 2, render: function (data, type, full, meta) { return "<span class='fw-medium text-heading'>#" + data + "</span>"; } },
                { data: 'url', targets: 3, render: function (data, type, full, meta) { return "<span class='fw-medium text-heading'>#" + data + "</span>"; } },                
                {
                    data: null,
                    targets: 4,
                    render: function (data, type, full, meta) {
                        return (data.is_active == 1) ? "<span class='badge bg-success'>Active</span>" : "<span class='badge bg-danger'>Inactive</span>";
                    }
                },
                {
                    data: null,
                    targets: 5,
                    title: 'Actions',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var id = full.id;
                        var menuName = full.title;
    
                        return (
                            '<div class="d-flex align-items-center">' +
                            '<a href="javascript:;" data-bs-toggle="tooltip" class="text-body" data-bs-placement="top" title="Hapus" onclick="confirmDelete(\'' + id + '\', \'/delete-childsubmenu?id=' + id + '\', \'' + menuName + '\')"><i class="bx bx-trash mx-1"></i></a>' +
                            '<a href="javascript:;" class="btn-open-edit-modal text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Dummy Action 3" data-uuid="' + full.uuid + '"><i class="bx bx-edit mx-1"></i></a>' +
                            '</div>'
                        );
                    }
                }
            ],
            dom:
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
                searchPlaceholder: 'Cari Submenu',
            },
            responsive: true,
        });

        $('.dt-buttons').remove();
        $('.dataTables_length').addClass('mt-0 mt-md-3 me-2');
        $('.dt-action-buttons').addClass('pt-0');
    }

    if (dt_role.length) {
        var dt_role = dt_role.DataTable({
            ajax: {
                url: '/get-all-role',
                dataSrc: 'data',
            },
            columns: [
                { data: null, targets: 0, render: function (data, type, full, meta) { return meta.row + 1; } },
                { data: 'role', targets: 1, render: function (data, type, full, meta) { return "<span class='fw-medium text-heading'>#" + data + "</span>"; } },               
                {
                    data: null,
                    targets: 3,
                    title: 'Actions',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var id = full.id;
                        var menuName = full.role;
    
                        return (
                            '<div class="d-flex align-items-center">' +
                            '<a href="javascript:;" data-bs-toggle="tooltip" class="text-body" data-bs-placement="top" title="Hapus" onclick="confirmDelete(\'' + id + '\', \'/delete-role?id=' + id + '\', \'' + menuName + '\')"><i class="bx bx-trash mx-1"></i></a>' +
                            '<a href="javascript:;" class="btn-open-edit-modal text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Dummy Action 3" data-uuid="' + full.uuid + '"><i class="bx bx-edit mx-1"></i></a>' +
                            '</div>'
                        );
                    }
                }
            ],
            dom:
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
                searchPlaceholder: 'Cari Submenu',
            },
            responsive: true,
        });
        $('.dt-buttons').remove();
        $('.dataTables_length').addClass('mt-0 mt-md-3 me-2');
        $('.dt-action-buttons').addClass('pt-0');
    }
    
    
});

function confirmDelete(id, deleteUrl, menuName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete ${menuName}?`,
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
