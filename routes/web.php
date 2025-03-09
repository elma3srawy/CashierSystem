<?php


use App\Models\OrderPricing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Admin\ChangeRoleController;
use App\Http\Controllers\Section\SectionsController;
use App\Http\Controllers\Sales\SalesWeeklyController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Notification\NotificationController;

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



Route::controller(AuthenticatedSessionController::class)->group(function(){
    Route::get('/login' , 'create')->middleware('guest')->name('login');
    Route::post('/login' , 'store')->middleware('guest')->name('login.store');
    Route::post('/logout' , 'destroy')->middleware('auth')->name('logout');
});


Route::middleware('auth')->group(function(){
    Route::get('/', [DashboardController::class ,'__invoke'])->name('dashboard');


    Route::get('/invoice/{id}/print', [InvoiceController::class, 'print'])->name('invoice.print');
    Route::get('/get-products', [InvoiceController::class, 'getProductData'])->name('invoice.get.product');
    Route::get('/get-section', [InvoiceController::class, 'getSection'])->name('invoice.get.section');
    Route::get('/invoices/pending', [InvoiceController::class, 'pending'])->name('invoice.pending');
    Route::get('/invoices/inactive', [InvoiceController::class, 'inactive'])->name('invoice.inactive');
    Route::get('/invoices/search', [InvoiceController::class, 'search'])->name('invoice.search');
    Route::get('/invoices/create/pending', [InvoiceController::class, 'createPending'])->name('invoice.create.pending');
    Route::get('/invoices/create/inactive', [InvoiceController::class, 'createInactive'])->name('invoice.create.inactive');

    Route::resource('/invoice', InvoiceController::class)->except('create');

    Route::middleware('super.admin')->group(function(){

        Route::post('/section-change-status/{section}' , [SectionsController::class , "changeStatus"]);
        Route::resource('/section' ,SectionsController::class);

        Route::resource('/products', ProductController::class)->except(['index' , 'create']);
        Route::get('/product/{id}', [ProductController::class, 'index'])->name('products.index');
        Route::get('/product/create/{id}', [ProductController::class, 'create'])->name('products.create');
        Route::get('/products-search', [ProductController::class, 'search'])->name('product.search');

        Route::put('/invoice/{id}/restore', [InvoiceController::class, 'restore'])->name('invoice.restore');
        Route::get('/invoice/{id}/clients', [InvoiceController::class, 'invoiceClients'])->name('invoice.clients');
        Route::post('/invoice/{id}/pay', [InvoiceController::class, 'pay'])->name('invoice.pay');
        Route::get('/invoice/{id}/{status}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit');
        Route::put('/invoice/{id}/update', [InvoiceController::class, 'update'])->name('invoice.update');

        Route::get('/order/create', [InvoiceController::class, 'createOrder'])->name('order.create');
        Route::put('/order/update', [InvoiceController::class, 'updateOrder'])->name('order.update');
        Route::delete('/order/delete', [InvoiceController::class, 'destroyOrder'])->name('order.delete');

        Route::get('/sales', [SalesController::class, 'index'])->name('sales.test.index');
        Route::get('/deposits', [SalesWeeklyController::class, 'deposits'])->name('sales.deposit');
        // Route::get('/sales/pending', [SalesController::class, 'pending'])->name('sales.pending');
        // Route::get('/sales/inactive', [SalesController::class, 'inactive'])->name('sales.inactive');
        Route::get('/sales/weekly', [SalesWeeklyController::class, 'index'])->name('sales.index');
        Route::get('/sales/weekly/pending', [SalesWeeklyController::class, 'pending'])->name('sales.pending');
        Route::get('/sales/weekly/inactive', [SalesWeeklyController::class, 'inactive'])->name('sales.inactive');

        Route::patch('/sales/{id}/archive', [SalesWeeklyController::class, 'archive'])->name('sales.archive');
        // Route::patch('/sales/{id}/archive', [SalesController::class, 'archive'])->name('sales.archive');

        // Route::get('/sales/archive/all', [SalesController::class, 'archiveAll'])->name('sales.archive.all');
        // Route::get('/sales/archive/pending', [SalesController::class, 'archivePending'])->name('sales.archive.pending');
        // Route::get('/sales/archive/inactive', [SalesController::class, 'archiveInactive'])->name('sales.archive.inactive');
        Route::get('/sales/archive/all', [SalesWeeklyController::class, 'archiveAll'])->name('sales.archive.all');
        Route::get('/sales/archive/pending', [SalesWeeklyController::class, 'archivePending'])->name('sales.archive.pending');
        Route::get('/sales/archive/inactive', [SalesWeeklyController::class, 'archiveInactive'])->name('sales.archive.inactive');


        Route::get('/sales/orders', [SalesController::class, 'showOrders'])->name('sales.test.orders');
        Route::get('/sales/weekly/orders/{start_week}/{end_week}/{status}', [SalesWeeklyController::class, 'showOrders'])->name('sales.orders');
        Route::get('/get-recent-invoices', [InvoiceController::class, 'getRecentInvoices'])->name('recent.invoices');
        Route::get('/get-recent-invoices-search', [InvoiceController::class, 'searchRecentInvoices'])->name('invoice.recent.search');
    });

    Route::resource('/clients', ClientController::class);
    Route::post('/notifications/markAsRead', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/notifications/update', [NotificationController::class, 'update'])->name('notifications.update');
    Route::post('/notifications/clearAll', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');

    Route::post('/admin/change-role', [ChangeRoleController::class, 'changeRole'])->name('admin.changeRole');
    Route::delete('/admin/delete-role', [ChangeRoleController::class, 'deleteRole'])->name('admin.deleteRole');


    // Route::get('/send-whatsapp', [WhatsAppController::class, 'sendWhatsAppMessage']);
});
