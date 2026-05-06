<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:admin,hr,direktur'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
    
    Route::prefix('results')->name('results.')->group(function() {
        Route::get('/', [App\Http\Controllers\AssessmentResultController::class, 'index'])->name('index');
        Route::get('/{date}/{branch}', [App\Http\Controllers\AssessmentResultController::class, 'showEmployees'])->name('period');
        Route::get('/{date}/{branch}/export-excel', [App\Http\Controllers\AssessmentResultController::class, 'exportExcel'])->name('export-excel');
        Route::get('/{date}/{branch}/{user}', [App\Http\Controllers\AssessmentResultController::class, 'showDetail'])->name('detail');
        Route::delete('/destroy-period', [App\Http\Controllers\AssessmentResultController::class, 'destroy'])
            ->middleware('role:admin')
            ->name('destroy');
    });
});

Route::middleware(['auth', 'role:admin,hr'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('branches', App\Http\Controllers\BranchController::class);
    Route::resource('divisions', App\Http\Controllers\DivisionController::class);
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('indicators', App\Http\Controllers\IndicatorController::class);
    Route::resource('interaction-matrices', App\Http\Controllers\InteractionMatrixController::class);
    Route::post('store-assignments', [App\Http\Controllers\InteractionMatrixController::class, 'storeAssignments'])->name('store-assignments');
    Route::delete('destroy-assignment/{assignment}', [App\Http\Controllers\InteractionMatrixController::class, 'destroyAssignment'])->name('destroy-assignment');
    Route::delete('bulk-destroy-assignments', [App\Http\Controllers\InteractionMatrixController::class, 'bulkDestroy'])->name('bulk-destroy-assignments');
});

Route::middleware(['auth', 'role:karyawan,direktur'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\KaryawanController::class, 'index'])->name('dashboard');
    Route::get('/evaluate/{assignment}', [App\Http\Controllers\KaryawanController::class, 'evaluate'])->name('evaluate');
    Route::post('/evaluate/{assignment}', [App\Http\Controllers\KaryawanController::class, 'storeEvaluation'])->name('evaluate.store');
});
