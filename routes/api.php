<?php
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\HealthProfessionalController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], static function () {
    Route::prefix('appointments')->group(function () {
        Route::post('/', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/{id}', [AppointmentController::class, 'show'])->name('appointments.show');
    });

    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/{id}', [ServiceController::class, 'show'])->name('services.show');
    });

    Route::prefix('health-professionals')->group(function () {
        Route::get('/', [HealthProfessionalController::class, 'index'])->name('health-professionals.index');
        Route::get('/{id}', [HealthProfessionalController::class, 'show'])->name('health-professionals.show');
    });
});
