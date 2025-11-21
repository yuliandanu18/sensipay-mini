
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sensijet\OwnerDashboardController;

/*
|--------------------------------------------------------------------------
| Routes dashboard owner Sensijet
|--------------------------------------------------------------------------
|
| File ini bisa kamu require dari routes/web.php:
|
|   require base_path('routes/sensijet_dashboard_routes.php');
|
*/

Route::middleware(['web', 'auth', 'role:owner,operational_director'])
    ->prefix('sensijet')
    ->as('sensijet.')
    ->group(function () {
        Route::get('dashboard-owner', [OwnerDashboardController::class, 'index'])
            ->name('dashboard.owner');
    });
