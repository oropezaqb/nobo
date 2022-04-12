<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayeeController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ReviewedVoucherController;
use App\Http\Controllers\ApprovedVoucherController;
use App\Http\Controllers\BankEndorsementController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::resources([
    'payees' => PayeeController::class,
    'queries' => QueryController::class,
    'bills' => BillController::class,
    'vouchers' => VoucherController::class,
    'reviewed-vouchers' => ReviewedVoucherController::class,
    'approved-vouchers' => ApprovedVoucherController::class,
    'bank-endorsements' => BankEndorsementController::class,
    'payments' => PaymentController::class,
    'reports' => ReportController::class,
]);

Route::post('/payees/export', [PayeeController::class, 'export']);
Route::post('/queries/{query}/run', [QueryController::class, 'run'])->name('queries.run');
Route::post('/queries/{query}/csv', [QueryController::class, 'csv'])->name('queries.csv');
Route::post('/vouchers/getbill', [VoucherController::class, 'getbill'])->name('vouchers.getbill');
Route::post('/reviewed-vouchers/get-voucher', [ReviewedVoucherController::class, 'getVoucher'])->name('reviewed-vouchers.getVoucher');
Route::post('/approved-vouchers/get-voucher', [ApprovedVoucherController::class, 'getVoucher'])->name('approved-vouchers.getVoucher');
Route::post('/bank-endorsements/get-voucher', [BankEndorsementController::class, 'getVoucher'])->name('bank-endorsements.getVoucher');
Route::post('/payments/get-voucher', [PaymentController::class, 'getVoucher'])->name('payments.getVoucher');
Route::post('/payees/upload', [PayeeController::class, 'upload'])->name('payees.upload');
Route::post('/vouchers/upload', [VoucherController::class, 'upload'])->name('vouchers.upload');
Route::post('/reviewed-vouchers/upload', [ReviewedVoucherController::class, 'upload'])->name('reviewed-vouchers.upload');
Route::post('/approved-vouchers/upload', [ApprovedVoucherController::class, 'upload'])->name('approved-vouchers.upload');
Route::post('/reports/bills-for-payment', [ReportController::class, 'billsForPayment'])->name('reports.bills-for-payment');
Route::post('/reports/bills-for-payment-csv', [ReportController::class, 'billsForPaymentCSV'])->name('reports.bills-for-payment-csv');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::post('/setup', [SetupController::class, 'store']);
