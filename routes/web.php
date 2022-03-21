<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayeeController;
use App\Http\Controllers\SetupController;

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
]);

Route::post('/payees/export', [PayeeController::class, 'export']);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::post('/setup', [SetupController::class, 'store']);
