<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterItemsController;

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

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/master-items', [MasterItemsController::class, 'index']);
Route::post('/master-items/store', [MasterItemsController::class, 'store'])->name('master_items.store');
Route::put('/master-items/update/{id}', [MasterItemsController::class, 'update'])->name('master_items.update');
Route::get('/master-items/search', [MasterItemsController::class, 'search']);
Route::get('/master-items/form/{method}/{id?}', [MasterItemsController::class, 'formView']);

Route::get('/master-items/view/{kode}', [MasterItemsController::class, 'singleView']);
Route::get('/master-items/delete/{id}', [MasterItemsController::class, 'delete']);


Route::get('/master-items/update-random-data', [MasterItemsController::class, 'updateRandomData']);
