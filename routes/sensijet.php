<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sensijet\ClassRoomController;
use App\Http\Controllers\Sensijet\KbmSessionController;
use App\Http\Controllers\Sensijet\AttendanceController;
use App\Http\Controllers\Sensijet\PayrollController;
use App\Http\Controllers\Sensijet\OwnerDashboardController;


// TEST
Route::get('/sensijet/ping', function () {
    return 'sensijet ok';
});

// GROUP UTAMA SENSIJET
Route::middleware(['web'])
    ->prefix('sensijet')
    ->as('sensijet.')
    ->group(function () {

        // === KELAS (INI YANG BIKIN sensijet.classes.index ADA) ===
        Route::resource('classes', ClassRoomController::class)->names('classes');
        // hasilnya:
        // sensijet.classes.index      → GET /sensijet/classes
        // sensijet.classes.create     → GET /sensijet/classes/create
        // sensijet.classes.store      → POST /sensijet/classes
        // dst.

        // === SESI KBM ===
        Route::get('classes/{classRoom}/sessions/create', [KbmSessionController::class, 'createForClass'])
            ->name('classes.sessions.create');
        Route::post('classes/{classRoom}/sessions', [KbmSessionController::class, 'storeForClass'])
            ->name('classes.sessions.store');

        Route::get('sessions/{session}/edit', [KbmSessionController::class, 'edit'])
            ->name('sessions.edit');
        Route::put('sessions/{session}', [KbmSessionController::class, 'update'])
            ->name('sessions.update');
        Route::delete('sessions/{session}', [KbmSessionController::class, 'destroy'])
            ->name('sessions.destroy');

        Route::get('sessions/my', [KbmSessionController::class, 'mySchedule'])
            ->name('sessions.my');
        Route::post('sessions/{session}/attendance', [AttendanceController::class, 'storeForTeacher'])
            ->name('sessions.attendance.store');

        // === PAYROLL ===
        Route::get('payroll', [PayrollController::class, 'index'])
            ->name('payroll.index');
        Route::get('payroll/{teacher}', [PayrollController::class, 'show'])
            ->name('payroll.show');
            
        // === Dashboard Owner ===
             Route::get('dashboard-owner', [OwnerDashboardController::class, 'index'])
            ->name('dashboard.owner');
    });
