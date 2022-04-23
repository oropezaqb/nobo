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
Route::post('/payees/upload', [PayeeController::class, 'upload'])->name('payees.upload');

Route::post('/queries/{query}/run', [QueryController::class, 'run'])->name('queries.run');
Route::post('/queries/{query}/csv', [QueryController::class, 'csv'])->name('queries.csv');

Route::post('/vouchers/getbill', [VoucherController::class, 'getbill'])->name('vouchers.getbill');
Route::post('/vouchers/upload', [VoucherController::class, 'upload'])->name('vouchers.upload');

Route::post('/reviewed-vouchers/get-voucher', [ReviewedVoucherController::class, 'getVoucher'])->name('reviewed-vouchers.getVoucher');
Route::post('/reviewed-vouchers/upload', [ReviewedVoucherController::class, 'upload'])->name('reviewed-vouchers.upload');

Route::post('/approved-vouchers/get-voucher', [ApprovedVoucherController::class, 'getVoucher'])->name('approved-vouchers.getVoucher');
Route::post('/approved-vouchers/upload', [ApprovedVoucherController::class, 'upload'])->name('approved-vouchers.upload');

Route::post('/bank-endorsements/get-voucher', [BankEndorsementController::class, 'getVoucher'])->name('bank-endorsements.getVoucher');
Route::post('/bank-endorsements/upload', [BankEndorsementController::class, 'upload'])->name('bank-endorsements.upload');

Route::post('/payments/get-voucher', [PaymentController::class, 'getVoucher'])->name('payments.getVoucher');
Route::post('/payments/upload', [PaymentController::class, 'upload'])->name('payments.upload');

Route::post('/reports/bills-for-payment', [ReportController::class, 'billsForPayment'])->name('reports.bills-for-payment');
Route::post('/reports/bills-for-payment-csv', [ReportController::class, 'billsForPaymentCSV'])->name('reports.bills-for-payment-csv');

Route::post('/reports/reviewed-vouchers-csv', [ReportController::class, 'reviewedVouchersCSV'])->name('reports.reviewed-vouchers-csv');

Route::post('/reports/bills-for-processing', [ReportController::class, 'billsForProcessing'])->name('reports.bills-for-processing');
Route::post('/reports/bills-for-processing-csv', [ReportController::class, 'billsForProcessingCSV'])->name('reports.bills-for-processing-csv');

Route::post('/reports/vouchers-for-review', [ReportController::class, 'vouchersForReview'])->name('reports.vouchers-for-review');
Route::post('/reports/vouchers-for-review-csv', [ReportController::class, 'vouchersForReviewCSV'])->name('reports.vouchers-for-review-csv');

Route::post('/reports/vouchers-for-approval', [ReportController::class, 'vouchersForApproval'])->name('reports.vouchers-for-approval');
Route::post('/reports/vouchers-for-approval-csv', [ReportController::class, 'vouchersForApprovalCSV'])->name('reports.vouchers-for-approval-csv');

Route::post('/reports/vouchers-for-HO-endorsement', [ReportController::class, 'vouchersForHOEndorsement'])->name('reports.vouchers-for-HO-endorsement');
Route::post('/reports/vouchers-for-HO-endorsement-csv', [ReportController::class, 'vouchersForHOEndorsementCSV'])->name('reports.vouchers-for-HO-endorsement-csv');

Route::post('/reports/vouchers-for-bank-endorsement', [ReportController::class, 'vouchersForBankEndorsement'])->name('reports.vouchers-for-bank-endorsement');
Route::post('/reports/vouchers-for-bank-endorsement-csv', [ReportController::class, 'vouchersForBankEndorsementCSV'])->name('reports.vouchers-for-bank-endorsement-csv');

Route::post('/reports/vouchers-for-payment', [ReportController::class, 'vouchersForPayment'])->name('reports.vouchers-for-payment');
Route::post('/reports/vouchers-for-payment-csv', [ReportController::class, 'vouchersForPaymentCSV'])->name('reports.vouchers-for-payment-csv');

Route::post('/reports/current-accounts-payable', [ReportController::class, 'currentAccountsPayable'])->name('reports.current-accounts-payable');
Route::post('/reports/current-accounts-payable-csv', [ReportController::class, 'currentAccountsPayableCSV'])->name('reports.current-accounts-payable-csv');

Route::post('/reports/accounts-payable-thirty', [ReportController::class, 'accountsPayableThirty'])->name('reports.accounts-payable-thirty');
Route::post('/reports/accounts-payable-thirty-csv', [ReportController::class, 'accountsPayableThirtyCSV'])->name('reports.accounts-payable-thirty-csv');

Route::post('/reports/accounts-payable-sixty', [ReportController::class, 'accountsPayableSixty'])->name('reports.accounts-payable-sixty');
Route::post('/reports/accounts-payable-sixty-csv', [ReportController::class, 'accountsPayableSixtyCSV'])->name('reports.accounts-payable-sixty-csv');

Route::post('/reports/accounts-payable-ninety', [ReportController::class, 'accountsPayableNinety'])->name('reports.accounts-payable-ninety');
Route::post('/reports/accounts-payable-ninety-csv', [ReportController::class, 'accountsPayableNinetyCSV'])->name('reports.accounts-payable-ninety-csv');

Route::post('/reports/accounts-payable-ninetyplus', [ReportController::class, 'accountsPayableNinetyplus'])->name('reports.accounts-payable-ninetyplus');
Route::post('/reports/accounts-payable-ninetyplus-csv', [ReportController::class, 'accountsPayableNinetyplusCSV'])->name('reports.accounts-payable-ninetyplus-csv');

Route::post('/reports/petty-current', [ReportController::class, 'pettyCurrent'])->name('reports.petty-current');
Route::post('/reports/petty-current-csv', [ReportController::class, 'pettyCurrentCSV'])->name('reports.petty-current-csv');

Route::post('/reports/petty-seven', [ReportController::class, 'pettySeven'])->name('reports.petty-seven');
Route::post('/reports/petty-seven-csv', [ReportController::class, 'pettySevenCSV'])->name('reports.petty-seven-csv');

Route::post('/reports/petty-fourteen', [ReportController::class, 'pettyFourteen'])->name('reports.petty-fourteen');
Route::post('/reports/petty-fourteen-csv', [ReportController::class, 'pettyFourteenCSV'])->name('reports.petty-fourteen-csv');

Route::post('/reports/petty-twenty-one', [ReportController::class, 'pettyTwentyOne'])->name('reports.petty-twenty-one');
Route::post('/reports/petty-twenty-one-csv', [ReportController::class, 'pettyTwentyOneCSV'])->name('reports.petty-twenty-one-csv');

Route::post('/reports/petty-twenty-one-plus', [ReportController::class, 'pettyTwentyOnePlus'])->name('reports.petty-twenty-one-plus');
Route::post('/reports/petty-twenty-one-plus-csv', [ReportController::class, 'pettyTwentyOnePlusCSV'])->name('reports.petty-twenty-one-plus-csv');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::post('/setup', [SetupController::class, 'store']);
