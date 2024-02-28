<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\SalesController;
use App\Http\Controllers\Portal\CustomerController;
use App\Http\Controllers\Portal\InvoiceController;
use App\Http\Controllers\Portal\KeuanganController;
use App\Http\Controllers\Portal\ProductController;
use App\Http\Controllers\Portal\MenuController;
use App\Http\Controllers\Portal\UserController;
use App\Http\Controllers\Portal\ReportController;
use App\Http\Controllers\Portal\DownloadController;
use App\Http\Controllers\Portal\kirimWhatsapp;


Route::get('/',                         [AuthController::class, 'showLoginPage'])->name('login');
Route::post('/login',                   [AuthController::class, 'login'])->name('login.post');
Route::get('/register',                 [AuthController::class, 'showRegisForm'])->name('register');
Route::post('/register',                [AuthController::class, 'register'])->name('register.post');
Route::get('/logout',                   [AuthController::class, 'logout'])->name('logout');
Route::get('/unduh/invoice',            [DownloadController::class, 'unduhInvoice'])->name('unduh.Invoice');



Route::middleware(['auth'])->group(function () {
    Route::get('dashboard',             [DashboardController::class, 'showPortalPage'])->name('dashboard');

    //custumer fix route
    Route::get('/get-all-customers',    [CustomerController::class, 'getAllCustomers'])->name('getAllCustomers');
    Route::get('customer/list',         [CustomerController::class, 'showCustIndex'])->name('customer.list');
    Route::get('/delete-customer',      [CustomerController::class, 'deleteCustomer'])->name('deleteCustomer');
    Route::post('custumer',             [CustomerController::class, 'addCustomer'])->name('add.customer');
    //!custumer fix route

    Route::post('edit/customer',        [CustomerController::class, 'editcustomer'])->name('edit.customer');
    Route::get('/get-customer/{uuid}',  [CustomerController::class, 'getCustomerByUUID'])->name('get-customer');

    //invoice fix route
    Route::get('/get-all-invoice',      [InvoiceController::class, 'getAllInvoices'])->name('getAllInvoices');
    Route::get('invoice/list',          [InvoiceController::class, 'showInvoiceIndex'])->name('invoice.list');
    Route::get('invoice/add',           [InvoiceController::class, 'addItem'])->name('invoice.add');
    Route::get('delete-invoice',        [InvoiceController::class, 'deleteInvoice'])->name('deleteInvoice');
    Route::get('print/{invoiceNumber}', [InvoiceController::class, 'printInvoice'])->name('generatePdf');
    Route::post('add-invoice',          [InvoiceController::class, 'store'])->name('add.invoice');
    Route::post('bayarInvoice',         [InvoiceController::class, 'bayarInvoice'])->name('bayarInvoice');    
    Route::get('/send-invoice',         [InvoiceController::class, 'sendInvoice'])->name('send.Invoice');
    Route::post('/update-invoice-dates',[InvoiceController::class, 'updateInvoiceDates'])->name('updateInvoiceDates');
    //!!invoice fix route


    //item fix route
    Route::get('/get-items',            [InvoiceController::class, 'getInvoiceItems'])->name('get.invoice.items');
    Route::post('item-invoices',        [InvoiceController::class, 'itemStore'])->name('addItemInvoice');
    Route::post('updateItem/',          [InvoiceController::class, 'updateItem'])->name('updateItem'); //NF
    Route::get('/get-items/{invNumber}',[InvoiceController::class, 'getInvoiceItems'])->name('get.invoice.items');
    Route::get('delete-item/',          [InvoiceController::class, 'deleteItem'])->name('delete.invoice');
    Route::get('/edit-items/{itemId}',  [InvoiceController::class, 'getItemById'])->name('get.items.byId');
    Route::get('itemData/{ItemId}',     [InvoiceController::class, 'getInvoiceItemData'])->name('getId.Item');
    //!!item fix route

    // fix keuangan
    Route::get('get-keua',              [KeuanganController::class, 'getAllKeuangan'])->name('dataKeuangan');
    Route::get('keuangan',              [KeuanganController::class, 'showKeuanganIndex'])->name('keuangan');
    Route::post('keuangan',             [KeuanganController::class, 'addNewTransaction'])->name('addNewTransaction');
    Route::post('/edit-transaction',    [KeuanganController::class, 'editTransaction'])->name('editTransaction');
    Route::get('/delete/trans',         [KeuanganController::class, 'delTrans'])->name('delTransaction');
    Route::get('/report',               [KeuanganController::class, 'generatePDF'])->name('generatePDF');
    
    
    Route::get('get-tagih',             [KeuanganController::class, 'getAlltagihans'])->name('dataTagihan');
    Route::get('keuangan/tagihan',      [KeuanganController::class, 'showTagihanIndex'])->name('keuangan.tagihan');
    Route::post('/bayar-gaji',          [KeuanganController ::class, 'bayarGaji'])->name('bayarGaji');
    Route::post('/edit-masa-kerja',     [KeuanganController::class, 'editMasaKerja'])->name('editMasaKerja');

    Route::get('keuangan/laporan',      [KeuanganController::class, 'showLaporan'])->name('keuangan.laporan');    
    // !!fix keuangan

    //Product fix route
    Route::get('product',               [ProductController::class, 'index'])->name('product');
    Route::post('/add-product',         [ProductController::class, 'create'])->name('addProduct');
    Route::get('/get-product/{id}',     [ProductController::class, 'getProduct'])->name('get.product');
    Route::get('/get-products',         [ProductController::class, 'getProducts'])->name('get.products');
    Route::post('/updateproduct',       [ProductController::class, 'updateProduct'])->name('updateProduct');
    // !! Product fix route

    Route::get('/categories',           [ProductController::class, 'indexCategory'])->name('categories');
    Route::post('/categories',          [ProductController::class, 'storeCategory'])->name('addCategory');
    Route::post('/updateCategory',      [ProductController::class, 'updateCategory'])->name('updateCategory');
    Route::get('/categories/del/{id}',  [ProductController::class, 'deleteCategory'])->name('deleteCategory');

    //menus
    Route::get('/get-all-menus',        [MenuController::class, 'getAllMenus'])->name('getAll.Menus');
    Route::get('menu',                  [MenuController::class, 'showMenusIndex'])->name('menu');
    Route::post('/add-menu',            [MenuController::class, 'addMenu'])->name('add.menu');
    Route::get('/delete-menu',          [MenuController::class, 'deleteMenu'])->name('delete.menu');
    Route::post('/change-access',       [MenuController::class, 'changeAccess'])->name('change.access');

    //submenus
    Route::get('/get-all-submenus',         [MenuController::class, 'getAllSubmenus'])->name('getAll.Submenus');
    Route::get('menu/submenu',              [MenuController::class, 'showSubmenusIndex'])->name('menu.submenu');
    Route::post('/add-submenu',             [MenuController::class, 'addSubmenu'])->name('add.submenu');
    Route::post('/edit-submenu',            [MenuController::class, 'editSubmenu'])->name('edit.submenu');
    Route::get('/delete-submenu',           [MenuController::class, 'deleteSubmenu'])->name('delete.submenu');

    //child Submenu
    Route::get('/get-all-child',            [MenuController::class, 'getAllChildSubmenus'])->name('getAll.childSubmenus');
    Route::get('menu/child',                [MenuController::class, 'showChildSubmenusIndex'])->name('menu.child');
    Route::post('/add-childsubmenu',        [MenuController::class, 'addChildSubmenu'])->name('add.ChildSubmenu');
    Route::get('/delete-childsubmenu',      [MenuController::class, 'deleteChildSubmenu'])->name('delete.childsubmenu');

    //menu role
    Route::get('/get-all-role',             [MenuController::class, 'getAllRole'])->name('getAll.role');
    Route::get('menu/role',                 [MenuController::class, 'showRoleIndex'])->name('menu.role');
    Route::post('/add-role',                [MenuController::class, 'addRole'])->name('add.role');
    Route::get('/delete-role',              [MenuController::class, 'deleteRole'])->name('delete.role');

    //user
    Route::get('/user/profile',             [UserController::class, 'index'])->name('user.profile');
    Route::get('/user/gaji',                [UserController::class, 'showPayroll'])->name('user.gaji');
    Route::get('/user/setting',             [UserController::class, 'showSetting'])->name('user.setting');
    Route::get('/user/security',            [UserController::class, 'showSecurity'])->name('user.security');
    Route::post('/upload-avatar',           [UserController::class, 'uploadAvatar'])->name('upload.avatar');
    Route::post('/change-password',         [UserController::class, 'changePassword'])->name('change.password');
    //!!user

    //Send Report
    Route::post('send-report',              [ReportController::class, 'sendReport'])->name('send.report');
    //!Send Report

    // users Administrator
    Route::get('/get-all-user',             [UserController::class, 'getAllUser'])->name('getAll.User');
    Route::get('/users',                    [UserController::class, 'showUserAdminIndex'])->name('users');
    Route::post('users',                    [UserController::class, 'addUsers'])->name('add.users');
    Route::get('/delete/user',              [UserController::class, 'deleteUser'])->name('delete.user');
    Route::post('/update/user',             [UserController::class, 'update'])->name('update.user');
    //!! users Administrator

    Route::get('/send-message',             [kirimWhatsapp::class, 'kirimPesan'])->name('kirim.Pesan');

    Route::get('/users/access',             [UserController::class, 'showRoleList'])->name('users.access');
});