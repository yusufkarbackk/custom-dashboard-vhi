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
    return redirect('/dashboard');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::controller(DomainController::class)->group(function () {
        Route::get('/domain', 'domains')->name('domain.index');
        Route::post('/domain', 'store')->name('domain.store');
    });


    Route::controller(ProjectController::class)->group(function () {
        Route::get('/projects', 'index')->name('projects.index');
        Route::get('/projects/create', 'create')->name('projects.create');
        Route::post('/projects', 'store')->name('projects.store');
        Route::get('/projects/{id}', 'show')->name('projects.show');
    });

    Route::controller(VirtualMachineController::class)->group(function () {
        Route::get('/servers', 'index')->name('servers.index');
        Route::get('/servers/{projectId}/{serverId}', 'show')->name('servers.show');
        Route::get('/servers/create', 'create')->name('servers.create');
        Route::post('/servers', 'store')->name('servers.store');
        Route::post('/flavours/select', 'selectFlavor')->name('servers.flavors');
        Route::delete('/servers/{projectId}/{serverId}/delete', 'delete')->name('servers.delete');
    });
});
