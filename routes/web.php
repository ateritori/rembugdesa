<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    DashboardController
};
use App\Http\Controllers\Admin\{
    DecisionSessionController as AdminDecisionSessionController,
    DecisionControlController,
    DecisionSessionAssignmentController,
    DecisionSessionResultController,
    CriteriaController as AdminCriteriaController,
    CriteriaAggregationController as AdminCriteriaAggregationController,
    CriteriaScoringRuleController as AdminCriteriaScoringRuleController,
    AlternativeController as AdminAlternativeController
};
use App\Http\Controllers\Superadmin\DecisionSessionController as SuperadminDecisionSessionController;
use App\Http\Controllers\Superadmin\UsabilityInstrumentController;
use App\Http\Controllers\Superadmin\UsabilityReportController;
use App\Http\Controllers\Dm\{
    DecisionMakerController,
    AhpPairwiseController as DmAhpPairwiseController,
    AlternativeEvaluationController,
    UsabilityResponseController as DmUsabilityResponseController,
    DecisionSummaryController as DmDecisionSummaryController
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

    /*
    |--------------------------------------------------------------------------
    | SUPERADMIN AREA
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:superadmin')->group(function () {

        // Superadmin Dashboard
        Route::get('/superadmin/dashboard', [DashboardController::class, 'index'])
            ->name('superadmin.dashboard');


        // User Management
        Route::controller(\App\Http\Controllers\Superadmin\UserController::class)->group(function () {
            Route::get('/superadmin/users', 'index')->name('superadmin.users.index');
            Route::get('/superadmin/users/{user}/edit', 'edit')->name('superadmin.users.edit');
            Route::put('/superadmin/users/{user}', 'update')->name('superadmin.users.update');
            Route::delete('/superadmin/users/{user}', 'destroy')->name('superadmin.users.destroy');
        });

        // Role Management
        Route::resource('/superadmin/roles', \App\Http\Controllers\Superadmin\RoleController::class)
            ->names('superadmin.roles')
            ->except(['show']);

        // Global Decision Sessions Monitoring (Superadmin)
        Route::controller(SuperadminDecisionSessionController::class)->group(function () {
            Route::get('/superadmin/decision-sessions', 'index')
                ->name('superadmin.decision-sessions.index');

            Route::get('/superadmin/decision-sessions/{session}', 'show')
                ->name('superadmin.decision-sessions.show');

            Route::patch('/superadmin/decision-sessions/{session}/status', 'updateStatus')
                ->name('superadmin.decision-sessions.update-status');
        });

        /*
        |--------------------------------------------------------------------------
        | USABILITY (SUS) - SUPERADMIN
        |--------------------------------------------------------------------------
        */
        Route::controller(UsabilityInstrumentController::class)->group(function () {
            Route::get('/superadmin/usability/instrument', 'index')
                ->name('superadmin.usability.instruments.index');

            Route::get('/superadmin/usability/instrument/edit', 'edit')
                ->name('superadmin.usability.instruments.edit');

            Route::put('/superadmin/usability/instrument', 'update')
                ->name('superadmin.usability.instruments.update');

            Route::put('/superadmin/usability/questions/{question}', 'updateQuestion')
                ->name('superadmin.usability.questions.update');
        });

        Route::get(
            '/superadmin/usability/reports',
            [UsabilityReportController::class, 'index']
        )->name('superadmin.usability.reports.index');

        // System Monitoring (opsional, siap dikembangkan)
        Route::get('/superadmin/system', function () {
            return view('superadmin.system.index');
        })->name('superadmin.system.index');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | USABILITY (SUS) - RESPONDENT
    |--------------------------------------------------------------------------
    */
    Route::controller(DmUsabilityResponseController::class)->group(function () {
        Route::get('/usability/respond', 'create')
            ->name('usability.responses.create');

        Route::post('/usability/respond', 'store')
            ->name('usability.responses.store');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN AREA
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        // CRUD Decision Session
        Route::controller(AdminDecisionSessionController::class)->group(function () {
            Route::get('/decision-sessions', 'index')->name('decision-sessions.index');
            Route::get('/decision-sessions/create', 'create')->name('decision-sessions.create');
            Route::post('/decision-sessions', 'store')->name('decision-sessions.store');
            Route::get('/decision-sessions/{decisionSession}/edit', 'edit')->name('decision-sessions.edit');
            Route::put('/decision-sessions/{decisionSession}', 'update')->name('decision-sessions.update');
            Route::delete('/decision-sessions/{decisionSession}', 'destroy')->name('decision-sessions.destroy');
        });

        // Control & Lifecycle
        Route::controller(DecisionControlController::class)->group(function () {
            Route::get('/decision-sessions/{decisionSession}/control', 'index')->name('control.index');
            Route::patch('/decision-sessions/{decisionSession}/activate', 'activate')->name('decision-sessions.activate');
            Route::patch('/decision-sessions/{decisionSession}/close', 'close')->name('decision-sessions.close');
        });

        // DM Assignment
        Route::controller(DecisionSessionAssignmentController::class)->group(function () {
            Route::get('/decision-sessions/{decisionSession}/assign-dms', 'index')->name('decision-sessions.assign-dms');
            Route::post('/decision-sessions/{decisionSession}/assign-dms', 'store')->name('decision-sessions.assign-dms.store');
        });

        // Criteria Management
        Route::controller(AdminCriteriaController::class)->group(function () {
            Route::get('/decision-sessions/{decisionSession}/criteria', 'index')->name('criteria.index');
            Route::post('/decision-sessions/{decisionSession}/criteria', 'store')->name('criteria.store');
            Route::put('/criteria/{criteria}', 'update')->name('criteria.update');
            Route::patch('/criteria/{criteria}/toggle', 'toggle')->name('criteria.toggle');
            Route::delete('/criteria/{criteria}', 'destroy')->name('criteria.destroy');
        });

        // Criteria Scoring Rules
        Route::controller(AdminCriteriaScoringRuleController::class)->group(function () {
            Route::post('/criteria/{criteria}/scoring-rule', 'store')->name('criteria.scoring.store');
            Route::put('/criteria/{criteria}/scoring-rule/{rule}', 'update')->name('criteria.scoring.update');
        });

        // Alternatives Management
        Route::controller(AdminAlternativeController::class)->group(function () {
            Route::get('/decision-sessions/{decisionSession}/alternatives', 'index')->name('alternatives.index');
            Route::post('/decision-sessions/{decisionSession}/alternatives', 'store')->name('alternatives.store');
            Route::put('/alternatives/{alternative}', 'update')->name('alternatives.update');
            Route::patch('/alternatives/{alternative}/toggle', 'toggle')->name('alternatives.toggle');
            Route::delete('/alternatives/{alternative}', 'destroy')->name('alternatives.destroy');
        });

        // Aggregation Logic
        Route::patch('/decision-sessions/{decisionSession}/lock-criteria', [AdminCriteriaAggregationController::class, 'lock'])
            ->name('decision-sessions.lock-criteria');
    });

    /*
    |--------------------------------------------------------------------------
    | SHARED RESULTS (Admin & DM)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin|dm')->group(function () {
        Route::get(
            '/decision-sessions/{decisionSession}/result',
            [DecisionSessionResultController::class, 'showPublic']
        )->name('decision-sessions.result');
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
        Route::controller(DmAhpPairwiseController::class)->group(function () {
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
        Route::get('/decision-sessions/{decisionSession}/summary', [DmDecisionSummaryController::class, 'show'])
            ->name('decision-sessions.summary');
    });
});

require __DIR__ . '/auth.php';
