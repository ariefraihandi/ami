<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\SalesController;
use App\Http\Controllers\Portal\CustomerController;
use App\Http\Controllers\Portal\InvoiceController;
use App\Http\Controllers\Portal\KeuanganController;
use App\Http\Controllers\Portal\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('login',                 [AuthController::class, 'showLoginPage'])->name('login.page');

Route::get('dashboard',                 [DashboardController::class, 'showPortalPage'])->name('dashboard.page');

Route::get('/get-all-customers',        [CustomerController::class, 'getAllCustomers'])->name('getAllCustomers');
Route::get('custumer',                  [CustomerController::class, 'showCustIndex'])->name('customer.page');
Route::post('custumer',                 [CustomerController::class, 'addCustomer'])->name('add.customer');
Route::post('edit/customer',            [CustomerController::class, 'editcustomer'])->name('edit.customer');
Route::get('/delete-customer',          [CustomerController::class, 'deleteCustomer'])->name('deleteCustomer');
Route::get('/get-customer/{uuid}',      [CustomerController::class, 'getCustomerByUUID'])->name('get-customer');


Route::get('/get-all-invoice',          [InvoiceController::class, 'getAllInvoices'])->name('getAllInvoices');


Route::get('invoice',                   [InvoiceController::class, 'showInvoiceIndex'])->name('invoices.index');
Route::post('add-invoice',              [InvoiceController::class, 'store'])->name('add.invoice');

//item
Route::get('/get-items/{invNumber}',    [InvoiceController::class, 'getInvoiceItems'])->name('get.invoice.items');
Route::get('/get-items',                [InvoiceController::class, 'getInvoiceItems'])->name('get.invoice.items');
Route::get('/edit-items/{itemId}',      [InvoiceController::class, 'getItemById'])->name('get.items.byId');
Route::get('invoice/item',              [InvoiceController::class, 'addItem'])->name('addItem.index');
Route::post('item-invoices',            [InvoiceController::class, 'itemStore'])->name('addItemInvoice');
Route::get('delete-item/',              [InvoiceController::class, 'deleteItem'])->name('delete.invoice');

Route::post('update-invoice-dates',     [InvoiceController::class, 'updateInvoiceDates'])->name('updateInvoiceDates');
Route::get('itemData/{ItemId}',         [InvoiceController::class, 'getInvoiceItemData'])->name('getId.Item');
Route::post('updateItem/',              [InvoiceController::class, 'updateItem'])->name('updateItem');
Route::post('bayarInvoice',             [InvoiceController::class, 'bayarInvoice'])->name('bayarInvoice');
Route::get('delete-invoice',            [InvoiceController::class, 'deleteInvoice'])->name('deleteInvoice');
Route::get('print/{invoiceNumber}',     [InvoiceController::class, 'generatePdf'])->name('generatePdf');


Route::get('keuangan',              [KeuanganController::class, 'showKeuanganIndex'])->name('showKeuanganIndex');
Route::post('keuangan',             [KeuanganController::class, 'addNewTransaction'])->name('addNewTransaction');

Route::get('product',               [ProductController::class, 'index'])->name('indexProduk');
Route::post('/add-product',         [ProductController::class, 'create'])->name('addProduct');
Route::get('/get-product/{id}',     [ProductController::class, 'getProduct'])->name('get.product');
Route::get('/get-products',         [ProductController::class, 'getProducts'])->name('get.products');
Route::post('/updateproduct',       [ProductController::class, 'updateProduct'])->name('updateProduct');


Route::get('/categories',           [ProductController::class, 'indexCategory'])->name('categoryIndex');
Route::post('/categories',          [ProductController::class, 'storeCategory'])->name('addCategory');
Route::post('/updateCategory',      [ProductController::class, 'updateCategory'])->name('updateCategory');
Route::get('/categories/del/{id}',  [ProductController::class, 'deleteCategory'])->name('deleteCategory');



