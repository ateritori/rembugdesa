<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DecisionSessionController;
use App\Http\Controllers\DecisionMakerController;
use App\Http\Controllers\DecisionSummaryController;
use App\Http\Controllers\DecisionResultController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\CriteriaAggregationController;
use App\Http\Controllers\CriteriaScoringRuleController;
use App\Http\Controllers\AlternativeController;
use App\Http\Controllers\AlternativeEvaluationController;
use App\Http\Controllers\AhpPairwiseController;

/*
|--------------------------------------------------------------------------
| Landing
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/decision-sessions', [DecisionSessionController::class, 'index'])
        ->name('decision-sessions.index');

    Route::get('/decision-sessions/create', [DecisionSessionController::class, 'create'])
        ->name('decision-sessions.create');

    Route::post('/decision-sessions', [DecisionSessionController::class, 'store'])
        ->name('decision-sessions.store');

    Route::get('/decision-sessions/{decisionSession}/edit', [DecisionSessionController::class, 'edit'])
        ->name('decision-sessions.edit');

    Route::put('/decision-sessions/{decisionSession}', [DecisionSessionController::class, 'update'])
        ->name('decision-sessions.update');

    Route::patch('/decision-sessions/{decisionSession}/activate', [DecisionSessionController::class, 'activate'])
        ->name('decision-sessions.activate');

    Route::patch('/decision-sessions/{decisionSession}/close', [DecisionSessionController::class, 'close'])
        ->name('decision-sessions.close');


    Route::delete('/decision-sessions/{decisionSession}', [DecisionSessionController::class, 'destroy'])
        ->name('decision-sessions.destroy');

    // Control Center (Halaman Kelola untuk Sesi yang sudah Aktif)
    Route::get('/decision-sessions/{decisionSession}/control', [DecisionSessionController::class, 'control'])
        ->name('control.index');

    // Criteria
    Route::get('/decision-sessions/{decisionSession}/criteria', [CriteriaController::class, 'index'])
        ->name('criteria.index');

    Route::post('/decision-sessions/{decisionSession}/criteria', [CriteriaController::class, 'store'])
        ->name('criteria.store');

    Route::put('/criteria/{criteria}', [CriteriaController::class, 'update'])
        ->name('criteria.update');

    Route::patch('/criteria/{criteria}/toggle', [CriteriaController::class, 'toggle'])
        ->name('criteria.toggle');

    Route::delete('/criteria/{criteria}', [CriteriaController::class, 'destroy'])
        ->name('criteria.destroy');

    // Criteria Scoring Rules
    Route::post('/criteria/{criteria}/scoring-rule', [CriteriaScoringRuleController::class, 'store'])
        ->name('criteria.scoring.store');

    Route::put('/criteria/{criteria}/scoring-rule/{rule}', [CriteriaScoringRuleController::class, 'update'])
        ->name('criteria.scoring.update');

    // Alternatives
    Route::get('/decision-sessions/{decisionSession}/alternatives', [AlternativeController::class, 'index'])
        ->name('alternatives.index');

    Route::post('/decision-sessions/{decisionSession}/alternatives', [AlternativeController::class, 'store'])
        ->name('alternatives.store');

    Route::put('/alternatives/{alternative}', [AlternativeController::class, 'update'])
        ->name('alternatives.update');

    Route::patch('/alternatives/{alternative}/toggle', [AlternativeController::class, 'toggle'])
        ->name('alternatives.toggle');

    Route::delete('/alternatives/{alternative}', [AlternativeController::class, 'destroy'])
        ->name('alternatives.destroy');

    // Control & Aggregation
    Route::patch('/decision-sessions/{decisionSession}/lock-criteria', [CriteriaAggregationController::class, 'lock'])
        ->name('decision-sessions.lock-criteria');

    Route::get('/decision-sessions/{decisionSession}/assign-dms', [DecisionSessionController::class, 'assignDms'])
        ->name('decision-sessions.assign-dms');

    Route::post('/decision-sessions/{decisionSession}/assign-dms', [DecisionSessionController::class, 'storeAssignedDms'])
        ->name('decision-sessions.assign-dms.store');
});

/*
|--------------------------------------------------------------------------
| HASIL (admin & dm)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|dm'])->group(function () {
    Route::get(
        '/decision-sessions/{decisionSession}/result',
        [DecisionSessionController::class, 'result']
    )->name('decision-sessions.result');
});

/*
|--------------------------------------------------------------------------
| DECISION MAKER (DM)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:dm'])->group(function () {

    // Workspace DM
    Route::get(
        '/decision-sessions/{decisionSession}/dms',
        [DecisionMakerController::class, 'index']
    )->name('dms.index');

    // Pairwise AHP (Bobot Individu)
    Route::get(
        '/decision-sessions/{decisionSession}/pairwise',
        [AhpPairwiseController::class, 'index']
    )->name('decision-sessions.pairwise.index');

    Route::post(
        '/decision-sessions/{decisionSession}/pairwise',
        [AhpPairwiseController::class, 'store']
    )->name('decision-sessions.pairwise.store');

    // Bobot Individu (read-only hasil pairwise DM)
    Route::get(
        '/decision-sessions/{decisionSession}/dms/weights',
        [DecisionMakerController::class, 'weights']
    )->name('dms.weights.index');

    // Bobot Kelompok
    Route::get(
        '/decision-sessions/{decisionSession}/dms/group-weights',
        [DecisionMakerController::class, 'groupWeights']
    )->name('dms.group-weights.index');

    // Form penilaian (create / edit)
    Route::get(
        '/decision-sessions/{decisionSession}/alternative-evaluations',
        [AlternativeEvaluationController::class, 'index']
    )->name('alternative-evaluations.index');

    // Simpan penilaian (create / update)
    Route::post(
        '/decision-sessions/{decisionSession}/alternative-evaluations',
        [AlternativeEvaluationController::class, 'store']
    )->name('alternative-evaluations.store');

    // Ringkasan Hasil
    Route::get(
        '/decision-sessions/{decisionSession}/summary',
        [DecisionSummaryController::class, 'show']
    )->name('decision-sessions.summary');
});


require __DIR__ . '/auth.php';
