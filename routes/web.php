<?php

use App\Http\Controllers\DomainController;
use App\Http\Controllers\Plcaement_Controller;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\VirtualMachineController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});


Route::controller(DomainController::class)->group(function () {
    Route::get('/domain', 'domains')->name('domain.index');
    Route::post('/domain', 'store')->name('domain.store');
});


Route::controller(ProjectController::class)->group(function () {
    Route::get('/projects', 'index')->name('projects.index');
});

// Route::controller(Plcaement_Controller::class)->group(function () {
//     Route::get('/providers', 'index')->name('provider.index');
// });

Route::controller(VirtualMachineController
    ::class)->group(function () {
        Route::get('/servers', 'index')->name('servers.index');
    });


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
