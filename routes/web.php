<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    DashboardController,
    DecisionSessionController,
    DecisionMakerController,
    DecisionSummaryController,
    DecisionResultController,
    CriteriaController,
    CriteriaAggregationController,
    CriteriaScoringRuleController,
    AlternativeController,
    AlternativeEvaluationController,
    AhpPairwiseController
};

/*
|--------------------------------------------------------------------------
| Public / Landing
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('auth.login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN AREA
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        // Decision Sessions Management
        Route::controller(DecisionSessionController::class)->group(function () {
            Route::get('/decision-sessions', 'index')->name('decision-sessions.index');
            Route::get('/decision-sessions/create', 'create')->name('decision-sessions.create');
            Route::post('/decision-sessions', 'store')->name('decision-sessions.store');
            Route::get('/decision-sessions/{decisionSession}/edit', 'edit')->name('decision-sessions.edit');
            Route::put('/decision-sessions/{decisionSession}', 'update')->name('decision-sessions.update');
            Route::patch('/decision-sessions/{decisionSession}/activate', 'activate')->name('decision-sessions.activate');
            Route::patch('/decision-sessions/{decisionSession}/close', 'close')->name('decision-sessions.close');
            Route::delete('/decision-sessions/{decisionSession}', 'destroy')->name('decision-sessions.destroy');
            Route::get('/decision-sessions/{decisionSession}/control', 'control')->name('control.index');

            // DM Assignment
            Route::get('/decision-sessions/{decisionSession}/assign-dms', 'assignDms')->name('decision-sessions.assign-dms');
            Route::post('/decision-sessions/{decisionSession}/assign-dms', 'storeAssignedDms')->name('decision-sessions.assign-dms.store');
        });

        // Criteria Management
        Route::controller(CriteriaController::class)->group(function () {
            Route::get('/decision-sessions/{decisionSession}/criteria', 'index')->name('criteria.index');
            Route::post('/decision-sessions/{decisionSession}/criteria', 'store')->name('criteria.store');
            Route::put('/criteria/{criteria}', 'update')->name('criteria.update');
            Route::patch('/criteria/{criteria}/toggle', 'toggle')->name('criteria.toggle');
            Route::delete('/criteria/{criteria}', 'destroy')->name('criteria.destroy');
        });

        // Criteria Scoring Rules
        Route::controller(CriteriaScoringRuleController::class)->group(function () {
            Route::post('/criteria/{criteria}/scoring-rule', 'store')->name('criteria.scoring.store');
            Route::put('/criteria/{criteria}/scoring-rule/{rule}', 'update')->name('criteria.scoring.update');
        });

        // Alternatives Management
        Route::controller(AlternativeController::class)->group(function () {
            Route::get('/decision-sessions/{decisionSession}/alternatives', 'index')->name('alternatives.index');
            Route::post('/decision-sessions/{decisionSession}/alternatives', 'store')->name('alternatives.store');
            Route::put('/alternatives/{alternative}', 'update')->name('alternatives.update');
            Route::patch('/alternatives/{alternative}/toggle', 'toggle')->name('alternatives.toggle');
            Route::delete('/alternatives/{alternative}', 'destroy')->name('alternatives.destroy');
        });

        // Aggregation Logic
        Route::patch('/decision-sessions/{decisionSession}/lock-criteria', [CriteriaAggregationController::class, 'lock'])
            ->name('decision-sessions.lock-criteria');
    });

    /*
    |--------------------------------------------------------------------------
    | SHARED RESULTS (Admin & DM)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin|dm')->group(function () {
        Route::get('/decision-sessions/{decisionSession}/result', [DecisionSessionController::class, 'result'])
            ->name('decision-sessions.result');
    });

    /*
|--------------------------------------------------------------------------
| DECISION MAKER (DM) AREA
|--------------------------------------------------------------------------
*/
    Route::middleware('role:dm')->group(function () {

        // DM Workspace & Weights
        Route::controller(DecisionMakerController::class)->group(function () {
            Route::get('/decision-sessions/{decisionSession}/dms', 'index')->name('dms.index');
            Route::get('/decision-sessions/{decisionSession}/dms/weights', 'weights')->name('dms.weights.index');
            Route::get('/decision-sessions/{decisionSession}/dms/group-weights', 'groupWeights')->name('dms.group-weights.index');
        });

        // Pairwise AHP - PERBAIKAN: Pastikan parameter sesuai dengan Controller
        Route::controller(AhpPairwiseController::class)->group(function () {
            // Link: /decision-sessions/1/pairwise
            Route::get('/decision-sessions/{decisionSession}/pairwise', 'index')->name('decision-sessions.pairwise.index');

            // Link POST: /decision-sessions/1/pairwise
            // Pastikan nama {decisionSession} di sini sama persis dengan variabel di store(..., DecisionSession $decisionSession)
            Route::post('/decision-sessions/{decisionSession}/pairwise', 'store')->name('decision-sessions.pairwise.store');
        });

        // Alternative Evaluations
        Route::controller(AlternativeEvaluationController::class)->group(function () {
            Route::get('/decision-sessions/{decisionSession}/alternative-evaluations', 'index')->name('alternative-evaluations.index');
            Route::post('/decision-sessions/{decisionSession}/alternative-evaluations', 'store')->name('alternative-evaluations.store');
        });

        // Individual Summary
        Route::get('/decision-sessions/{decisionSession}/summary', [DecisionSummaryController::class, 'show'])
            ->name('decision-sessions.summary');
    });
});

require __DIR__ . '/auth.php';
