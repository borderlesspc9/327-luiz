<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportDataController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProcessFieldController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\JWTMiddleware;
use App\Http\Middleware\PermissionMiddleware;

use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);

Route::get('/teste-api', function () {
    return response()->json(['message' => 'API is working']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(JWTMiddleware::class)->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
    
    Route::post('/import/all', [ImportDataController::class, 'all'])->middleware([PermissionMiddleware::class.':Export data']);
    Route::get('/export/all', [ExportController::class, 'all'])->middleware([PermissionMiddleware::class.':Import data']);

    Route::get('/search', [SearchController::class, 'search']);

    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->middleware([PermissionMiddleware::class.':Read client']);
        Route::post('/', [ClientController::class, 'store'])->middleware([PermissionMiddleware::class.':Create client']);
        Route::get('/{client}', [ClientController::class, 'show'])->middleware([PermissionMiddleware::class.':Read client']);
        Route::put('/{client}', [ClientController::class, 'update'])->middleware([PermissionMiddleware::class.':Update client']);
        Route::delete('/{client}', [ClientController::class, 'destroy'])->middleware([PermissionMiddleware::class.':Delete client']);
        
        Route::post('/upload-file/{client}', [ClientController::class, 'uploadFile'])->middleware([PermissionMiddleware::class.':Upload File']);
        Route::delete('/delete-file/{file}', [ClientController::class, 'deleteFile'])->middleware([PermissionMiddleware::class.':Delete File']);

        Route::post('/{client}/pdf', [ClientController::class, 'pdfGenerate'])->middleware([PermissionMiddleware::class.':Create PDF Procuration']);
    });

    Route::prefix('services')->group(function () {
        // Route::get('/', [ServiceController::class, 'index'])->middleware([PermissionMiddleware::class.':Read service']);
        Route::get('/', [ServiceController::class, 'index'])->middleware([PermissionMiddleware::class . ':Read service,Work process']);
        Route::post('/', [ServiceController::class, 'store'])->middleware([PermissionMiddleware::class.':Create service']);
        Route::get('/{service}', [ServiceController::class, 'show'])->middleware([PermissionMiddleware::class.':Read service']);
        Route::put('/{service}', [ServiceController::class, 'update'])->middleware([PermissionMiddleware::class.':Update service']);
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->middleware([PermissionMiddleware::class.':Delete service']);
    });

    Route::prefix('processes')->group(function () {
        Route::get('/', [ProcessController::class, 'index'])->middleware([PermissionMiddleware::class.':Read process']);
        Route::get('/agencies/unique', [ProcessController::class, 'getUniqueAgencies'])->middleware([PermissionMiddleware::class.':Read process']);
        Route::get('/by-plate', [ProcessController::class, 'getByPlate'])->middleware([PermissionMiddleware::class.':Read process']);
        Route::post('/', [ProcessController::class, 'store'])->middleware([PermissionMiddleware::class.':Create process']);        
        Route::get('/{process}', [ProcessController::class, 'show'])->middleware([PermissionMiddleware::class.':Read process']);
        Route::put('/{process}', [ProcessController::class, 'update'])->middleware([PermissionMiddleware::class.':Update process']);
        Route::put('/update-status/{process}', [ProcessController::class, 'updateStatus'])->middleware([PermissionMiddleware::class.':Update process,Work process']);
        Route::delete('/{process}', [ProcessController::class, 'destroy'])->middleware([PermissionMiddleware::class.':Delete process']);
        Route::post('refresh-date/{process}', [ProcessController::class, 'refresh'])->middleware([PermissionMiddleware::class.':Refresh process date']);
        Route::post('/{process}/pdf', [ProcessController::class, 'pdfGenerate'])->middleware([PermissionMiddleware::class.':Create PDF Contract']);
    });

    Route::prefix('status')->group(function () {
        Route::get('/', [StatusController::class, 'index'])->middleware([PermissionMiddleware::class.':Read status,Work process']);
        Route::post('/', [StatusController::class, 'store'])->middleware([PermissionMiddleware::class.':Create status']);
        Route::get('/{status}', [StatusController::class, 'show'])->middleware([PermissionMiddleware::class.':Read status']);
        Route::put('/{status}', [StatusController::class, 'update'])->middleware([PermissionMiddleware::class.':Update status']);
        Route::delete('/{status}', [StatusController::class, 'destroy'])->middleware([PermissionMiddleware::class.':Delete status']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware([PermissionMiddleware::class.':Read user']);
        Route::post('/', [UserController::class, 'store'])->middleware([PermissionMiddleware::class.':Create user']);
        Route::get('/{user}', [UserController::class, 'show'])->middleware([PermissionMiddleware::class.':Read user']);
        Route::put('/{user}', [UserController::class, 'update'])->middleware([PermissionMiddleware::class.':Update user']);
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware([PermissionMiddleware::class.':Delete user']);
    });

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware([PermissionMiddleware::class.':Read roles']);
        Route::post('/', [RoleController::class, 'store'])->middleware([PermissionMiddleware::class.':Create roles']);
        Route::get('/{roles}', [RoleController::class, 'show'])->middleware([PermissionMiddleware::class.':Read roles']);
        Route::put('/{roles}', [RoleController::class, 'update'])->middleware([PermissionMiddleware::class.':Update roles']);
        Route::delete('/{roles}', [RoleController::class, 'destroy'])->middleware([PermissionMiddleware::class.':Delete roles']);
    });

    Route::get('permissions', [PermissionController::class, 'index']);//->middleware([PermissionMiddleware::class.':Read permissions']);

    Route::get('process-fields', [ProcessFieldController::class, 'index']);//->middleware([PermissionMiddleware::class.':Read process field']);
    Route::get('sellers', [AuthController::class, 'sellers']);//->middleware([PermissionMiddleware::class.':Read user']);
    
});
